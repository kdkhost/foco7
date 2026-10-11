<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Agendamento;
use App\Models\Fatura;
use App\Models\Visita;
use App\Services\AnalyticsService;

class DashboardAnalyticsEAntecipacaoTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_dash@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa se o acesso ao site registra visita no banco via middleware
     */
    public function test_visita_e_registrada_no_banco_ao_acessar_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $this->assertDatabaseHas('visitas', [
            'rota' => 'site.home',
        ]);
    }

    /**
     * Testa se a dashboard do admin carrega com métricas analíticas e KPIs
     */
    public function test_dashboard_admin_carrega_com_analytics_e_kpis(): void
    {
        // Registrar visita prévia
        Visita::create([
            'ip_hash' => hash('sha256', '127.0.0.1'),
            'session_id' => 'sess_test_123',
            'url' => 'https://foco7.com.br/',
            'user_agent' => 'Mozilla/5.0 Test',
            'dispositivo' => 'desktop',
            'navegador' => 'Chrome',
            'rota' => 'site.home',
            'data_visita' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Painel de Controle &amp; Analítico', false);
        $response->assertSee('Visitantes Únicos Hoje');
        $response->assertSee('Analítico de Tráfego');
        $response->assertSee('Dispositivos dos Visitantes');
    }

    /**
     * Testa se o cancelamento de agendamento funciona com motivo e altera status
     */
    public function test_cancelamento_de_agendamento_libera_horario_e_atualiza_status(): void
    {
        $agendamento = Agendamento::create([
            'cliente_nome' => 'Marcos Vinicius',
            'cliente_telefone' => '(91) 98888-7777',
            'categoria' => 'rastreamento',
            'tipo_servico' => 'Instalação Rastreador',
            'valor' => 120.00,
            'veiculo_modelo' => 'Honda Civic',
            'veiculo_placa' => 'QER-9988',
            'endereco_atendimento' => 'Av. Nazaré, 500',
            'cidade' => 'Belém',
            'status' => 'pendente',
            'data_hora_inicio' => now()->addDays(2),
            'data_hora_fim' => now()->addDays(2)->addHour(),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/agendamentos/cancelar/{$agendamento->id}", [
            'motivo' => 'Cliente solicitou remarcação para outro mês por viagem',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $agendamento->refresh();
        $this->assertEquals('cancelado', $agendamento->status);
        $this->assertStringContainsString('Cliente solicitou remarcação', $agendamento->observacoes);
    }

    /**
     * Testa antecipação de fatura individual com cálculo de desconto
     */
    public function test_antecipacao_de_fatura_individual_com_desconto(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $fatura = Fatura::create([
            'user_id' => $cliente->id,
            'codigo_fatura' => 'FAT-TEST-ANTECIPA-01',
            'descricao' => 'Mensalidade Rastreamento Veicular 05/2026',
            'valor' => 150.00,
            'data_vencimento' => now()->addDays(15),
            'status' => 'pendente',
            'numero_parcela' => 5,
            'total_parcelas' => 12,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/antecipar/{$fatura->id}", [
            'desconto' => 20.00,
            'metodo_pagamento' => 'pix',
            'observacao' => 'Antecipação com 20 reais de desconto promocional',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $fatura->refresh();
        $this->assertEquals('pago', $fatura->status);
        $this->assertEquals(130.00, (float) $fatura->valor);
        $this->assertNotNull($fatura->data_pagamento);
    }

    /**
     * Testa antecipação em lote de parcelas
     */
    public function test_antecipacao_em_lote_de_multiplas_faturas(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $f1 = Fatura::create([
            'user_id' => $cliente->id,
            'codigo_fatura' => 'FAT-LOTE-01',
            'descricao' => 'Parcela 1',
            'valor' => 100.00,
            'data_vencimento' => now()->addDays(30),
            'status' => 'pendente',
        ]);

        $f2 = Fatura::create([
            'user_id' => $cliente->id,
            'codigo_fatura' => 'FAT-LOTE-02',
            'descricao' => 'Parcela 2',
            'valor' => 100.00,
            'data_vencimento' => now()->addDays(60),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/antecipar-lote", [
            'fatura_ids' => [$f1->id, $f2->id],
            'desconto_total' => 30.00,
            'metodo_pagamento' => 'pix',
            'observacao' => 'Quitação adiantada em lote',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $f1->refresh();
        $f2->refresh();
        $this->assertEquals('pago', $f1->status);
        $this->assertEquals('pago', $f2->status);
        $this->assertEquals(85.00, (float) $f1->valor);
        $this->assertEquals(85.00, (float) $f2->valor);
    }
}
