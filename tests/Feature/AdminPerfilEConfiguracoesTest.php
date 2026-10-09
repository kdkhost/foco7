<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\Hash;

class AdminPerfilEConfiguracoesTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Super Administrador',
            'email' => 'admin@foco7.com.br',
            'role' => 'admin',
            'password' => Hash::make('foco7@2026'),
            'telefone' => '(91) 8051-0736',
            'status' => 'ativo',
        ]);
    }

    public function test_admin_consegue_acessar_tela_de_perfil(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/perfil');
        $response->assertStatus(200);
        $response->assertSee('Perfil de Usuário');
        $response->assertSee('admin@foco7.com.br');
    }

    public function test_admin_consegue_atualizar_dados_do_perfil_via_ajax(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/admin/perfil/dados', [
            'name' => 'Administrador Geral Foco 7',
            'email' => 'admin@foco7.com.br',
            'telefone' => '(91) 98051-0736',
            'cpf_cnpj' => '12.345.678/0001-90',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->adminUser->id,
            'name' => 'Administrador Geral Foco 7',
        ]);
    }

    public function test_admin_altera_senha_com_validacao_da_senha_atual(): void
    {
        // Tentativa com senha atual incorreta
        $responseErro = $this->actingAs($this->adminUser)->postJson('/admin/perfil/senha', [
            'senha_atual' => 'senha_errada',
            'nova_senha' => 'novafoco7@2026',
            'nova_senha_confirmation' => 'novafoco7@2026',
        ]);

        $responseErro->assertStatus(422);

        // Sucesso com senha correta
        $responseOk = $this->actingAs($this->adminUser)->postJson('/admin/perfil/senha', [
            'senha_atual' => 'foco7@2026',
            'nova_senha' => 'novafoco7@2026',
            'nova_senha_confirmation' => 'novafoco7@2026',
        ]);

        $responseOk->assertStatus(200);
        $responseOk->assertJson(['success' => true]);
    }

    public function test_admin_consegue_acessar_e_salvar_configuracoes_do_sistema_e_smtp(): void
    {
        $responseGet = $this->actingAs($this->adminUser)->get('/admin/configuracoes');
        $responseGet->assertStatus(200);
        $responseGet->assertSee('Configurações Gerais & E-mail');

        $responsePost = $this->actingAs($this->adminUser)->postJson('/admin/configuracoes/salvar', [
            'site_nome' => 'Foco 7 Rastreamento Avançado',
            'contato_whatsapp' => '559180510736',
            'contato_whatsapp_formatado' => '(91) 8051-0736',
            'mail_host' => 'smtp.foco7.com.br',
            'mail_port' => 465,
            'mail_encryption' => 'ssl',
            'mail_username' => 'noreply@foco7.com.br',
            'mail_from_address' => 'contato@foco7.com.br',
            'mail_from_name' => 'Foco 7 Segurança',
        ]);

        $responsePost->assertStatus(200);
        $responsePost->assertJson(['success' => true]);

        $this->assertEquals('Foco 7 Rastreamento Avançado', CmsSetting::get('site_nome'));
    }

    public function test_admin_salva_configuracoes_completas_do_portal_de_pagamento(): void
    {
        $responsePost = $this->actingAs($this->adminUser)->postJson('/admin/gateways', [
            'mp_ativo' => '1',
            'mp_public_key' => 'APP_USR-TESTE-123',
            'mp_access_token' => 'APP_USR-TOKEN-456',
            'mp_sandbox' => '1',
            'efi_ativo' => '1',
            'efi_client_id' => 'Client_Id_Teste_789',
            'efi_chave_pix' => 'foco7@pix.com.br',
            'efi_sandbox' => '1',
            'pix_desconto' => '5',
            'cartao_max_parcelas' => '12',
            'cartao_sem_juros' => '3',
        ]);

        $responsePost->assertStatus(200);
        $responsePost->assertJson(['success' => true]);

        $this->assertEquals('APP_USR-TESTE-123', CmsSetting::get('mercadopago_public_key'));
        $this->assertEquals('foco7@pix.com.br', CmsSetting::get('efi_chave_pix'));
        $this->assertEquals('5', CmsSetting::get('pix_desconto'));
    }

    public function test_admin_consegue_salvar_controle_de_opacidade_e_configuracoes_do_hero_via_cms(): void
    {
        $responsePost = $this->actingAs($this->adminUser)->postJson('/admin/cms/configuracoes', [
            'hero_bg_opacidade' => '75',
            'hero_overlay_cor' => '#0b0f19',
            'hero_bg_blur' => '3',
        ]);

        $responsePost->assertStatus(200);
        $responsePost->assertJson(['success' => true]);

        $this->assertEquals('75', CmsSetting::get('hero_bg_opacidade'));
        $this->assertEquals('#0b0f19', CmsSetting::get('hero_overlay_cor'));
        $this->assertEquals('3', CmsSetting::get('hero_bg_blur'));

        // Verifica que a landing page pública reflete a configuração
        $responseSite = $this->get('/');
        $responseSite->assertStatus(200);
        $responseSite->assertSee('hero-bg-overlay');
        $responseSite->assertSee('opacity: 0.75');
    }
}
