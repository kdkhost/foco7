<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class CronController extends Controller
{
    /**
     * Central dedicada de Tarefas Cron & Agendamentos
     */
    public function index()
    {
        $comandoCpanel = '* * * * * cd /home/fococomb/public_html && /usr/local/bin/php artisan schedule:run >> /home/fococomb/public_html/storage/logs/cron.log 2>&1';

        $heartbeatTs = (int) CmsSetting::get('cron_heartbeat_timestamp', 0);
        $ultimoBatimento = CmsSetting::get('cron_heartbeat_datahora', 'Aguardando primeiro batimento...');
        $diffEmSegundos = $heartbeatTs > 0 ? (now()->timestamp - $heartbeatTs) : 999999;
        $cronAtivo = ($heartbeatTs > 0 && $diffEmSegundos <= 180);

        $logFile = storage_path('logs/cron.log');
        $logRecente = '';
        if (file_exists($logFile)) {
            $linhas = @file($logFile);
            if (!empty($linhas)) {
                $ultimasLinhas = array_slice($linhas, -35);
                $logRecente = trim(implode('', $ultimasLinhas));
            }
        }

        $rotinas = [
            'verificar_vencidas' => [
                'nome' => 'Verificação de Faturas Vencidas',
                'descricao' => 'Atualiza status para vencida e dispara o e-mail de alerta de suspensão ao cliente.',
                'comando' => 'faturas:verificar-vencidas',
                'frequencia' => 'Diariamente às 06:00',
                'ultima_execucao' => CmsSetting::get('cron_last_run_verificar_vencidas', 'Nunca executado'),
            ],
            'enviar_lembretes' => [
                'nome' => 'Lembrete Preventivo de Vencimento',
                'descricao' => 'Alerta os clientes com 3 dias de antecedência para evitar atrasos.',
                'comando' => 'faturas:enviar-lembretes',
                'frequencia' => 'Diariamente às 08:00',
                'ultima_execucao' => CmsSetting::get('cron_last_run_enviar_lembretes', 'Nunca executado'),
            ],
            'sincronizar_pagamentos' => [
                'nome' => 'Sincronização de Pagamentos dos Gateways',
                'descricao' => 'Consulta status de PIX e Boletos pendentes no Mercado Pago e Efí PRO.',
                'comando' => 'gateways:sincronizar-pagamentos',
                'frequencia' => 'A cada 15 minutos',
                'ultima_execucao' => CmsSetting::get('cron_last_run_sincronizar_pagamentos', 'Nunca executado'),
            ],
        ];

        return view('admin.cron.index', compact('comandoCpanel', 'rotinas', 'cronAtivo', 'ultimoBatimento', 'diffEmSegundos', 'logRecente'));
    }

    /**
     * Retorna o status em tempo real do agendador e dos logs via AJAX
     */
    public function status()
    {
        $heartbeatTs = (int) CmsSetting::get('cron_heartbeat_timestamp', 0);
        $ultimoBatimento = CmsSetting::get('cron_heartbeat_datahora', 'Aguardando primeiro batimento...');
        $diffEmSegundos = $heartbeatTs > 0 ? (now()->timestamp - $heartbeatTs) : 999999;
        $cronAtivo = ($heartbeatTs > 0 && $diffEmSegundos <= 180);

        $logFile = storage_path('logs/cron.log');
        $logRecente = '';
        if (file_exists($logFile)) {
            $linhas = @file($logFile);
            if (!empty($linhas)) {
                $ultimasLinhas = array_slice($linhas, -35);
                $logRecente = trim(implode('', $ultimasLinhas));
            }
        }

        return response()->json([
            'success' => true,
            'cron_ativo' => $cronAtivo,
            'ultimo_batimento' => $ultimoBatimento,
            'diff_segundos' => $diffEmSegundos,
            'horario_servidor' => date('d/m/Y H:i:s'),
            'log_recente' => $logRecente ?: 'Nenhum registro encontrado no arquivo storage/logs/cron.log',
            'rotinas' => [
                'verificar_vencidas' => CmsSetting::get('cron_last_run_verificar_vencidas', 'Nunca executado'),
                'enviar_lembretes' => CmsSetting::get('cron_last_run_enviar_lembretes', 'Nunca executado'),
                'sincronizar_pagamentos' => CmsSetting::get('cron_last_run_sincronizar_pagamentos', 'Nunca executado'),
            ]
        ]);
    }

    /**
     * Dispara manualmente uma tarefa de cron sob demanda via AJAX
     */
    public function executar(Request $request)
    {
        $request->validate([
            'tarefa' => 'required|string|in:verificar_vencidas,enviar_lembretes,sincronizar_pagamentos',
        ]);

        $tarefa = $request->input('tarefa');
        $comandos = [
            'verificar_vencidas' => 'faturas:verificar-vencidas',
            'enviar_lembretes' => 'faturas:enviar-lembretes',
            'sincronizar_pagamentos' => 'gateways:sincronizar-pagamentos',
        ];

        $cmd = $comandos[$tarefa];

        try {
            Artisan::call($cmd);
            $output = Artisan::output();
            $dataHora = date('d/m/Y H:i:s');

            CmsSetting::set("cron_last_run_{$tarefa}", $dataHora, 'cron');

            return response()->json([
                'success' => true,
                'message' => "Comando {$cmd} executado com sucesso!",
                'output' => trim($output) ?: 'Comando finalizado sem saídas no console.',
                'data_hora' => $dataHora,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar o comando: ' . $e->getMessage(),
            ], 500);
        }
    }
}
