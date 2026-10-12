<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReguaCobrancaService;
use App\Models\CronJob;

class ProcessarReguaCobrancaCommand extends Command
{
    protected $signature = 'faturas:processar-regua';
    protected $description = 'Processa a régua completa de cobrança: gera mensalidades antecipadas, dispara lembretes pré/pós-vencimento e atualiza encargos';

    public function handle(): int
    {
        $this->info("Iniciando processamento da Régua de Cobrança Foco 7...");

        $resultado = ReguaCobrancaService::processarFila();

        if (!$resultado['sucesso']) {
            $this->warn($resultado['mensagem']);
            return Command::SUCCESS;
        }

        $m = $resultado['metricas'];
        $this->info("--- Resumo da Operação ---");
        $this->line("Novas mensalidades geradas: {$m['mensalidades_geradas']}");
        $this->line("1º Lembrete Pré-vencimento: {$m['lembretes_pre_1']}");
        $this->line("2º Lembrete Pré-vencimento: {$m['lembretes_pre_2']}");
        $this->line("3º Lembrete Pré-vencimento: {$m['lembretes_pre_3']}");
        $this->line("Avisos de Vencimento Hoje: {$m['vencimento_hoje']}");
        $this->line("1º Aviso de Atraso: {$m['atraso_1']}");
        $this->line("2º Aviso de Atraso: {$m['atraso_2']}");
        $this->line("3º Aviso de Atraso (Suspensão): {$m['atraso_3']}");
        $this->line("Faturas com encargos atualizados: {$m['faturas_atualizadas_encargos']}");

        $msg = "Régua processada: {$m['mensalidades_geradas']} mensalidade(s) gerada(s), " .
            ($m['lembretes_pre_1'] + $m['lembretes_pre_2'] + $m['lembretes_pre_3'] + $m['vencimento_hoje']) . " lembrete(s) preventivo(s) e " .
            ($m['atraso_1'] + $m['atraso_2'] + $m['atraso_3']) . " aviso(s) de atraso enviados.";

        CronJob::updateOrCreate(
            ['comando' => 'faturas:processar-regua'],
            [
                'nome' => 'Régua de Cobrança & Mensalidades Recorrentes',
                'frequencia' => 'Diário às 07:00 e 13:00',
                'descricao' => 'Gera mensalidades antecipadas, dispara lembretes pré e pós-vencimento com cálculo de multa e juros.',
                'ultima_execucao' => now(),
                'ultimo_status' => 'sucesso',
                'ultimo_output' => $msg,
            ]
        );

        $this->info($msg);
        return Command::SUCCESS;
    }
}
