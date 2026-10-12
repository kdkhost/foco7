<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Filial;

class FiliaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            [
                'nome' => 'Matriz Belém (Nazaré)',
                'cidade' => 'Belém',
                'estado' => 'PA',
                'endereco' => 'Tv. Quintino Bocaiúva, 1200',
                'bairro' => 'Nazaré',
                'cep' => '66053-240',
                'telefone' => '(91) 3222-7000',
                'whatsapp' => '(91) 98051-0736',
                'email' => 'belem@foco7.com.br',
                'horario_atendimento' => 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00',
                'latitude' => -1.4502989,
                'longitude' => -48.4881242,
                'link_rota' => null,
                'destaque' => true,
                'ativo' => true,
                'ordem' => 1,
            ],
            [
                'nome' => 'Filial Ananindeua',
                'cidade' => 'Ananindeua',
                'estado' => 'PA',
                'endereco' => 'Rod. BR-316, Km 03, 1420',
                'bairro' => 'Coqueiro',
                'cep' => '67113-000',
                'telefone' => '(91) 3255-8080',
                'whatsapp' => '(91) 98051-0736',
                'email' => 'ananindeua@foco7.com.br',
                'horario_atendimento' => 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00',
                'latitude' => -1.4017675,
                'longitude' => -48.4277227,
                'link_rota' => null,
                'destaque' => false,
                'ativo' => true,
                'ordem' => 2,
            ],
            [
                'nome' => 'Filial Castanhal',
                'cidade' => 'Castanhal',
                'estado' => 'PA',
                'endereco' => 'Av. Barão do Rio Branco, 850',
                'bairro' => 'Centro',
                'cep' => '68743-050',
                'telefone' => '(91) 3721-4500',
                'whatsapp' => '(91) 98051-0736',
                'email' => 'castanhal@foco7.com.br',
                'horario_atendimento' => 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00',
                'latitude' => -1.3014937,
                'longitude' => -47.9961794,
                'link_rota' => null,
                'destaque' => false,
                'ativo' => true,
                'ordem' => 3,
            ],
            [
                'nome' => 'Filial São Luís',
                'cidade' => 'São Luís',
                'estado' => 'MA',
                'endereco' => 'Av. Colares Moreira, 444',
                'bairro' => 'Renascença',
                'cep' => '65075-441',
                'telefone' => '(98) 3235-9000',
                'whatsapp' => '(91) 98051-0736',
                'email' => 'saoluis@foco7.com.br',
                'horario_atendimento' => 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00',
                'latitude' => -2.5004301,
                'longitude' => -44.2860749,
                'link_rota' => null,
                'destaque' => false,
                'ativo' => true,
                'ordem' => 4,
            ],
            [
                'nome' => 'Filial Fortaleza',
                'cidade' => 'Fortaleza',
                'estado' => 'CE',
                'endereco' => 'Av. Santos Dumont, 2122',
                'bairro' => 'Aldeota',
                'cep' => '60150-160',
                'telefone' => '(85) 3030-4040',
                'whatsapp' => '(91) 98051-0736',
                'email' => 'fortaleza@foco7.com.br',
                'horario_atendimento' => 'Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00',
                'latitude' => -3.7425643,
                'longitude' => -38.4787474,
                'link_rota' => null,
                'destaque' => false,
                'ativo' => true,
                'ordem' => 5,
            ],
        ];

        foreach ($unidades as $dados) {
            Filial::updateOrCreate(
                ['nome' => $dados['nome']],
                $dados
            );
        }
    }
}
