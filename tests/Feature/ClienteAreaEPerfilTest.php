<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Agendamento;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Hash;

class ClienteAreaEPerfilTest extends TestCase
{
    use RefreshDatabase;

    protected User $clienteUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clienteUser = User::factory()->create([
            'email' => 'cliente_test@foco7.com.br',
            'name' => 'Cliente Teste Foco 7',
            'password' => Hash::make('senha123456'),
            'role' => 'cliente',
            'telefone' => '(91) 98888-7777',
            'status' => 'ativo',
        ]);
    }

    /**
     * Testa acesso ao dashboard e listagem de agendamentos
     */
    public function test_cliente_pode_acessar_sua_dashboard_e_ver_seus_agendamentos(): void
    {
        $agendamento = Agendamento::create([
            'user_id' => $this->clienteUser->id,
            'categoria' => 'rastreamento',
            'tipo_servico' => 'Instalação de Rastreador',
            'cliente_nome' => $this->clienteUser->name,
            'cliente_telefone' => $this->clienteUser->telefone,
            'cliente_email' => $this->clienteUser->email,
            'veiculo_modelo' => 'Onix 2024',
            'veiculo_placa' => 'ABC-1D23',
            'data_hora_inicio' => now()->addDays(2),
            'status' => 'pendente',
            'cor_calendario' => '#28a745',
        ]);

        $response = $this->actingAs($this->clienteUser)->get('/cliente/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Onix 2024');
        $response->assertSee('Instalação de Rastreador');
        $response->assertSee('btn-cancelar-agendamento-cli');
    }

    /**
     * Testa cancelamento de agendamento pelo cliente com liberação de horário e notificação
     */
    public function test_cliente_pode_cancelar_seu_agendamento_com_sucesso(): void
    {
        $agendamento = Agendamento::create([
            'user_id' => $this->clienteUser->id,
            'categoria' => 'estetica',
            'tipo_servico' => 'Polimento Técnico',
            'cliente_nome' => $this->clienteUser->name,
            'cliente_telefone' => $this->clienteUser->telefone,
            'cliente_email' => $this->clienteUser->email,
            'veiculo_modelo' => 'Civic Touring',
            'data_hora_inicio' => now()->addDays(3),
            'status' => 'pendente',
            'cor_calendario' => '#1e293b',
        ]);

        $response = $this->actingAs($this->clienteUser)->postJson("/cliente/agendamentos/{$agendamento->id}/cancelar");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso. O horário foi liberado.',
        ]);

        $agendamento->refresh();
        $this->assertEquals('cancelado', $agendamento->status);

        // Notificação deve ter sido gerada para a equipe/admin
        $this->assertDatabaseHas('notificacoes', [
            'tipo' => 'agendamento',
            'icone' => 'fa-calendar-times',
        ]);
    }

    /**
     * Testa que o cliente não consegue cancelar agendamento de terceiros
     */
    public function test_cliente_nao_pode_cancelar_agendamento_de_outro_cliente(): void
    {
        $outroCliente = User::factory()->create(['role' => 'cliente']);

        $agendamentoOutro = Agendamento::create([
            'user_id' => $outroCliente->id,
            'categoria' => 'rastreamento',
            'tipo_servico' => 'Manutenção',
            'cliente_nome' => $outroCliente->name,
            'cliente_telefone' => '91999990000',
            'cliente_email' => 'outro@email.com',
            'veiculo_modelo' => 'Corolla',
            'data_hora_inicio' => now()->addDay(),
            'status' => 'pendente',
        ]);

        $response = $this->actingAs($this->clienteUser)->postJson("/cliente/agendamentos/{$agendamentoOutro->id}/cancelar");

        $response->assertStatus(404);
        $agendamentoOutro->refresh();
        $this->assertEquals('pendente', $agendamentoOutro->status);
    }

    /**
     * Testa acesso à tela de perfil do cliente e atualização cadastral
     */
    public function test_cliente_pode_acessar_perfil_e_atualizar_seus_dados_cadastrais(): void
    {
        $response = $this->actingAs($this->clienteUser)->get('/cliente/perfil');
        $response->assertStatus(200);
        $response->assertSee('Meu Perfil &amp; Segurança', false);

        $responseUpdate = $this->actingAs($this->clienteUser)->postJson('/cliente/perfil/dados', [
            'name' => 'Cliente Atualizado Foco 7',
            'telefone' => '(91) 99123-4567',
            'cpf_cnpj' => '111.222.333-44',
            'cep' => '66055-000',
            'endereco' => 'Tv. Dom Romualdo de Seixas',
            'numero' => '1000',
            'bairro' => 'Umarizal',
            'cidade' => 'Belém',
            'estado' => 'PA',
        ]);

        $responseUpdate->assertStatus(200);
        $responseUpdate->assertJson([
            'success' => true,
            'message' => 'Seus dados cadastrais foram atualizados com sucesso!',
        ]);

        $this->clienteUser->refresh();
        $this->assertEquals('Cliente Atualizado Foco 7', $this->clienteUser->name);
        $this->assertEquals('(91) 99123-4567', $this->clienteUser->telefone);
        $this->assertEquals('Umarizal', $this->clienteUser->bairro);
    }

    /**
     * Testa alteração segura de senha pelo cliente validando a senha atual
     */
    public function test_cliente_pode_alterar_sua_senha_com_seguranca_validando_senha_atual(): void
    {
        // 1. Tentar alterar com senha atual errada deve falhar
        $responseErro = $this->actingAs($this->clienteUser)->postJson('/cliente/perfil/senha', [
            'senha_atual' => 'senha_incorreta_999',
            'nova_senha' => 'novaSenhaFoco7!',
            'nova_senha_confirmation' => 'novaSenhaFoco7!',
        ]);

        $responseErro->assertStatus(422);
        $responseErro->assertJson([
            'success' => false,
            'message' => 'A senha atual informada está incorreta.',
        ]);

        // 2. Alterar com senha atual correta
        $responseSucesso = $this->actingAs($this->clienteUser)->postJson('/cliente/perfil/senha', [
            'senha_atual' => 'senha123456',
            'nova_senha' => 'novaSenhaFoco7!',
            'nova_senha_confirmation' => 'novaSenhaFoco7!',
        ]);

        $responseSucesso->assertStatus(200);
        $responseSucesso->assertJson([
            'success' => true,
            'message' => 'Sua senha foi alterada com sucesso!',
        ]);

        $this->clienteUser->refresh();
        $this->assertTrue(Hash::check('novaSenhaFoco7!', $this->clienteUser->password));
    }
}
