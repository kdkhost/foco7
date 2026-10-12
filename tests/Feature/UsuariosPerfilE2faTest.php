<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use App\Services\PermissaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UsuariosPerfilE2faTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrador Foco 7',
            'email' => 'admin@foco7.com.br',
            'password' => Hash::make('foco7@2026'),
            'role' => 'admin',
            'status' => 'ativo',
            'two_factor_enabled' => false,
        ]);

        $this->operador = User::factory()->create([
            'name' => 'Operador Suporte',
            'email' => 'suporte@foco7.com.br',
            'password' => Hash::make('suporte@123'),
            'role' => 'atendente',
            'status' => 'ativo',
            'two_factor_enabled' => false,
            'permissoes' => ['dashboard', 'clientes', 'notificacoes'],
        ]);
    }

    /**
     * Valida que a listagem de usuários requer autenticação de administrador
     */
    public function test_listagem_de_usuarios_requer_autenticacao(): void
    {
        $response = $this->get('/admin/usuarios');
        $response->assertRedirect('/login');

        $responseAutenticado = $this->actingAs($this->admin)->get('/admin/usuarios');
        $responseAutenticado->assertStatus(200);
        $responseAutenticado->assertSee('Administrador Foco 7');
        $responseAutenticado->assertSee('Operador Suporte');
    }

    /**
     * Valida cadastro de novo usuário via AJAX com endereço completo e permissões
     */
    public function test_admin_pode_cadastrar_novo_usuario_via_ajax(): void
    {
        $dados = [
            'name' => 'Carlos Gerente',
            'email' => 'carlos@foco7.com.br',
            'password' => 'senhaForte@123',
            'role' => 'gerente',
            'status' => 'ativo',
            'telefone' => '(91) 98888-7777',
            'cpf_cnpj' => '123.456.789-00',
            'data_nascimento' => '1990-05-15',
            'genero' => 'Masculino',
            'cargo' => 'Gerente de Operações',
            'cep' => '66053-240',
            'endereco' => 'Tv. Quintino Bocaiúva',
            'numero' => '1200',
            'complemento' => 'Sala 402',
            'bairro' => 'Nazaré',
            'cidade' => 'Belém',
            'estado' => 'PA',
            'bio' => 'Mais de 10 anos de experiência em gestão.',
            'permissoes' => ['dashboard', 'faturas', 'clientes', 'agendamentos'],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/usuarios', $dados);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@foco7.com.br',
            'role' => 'gerente',
            'cargo' => 'Gerente de Operações',
            'cidade' => 'Belém',
            'estado' => 'PA',
        ]);

        $novoUser = User::where('email', 'carlos@foco7.com.br')->first();
        $this->assertTrue($novoUser->temPermissao('dashboard'));
        $this->assertTrue($novoUser->temPermissao('faturas'));
        $this->assertFalse($novoUser->temPermissao('manutencao'));
    }

    /**
     * Valida retorno dos dados completos de usuário para edição via AJAX
     */
    public function test_admin_pode_obter_dados_de_usuario_para_edicao(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/admin/usuarios/{$this->operador->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'usuario' => [
                'id' => $this->operador->id,
                'name' => 'Operador Suporte',
                'email' => 'suporte@foco7.com.br',
                'role' => 'atendente',
            ]
        ]);
    }

    /**
     * Valida toggle de status do usuário (bloqueando auto-desativação)
     */
    public function test_admin_pode_alternar_status_de_outro_usuario_mas_nao_de_si_mesmo(): void
    {
        // Alternar status do operador
        $response = $this->actingAs($this->admin)
            ->postJson("/admin/usuarios/toggle/{$this->operador->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'novo_status' => 'inativo']);

        $this->assertDatabaseHas('users', [
            'id' => $this->operador->id,
            'status' => 'inativo',
        ]);

        // Bloqueia auto-desativação
        $responseSelf = $this->actingAs($this->admin)
            ->postJson("/admin/usuarios/toggle/{$this->admin->id}");

        $responseSelf->assertStatus(422);
    }

    /**
     * Valida exclusão de usuário (bloqueando auto-exclusão e último admin)
     */
    public function test_admin_pode_excluir_usuario_com_seguranca(): void
    {
        // Excluir operador
        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/usuarios/{$this->operador->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('users', [
            'id' => $this->operador->id,
        ]);

        // Tentar excluir a si mesmo deve ser bloqueado
        $responseSelf = $this->actingAs($this->admin)
            ->deleteJson("/admin/usuarios/{$this->admin->id}");

        $responseSelf->assertStatus(422);
    }

    /**
     * Valida atualização de dados pessoais completos e endereço no perfil do usuário
     */
    public function test_usuario_pode_atualizar_dados_pessoais_e_endereco_no_perfil(): void
    {
        $dados = [
            'name' => 'Administrador Atualizado',
            'email' => 'admin@foco7.com.br',
            'telefone' => '(91) 98051-0736',
            'cpf_cnpj' => '999.888.777-66',
            'data_nascimento' => '1985-10-20',
            'genero' => 'Masculino',
            'cargo' => 'Diretor Presidente',
            'bio' => 'Fundador e Diretor de Tecnologia.',
            'cep' => '66053-240',
            'endereco' => 'Tv. Quintino Bocaiúva',
            'numero' => '1200',
            'complemento' => 'Cobertura',
            'bairro' => 'Nazaré',
            'cidade' => 'Belém',
            'estado' => 'PA',
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/perfil/dados', $dados);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $this->admin->id,
            'name' => 'Administrador Atualizado',
            'cargo' => 'Diretor Presidente',
            'cep' => '66053-240',
            'cidade' => 'Belém',
        ]);

        $adminAtualizado = $this->admin->fresh();
        $this->assertStringContainsString('Tv. Quintino Bocaiúva, 1200 - Cobertura', $adminAtualizado->endereco_completo);
    }

    /**
     * Valida ciclo de vida de configuração e ativação do 2FA (TOTP)
     */
    public function test_ciclo_de_ativacao_e_desativacao_de_2fa(): void
    {
        // 1. Iniciar configuração do 2FA
        $resIniciar = $this->actingAs($this->admin)
            ->postJson('/admin/perfil/2fa/iniciar');

        $resIniciar->assertStatus(200);
        $resIniciar->assertJson(['success' => true]);
        $secret = $resIniciar->json('secret');
        $this->assertNotEmpty($secret);

        // 2. Tentar confirmar com código errado deve falhar
        $resConfirmarErrado = $this->actingAs($this->admin)
            ->withSession(['temp_2fa_secret' => $secret])
            ->postJson('/admin/perfil/2fa/confirmar', [
                'code' => '000000',
            ]);
        $resConfirmarErrado->assertStatus(422);

        // 3. Confirmar com código TOTP válido gerado pelo algoritmo
        $codigoCorreto = TwoFactorAuthService::calculateCode($secret);
        $resConfirmarCerto = $this->actingAs($this->admin)
            ->withSession(['temp_2fa_secret' => $secret])
            ->postJson('/admin/perfil/2fa/confirmar', [
                'code' => $codigoCorreto,
            ]);

        $resConfirmarCerto->assertStatus(200);
        $resConfirmarCerto->assertJson(['success' => true]);
        $recoveryCodes = $resConfirmarCerto->json('recovery_codes');
        $this->assertCount(8, $recoveryCodes);

        $adminCom2fa = $this->admin->fresh();
        $this->assertTrue($adminCom2fa->hasTwoFactor());
        $this->assertEquals($secret, $adminCom2fa->two_factor_secret);

        // 4. Desativar 2FA com senha atual
        $resDesativar = $this->actingAs($this->admin)
            ->postJson('/admin/perfil/2fa/desativar', [
                'senha_ou_codigo' => 'foco7@2026',
            ]);

        $resDesativar->assertStatus(200);
        $adminSem2fa = $this->admin->fresh();
        $this->assertFalse($adminSem2fa->hasTwoFactor());
    }

    /**
     * Valida que usuário com 2FA ativo é interceptado no login e requer o segundo fator
     */
    public function test_login_intercepta_usuario_com_2fa_e_autentica_com_codigo_valido(): void
    {
        // Ativa 2FA no admin
        $secret = TwoFactorAuthService::generateSecretKey(16);
        $recoveryCodes = TwoFactorAuthService::generateRecoveryCodes(8);

        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => now(),
            'two_factor_enabled' => true,
        ]);

        // 1. Tentar login regular via AJAX
        $resLogin = $this->postJson('/login', [
            'email' => 'admin@foco7.com.br',
            'password' => 'foco7@2026',
        ]);

        // Deve requerer 2FA e redirecionar para a tela de desafio
        $resLogin->assertStatus(200);
        $resLogin->assertJson([
            'success' => true,
            'requires_2fa' => true,
            'redirect' => route('login.2fa'),
        ]);

        // Usuário ainda NÃO deve estar autenticado na sessão global
        $this->assertGuest();

        // 2. Submeter código TOTP correto no desafio de 2FA
        $codigoTotp = TwoFactorAuthService::calculateCode($secret);

        $res2fa = $this->withSession([
            'login.2fa.user_id' => $this->admin->id,
            'login.2fa.remember' => false,
        ])->postJson('/login/2fa', [
            'code' => $codigoTotp,
        ]);

        $res2fa->assertStatus(200);
        $res2fa->assertJson(['success' => true]);

        // Agora o usuário DEVE estar autenticado como admin
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * Valida que o login com 2FA permite autenticação via código de recuperação de emergência
     */
    public function test_login_com_2fa_permite_acesso_com_recovery_code(): void
    {
        $secret = TwoFactorAuthService::generateSecretKey(16);
        $recoveryCodes = TwoFactorAuthService::generateRecoveryCodes(8);

        $this->admin->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => now(),
            'two_factor_enabled' => true,
        ]);

        $primeiroRecovery = $recoveryCodes[0];

        $res2fa = $this->withSession([
            'login.2fa.user_id' => $this->admin->id,
            'login.2fa.remember' => false,
        ])->postJson('/login/2fa', [
            'usar_recuperacao' => true,
            'recovery_code' => $primeiroRecovery,
        ]);

        $res2fa->assertStatus(200);
        $res2fa->assertJson(['success' => true]);
        $this->assertAuthenticatedAs($this->admin);

        // O código de recuperação utilizado não pode ser reutilizado
        $resReuso = TwoFactorAuthService::verifyAndConsumeRecoveryCode($this->admin->fresh(), $primeiroRecovery);
        $this->assertFalse($resReuso);
    }
}
