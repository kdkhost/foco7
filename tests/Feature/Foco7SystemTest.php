<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\CmsSetting;
use App\Models\Plano;

class Foco7SystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Testa o carregamento da Landing Page pública com dados populados pelo seeder
     */
    public function test_landing_page_carrega_com_sucesso(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('FOCO');
        $response->assertSee('Plano Carro Protegido');
    }

    /**
     * Testa a tela de login
     */
    public function test_tela_de_login_carrega_com_sucesso(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Acesso Seguro ao Sistema');
    }

    /**
     * Testa o assistente de instalação
     */
    public function test_instalador_web_carrega_com_sucesso(): void
    {
        $response = $this->get('/instalar');
        $response->assertStatus(200);
        $response->assertSee('Assistente de Instalação');
    }

    /**
     * Testa autenticação do Administrador e acesso ao Dashboard
     */
    public function test_admin_consegue_acessar_painel(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Visão Geral');
    }

    /**
     * Testa que visitante não consegue acessar o painel admin
     */
    public function test_visitante_e_redirecionado_ao_tentar_acessar_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Testa que cliente não consegue acessar o painel admin (ACL)
     */
    public function test_cliente_nao_tem_acesso_ao_painel_admin(): void
    {
        $cliente = User::where('role', 'cliente')->first();

        $response = $this->actingAs($cliente)->get('/admin/dashboard');
        $response->assertRedirect('/cliente/dashboard');
    }

    /**
     * Testa acesso do cliente à Área do Cliente
     */
    public function test_cliente_acessa_area_do_cliente(): void
    {
        $cliente = User::where('role', 'cliente')->first();

        $response = $this->actingAs($cliente)->get('/cliente/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Central do Assinante');
        $response->assertSee('app-wrapper');
        $response->assertSee('app-sidebar');
        $response->assertSee('adminThemeToggle');
        $response->assertSee('Minhas Faturas');
        $response->assertSee('CLIENTE');
    }

    /**
     * Testa ativação do modo de manutenção
     */
    public function test_modo_de_manutencao_bloqueia_visitante_e_permite_bypass(): void
    {
        CmsSetting::set('manutencao_ativa', '1');
        CmsSetting::set('manutencao_ips_liberados', '200.100.50.25'); // IP diferente do visitante

        // Visitante comum recebe HTTP 503
        $response = $this->get('/');
        $response->assertStatus(503);
        $response->assertSee('Manutenção Programada');

        // Com token de bypass
        $responseBypass = $this->get('/manutencao/bypass/foco7_vip_bypass_2026');
        $responseBypass->assertRedirect('/');
        $responseBypass->assertCookie('foco7_maintenance_bypass', 'foco7_vip_bypass_2026');
    }

    /**
     * Testa envio de contato via AJAX e persistência como lead no banco
     */
    public function test_envio_de_contato_ajax(): void
    {
        $response = $this->postJson('/contato', [
            'nome' => 'Carlos Alberto',
            'telefone' => '(91) 98888-7777',
            'email' => 'carlos@teste.com',
            'mensagem' => 'Gostaria de saber o valor para frotas.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('agendamentos', [
            'cliente_nome' => 'Carlos Alberto',
            'cliente_telefone' => '(91) 98888-7777',
            'tipo_servico' => 'Contato / Dúvida via Landing Page',
            'status' => 'pendente',
        ]);
    }

    /**
     * Testa que as rotas de webhook estão isentas de CSRF e respondem com 200
     */
    public function test_webhooks_isentos_de_csrf(): void
    {
        $responseMp = $this->postJson('/webhook/mercadopago', [
            'type' => 'payment',
            'data' => ['id' => '123456789'],
        ]);
        $responseMp->assertStatus(200);
        $responseMp->assertJson(['status' => 'received']);

        $responseEfi = $this->postJson('/webhook/efi', [
            'pix' => [],
        ]);
        $responseEfi->assertStatus(200);
        $responseEfi->assertJson(['status' => 'received']);
    }

    /**
     * Testa que o template de lembrete de agendamento está disponível no EmailTemplateService
     */
    public function test_template_lembrete_agendamento_disponivel(): void
    {
        $templates = \App\Services\EmailTemplateService::getTemplates();
        $this->assertArrayHasKey('lembrete_agendamento', $templates);

        $preview = \App\Services\EmailTemplateService::renderPreview('lembrete_agendamento');
        $this->assertStringContainsString('AGENDAMENTO HOJE', $preview);
    }
}
