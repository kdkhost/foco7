<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Depoimento;
use App\Models\Notificacao;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\Http;

class DepoimentosEAntiSpamRecaptchaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_depoimentos@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa acesso do administrador à página dedicada de depoimentos e moderação
     */
    public function test_admin_pode_acessar_pagina_dedicada_de_depoimentos(): void
    {
        Depoimento::create([
            'nome_cliente' => 'Marcos Vinicius',
            'cidade_uf' => 'Belém - PA',
            'veiculo' => 'Corolla 2023',
            'estrelas' => 5,
            'texto' => 'Excelente rastreamento e ronda tática veloz.',
            'ativo' => true,
        ]);

        Depoimento::create([
            'nome_cliente' => 'Larissa Souza',
            'cidade_uf' => 'Ananindeua - PA',
            'veiculo' => 'HB20 2022',
            'estrelas' => 4,
            'texto' => 'Muito bom o atendimento via WhatsApp.',
            'ativo' => false, // Pendente de moderação
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/depoimentos');

        $response->assertStatus(200);
        $response->assertSee('Marcos Vinicius');
        $response->assertSee('Larissa Souza');
        $response->assertSee('Moderação de avaliações');
    }

    /**
     * Testa envio de depoimento do cliente pelo site que entra como inativo para moderação
     */
    public function test_depoimento_enviado_pelo_site_entra_como_inativo_aguardando_moderacao(): void
    {
        $response = $this->postJson('/depoimento/enviar', [
            'nome_cliente' => 'Fabio Silveira',
            'cidade_uf' => 'Belém - PA',
            'veiculo' => 'Hilux 2024',
            'estrelas' => 5,
            'texto' => 'Instalação rápida e equipe muito atenciosa no plantão!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifica que o depoimento foi salvo com ativo = false (pendente de moderação)
        $this->assertDatabaseHas('depoimentos', [
            'nome_cliente' => 'Fabio Silveira',
            'estrelas' => 5,
            'ativo' => false,
        ]);

        // Verifica que gerou notificação dedicada para a equipe moderar
        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'depoimento',
            'lida' => false,
        ]);
    }

    /**
     * Testa aprovação e alternância de status de depoimento pelo administrador via AJAX
     */
    public function test_admin_pode_aprovar_ou_reprovar_depoimento_via_ajax(): void
    {
        $depoimento = Depoimento::create([
            'nome_cliente' => 'Mariana Campos',
            'cidade_uf' => 'Castanhal - PA',
            'estrelas' => 5,
            'texto' => 'Ótimo serviço, recomendo a todos.',
            'ativo' => false,
        ]);

        // 1. Aprova o depoimento
        $response = $this->actingAs($this->adminUser)->postJson("/admin/depoimentos/{$depoimento->id}/toggle-aprovacao");
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'ativo' => true]);
        $this->assertDatabaseHas('depoimentos', ['id' => $depoimento->id, 'ativo' => true]);

        // 2. Suspende/Desativa o depoimento
        $response2 = $this->actingAs($this->adminUser)->postJson("/admin/depoimentos/{$depoimento->id}/toggle-aprovacao");
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'ativo' => false]);
        $this->assertDatabaseHas('depoimentos', ['id' => $depoimento->id, 'ativo' => false]);
    }

    /**
     * Testa cadastro, consulta e edição de depoimento no painel administrativo
     */
    public function test_admin_pode_cadastrar_e_editar_depoimento_no_painel(): void
    {
        // 1. Cadastrar novo
        $response = $this->actingAs($this->adminUser)->postJson('/admin/depoimentos/salvar', [
            'nome_cliente' => 'Renato Pacheco',
            'cidade_uf' => 'Belém - PA',
            'veiculo' => 'Yamaha MT-07',
            'estrelas' => 5,
            'texto' => 'O corte de combustível pelo aplicativo funcionou perfeitamente no teste.',
            'ativo' => '1',
            'ordem' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('depoimentos', ['nome_cliente' => 'Renato Pacheco', 'ativo' => true]);

        $depoimento = Depoimento::where('nome_cliente', 'Renato Pacheco')->first();

        // 2. Obter via AJAX para edição
        $responseObter = $this->actingAs($this->adminUser)->getJson("/admin/depoimentos/{$depoimento->id}/obter");
        $responseObter->assertStatus(200);
        $responseObter->assertJson(['success' => true, 'depoimento' => ['nome_cliente' => 'Renato Pacheco']]);

        // 3. Atualizar depoimento
        $responseUpdate = $this->actingAs($this->adminUser)->postJson('/admin/depoimentos/salvar', [
            'id' => $depoimento->id,
            'nome_cliente' => 'Renato Pacheco Atualizado',
            'cidade_uf' => 'Belém - PA',
            'veiculo' => 'Yamaha MT-07',
            'estrelas' => 5,
            'texto' => 'Texto atualizado com satisfação máxima.',
            'ativo' => '1',
            'ordem' => 1,
        ]);

        $responseUpdate->assertStatus(200);
        $this->assertDatabaseHas('depoimentos', ['id' => $depoimento->id, 'nome_cliente' => 'Renato Pacheco Atualizado']);
    }

    /**
     * Testa exclusão de depoimento via AJAX
     */
    public function test_admin_pode_excluir_depoimento_via_ajax(): void
    {
        $depoimento = Depoimento::create([
            'nome_cliente' => 'Depoimento Para Exclusao',
            'cidade_uf' => 'Belém - PA',
            'estrelas' => 1,
            'texto' => 'Spam ou depoimento falso que deve ser excluído.',
            'ativo' => false,
        ]);

        $response = $this->actingAs($this->adminUser)->deleteJson("/admin/depoimentos/{$depoimento->id}/excluir");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('depoimentos', ['id' => $depoimento->id]);
    }

    /**
     * Testa se bot que preenche o campo honeypot invisível é neutralizado sem poluir o sistema
     */
    public function test_contato_rejeita_bot_honeypot_silenciosamente(): void
    {
        $response = $this->postJson('/contato', [
            'nome' => 'Bot Spammer',
            'telefone' => '(91) 99999-9999',
            'mensagem' => 'Compre produtos falsos aqui',
            '_foco_hp_website' => 'http://site-de-spam.com', // Preenchido pelo bot
        ]);

        // Retorna 200 simulado para despistar o bot
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Não deve registrar notificação no banco de dados
        $this->assertDatabaseMissing('notificacoes', [
            'titulo' => 'Nova Mensagem de Contato do Site',
        ]);
    }

    /**
     * Testa bloqueio de injeção SQL e XSS no formulário de contato
     */
    public function test_contato_bloqueia_injecao_sql_e_xss(): void
    {
        // 1. Tentativa de SQL Injection
        $responseSql = $this->postJson('/contato', [
            'nome' => 'Tester',
            'telefone' => '(91) 98000-0000',
            'mensagem' => "' OR '1'='1' UNION SELECT * FROM users; --",
        ]);

        $responseSql->assertStatus(422);
        $responseSql->assertJson(['success' => false]);

        // 2. Tentativa de Script XSS Injection
        $responseXss = $this->postJson('/contato', [
            'nome' => 'Tester',
            'telefone' => '(91) 98000-0000',
            'mensagem' => "<script>alert('hack')</script>",
        ]);

        $responseXss->assertStatus(422);
        $responseXss->assertJson(['success' => false]);
    }

    /**
     * Testa validação do Google reCAPTCHA v3 quando ativado no painel
     */
    public function test_contato_valida_recaptcha_v3_quando_ativado(): void
    {
        CmsSetting::set('recaptcha_ativo', '1', 'sistema');
        CmsSetting::set('recaptcha_site_key', 'test_site_key_123', 'sistema');
        CmsSetting::set('recaptcha_secret_key', 'test_secret_key_456', 'sistema');

        // 1. Envio sem token deve ser rejeitado
        $responseSemToken = $this->postJson('/contato', [
            'nome' => 'Cliente Legítimo',
            'telefone' => '(91) 98111-2222',
            'mensagem' => 'Gostaria de cotar o rastreador para meu veículo.',
        ]);

        $responseSemToken->assertStatus(422);
        $responseSemToken->assertJson(['success' => false]);

        // 2. Envio com resposta simulada da API Google (reCAPTCHA Score Alto = Humano)
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'contato',
            ], 200),
        ]);

        $responseComToken = $this->postJson('/contato', [
            'nome' => 'Cliente Legítimo',
            'telefone' => '(91) 98111-2222',
            'mensagem' => 'Gostaria de cotar o rastreador para meu veículo.',
            'g-recaptcha-response' => 'fake_recaptcha_token_human',
        ]);

        $responseComToken->assertStatus(200);
        $responseComToken->assertJson(['success' => true]);

        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'contato',
            'titulo' => 'Nova Mensagem de Contato do Site',
        ]);
    }

    /**
     * Testa persistência das configurações de reCAPTCHA no painel
     */
    public function test_admin_pode_salvar_configuracoes_do_recaptcha(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/admin/configuracoes/salvar', [
            'site_nome' => 'Foco 7 Rastreamento',
            'recaptcha_site_key' => 'nova_chave_site_v3',
            'recaptcha_secret_key' => 'nova_chave_secret_v3',
            'recaptcha_ativo' => '1',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('nova_chave_site_v3', CmsSetting::get('recaptcha_site_key'));
        $this->assertEquals('nova_chave_secret_v3', CmsSetting::get('recaptcha_secret_key'));
        $this->assertEquals('1', CmsSetting::get('recaptcha_ativo'));
    }
}
