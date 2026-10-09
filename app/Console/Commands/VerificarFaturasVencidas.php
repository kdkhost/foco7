<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fatura;
use App\Services\EmailTemplateService;

class VerificarFaturasVencidas extends Command
{
    protected $signature = 'faturas:verificar-vencidas';
    protected $description = 'Identifica faturas pendentes com vencimento expirado, marca como vencidas e envia notificação';

    public function handle(): int
    {
        $hoje = now()->toDateString();
        $this->info("Iniciando verificação de faturas vencidas até {$hoje}...");

        $faturas = Fatura::with(['user', 'plano'])
            ->where('status', 'pendente')
            ->where('data_vencimento', '<', $hoje)
            ->get();

        $total = $faturas->count();
        if ($total === 0) {
            $this->info("Nenhuma fatura vencida pendente de processamento.");
            return Command::SUCCESS;
        }

        $processadas = 0;
        foreach ($faturas as $fatura) {
            $fatura->status = 'vencida';
            $fatura->save();
            $processadas++;

            if ($fatura->user && !empty($fatura->user->email)) {
                EmailTemplateService::enviar($fatura->user->email, 'fatura_vencida', [
                    'cliente_nome' => $fatura->user->name,
                    'cliente_email' => $fatura->user->email,
                    'fatura_id' => $fatura->codigo_fatura,
                    'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                    'valor' => $fatura->valor_formatado,
                    'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                ]);
            }
        }

        $this->info("Concluído: {$processadas} fatura(s) atualizada(s) para status 'vencida'.");
        return Command::SUCCESS;
    }
}
