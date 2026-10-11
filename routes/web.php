<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Instalador\InstaladorController;
use App\Http\Controllers\Pagamento\CheckoutController;
use App\Http\Controllers\Pagamento\WebhookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\PlanosController;
use App\Http\Controllers\Admin\AgendamentosController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ManutencaoController;
use App\Http\Controllers\Admin\GatewaysController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\ConfiguracoesController;
use App\Http\Controllers\Admin\ServicosEsteticaController;
use App\Http\Controllers\Admin\ClientesController;
use App\Http\Controllers\Admin\FaturasController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\CronController;
use App\Http\Controllers\Admin\NotificacoesController;
use App\Http\Controllers\Admin\DepoimentosController;
use App\Http\Controllers\Cliente\ClienteDashboardController;
use App\Http\Controllers\Cliente\ClienteFaturasController;

use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas do Site Foco 7 (Landing Page)
|--------------------------------------------------------------------------
*/
Route::get('/', [SiteController::class, 'index'])->name('site.home');
Route::get('/privacidade', [SiteController::class, 'privacidade'])->name('site.privacidade');
Route::post('/contato', [SiteController::class, 'contato'])->name('site.contato');
Route::post('/agendar', [SiteController::class, 'agendar'])->name('site.agendar');
Route::post('/depoimento/enviar', [SiteController::class, 'enviarDepoimento'])->name('site.depoimento.enviar');
Route::get('/manutencao/bypass/{token}', [SiteController::class, 'bypassManutencao'])->name('site.manutencao.bypass');
Route::post('/checkout/gerar', [CheckoutController::class, 'checkout'])->name('checkout.gerar');
Route::get('/checkout/calcular-taxas', [CheckoutController::class, 'calcularTaxas'])->name('checkout.calcular_taxas');
Route::get('/faturas/carne/{codigo}/pdf', [CheckoutController::class, 'downloadCarnePdf'])->name('faturas.carne.pdf');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação & Recuperação de Senha Segura
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/status', [AuthController::class, 'statusAuth'])->name('auth.status');
Route::post('/auth/rapido/login', [AuthController::class, 'loginRapidoAjax'])->name('auth.rapido.login');
Route::post('/auth/rapido/cadastro', [AuthController::class, 'cadastroRapidoAjax'])->name('auth.rapido.cadastro');

