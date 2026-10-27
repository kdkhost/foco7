<?php

use App\Http\Controllers\Admin\AgendamentosController;
use App\Http\Controllers\Admin\ClientesController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ConfiguracoesController;
use App\Http\Controllers\Admin\CronController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepoimentosController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\FaturasController;
use App\Http\Controllers\Admin\FiliaisController;
use App\Http\Controllers\Admin\GatewaysController;
use App\Http\Controllers\Admin\ManutencaoController;
use App\Http\Controllers\Admin\NotificacoesController;
use App\Http\Controllers\Admin\ParceirosController;
use App\Http\Controllers\Admin\PerfilController;
use App\Http\Controllers\Admin\PlanosController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServicosEsteticaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Cliente\ClienteDashboardController;
use App\Http\Controllers\Cliente\ClienteFaturasController;
use App\Http\Controllers\Instalador\InstaladorController;
use App\Http\Controllers\Pagamento\CheckoutController;
use App\Http\Controllers\Pagamento\WebhookController;
use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas do Site Foco 7 (Landing Page)
|--------------------------------------------------------------------------
*/
Route::get('/', [SiteController::class, 'index'])->name('site.home');
Route::get('/manutencao', [SiteController::class, 'manutencaoView'])->name('site.manutencao.publica');
Route::get('/privacidade', [SiteController::class, 'privacidade'])->name('site.privacidade');
Route::post('/contato', [SiteController::class, 'contato'])->name('site.contato');
Route::post('/agendar', [SiteController::class, 'agendar'])->name('site.agendar');
Route::post('/depoimento/enviar', [SiteController::class, 'enviarDepoimento'])->name('site.depoimento.enviar');
Route::get('/manutencao/bypass/{token}', [SiteController::class, 'bypassManutencao'])->name('site.manutencao.bypass');
Route::post('/checkout/gerar', [CheckoutController::class, 'checkout'])->name('checkout.gerar');
Route::get('/checkout/calcular-taxas', [CheckoutController::class, 'calcularTaxas'])->name('checkout.calcular_taxas');
Route::get('/faturas/carne/{codigo}/pdf', [CheckoutController::class, 'downloadCarnePdf'])->name('faturas.carne.pdf');
Route::get('/uploads/{path}', [SiteController::class, 'servirMidiaUpload'])->where('path', '.*')->name('midia.uploads');
Route::get('/storage/{path}', [SiteController::class, 'servirMidiaStorage'])->where('path', '.*')->name('midia.storage');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação & Recuperação de Senha Segura
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
Route::get('/login/2fa', [AuthController::class, 'twoFactorChallengeForm'])->name('login.2fa');
Route::post('/login/2fa', [AuthController::class, 'twoFactorChallengeVerify'])->middleware('throttle:5,1')->name('login.2fa.verify');
Route::get('/login/2fa/cancelar', [AuthController::class, 'cancelTwoFactor'])->name('login.2fa.cancel');
Route::get('/cadastro', [AuthController::class, 'cadastroForm'])->name('register');
Route::post('/cadastro', [AuthController::class, 'cadastro'])->middleware('throttle:5,1')->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/status', [AuthController::class, 'statusAuth'])->name('auth.status');
Route::match(['get', 'post'], '/auth/ping-sessao', [AuthController::class, 'pingSessao'])->name('auth.ping_sessao');
Route::post('/auth/rapido/login', [AuthController::class, 'loginRapidoAjax'])->middleware('throttle:10,1')->name('auth.rapido.login');
Route::post('/auth/rapido/cadastro', [AuthController::class, 'cadastroRapidoAjax'])->middleware('throttle:5,1')->name('auth.rapido.cadastro');

