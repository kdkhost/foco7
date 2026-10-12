<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Plano;
use App\Models\Fatura;
use App\Models\CmsSetting;
use App\Services\ReguaCobrancaService;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

class ReguaCobrancaERecorrenciaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cliente;
    protected Plano $plano;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin.regua@foco7.com.br',
            'status' => 'ativo',
        ]);

        $this->cliente = User::factory()->create([
            'role' => 'cliente',
            'name' => 'Roberto Carlos Alencar',
            'email' => 'roberto.alencar@teste.com.br',
            'cpf_cnpj' => '12345678909',
            'status' => 'ativo',
        ]);

        $this->plano = Plano::create([
            'nome' => 'Plano Rastreador Veicular Pro',
            'slug' => 'rastreador-pro',
            'descricao' => 'Rastreamento em tempo real com telemetria avançada',
            'preco_mensal' => 79.90,
            'taxa_adesao' => 0.00,
            'recursos' => ['App iOS e Android', 'Corte de combustível', 'Alerta de ignição'],
            'status' => true,
        ]);

        // Vincula cliente ao plano
        $this->cliente->update(['plano_id' => $this->plano->id]);
    }

    /**
     * Teste: Parâmetros padrão e customizados da régua de cobrança
     */
    public function test_obter_parametros_padrao_e_customizados(): void
    {
        $params = ReguaCobrancaService::obterParametros();

        $this->assertEquals(10, $params['dias_antecedencia_fatura']);
        $this->assertEquals(10, $params['dias_lembrete_1']);
        $this->assertEquals(5, $params['dias_lembrete_2']);
        $this->assertEquals(1, $params['dias_lembrete_3']);
        $this->assertEquals(1, $params['dias_atraso_1']);
        $this->assertEquals(5, $params['dias_atraso_2']);
        $this->assertEquals(10, $params['dias_atraso_3']);
        $this->assertEquals(2.0, $params['multa_percentual']);
        $this->assertEquals(1.0, $params['juros_mensal_percentual']);

        // Customizar via CMS
        CmsSetting::set('cobranca_dias_lembrete_1', '7', 'gateways');
        CmsSetting::set('cobranca_multa_percentual', '2.5', 'gateways');

        $paramsAtualizados = ReguaCobrancaService::obterParametros();
        $this->assertEquals(7, $paramsAtualizados['dias_lembrete_1']);
        $this->assertEquals(2.5, $paramsAtualizados['multa_percentual']);
    }

    /**
     * Teste: Cálculo de encargos (Multa de 2% e Juros de Mora diários)
     */
    public function test_calculo_correto_de_multa_e_juros(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-TESTE-ENCARGOS',
            'descricao' => 'Mensalidade',
            'valor' => 100.00,
            'status' => 'vencida',
            'data_vencimento' => now()->subDays(10), // 10 dias de atraso
        ]);

        $params = ReguaCobrancaService::obterParametros();
        $encargos = ReguaCobrancaService::calcularEncargos($fatura, $params);

        // Multa padrão: 2% de R$ 100,00 = R$ 2,00
        $this->assertEquals(2.00, $encargos['valor_multa']);
        // 10 dias de atraso
        $this->assertEquals(10, $encargos['dias_atraso']);
        // Juros: 10 dias * (1% / 30) = ~0.33% = R$ 0.33
        $this->assertEquals(0.33, $encargos['valor_juros']);
        // Valor total: R$ 100,00 + R$ 2,00 + R$ 0,33 = R$ 102,33
        $this->assertEquals(102.33, $encargos['valor_total']);
    }

    /**
     * Teste: Geração de mensalidades recorrentes com antecedência para clientes ativos
     */
    public function test_geracao_antecipada_de_mensalidade_recorrente(): void
    {
        $resultado = ReguaCobrancaService::gerarMensalidadesRecorrentes();

        $this->assertEquals(1, $resultado['geradas']);
        $this->assertDatabaseHas('faturas', [
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'status' => 'pendente',
            'metodo_pagamento' => 'pix',
        ]);

        $faturaCriada = Fatura::where('user_id', $this->cliente->id)->first();
        $this->assertNotEmpty($faturaCriada->qr_code_pix);
        // Garantir que não contém strings fictícias de teste
        $this->assertStringNotContainsString('FOCO7PIXDEMO', $faturaCriada->qr_code_pix_url);
        $this->assertStringNotContainsString('6304ABCD', $faturaCriada->qr_code_pix);

        // Executar novamente não deve gerar duplicata
        $resultado2 = ReguaCobrancaService::gerarMensalidadesRecorrentes();
        $this->assertEquals(0, $resultado2['geradas']);
        $this->assertEquals(1, Fatura::where('user_id', $this->cliente->id)->count());
    }

    /**
     * Teste: Disparo da régua pré-vencimento (1º Lembrete a D-10 dias)
     */
    public function test_disparo_primeiro_lembrete_prevencimento(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-LEMBRETE-1',
            'descricao' => 'Mensalidade Foco 7',
            'valor' => 79.90,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(10)->startOfDay(),
        ]);

        $res = ReguaCobrancaService::processarFila();

        $this->assertEquals(1, $res['lembrete_1']);
        $fatura->refresh();
        $this->assertEquals('cobranca_lembrete_1', $fatura->ultimo_lembrete_enviado);
        $this->assertEquals(1, $fatura->total_lembretes_enviados);
        $this->assertNotNull($fatura->data_ultimo_lembrete);
    }

    /**
     * Teste: Disparo no dia do vencimento (Vence Hoje)
     */
    public function test_disparo_cobranca_no_dia_do_vencimento(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-VENCE-HOJE',
            'descricao' => 'Mensalidade Vence Hoje',
            'valor' => 79.90,
            'status' => 'pendente',
            'data_vencimento' => now()->startOfDay(),
        ]);

        $res = ReguaCobrancaService::processarFila();

        $this->assertEquals(1, $res['vencimento_hoje']);
        $fatura->refresh();
        $this->assertEquals('cobranca_vencimento_hoje', $fatura->ultimo_lembrete_enviado);
    }

    /**
     * Teste: Disparo de atraso pós-vencimento com cálculo e persistência de multa e juros
     */
    public function test_disparo_aviso_de_atraso_com_encargos(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-ATRASO-1',
            'descricao' => 'Mensalidade Atrasada',
            'valor' => 100.00,
            'status' => 'pendente',
            'data_vencimento' => now()->subDays(1)->startOfDay(), // 1 dia de atraso
        ]);

        $res = ReguaCobrancaService::processarFila();

        $this->assertEquals(1, $res['atraso_1']);
        $fatura->refresh();
        $this->assertEquals('vencida', $fatura->status);
        $this->assertEquals('cobranca_atraso_1', $fatura->ultimo_lembrete_enviado);
        $this->assertEquals(2.00, (float) $fatura->valor_multa);
        $this->assertGreaterThan(0, (float) $fatura->valor_juros);
        $this->assertGreaterThan(102.00, (float) $fatura->valor_total_com_encargos);
    }

    /**
     * Teste: Prevenção de duplicidade de disparos no mesmo ciclo/dia
     */
    public function test_prevencao_de_duplicidade_de_disparos(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-IDEMPOTENCIA',
            'descricao' => 'Mensalidade',
            'valor' => 79.90,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(5)->startOfDay(),
        ]);

        $res1 = ReguaCobrancaService::processarFila();
        $this->assertEquals(1, $res1['lembrete_2']);

        // Segunda execução imediata
        $res2 = ReguaCobrancaService::processarFila();
        $this->assertEquals(0, $res2['lembrete_2']);
        $this->assertEquals(0, $res2['total_processados']);
    }

    /**
     * Teste: Execução do comando Artisan faturas:processar-regua
     */
    public function test_comando_artisan_processar_regua(): void
    {
        $exitCode = Artisan::call('faturas:processar-regua');
        $this->assertEquals(0, $exitCode);
    }

    /**
     * Teste: Endpoint AJAX do Painel Admin admin/gateways/executar-regua
     */
    public function test_endpoint_ajax_executar_regua_admin(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.gateways.executar_regua'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'detalhes' => [
                'geradas',
                'fila' => [
                    'lembrete_1',
                    'lembrete_2',
                    'lembrete_3',
                    'vencimento_hoje',
                    'atraso_1',
                    'atraso_2',
                    'atraso_3',
                    'total_processados',
                ],
            ],
        ]);
    }

    /**
     * Teste: Salvamento das configurações da régua no Admin Gateways
     */
    public function test_salvar_configuracoes_da_regua_no_painel_admin(): void
    {
        $payload = [
            'gateway_padrao' => 'efi',
            'cobranca_regua_ativa' => '1',
            'cobranca_dias_antecedencia_fatura' => '15',
            'cobranca_dias_lembrete_1' => '12',
            'cobranca_dias_lembrete_2' => '6',
            'cobranca_dias_lembrete_3' => '2',
            'cobranca_dias_atraso_1' => '2',
            'cobranca_dias_atraso_2' => '7',
            'cobranca_dias_atraso_3' => '15',
            'cobranca_multa_percentual' => '2.0',
            'cobranca_juros_mensal_percentual' => '1.5',
            'cobranca_bloquear_rastreador_apos_dias' => '20',
        ];

        $response = $this->actingAs($this->admin)->postJson(route('admin.gateways.salvar'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('15', CmsSetting::get('cobranca_dias_antecedencia_fatura'));
        $this->assertEquals('12', CmsSetting::get('cobranca_dias_lembrete_1'));
        $this->assertEquals('1.5', CmsSetting::get('cobranca_juros_mensal_percentual'));
        $this->assertEquals('20', CmsSetting::get('cobranca_bloquear_rastreador_apos_dias'));
    }

    /**
     * Teste: Endpoint de detalhes da fatura do cliente sem resíduos de teste
     */
    public function test_cliente_fatura_detalhes_sem_residuos_de_teste(): void
    {
        $fatura = Fatura::create([
            'user_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-TESTE-CLIENTE-001',
            'descricao' => 'Mensalidade Plano Carro',
            'valor' => 89.90,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(5),
        ]);

        $response = $this->actingAs($this->cliente)->getJson('/cliente/faturas/' . $fatura->codigo_fatura);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $faturaData = $response->json('fatura');
        $this->assertNotEmpty($faturaData['qr_code_pix']);
        $this->assertStringNotContainsString('FOCO7PIXDEMO', $faturaData['qr_code_pix_url']);
        $this->assertStringNotContainsString('6304ABCD', $faturaData['qr_code_pix']);
        $this->assertArrayHasKey('valor_total_com_encargos', $faturaData);
        $this->assertArrayHasKey('multa_formatada', $faturaData);
        $this->assertArrayHasKey('juros_formatado', $faturaData);
        $this->assertArrayHasKey('dias_atraso', $faturaData);
    }
}
