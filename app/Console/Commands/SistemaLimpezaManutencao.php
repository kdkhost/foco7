<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CmsSetting;
use App\Models\CronJob;

class SistemaLimpezaManutencao extends Command
{
    protected $signature = 'sistema:limpeza-manutencao';
    protected $description = 'Executa limpeza de tokens expirados de reset de senha e manutenção preventiva do sistema';

    public function handle(): int
    {
        $this->info("Iniciando rotina de limpeza e manutenção preventiva...");

        $tokensRemovidos = 0;
        try {
            // Remove tokens de redefinição de senha com mais de 48 horas
            $tokensRemovidos = DB::table('password_reset_tokens')
                ->where('created_at', '<', now()->subHours(48))
                ->delete();
        } catch (\Throwable $e) {
            $this->warn("Aviso ao limpar tokens de senha: " . $e->getMessage());
        }

        // Se o log de cron exceder 3MB, mantém apenas as últimas 300 linhas para evitar consumo de disco excessivo
        $logCron = storage_path('logs/cron.log');
        if (file_exists($logCron) && filesize($logCron) > 3 * 1024 * 1024) {
            $linhas = @file($logCron);
            if (!empty($linhas)) {
                $ultimas = array_slice($linhas, -300);
                @file_put_contents($logCron, implode('', $ultimas));
                $this->info("Arquivo de logs do cron podado para as últimas 300 linhas.");
            }
        }

        $msg = "Manutenção concluída. {$tokensRemovidos} token(s) expirados limpos.";
        $this->info($msg);

        // Registra execução
        $dataHora = now();
        CmsSetting::set('cron_last_run_sistema_limpeza', $dataHora->format('d/m/Y H:i:s'), 'cron');

        CronJob::where('comando', 'sistema:limpeza-manutencao')->update([
            'ultima_execucao' => $dataHora,
            'ultimo_status' => 'sucesso',
            'ultimo_output' => $msg,
        ]);

        return Command::SUCCESS;
    }
}
