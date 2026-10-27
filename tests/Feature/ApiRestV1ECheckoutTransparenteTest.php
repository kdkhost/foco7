<?php

namespace Tests\Feature;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Filial;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRestV1ECheckoutTransparenteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Plano $plano;

    protected string $apiKey = 'foco7_test_secret_api_key_123456';

    protected function setUp(): void
    {
        parent::setUp();

        CmsSetting::set('api_rest_secret_token', $this->apiKey, 'api');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'ativo',
            'email' => 'admin@foco7.com',
        ]);

        $this->plano = Plano::create([
            'nome' => 'Plano Rastreador Veicular Pro',
            'slug' => 'plano-rastreador-pro',
            'descricao' => 'Rastreamento em tempo real e bloqueio',
            'preco_mensal' => 79.90,
            'taxa_adesao' => 49.90,
            'ativo' => true,
            'destaque' => true,
            'beneficios' => ['App Android e iOS', 'Bloqueio Imediato', 'Assistência 24h'],
        ]);

        Filial::create([
            'nome' => 'Unidade Rio de Janeiro',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'bairro' => 'Bonsucesso',
            'endereco' => 'Av. Brasil, 1500',
            'telefone' => '21981325441',
            'whatsapp' => '5521981325441',
            'ativo' => true,
        ]);
    }

    public function test_api_ping_endpoint(): void
    {
        $response = $this->getJson('/api/v1/ping');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'healthy',
                'version' => 'v1.0.0',
            ]);
    }

    public function test_api_catalogo_planos_publico(): void
    {
        $response = $this->getJson('/api/v1/planos');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1,
            ])
            ->assertJsonFragment([
                'nome' => 'Plano Rastreador Veicular Pro',
                'preco_mensal' => 79.90,
            ]);
    }

    public function test_api_filiais_publico(): void
    {
        $response = $this->getJson('/api/v1/filiais');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 1,
            ])
            ->assertJsonFragment([
                'cidade' => 'Rio de Janeiro',
            ]);
    }

    public function test_api_checkout_transparente_calcular_taxas(): void
    {
        $response = $this->postJson('/api/v1/checkout/calcular-taxas', [
            'plano_id' => $this->plano->id,
            'metodo_pagamento' => 'pix',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'taxas' => [
                    'valor_original',
                    'valor_final',
                    'desconto',
                ],
            ]);
    }

    public function test_api_checkout_transparente_gerar_sem_redirecionamento_externo(): void
    {
        $payload = [
            'plano_id' => $this->plano->id,
            'nome' => 'Carlos Cliente API',
            'email' => 'carlos.api@cliente.com',
            'telefone' => '21999998888',
            'cpf_cnpj' => '12345678909',
            'metodo_pagamento' => 'pix',
            'gateway' => 'efi',
        ];

        $response = $this->postJson('/api/v1/checkout/gerar', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'metodo' => 'pix',
            ])
            ->assertJsonStructure([
                'fatura' => [
                    'codigo',
                    'plano',
                    'valor_formatado',
                    'qr_code_pix',
                ],
            ]);

        $this->assertDatabaseHas('faturas', [
            'metodo_pagamento' => 'pix',
            'gateway' => 'efi',
            'status' => 'pendente',
        ]);
    }

    public function test_api_consultar_fatura_por_codigo(): void
    {
        $cliente = User::factory()->create([
            'role' => 'cliente',
            'name' => 'Mariana Oliveira',
            'email' => 'mariana@email.com',
        ]);

        $fatura = Fatura::create([
            'user_id' => $cliente->id,
            'plano_id' => $this->plano->id,
            'codigo_fatura' => 'FAT-API-TESTE-999',
            'descricao' => 'Mensalidade Rastreamento Foco 7',
            'valor' => 79.90,
            'gateway' => 'efi',
            'metodo_pagamento' => 'pix',
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(5),
            'qr_code_pix' => '00020126580014BR.GOV.BCB.PIX...',
        ]);

        $response = $this->getJson("/api/v1/faturas/{$fatura->codigo_fatura}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'codigo_fatura' => 'FAT-API-TESTE-999',
                    'status' => 'pendente',
                    'valor' => 79.90,
                ],
            ]);
    }

    public function test_api_admin_metricas_protegido_por_chave(): void
    {
        // 1. Sem token -> 401
        $this->getJson('/api/v1/admin/metricas')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);

        // 2. Com token Bearer válido -> 200
        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/admin/metricas');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_faturas',
                    'total_pendentes',
                    'total_pagas',
                    'receita_mes_corrente',
                ],
            ]);
    }

    public function test_api_admin_emissao_carne_proprio_vs_efi(): void
    {
        $cliente = User::factory()->create([
            'role' => 'cliente',
            'cpf_cnpj' => '11122233344',
            'telefone' => '21988887777',
        ]);

        // 1. Emissão de Carnê Próprio do Sistema Foco 7
        $responseProprio = $this->withHeader('X-API-Key', $this->apiKey)
            ->postJson('/api/v1/admin/carnes/emitir', [
                'cliente_id' => $cliente->id,
                'plano_id' => $this->plano->id,
                'descricao' => 'Carnê Próprio 12x Foco 7',
                'valor_parcela' => 'R$ 69,90',
                'total_parcelas' => 12,
                'tipo_carne' => 'proprio',
            ]);

        $responseProprio->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tipo_carne' => 'proprio',
            ]);

        $this->assertDatabaseHas('faturas', [
            'user_id' => $cliente->id,
            'gateway' => 'proprio',
            'numero_parcela' => 1,
        ]);
    }
}
