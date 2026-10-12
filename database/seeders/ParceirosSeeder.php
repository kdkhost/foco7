<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parceiro;

class ParceirosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parceiros = [
            [
                'nome' => 'Sascar',
                'logo' => '/img/parceiros/sascar.svg',
                'link' => 'https://www.sascar.com.br',
                'descricao' => 'Líder em Gestão de Frotas e Telemetria Veicular',
                'ordem' => 1,
                'ativo' => true,
            ],
            [
                'nome' => 'Cobli',
                'logo' => '/img/parceiros/cobli.svg',
                'link' => 'https://www.cobli.co',
                'descricao' => 'Conectividade e Inteligência para Frotas',
                'ordem' => 2,
                'ativo' => true,
            ],
            [
                'nome' => 'Suntech',
                'logo' => '/img/parceiros/suntech.svg',
                'link' => 'https://www.suntech.com.br',
                'descricao' => 'Hardwares e Módulos GPS de Alta Precisão',
                'ordem' => 3,
                'ativo' => true,
            ],
            [
                'nome' => 'Queclink',
                'logo' => '/img/parceiros/queclink.svg',
                'link' => 'https://www.queclink.com',
                'descricao' => 'Líder Global em Dispositivos IoT e Rastreamento',
                'ordem' => 4,
                'ativo' => true,
            ],
            [
                'nome' => 'Teltonika',
                'logo' => '/img/parceiros/teltonika.svg',
                'link' => 'https://teltonika-gps.com',
                'descricao' => 'Telemetria Europeia Homologada pela Anatel',
                'ordem' => 5,
                'ativo' => true,
            ],
            [
                'nome' => 'Porto Seguro',
                'logo' => '/img/parceiros/porto-seguro.svg',
                'link' => 'https://www.portoseguro.com.br',
                'descricao' => 'Assistência e Proteção Veicular de Confiança',
                'ordem' => 6,
                'ativo' => true,
            ],
            [
                'nome' => 'Mapfre',
                'logo' => '/img/parceiros/mapfre.svg',
                'link' => 'https://www.mapfre.com.br',
                'descricao' => 'Cobertura e Atendimento 24 Horas em Todo o Brasil',
                'ordem' => 7,
                'ativo' => true,
            ],
            [
                'nome' => 'Tokio Marine',
                'logo' => '/img/parceiros/tokio-marine.svg',
                'link' => 'https://www.tokiomarine.com.br',
                'descricao' => 'Tranquilidade e Soluções Integradas em Segurança',
                'ordem' => 8,
                'ativo' => true,
            ],
        ];

        foreach ($parceiros as $parceiro) {
            Parceiro::updateOrCreate(
                ['nome' => $parceiro['nome']],
                $parceiro
            );
        }
    }
}
