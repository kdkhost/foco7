<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Notificacao;
use App\Models\Agendamento;

class CentralNotificacoesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_notif@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa se o envio do formulário de contato registra notificação na Central
     */
    public function test_formulario_de_contato_cria_notificacao_na_central(): void
    {
        $response = $this->postJson('/contato', [
            'nome' => 'Carlos Alberto Santos',
            'telefone' => '(91) 98111-2233',
            'email' => 'carlos@exemplo.com',
            'veiculo' => 'Toyota Hilux 2023',
            'mensagem' => 'Gostaria de saber como funciona o bloqueio remoto via app.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'contato',
            'titulo' => 'Nova Mensagem de Contato do Site',
            'lida' => false,
        ]);
    }

    /**
     * Testa se o agendamento de cliente autenticado registra notificação
     */
    public function test_agendamento_cria_notificacao_na_central(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $response = $this->actingAs($cliente)->postJson('/agendar', [
            'cliente_nome' => 'Juliana Lima',
            'cliente_telefone' => '(91) 98777-6655',
            'veiculo_modelo' => 'Honda Civic G10',
            'cidade' => 'Belém',
            'tipo_pagamento' => 'online',
            'tipo_servico' => 'Instalação de Rastreador Foco 7',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'agendamento',
            'lida' => false,
        ]);
    }

    /**
     * Testa a listagem da Central de Notificações no Admin
     */
    public function test_admin_consegue_acessar_central_de_notificacoes(): void
    {
        Notificacao::registrar(
            tipo: 'contato',
            titulo: 'Mensagem de Teste',
            mensagem: 'Conteúdo de teste para a Central de Notificações',
            notificarEmail: false
        );

        $response = $this->actingAs($this->adminUser)->get('/admin/notificacoes');

        $response->assertStatus(200);
        $response->assertSee('Central de Notificações');
        $response->assertSee('Mensagem de Teste');
    }

    /**
     * Testa o endpoint AJAX de polling do sininho da navbar
     */
    public function test_endpoint_ajax_ultimas_notificacoes(): void
    {
        Notificacao::registrar(
            tipo: 'agendamento',
            titulo: 'Agendamento Recente',
            mensagem: 'Agendamento de teste para polling',
            notificarEmail: false
        );

        $response = $this->actingAs($this->adminUser)->getJson('/admin/notificacoes/ultimas-ajax');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'nao_lidas_count',
            'ultimas',
        ]);
        $this->assertEquals(1, $response->json('nao_lidas_count'));
    }

    /**
     * Testa marcar notificação individual como lida
     */
    public function test_marcar_notificacao_como_lida(): void
    {
        $notif = Notificacao::registrar(
            tipo: 'contato',
            titulo: 'Notificação para Ler',
            mensagem: 'Mensagem...',
            notificarEmail: false
        );

        $response = $this->actingAs($this->adminUser)->postJson("/admin/notificacoes/{$notif->id}/lida");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('notificacoes', [
            'id' => $notif->id,
            'lida' => true,
        ]);
    }

    /**
     * Testa marcar todas as notificações como lidas
     */
    public function test_marcar_todas_notificacoes_como_lidas(): void
    {
        Notificacao::registrar('contato', 'N1', 'M1', null, [], false);
        Notificacao::registrar('agendamento', 'N2', 'M2', null, [], false);

        $response = $this->actingAs($this->adminUser)->postJson('/admin/notificacoes/marcar-todas');

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'nao_lidas_count' => 0]);

        $this->assertEquals(0, Notificacao::naoLidas()->count());
    }

    /**
     * Testa disparo de notificação push de teste
     */
    public function test_disparo_de_teste_push(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/admin/notificacoes/teste');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'sistema',
            'titulo' => 'Teste de Notificação Push & E-mail',
        ]);
    }

    /**
     * Testa se clicar para abrir notificação marca como lida e redireciona
     */
    public function test_abrir_notificacao_marca_como_lida_e_redireciona(): void
    {
        $notif = Notificacao::registrar(
            tipo: 'contato',
            titulo: 'Lead para abrir',
            mensagem: 'Mensagem do lead...',
            linkAcao: route('admin.notificacoes.index'),
            notificarEmail: false
        );

        $this->assertFalse($notif->lida);

        $response = $this->actingAs($this->adminUser)->get("/admin/notificacoes/{$notif->id}/abrir");

        $response->assertRedirect(route('admin.notificacoes.index'));

        $this->assertDatabaseHas('notificacoes', [
            'id' => $notif->id,
            'lida' => true,
        ]);
    }

    /**
     * Testa obter notificação via AJAX para o modal de leitura marcando como lida automaticamente
     */
    public function test_obter_notificacao_via_ajax_para_modal_marca_como_lida_automaticamente(): void
    {
        $notif = Notificacao::registrar(
            tipo: 'contato',
            titulo: 'Visualização Modal',
            mensagem: 'Mensagem detalhada do cliente',
            linkAcao: route('admin.notificacoes.index'),
            notificarEmail: false
        );

        $this->assertFalse($notif->lida);

        $response = $this->actingAs($this->adminUser)->getJson("/admin/notificacoes/{$notif->id}/obter");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'notificacao' => [
                'id' => $notif->id,
                'titulo' => 'Visualização Modal',
                'lida' => true,
            ],
            'foi_marcada_agora' => true,
        ]);

        $this->assertTrue($notif->fresh()->lida);
    }

    /**
     * Testa salvar configurações da Central de Notificações incluindo OneSignal com imagem
     */
    public function test_salvar_configuracoes_onesignal_e_email(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/admin/notificacoes/salvar-config', [
            'admin_email_notificacoes' => 'gestao@foco7.com.br',
            'notificar_email_contatos' => '1',
            'notificar_email_agendamentos' => '1',
            'onesignal_ativo' => '1',
            'onesignal_app_id' => 'test-app-id-12345',
            'onesignal_rest_api_key' => 'test-api-key-67890',
            'onesignal_imagem_padrao' => 'https://foco7.com.br/img/og-foco7.jpg',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('gestao@foco7.com.br', \App\Models\CmsSetting::get('admin_email_notificacoes'));
        $this->assertEquals('test-app-id-12345', \App\Models\CmsSetting::get('onesignal_app_id'));
        $this->assertEquals('test-api-key-67890', \App\Models\CmsSetting::get('onesignal_rest_api_key'));
        $this->assertEquals('1', \App\Models\CmsSetting::get('onesignal_ativo'));
        $this->assertEquals('https://foco7.com.br/img/og-foco7.jpg', \App\Models\CmsSetting::get('onesignal_imagem_padrao'));
    }
}
