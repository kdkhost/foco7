<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Central de Agendamento Automático (Cron Jobs Foco 7)
// Batimento cardíaco a cada minuto para confirmação visual e monitoramento na Central do Painel Admin
Schedule::call(function () {
    \App\Models\CmsSetting::set('cron_heartbeat_timestamp', (string) now()->timestamp, 'cron');
    \App\Models\CmsSetting::set('cron_heartbeat_datahora', date('d/m/Y H:i:s'), 'cron');
})->everyMinute()->name('cron-heartbeat');

// Carregamento dinâmico de tarefas ativas cadastradas no banco de dados
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('cron_jobs')) {
        $jobs = \App\Models\CronJob::where('ativo', true)->get();
        if ($jobs->isNotEmpty()) {
            foreach ($jobs as $job) {
                $expr = trim($job->expressao_cron ?: '* * * * *');
                if ($job->tipo === 'url') {
                    Schedule::call(function () use ($job) {
                        try {
                            $response = \Illuminate\Support\Facades\Http::timeout(15)->get($job->comando);
                            $job->update([
                                'ultima_execucao' => now(),
                                'ultimo_status' => $response->successful() ? 'sucesso' : 'erro',
                                'ultimo_output' => "Status HTTP {$response->status()}: " . substr($response->body(), 0, 500),
                            ]);
                        } catch (\Throwable $e) {
                            $job->update([
                                'ultima_execucao' => now(),
                                'ultimo_status' => 'erro',
                                'ultimo_output' => 'Falha na requisição URL: ' . $e->getMessage(),
                            ]);
                        }
                    })->cron($expr)->name("cron-job-url-{$job->id}");
                } else {
                    Schedule::command($job->comando)
                        ->cron($expr)
                        ->name("cron-job-artisan-{$job->id}")
                        ->onSuccess(function () use ($job) {
                            $job->update([
                                'ultima_execucao' => now(),
                                'ultimo_status' => 'sucesso',
                                'ultimo_output' => 'Executado com sucesso via agendador automático.',
                            ]);
                        })
                        ->onFailure(function () use ($job) {
                            $job->update([
                                'ultima_execucao' => now(),
                                'ultimo_status' => 'erro',
                                'ultimo_output' => 'Falha na execução pelo agendador automático.',
                            ]);
                        });
                }
            }
        } else {
            Schedule::command('faturas:verificar-vencidas')->dailyAt('06:00')->name('faturas-verificar-vencidas');
            Schedule::command('faturas:enviar-lembretes')->dailyAt('08:00')->name('faturas-enviar-lembretes');
            Schedule::command('gateways:sincronizar-pagamentos')->everyFifteenMinutes()->name('gateways-sincronizar-pagamentos');
            Schedule::command('sistema:limpeza-manutencao')->dailyAt('03:00')->name('sistema-limpeza-manutencao');
            Schedule::command('agendamentos:lembrete-dia')->dailyAt('07:00')->name('agendamentos-lembrete-dia');
        }
    } else {
        Schedule::command('faturas:verificar-vencidas')->dailyAt('06:00')->name('faturas-verificar-vencidas');
        Schedule::command('faturas:enviar-lembretes')->dailyAt('08:00')->name('faturas-enviar-lembretes');
        Schedule::command('gateways:sincronizar-pagamentos')->everyFifteenMinutes()->name('gateways-sincronizar-pagamentos');
        Schedule::command('sistema:limpeza-manutencao')->dailyAt('03:00')->name('sistema-limpeza-manutencao');
        Schedule::command('agendamentos:lembrete-dia')->dailyAt('07:00')->name('agendamentos-lembrete-dia');
    }
} catch (\Throwable $e) {
    Schedule::command('faturas:verificar-vencidas')->dailyAt('06:00')->name('faturas-verificar-vencidas');
    Schedule::command('faturas:enviar-lembretes')->dailyAt('08:00')->name('faturas-enviar-lembretes');
    Schedule::command('gateways:sincronizar-pagamentos')->everyFifteenMinutes()->name('gateways-sincronizar-pagamentos');
    Schedule::command('sistema:limpeza-manutencao')->dailyAt('03:00')->name('sistema-limpeza-manutencao');
    Schedule::command('agendamentos:lembrete-dia')->dailyAt('07:00')->name('agendamentos-lembrete-dia');
}
