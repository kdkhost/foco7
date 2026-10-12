<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Fatura;
use Illuminate\Support\Facades\RateLimiter;

class EstornoContabilEAutenticacaoSeguraTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login|');

        $this->adminUser = User::factory()->create([
            'email' => 'admin_estorno@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa se fatura quitada bloqueia alteração manual direta de status
     */
    public function test_fatura_paga_bloqueia_alteracao_manual_de_status(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $fatura = Fatura::create([
            'user_id' => $cliente->id,
            'codigo_fatura' => 'FAT-QUITADA-01',
            'descricao' => 'Mensalidade Rastreamento Veicular',
            'valor' => 100.00,
            'data_vencimento' => now(),
            'data_pagamento' => now(),
            'status' => 'pago',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/status/{$fatura->id}", [
            'status' => 'cancelado',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertStringContainsString('Faturas já quitadas não podem ter o status alterado manualmente', $response->json('message'));

        $fatura->refresh();
        $this->assertEquals('pago', $fatura->status);
    }

    /**
     * Testa estorno manual formal de fatura paga e recálculo da receita contábil
     */
    public function test_estorno_manual_recalcula_receita_e_atualiza_status_para_estornado(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $fatura = Fatura::create([
            'user_id' => $cliente->id,
            'codigo_fatura' => 'FAT-ESTORNO-01',
            'descricao' => 'Instalação Rastreador 4G',
            'valor' => 250.00,
            'data_vencimento' => now(),
            'data_pagamento' => now(),
            'status' => 'pago',
        ]);

        // Receita antes do estorno
        $receitaAntes = Fatura::where('status', 'pago')->sum('valor');
        $this->assertEquals(250.00, (float) $receitaAntes);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/estornar/{$fatura->id}", [
            'motivo' => 'Devolução por arrependimento em 7 dias (CDC)',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $fatura->refresh();
        $this->assertEquals('estornado', $fatura->status);
        $this->assertStringContainsString('Devolução por arrependimento', $fatura->descricao);

        // Receita recalculada após estorno (deve ser 0)
        $receitaDepois = Fatura::where('status', 'pago')->sum('valor');
        $this->assertEquals(0.00, (float) $receitaDepois);

        // Notificação de auditoria gerada
        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'financeiro',
            'titulo' => 'Fatura Estornada: ' . $fatura->codigo_fatura,
        ]);
    }

    /**
     * Testa bloqueio imediato contra bots preenchendo campo honeypot no login
     */
    public function test_login_bloqueia_ataque_honeypot(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'admin_estorno@foco7.com.br',
            'password' => 'qualquer_senha',
            '_hp_security_check' => 'eu_sou_um_bot_spam',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Testa bloqueio por força bruta (Rate Limiting) após sucessivas falhas de login
     */
    public function test_login_bloqueia_forca_bruta_apos_tentativas_falhas(): void
    {
        $email = 'vitima_teste@foco7.com.br';

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', [
                'email' => $email,
                'password' => 'senha_errada_123',
            ]);
        }

        // 6ª tentativa deve ser bloqueada com status 429 Too Many Requests
        $response = $this->postJson('/login', [
            'email' => $email,
            'password' => 'senha_errada_123',
        ]);

        $response->assertStatus(429);
        $this->assertStringContainsString('Muitas tentativas', $response->json('message'));
    }

    /**
     * Testa cadastro público de cliente com login automático
     */
    public function test_cadastro_publico_cria_cliente_e_conecta_sessao(): void
    {
        $response = $this->postJson('/cadastro', [
            'name' => 'Ana Clara Ribeiro',
            'email' => 'anaclara@exemplo.com',
            'telefone' => '(91) 98111-4455',
            'password' => 'segredo123',
            'password_confirmation' => 'segredo123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'anaclara@exemplo.com',
            'role' => 'cliente',
        ]);

        $this->assertAuthenticated();
    }
}
