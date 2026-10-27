<?php

namespace Tests\Feature;

use App\Models\Fatura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelamentoEExclusaoCarneTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_carne@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        $this->cliente = User::factory()->create([
            'email' => 'cliente_carne@foco7.com.br',
            'role' => 'cliente',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa endpoint carne-info retornando dados quando não há parcelas pagas
     */
    public function test_obter_info_carne_sem_parcelas_pagas(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST01';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 1/3',
            'valor' => 89.90,
            'data_vencimento' => now()->addDays(10),
            'status' => 'pendente',
        ]);

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 2/3',
            'valor' => 89.90,
            'data_vencimento' => now()->addDays(40),
            'status' => 'pendente',
        ]);

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-03",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 3,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 3/3',
            'valor' => 89.90,
            'data_vencimento' => now()->addDays(70),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->getJson("/admin/faturas/carne-info/{$p1->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'carne' => [
                'is_carne' => true,
                'codigo_carne' => $codigoCarne,
                'total_geral' => 3,
                'tem_pagas' => false,
                'total_pagas' => 0,
                'total_pendentes' => 3,
                'total_sequencia' => 3,
            ],
        ]);
    }

    /**
     * Testa endpoint carne-info identificando parcelas já pagas e cálculo da sequência
     */
    public function test_obter_info_carne_com_parcelas_pagas(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST02';

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 1/3 (Paga)',
            'valor' => 89.90,
            'data_vencimento' => now()->subDays(10),
            'status' => 'pago',
            'data_pagamento' => now()->subDays(9),
        ]);

        $p2 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 2/3 (Aberta)',
            'valor' => 89.90,
            'data_vencimento' => now()->addDays(20),
            'status' => 'pendente',
        ]);

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-03",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 3,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 3/3 (Aberta)',
            'valor' => 89.90,
            'data_vencimento' => now()->addDays(50),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->getJson("/admin/faturas/carne-info/{$p2->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'carne' => [
                'is_carne' => true,
                'tem_pagas' => true,
                'total_pagas' => 1,
                'total_pendentes' => 2,
                'total_sequencia' => 2,
                'numero_parcela' => 2,
            ],
        ]);
    }

    /**
     * Testa cancelamento de somente uma parcela do carnê
     */
    public function test_cancelar_somente_uma_parcela_do_carne(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST03';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 2,
            'descricao' => 'Parcela 1/2',
            'valor' => 100.00,
            'data_vencimento' => now()->addDays(5),
            'status' => 'pendente',
        ]);

        $p2 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 2,
            'descricao' => 'Parcela 2/2',
            'valor' => 100.00,
            'data_vencimento' => now()->addDays(35),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/cancelar-carne/{$p1->id}", [
            'escopo' => 'somente_esta',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $p1->refresh();
        $p2->refresh();
        $this->assertEquals('cancelado', $p1->status);
        $this->assertEquals('pendente', $p2->status);
    }

    /**
     * Testa cancelamento da sequência ainda pendente quando há parcela paga
     */
    public function test_cancelar_sequencia_pendente_do_carne(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST04';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 1/3 (Paga)',
            'valor' => 50.00,
            'data_vencimento' => now()->subDays(5),
            'status' => 'pago',
            'data_pagamento' => now()->subDays(4),
        ]);

        $p2 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 2/3',
            'valor' => 50.00,
            'data_vencimento' => now()->addDays(25),
            'status' => 'pendente',
        ]);

        $p3 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-03",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 3,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 3/3',
            'valor' => 50.00,
            'data_vencimento' => now()->addDays(55),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/cancelar-carne/{$p2->id}", [
            'escopo' => 'sequencia_pendente',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $p1->refresh();
        $p2->refresh();
        $p3->refresh();

        $this->assertEquals('pago', $p1->status); // Parcela paga permanece intacta
        $this->assertEquals('cancelado', $p2->status); // Parcela 2 cancelada
        $this->assertEquals('cancelado', $p3->status); // Parcela 3 cancelada
    }

    /**
     * Testa cancelamento de todas as parcelas do carnê quando nenhuma está paga
     */
    public function test_cancelar_todas_as_parcelas_quando_nenhuma_foi_paga(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST05';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 1/3',
            'valor' => 70.00,
            'data_vencimento' => now()->addDays(10),
            'status' => 'pendente',
        ]);

        $p2 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 2/3',
            'valor' => 70.00,
            'data_vencimento' => now()->addDays(40),
            'status' => 'pendente',
        ]);

        $p3 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-03",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 3,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 3/3',
            'valor' => 70.00,
            'data_vencimento' => now()->addDays(70),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/cancelar-carne/{$p1->id}", [
            'escopo' => 'todas_parcelas',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $p1->refresh();
        $p2->refresh();
        $p3->refresh();

        $this->assertEquals('cancelado', $p1->status);
        $this->assertEquals('cancelado', $p2->status);
        $this->assertEquals('cancelado', $p3->status);
    }

    /**
     * Testa a opção de deletar TODAS as parcelas de uma única vez
     */
    public function test_deletar_todas_as_parcelas_de_uma_unica_vez(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST06';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 1/3',
            'valor' => 80.00,
            'data_vencimento' => now()->addDays(10),
            'status' => 'pendente',
        ]);

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 2/3',
            'valor' => 80.00,
            'data_vencimento' => now()->addDays(40),
            'status' => 'pendente',
        ]);

        Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-03",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 3,
            'total_parcelas' => 3,
            'descricao' => 'Parcela 3/3',
            'valor' => 80.00,
            'data_vencimento' => now()->addDays(70),
            'status' => 'pendente',
        ]);

        $this->assertEquals(3, Fatura::where('codigo_carne', $codigoCarne)->count());

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/excluir-carne/{$p1->id}", [
            'escopo' => 'todas_parcelas',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Todas as 3 faturas devem ter sido apagadas do banco
        $this->assertEquals(0, Fatura::where('codigo_carne', $codigoCarne)->count());
    }

    /**
     * Testa exclusão de todas as parcelas quando há uma quitada (preserva a quitada)
     */
    public function test_deletar_todas_as_parcelas_preserva_quitadas(): void
    {
        $codigoCarne = 'CARNE-20261027-TEST07';

        $p1 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-01",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 1,
            'total_parcelas' => 2,
            'descricao' => 'Parcela 1/2 Paga',
            'valor' => 60.00,
            'data_vencimento' => now()->subDays(5),
            'status' => 'pago',
            'data_pagamento' => now()->subDays(4),
        ]);

        $p2 = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => "{$codigoCarne}-02",
            'codigo_carne' => $codigoCarne,
            'numero_parcela' => 2,
            'total_parcelas' => 2,
            'descricao' => 'Parcela 2/2 Aberta',
            'valor' => 60.00,
            'data_vencimento' => now()->addDays(25),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/excluir-carne/{$p2->id}", [
            'escopo' => 'todas_parcelas',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // A parcela 1 (paga) continua existindo no banco
        $this->assertDatabaseHas('faturas', ['id' => $p1->id, 'status' => 'pago']);
        // A parcela 2 (pendente) foi excluída
        $this->assertDatabaseMissing('faturas', ['id' => $p2->id]);
    }

    /**
     * Bloqueio contábil: fatura já paga não pode ser cancelada diretamente
     */
    public function test_fatura_paga_nao_pode_ser_cancelada_diretamente(): void
    {
        $faturaPaga = Fatura::create([
            'user_id' => $this->cliente->id,
            'codigo_fatura' => 'FAT-QUITADA-01',
            'descricao' => 'Mensalidade Paga',
            'valor' => 99.00,
            'data_vencimento' => now()->subDays(2),
            'status' => 'pago',
            'data_pagamento' => now()->subDays(1),
        ]);

        $response = $this->actingAs($this->adminUser)->postJson("/admin/faturas/cancelar-carne/{$faturaPaga->id}", [
            'escopo' => 'somente_esta',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        $faturaPaga->refresh();
        $this->assertEquals('pago', $faturaPaga->status);
    }
}
