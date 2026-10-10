<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CorrigirEncodingCommand extends Command
{
    /**
     * Nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'sistema:corrigir-encoding {--dry-run : Apenas simula sem salvar no banco}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Corrige caracteres acentuados corrompidos (mojibake) e assegura integridade UTF-8 pura nas tabelas do sistema';

    /**
     * Dicionário completo de substituições de mojibake para UTF-8 brasileiro correto.
     */
    public static function fixMojibake(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        $map = [
            'SolicitaÃ§Ã£o' => 'Solicitação',
            'solicitaÃ§Ã£o' => 'solicitação',
            'Ã§Ã£o'        => 'ção',
            'Ã§Ãµes'       => 'ções',
            'Ã§'           => 'ç',
            'Ã‡'           => 'Ç',
            'Ã£'           => 'ã',
            'Ãƒ'           => 'Ã',
            'Ã¡'           => 'á',
            'Ã'           => 'Á',
            'Ã '           => 'Á',
            'Ã©'           => 'é',
            'Ã‰'           => 'É',
            'Ã­'           => 'í',
            'Ã'           => 'Í',
            'Ã³'           => 'ó',
            'Ã“'           => 'Ó',
            'Ãº'           => 'ú',
            'Ãš'           => 'Ú',
            'Ãª'           => 'ê',
            'ÃŠ'           => 'Ê',
            'Ã´'           => 'ô',
            'Ã”'           => 'Ô',
            'Ãµ'           => 'õ',
            'Ã•'           => 'Õ',
            'Ã¢'           => 'â',
            'Ã‚'           => 'Â',
            'Ã '           => 'à',
            'Ã€'           => 'À',
            'â€™'          => "'",
            'â€˜'          => "'",
            'â€œ'          => '"',
            'â€'          => '"',
            'â€“'          => '–',
            'â€”'          => '—',
            'Â°'           => '°',
            'Â·'           => '·',
            'Â '           => ' ',
        ];

        return str_replace(array_keys($map), array_values($map), $text);
    }

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $this->info('Iniciando auditoria e correção de acentuação e encoding UTF-8...');

        $tabelasColunas = [
            'agendamentos' => ['observacoes', 'veiculo_modelo', 'endereco', 'cidade', 'cancelamento_motivo', 'tipo_servico'],
            'configuracoes' => ['valor'],
            'depoimentos' => ['cliente_nome', 'cargo_cidade', 'texto', 'servico_utilizado'],
            'faqs' => ['pergunta', 'resposta', 'categoria'],
            'planos' => ['nome', 'descricao', 'recursos'],
            'servicos_estetica' => ['nome', 'subtitulo', 'descricao', 'itens_inclusos'],
            'banner_slides' => ['titulo', 'subtitulo', 'texto_botao', 'badge_texto'],
            'users' => ['name'],
            'leads' => ['nome', 'mensagem', 'veiculo_interesse'],
            'email_templates' => ['assunto', 'corpo_html'],
            'cron_jobs' => ['nome', 'descricao'],
        ];

        $totalCorrigidos = 0;

        foreach ($tabelasColunas as $tabela => $colunas) {
            if (!Schema::hasTable($tabela)) {
                continue;
            }

            foreach ($colunas as $coluna) {
                if (!Schema::hasColumn($tabela, $coluna)) {
                    continue;
                }

                // Busca registros com padrões corrompidos
                $registros = DB::table($tabela)
                    ->whereNotNull($coluna)
                    ->where(function ($query) use ($coluna) {
                        $query->where($coluna, 'like', '%Ã%')
                              ->orWhere($coluna, 'like', '%â%')
                              ->orWhere($coluna, 'like', '%Â%');
                    })
                    ->get();

                foreach ($registros as $reg) {
                    $original = $reg->{$coluna};
                    $corrigido = self::fixMojibake($original);

                    if ($original !== $corrigido) {
                        $this->line("Tabela [{$tabela}] ID #{$reg->id} coluna [{$coluna}]:");
                        $this->line("  Antes:  {$original}");
                        $this->line("  Depois: {$corrigido}");

                        if (!$isDryRun) {
                            DB::table($tabela)
                                ->where('id', $reg->id)
                                ->update([$coluna => $corrigido]);
                        }

                        $totalCorrigidos++;
                    }
                }
            }
        }

        if ($isDryRun) {
            $this->warn("Simulação concluída! {$totalCorrigidos} campos com mojibake identificados.");
        } else {
            $this->info("Sucesso! {$totalCorrigidos} campos corrigidos com acentuação UTF-8 perfeita.");
        }

        return 0;
    }
}
