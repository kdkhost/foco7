<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Filial;
use App\Models\CmsSetting;

class FiliaisEMapaBrasilTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Garante que o geoblock não impeça os testes locais
        CmsSetting::set('geoblock_ativo', '0');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'ativo',
        ]);
    }

    /**
     * Se não houver filiais ativas, a seção e os menus devem ser ocultados
     */
    public function test_secao_filiais_e_menus_ficam_ocultos_quando_nao_ha_filiais_ativas(): void
    {
        Filial::query()->delete();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('id="unidades"', false);
        $response->assertDontSee('href="#unidades"', false);
        $response->assertDontSee('mapaBrasilFiliais', false);
    }

    /**
     * Se houver filiais ativas, a seção, os menus e o mapa devem ser exibidos
     */
    public function test_secao_filiais_e_mapa_sao_exibidos_quando_ha_filiais_ativas(): void
    {
        Filial::create([
            'nome' => 'Foco 7 Belém Matriz',
            'cidade' => 'Belém',
            'estado' => 'PA',
            'endereco' => 'Av. Nazaré, 500',
            'bairro' => 'Nazaré',
            'cep' => '66035-170',
            'telefone' => '(91) 3222-1000',
            'whatsapp' => '(91) 98051-0736',
            'latitude' => -1.455833,
            'longitude' => -48.490833,
            'destaque' => true,
            'ativo' => true,
            'ordem' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="unidades"', false);
        $response->assertSee('href="#unidades"', false);
        $response->assertSee('Foco 7 Belém Matriz');
        $response->assertSee('mapaBrasilFiliais');
        $response->assertSee('leaflet');
    }

    /**
     * O painel administrativo de filiais requer autenticação de admin
     */
    public function test_painel_filiais_requer_autenticacao_admin(): void
    {
        $responseAnon = $this->get('/admin/filiais');
        $responseAnon->assertRedirect('/login');

        $responseAdmin = $this->actingAs($this->admin)->get('/admin/filiais');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Unidades & Filiais');
    }

    /**
     * Cadastro de nova filial via AJAX
     */
    public function test_admin_pode_cadastrar_nova_filial_via_ajax(): void
    {
        $dados = [
            'nome' => 'Filial Fortaleza Aldeota',
            'cidade' => 'Fortaleza',
            'estado' => 'CE',
            'endereco' => 'Av. Santos Dumont, 1200',
            'bairro' => 'Aldeota',
            'cep' => '60150-160',
            'telefone' => '(85) 3030-4040',
            'whatsapp' => '(85) 99999-8888',
            'latitude' => -3.7319,
            'longitude' => -38.5267,
            'destaque' => '0',
            'ativo' => '1',
            'ordem' => 2,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/filiais/salvar', $dados);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('filiais', [
            'nome' => 'Filial Fortaleza Aldeota',
            'cidade' => 'Fortaleza',
            'estado' => 'CE',
        ]);
    }

    /**
     * Obter dados de filial para edição via AJAX
     */
    public function test_admin_pode_obter_dados_de_filial_para_edicao(): void
    {
        $filial = Filial::create([
            'nome' => 'Filial Ananindeua',
            'cidade' => 'Ananindeua',
            'estado' => 'PA',
            'endereco' => 'BR-316, Km 4',
            'latitude' => -1.3655,
            'longitude' => -48.3742,
            'ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/filiais/obter/{$filial->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'filial' => [
                'id' => $filial->id,
                'nome' => 'Filial Ananindeua',
                'cidade' => 'Ananindeua',
            ]
        ]);
    }

    /**
     * Alternar status ativo/inativo via AJAX (Toggle)
     */
    public function test_admin_pode_alternar_status_de_filial_via_toggle(): void
    {
        $filial = Filial::create([
            'nome' => 'Filial Castanhal',
            'cidade' => 'Castanhal',
            'estado' => 'PA',
            'endereco' => 'Av. Barão do Rio Branco, 850',
            'ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/filiais/toggle/{$filial->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'ativo' => false,
        ]);

        $this->assertDatabaseHas('filiais', [
            'id' => $filial->id,
            'ativo' => false,
        ]);
    }

    /**
     * Excluir filial via AJAX
     */
    public function test_admin_pode_excluir_filial(): void
    {
        $filial = Filial::create([
            'nome' => 'Filial Temporária',
            'cidade' => 'Santarém',
            'estado' => 'PA',
            'endereco' => 'Av. Cuiabá, 300',
            'ativo' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/filiais/{$filial->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('filiais', [
            'id' => $filial->id,
        ]);
    }
}
