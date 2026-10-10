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

Schedule::command('faturas:verificar-vencidas')->dailyAt('06:00')->name('faturas-verificar-vencidas');
Schedule::command('faturas:enviar-lembretes')->dailyAt('08:00')->name('faturas-enviar-lembretes');
Schedule::command('gateways:sincronizar-pagamentos')->everyFifteenMinutes()->name('gateways-sincronizar-pagamentos');
