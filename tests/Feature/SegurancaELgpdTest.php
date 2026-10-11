<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Depoimento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SegurancaELgpdTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a página pública de Termos e LGPD responde com sucesso
     */
    public function test_pagina_privacidade_e_termos_lgpd_responde_com_sucesso(): void
    {
        $response = $this->get('/privacidade');

        $response->assertStatus(200);
        $response->assertSee('Termos de Uso');
        $response->assertSee('LGPD');
    }

    /**
     * Testa se os cabeçalhos rigorosos de segurança HTTP estão presentes na resposta
     */
    public function test_cabecalhos_de_seguranca_http_estao_presentes(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Testa o fluxo de solicitação de recuperação de senha via e-mail
     */
    public function test_solicitacao_de_recuperacao_de_senha(): void
    {
        $user = User::factory()->create([
            'email' => 'recupera@foco7.com.br',
        ]);

        $response = $this->postJson('/esqueci-senha', [
            'email' => 'recupera@foco7.com.br',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'recupera@foco7.com.br',
        ]);
    }

    /**
     * Testa a redefinição de senha com token válido
     */
    public function test_redefinicao_de_senha_com_token_valido(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente.reset@foco7.com.br',
            'password' => Hash::make('senhaAntiga123'),
        ]);

        $plainToken = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/redefinir-senha', [
            'token' => $plainToken,
            'email' => $user->email,
            'password' => 'NovaSenhaSegura@2026',
            'password_confirmation' => 'NovaSenhaSegura@2026',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifica se a nova senha foi gravada e o token deletado
        $user->refresh();
        $this->assertTrue(Hash::check('NovaSenhaSegura@2026', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    /**
     * Testa o cadastro rápido de cliente via AJAX para o fluxo de agendamento
     */
    public function test_cadastro_rapido_ajax_para_agendamento(): void
    {
        $response = $this->postJson('/auth/rapido/cadastro', [
            'name' => 'Cliente Teste Rápido',
            'email' => 'rapido@teste.com',
            'telefone' => '(91) 98888-7777',
            'password' => 'senhaSegura123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'rapido@teste.com',
            'role' => 'cliente',
        ]);
    }

    /**
     * Testa o envio público de depoimento com avaliação dinâmica em estrelas
     */
    public function test_envio_publico_de_depoimento_com_estrelas(): void
    {
        $response = $this->postJson('/depoimento/enviar', [
            'nome_cliente' => 'Fabio Silveira',
            'cidade_uf' => 'Belém - PA',
            'veiculo' => 'Chevrolet Tracker',
            'estrelas' => 5,
            'texto' => 'Excelente atendimento e rapidez no bloqueio do veículo!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('depoimentos', [
            'nome_cliente' => 'Fabio Silveira',
            'estrelas' => 5,
            'ativo' => false,
        ]);
    }

    /**
     * Testa o conversor e sanitizador de encoding UTF-8 / Mojibake
     */
    public function test_correcao_mojibake_e_integridade_utf8(): void
    {
        $corrompido = 'SolicitaÃ§Ã£o realizada via Landing Page.';
        $corrigido = \App\Console\Commands\CorrigirEncodingCommand::fixMojibake($corrompido);

        $this->assertEquals('Solicitação realizada via Landing Page.', $corrigido);

        $corrompido2 = 'InstalaÃ§Ã£o de rastreador e manutenÃ§Ã£o tÃ©cnica de veÃ­culos com precisÃ£o';
        $corrigido2 = \App\Console\Commands\CorrigirEncodingCommand::fixMojibake($corrompido2);

        $this->assertEquals('Instalação de rastreador e manutenção técnica de veículos com precisão', $corrigido2);
    }
}

