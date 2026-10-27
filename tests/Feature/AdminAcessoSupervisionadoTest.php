<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminAcessoSupervisionadoTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $clienteUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Administrador Teste',
            'email' => 'admin@foco7.com.br',
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        $this->clienteUser = User::factory()->create([
            'name' => 'Cliente Teste Foco 7',
            'email' => 'cliente@teste.com.br',
            'role' => 'cliente',
            'status' => 'ativo',
        ]);
    }

    public function test_admin_inicia_acesso_supervisionado_para_cliente_com_sucesso(): void
    {
        $origemUrl = 'http://localhost/admin/clientes?busca=Cliente';

        $response = $this->actingAs($this->adminUser)
            ->from($origemUrl)
            ->get(route('admin.clientes.supervisionar', $this->clienteUser->id));

        $response->assertRedirect(route('cliente.dashboard'));

        // Usuário ativo no Auth agora é o cliente
        $this->assertEquals($this->clienteUser->id, Auth::id());

        // Sessão armazena os dados do administrador original e a URL exata de retorno
        $this->assertEquals($this->adminUser->id, session('impersonator_id'));
        $this->assertEquals('Administrador Teste', session('impersonator_nome'));
        $this->assertEquals($origemUrl, session('impersonator_previous_url'));
    }

    public function test_painel_do_cliente_exibe_banner_e_botoes_quando_em_modo_supervisionado(): void
    {
        $response = $this->actingAs($this->clienteUser)
            ->withSession([
                'impersonator_id' => $this->adminUser->id,
                'impersonator_nome' => 'Administrador Teste',
                'impersonator_previous_url' => 'http://localhost/admin/clientes',
            ])
            ->get(route('cliente.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('MODO SUPERVISIONADO');
        $response->assertSee('Voltar ao Administrador');
        $response->assertSee(route('admin.supervisao.voltar'));
    }

    public function test_voltar_supervisao_restaura_sessao_do_administrador_e_redireciona_para_mesma_pagina_anterior(): void
    {
        $paginaExataAnterior = 'http://localhost/admin/clientes?busca=Cliente&status=ativo';

        $response = $this->actingAs($this->clienteUser)
            ->withSession([
                'impersonator_id' => $this->adminUser->id,
                'impersonator_nome' => 'Administrador Teste',
                'impersonator_previous_url' => $paginaExataAnterior,
            ])
            ->get(route('admin.supervisao.voltar'));

        // Redireciona para a mesma página anterior
        $response->assertRedirect($paginaExataAnterior);

        // Usuário ativo volta a ser o Administrador
        $this->assertEquals($this->adminUser->id, Auth::id());
        $this->assertTrue(Auth::user()->isAdmin());

        // Chaves de impersonation são limpas da sessão
        $this->assertFalse(session()->has('impersonator_id'));
        $this->assertFalse(session()->has('impersonator_previous_url'));
    }

    public function test_cliente_comum_nao_pode_iniciar_supervisao_de_outro_cliente(): void
    {
        $outroCliente = User::factory()->create([
            'role' => 'cliente',
            'status' => 'ativo',
        ]);

        // Acesso via navegador convencional redireciona com mensagem de erro
        $response = $this->actingAs($this->clienteUser)
            ->get(route('admin.clientes.supervisionar', $outroCliente->id));

        $response->assertRedirect(route('cliente.dashboard'));

        // Acesso via JSON/AJAX retorna 403 Forbidden
        $responseJson = $this->actingAs($this->clienteUser)
            ->getJson(route('admin.clientes.supervisionar', $outroCliente->id));

        $responseJson->assertStatus(403);
    }

    public function test_nao_e_possivel_supervisionar_cliente_inativo(): void
    {
        $clienteInativo = User::factory()->create([
            'role' => 'cliente',
            'status' => 'inativo',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.clientes.supervisionar', $clienteInativo->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals($this->adminUser->id, Auth::id());
    }
}
