<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Central de Agendamento Automático (Cron Jobs Foco 7)
Schedule::command('faturas:verificar-vencidas')->dailyAt('01:00');
Schedule::command('faturas:enviar-lembretes')->dailyAt('08:30');
Schedule::command('gateways:sincronizar-pagamentos')->everyFifteenMinutes();
