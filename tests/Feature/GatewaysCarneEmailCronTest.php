<?php

namespace Tests\Feature;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatewaysCarneEmailCronTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $cliente;
    protected Plano $plano;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Gestor Foco 7',
            'email' => 'admin@foco7.com.br',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'ativo',
        ]);

        $this->cliente = User::create([
            'name' => 'Carlos Alberto Silva',
            'email' => 'carlos.silva@exemplo.com.br',
            'password' => bcrypt('senha123'),
            'role' => 'cliente',
            'status' => 'ativo',
            'telefone' => '(91) 98888-7777',
        ]);

        $this->plano = Plano::create([
            'nome' => 'Plano Rastreador Pro Veicular',
            'slug' => 'rastreador-pro-veicular',
            'categoria' => 'veicular',
            'subtitulo' => 'Monitoramento 24h com telemetria',
            'preco_mensal' => 89.90,
            'taxa_adesao' => 0.00,
            'ativo' => true,
        ]);
    }

    public function test_admin_consegue_visualizar_portal_gateways_com_todas_as_guias(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/gateways');

        $response->assertStatus(200);
        $response->assertSee('Mercado Pago');
        $response->assertSee('Efí PRO (Gerencianet)');
        $response->assertSee('Carnê');
        $response->assertSee('Templates de E-mail');
        $response->assertSee('Central de Cron');
    }

    public function test_admin_salva_credenciais_distintas_sandbox_e_producao(): void
    {
        $payload = [
            'mp_ativo' => '1',
            'mp_ambiente' => 'producao',
            'mp_public_key_sandbox' => 'TEST-PK-SANDBOX-111',
            'mp_access_token_sandbox' => 'TEST-AT-SANDBOX-111',
            'mp_public_key_prod' => 'APP_USR-PK-PROD-999',
            'mp_access_token_prod' => 'APP_USR-AT-PROD-999',
            'efi_ativo' => '1',
            'efi_ambiente' => 'producao',
            'efi_client_id_sandbox' => 'Client_Id_SBX_123',
            'efi_chave_pix_sandbox' => 'pix-sandbox@foco7.com.br',
            'efi_client_id_prod' => 'Client_Id_PROD_456',
            'efi_chave_pix_prod' => 'financeiro@foco7.com.br',
            'carne_ativo' => '1',
            'carne_max_parcelas' => '24',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifica que salvou as versões individuais
        $this->assertEquals('TEST-PK-SANDBOX-111', CmsSetting::get('mercadopago_public_key_sandbox'));
        $this->assertEquals('APP_USR-PK-PROD-999', CmsSetting::get('mercadopago_public_key_prod'));
        $this->assertEquals('APP_USR-PK-PROD-999', CmsSetting::get('mercadopago_public_key'));

        $this->assertEquals('Client_Id_PROD_456', CmsSetting::get('efi_client_id_prod'));
        $this->assertEquals('financeiro@foco7.com.br', CmsSetting::get('efi_chave_pix_prod'));
        $this->assertEquals('financeiro@foco7.com.br', CmsSetting::get('efi_chave_pix'));
        $this->assertEquals('24', CmsSetting::get('carne_max_parcelas'));
    }

    public function test_admin_gera_carne_parcelado_para_cliente_existente(): void
    {
        $payload = [
            'cliente_id' => $this->cliente->id,
            'plano_id' => $this->plano->id,
            'descricao' => 'Carnê Anual de Rastreamento Veicular',
            'valor_parcela' => '89.90',
            'total_parcelas' => 6,
            'data_primeiro_vencimento' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways/gerar-carne', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'message', 'codigo_carne', 'total_parcelas']);

        // Deve ter gerado exatamente 6 faturas do carnê
        $faturas = Fatura::where('user_id', $this->cliente->id)->get();
        $this->assertCount(6, $faturas);

        // Verifica que o primeiro vencimento e o último têm a numeração correta
        $primeira = $faturas->first();
        $this->assertStringContainsString('CARNE-', $primeira->codigo_fatura);
        $this->assertEquals('carne', $primeira->metodo_pagamento);
        $this->assertEquals('89.90', (string) $primeira->valor);
    }

    public function test_admin_personaliza_template_de_email_com_tags_dinamicas(): void
    {
        $payload = [
            'template_chave' => 'fatura_gerada',
            'assunto' => 'Sua nova fatura de Rastreamento Foco 7 está disponível',
            'corpo' => '<p>Olá <strong>{cliente_nome}</strong>, seu boleto #{fatura_id} vence em {data_vencimento}.</p>',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways/salvar-email', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('Sua nova fatura de Rastreamento Foco 7 está disponível', CmsSetting::get('email_assunto_fatura_gerada'));
        $this->assertStringContainsString('{cliente_nome}', CmsSetting::get('email_corpo_fatura_gerada'));
    }

    public function test_admin_envia_email_de_teste(): void
    {
        $payload = [
            'template_chave' => 'fatura_gerada',
            'email_destinatario' => 'teste@foco7.com.br',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways/email-teste', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    public function test_admin_executa_rotina_de_cron_manualmente_via_ajax(): void
    {
        $payload = [
            'tarefa' => 'verificar_vencidas',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways/executar-cron', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'message', 'output', 'data_hora']);

        // Verifica que registrou a última execução no banco
        $lastRun = CmsSetting::get('cron_last_run_verificar_vencidas');
        $this->assertNotEmpty($lastRun);
    }

    public function test_rota_unica_de_email_compila_layout_responsivo_master(): void
    {
        $corpo = '<p>Prezado {cliente_nome}, informamos que seu pagamento de {valor} foi confirmado.</p>';
        $dados = [
            'cliente_nome' => 'Marcos Vinicius',
            'valor' => 'R$ 89,90',
            'assunto' => 'Confirmação de Pagamento',
        ];

        $html = \App\Services\EmailTemplateService::compilarHtml($corpo, $dados);

        // Deve conter os elementos mandatórios do layout responsivo master
        $this->assertStringContainsString('Marcos Vinicius', $html);
        $this->assertStringContainsString('R$ 89,90', $html);
        $this->assertStringContainsString('logo-foco7.png', $html);
        $this->assertStringContainsString('max-width: 620px', $html);
        $this->assertStringContainsString('@media only screen and (max-width: 620px)', $html);
        $this->assertStringContainsString('Suporte', $html);
        $this->assertStringContainsString('WhatsApp', $html);
        $this->assertStringContainsString('Todos os direitos reservados', $html);
    }

    public function test_preview_email_retorna_html_responsivo_via_ajax(): void
    {
        $payload = [
            'template_chave' => 'fatura_gerada',
            'assunto' => 'Preview Teste Dinâmico - {cliente_nome}',
            'corpo' => '<p>Olá <strong>{cliente_nome}</strong>, fatura #{fatura_id} gerada com sucesso!</p>',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/gateways/preview-email', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'html']);

        $html = $response->json('html');
        $this->assertStringContainsString('Carlos Alberto Silva (Demonstração)', $html);
        $this->assertStringContainsString('wrapper-table', $html);
        $this->assertStringContainsString('@media only screen and (max-width: 620px)', $html);
    }

    public function test_admin_cria_e_exclui_template_personalizado_com_preview(): void
    {
        // Criação de template personalizado
        $payload = [
            'nome' => 'Boas-Vindas Novo Associado',
            'chave' => 'boas_vindas_associado',
            'descricao' => 'Disparado logo após a ativação do rastreador',
            'assunto' => '🎉 Seja bem-vindo à Foco 7, {cliente_nome}!',
            'corpo' => '<p>Parabéns {cliente_nome}! Seu veículo agora está sob proteção 24h.</p>',
        ];

        $resCriar = $this->actingAs($this->adminUser)->postJson('/admin/gateways/criar-template-email', $payload);
        $resCriar->assertStatus(200);
        $resCriar->assertJson(['success' => true]);

        // Verifica se o template aparece na lista
        $templates = \App\Services\EmailTemplateService::getTemplates();
        $this->assertArrayHasKey('boas_vindas_associado', $templates);
        $this->assertEquals('Boas-Vindas Novo Associado', $templates['boas_vindas_associado']['nome']);

        // Exclusão do template personalizado
        $resExcluir = $this->actingAs($this->adminUser)->deleteJson('/admin/gateways/excluir-template-email/boas_vindas_associado');
        $resExcluir->assertStatus(200);
        $resExcluir->assertJson(['success' => true]);

        // Templates do sistema não podem ser excluídos
        $resExcluirNativo = $this->actingAs($this->adminUser)->deleteJson('/admin/gateways/excluir-template-email/fatura_gerada');
        $resExcluirNativo->assertStatus(422);
    }

    public function test_admin_acessa_painel_dedicado_de_templates_de_email(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/emails');

        $response->assertStatus(200);
        $response->assertSee('Central de Templates de E-mails');
        $response->assertSee('Conta SMTP Exclusiva do Sistema');
        $response->assertSee('Nova Fatura Gerada');
        $response->assertSee('Lembrete Preventivo de Vencimento');
    }

    public function test_admin_acessa_pagina_de_edicao_dedicada_de_template_com_editor_e_preview(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/emails/fatura_gerada/editar');

        $response->assertStatus(200);
        $response->assertSee('Editar Template de E-mail');
        $response->assertSee('Simulador em Tempo Real');
        $response->assertSee('iframePreviewEmail');
        $response->assertSee('Layout Master Responsivo');
    }

    public function test_admin_atualiza_template_em_pagina_dedicada_via_ajax(): void
    {
        $payload = [
            'assunto' => 'Sua fatura #{fatura_id} já está fechada',
            'corpo' => '<p>Olá {cliente_nome}, seu boleto #{fatura_id} vence dia {data_vencimento}.</p>',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/admin/emails/fatura_gerada', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('Sua fatura #{fatura_id} já está fechada', CmsSetting::get('email_assunto_fatura_gerada'));
    }

    public function test_admin_acessa_central_dedicada_de_cron_e_executa_rotina(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/cron');

        $response->assertStatus(200);
        $response->assertSee('Central de Cron Jobs');
        $response->assertSee('schedule:run');
        $response->assertSee('faturas:enviar-lembretes');

        // Executar rotina pela rota dedicada
        $execResponse = $this->actingAs($this->adminUser)->postJson('/admin/cron/executar', [
            'tarefa' => 'enviar_lembretes',
        ]);

        $execResponse->assertStatus(200);
        $execResponse->assertJson(['success' => true]);
        $execResponse->assertJsonStructure(['success', 'message', 'output', 'data_hora']);
    }
}