// Esqueci minha senha & Redefinição
Route::get('/esqueci-senha', [ResetPasswordController::class, 'esqueciSenhaForm'])->name('password.request');
Route::post('/esqueci-senha', [ResetPasswordController::class, 'enviarLinkRecuperacao'])->middleware('throttle:5,1')->name('password.email');
Route::get('/redefinir-senha/{token}', [ResetPasswordController::class, 'redefinirSenhaForm'])->name('password.reset');
Route::post('/redefinir-senha', [ResetPasswordController::class, 'atualizarSenha'])->middleware('throttle:10,1')->name('password.update');

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

    // Parceiros & Marcas Homologadas (Carrossel Infinito CMS)
    Route::prefix('parceiros')->group(function () {
        Route::get('/', [ParceirosController::class, 'index'])->name('admin.parceiros.index');
        Route::get('/obter/{id}', [ParceirosController::class, 'obter'])->name('admin.parceiros.obter');
        Route::post('/salvar', [ParceirosController::class, 'salvar'])->name('admin.parceiros.salvar');
        Route::post('/toggle/{id}', [ParceirosController::class, 'toggle'])->name('admin.parceiros.toggle');
        Route::delete('/{id}', [ParceirosController::class, 'excluir'])->name('admin.parceiros.excluir');
    });

    // Filiais & Localidades de Atendimento (Mapa Interativo do Brasil)
    Route::prefix('filiais')->group(function () {
        Route::get('/', [FiliaisController::class, 'index'])->name('admin.filiais.index');
        Route::get('/obter/{id}', [FiliaisController::class, 'obter'])->name('admin.filiais.obter');
        Route::post('/salvar', [FiliaisController::class, 'salvar'])->name('admin.filiais.salvar');
        Route::post('/toggle/{id}', [FiliaisController::class, 'toggle'])->name('admin.filiais.toggle');
        Route::delete('/{id}', [FiliaisController::class, 'excluir'])->name('admin.filiais.excluir');
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
        Route::get('/carne-info/{id}', [FaturasController::class, 'obterInfoCarne'])->name('admin.faturas.carne_info');
        Route::post('/', [FaturasController::class, 'salvar'])->name('admin.faturas.salvar');
        Route::post('/status/{id}', [FaturasController::class, 'alterarStatus'])->name('admin.faturas.status');
        Route::post('/cancelar-carne/{id}', [FaturasController::class, 'cancelarCarne'])->name('admin.faturas.cancelar_carne');
        Route::post('/excluir-carne/{id}', [FaturasController::class, 'excluirCarne'])->name('admin.faturas.excluir_carne');
        Route::post('/estornar/{id}', [FaturasController::class, 'estornar'])->name('admin.faturas.estornar');
        Route::post('/antecipar/{id}', [FaturasController::class, 'anteciparParcela'])->name('admin.faturas.antecipar');
        Route::post('/antecipar-lote', [FaturasController::class, 'anteciparLote'])->name('admin.faturas.antecipar_lote');
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
        Route::post('/cancelar/{id}', [AgendamentosController::class, 'cancelar'])->name('admin.agendamentos.cancelar');
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
        Route::post('/limpar-ips', [ManutencaoController::class, 'limparIps'])->name('admin.manutencao.limpar_ips');
        Route::get('/preview', [ManutencaoController::class, 'preview'])->name('admin.manutencao.preview');
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
        Route::post('/executar-regua', [GatewaysController::class, 'executarRegua'])->name('admin.gateways.executar_regua');
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

    // Gestão de Usuários & Permissões do Sistema
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
        Route::post('/', [UsuarioController::class, 'store'])->name('admin.usuarios.store');
        Route::get('/{id}', [UsuarioController::class, 'show'])->name('admin.usuarios.show');
        Route::post('/{id}', [UsuarioController::class, 'update'])->name('admin.usuarios.update');
        Route::post('/toggle/{id}', [UsuarioController::class, 'toggleStatus'])->name('admin.usuarios.toggle');
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy');
        Route::post('/reset-senha/{id}', [UsuarioController::class, 'resetPassword'])->name('admin.usuarios.reset_password');
    });

    // Meu Perfil de Usuário & Autenticação de 2 Fatores (2FA)
    Route::prefix('perfil')->group(function () {
        Route::get('/', [PerfilController::class, 'index'])->name('admin.perfil.index');
        Route::post('/dados', [PerfilController::class, 'salvarDados'])->name('admin.perfil.salvar_dados');
        Route::post('/avatar', [PerfilController::class, 'salvarAvatar'])->name('admin.perfil.salvar_avatar');
        Route::post('/senha', [PerfilController::class, 'alterarSenha'])->name('admin.perfil.alterar_senha');
        Route::post('/2fa/iniciar', [PerfilController::class, 'iniciar2fa'])->name('admin.perfil.2fa.iniciar');
        Route::post('/2fa/confirmar', [PerfilController::class, 'confirmar2fa'])->name('admin.perfil.2fa.confirmar');
        Route::post('/2fa/desativar', [PerfilController::class, 'desativar2fa'])->name('admin.perfil.2fa.desativar');
        Route::post('/2fa/recovery-codes', [PerfilController::class, 'visualizarRecoveryCodes'])->name('admin.perfil.2fa.recovery_codes');
        Route::post('/2fa/regenerar-recovery-codes', [PerfilController::class, 'regenerarRecoveryCodes'])->name('admin.perfil.2fa.regenerar_recovery_codes');
    });

    // Configurações do Sistema & Servidor SMTP
    Route::prefix('configuracoes')->group(function () {
        Route::get('/', [ConfiguracoesController::class, 'index'])->name('admin.configuracoes.index');
        Route::post('/salvar', [ConfiguracoesController::class, 'salvar'])->name('admin.configuracoes.salvar');
        Route::post('/testar-smtp', [ConfiguracoesController::class, 'testarSmtp'])->name('admin.configuracoes.testar_smtp');
        Route::post('/testar-geoip', [ConfiguracoesController::class, 'testarGeoip'])->name('admin.configuracoes.testar_geoip');
        Route::post('/desbloquear-dev', [ConfiguracoesController::class, 'desbloquearDev'])->name('admin.configuracoes.desbloquear_dev');
        Route::post('/bloquear-dev', [ConfiguracoesController::class, 'bloquearDev'])->name('admin.configuracoes.bloquear_dev');
    });

    // Central de Notificações (Web Push, E-mails e Alertas em Tempo Real)
    Route::prefix('notificacoes')->group(function () {
        Route::get('/', [NotificacoesController::class, 'index'])->name('admin.notificacoes.index');
        Route::get('/ultimas-ajax', [NotificacoesController::class, 'ultimasAjax'])->name('admin.notificacoes.ultimas_ajax');
        Route::post('/marcar-todas', [NotificacoesController::class, 'marcarTodasLidas'])->name('admin.notificacoes.marcar_todas');
        Route::post('/limpar-lidas', [NotificacoesController::class, 'limparLidas'])->name('admin.notificacoes.limpar_lidas');
        Route::post('/salvar-config', [NotificacoesController::class, 'salvarConfiguracoes'])->name('admin.notificacoes.salvar_config');
        Route::post('/teste', [NotificacoesController::class, 'dispararTeste'])->name('admin.notificacoes.teste');
        Route::post('/{notificacao}/lida', [NotificacoesController::class, 'marcarLida'])->name('admin.notificacoes.marcar_lida');
        Route::get('/{notificacao}/obter', [NotificacoesController::class, 'obter'])->name('admin.notificacoes.obter');
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
    Route::post('/agendamentos/{id}/cancelar', [ClienteDashboardController::class, 'cancelarAgendamento'])->name('cliente.agendamentos.cancelar');
    Route::get('/faturas', [ClienteFaturasController::class, 'index'])->name('cliente.faturas.index');
    Route::get('/faturas/{codigo}', [ClienteFaturasController::class, 'detalhes'])->name('cliente.faturas.detalhes');
    Route::get('/perfil', [ClienteDashboardController::class, 'perfil'])->name('cliente.perfil');
    Route::post('/perfil/dados', [ClienteDashboardController::class, 'salvarDados'])->name('cliente.perfil.dados');
    Route::post('/perfil/senha', [ClienteDashboardController::class, 'alterarSenha'])->name('cliente.perfil.senha');
    Route::post('/perfil/avatar', [ClienteDashboardController::class, 'salvarAvatar'])->name('cliente.perfil.avatar');
});