// Esqueci minha senha & Redefinição
Route::get('/esqueci-senha', [ResetPasswordController::class, 'esqueciSenhaForm'])->name('password.request');
Route::post('/esqueci-senha', [ResetPasswordController::class, 'enviarLinkRecuperacao'])->name('password.email');
Route::get('/redefinir-senha/{token}', [ResetPasswordController::class, 'redefinirSenhaForm'])->name('password.reset');
Route::post('/redefinir-senha', [ResetPasswordController::class, 'atualizarSenha'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Assistente Web de Instalação (cPanel / MySQL)
|--------------------------------------------------------------------------
*/
Route::prefix('instalar')->group(function () {
    Route::get('/', [InstaladorController::class, 'index'])->name('instalador.index');
    Route::post('/testar-banco', [InstaladorController::class, 'testarBanco'])->name('instalador.testar_banco');
    Route::post('/executar', [InstaladorController::class, 'executar'])->name('instalador.executar');
});

/*
|--------------------------------------------------------------------------
| Webhooks de Gateways de Pagamento (Isentos de CSRF)
|--------------------------------------------------------------------------
*/
Route::post('/webhook/mercadopago', [WebhookController::class, 'mercadopago'])->name('webhook.mercadopago');
Route::post('/webhook/efi', [WebhookController::class, 'efi'])->name('webhook.efi');

/*
|--------------------------------------------------------------------------
| Painel Administrativo CMS (AdminLTE 4) - Requer papel 'admin'
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // CMS (Banners, Logos, Cores, Textos, FAQs, Depoimentos)
    Route::prefix('cms')->group(function () {
        Route::get('/', [CmsController::class, 'index'])->name('admin.cms.index');
        Route::post('/configuracoes', [CmsController::class, 'salvarConfiguracoes'])->name('admin.cms.salvar_configs');
        Route::get('/banner/obter/{id}', [CmsController::class, 'obterBanner'])->name('admin.cms.obter_banner');
        Route::post('/banner', [CmsController::class, 'salvarBanner'])->name('admin.cms.salvar_banner');
        Route::delete('/banner/{id}', [CmsController::class, 'excluirBanner'])->name('admin.cms.excluir_banner');
        Route::get('/faq/obter/{id}', [CmsController::class, 'obterFaq'])->name('admin.cms.obter_faq');
        Route::post('/faq', [CmsController::class, 'salvarFaq'])->name('admin.cms.salvar_faq');
        Route::delete('/faq/{id}', [CmsController::class, 'excluirFaq'])->name('admin.cms.excluir_faq');
        Route::get('/depoimento/obter/{id}', [CmsController::class, 'obterDepoimento'])->name('admin.cms.obter_depoimento');
        Route::post('/depoimento', [CmsController::class, 'salvarDepoimento'])->name('admin.cms.salvar_depoimento');
        Route::delete('/depoimento/{id}', [CmsController::class, 'excluirDepoimento'])->name('admin.cms.excluir_depoimento');
    });

    // Depoimentos & Moderação de Avaliações (Página Administrativa Dedicada)
    Route::prefix('depoimentos')->group(function () {
        Route::get('/', [DepoimentosController::class, 'index'])->name('admin.depoimentos.index');
        Route::get('/obter/{id}', [DepoimentosController::class, 'obter'])->name('admin.depoimentos.obter');
        Route::post('/salvar', [DepoimentosController::class, 'salvar'])->name('admin.depoimentos.salvar');
        Route::post('/toggle/{id}', [DepoimentosController::class, 'toggleAprovacao'])->name('admin.depoimentos.toggle');
        Route::delete('/{id}', [DepoimentosController::class, 'excluir'])->name('admin.depoimentos.excluir');
    });

    // Estética Automotiva (Serviços & Pacotes)
    Route::prefix('estetica')->group(function () {
        Route::get('/', [ServicosEsteticaController::class, 'index'])->name('admin.estetica.index');
        Route::get('/obter/{id}', [ServicosEsteticaController::class, 'obter'])->name('admin.estetica.obter');
        Route::post('/', [ServicosEsteticaController::class, 'salvar'])->name('admin.estetica.salvar');
        Route::post('/toggle/{id}', [ServicosEsteticaController::class, 'toggleStatus'])->name('admin.estetica.toggle');
        Route::delete('/{id}', [ServicosEsteticaController::class, 'excluir'])->name('admin.estetica.excluir');
    });

    // Gestão de Clientes (Base Cadastral, Edição e Senhas)
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClientesController::class, 'index'])->name('admin.clientes.index');
        Route::get('/obter/{id}', [ClientesController::class, 'obter'])->name('admin.clientes.obter');
        Route::post('/', [ClientesController::class, 'salvar'])->name('admin.clientes.salvar');
        Route::post('/senha/{id}', [ClientesController::class, 'alterarSenha'])->name('admin.clientes.senha');
        Route::delete('/{id}', [ClientesController::class, 'excluir'])->name('admin.clientes.excluir');
    });

    // Gestão de Faturas & Mensalidades
    Route::prefix('faturas')->group(function () {
        Route::get('/', [FaturasController::class, 'index'])->name('admin.faturas.index');
        Route::get('/obter/{id}', [FaturasController::class, 'obter'])->name('admin.faturas.obter');
        Route::post('/', [FaturasController::class, 'salvar'])->name('admin.faturas.salvar');
        Route::post('/status/{id}', [FaturasController::class, 'alterarStatus'])->name('admin.faturas.status');
        Route::delete('/{id}', [FaturasController::class, 'excluir'])->name('admin.faturas.excluir');
    });

    // Planos de Rastreamento
    Route::prefix('planos')->group(function () {
        Route::get('/', [PlanosController::class, 'index'])->name('admin.planos.index');
        Route::get('/obter/{id}', [PlanosController::class, 'obter'])->name('admin.planos.obter');
        Route::post('/', [PlanosController::class, 'salvar'])->name('admin.planos.salvar');
        Route::delete('/{id}', [PlanosController::class, 'excluir'])->name('admin.planos.excluir');
    });

    // Agendamentos (FullCalendar 6 - Rastreamento e Estética)
    Route::prefix('agendamentos')->group(function () {
        Route::get('/', [AgendamentosController::class, 'index'])->name('admin.agendamentos.index');
        Route::get('/eventos', [AgendamentosController::class, 'apiEventos'])->name('admin.agendamentos.eventos');
        Route::get('/obter/{id}', [AgendamentosController::class, 'obter'])->name('admin.agendamentos.obter');
        Route::post('/', [AgendamentosController::class, 'salvar'])->name('admin.agendamentos.salvar');
        Route::post('/horario', [AgendamentosController::class, 'atualizarHorario'])->name('admin.agendamentos.horario');
        Route::delete('/{id}', [AgendamentosController::class, 'excluir'])->name('admin.agendamentos.excluir');
    });

    // SEO Score & Meta Tags
    Route::prefix('seo')->group(function () {
        Route::get('/', [SeoController::class, 'index'])->name('admin.seo.index');
        Route::post('/', [SeoController::class, 'salvar'])->name('admin.seo.salvar');
        Route::post('/analisar', [SeoController::class, 'analisar'])->name('admin.seo.analisar');
    });

    // Modo de Manutenção
    Route::prefix('manutencao')->group(function () {
        Route::get('/', [ManutencaoController::class, 'index'])->name('admin.manutencao.index');
        Route::post('/toggle', [ManutencaoController::class, 'toggle'])->name('admin.manutencao.toggle');
        Route::post('/', [ManutencaoController::class, 'salvar'])->name('admin.manutencao.salvar');
    });

    // Gateways de Pagamento & Cobrança por Carnê
    Route::prefix('gateways')->group(function () {
        Route::get('/', [GatewaysController::class, 'index'])->name('admin.gateways.index');
        Route::post('/', [GatewaysController::class, 'salvar'])->name('admin.gateways.salvar');
        Route::post('/gerar-carne', [GatewaysController::class, 'gerarCarne'])->name('admin.gateways.gerar_carne');
        // Aliases para retrocompatibilidade
        Route::post('/email-teste', [GatewaysController::class, 'enviarEmailTeste'])->name('admin.gateways.email_teste');
        Route::post('/salvar-email', [GatewaysController::class, 'salvarTemplateEmail'])->name('admin.gateways.salvar_email');
        Route::post('/criar-template-email', [GatewaysController::class, 'criarEmailTemplate'])->name('admin.gateways.criar_email_template');
        Route::delete('/excluir-template-email/{chave}', [GatewaysController::class, 'excluirEmailTemplate'])->name('admin.gateways.excluir_email_template');
        Route::post('/preview-email', [GatewaysController::class, 'previewEmail'])->name('admin.gateways.preview_email');
        Route::post('/executar-cron', [GatewaysController::class, 'executarCron'])->name('admin.gateways.executar_cron');
    });

    // Templates de E-mails Próprios (Páginas Dedicadas)
    Route::prefix('emails')->group(function () {
        Route::get('/', [EmailTemplatesController::class, 'index'])->name('admin.emails.index');
        Route::get('/novo', [EmailTemplatesController::class, 'create'])->name('admin.emails.create');
        Route::post('/', [EmailTemplatesController::class, 'store'])->name('admin.emails.store');
        Route::get('/{chave}/editar', [EmailTemplatesController::class, 'edit'])->name('admin.emails.edit');
        Route::post('/{chave}', [EmailTemplatesController::class, 'update'])->name('admin.emails.update');
        Route::delete('/{chave}', [EmailTemplatesController::class, 'destroy'])->name('admin.emails.destroy');
        Route::post('/preview/render', [EmailTemplatesController::class, 'preview'])->name('admin.emails.preview');
        Route::post('/teste/enviar', [EmailTemplatesController::class, 'testar'])->name('admin.emails.testar');
    });

    // Central de Cron Jobs & Automação do Agendador
    Route::prefix('cron')->group(function () {
        Route::get('/', [CronController::class, 'index'])->name('admin.cron.index');
        Route::get('/status', [CronController::class, 'status'])->name('admin.cron.status');
        Route::post('/executar', [CronController::class, 'executar'])->name('admin.cron.executar');
        Route::post('/salvar', [CronController::class, 'store'])->name('admin.cron.store');
        Route::put('/atualizar/{id}', [CronController::class, 'update'])->name('admin.cron.update');
        Route::post('/toggle/{id}', [CronController::class, 'toggle'])->name('admin.cron.toggle');
        Route::delete('/excluir/{id}', [CronController::class, 'destroy'])->name('admin.cron.destroy');
    });

    // Meu Perfil de Usuário
    Route::prefix('perfil')->group(function () {
        Route::get('/', [PerfilController::class, 'index'])->name('admin.perfil.index');
        Route::post('/dados', [PerfilController::class, 'salvarDados'])->name('admin.perfil.salvar_dados');
        Route::post('/avatar', [PerfilController::class, 'salvarAvatar'])->name('admin.perfil.salvar_avatar');
        Route::post('/senha', [PerfilController::class, 'alterarSenha'])->name('admin.perfil.alterar_senha');
    });

    // Configurações do Sistema & Servidor SMTP
    Route::prefix('configuracoes')->group(function () {
        Route::get('/', [ConfiguracoesController::class, 'index'])->name('admin.configuracoes.index');
        Route::post('/salvar', [ConfiguracoesController::class, 'salvar'])->name('admin.configuracoes.salvar');
        Route::post('/testar-smtp', [ConfiguracoesController::class, 'testarSmtp'])->name('admin.configuracoes.testar_smtp');
    });

    // Central de Notificações (Web Push, E-mails e Alertas em Tempo Real)
    Route::prefix('notificacoes')->group(function () {
        Route::get('/', [NotificacoesController::class, 'index'])->name('admin.notificacoes.index');
        Route::get('/ultimas-ajax', [NotificacoesController::class, 'ultimasAjax'])->name('admin.notificacoes.ultimas_ajax');
        Route::post('/marcar-todas', [NotificacoesController::class, 'marcarTodasLidas'])->name('admin.notificacoes.marcar_todas');
        Route::post('/salvar-config', [NotificacoesController::class, 'salvarConfiguracoes'])->name('admin.notificacoes.salvar_config');
        Route::post('/teste', [NotificacoesController::class, 'dispararTeste'])->name('admin.notificacoes.teste');
        Route::post('/{notificacao}/lida', [NotificacoesController::class, 'marcarLida'])->name('admin.notificacoes.marcar_lida');
        Route::get('/{notificacao}/abrir', [NotificacoesController::class, 'abrirERedirecionar'])->name('admin.notificacoes.abrir');
        Route::delete('/{notificacao}', [NotificacoesController::class, 'excluir'])->name('admin.notificacoes.excluir');
    });

    // Depoimentos & Moderação de Avaliações
    Route::prefix('depoimentos')->group(function () {
        Route::get('/', [DepoimentosController::class, 'index'])->name('admin.depoimentos.index');
        Route::get('/obter/{id}', [DepoimentosController::class, 'obter'])->name('admin.depoimentos.obter');
        Route::get('/{id}/obter', [DepoimentosController::class, 'obter']);
        Route::post('/salvar', [DepoimentosController::class, 'salvar'])->name('admin.depoimentos.salvar');
        Route::post('/toggle/{id}', [DepoimentosController::class, 'toggleAprovacao'])->name('admin.depoimentos.toggle_aprovacao');
        Route::post('/{id}/toggle-aprovacao', [DepoimentosController::class, 'toggleAprovacao']);
        Route::delete('/{id}', [DepoimentosController::class, 'excluir'])->name('admin.depoimentos.excluir');
        Route::delete('/{id}/excluir', [DepoimentosController::class, 'excluir']);
    });
});

/*
|--------------------------------------------------------------------------
| Área do Cliente (Faturas, Serviços, Agendamentos) - Requer papel 'cliente'
|--------------------------------------------------------------------------
*/
Route::prefix('cliente')->middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/', fn () => redirect()->route('cliente.dashboard'));
    Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('cliente.dashboard');
    Route::post('/agendamentos/solicitar', [ClienteDashboardController::class, 'solicitarAgendamento'])->name('cliente.agendamentos.solicitar');
    Route::get('/faturas', [ClienteFaturasController::class, 'index'])->name('cliente.faturas.index');
    Route::get('/faturas/{codigo}', [ClienteFaturasController::class, 'detalhes'])->name('cliente.faturas.detalhes');
});
