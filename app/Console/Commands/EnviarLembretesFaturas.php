<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fatura;
use App\Models\CmsSetting;
use App\Services\EmailTemplateService;

class EnviarLembretesFaturas extends Command
{
    protected $signature = 'faturas:enviar-lembretes {--dias=3 : Dias de antecedência para disparo do lembrete}';
    protected $description = 'Dispara e-mails de lembrete preventivo para faturas próximas do vencimento';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');
        $alvoData = now()->addDays($dias)->toDateString();

        $this->info("Buscando faturas com vencimento em {$alvoData} ({$dias} dias)...");

        $faturas = Fatura::with(['user', 'plano'])
            ->where('status', 'pendente')
            ->whereNull('data_pagamento')
            ->whereNotIn('status', ['pago', 'paga', 'quitada', 'liquidada', 'cancelado', 'cancelada', 'estornado', 'estornada'])
            ->whereDate('data_vencimento', $alvoData)
            ->get();

        $total = $faturas->count();
        if ($total === 0) {
            $msg = "Nenhuma fatura localizada para a data informada.";
            $this->info($msg);
            $agora = now();
            CmsSetting::set('cron_last_run_enviar_lembretes', $agora->format('d/m/Y H:i:s'), 'cron');
            \App\Models\CronJob::where('comando', 'faturas:enviar-lembretes')->update([
                'ultima_execucao' => $agora,
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $msg,
            ]);
            return Command::SUCCESS;
        }

        $enviados = 0;
        foreach ($faturas as $fatura) {
            // Ignora estritamente se a fatura já estiver quitada ou cancelada
            if ($fatura->isLiquidadaOuCancelada() || !empty($fatura->data_pagamento)) {
                continue;
            }

            if ($fatura->user && !empty($fatura->user->email)) {
                $sucesso = EmailTemplateService::enviar($fatura->user->email, 'lembrete_vencimento', [
                    'cliente_nome' => $fatura->user->name,
                    'cliente_email' => $fatura->user->email,
                    'fatura_id' => $fatura->codigo_fatura,
                    'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                    'valor' => $fatura->valor_formatado,
                    'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                ]);

                if ($sucesso) {
                    $enviados++;
                }
            }
        }

        $msg = "Concluído: {$enviados} lembrete(s) preventivo(s) enviado(s) aos clientes.";
        $this->info($msg);
        $agora = now();
        CmsSetting::set('cron_last_run_enviar_lembretes', $agora->format('d/m/Y H:i:s'), 'cron');
        \App\Models\CronJob::where('comando', 'faturas:enviar-lembretes')->update([
            'ultima_execucao' => $agora,
            'ultimo_status' => 'sucesso',
            'ultimo_output' => $msg,
        ]);
        return Command::SUCCESS;
    }
}
