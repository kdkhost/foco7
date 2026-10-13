<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\CronJob;
use Database\Seeders\CronJobsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CronController extends Controller
{
    /**
     * Central dedicada de Tarefas Cron & Agendamentos
     */
    public function index()
    {
        $comandoCpanel = '* * * * * cd /home/fococomb/public_html && /usr/local/bin/php artisan schedule:run >> /home/fococomb/public_html/storage/logs/cron.log 2>&1';

        // Garante que as rotinas vitais do sistema estejam cadastradas no banco
        if (Schema::hasTable('cron_jobs') && CronJob::count() === 0) {
            try {
                (new CronJobsSeeder())->run();
            } catch (Throwable $e) {
                // Silencia se houver inconsistência momentânea
            }
        }

        $cronJobs = Schema::hasTable('cron_jobs')
            ? CronJob::orderBy('sistema', 'desc')->orderBy('id', 'asc')->get()
            : collect();

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

        $agoraSp = now()->setTimezone('America/Sao_Paulo');
        $horarioServidorSp = $agoraSp->format('d/m/Y H:i:s');
        $timestampServidorMs = $agoraSp->getTimestamp() * 1000;

        return view('admin.cron.index', compact('comandoCpanel', 'cronJobs', 'cronAtivo', 'ultimoBatimento', 'diffEmSegundos', 'logRecente', 'horarioServidorSp', 'timestampServidorMs'));
    }

    /**
     * Retorna o status em tempo real do agendador, jobs e logs via AJAX
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

        $jobsData = [];
        if (Schema::hasTable('cron_jobs')) {
            $cronJobs = CronJob::orderBy('sistema', 'desc')->orderBy('id', 'asc')->get();
            foreach ($cronJobs as $j) {
                $jobsData[$j->id] = [
                    'id' => $j->id,
                    'nome' => $j->nome,
                    'ativo' => $j->ativo,
                    'ultima_execucao' => $j->ultima_execucao_formatada,
                    'ultimo_status' => $j->ultimo_status,
                    'status_badge' => $j->status_badge,
                ];
            }
        }

        $agoraSp = now()->setTimezone('America/Sao_Paulo');

        return response()->json([
            'success' => true,
            'cron_ativo' => $cronAtivo,
            'ultimo_batimento' => $ultimoBatimento,
            'diff_segundos' => $diffEmSegundos,
            'horario_servidor' => $agoraSp->format('d/m/Y H:i:s'),
            'timestamp_ms' => $agoraSp->getTimestamp() * 1000,
            'log_recente' => $logRecente ?: 'Nenhum registro encontrado no arquivo storage/logs/cron.log',
            'jobs' => $jobsData,
        ]);
    }

    /**
     * Dispara manualmente uma tarefa de cron sob demanda via AJAX
     */
    public function executar(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer',
            'tarefa' => 'nullable|string',
        ]);

        $cronJob = null;
        if ($request->filled('id')) {
            $cronJob = CronJob::find($request->input('id'));
        } elseif ($request->filled('tarefa')) {
            $comandosLegados = [
                'verificar_vencidas' => 'faturas:verificar-vencidas',
                'enviar_lembretes' => 'faturas:enviar-lembretes',
                'sincronizar_pagamentos' => 'gateways:sincronizar-pagamentos',
            ];
            $cmd = $comandosLegados[$request->input('tarefa')] ?? $request->input('tarefa');
            $cronJob = CronJob::where('comando', $cmd)->first();
        }

        if (!$cronJob) {
            return response()->json([
                'success' => false,
                'message' => 'Tarefa cron não localizada no sistema.',
            ], 404);
        }

        try {
            $output = '';
            $agora = now();

            if ($cronJob->tipo === 'url') {
                $response = Http::timeout(20)->get($cronJob->comando);
                $statusHttp = $response->status();
                $body = substr($response->body(), 0, 500);
                $output = "Requisição HTTP GET enviada para: {$cronJob->comando}\nStatus: {$statusHttp}\nResposta:\n{$body}";
            } else {
                Artisan::call($cronJob->comando);
                $output = Artisan::output();
            }

            $outputFormatado = trim($output) ?: 'Comando finalizado com sucesso sem saídas extras no console.';

            $cronJob->update([
                'ultima_execucao' => $agora,
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $outputFormatado,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Rotina '{$cronJob->nome}' executada com sucesso!",
                'output' => $outputFormatado,
                'data_hora' => $agora->format('d/m/Y H:i:s'),
                'job_id' => $cronJob->id,
            ]);
        } catch (Throwable $e) {
            if ($cronJob) {
                $cronJob->update([
                    'ultima_execucao' => now(),
                    'ultimo_status' => 'erro',
                    'ultimo_output' => 'Erro: ' . $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar o comando: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cadastra uma nova rotina de cron manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:500',
            'tipo' => 'required|in:artisan,url',
            'comando' => 'required|string|max:500',
            'expressao_cron' => 'required|string|max:50',
            'frequencia_descricao' => 'nullable|string|max:100',
            'ativo' => 'nullable|boolean',
        ], [
            'nome.required' => 'O nome da rotina é obrigatório.',
            'comando.required' => 'O comando Artisan ou URL é obrigatório.',
            'expressao_cron.required' => 'A expressão cron é obrigatória.',
        ]);

        $cronJob = CronJob::create([
            'nome' => $request->input('nome'),
            'descricao' => $request->input('descricao'),
            'tipo' => $request->input('tipo'),
            'comando' => trim($request->input('comando')),
            'expressao_cron' => trim($request->input('expressao_cron')),
            'frequencia_descricao' => $request->input('frequencia_descricao') ?: $request->input('expressao_cron'),
            'ativo' => $request->boolean('ativo', true),
            'sistema' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Nova tarefa cron '{$cronJob->nome}' cadastrada com sucesso!",
            'job' => $cronJob,
        ]);
    }

    /**
     * Atualiza uma rotina de cron existente
     */
    public function update(Request $request, $id)
    {
        $cronJob = CronJob::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:500',
            'tipo' => 'required|in:artisan,url',
            'comando' => 'required|string|max:500',
            'expressao_cron' => 'required|string|max:50',
            'frequencia_descricao' => 'nullable|string|max:100',
            'ativo' => 'nullable|boolean',
        ]);

        // Se for rotina de sistema, preserva comando e tipo originais por segurança
        $dados = [
            'nome' => $request->input('nome'),
            'descricao' => $request->input('descricao'),
            'expressao_cron' => trim($request->input('expressao_cron')),
            'frequencia_descricao' => $request->input('frequencia_descricao') ?: $request->input('expressao_cron'),
            'ativo' => $request->boolean('ativo', true),
        ];

        if (!$cronJob->sistema) {
            $dados['tipo'] = $request->input('tipo');
            $dados['comando'] = trim($request->input('comando'));
        }

        $cronJob->update($dados);

        return response()->json([
            'success' => true,
            'message' => "Tarefa cron '{$cronJob->nome}' atualizada com sucesso!",
            'job' => $cronJob,
        ]);
    }

    /**
     * Alterna status ativo/pausado da tarefa cron via AJAX
     */
    public function toggle($id)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->ativo = !$cronJob->ativo;
        $cronJob->save();

        $statusTxt = $cronJob->ativo ? 'ativada' : 'pausada';

        return response()->json([
            'success' => true,
            'ativo' => $cronJob->ativo,
            'message' => "Rotina '{$cronJob->nome}' foi {$statusTxt} com sucesso!",
        ]);
    }

    /**
     * Remove uma tarefa personalizada
     */
    public function destroy($id)
    {
        $cronJob = CronJob::findOrFail($id);

        if ($cronJob->sistema) {
            return response()->json([
                'success' => false,
                'message' => 'Esta é uma rotina vital do sistema Foco 7 e não pode ser excluída.',
            ], 403);
        }

        $nome = $cronJob->nome;
        $cronJob->delete();

        return response()->json([
            'success' => true,
            'message' => "Rotina '{$nome}' removida com sucesso do agendador!",
        ]);
    }
}
