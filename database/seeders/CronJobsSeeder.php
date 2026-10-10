<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronJob;

class CronJobsSeeder extends Seeder
{
    public function run(): void
    {
        $padroes = [
            [
                'nome' => 'Verificação de Faturas Vencidas',
                'descricao' => 'Atualiza status para vencida e dispara e-mail de alerta de suspensão ao cliente.',
                'comando' => 'faturas:verificar-vencidas',
                'tipo' => 'artisan',
                'expressao_cron' => '0 6 * * *',
                'frequencia_descricao' => 'Diariamente às 06:00',
                'ativo' => true,
                'sistema' => true,
            ],
            [
                'nome' => 'Lembrete Preventivo de Vencimento',
                'descricao' => 'Alerta os clientes com 3 dias de antecedência sobre o vencimento da mensalidade.',
                'comando' => 'faturas:enviar-lembretes',
                'tipo' => 'artisan',
                'expressao_cron' => '0 8 * * *',
                'frequencia_descricao' => 'Diariamente às 08:00',
                'ativo' => true,
                'sistema' => true,
            ],
            [
                'nome' => 'Sincronização de Pagamentos dos Gateways',
                'descricao' => 'Consulta status de PIX e Boletos pendentes no Mercado Pago e Efí PRO.',
                'comando' => 'gateways:sincronizar-pagamentos',
                'tipo' => 'artisan',
                'expressao_cron' => '*/15 * * * *',
                'frequencia_descricao' => 'A cada 15 minutos',
                'ativo' => true,
                'sistema' => true,
            ],
            [
                'nome' => 'Limpeza e Manutenção do Sistema',
                'descricao' => 'Remove tokens de redefinição expirados e poda arquivos temporários antigos.',
                'comando' => 'sistema:limpeza-manutencao',
                'tipo' => 'artisan',
                'expressao_cron' => '0 3 * * *',
                'frequencia_descricao' => 'Diariamente às 03:00',
                'ativo' => true,
                'sistema' => true,
            ],
            [
                'nome' => 'Lembrete de Agendamentos e Instalações',
                'descricao' => 'Verifica serviços de instalação de rastreadores e estética agendados para o dia.',
                'comando' => 'agendamentos:lembrete-dia',
                'tipo' => 'artisan',
                'expressao_cron' => '0 7 * * *',
                'frequencia_descricao' => 'Diariamente às 07:00',
                'ativo' => true,
                'sistema' => true,
            ],
        ];

        foreach ($padroes as $item) {
            CronJob::updateOrCreate(
                ['comando' => $item['comando']],
                $item
            );
        }
    }
}
