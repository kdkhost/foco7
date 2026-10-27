<?php

namespace App\Console\Commands;

use App\Models\CmsSetting;
use App\Models\CronJob;
use App\Models\Fatura;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SincronizarPagamentosGateways extends Command
{
    protected $signature = 'gateways:sincronizar-pagamentos';

    protected $description = 'Consulta e sincroniza o status de faturas pendentes junto às APIs do Mercado Pago e Efí PRO';

    public function handle(): int
    {
        $this->info('Iniciando varredura de faturas pendentes com transação em gateways...');

        $faturas = Fatura::with(['user', 'plano'])
            ->where('status', 'pendente')
            ->whereNotNull('gateway_transaction_id')
            ->get();

        $total = $faturas->count();
        if ($total === 0) {
            $msg = 'Nenhuma fatura pendente com ID de transação para sincronizar no momento.';
            $this->info($msg);
            $agora = now();
            CmsSetting::set('cron_last_run_sincronizar_pagamentos', $agora->format('d/m/Y H:i:s'), 'cron');
            CronJob::where('comando', 'gateways:sincronizar-pagamentos')->update([
                'ultima_execucao' => $agora,
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $msg,
            ]);

            return Command::SUCCESS;
        }

        $mpToken = CmsSetting::get('mercadopago_sandbox', '1') == '1'
            ? CmsSetting::get('mercadopago_access_token_sandbox', CmsSetting::get('mercadopago_access_token', ''))
            : CmsSetting::get('mercadopago_access_token_prod', CmsSetting::get('mercadopago_access_token', ''));

        $baixadas = 0;

        foreach ($faturas as $fatura) {
            try {
                // 1. Sincronização Mercado Pago
                if ($fatura->gateway === 'mercadopago' && ! empty($mpToken)) {
                    $response = Http::withToken($mpToken)
                        ->timeout(10)
                        ->get("https://api.mercadopago.com/v1/payments/{$fatura->gateway_transaction_id}");

                    if ($response->successful()) {
                        $paymentData = $response->json();
                        $status = $paymentData['status'] ?? null;

                        if ($status === 'approved') {
                            $fatura->update([
                                'status' => 'pago',
                                'data_pagamento' => now(),
                                'webhook_payload' => $paymentData,
                            ]);
                            $baixadas++;
                            $this->line("Fatura #{$fatura->codigo_fatura} baixada com sucesso (Mercado Pago).");

                            // Envia e-mail de confirmação
                            if ($fatura->user && ! empty($fatura->user->email)) {
                                EmailTemplateService::enviar($fatura->user->email, 'pagamento_confirmado', [
                                    'cliente_nome' => $fatura->user->name,
                                    'cliente_email' => $fatura->user->email,
                                    'fatura_id' => $fatura->codigo_fatura,
                                    'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                                    'valor' => $fatura->valor_formatado,
                                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                                ]);
                            }
                        }
                    }
                }

                // 2. Sincronização Efí PRO (Carnês Oficiais com carnet_id)
                if ($fatura->gateway === 'efi' && ! empty($fatura->efi_carnet_id)) {
                    $dadosCarne = EfiPaymentService::consultarCarneV1($fatura->efi_carnet_id);
                    if ($dadosCarne && isset($dadosCarne['charges'])) {
                        foreach ($dadosCarne['charges'] as $charge) {
                            if (($charge['parcel'] ?? null) == $fatura->numero_parcela && in_array($charge['status'] ?? '', ['paid', 'settled'])) {
                                $fatura->update([
                                    'status' => 'pago',
                                    'data_pagamento' => now(),
                                    'webhook_payload' => $charge,
                                ]);
                                $baixadas++;
                                $this->line("Fatura #{$fatura->codigo_fatura} baixada com sucesso (Carnê Efí).");

                                if ($fatura->user && ! empty($fatura->user->email)) {
                                    EmailTemplateService::enviar($fatura->user->email, 'pagamento_confirmado', [
                                        'cliente_nome' => $fatura->user->name,
                                        'cliente_email' => $fatura->user->email,
                                        'fatura_id' => $fatura->codigo_fatura,
                                        'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                                        'valor' => $fatura->valor_formatado,
                                        'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                                    ]);
                                }
                                break;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $this->warn("Erro ao consultar fatura #{$fatura->codigo_fatura}: ".$e->getMessage());
            }
        }

        $msg = "Sincronização finalizada: {$baixadas} de {$total} fatura(s) compensada(s).";
        $this->info($msg);
        $agora = now();
        CmsSetting::set('cron_last_run_sincronizar_pagamentos', $agora->format('d/m/Y H:i:s'), 'cron');
        CronJob::where('comando', 'gateways:sincronizar-pagamentos')->update([
            'ultima_execucao' => $agora,
            'ultimo_status' => 'sucesso',
            'ultimo_output' => $msg,
        ]);

        return Command::SUCCESS;
    }
}
