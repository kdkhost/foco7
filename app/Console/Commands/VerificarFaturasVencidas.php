<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fatura;
use App\Models\CmsSetting;
use App\Services\EmailTemplateService;

class VerificarFaturasVencidas extends Command
{
    protected $signature = 'faturas:verificar-vencidas';
    protected $description = 'Identifica faturas pendentes com vencimento expirado, marca como vencidas e envia notificação';

    public function handle(): int
    {
        $hoje = now()->toDateString();
        $this->info("Iniciando verificação de faturas vencidas até {$hoje}...");

        $faturas = Fatura::with(['user', 'plano'])
            ->where('status', 'pendente')
            ->whereNull('data_pagamento')
            ->whereNotIn('status', ['pago', 'paga', 'quitada', 'liquidada', 'cancelado', 'cancelada', 'estornado', 'estornada'])
            ->where('data_vencimento', '<', $hoje)
            ->get();

        $total = $faturas->count();
        if ($total === 0) {
            $msg = "Nenhuma fatura vencida pendente de processamento.";
            $this->info($msg);
            $agora = now();
            CmsSetting::set('cron_last_run_verificar_vencidas', $agora->format('d/m/Y H:i:s'), 'cron');
            \App\Models\CronJob::where('comando', 'faturas:verificar-vencidas')->update([
                'ultima_execucao' => $agora,
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $msg,
            ]);
            return Command::SUCCESS;
        }

        $processadas = 0;
        $params = \App\Services\ReguaCobrancaService::obterParametros();
        foreach ($faturas as $fatura) {
            // Ignora estritamente se a fatura já estiver quitada ou cancelada
            if ($fatura->isLiquidadaOuCancelada() || !empty($fatura->data_pagamento)) {
                continue;
            }

            $fatura->status = 'vencida';
            $encargos = \App\Services\ReguaCobrancaService::calcularEncargos($fatura, $params);
            $fatura->valor_multa = $encargos['valor_multa'];
            $fatura->valor_juros = $encargos['valor_juros'];
            $fatura->save();
            $processadas++;

            if ($fatura->user && !empty($fatura->user->email)) {
                EmailTemplateService::enviar($fatura->user->email, 'fatura_vencida', [
                    'cliente_nome' => $fatura->user->name,
                    'cliente_email' => $fatura->user->email,
                    'fatura_id' => $fatura->codigo_fatura,
                    'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                    'valor' => $fatura->valor_formatado,
                    'valor_multa' => $fatura->multa_formatada,
                    'valor_juros' => $fatura->juros_formatado,
                    'valor_total' => 'R$ ' . number_format($encargos['valor_total'], 2, ',', '.'),
                    'dias_atraso' => (string) $encargos['dias_atraso'],
                    'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                ]);
            }
        }

        $msg = "Concluído: {$processadas} fatura(s) atualizada(s) para status 'vencida'.";
        $this->info($msg);
        $agora = now();
        CmsSetting::set('cron_last_run_verificar_vencidas', $agora->format('d/m/Y H:i:s'), 'cron');
        \App\Models\CronJob::where('comando', 'faturas:verificar-vencidas')->update([
            'ultima_execucao' => $agora,
            'ultimo_status' => 'sucesso',
            'ultimo_output' => $msg,
        ]);
        return Command::SUCCESS;
    }
}
