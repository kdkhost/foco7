<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\CmsSetting;
use App\Services\ReguaCobrancaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class SegurancaSessaoEReguaCobrancaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        $this->cliente = User::factory()->create([
            'role' => 'cliente',
            'status' => 'ativo',
        ]);
    }

    /**
     * Teste 1: Configuração do tempo de inatividade da sessão no painel admin
     */
    public function test_admin_pode_configurar_tempo_de_inatividade_da_sessao(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.configuracoes.salvar'), [
            'site_nome' => 'Foco 7 Seguranca',
            'sessao_inatividade_minutos' => 45,
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(45, (int) CmsSetting::get('sessao_inatividade_minutos'));
    }

    /**
     * Teste 2: Middleware CheckSessionInactivity desconecta usuário após inatividade
     */
    public function test_middleware_desconecta_usuario_apos_inatividade(): void
    {
        CmsSetting::set('sessao_inatividade_minutos', 15, 'seguranca');

        // Simula usuário logado com última atividade há 20 minutos (expirada)
        $timestampAntigo = now()->subMinutes(20)->timestamp;

        $response = $this->actingAs($this->admin)
            ->withSession(['foco7_ultima_atividade' => $timestampAntigo])
            ->get(route('admin.dashboard'));

        // Deve redirecionar para o login com motivo de inatividade
        $response->assertRedirect(route('login', ['motivo' => 'inatividade']));
        $this->assertGuest();
    }

    /**
     * Teste 3: Middleware permite acesso se o tempo estiver dentro do limite de inatividade
     */
    public function test_middleware_permite_acesso_se_inatividade_estiver_dentro_do_limite(): void
    {
        CmsSetting::set('sessao_inatividade_minutos', 30, 'seguranca');

        // Atividade recente de 5 minutos atrás
        $timestampRecente = now()->subMinutes(5)->timestamp;

        $response = $this->actingAs($this->admin)
            ->withSession(['foco7_ultima_atividade' => $timestampRecente])
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * Teste 4: Endpoint de ping de sessão renova a atividade com sucesso
     */
    public function test_ping_sessao_renova_atividade_com_sucesso(): void
    {
        $response = $this->actingAs($this->admin)->post(route('auth.ping_sessao'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'authenticated' => true,
        ]);
    }

    /**
     * Teste 5: Model Fatura identifica corretamente faturas pagas e quitadas
     */
    public function test_modelo_fatura_identifica_faturas_pagas_e_quitadas(): void
    {
        $faturaPendente = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-TEST-PEND',
            'descricao' => 'Fatura Teste',
            'valor' => 120.00,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(5)->toDateString(),
        ]);

        $this->assertFalse($faturaPendente->isPaga());
        $this->assertFalse($faturaPendente->isLiquidadaOuCancelada());

        // Fatura com status 'pago'
        $faturaPaga = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-TEST-PAGA',
            'descricao' => 'Fatura Paga',
            'valor' => 120.00,
            'status' => 'pago',
            'data_pagamento' => now(),
            'data_vencimento' => now()->subDays(2)->toDateString(),
        ]);

        $this->assertTrue($faturaPaga->isPaga());
        $this->assertTrue($faturaPaga->isQuitada());
        $this->assertTrue($faturaPaga->isLiquidadaOuCancelada());

        // Fatura cancelada
        $faturaCancelada = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-TEST-CANC',
            'descricao' => 'Fatura Cancelada',
            'valor' => 120.00,
            'status' => 'cancelado',
            'data_vencimento' => now()->addDays(1)->toDateString(),
        ]);

        $this->assertFalse($faturaCancelada->isPaga());
        $this->assertTrue($faturaCancelada->isLiquidadaOuCancelada());
    }

    /**
     * Teste 6: Blindagem da Régua de Cobrança bloqueia disparo para faturas quitadas
     */
    public function test_regua_de_cobranca_bloqueia_disparo_para_faturas_quitadas(): void
    {
        $faturaPaga = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-BLINDADA-01',
            'descricao' => 'Mensalidade Blindada',
            'valor' => 89.90,
            'status' => 'pago',
            'data_pagamento' => now(),
            'data_vencimento' => now()->toDateString(),
        ]);

        // Tentar disparar qualquer etapa da régua para fatura já quitada deve retornar false
        $resultado = ReguaCobrancaService::dispararEtapa($faturaPaga, 'cobranca_vencimento_hoje');
        $this->assertFalse($resultado);

        // Nenhuma notificação de cobrança deve ser gerada para a fatura quitada
        $this->assertDatabaseMissing('notificacoes', [
            'user_id' => $this->cliente->id,
            'mensagem' => 'Fatura #' . $faturaPaga->codigo_fatura . '%',
        ]);
    }

    /**
     * Teste 7: Comando faturas:enviar-lembretes ignora faturas já pagas
     */
    public function test_comando_enviar_lembretes_ignora_faturas_pagas(): void
    {
        $dataAlvo = now()->addDays(3)->toDateString();

        // Fatura paga com vencimento na data alvo
        $faturaPaga = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-LEMBRETE-PAGA',
            'descricao' => 'Mensalidade',
            'valor' => 100.00,
            'status' => 'pago',
            'data_pagamento' => now(),
            'data_vencimento' => $dataAlvo,
        ]);

        $exitCode = Artisan::call('faturas:enviar-lembretes', ['--dias' => 3]);
        $this->assertEquals(0, $exitCode);

        // Fatura paga não deve ter recebido lembrete
        $faturaPaga->refresh();
        $this->assertNull($faturaPaga->ultimo_lembrete_enviado);
    }
}
