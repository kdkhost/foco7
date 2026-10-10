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
            ->whereDate('data_vencimento', $alvoData)
            ->get();

        $total = $faturas->count();
        if ($total === 0) {
            $this->info("Nenhuma fatura localizada para a data informada.");
            CmsSetting::set('cron_last_run_enviar_lembretes', date('d/m/Y H:i:s'), 'cron');
            return Command::SUCCESS;
        }

        $enviados = 0;
        foreach ($faturas as $fatura) {
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

        $this->info("Concluído: {$enviados} lembrete(s) preventivo(s) enviado(s) aos clientes.");
        CmsSetting::set('cron_last_run_enviar_lembretes', date('d/m/Y H:i:s'), 'cron');
        return Command::SUCCESS;
    }
}
