<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agendamento;
use App\Models\CmsSetting;
use App\Models\CronJob;
use App\Services\EmailTemplateService;

class AgendamentosLembreteDia extends Command
{
    protected $signature = 'agendamentos:lembrete-dia';
    protected $description = 'Verifica agendamentos de rastreador e estética previstos para hoje e envia lembrete aos clientes';

    public function handle(): int
    {
        $hoje = now()->toDateString();
        $this->info("Buscando agendamentos programados para a data de hoje ({$hoje})...");

        $agendamentos = Agendamento::with('user')
            ->whereDate('data_hora_inicio', $hoje)
            ->whereIn('status', ['confirmado', 'pendente'])
            ->get();

        $total = $agendamentos->count();
        if ($total === 0) {
            $msg = "Nenhum agendamento programado para hoje.";
            $this->info($msg);

            $dataHora = now();
            CmsSetting::set('cron_last_run_agendamentos_dia', $dataHora->format('d/m/Y H:i:s'), 'cron');
            CronJob::where('comando', 'agendamentos:lembrete-dia')->update([
                'ultima_execucao' => $dataHora,
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $msg,
            ]);

            return Command::SUCCESS;
        }

        $notificados = 0;
        foreach ($agendamentos as $item) {
            $email = $item->cliente_email ?: ($item->user->email ?? null);
            if ($email) {
                // Tenta notificar se houver e-mail válido
                $hora = $item->data_hora_inicio ? $item->data_hora_inicio->format('H:i') : 'Horário a combinar';
                $servico = $item->tipo_servico ?: 'Atendimento';

                EmailTemplateService::enviar($email, 'lembrete_agendamento', [
                    'cliente_nome' => $item->cliente_nome,
                    'servico' => $servico,
                    'horario' => $hora,
                    'endereco' => $item->endereco_atendimento ?: 'Unidade Foco 7',
                    'telefone_suporte' => CmsSetting::get('contato_telefone', '(91) 98051-0736'),
                ]);
                $notificados++;
            }
        }

        $msg = "Processados {$total} agendamento(s) para hoje. {$notificados} notificação(ões) enviada(s).";
        $this->info($msg);

        $dataHora = now();
        CmsSetting::set('cron_last_run_agendamentos_dia', $dataHora->format('d/m/Y H:i:s'), 'cron');
        CronJob::where('comando', 'agendamentos:lembrete-dia')->update([
            'ultima_execucao' => $dataHora,
            'ultimo_status' => 'sucesso',
            'ultimo_output' => $msg,
        ]);

        return Command::SUCCESS;
    }
}
