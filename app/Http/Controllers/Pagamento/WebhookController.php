<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook de retorno do Mercado Pago
     */
    public function mercadopago(Request $request)
    {
        Log::info('Webhook Mercado Pago recebido:', $request->all());

        $type = $request->input('type');
        $dataId = $request->input('data.id') ?: $request->input('id');
        $faturaCodigo = $request->query('fatura_codigo') ?: $request->input('fatura_codigo');

        if (($type === 'payment' || $request->input('action') === 'payment.created' || $request->input('action') === 'payment.updated' || ! empty($dataId)) && ($dataId || $faturaCodigo)) {
            // Localiza a fatura vinculada ao transaction id ou código dinâmico
            $fatura = Fatura::where(function ($q) use ($dataId, $faturaCodigo) {
                if (! empty($dataId)) {
                    $q->where('gateway_transaction_id', (string) $dataId);
                }
                if (! empty($faturaCodigo)) {
                    $q->orWhere('codigo_fatura', (string) $faturaCodigo);
                }
            })->first();

            if ($fatura && $fatura->status !== 'pago') {
                $fatura->update([
                    'status' => 'pago',
                    'data_pagamento' => now(),
                    'webhook_payload' => $request->all(),
                ]);

                Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Mercado Pago Webhook Dinâmico.");

                $this->processarPosPagamentoFatura($fatura);
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Webhook de retorno da Efí PRO (Gerencianet)
     */
    public function efi(Request $request)
    {
        Log::info('Webhook Efí PRO recebido:', $request->all());

        // 1. Tratamento de Notificações Pix da Efí
        $pixList = $request->input('pix', []);
        foreach ($pixList as $pixItem) {
            $txid = $pixItem['txid'] ?? null;
            if ($txid) {
                $fatura = Fatura::where('gateway_transaction_id', $txid)
                    ->orWhere('codigo_fatura', 'like', "%{$txid}%")
                    ->first();

                if ($fatura && $fatura->status !== 'pago') {
                    $fatura->update([
                        'status' => 'pago',
                        'data_pagamento' => now(),
                        'webhook_payload' => $pixItem,
                    ]);

                    Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Efí PRO Pix Webhook.");

                    $this->processarPosPagamentoFatura($fatura);
                }
            }
        }

        // 2. Tratamento de Notificações de Carnê e Boleto v1 da Efí
        $notificationToken = $request->input('notification');
        if (! empty($notificationToken)) {
            $dadosNotif = EfiPaymentService::consultarNotificacaoV1($notificationToken);
            if (! empty($dadosNotif) && is_array($dadosNotif)) {
                foreach ($dadosNotif as $evento) {
                    $chargeId = $evento['identifiers']['charge_id'] ?? null;
                    $status = $evento['status']['current'] ?? null;

                    if ($chargeId && in_array($status, ['paid', 'settled'])) {
                        $fatura = Fatura::where('gateway_transaction_id', (string) $chargeId)
                            ->orWhere('codigo_fatura', 'like', "%{$chargeId}%")
                            ->first();

                        if ($fatura && $fatura->status !== 'pago') {
                            $fatura->update([
                                'status' => 'pago',
                                'data_pagamento' => now(),
                                'webhook_payload' => $evento,
                            ]);

                            Log::info("Fatura {$fatura->codigo_fatura} (Carnê/Boleto Efí) baixada automaticamente via Notificação v1.");

                            $this->processarPosPagamentoFatura($fatura);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Webhook de retorno da Evolution API Go (v3.0)
     * Recebe eventos de status de conexão, mensagens, QR code, etc.
     */
    public function evolution(Request $request)
    {
        $payload = $request->all();
        $event = $request->input('event') ?? $request->input('type') ?? 'UNKNOWN';

        Log::info("Webhook Evolution API Go [{$event}]:", $payload);

        // Tratamento de evento de CONEXÃO (CONNECTION_UPDATE)
        if (str_contains(strtoupper($event), 'CONNECTION') || isset($payload['state'])) {
            $state = $payload['state'] ?? ($payload['data']['state'] ?? null);
            if ($state) {
                $statusMapeado = match (strtolower((string) $state)) {
                    'open', 'connected' => 'conectado',
                    'connecting' => 'conectando',
                    'close', 'disconnected' => 'desconectado',
                    'refused' => 'recusado',
                    default => strtolower((string) $state),
                };

                CmsSetting::set('evolution_status_cache', $statusMapeado, 'whatsapp');
                CmsSetting::set('evolution_last_connection_event', now()->format('Y-m-d H:i:s'), 'whatsapp');
                Log::info("Status da Evolution API Go atualizado para: {$statusMapeado}");
            }
        }

        // Tratamento de evento de QRCODE
        if (str_contains(strtoupper($event), 'QRCODE') || isset($payload['qrcode'])) {
            $qrCode = $payload['qrcode']['base64'] ?? ($payload['data']['qrcode']['base64'] ?? null);
            if ($qrCode) {
                CmsSetting::set('evolution_last_qrcode', $qrCode, 'whatsapp');
            }
        }

        return response()->json(['status' => 'success', 'event' => $event], 200);
    }

    /**
     * Processa notificações e ativação pós-pagamento de fatura
     */
    protected function processarPosPagamentoFatura(Fatura $fatura): void
    {
        // 1. Dispara confirmação imediata por e-mail ao cliente
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

        // 2. Dispara confirmação imediata via WhatsApp
        if ($fatura->user && ! empty($fatura->user->telefone) && EvolutionApiService::isAtivo()) {
            try {
                EvolutionApiService::enviarConfirmacaoPagamento($fatura);
            } catch (\Throwable $e) {
                Log::warning("Falha ao enviar confirmação WhatsApp: " . $e->getMessage());
            }
        }

        // 3. Ativação automática de Licença de Software FOCO 7
        if (str_contains(strtolower($fatura->descricao ?? ''), 'licen') 
            || \App\Models\Licenca::where('fatura_id', $fatura->id)->exists() 
            || \App\Models\Licenca::where('user_id', $fatura->user_id)->where('status', 'pendente')->exists()) {
            try {
                \App\Services\LicenciamentoService::ativarLicencaPosPagamento($fatura);
            } catch (\Throwable $e) {
                Log::warning("Falha ao ativar licença no webhook: " . $e->getMessage());
            }
        }
    }
}
