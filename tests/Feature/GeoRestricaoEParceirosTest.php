<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Parceiro;
use App\Models\CmsSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GeoRestricaoEParceirosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Garante que o geoblock esteja ativo para os testes
        CmsSetting::set('geoblock_ativo', '1');
    }

    /**
     * Testa se requisições originadas fora das Américas (ex: Europa, Ásia) são bloqueadas com 403
     */
    public function test_geoblock_bloqueia_paises_fora_das_americas(): void
    {
        // Simulando acesso da Alemanha (DE)
        $response = $this->withHeaders([
            'CF-IPCountry' => 'DE',
            'REMOTE_ADDR' => '185.190.140.1',
        ])->get('/');

        $response->assertStatus(403);
        $response->assertSee('Acesso Restrito');
        $response->assertSee('Américas');
        $response->assertSee('Alemanha');
    }

    /**
     * Testa se requisições da Rússia (RU) ou China (CN) também são bloqueadas
     */
    public function test_geoblock_bloqueia_russia_e_china(): void
    {
        $responseRussia = $this->withHeaders([
            'CF-IPCountry' => 'RU',
            'REMOTE_ADDR' => '95.173.136.1',
        ])->get('/');

        $responseRussia->assertStatus(403);

        $responseChina = $this->withHeaders([
            'CF-IPCountry' => 'CN',
            'REMOTE_ADDR' => '220.181.38.1',
        ])->get('/');

        $responseChina->assertStatus(403);
    }

    /**
     * Testa se requisições das Américas (Brasil, EUA, Argentina, etc.) são liberadas
     */
    public function test_geoblock_permite_acesso_de_paises_das_americas(): void
    {
        // Brasil (BR)
        $responseBrasil = $this->withHeaders([
            'CF-IPCountry' => 'BR',
            'REMOTE_ADDR' => '177.100.50.2',
        ])->get('/');

        $responseBrasil->assertStatus(200);

        // Estados Unidos (US)
        $responseEua = $this->withHeaders([
            'CF-IPCountry' => 'US',
            'REMOTE_ADDR' => '8.8.8.8',
        ])->get('/');

        $responseEua->assertStatus(200);

        // Argentina (AR)
        $responseArgentina = $this->withHeaders([
            'CF-IPCountry' => 'AR',
            'REMOTE_ADDR' => '181.47.10.1',
        ])->get('/');

        $responseArgentina->assertStatus(200);
    }

    /**
     * Testa se um IP cadastrado na whitelist do admin é liberado mesmo se estiver na Europa
     */
    public function test_geoblock_permite_ip_em_whitelist_mesmo_no_exterior(): void
    {
        $ipEspecial = '195.154.122.50';
        CmsSetting::set('geoblock_ips_liberados', "8.8.4.4\n" . $ipEspecial . "\n1.1.1.1");

        // Requisição da França mas com o IP na Whitelist
        $response = $this->withHeaders([
            'CF-IPCountry' => 'FR',
            'REMOTE_ADDR' => $ipEspecial,
        ])->get('/');

        $response->assertStatus(200);
    }

    /**
     * Testa se rotas de Webhook de gateway de pagamento continuam abertas mesmo do exterior
     */
    public function test_geoblock_isenta_rotas_de_webhook_de_pagamento(): void
    {
        $responseWebhook = $this->withHeaders([
            'CF-IPCountry' => 'NL', // Holanda
            'REMOTE_ADDR' => '188.166.0.1',
        ])->postJson('/webhook/efi', ['evento' => 'teste']);

        // Não deve ser 403 Forbidden de geoblock (pode ser 200 ou 422 dependendo do payload do webhook)
        $this->assertNotEquals(403, $responseWebhook->getStatusCode());
    }

    /**
     * Testa se quando o bloqueio geográfico for desativado no painel, qualquer país consegue acessar
     */
    public function test_geoblock_pode_ser_desativado_pelo_admin(): void
    {
        CmsSetting::set('geoblock_ativo', '0');

        $response = $this->withHeaders([
            'CF-IPCountry' => 'FR',
            'REMOTE_ADDR' => '195.154.122.1',
        ])->get('/');

        $response->assertStatus(200);
    }

    /**
     * Testa o simulador de GeoIP via AJAX no painel administrativo
     */
    public function test_simulador_geoip_no_painel_administrativo(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/configuracoes/testar-geoip', [
            'ip' => '177.100.50.2',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'dados' => [
                'tipo',
                'ip',
                'pais',
                'codigo',
                'continente',
                'is_americas',
                'resultado',
                'cor',
            ]
        ]);
    }

    /**
     * Testa o CRUD completo e o toggle de ativação de Parceiros no admin
     */
    public function test_crud_de_parceiros_no_painel_administrativo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        // 1. Listagem
        $responseIndex = $this->actingAs($admin)->get('/admin/parceiros');
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Ativos no Carrossel Infinito');

        // 2. Cadastro de Parceiro
        $fakeLogo = UploadedFile::fake()->create('parceiro_teste.png', 10, 'image/png');
        $responseStore = $this->actingAs($admin)->post(route('admin.parceiros.salvar'), [
            'nome' => 'Parceiro Tech Brasil',
            'categoria' => 'Hardware & GPS',
            'link' => 'https://parceiro.com.br',
            'ordem' => 1,
            'ativo' => '1',
            'logo' => $fakeLogo,
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $responseStore->assertStatus(200);
        $responseStore->assertJson(['success' => true]);

        $parceiro = Parceiro::where('nome', 'Parceiro Tech Brasil')->first();
        $this->assertNotNull($parceiro);
        $this->assertEquals('Hardware & GPS', $parceiro->categoria);
        $this->assertTrue($parceiro->ativo);

        // 3. Toggle de status via AJAX
        $responseToggle = $this->actingAs($admin)->postJson(route('admin.parceiros.toggle', $parceiro->id));
        $responseToggle->assertStatus(200);
        $responseToggle->assertJson([
            'success' => true,
            'ativo' => false,
        ]);
        $this->assertFalse($parceiro->fresh()->ativo);

        // 4. Edição
        $responseUpdate = $this->actingAs($admin)->post(route('admin.parceiros.salvar'), [
            'id' => $parceiro->id,
            'nome' => 'Parceiro Tech Editado',
            'categoria' => 'Seguradora Homologada',
            'ordem' => 5,
            'ativo' => '1',
        ], ['X-Requested-With' => 'XMLHttpRequest']);
        $responseUpdate->assertStatus(200);
        $this->assertEquals('Parceiro Tech Editado', $parceiro->fresh()->nome);

        // 5. Exclusão via AJAX
        $responseDelete = $this->actingAs($admin)->deleteJson(route('admin.parceiros.excluir', $parceiro->id));
        $responseDelete->assertStatus(200);
        $responseDelete->assertJson(['success' => true]);
        $this->assertNull(Parceiro::find($parceiro->id));
    }

    /**
     * Testa se os parceiros ativos aparecem no carrossel infinito da Landing Page pública
     */
    public function test_parceiros_ativos_aparecem_na_landing_page(): void
    {
        Parceiro::create([
            'nome' => 'Cobli Frotas',
            'categoria' => 'Tecnologia de Frotas',
            'logo' => 'img/parceiros/cobli.svg',
            'ordem' => 1,
            'ativo' => true,
        ]);

        Parceiro::create([
            'nome' => 'Teltonika Telematics',
            'categoria' => 'Hardware Homologado',
            'logo' => 'img/parceiros/teltonika.svg',
            'ordem' => 2,
            'ativo' => true,
        ]);

        // Parceiro inativo (não deve aparecer)
        Parceiro::create([
            'nome' => 'Parceiro Oculto',
            'categoria' => 'Inativo',
            'logo' => 'img/parceiros/suntech.svg',
            'ordem' => 3,
            'ativo' => false,
        ]);

        $response = $this->withHeaders([
            'CF-IPCountry' => 'BR',
        ])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Empresas Parceiras &amp; Tecnologias Homologadas', false);
        $response->assertSee('Cobli Frotas');
        $response->assertSee('Teltonika Telematics');
        $response->assertDontSee('Parceiro Oculto');
        $response->assertSee('swiper swiper-parceiros');
    }
}
