<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\ServicoEstetica;
use App\Models\Agendamento;
use App\Models\Fatura;
use App\Models\BannerSlide;

class EsteticaEEdicaoAvancadaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Testa se a landing page exibe a seção de estética automotiva e os serviços
     */
    public function test_landing_page_exibe_secao_de_estetica_automotiva(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Estética');
        $response->assertSee('Polimento Técnico');
        $response->assertSee('Vitrificação Cerâmica');
    }

    /**
     * Testa o agendamento de estética automotiva exigindo autenticação obrigatória e tipo_pagamento
     */
    public function test_agendamento_publico_de_estetica_automotiva(): void
    {
        $servico = ServicoEstetica::first();

        $dados = [
            'cliente_nome' => 'Marcos Vinicius',
            'cliente_telefone' => '(91) 98111-2222',
            'cliente_email' => 'marcos@exemplo.com',
            'categoria' => 'estetica',
            'servico_estetica_id' => $servico->id,
            'veiculo_modelo' => 'Toyota Hilux 2024',
            'cidade' => 'Belém',
            'tipo_pagamento' => 'online',
            'data_preferencial' => date('Y-m-d', strtotime('+3 days')),
            'observacoes' => 'Gostaria do polimento completo.',
        ];

        // 1. Visitante não autenticado deve ser barrado com 401 e auth_required
        $responseNaoAuth = $this->postJson('/agendar', $dados);
        $responseNaoAuth->assertStatus(401);
        $responseNaoAuth->assertJson(['success' => false, 'auth_required' => true]);

        // 2. Cliente autenticado realiza o agendamento com sucesso
        $user = User::factory()->create(['role' => 'cliente']);
        $responseAuth = $this->actingAs($user)->postJson('/agendar', $dados);

        $responseAuth->assertStatus(200);
        $responseAuth->assertJson(['success' => true]);

        $this->assertDatabaseHas('agendamentos', [
            'cliente_nome' => 'Marcos Vinicius',
            'categoria' => 'estetica',
            'servico_estetica_id' => $servico->id,
            'veiculo_modelo' => 'Toyota Hilux 2024',
            'tipo_pagamento' => 'online',
        ]);
    }

    /**
     * Testa CRUD de Serviços de Estética no painel admin
     */
    public function test_admin_gerencia_servicos_de_estetica(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Acesso à página
        $response = $this->actingAs($admin)->get('/admin/estetica');
        $response->assertStatus(200);
        $response->assertSee('Adicionar Novo Serviço');

        // 2. Criação de novo serviço
        $responseCriar = $this->actingAs($admin)->postJson('/admin/estetica', [
            'nome' => 'Higienização de Ozônio Plus',
            'descricao' => 'Elimina 99.9% de fungos, ácaros e odores desagradáveis.',
            'preco' => '180,00',
            'tempo_estimado' => '1h 30min',
            'icone' => 'fa-wind',
            'ordem' => 10,
            'ativo' => 1,
        ]);
        $responseCriar->assertStatus(200);
        $responseCriar->assertJson(['success' => true]);

        $this->assertDatabaseHas('servicos_estetica', [
            'nome' => 'Higienização de Ozônio Plus',
            'preco' => 180.00,
        ]);
    }

    /**
     * Testa CRUD de Clientes no painel admin
     */
    public function test_admin_gerencia_clientes(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Acessa lista
        $response = $this->actingAs($admin)->get('/admin/clientes');
        $response->assertStatus(200);
        $response->assertSee('Cadastrar Novo Cliente');

        // 2. Cadastra novo cliente
        $responseCriar = $this->actingAs($admin)->postJson('/admin/clientes', [
            'name' => 'Ana Clara Peixoto',
            'email' => 'anaclara@teste.com',
            'telefone' => '(91) 98877-6655',
            'cpf_cnpj' => '123.456.789-00',
            'cidade' => 'Ananindeua',
            'status' => 'ativo',
            'password' => '12345678',
        ]);
        $responseCriar->assertStatus(200);
        $responseCriar->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'anaclara@teste.com',
            'role' => 'cliente',
        ]);
    }

    /**
     * Testa CRUD de Faturas no painel admin
     */
    public function test_admin_gerencia_faturas(): void
    {
        $admin = User::where('role', 'admin')->first();
        $cliente = User::where('role', 'cliente')->first();

        // 1. Acessa lista de faturas
        $response = $this->actingAs($admin)->get('/admin/faturas');
        $response->assertStatus(200);
        $response->assertSee('Lançar Nova Fatura');

        // 2. Emite nova fatura
        $responseCriar = $this->actingAs($admin)->postJson('/admin/faturas', [
            'user_id' => $cliente->id,
            'descricao' => 'Mensalidade Rastreamento Veicular - Outubro/2026',
            'valor' => '69,90',
            'data_vencimento' => date('Y-m-d', strtotime('+10 days')),
            'status' => 'pendente',
            'metodo_pagamento' => 'pix',
        ]);
        $responseCriar->assertStatus(200);
        $responseCriar->assertJson(['success' => true]);

        $this->assertDatabaseHas('faturas', [
            'user_id' => $cliente->id,
            'valor' => 69.90,
            'status' => 'pendente',
        ]);
    }

    /**
     * Testa rota de edição no CMS
     */
    public function test_admin_obtem_dados_de_banner_para_edicao(): void
    {
        $admin = User::where('role', 'admin')->first();
        $slide = BannerSlide::first();

        $response = $this->actingAs($admin)->getJson('/admin/cms/banner/obter/' . $slide->id);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'banner' => [
                'id' => $slide->id,
            ]
        ]);
    }

    /**
     * Testa cliente agendando estética pelo seu próprio painel
     */
    public function test_cliente_solicita_agendamento_de_estetica_no_painel(): void
    {
        $cliente = User::where('role', 'cliente')->first();
        $servico = ServicoEstetica::first();

        $response = $this->actingAs($cliente)->postJson('/cliente/agendamentos/solicitar', [
            'categoria' => 'estetica',
            'servico_estetica_id' => $servico->id,
            'veiculo_modelo' => 'Jeep Compass 2023',
            'veiculo_placa' => 'QER-5F99',
            'data_hora_inicio' => date('Y-m-d 14:00:00', strtotime('+2 days')),
            'observacoes' => 'Favor realizar o serviço no período da tarde.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('agendamentos', [
            'user_id' => $cliente->id,
            'categoria' => 'estetica',
            'veiculo_modelo' => 'Jeep Compass 2023',
            'veiculo_placa' => 'QER-5F99',
        ]);
    }
}
