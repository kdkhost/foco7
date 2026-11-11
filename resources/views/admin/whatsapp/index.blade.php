@extends('admin.layouts.app')

@section('title', 'WhatsApp Evolution Go & Cobranças PIX')
@section('page_title', 'WhatsApp Cobranças & Evolution API Go (v3.0)')
@section('page_description', 'Gerencie instâncias dedicadas, cobranças recorrentes PIX (R$ 60/mês), suspensão automática e mensageria inteligente.')

@section('styles')
<style>
    /* Estilos Modernos & Microinterações */
    .pulse-online {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #198754;
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        animation: pulseGreen 1.8s infinite;
    }
    .pulse-offline {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #dc3545;
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        animation: pulseRed 1.8s infinite;
    }
    .pulse-warning {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #ffc107;
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        animation: pulseYellow 1.8s infinite;
    }
    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(25, 135, 84, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    @keyframes pulseRed {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    @keyframes pulseYellow {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 193, 7, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    .kpi-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }
    [data-bs-theme="dark"] .kpi-card {
        border: 1px solid rgba(255,255,255,0.08);
    }

    .tarja-dev-bloqueado {
        letter-spacing: 2px !important;
        font-weight: 700 !important;
        background-color: rgba(108, 117, 125, 0.08) !important;
        color: var(--bs-secondary-color) !important;
        cursor: not-allowed;
    }

    /* Simulador de Smartphone WhatsApp */
    .smartphone-mockup {
        width: 100%;
        max-width: 360px;
        height: 600px;
        background: #0b141a;
        border: 10px solid #2a2f32;
        border-radius: 36px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        margin: 0 auto;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .phone-notch {
        width: 130px;
        height: 20px;
        background: #2a2f32;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        margin: 0 auto;
        z-index: 10;
    }
    .phone-header {
        background: #202c33;
        color: #e9edef;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #2a3942;
    }
    .phone-body {
        flex: 1;
        background-color: #0b141a;
        background-image: radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
        background-size: 16px 16px;
        padding: 14px;
        overflow-y: auto;
    }
    .chat-bubble-wpp {
        background: #005c4b;
        color: #e9edef;
        border-radius: 10px 10px 2px 10px;
        padding: 10px 12px;
        max-width: 90%;
        margin-left: auto;
        box-shadow: 0 2px 4px rgba(0,0,0,0.25);
        font-size: 13px;
        line-height: 1.45;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .chat-bubble-footer {
        font-size: 10px;
        color: rgba(255,255,255,0.6);
        text-align: right;
        margin-top: 4px;
    }
    .chat-btn-action {
        background: #202c33;
        border-top: 1px solid rgba(255,255,255,0.1);
        color: #53bdeb;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        padding: 8px;
        margin-top: 6px;
        border-radius: 6px;
        display: block;
        text-decoration: none;
    }
    .chat-btn-action:hover {
        background: #2a3942;
        color: #70c7ed;
    }

    /* Timeline Régua */
    .timeline-regua {
        position: relative;
        padding-left: 28px;
    }
    .timeline-regua::before {
        content: '';
        position: absolute;
        top: 10px;
        bottom: 10px;
        left: 10px;
        width: 3px;
        background: #dee2e6;
    }
    [data-bs-theme="dark"] .timeline-regua::before {
        background: #373b3e;
    }
    .timeline-step {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-step-icon {
        position: absolute;
        left: -28px;
        top: 2px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid var(--bs-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        z-index: 2;
    }
    [data-bs-theme="dark"] .timeline-step-icon {
        background: #212529;
    }

    .formato-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid var(--bs-border-color, #dee2e6) !important;
        background-color: var(--bs-body-bg, #ffffff) !important;
        border-radius: 10px;
    }
    .formato-card:hover {
        transform: translateY(-2px);
        border-color: rgba(var(--bs-primary-rgb, 217, 0, 0), 0.45) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .formato-card.selecionado {
        border-color: var(--bs-primary, #d90000) !important;
        background-color: rgba(var(--bs-primary-rgb, 217, 0, 0), 0.04) !important;
        box-shadow: 0 4px 16px rgba(var(--bs-primary-rgb, 217, 0, 0), 0.12) !important;
    }
    .formato-card.selecionado h6 {
        color: var(--bs-primary, #d90000) !important;
        font-weight: 700;
    }
    .formato-card.selecionado p,
    .formato-card.selecionado ul,
    .formato-card.selecionado li {
        color: var(--bs-body-color) !important;
    }
    [data-bs-theme="dark"] .formato-card {
        border-color: #373b3e !important;
        background-color: #1e2227 !important;
    }
    [data-bs-theme="dark"] .formato-card.selecionado {
        border-color: #ff4d4d !important;
        background-color: rgba(255, 77, 77, 0.12) !important;
        box-shadow: 0 4px 16px rgba(255, 77, 77, 0.2) !important;
    }
    [data-bs-theme="dark"] .formato-card.selecionado p,
    [data-bs-theme="dark"] .formato-card.selecionado ul,
    [data-bs-theme="dark"] .formato-card.selecionado li {
        color: #e9ecef !important;
    }

    .nav-tabs .nav-link {
        font-weight: 600;
        color: var(--bs-secondary-color);
        padding: 12px 18px;
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: var(--bs-primary) !important;
        border-bottom: 3px solid var(--bs-primary) !important;
        background: transparent !important;
    }

    /* Personalização e Integração do Summernote BS5 */
    .note-editor.note-frame {
        border-radius: 8px !important;
        border-color: var(--bs-border-color, #dee2e6) !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    [data-bs-theme="dark"] .note-editor.note-frame {
        background-color: #212529 !important;
        border-color: #373b3e !important;
        color: #f8f9fa !important;
    }
    [data-bs-theme="dark"] .note-editor.note-frame .note-toolbar {
        background-color: #2c3035 !important;
        border-bottom-color: #373b3e !important;
    }
    [data-bs-theme="dark"] .note-editor.note-frame .note-statusbar {
        background-color: #2c3035 !important;
        border-top-color: #373b3e !important;
    }
    [data-bs-theme="dark"] .note-editor.note-frame .note-editing-area .note-editable {
        background-color: #1e2227 !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .note-editor .note-btn {
        background-color: #373b3e !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .note-editor .note-btn:hover {
        background-color: #495057 !important;
        color: #fff !important;
    }
    [data-bs-theme="dark"] .note-editor .dropdown-menu {
        background-color: #2c3035 !important;
        color: #e9ecef !important;
        border-color: #373b3e !important;
    }
    [data-bs-theme="dark"] .note-editor .dropdown-item {
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .note-editor .dropdown-item:hover {
        background-color: #373b3e !important;
    }
</style>
@endsection

@section('content')
<div class="row g-4">

    <!-- ===================================================================
         HERO HEADER: STATUS EM TEMPO REAL & CONTROLE RÁPIDO DO CLUSTER
         =================================================================== -->
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden bg-body">
            <div class="card-body p-4 bg-body-secondary border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" id="statusIconBox" style="width: 64px; height: 64px; background-color: rgba(25, 135, 84, 0.15);">
                            <i class="fab fa-whatsapp fs-1 text-success" id="statusIcon"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h3 class="fw-bold mb-0 text-body">WhatsApp Evolution API Go</h3>
                                <span class="badge {{ $statusCache === 'conectado' ? 'bg-success' : 'bg-secondary' }} px-3 py-2 fs-6 text-uppercase shadow-xs" id="badgeStatusGeral">
                                    <span class="{{ $statusCache === 'conectado' ? 'pulse-online' : 'pulse-offline' }} me-2" id="badgeStatusPulse"></span>
                                    <span id="badgeStatusTexto">{{ $statusCache === 'conectado' ? 'Conectado' : ucfirst($statusCache) }}</span>
                                </span>
                                <span class="badge {{ $statusCache === 'conectado' ? 'bg-success-subtle text-success border border-success' : 'd-none' }} px-2 py-1 small" id="badgeNumeroConectadoHero">
                                    <i class="fab fa-whatsapp me-1"></i> <span id="txtNumeroConectadoHero">{{ $numeroConectado ?? '' }}</span>
                                </span>
                                <span class="badge bg-dark text-white border border-secondary px-2 py-1 small">
                                    <i class="fas fa-lock text-warning me-1"></i> AES-256 Protegido
                                </span>
                                <span class="badge bg-primary text-white px-2 py-1 small" id="badgeLatenciaPing">
                                    <i class="fas fa-bolt me-1"></i> <span id="txtLatenciaMs">Ping: Testar</span>
                                </span>
                            </div>
                            <p class="text-secondary small mb-0">
                                Instância Principal: <strong class="text-body font-monospace" id="txtInstanciaNome">{{ $configs['instance'] }}</strong> | 
                                Servidor: <a href="https://api.kdkhost.com.br/docs/llms.txt" target="_blank" class="text-primary font-monospace fw-semibold" id="txtApiUrl">{{ $configs['api_url'] }}</a> | 
                                Instâncias Extras: <strong class="text-danger">{{ $totalInstanciasExtras ?? 0 }} (R$ {{ number_format($custoTotalRecorrente ?? 0, 2, ',', '.') }}/mês)</strong> | 
                                Chave PIX Recorrência: 
                                @if($configs['dev_desbloqueado'])
                                    <strong class="font-monospace text-primary" id="txtHeroPixRecorrencia">{{ $configs['pix_email_recorrencia'] }}</strong>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace" id="txtHeroPixRecorrencia" title="Clique para desbloquear com a Senha Master" style="cursor: pointer;" onclick="solicitarSenhaSegurancaWpp()">
                                        <i class="fas fa-lock me-1"></i> ••••••••••••••• (Restrito Master)
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- BOTÕES DE AÇÃO RÁPIDA -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" id="btnTestarLatencia" onclick="testarLatenciaServidor()">
                            <i class="fas fa-bolt me-1" id="iconLatencia"></i> Testar Latência
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btnVerificarStatus" onclick="verificarStatusApi(true)">
                            <i class="fas fa-rotate me-1" id="iconVerificarStatus"></i> Verificar Status
                        </button>
                        <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm px-3" id="btnAbrirModalQr" onclick="conectarEabrirQr()">
                            <i class="fas fa-qrcode me-1"></i> Conectar / QR Code
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm fw-semibold" id="btnDesconectarWpp" onclick="desconectarInstancia()">
                            <i class="fas fa-power-off me-1"></i> Desconectar
                        </button>
                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarInstancia">
                            <i class="fas fa-plus-circle me-1"></i> Criar Minha Instância (+R$ 60/mês)
                        </button>
                    </div>
                </div>
            </div>

            <!-- BANNER DE REGRA DE NEGÓCIO: INSTÂNCIA PRÓPRIA DO ADMIN X COBRANÇA VIA E-MAIL -->
            <div class="card-footer px-4 py-3 border-0 {{ ($permissaoWhatsapp['permitido'] ?? false) ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-circle {{ ($permissaoWhatsapp['permitido'] ?? false) ? 'bg-success bg-opacity-20 text-success' : 'bg-warning bg-opacity-25 text-warning' }}">
                            <i class="fas {{ ($permissaoWhatsapp['permitido'] ?? false) ? 'fa-check-double' : 'fa-triangle-exclamation' }} fs-5"></i>
                        </div>
                        <div>
                            @if($permissaoWhatsapp['permitido'] ?? false)
                                <div class="fw-bold text-success small text-uppercase">
                                    <i class="fab fa-whatsapp me-1"></i> Cobranças de Clientes via WhatsApp Liberadas
                                </div>
                                <div class="small text-body">
                                    Sua instância própria (<code class="fw-bold">{{ $permissaoWhatsapp['instancia_propria']['nome'] ?? $configs['instance'] }}</code>) está ativa e em dia no Módulo Mercado Pago do Desenvolvedor. Disparos via WhatsApp e E-mail operacionais.
                                </div>
                            @else
                                <div class="fw-bold text-danger small text-uppercase">
                                    <i class="fas fa-envelope-circle-check me-1"></i> Modo Somente E-mail Ativo — Crie Sua Própria Instância para Cobrar via WhatsApp
                                </div>
                                <div class="small text-body">
                                    {{ $permissaoWhatsapp['message'] ?? 'A instância atual de configuração (foco7) é exclusiva para configuração técnica. O Administrador deve criar e ativar sua própria instância através deste painel antes de realizar cobranças via WhatsApp; fora isso, só é possível cobrar clientes via E-mail.' }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if(!($permissaoWhatsapp['permitido'] ?? false))
                            @if(!empty($permissaoWhatsapp['instancia_propria']['nome']))
                                <button type="button" class="btn btn-warning btn-sm fw-bold text-dark shadow-sm" onclick="abrirCheckoutPixInstancia('{{ $permissaoWhatsapp['instancia_propria']['nome'] }}')">
                                    <i class="fab fa-pix me-1"></i> Pagar PIX de '{{ $permissaoWhatsapp['instancia_propria']['nome'] }}' p/ Ativar WhatsApp
                                </button>
                            @else
                                <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarInstancia">
                                    <i class="fas fa-plus-circle me-1"></i> Criar Minha Própria Instância Agora
                                </button>
                            @endif
                        @endif
                        <button type="button" class="btn btn-outline-dark btn-sm fw-semibold" onclick="abrirAbaMpDev()">
                            <i class="fas fa-hand-holding-dollar me-1"></i> Módulo Mercado Pago (Dev)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================
         CARDS DE MÉTRICAS & KPIS GERAIS (4 COLUNAS)
         =================================================================== -->
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Servidor Evolution Go</span>
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="fas fa-server fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-1 text-body font-monospace" style="font-size: 1.15rem;">api.kdkhost.com.br</h4>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary"><i class="fas fa-code-branch me-1"></i> Versão Go v3.0</span>
                    <a href="https://api.kdkhost.com.br/docs/llms.txt" target="_blank" class="fw-bold text-primary">Swagger / LLMs &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Instâncias no Cluster</span>
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="fas fa-layer-group fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-body">{{ ($totalInstanciasExtras ?? 0) + 1 }}</h3>
                    <small class="text-secondary">({{ $instanciasAtivas ?? 1 }} ativas / {{ $instanciasSuspensas ?? 0 }} pendentes)</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-primary fw-semibold"><i class="fas fa-sliders me-1"></i> 1 Config ({{ $instanciaConfiguracao['nome'] ?? 'foco7' }})</span>
                    <span class="badge bg-danger">{{ $totalInstanciasExtras ?? 0 }} Criadas</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card shadow-sm h-100 bg-body border-warning border-opacity-50">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Recorrência PIX Mensal</span>
                    <div class="p-2 bg-warning bg-opacity-15 text-warning rounded-circle">
                        <i class="fab fa-pix fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    @if($configs['dev_desbloqueado'])
                        <h3 class="fw-bold mb-0 text-danger">R$ {{ number_format($custoTotalRecorrente ?? 0, 2, ',', '.') }}</h3>
                        <small class="text-secondary">/ mês</small>
                    @else
                        <h3 class="fw-bold mb-0 text-secondary font-monospace"><span class="badge bg-secondary-subtle text-secondary fs-5" title="Protegido por Senha Master"><i class="fas fa-lock me-1"></i> ••••••</span></h3>
                        <small class="text-secondary">(Protegido)</small>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary">
                        <i class="fas fa-coins me-1"></i>
                        @if($configs['dev_desbloqueado'])
                            R$ {{ number_format($custoInstanciaExtra ?? 60.00, 2, ',', '.') }}/instância
                        @else
                            R$ •••••• / instância
                        @endif
                    </span>
                    <span class="badge {{ ($configs['suspensao_automatica'] ?? true) ? 'bg-success' : 'bg-secondary' }}">
                        {{ ($configs['suspensao_automatica'] ?? true) ? 'Auto-Suspensão Ativa' : 'Suspensão Manual' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Disparos de Cobrança</span>
                    <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                        <i class="fas fa-paper-plane fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-body">{{ $totalCobrancasWpp ?? 0 }}</h3>
                    <small class="text-secondary">mensagens enviadas</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary"><i class="fas fa-clock-rotate-left me-1"></i> Canal Ativo:</span>
                    @if($permissaoWhatsapp['permitido'] ?? false)
                        <span class="badge bg-success-subtle text-success"><i class="fab fa-whatsapp me-1"></i> WhatsApp + E-mail</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis"><i class="fas fa-envelope me-1"></i> Somente E-mail</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================
         NAVEGAÇÃO POR GUIAS (TABS) PRINCIPAL
         =================================================================== -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="whatsappTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-conexao-btn" data-bs-toggle="tab" data-bs-target="#tab-conexao" type="button" role="tab">
                            <i class="fas fa-network-wired text-primary me-2"></i> Conexão &amp; Credenciais
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-instancias-btn" data-bs-toggle="tab" data-bs-target="#tab-instancias" type="button" role="tab">
                            <i class="fas fa-layer-group text-warning me-2"></i> Instâncias &amp; Cobrança PIX
                            <span class="badge bg-danger ms-1">R$ 60/mês</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-mp-dev-btn" data-bs-toggle="tab" data-bs-target="#tab-mp-dev" type="button" role="tab">
                            <i class="fas fa-hand-holding-dollar text-primary me-2"></i> Mercado Pago (Desenvolvedor)
                            <span class="badge bg-dark ms-1"><i class="fas fa-lock text-warning me-1"></i> Master</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-pix-btn" data-bs-toggle="tab" data-bs-target="#tab-pix" type="button" role="tab">
                            <i class="fab fa-pix text-success me-2"></i> Cobrança PIX de Clientes
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-regua-btn" data-bs-toggle="tab" data-bs-target="#tab-regua" type="button" role="tab">
                            <i class="fas fa-clock-rotate-left text-danger me-2"></i> Régua de Cobrança
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-templates-btn" data-bs-toggle="tab" data-bs-target="#tab-templates" type="button" role="tab">
                            <i class="fas fa-mobile-screen-button text-info me-2"></i> Templates &amp; Smartphone
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-teste-btn" data-bs-toggle="tab" data-bs-target="#tab-teste" type="button" role="tab">
                            <i class="fas fa-vial text-warning me-2"></i> Disparo de Teste
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-webhook-btn" data-bs-toggle="tab" data-bs-target="#tab-webhook" type="button" role="tab">
                            <i class="fas fa-satellite-dish text-secondary me-2"></i> Webhook &amp; Eventos
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.whatsapp.salvar') }}" method="POST" id="formWhatsappConfig" class="ajax-form keep-data">
                    @csrf

                    <div class="tab-content" id="whatsappTabsContent">

                        <!-- ===================================================================
                             GUIA 1: CONEXÃO & CREDENCIAIS (CRIPTOGRAFIA TOTAL NO BANCO)
                             =================================================================== -->
                        <div class="tab-pane fade show active" id="tab-conexao" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="fw-bold text-body mb-0"><i class="fas fa-key text-primary me-2"></i> Parâmetros da Evolution API Go (v3.0)</h5>
                                        <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} px-3 py-1 fs-6" id="badgeStatusSegurancaWpp">
                                            <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }} me-1"></i>
                                            <span id="badgeTextoSegurancaWpp">{{ $configs['dev_desbloqueado'] ? 'Acesso Liberado' : 'Protegido por Senha Master' }}</span>
                                        </span>
                                    </div>
                                    <p class="text-secondary small mb-0">Todas as credenciais e tokens são gravados criptografados com AES-256 no banco de dados.</p>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <button type="button" class="btn btn-sm {{ $configs['dev_desbloqueado'] ? 'btn-outline-secondary' : 'btn-danger' }} fw-bold shadow-sm" id="btnAcaoSegurancaWpp" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}">
                                        <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }} me-1"></i>
                                        <span id="btnTextoSegurancaWpp">{{ $configs['dev_desbloqueado'] ? 'Bloquear Acesso' : 'Desbloquear com Senha' }}</span>
                                    </button>
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" name="ativo" id="wpp_ativo" value="1" {{ $configs['ativo'] ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6 fw-bold" for="wpp_ativo">WhatsApp Ativo no Sistema</label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="dev_password" id="wpp_dev_password_hidden" value="">

                            <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-center gap-2 {{ $configs['dev_desbloqueado'] ? 'd-none' : '' }}" id="alertaTarjaWpp">
                                <i class="fas fa-shield-alt text-warning fa-lg"></i>
                                <div>Os parâmetros de conexão e dados de cobrança do cluster estão protegidos por segurança. Para visualizar ou alterar, confirme a <strong>Senha Master Exclusiva</strong>.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">URL Base do Servidor Evolution API <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        <input type="{{ $configs['dev_desbloqueado'] ? 'url' : 'text' }}" name="api_url" id="inputApiUrl" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['api_url'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} required>
                                    </div>
                                    <div class="form-text small">Servidor oficial: <code>https://api.kdkhost.com.br</code> (Sem barra no final).</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome da Instância Principal <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-robot"></i></span>
                                        <input type="text" name="instance" id="inputInstance" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['instance'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="listarInstanciasServidor()" title="Buscar instâncias no servidor">
                                            <i class="fas fa-list me-1"></i> Ver no Servidor
                                        </button>
                                    </div>
                                    <div class="form-text small">Identificador único da instância principal da empresa. Ex: <code>foco7</code>.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Token da Instância (Instance API Key / Sanctum Token) <span class="badge bg-warning text-dark small ms-1"><i class="fas fa-lock"></i> Criptografado</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                        <input type="password" name="apikey" id="inputApiKey" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['apikey'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputApiKey', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small">Enviado no header <code>Authorization: Bearer &lt;token&gt;</code> e <code>apikey</code>.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Global API Key (Chave Mestra Admin) <span class="badge bg-warning text-dark small ms-1"><i class="fas fa-lock"></i> Criptografado</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                        <input type="password" name="global_apikey" id="inputGlobalApiKey" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['global_apikey'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputGlobalApiKey', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small">Necessário para criar, listar e gerenciar instâncias de licenciados automaticamente.</div>
                                </div>

                                <!-- BLOCO DE INFORMAÇÕES DO FRANQUEADO NA CONEXÃO DA INSTÂNCIA ATIVA -->
                                <div class="col-12 mt-4">
                                    <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 shadow-none">
                                        <div class="card-body p-3">
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom border-primary border-opacity-25">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary"><i class="fas fa-user-tie me-1"></i> Identificação do Franqueado na Conexão</span>
                                                    <strong class="text-body" id="txtCardFranqueadoAtivoTitulo">
                                                        {{ $franqueadoAtivoInfo['empresa_franqueado'] ?? 'Foco 7 Rastreamento' }}
                                                    </strong>
                                                    <span class="badge bg-secondary-subtle text-secondary font-monospace" id="txtCardFranqueadoAtivoInstancia">
                                                        Instância: {{ $franqueadoAtivoInfo['instancia'] ?? ($instanciaConfiguracao['nome'] ?? 'foco7') }}
                                                    </span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="abrirModalEditarFranqueadoInstancia('{{ $franqueadoAtivoInfo['instancia'] ?? ($instanciaConfiguracao['nome'] ?? 'foco7') }}')">
                                                        <i class="fas fa-user-pen me-1"></i> Editar Dados do Franqueado
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="listarInstanciasServidor()">
                                                        <i class="fas fa-layer-group me-1"></i> Ver Instâncias Criadas neste Painel
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-md-3">
                                                    <span class="text-secondary d-block">Franqueado / Responsável:</span>
                                                    <strong class="text-body" id="txtCardFranqueadoAtivoNome">{{ $franqueadoAtivoInfo['nome_franqueado'] ?? 'Administrador / Franqueado' }}</strong>
                                                    @if(!empty($franqueadoAtivoInfo['documento_franqueado']))
                                                        <span class="text-muted font-monospace d-block">{{ $franqueadoAtivoInfo['documento_franqueado'] }}</span>
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-secondary d-block">Chave de Licença / Vínculo:</span>
                                                    <strong class="font-monospace text-primary" id="txtCardFranqueadoAtivoChave">{{ $franqueadoAtivoInfo['chave_licenca'] ?: ($franqueadoAtivoInfo['tipo_label'] ?? 'Franqueado Local') }}</strong>
                                                    <span class="text-muted d-block" id="txtCardFranqueadoAtivoDominio"><i class="fas fa-globe me-1"></i> {{ $franqueadoAtivoInfo['dominio_franqueado'] ?: 'foco7.com.br' }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-secondary d-block">Contato do Franqueado:</span>
                                                    <strong class="text-body font-monospace" id="txtCardFranqueadoAtivoTel"><i class="fab fa-whatsapp text-success me-1"></i> {{ $franqueadoAtivoInfo['telefone_franqueado'] ?: 'Não informado' }}</strong>
                                                    <span class="text-muted d-block text-truncate" id="txtCardFranqueadoAtivoEmail"><i class="fas fa-envelope me-1"></i> {{ $franqueadoAtivoInfo['email_franqueado'] ?: 'Não informado' }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-secondary d-block">Aparelho Conectado &amp; Vigência:</span>
                                                    <strong class="font-monospace text-success" id="txtCardFranqueadoAtivoOwner">
                                                        <i class="fas fa-mobile-screen-button me-1"></i> {{ !empty($numeroConectado) ? $numeroConectado : 'Aguardando leitura QR/Número' }}
                                                    </strong>
                                                    <span class="text-muted d-block">Vencimento: <strong>{{ $franqueadoAtivoInfo['data_vencimento_fmt'] ?? date('d/m/Y', strtotime('+30 days')) }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 2: GESTÃO DE INSTÂNCIAS EXTRAS & RECORRÊNCIA PIX (R$ 60/MÊS)
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-instancias" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <div>
                                    <h5 class="fw-bold text-body mb-1">
                                        <i class="fas fa-layer-group text-warning me-2"></i> Cobrança Recorrente de Instâncias (Master &amp; Extras)
                                    </h5>
                                    <p class="text-secondary small mb-0">
                                        Recebimento via PIX Transparente 
                                        @if($configs['dev_desbloqueado'])
                                            na conta <strong class="text-primary font-monospace" id="txtGuiaPixRecorrencia">{{ $configs['pix_email_recorrencia'] }}</strong>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary font-monospace" id="txtGuiaPixRecorrencia" title="Clique para desbloquear com a Senha Master" style="cursor: pointer;" onclick="solicitarSenhaSegurancaWpp()"><i class="fas fa-lock me-1"></i> ••••••••••••••• (Protegido por Senha Master)</span>
                                        @endif
                                        com suspensão automática no vencimento.
                                    </p>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-warning btn-sm fw-bold" onclick="executarCobrancaPreventivaWpp()" title="Disparar lembrete e código PIX para instâncias com vencimento próximo">
                                        <i class="fas fa-paper-plane me-1"></i> Disparar Régua Preventiva
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm fw-semibold" onclick="sincronizarPagamentosMpWpp()" title="Verificar na API do Mercado Pago faturas pagas e reativar instâncias">
                                        <i class="fab fa-pix me-1"></i> Sincronizar MP
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold" onclick="processarVarreduraVencidos()">
                                        <i class="fas fa-gavel me-1"></i> Verificar &amp; Suspender Vencidos
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="listarInstanciasServidor()">
                                        <i class="fas fa-rotate me-1"></i> Sincronizar Servidor
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarInstancia">
                                        <i class="fas fa-plus-circle me-1"></i> Nova Instância (+R$ 60/mês)
                                    </button>
                                </div>
                            </div>

                            <!-- PARÂMETROS DA RECORRÊNCIA PIX -->
                            <div class="card bg-body-tertiary border mb-4 shadow-none">
                                <div class="card-body p-3">
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold mb-1">
                                                Chave PIX / Conta de Recebimento das Instâncias Extras
                                                <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} small ms-1" id="badgePixRecorrenciaLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }}"></i> {{ $configs['dev_desbloqueado'] ? 'Liberado' : 'Protegido Master' }}
                                                </span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-envelope text-primary"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'email' : 'password' }}" name="pix_email_recorrencia" id="inputPixEmailRecorrencia" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['pix_email_recorrencia'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="••••••••••••••••••••••••">
                                                <button class="btn btn-outline-secondary" type="button" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}" title="{{ $configs['dev_desbloqueado'] ? 'Bloquear e Ofuscar' : 'Desbloquear com Senha Master' }}" id="btnPixRecorrenciaLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }}"></i>
                                                </button>
                                            </div>
                                            <div class="form-text small">Checkout transparente: o parceiro ou licenciado realiza o pagamento sem visualizar dados privados da conta.</div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between align-items-center">
                                                <span>Valor por Instância (R$)</span>
                                                <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} small" id="badgeCustoExtraLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }}"></i> {{ $configs['dev_desbloqueado'] ? 'Liberado' : 'Protegido Master' }}
                                                </span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">R$</span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'number' : 'password' }}" step="0.01" name="custo_instancia_extra" id="inputCustoInstanciaExtra" class="form-control fw-bold font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado text-secondary' : 'text-danger' }}" value="{{ $configs['dev_desbloqueado'] ? number_format($custoInstanciaExtra ?? 60.00, 2, '.', '') : '••••••' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}" title="{{ $configs['dev_desbloqueado'] ? 'Bloquear e Ofuscar' : 'Desbloquear com Senha Master' }}" id="btnCustoExtraLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }}"></i>
                                                </button>
                                            </div>
                                            <div class="form-text small">Recorrência mensal (Master e Adicionais).</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold mb-1">Política de Suspensão no Vencimento</label>
                                            <div class="form-check form-switch fs-6 mb-0">
                                                <input class="form-check-input" type="checkbox" name="suspensao_automatica" id="checkSuspensaoAuto" value="1" {{ ($configs['suspensao_automatica'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold" for="checkSuspensaoAuto">
                                                    Suspender Automaticamente no Vencimento
                                                </label>
                                            </div>
                                            <div class="form-text small">Com carência protetiva de 24h para não cortar clientes de surpresa.</div>
                                        </div>
                                    </div>

                                    <!-- LINHA DO ACCESS TOKEN MERCADO PAGO EXCLUSIVO PARA INSTÂNCIAS -->
                                    <div class="row g-3 align-items-center pt-2 border-top">
                                        <div class="col-md-8">
                                            <label class="form-label small fw-bold mb-1 d-flex justify-content-between align-items-center">
                                                <span>Access Token Mercado Pago (Recebimento Direto na sua Conta)</span>
                                                <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} small" id="badgeMpTokenLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }}"></i> {{ $configs['dev_desbloqueado'] ? 'Liberado' : 'Protegido Master' }}
                                                </span>
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fab fa-pix text-warning"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="evolution_mp_access_token" id="inputEvolutionMpAccessToken" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['evolution_mp_access_token'] }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="APP_USR-...">
                                                <button class="btn btn-outline-secondary" type="button" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}" title="{{ $configs['dev_desbloqueado'] ? 'Bloquear e Ofuscar' : 'Desbloquear com Senha Master' }}" id="btnMpTokenLock">
                                                    <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }}"></i>
                                                </button>
                                            </div>
                                            <div class="form-text small">O dinheiro das instâncias cai direto na sua conta do Mercado Pago sem passar por painel de terceiros.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2 border rounded bg-body-tertiary">
                                                <div class="small fw-bold text-success mb-1"><i class="fas fa-bolt me-1"></i> Automação e Reativação em Segundos</div>
                                                <div class="small text-secondary" style="font-size: 0.75rem;">
                                                    Ao pagar via PIX, o Mercado Pago notifica o webhook e a instância é reativada instantaneamente no servidor.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TABELA DE INSTÂNCIAS REGISTRADAS -->
                            <div class="card border">
                                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-body">
                                            <i class="fas fa-list me-2 text-primary"></i> Instâncias Provisionadas deste Painel &amp; Monitoramento de Vencimentos
                                        </h6>
                                        <small class="text-secondary" style="font-size: 0.74rem;">
                                            <i class="fas fa-shield-halved text-success me-1"></i> Isolamento Estrito Ativo: cada licenciado visualiza exclusivamente suas próprias instâncias criadas neste painel.
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-user-lock me-1"></i> Zero Vazamento entre Franqueados</span>
                                        <span class="badge bg-secondary">Total: {{ ($totalInstanciasExtras ?? 0) + 1 }} instâncias deste painel</span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Identificador / Nome</th>
                                                <th>Proprietário / Tipo</th>
                                                <th>Vencimento / Próxima Cobrança</th>
                                                <th>Mensalidade</th>
                                                <th>Status da Conexão</th>
                                                <th class="text-end">Ações de Gestão &amp; PIX</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- INSTÂNCIA ATUAL DE CONFIGURAÇÃO (TAMBÉM COBRADA PELO MÓDULO MERCADO PAGO DO DESENVOLVEDOR) -->
                                            @php
                                                $nomeInstConfig = $instanciaConfiguracao['nome'] ?? 'foco7';
                                                $vencInstConfig = $instanciaConfiguracao['data_vencimento'] ?? date('Y-m-d', strtotime('+30 days'));
                                                $stInstConfig = $instanciaConfiguracao['status'] ?? 'ativa';
                                                $hashInstConfig = $instanciaConfiguracao['hash_pagamento'] ?? md5($nomeInstConfig.'wpp_sec');
                                            @endphp
                                            <tr class="table-light">
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="{{ $stInstConfig === 'suspensa' ? 'pulse-offline' : 'pulse-online' }}"></span>
                                                        @if($configs['dev_desbloqueado'])
                                                            <strong class="font-monospace text-primary fs-6">{{ $nomeInstConfig }}</strong>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary font-monospace" title="Protegido por Senha Master"><i class="fas fa-lock me-1"></i> ••••••••••••</span>
                                                        @endif
                                                        <span class="badge bg-dark ms-1"><i class="fas fa-sliders text-warning me-1"></i> Instância de Configuração</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-dark mb-1"><i class="fas fa-crown text-warning me-1"></i> {{ $instanciaConfiguracao['empresa_franqueado'] ?? 'Desenvolvedor / Config' }}</span>
                                                    <div class="small text-secondary">
                                                        <div><i class="fas fa-user-tie me-1 text-primary"></i> <strong>{{ $instanciaConfiguracao['nome_responsavel'] ?? 'Foco 7 Tecnologia' }}</strong></div>
                                                        @if(!empty($instanciaConfiguracao['dominio_franqueado']))
                                                            <div class="font-monospace text-muted" style="font-size: 0.72rem;"><i class="fas fa-globe me-1"></i>{{ $instanciaConfiguracao['dominio_franqueado'] }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($configs['dev_desbloqueado'])
                                                        <strong>{{ date('d/m/Y', strtotime($vencInstConfig)) }}</strong>
                                                        <small class="text-secondary d-block">Cobrança Recorrente MP Dev</small>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary font-monospace" title="Protegido por Senha Master"><i class="fas fa-lock me-1"></i> ••••••••••••</span>
                                                        <small class="text-secondary d-block">Cobrança Ativa (MP Dev)</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($configs['dev_desbloqueado'])
                                                        <strong class="text-danger">R$ {{ number_format($custoInstanciaExtra ?? 60.00, 2, ',', '.') }}</strong>
                                                        <small class="text-muted">/ mês</small>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary font-monospace" title="Protegido por Senha Master"><i class="fas fa-lock me-1"></i> ••••••</span>
                                                        <small class="text-muted">(Protegido)</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $statusCache === 'conectado' ? 'bg-success' : 'bg-secondary' }}" id="badgeStatusPrincipalTabela">
                                                        <span class="{{ $statusCache === 'conectado' ? 'pulse-online' : 'pulse-offline' }} me-1" id="pulseStatusPrincipalTabela"></span>
                                                        <span id="txtStatusPrincipalTabela">{{ $statusCache === 'conectado' ? 'Conectado' : ucfirst($statusCache) }}</span>
                                                    </span>
                                                    <small class="text-secondary d-block mt-1 font-monospace {{ empty($numeroConectado) ? 'd-none' : '' }}" id="txtOwnerPrincipalTabela">{{ $numeroConectado }}</small>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-warning fw-bold text-dark" onclick="abrirCheckoutPixInstancia('{{ $nomeInstConfig }}')" title="Cobrar PIX desta instância no Módulo Mercado Pago do Desenvolvedor">
                                                            <i class="fab fa-pix me-1"></i> Cobrar PIX
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" onclick="copiarLinkPagamentoInstancia('{{ url('/pagar/instancia/' . $hashInstConfig) }}')" title="Copiar link direto de pagamento público">
                                                            <i class="fas fa-link"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success" onclick="confirmarPagamentoManualInstancia('{{ $nomeInstConfig }}')" title="Confirmar pagamento recebido manualmente (Restrito Senha Master)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @if($stInstConfig === 'suspensa')
                                                            <button type="button" class="btn btn-outline-primary" onclick="reativarInstanciaManual('{{ $nomeInstConfig }}')" title="Reativar instância (Restrito Master)">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-outline-danger" onclick="suspenderInstanciaManual('{{ $nomeInstConfig }}')" title="Suspender instância (Restrito Master)">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-outline-primary" onclick="abrirModalEditarFranqueadoInstancia('{{ $nomeInstConfig }}')" title="Ver / Editar informações do Franqueado na conexão desta instância">
                                                            <i class="fas fa-user-tie"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-success fw-semibold" onclick="conectarEabrirQr('{{ $nomeInstConfig }}')" title="Conectar via QR Code ou Número de Telefone">
                                                            <i class="fas fa-qrcode me-1"></i> Conectar
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="desconectarInstancia('{{ $nomeInstConfig }}')" title="Desconectar aparelho e limpar tudo desta instância">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- INSTÂNCIAS CRIADAS ATRAVÉS DESTE PAINEL (PRÓPRIA DO ADMIN, EMPRESA E LICENCIADOS) -->
                                            @if(!empty($instanciasExtras) && is_array($instanciasExtras))
                                                @foreach($instanciasExtras as $extra)
                                                @php
                                                    $st = $extra['status'] ?? 'ativa';
                                                    $venc = $extra['data_vencimento'] ?? date('Y-m-d', strtotime('+30 days'));
                                                    $isVencida = ($venc < date('Y-m-d')) || $st === 'pendente_pagamento';
                                                    $diasRestantes = (int) round((strtotime($venc) - strtotime('today')) / 86400);
                                                @endphp
                                                <tr class="{{ $st === 'suspensa' ? 'table-danger table-opacity-10' : ($isVencida ? 'table-warning table-opacity-10' : '') }}">
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="{{ $st === 'suspensa' ? 'pulse-offline' : ($isVencida ? 'pulse-warning' : 'pulse-online') }}"></span>
                                                            <strong class="font-monospace text-body fs-6">{{ $extra['nome'] ?? 'N/D' }}</strong>
                                                            @if(strtolower($extra['nome'] ?? '') === strtolower($configs['instance'] ?? ''))
                                                                <span class="badge bg-success-subtle text-success border border-success small">Em Uso no Painel</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if(($extra['tipo'] ?? '') === 'licenciado')
                                                            <span class="badge bg-primary mb-1"><i class="fas fa-id-badge me-1"></i> Franqueado / Licenciado {{ !empty($extra['licenciado_id']) ? '#'.$extra['licenciado_id'] : '' }}</span>
                                                        @elseif(($extra['tipo'] ?? '') === 'propria_admin')
                                                            <span class="badge bg-success mb-1"><i class="fas fa-user-check me-1"></i> Própria do Admin / Franqueado</span>
                                                        @else
                                                            <span class="badge bg-secondary mb-1"><i class="fas fa-building me-1"></i> Unidade / Empresa Franqueada</span>
                                                        @endif
                                                        <div class="small text-secondary">
                                                            <div><i class="fas fa-user-tie me-1 text-primary"></i> <strong>{{ $extra['nome_responsavel'] ?? 'Franqueado' }}</strong> {{ !empty($extra['empresa_franqueado']) && $extra['empresa_franqueado'] !== ($extra['nome_responsavel'] ?? '') ? '• '.$extra['empresa_franqueado'] : '' }}</div>
                                                            @if(!empty($extra['telefone_responsavel']) || !empty($extra['email_responsavel']))
                                                                <div class="text-muted" style="font-size: 0.73rem;">
                                                                    @if(!empty($extra['telefone_responsavel']))
                                                                        <span class="me-2"><i class="fab fa-whatsapp text-success me-1"></i>{{ $extra['telefone_responsavel'] }}</span>
                                                                    @endif
                                                                    @if(!empty($extra['email_responsavel']))
                                                                        <span><i class="fas fa-envelope me-1"></i>{{ $extra['email_responsavel'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            @if(!empty($extra['chave_licenca']) || !empty($extra['dominio_franqueado']))
                                                                <div class="font-monospace text-muted" style="font-size: 0.72rem;">
                                                                    @if(!empty($extra['chave_licenca']))
                                                                        <span class="me-2"><i class="fas fa-key text-warning me-1"></i>{{ $extra['chave_licenca'] }}</span>
                                                                    @endif
                                                                    @if(!empty($extra['dominio_franqueado']))
                                                                        <span><i class="fas fa-globe me-1"></i>{{ $extra['dominio_franqueado'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            @if($st === 'pendente_pagamento')
                                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Aguardando 1º Pagamento PIX</span>
                                                                <small class="text-danger d-block fw-semibold">Pague p/ liberar WhatsApp</small>
                                                            @else
                                                                <strong class="{{ $isVencida ? 'text-danger' : 'text-body' }}">
                                                                    {{ date('d/m/Y', strtotime($venc)) }}
                                                                </strong>
                                                                @if($isVencida)
                                                                    <span class="badge bg-danger ms-1">Vencida (há {{ abs($diasRestantes) }}d)</span>
                                                                @else
                                                                    <small class="text-secondary d-block">Vence em {{ $diasRestantes }} dias</small>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($configs['dev_desbloqueado'])
                                                            <strong class="text-danger">R$ {{ number_format($extra['valor_recorrente'] ?? 60.00, 2, ',', '.') }}</strong>
                                                            <small class="text-muted">/ mês</small>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary font-monospace" title="Protegido por Senha Master"><i class="fas fa-lock me-1"></i> ••••••</span>
                                                            <small class="text-muted">(Protegido)</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($st === 'suspensa')
                                                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Suspensa</span>
                                                        @elseif($st === 'pendente_pagamento')
                                                            <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Pendente PIX</span>
                                                        @elseif($isVencida)
                                                            <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Aguardando PIX</span>
                                                        @else
                                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Ativa &amp; Liberada</span>
                                                        @endif
                                                        @if(!empty($extra['owner']))
                                                            <small class="text-secondary d-block mt-1 font-monospace owner-extra-label" data-instancia="{{ $extra['nome'] }}">{{ $extra['owner'] }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-warning fw-bold text-dark" onclick="abrirCheckoutPixInstancia('{{ $extra['nome'] }}')" title="Pagar / Gerar cobrança PIX no Módulo Mercado Pago do Desenvolvedor">
                                                                <i class="fab fa-pix me-1"></i> {{ $st === 'pendente_pagamento' ? 'Pagar p/ Ativar' : 'Cobrar PIX' }}
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info" onclick="copiarLinkPagamentoInstancia('{{ url('/pagar/instancia/' . ($extra['hash_pagamento'] ?? $extra['nome'])) }}')" title="Copiar link direto de pagamento público">
                                                                <i class="fas fa-link"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-success" onclick="confirmarPagamentoManualInstancia('{{ $extra['nome'] }}')" title="Confirmar pagamento manual (Restrito Senha Master)">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            @if(in_array($st, ['suspensa', 'pendente_pagamento']))
                                                                <button type="button" class="btn btn-outline-primary" onclick="reativarInstanciaManual('{{ $extra['nome'] }}')" title="Reativar instância (Restrito Senha Master)">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-outline-danger" onclick="suspenderInstanciaManual('{{ $extra['nome'] }}')" title="Suspender e cortar conexão (Restrito Senha Master)">
                                                                    <i class="fas fa-pause"></i>
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-outline-primary" onclick="abrirModalEditarFranqueadoInstancia('{{ $extra['nome'] }}')" title="Ver / Editar informações do Franqueado na conexão desta instância">
                                                                <i class="fas fa-user-tie"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success fw-semibold" onclick="conectarEabrirQr('{{ $extra['nome'] }}')" title="Conectar via QR Code ou Número de Telefone">
                                                                <i class="fas fa-qrcode me-1"></i> Conectar
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" onclick="desconectarInstancia('{{ $extra['nome'] }}')" title="Desconectar aparelho e limpar tudo desta instância">
                                                                <i class="fas fa-power-off"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" onclick="selecionarInstanciaServidor('{{ $extra['nome'] }}', '{{ $extra['token'] ?? '' }}')" title="Usar esta instância no painel">
                                                                <i class="fas fa-arrow-right"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" onclick="excluirInstanciaExtra('{{ $extra['nome'] }}')" title="Excluir monitoramento (Restrito Senha Master)">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 2.5: SEGUNDO MÓDULO MERCADO PAGO EM PARALELO (EXCLUSIVO DO DESENVOLVEDOR)
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-mp-dev" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="fw-bold text-body mb-0">
                                            <i class="fas fa-hand-holding-dollar text-primary me-2"></i> Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor)
                                        </h5>
                                        <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} px-3 py-1">
                                            <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }} me-1"></i>
                                            {{ $configs['dev_desbloqueado'] ? 'Desbloqueado pelo Master' : 'Ofuscado • Restrito Master' }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                            <i class="fab fa-pix me-1"></i> Cobrança via PIX Apenas
                                        </span>
                                        <span class="badge bg-info-subtle text-info border border-info px-2 py-1">
                                            <i class="fas fa-bolt me-1"></i> Webhook Dinâmico por Requisição
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                            <i class="fas fa-rotate me-1"></i> Retorno Automático Transparente
                                        </span>
                                    </div>
                                    <p class="text-secondary small mb-0">
                                        Módulo em paralelo 100% independente do gateway da empresa: cobra <strong>exclusivamente via PIX Transparente</strong> as instâncias criadas no Evolution Go através deste painel (inclusive a atual de configuração), com <strong>webhook dinâmico a cada requisição</strong> e <strong>retorno automático</strong>.
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold" id="btnTestarMpDev" onclick="testarConexaoMpDev()">
                                        <i class="fas fa-plug-circle-check me-1"></i> Testar Conexão MP (<span id="txtBtnAmbienteMpDev">{{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'Sandbox' : 'Produção' }}</span>)
                                    </button>
                                    <button type="button" class="btn btn-sm {{ $configs['dev_desbloqueado'] ? 'btn-outline-secondary' : 'btn-danger' }} fw-bold" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}">
                                        <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }} me-1"></i>
                                        {{ $configs['dev_desbloqueado'] ? 'Bloquear & Ofuscar' : 'Desbloquear c/ Senha Master' }}
                                    </button>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold small text-uppercase text-secondary">Status &amp; Método Exclusivo</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="dev_mp_ativo" id="inputDevMpAtivo" value="1" {{ ($configs['dev_mp_ativo'] ?? true) ? 'checked' : '' }} {{ !$configs['dev_desbloqueado'] ? 'disabled' : '' }}>
                                                <label class="form-check-label small fw-bold" for="inputDevMpAtivo">Módulo Ativo</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-success"><i class="fab fa-pix me-1"></i> Somente PIX</span>
                                            <span class="badge bg-dark-subtle text-body border">Checkout Transparente Íntegro</span>
                                        </div>
                                        <div class="small text-secondary">
                                            Opera isolado de <code>/admin/gateways</code> exclusivamente via PIX com retorno automático após compensação.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label small fw-bold text-uppercase text-secondary mb-0">Ambiente Dinâmico (Dev)</label>
                                            <span class="badge {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'bg-warning text-dark' : 'bg-success' }}" id="badgeAmbienteAtivoMpDev">
                                                <i class="fas {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'fa-flask' : 'fa-check-circle' }} me-1"></i>
                                                {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'Modo Sandbox Ativo' : 'Modo Produção Ativo' }}
                                            </span>
                                        </div>
                                        <select name="dev_mp_ambiente" id="inputDevMpAmbiente" class="form-select form-select-sm fw-bold mb-2" onchange="alternarAmbienteMpDev(this.value)" {{ !$configs['dev_desbloqueado'] ? 'disabled' : '' }}>
                                            <option value="producao" {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'producao' ? 'selected' : '' }}>Produção (Recebimento Real PIX)</option>
                                            <option value="sandbox" {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'selected' : '' }}>Sandbox (Homologação / Testes PIX)</option>
                                        </select>
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            <button type="button" class="btn {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'producao' ? 'btn-success' : 'btn-outline-secondary' }} fw-semibold" id="btnTabAmbProd" onclick="selecionarAmbienteMpDevClick('producao')">
                                                <i class="fas fa-shield-halved me-1"></i> Produção
                                            </button>
                                            <button type="button" class="btn {{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'btn-warning text-dark' : 'btn-outline-secondary' }} fw-semibold" id="btnTabAmbSandbox" onclick="selecionarAmbienteMpDevClick('sandbox')">
                                                <i class="fas fa-flask me-1"></i> Sandbox
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                        <label class="form-label small fw-bold text-uppercase text-secondary mb-1">Titular / Beneficiário (EMV PIX)</label>
                                        <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="dev_mp_beneficiario" id="inputDevMpBeneficiario" class="form-control form-control-sm font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_beneficiario'] ?? 'FOCO 7 TECNOLOGIA' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                        <div class="form-text small">Identificação exibida no checkout transparente de ativação de instâncias.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOCO DINÂMICO 1: CREDENCIAIS DE PRODUÇÃO DO DESENVOLVEDOR -->
                            <div class="card border border-success border-opacity-50 mb-4 shadow-none" id="boxCredenciaisMpDevProd" style="{{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? 'display: none;' : '' }}">
                                <div class="card-header bg-success bg-opacity-10 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="fw-bold small text-body">
                                        <i class="fas fa-shield-halved text-success me-2"></i> Credenciais Dinâmicas de PRODUÇÃO — Conta Mercado Pago do Desenvolvedor
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success"><i class="fab fa-pix me-1"></i> PIX Produção Ativo</span>
                                        <span class="badge bg-success-subtle text-success border border-success small">Criptografia AES-256</span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Public Key (Produção - Desenvolvedor)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-key text-success"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="dev_mp_public_key_prod" id="inputDevMpPublicKeyProd" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_public_key_prod'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="APP_USR-...">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpPublicKeyProd', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Access Token (Produção - Desenvolvedor) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fab fa-pix text-success"></i></span>
                                                <input type="password" name="dev_mp_access_token_prod" id="inputDevMpAccessTokenProd" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_access_token_prod'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="APP_USR-...">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpAccessTokenProd', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Client ID (Produção - Opcional)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="dev_mp_client_id_prod" id="inputDevMpClientIdProd" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_client_id_prod'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpClientIdProd', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Client Secret (Produção - Opcional)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-user-secret"></i></span>
                                                <input type="password" name="dev_mp_client_secret_prod" id="inputDevMpClientSecretProd" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_client_secret_prod'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpClientSecretProd', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BLOCO DINÂMICO 2: CREDENCIAIS DE SANDBOX DO DESENVOLVEDOR -->
                            <div class="card border border-warning border-opacity-50 mb-4 shadow-none" id="boxCredenciaisMpDevSandbox" style="{{ ($configs['dev_mp_ambiente'] ?? 'producao') === 'sandbox' ? '' : 'display: none;' }}">
                                <div class="card-header bg-warning bg-opacity-10 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="fw-bold small text-body">
                                        <i class="fas fa-flask text-warning me-2"></i> Credenciais Dinâmicas de SANDBOX (Homologação) — Conta Mercado Pago do Desenvolvedor
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark"><i class="fas fa-vial me-1"></i> PIX Sandbox Ativo</span>
                                        <span class="badge bg-warning-subtle text-warning border border-warning small">Criptografia AES-256</span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Public Key (Sandbox - Desenvolvedor)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-key text-warning"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="dev_mp_public_key_sandbox" id="inputDevMpPublicKeySandbox" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_public_key_sandbox'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="TEST-...">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpPublicKeySandbox', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Access Token (Sandbox - Desenvolvedor) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fab fa-pix text-warning"></i></span>
                                                <input type="password" name="dev_mp_access_token_sandbox" id="inputDevMpAccessTokenSandbox" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_access_token_sandbox'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }} placeholder="TEST-...">
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpAccessTokenSandbox', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Client ID (Sandbox - Opcional)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" name="dev_mp_client_id_sandbox" id="inputDevMpClientIdSandbox" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_client_id_sandbox'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpClientIdSandbox', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Client Secret (Sandbox - Opcional)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-user-secret"></i></span>
                                                <input type="password" name="dev_mp_client_secret_sandbox" id="inputDevMpClientSecretSandbox" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_client_secret_sandbox'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpClientSecretSandbox', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- WEBHOOK DINÂMICO A CADA REQUISIÇÃO & RETORNO AUTOMÁTICO -->
                            <div class="card border shadow-none">
                                <div class="card-header bg-body-secondary py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span class="fw-bold small text-body">
                                        <i class="fas fa-satellite-dish text-info me-2"></i> Webhook Dinâmico a Cada Requisição &amp; Retorno Automático (PIX Transparente)
                                    </span>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-bolt me-1"></i> Injeção Automática em <code>notification_url</code>
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold mb-1">Assinatura Secreta Webhook (Opcional)</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text"><i class="fas fa-shield-virus"></i></span>
                                                <input type="password" name="dev_mp_webhook_secret" id="inputDevMpWebhookSecret" class="form-control font-monospace {{ !$configs['dev_desbloqueado'] ? 'tarja-dev-bloqueado' : '' }}" value="{{ $configs['dev_mp_webhook_secret'] ?? '' }}" {{ !$configs['dev_desbloqueado'] ? 'readonly' : '' }}>
                                                <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputDevMpWebhookSecret', this)"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold mb-1">Endpoint Base do Webhook Dinâmico (Gerado Automaticamente a Cada Requisição PIX)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="inputWebhookMpDevUrl" class="form-control font-monospace" value="{{ $webhookUrlMpDev ?? url('/webhook/mercadopago?origem=instancia_wpp&modulo=mp_dev_paralelo&metodo=pix') }}" readonly>
                                                <button class="btn btn-primary fw-bold" type="button" onclick="copiarWebhookMpDevUrl()">
                                                    <i class="fas fa-copy me-1"></i> Copiar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 small text-secondary">
                                        <i class="fas fa-circle-info text-info me-1"></i>
                                        A cada geração de QR Code PIX, o sistema injeta automaticamente na API do Mercado Pago uma <code>notification_url</code> exclusiva contendo <code>ambiente</code> (<code>producao</code>/<code>sandbox</code>), <code>wpp_instancia</code>, <code>fatura_id</code>, <code>req_token</code> único e <code>ts</code>, garantindo baixa imediata e retorno automático no checkout transparente sem necessidade de configuração manual.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 3: COBRANÇA PIX & FORMATO DOS DISPAROS DE CLIENTES
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-pix" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fab fa-pix text-success me-2"></i> Formato de Cobrança PIX para Faturas de Clientes</h5>
                                <p class="text-secondary small mb-0">Selecione como os clientes da sua empresa ou licenciados receberão as mensagens e botões interativos no WhatsApp.</p>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="card h-100 p-3 formato-card shadow-sm {{ $configs['tipo_envio'] === 'botoes_interativos' ? 'selecionado' : '' }}" onclick="selecionarFormato('botoes_interativos')">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-danger text-white">Recomendado</span>
                                            <input class="form-check-input fs-5" type="radio" name="tipo_envio" value="botoes_interativos" {{ $configs['tipo_envio'] === 'botoes_interativos' ? 'checked' : '' }} onchange="selecionarFormato('botoes_interativos')">
                                        </div>
                                        <h6 class="fw-bold mb-1"><i class="fas fa-arrow-pointer text-primary me-1"></i> Botões Interativos</h6>
                                        <p class="text-secondary small mb-2">Dispara o texto completo com botões nativos clicáveis:</p>
                                        <ul class="text-secondary small mb-0 ps-3">
                                            <li><strong>Botão URL:</strong> Abrir Fatura Online</li>
                                            <li><strong>Botão Copiar:</strong> Copia o código PIX com 1 toque</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card h-100 p-3 formato-card shadow-sm {{ $configs['tipo_envio'] === 'pix_nativo' ? 'selecionado' : '' }}" onclick="selecionarFormato('pix_nativo')">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-success">Nativo WhatsApp</span>
                                            <input class="form-check-input fs-5" type="radio" name="tipo_envio" value="pix_nativo" {{ $configs['tipo_envio'] === 'pix_nativo' ? 'checked' : '' }} onchange="selecionarFormato('pix_nativo')">
                                        </div>
                                        <h6 class="fw-bold mb-1"><i class="fab fa-pix text-success me-1"></i> Botão PIX Nativo (/send/pix)</h6>
                                        <p class="text-secondary small mb-2">Utiliza o endpoint nativo da Evolution Go v3.0 com o botão oficial PIX do WhatsApp.</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card h-100 p-3 formato-card shadow-sm {{ $configs['tipo_envio'] === 'texto' ? 'selecionado' : '' }}" onclick="selecionarFormato('texto')">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-secondary">Universal</span>
                                            <input class="form-check-input fs-5" type="radio" name="tipo_envio" value="texto" {{ $configs['tipo_envio'] === 'texto' ? 'checked' : '' }} onchange="selecionarFormato('texto')">
                                        </div>
                                        <h6 class="fw-bold mb-1"><i class="fas fa-align-left text-secondary me-1"></i> Texto com PIX Copia e Cola</h6>
                                        <p class="text-secondary small mb-2">Mensagem de texto formatada com emoji, link e o código Copia e Cola em bloco monoespaçado.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome do Estabelecimento / Beneficiário <span class="text-danger">*</span></label>
                                    <input type="text" name="merchant_name" class="form-control" value="{{ $configs['merchant_name'] }}" required>
                                    <div class="form-text small">Exibido no título da cobrança PIX no WhatsApp.</div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tipo de Chave PIX</label>
                                    <select name="pix_key_type" class="form-select">
                                        <option value="CPF" {{ $configs['pix_key_type'] === 'CPF' ? 'selected' : '' }}>CPF</option>
                                        <option value="CNPJ" {{ $configs['pix_key_type'] === 'CNPJ' ? 'selected' : '' }}>CNPJ</option>
                                        <option value="EMAIL" {{ $configs['pix_key_type'] === 'EMAIL' ? 'selected' : '' }}>E-mail</option>
                                        <option value="PHONE" {{ $configs['pix_key_type'] === 'PHONE' ? 'selected' : '' }}>Telefone</option>
                                        <option value="EVP" {{ $configs['pix_key_type'] === 'EVP' ? 'selected' : '' }}>Chave Aleatória (EVP)</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Chave PIX de Cobrança de Faturas</label>
                                    <input type="text" name="pix_key" class="form-control" value="{{ $configs['pix_key'] }}" placeholder="Chave para faturas de clientes">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Delay Anti-Bloqueio entre Disparos (Milissegundos)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-stopwatch"></i></span>
                                        <input type="number" name="delay" class="form-control" value="{{ $configs['delay'] ?? 1200 }}" min="0" step="100">
                                        <span class="input-group-text">ms</span>
                                    </div>
                                    <div class="form-text small">Intervalo simulado antes de cada envio (Ex: 1200ms para humanização anti-spam).</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Anexo de Carnê / Fatura em PDF</label>
                                    <div class="form-check form-switch fs-5 mt-1">
                                        <input class="form-check-input" type="checkbox" name="enviar_pdf" id="checkEnviarPdf" value="1" {{ $configs['enviar_pdf'] ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6 fw-bold" for="checkEnviarPdf">
                                            Enviar automaticamente o PDF da fatura/carnê como mídia em anexo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 4: RÉGUA DE COBRANÇA AUTOMATIZADA (TIMELINE VISUAL)
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-regua" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold text-body mb-1"><i class="fas fa-clock-rotate-left text-danger me-2"></i> Régua de Cobrança Automatizada via WhatsApp</h5>
                                    <p class="text-secondary small mb-0">Habilite ou desabilite as etapas de disparo automático sincronizadas com o agendador Cron.</p>
                                </div>
                            </div>

                            <div class="timeline-regua">
                                <!-- Lembrete 1 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-primary"><i class="fas fa-bell"></i></div>
                                    <div class="card shadow-xs border p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-body">1. Primeiro Lembrete Preventivo (10 Dias Antes)</h6>
                                                <small class="text-secondary">Avisa o cliente amigavelmente sobre a disponibilidade da fatura com link do boleto/PIX.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_1" value="1" {{ $configs['regua_lembrete_1'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lembrete 2 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-info"><i class="fas fa-calendar-check"></i></div>
                                    <div class="card shadow-xs border p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-body">2. Segundo Lembrete Preventivo (5 Dias Antes)</h6>
                                                <small class="text-secondary">Reforça a proximidade da data de vencimento com botão de pagamento imediato.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_2" value="1" {{ $configs['regua_lembrete_2'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lembrete 3 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-warning"><i class="fas fa-hourglass-half"></i></div>
                                    <div class="card shadow-xs border p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-body">3. Terceiro Lembrete Preventivo (1 Dia Antes)</h6>
                                                <small class="text-secondary">Avisa que a fatura vence no dia seguinte, prevenindo juros e mora.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_3" value="1" {{ $configs['regua_lembrete_3'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vencimento Hoje -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-danger"><i class="fas fa-circle-exclamation"></i></div>
                                    <div class="card shadow-xs border border-danger border-opacity-50 p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-danger">4. Cobrança no Dia do Vencimento (HOJE)</h6>
                                                <small class="text-secondary">Notificação prioritária com chave PIX copia e cola direto na mensagem.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_vencimento_hoje" value="1" {{ $configs['regua_vencimento_hoje'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Atraso 1 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-secondary"><i class="fas fa-triangle-exclamation"></i></div>
                                    <div class="card shadow-xs border p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-body">5. Primeiro Aviso de Atraso (1 Dia Após Vencimento)</h6>
                                                <small class="text-secondary">Alerta sobre a expiração do vencimento e orienta a regularização rápida.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_1" value="1" {{ $configs['regua_atraso_1'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Atraso 2 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-danger"><i class="fas fa-file-invoice-dollar"></i></div>
                                    <div class="card shadow-xs border p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-body">6. Segundo Aviso de Atraso com Encargos (5 Dias Após)</h6>
                                                <small class="text-secondary">Atualiza o valor total com incidência automática de multa e juros diários.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_2" value="1" {{ $configs['regua_atraso_2'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Atraso 3 -->
                                <div class="timeline-step">
                                    <div class="timeline-step-icon text-danger"><i class="fas fa-ban"></i></div>
                                    <div class="card shadow-xs border border-danger p-3 bg-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-danger">7. Aviso de Suspensão Preventiva de Sinal (10 Dias Após)</h6>
                                                <small class="text-secondary">Notifica sobre a iminente interrupção de sinal de rastreamento veicular por inadimplência.</small>
                                            </div>
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_3" value="1" {{ $configs['regua_atraso_3'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 5: TEMPLATES DE MENSAGENS & VISUALIZADOR DE SMARTPHONE
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-templates" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-mobile-screen-button text-info me-2"></i> Templates de Mensagens com Simulador em Tempo Real</h5>
                                <p class="text-secondary small mb-0">Personalize o texto de cada etapa da régua e visualize instantaneamente como ela aparecerá no WhatsApp do cliente.</p>
                            </div>

                            <!-- TAGS DINÂMICAS CLICÁVEIS -->
                            <div class="card bg-body-tertiary border p-3 mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <strong class="small text-body"><i class="fas fa-tags text-primary me-1"></i> Tags Dinâmicas Disponíveis:</strong>
                                    <span class="text-secondary small">(Clique em qualquer tag para copiar)</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{cliente_nome}')">{cliente_nome}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{fatura_id}')">{fatura_id}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{plano_nome}')">{plano_nome}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{valor}')">{valor}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{valor_atualizado}')">{valor_atualizado}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{data_vencimento}')">{data_vencimento}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{link_fatura}')">{link_fatura}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{pix_copia_cola}')">{pix_copia_cola}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{dias_atraso}')">{dias_atraso}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copiarTag('{empresa_nome}')">{empresa_nome}</button>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- COLUNA DA ESQUERDA: EDITOR DE TEMPLATES -->
                                <div class="col-lg-7">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Selecione o Template para Editar:</label>
                                        <select id="selectTemplateAtivo" class="form-select fw-semibold" onchange="trocarTemplateEdicao()">
                                            <option value="tpl_vencimento_hoje">Vencimento Hoje (Prioritário)</option>
                                            <option value="tpl_lembrete_1">Lembrete 1 (10 dias antes)</option>
                                            <option value="tpl_lembrete_2">Lembrete 2 (5 dias antes)</option>
                                            <option value="tpl_lembrete_3">Lembrete 3 (1 dia antes)</option>
                                            <option value="tpl_atraso_1">Atraso 1 (1 dia após)</option>
                                            <option value="tpl_atraso_2">Atraso 2 (5 dias após - com juros)</option>
                                            <option value="tpl_atraso_3">Atraso 3 (10 dias após - aviso suspensão)</option>
                                            <option value="tpl_fatura_gerada">Nova Fatura Gerada</option>
                                            <option value="tpl_pagamento_confirmado">Pagamento Confirmado (Baixa Automática)</option>
                                        </select>
                                    </div>

                                    <!-- ÁREAS DE TEXTO COM SUMMERNOTE WYSIWYG CONFORME SELEÇÃO -->
                                    <div class="box-template-editor" id="box_tpl_vencimento_hoje">
                                        <label class="form-label small fw-bold text-danger">Template: Vencimento Hoje</label>
                                        <textarea name="tpl_vencimento_hoje" id="textarea_tpl_vencimento_hoje" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_vencimento_hoje'], '<') ? nl2br(e($configs['tpl_vencimento_hoje'])) : $configs['tpl_vencimento_hoje'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_lembrete_1">
                                        <label class="form-label small fw-bold text-primary">Template: Primeiro Lembrete (10 Dias Antes)</label>
                                        <textarea name="tpl_lembrete_1" id="textarea_tpl_lembrete_1" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_lembrete_1'], '<') ? nl2br(e($configs['tpl_lembrete_1'])) : $configs['tpl_lembrete_1'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_lembrete_2">
                                        <label class="form-label small fw-bold text-info">Template: Segundo Lembrete (5 Dias Antes)</label>
                                        <textarea name="tpl_lembrete_2" id="textarea_tpl_lembrete_2" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_lembrete_2'], '<') ? nl2br(e($configs['tpl_lembrete_2'])) : $configs['tpl_lembrete_2'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_lembrete_3">
                                        <label class="form-label small fw-bold text-warning">Template: Terceiro Lembrete (1 Dia Antes)</label>
                                        <textarea name="tpl_lembrete_3" id="textarea_tpl_lembrete_3" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_lembrete_3'], '<') ? nl2br(e($configs['tpl_lembrete_3'])) : $configs['tpl_lembrete_3'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_atraso_1">
                                        <label class="form-label small fw-bold text-secondary">Template: Atraso 1 (1 Dia Após)</label>
                                        <textarea name="tpl_atraso_1" id="textarea_tpl_atraso_1" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_atraso_1'], '<') ? nl2br(e($configs['tpl_atraso_1'])) : $configs['tpl_atraso_1'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_atraso_2">
                                        <label class="form-label small fw-bold text-danger">Template: Atraso 2 (5 Dias Após)</label>
                                        <textarea name="tpl_atraso_2" id="textarea_tpl_atraso_2" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_atraso_2'], '<') ? nl2br(e($configs['tpl_atraso_2'])) : $configs['tpl_atraso_2'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_atraso_3">
                                        <label class="form-label small fw-bold text-danger">Template: Atraso 3 (10 Dias Após - Urgente)</label>
                                        <textarea name="tpl_atraso_3" id="textarea_tpl_atraso_3" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_atraso_3'], '<') ? nl2br(e($configs['tpl_atraso_3'])) : $configs['tpl_atraso_3'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_fatura_gerada">
                                        <label class="form-label small fw-bold text-primary">Template: Nova Fatura Gerada</label>
                                        <textarea name="tpl_fatura_gerada" id="textarea_tpl_fatura_gerada" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_fatura_gerada'], '<') ? nl2br(e($configs['tpl_fatura_gerada'])) : $configs['tpl_fatura_gerada'] !!}</textarea>
                                    </div>

                                    <div class="box-template-editor d-none" id="box_tpl_pagamento_confirmado">
                                        <label class="form-label small fw-bold text-success">Template: Pagamento Confirmado</label>
                                        <textarea name="tpl_pagamento_confirmado" id="textarea_tpl_pagamento_confirmado" class="form-control summernote" data-height="240">{!! !str_contains($configs['tpl_pagamento_confirmado'], '<') ? nl2br(e($configs['tpl_pagamento_confirmado'])) : $configs['tpl_pagamento_confirmado'] !!}</textarea>
                                    </div>
                                </div>

                                <!-- COLUNA DA DIREITA: SMARTPHONE MOCKUP PREVIEW -->
                                <div class="col-lg-5 text-center">
                                    <div class="small fw-bold text-secondary text-uppercase mb-2">
                                        <i class="fas fa-eye me-1"></i> Visualização Realista no WhatsApp
                                    </div>
                                    <div class="smartphone-mockup">
                                        <div class="phone-notch"></div>
                                        <div class="phone-header">
                                            <i class="fas fa-arrow-left small"></i>
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                                <i class="fas fa-car-side"></i>
                                            </div>
                                            <div class="text-start">
                                                <div class="fw-bold small lh-1">{{ $configs['merchant_name'] ?? 'Foco 7 Rastreamento' }}</div>
                                                <small class="text-success" style="font-size: 10px;">online agora</small>
                                            </div>
                                        </div>
                                        <div class="phone-body text-start">
                                            <div class="chat-bubble-wpp" id="previewChatBubbleText">
                                                <!-- Texto renderizado dinamicamente via JS -->
                                            </div>
                                            <div id="previewBotoesAcao">
                                                <a href="javascript:void(0)" class="chat-btn-action"><i class="fas fa-up-right-from-square me-1"></i> Pagar Fatura Online</a>
                                                <a href="javascript:void(0)" class="chat-btn-action"><i class="fas fa-copy me-1"></i> Copiar Código PIX</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 6: DISPARO DE TESTE & CONSOLE DE DIAGNÓSTICO
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-teste" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-vial text-warning me-2"></i> Disparo de Teste em Tempo Real</h5>
                                <p class="text-secondary small mb-0">Envie uma mensagem de teste para o seu próprio WhatsApp para validar a conexão e a renderização dos botões.</p>
                            </div>

                            <div class="row g-3" style="max-width: 750px;">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">Número de WhatsApp para Teste <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="text" id="inputTelefoneTeste" class="form-control" placeholder="(11) 99999-9999 ou 5511999999999">
                                    </div>
                                    <div class="form-text small">Pode digitar com ou sem máscara. O sistema adiciona o DDI 55 do Brasil automaticamente.</div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Tipo do Disparo <span class="text-danger">*</span></label>
                                    <select id="selectTipoTeste" class="form-select fw-semibold">
                                        <option value="texto">Texto Formatado Oficial</option>
                                        <option value="pix">Botão PIX Nativo (/send/pix)</option>
                                        <option value="botoes">Botões Interativos (URL + Copiar)</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="button" class="btn btn-warning fw-bold px-4 shadow-sm" id="btnDispararTeste" onclick="enviarMensagemTeste()">
                                        <i class="fas fa-paper-plane me-1"></i> Disparar Mensagem de Teste
                                    </button>
                                </div>

                                <div class="col-12 mt-4" id="boxRespostaTeste" style="display: none;">
                                    <label class="form-label fw-semibold small text-secondary">Retorno do Servidor Evolution API:</label>
                                    <pre class="bg-body-secondary p-3 rounded font-monospace small mb-0 border" id="jsonRespostaTeste" style="max-height: 250px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 7: WEBHOOK & MONITOR DE EVENTOS
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-webhook" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-satellite-dish text-secondary me-2"></i> Webhook da Evolution API Go</h5>
                                <p class="text-secondary small mb-0">Receba atualizações em tempo real sobre status de conexão, entregas e respostas de clientes.</p>
                            </div>

                            <div class="card bg-body-tertiary border p-4 mb-4">
                                <label class="form-label fw-semibold">URL de Webhook Oficial do Sistema</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control font-monospace" id="inputWebhookUrl" value="{{ $webhookUrl }}" readonly>
                                    <button class="btn btn-primary" type="button" onclick="copiarWebhookUrl()">
                                        <i class="fas fa-copy me-1"></i> Copiar URL
                                    </button>
                                </div>
                                <div class="form-text small">
                                    Esta URL já é configurada automaticamente ao clicar no botão <strong>Conectar / QR Code</strong> via <code>POST /instance/connect</code>.
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-transparent py-2">
                                    <strong class="small text-body"><i class="fas fa-list-check me-1"></i> Eventos Monitorados em Tempo Real:</strong>
                                </div>
                                <div class="card-body p-3">
                                    <ul class="mb-0 small text-secondary">
                                        <li><code>CONNECTION</code>: Monitora mudanças de estado da conexão (open, connecting, close) e atualiza os badges em tempo real.</li>
                                        <li><code>QRCODE</code>: Captura o QR Code para leitura no painel.</li>
                                        <li><code>SEND_MESSAGE</code>: Notificações de confirmação de envio no WhatsApp.</li>
                                        <li><code>MESSAGE</code>: Mensagens recebidas dos clientes.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- BOTÃO FIXO DE SALVAR CONFIGURAÇÕES -->
                    <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" id="btnSalvarConfigs">
                            <i class="fas fa-save me-1"></i> Salvar Configurações &amp; Criptografar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<!-- ===================================================================
     MODAL 1: CONEXÃO VIA QR CODE OU NÚMERO DE TELEFONE (PAIRING CODE)
     =================================================================== -->
<div class="modal fade" id="modalQrCode" tabindex="-1" aria-labelledby="modalQrCodeLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fab fa-whatsapp fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalQrCodeLabel">Conectar Aparelho WhatsApp</h5>
                        <small class="text-white text-opacity-75 d-block">Instância: <strong class="font-monospace" id="txtModalNomeInstanciaConexao">{{ $configs['instance'] ?? 'foco7' }}</strong></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="pararPollingQr()"></button>
            </div>
            <div class="modal-body p-4 text-center">

                <!-- PAINEL DE INFORMAÇÕES DO FRANQUEADO NA CONEXÃO DA INSTÂNCIA -->
                <div id="boxInfoFranqueadoModalQr" class="p-3 rounded-3 bg-light border text-start mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary" id="txtQrFranqueadoTipoBadge"><i class="fas fa-user-tie me-1"></i> Franqueado / Titular</span>
                            <strong class="text-body small" id="txtQrFranqueadoEmpresa">{{ $franqueadoAtivoInfo['empresa_franqueado'] ?? 'Foco 7 Rastreamento' }}</strong>
                            <span class="badge bg-success-subtle text-success border border-success" style="font-size: 0.68rem;" title="Isolamento estrito ativo: nenhum outro licenciado tem acesso a esta instância"><i class="fas fa-shield-halved me-1"></i> Isolado neste Painel</span>
                        </div>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" onclick="abrirModalEditarFranqueadoInstancia(instanciaModalAtual)">
                            <i class="fas fa-user-pen me-1"></i> Editar Franqueado
                        </button>
                    </div>
                    <div class="row g-2 small" style="font-size: 0.78rem;">
                        <div class="col-sm-6">
                            <div class="text-secondary"><i class="fas fa-user me-1 text-primary"></i> <strong>Responsável:</strong> <span class="text-body fw-semibold" id="txtQrFranqueadoNome">{{ $franqueadoAtivoInfo['nome_franqueado'] ?? 'Administrador' }}</span></div>
                            <div class="text-secondary"><i class="fas fa-globe me-1 text-info"></i> <strong>Domínio/Licença:</strong> <span class="font-monospace text-body" id="txtQrFranqueadoDominio">{{ $franqueadoAtivoInfo['dominio_franqueado'] ?: ($franqueadoAtivoInfo['chave_licenca'] ?? 'foco7.com.br') }}</span></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-secondary"><i class="fab fa-whatsapp me-1 text-success"></i> <strong>Contato:</strong> <span class="font-monospace text-body" id="txtQrFranqueadoTelefone">{{ $franqueadoAtivoInfo['telefone_franqueado'] ?: 'Não informado' }}</span></div>
                            <div class="text-secondary"><i class="fas fa-calendar-check me-1 text-warning"></i> <strong>Vencimento:</strong> <span class="fw-semibold text-body" id="txtQrFranqueadoVencimento">{{ $franqueadoAtivoInfo['data_vencimento_fmt'] ?? date('d/m/Y', strtotime('+30 days')) }}</span></div>
                        </div>
                    </div>
                </div>

                <div id="qrLoadingBox" class="py-4">
                    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h6 class="fw-bold text-body" id="txtQrLoadingTitulo">Gerando QR Code na Evolution API Go...</h6>
                    <p class="text-secondary small mb-0">Por favor, aguarde enquanto iniciamos a sessão da instância.</p>
                </div>

                <div id="qrCodeContentBox" style="display: none;">
                    <!-- Seletor de Modo: Ler QR Code ou Digitar Número -->
                    <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 border mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active fw-bold py-2" id="btnTabModoQr" onclick="alternarModoConexaoModal('qr')">
                                <i class="fas fa-qrcode me-1"></i> Ler QR Code
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link fw-bold py-2" id="btnTabModoNumero" onclick="alternarModoConexaoModal('numero')">
                                <i class="fas fa-mobile-screen-button me-1"></i> Conectar c/ Número
                            </button>
                        </li>
                    </ul>

                    <!-- ABA 1: LEITURA DO QR CODE -->
                    <div id="subPainelModoQr">
                        <div class="p-3 bg-white d-inline-block rounded-3 shadow-sm border mb-3">
                            <img id="imgQrCodeDisplay" src="" alt="QR Code WhatsApp" class="img-fluid" style="width: 245px; height: 245px; object-fit: contain;">
                        </div>
                        <div class="alert alert-light border small text-start mb-3">
                            <ol class="mb-0 ps-3">
                                <li>Abra o <strong>WhatsApp</strong> no seu celular.</li>
                                <li>Toque em <strong>Aparelhos conectados</strong> &rarr; <strong>Conectar um aparelho</strong>.</li>
                                <li>Aponte a câmera para o QR Code acima ou clique na aba <strong>Conectar c/ Número</strong>.</li>
                            </ol>
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-success btn-sm fw-bold" onclick="recarregarQrCode(true)">
                                <i class="fas fa-rotate me-1"></i> Gerar Novo QR Code
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="alternarModoConexaoModal('numero')">
                                <i class="fas fa-keyboard me-1"></i> Digitar Número de Telefone
                            </button>
                        </div>
                    </div>

                    <!-- ABA 2: CONECTAR COM NÚMERO DE TELEFONE (PAIRING CODE 8 DÍGITOS) -->
                    <div id="subPainelModoNumero" style="display: none;" class="text-start">
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <label class="form-label fw-bold small text-body mb-1">
                                <i class="fab fa-whatsapp text-success me-1"></i> Número do WhatsApp (com DDI + DDD)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-phone text-secondary"></i></span>
                                <input type="text" id="inputNumeroPareamento" class="form-control font-monospace fw-bold" placeholder="Ex: 5521999999999 ou (21) 99999-9999">
                                <button type="button" class="btn btn-success fw-bold" id="btnGerarCodigoPareamento" onclick="solicitarCodigoPareamento()">
                                    <i class="fas fa-key me-1"></i> Gerar Código
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Digite o número do celular que receberá o código de conexão no WhatsApp.</small>
                        </div>

                        <!-- Caixa com o Código de 8 Dígitos Gerado -->
                        <div id="boxCodigoPareamentoGerado" style="display: none;" class="p-3 rounded-3 bg-success bg-opacity-10 border border-success text-center mb-3">
                            <span class="badge bg-success mb-2"><i class="fas fa-lock-open me-1"></i> Código de Pareamento Gerado</span>
                            <div class="my-2">
                                <span id="txtCodigoPareamentoDisplay" class="font-monospace fw-bolder text-success bg-white px-3 py-2 rounded border border-success d-inline-block" style="font-size: 1.65rem; letter-spacing: 3px;">----</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success fw-bold mt-1" onclick="copiarCodigoPareamento()">
                                <i class="fas fa-copy me-1"></i> Copiar Código
                            </button>
                            <div class="alert alert-white bg-white border small text-start mt-3 mb-0">
                                <strong class="d-block text-dark mb-1"><i class="fas fa-mobile-screen me-1 text-success"></i> Como inserir no celular:</strong>
                                <ol class="mb-0 ps-3 text-secondary">
                                    <li>Toque na notificação do WhatsApp que chegou no celular ou vá em <strong>Aparelhos Conectados &rarr; Conectar um aparelho</strong>.</li>
                                    <li>Toque em <strong>"Conectar com número de telefone"</strong> na parte inferior da tela.</li>
                                    <li>Digite o código de 8 caracteres acima. A conexão será reconhecida automaticamente!</li>
                                </ol>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none text-secondary" onclick="alternarModoConexaoModal('qr')">
                                <i class="fas fa-qrcode me-1"></i> Voltar para leitura de QR Code
                            </button>
                        </div>
                    </div>
                </div>

                <div id="qrConectadoBox" style="display: none;" class="py-4">
                    <div class="bg-success bg-opacity-15 text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-check-circle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">WhatsApp Conectado com Sucesso!</h5>
                    <p class="text-secondary small mb-2">Sua instância está 100% online e operando no cluster Evolution Go.</p>
                    <div class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6 mb-3 font-monospace" id="boxModalNumeroConectado">
                        <i class="fab fa-whatsapp me-1"></i> <span id="txtModalNumeroConectado">{{ $numeroConectado ?: 'Aparelho Vinculado' }}</span>
                    </div>
                    <div class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                        <button type="button" class="btn btn-success fw-bold px-4" data-bs-dismiss="modal">
                            <i class="fas fa-check me-1"></i> Pronto para Envios
                        </button>
                        <button type="button" class="btn btn-outline-danger fw-semibold" onclick="desconectarELimparParaNovoQr()">
                            <i class="fas fa-power-off me-1"></i> Desconectar &amp; Trocar Aparelho
                        </button>
                    </div>
                </div>

                <div id="qrErroBox" style="display: none;" class="py-3">
                    <div class="bg-danger bg-opacity-15 text-danger rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-triangle-exclamation fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-danger mb-1">Não foi possível exibir o QR Code</h6>
                    <p class="text-secondary small mb-3" id="txtMensagemErroQr">A instância pode precisar de reinicialização da sessão.</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                        <button type="button" class="btn btn-success btn-sm fw-bold" onclick="conectarEabrirQr(instanciaModalAtual, true)">
                            <i class="fas fa-qrcode me-1"></i> Limpar Sessão &amp; Gerar Novo QR Code
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="abrirAbaNumeroDiretoNoModal()">
                            <i class="fas fa-mobile-screen-button me-1"></i> Conectar c/ Número
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="autoCriarOuSincronizarInstanciaModal()">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Sincronizar Instância
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL 2: CRIAÇÃO DE NOVA INSTÂNCIA (+ R$ 60,00/MÊS)
     =================================================================== -->
<div class="modal fade" id="modalCriarInstancia" tabindex="-1" aria-labelledby="modalCriarInstanciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalCriarInstanciaLabel"><i class="fas fa-plus-circle me-2"></i> Criar Minha Própria Instância / Adicional</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card border-warning bg-warning bg-opacity-10 mb-3 shadow-none">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <span class="badge bg-danger text-white px-2 py-1 fs-6">
                                <i class="fas fa-tag me-1"></i> R$ 60,00 / mês • Módulo Mercado Pago (Dev)
                            </span>
                            <span class="badge bg-success text-white">Cluster Evolution Go</span>
                        </div>
                        <p class="small text-secondary mb-2">
                            A instância atual (<code class="fw-bold text-dark">foco7</code>) é de uso exclusivo para configuração. O Administrador <strong>deve criar e ativar sua própria instância</strong> através deste painel antes de começar a realizar cobranças via WhatsApp (fora isso, as cobranças ocorrem somente via E-mail).
                        </p>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="checkConcordoValorExtra" checked>
                            <label class="form-check-label small fw-semibold text-body" for="checkConcordoValorExtra">
                                Estou ciente da mensalidade recorrente de <strong>R$ 60,00/mês</strong> cobrada pelo Módulo Mercado Pago do Desenvolvedor.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nome da Sua Instância <span class="text-danger">*</span></label>
                        <input type="text" id="modalInstanciaNome" class="form-control font-monospace" value="minha_empresa_{{ rand(100, 999) }}" placeholder="Ex: minha_empresa_wpp ou filial_belem">
                        <div class="form-text small">Identificador único neste painel</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Finalidade / Tipo da Instância</label>
                        <select id="modalInstanciaTipo" class="form-select" onchange="aoMudarTipoModalInstancia()">
                            <option value="propria_admin" selected>Minha Própria Instância Admin / Franqueado</option>
                            <option value="licenciado">Instância Dedicada de Franqueado / Licenciado</option>
                            <option value="empresa">Instância Adicional da Empresa / Filial</option>
                        </select>
                    </div>
                </div>

                @if(isset($licenciados) && count($licenciados) > 0 && ($configs['dev_desbloqueado'] ?? false))
                <div class="mb-3 p-2 rounded bg-light border">
                    <label class="form-label fw-bold small text-primary mb-1">
                        <i class="fas fa-user-tie me-1"></i> Selecionar Franqueado / Licenciado Cadastrado (Exclusivo Master)
                    </label>
                    <select id="modalInstanciaLicenciadoId" class="form-select form-select-sm" onchange="preencherCamposPorFranqueadoSelecionado(this, 'modalInstancia')">
                        <option value="">-- Preencher manualmente ou selecionar franqueado da lista --</option>
                        @foreach($licenciados as $lic)
                            <option value="{{ $lic->id }}"
                                data-nome="{{ $lic->user?->name ?? $lic->nome_empresa }}"
                                data-empresa="{{ $lic->nome_empresa }}"
                                data-doc="{{ $lic->user?->cpf_cnpj ?? '' }}"
                                data-tel="{{ $lic->user?->telefone ?? '' }}"
                                data-email="{{ $lic->user?->email ?? '' }}"
                                data-dominio="{{ $lic->dominio_autorizado ?? '' }}"
                                data-chave="{{ $lic->chave_licenca }}">
                                #{{ $lic->id }} - {{ $lic->nome_empresa }} ({{ $lic->user?->name ?? 'Franqueado' }}) • {{ $lic->chave_licenca }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="modalInstanciaLicenciadoId" value="{{ $contextoPainel['licenciado_id_usuario'] ?? '' }}">
                <div class="mb-3 p-2 rounded bg-success bg-opacity-10 border border-success small text-body">
                    <i class="fas fa-user-lock text-success me-1"></i>
                    <strong>Isolamento por Painel Ativo:</strong> Esta instância pertencerá exclusivamente a este painel (<code class="fw-bold">{{ $contextoPainel['dominio'] ?? request()->getHost() }}</code>), sem compartilhamento com outros licenciados.
                </div>
                @endif

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">Nome do Franqueado / Responsável <span class="text-danger">*</span></label>
                        <input type="text" id="modalInstanciaNomeResp" class="form-control form-control-sm" value="{{ $franqueadoAtivoInfo['nome_franqueado'] ?? (auth()->user()?->name ?? '') }}" placeholder="Ex: João da Silva">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">Empresa / Unidade do Franqueado <span class="text-danger">*</span></label>
                        <input type="text" id="modalInstanciaEmpresaFranqueado" class="form-control form-control-sm" value="{{ $franqueadoAtivoInfo['empresa_franqueado'] ?? '' }}" placeholder="Ex: Rastreamento Norte Ltda">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">WhatsApp do Franqueado (p/ Avisos PIX)</label>
                        <input type="text" id="modalInstanciaTelResp" class="form-control form-control-sm mascara-telefone" value="{{ $franqueadoAtivoInfo['telefone_franqueado'] ?? '' }}" placeholder="(99) 99999-9999">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">E-mail do Franqueado</label>
                        <input type="email" id="modalInstanciaEmailResp" class="form-control form-control-sm" value="{{ $franqueadoAtivoInfo['email_franqueado'] ?? (auth()->user()?->email ?? '') }}" placeholder="franqueado@empresa.com.br">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">CPF / CNPJ do Franqueado</label>
                        <input type="text" id="modalInstanciaDocFranqueado" class="form-control form-control-sm font-monospace" value="{{ $franqueadoAtivoInfo['documento_franqueado'] ?? '' }}" placeholder="00.000.000/0001-00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">Domínio / Servidor do Franqueado</label>
                        <input type="text" id="modalInstanciaDominioFranqueado" class="form-control form-control-sm font-monospace" value="{{ $franqueadoAtivoInfo['dominio_franqueado'] ?? '' }}" placeholder="painel.franqueado.com.br">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">Chave de Licença (Opcional)</label>
                        <input type="text" id="modalInstanciaChaveLicenca" class="form-control form-control-sm font-monospace" value="{{ $franqueadoAtivoInfo['chave_licenca'] ?? '' }}" placeholder="FOCO7-XXXX-XXXX">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold small mb-1">Token da Instância (Opcional - Gerado automaticamente se vazio)</label>
                    <input type="text" id="modalInstanciaToken" class="form-control form-control-sm font-monospace" placeholder="Deixe em branco para auto-geração">
                </div>
            </div>
            <div class="modal-footer bg-body-tertiary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary fw-bold" id="btnConfirmarCriarInstancia" onclick="executarCriacaoInstancia()">
                    <i class="fas fa-check me-1"></i> Confirmar &amp; Criar Instância
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL EXTRA: EDITAR DADOS DO FRANQUEADO NA CONEXÃO DA INSTÂNCIA
     =================================================================== -->
<div class="modal fade" id="modalEditarFranqueadoInstancia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user-tie me-2"></i> Dados do Franqueado na Instância <code class="text-white bg-dark bg-opacity-25 px-2 py-1 rounded" id="txtEditFranqueadoInstanciaLabel">foco7</code>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editFranqueadoInstanciaNome" value="">

                @if(isset($licenciados) && count($licenciados) > 0 && ($configs['dev_desbloqueado'] ?? false))
                <div class="mb-3 p-2 rounded bg-light border">
                    <label class="form-label fw-bold small text-primary mb-1">
                        <i class="fas fa-id-badge me-1"></i> Vincular a um Franqueado / Licenciado Cadastrado (Exclusivo Master)
                    </label>
                    <select id="editFranqueadoLicenciadoId" class="form-select form-select-sm" onchange="preencherCamposPorFranqueadoSelecionado(this, 'editFranqueado')">
                        <option value="">-- Manter preenchimento personalizado abaixo --</option>
                        @foreach($licenciados as $lic)
                            <option value="{{ $lic->id }}"
                                data-nome="{{ $lic->user?->name ?? $lic->nome_empresa }}"
                                data-empresa="{{ $lic->nome_empresa }}"
                                data-doc="{{ $lic->user?->cpf_cnpj ?? '' }}"
                                data-tel="{{ $lic->user?->telefone ?? '' }}"
                                data-email="{{ $lic->user?->email ?? '' }}"
                                data-dominio="{{ $lic->dominio_autorizado ?? '' }}"
                                data-chave="{{ $lic->chave_licenca }}">
                                #{{ $lic->id }} - {{ $lic->nome_empresa }} ({{ $lic->user?->name ?? 'Franqueado' }}) • {{ $lic->chave_licenca }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="editFranqueadoLicenciadoId" value="{{ $contextoPainel['licenciado_id_usuario'] ?? '' }}">
                <div class="mb-3 p-2 rounded bg-success bg-opacity-10 border border-success small text-body">
                    <i class="fas fa-shield-halved text-success me-1"></i>
                    <strong>Dados Isolados deste Painel:</strong> As informações abaixo identificam o franqueado titular desta instância neste painel.
                </div>
                @endif

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">Nome do Franqueado / Responsável</label>
                        <input type="text" id="editFranqueadoNome" class="form-control form-control-sm" placeholder="Nome completo do responsável">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">Empresa / Unidade Franqueada</label>
                        <input type="text" id="editFranqueadoEmpresa" class="form-control form-control-sm" placeholder="Nome da empresa ou franquia">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">WhatsApp do Franqueado</label>
                        <input type="text" id="editFranqueadoTelefone" class="form-control form-control-sm mascara-telefone" placeholder="(99) 99999-9999">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">E-mail do Franqueado</label>
                        <input type="email" id="editFranqueadoEmail" class="form-control form-control-sm" placeholder="contato@franqueado.com.br">
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">CPF / CNPJ do Franqueado</label>
                        <input type="text" id="editFranqueadoDocumento" class="form-control form-control-sm font-monospace" placeholder="00.000.000/0001-00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small mb-1">Domínio / Servidor do Franqueado</label>
                        <input type="text" id="editFranqueadoDominio" class="form-control form-control-sm font-monospace" placeholder="rastreamento.franqueado.com.br">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold small mb-1">Chave de Licença Vinculada</label>
                    <input type="text" id="editFranqueadoChave" class="form-control form-control-sm font-monospace" placeholder="FOCO7-XXXX-XXXX-XXXX">
                </div>
            </div>
            <div class="modal-footer bg-body-tertiary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" id="btnSalvarDadosFranqueado" onclick="salvarDadosFranqueadoModal()">
                    <i class="fas fa-save me-1"></i> Salvar Informações do Franqueado
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL 3: CHECKOUT TRANSPARENTE PIX ÍNTEGRO DE INSTÂNCIA (SOMENTE PIX + RETORNO AUTOMÁTICO)
     =================================================================== -->
<div class="modal fade" id="modalCheckoutPixInstancia" tabindex="-1" aria-labelledby="modalCheckoutPixLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fab fa-pix text-warning fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="modalCheckoutPixLabel">
                            Checkout Transparente PIX • Instância WhatsApp
                        </h6>
                        <small class="text-white-50" style="font-size: 0.72rem;">Módulo Mercado Pago (Desenvolvedor) • Somente PIX • Retorno Automático</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="pararPollingPixInstancia()"></button>
            </div>
            <div class="modal-body p-4 text-center">

                <!-- Loading PIX -->
                <div id="checkoutPixLoadingBox" class="py-4">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h6 class="fw-bold text-body">Gerando Checkout Transparente PIX...</h6>
                    <p class="text-secondary small mb-0">Injetando Webhook Dinâmico exclusivo e comunicando com o Mercado Pago.</p>
                </div>

                <!-- Conteúdo do Checkout Transparente Íntegro -->
                <div id="checkoutPixConteudoBox" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-1 mb-2">
                        <span class="badge bg-success"><i class="fab fa-pix me-1"></i> Somente PIX Transparente</span>
                        <span class="badge bg-primary" id="checkoutPixBadgeAmbiente">Produção • PIX Oficial</span>
                        <span class="badge bg-info text-dark"><i class="fas fa-bolt me-1"></i> Webhook Dinâmico Ativo</span>
                    </div>

                    <h3 class="fw-extrabold text-danger mb-1" id="checkoutPixValor">R$ 60,00</h3>
                    <p class="text-secondary small mb-2">
                        Instância: <strong class="font-monospace text-body" id="checkoutPixNomeInstancia">foco7</strong> | 
                        Vencimento: <strong class="text-body" id="checkoutPixDataVenc">--</strong>
                    </p>
                    <div class="small text-secondary mb-3" style="font-size: 0.75rem;">
                        Fatura: <code id="checkoutPixCodigoFatura">--</code> • Transação: <code id="checkoutPixTransacaoId">--</code>
                    </div>

                    <div class="p-3 bg-white d-inline-block rounded-3 shadow-sm border mb-2">
                        <img id="checkoutPixImgQr" src="" alt="QR Code PIX" class="img-fluid" style="width: 210px; height: 210px; object-fit: contain;">
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 small" onclick="abrirCheckoutPixInstancia(checkoutInstanciaAtual, true)">
                            <i class="fas fa-rotate me-1"></i> Gerar Novo QR Code PIX (Nova Requisição &amp; Webhook Dinâmico)
                        </button>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold text-secondary mb-1 d-flex justify-content-between">
                            <span>Código PIX Copia e Cola:</span>
                            <span class="text-success"><i class="fas fa-lock me-1"></i> Checkout Íntegro</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="checkoutPixCopiaCola" class="form-control font-monospace small" readonly>
                            <button type="button" class="btn btn-warning fw-bold text-dark" onclick="copiarPixCopiaCola()">
                                <i class="fas fa-copy me-1"></i> Copiar PIX
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-start mb-0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                                <strong class="text-success">Radar de Retorno Automático Ativo...</strong>
                            </div>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace" id="checkoutPixReqTokenBadge">REQ: --</span>
                        </div>
                        <span class="text-secondary d-block" style="font-size: 0.78rem;">
                            Assim que o PIX for compensado, o Webhook Dinâmico notifica o sistema e este checkout retorna automaticamente liberando a instância.
                        </span>
                    </div>
                </div>

                <!-- Box de Sucesso com Retorno Automático dentro do Checkout Transparente -->
                <div id="checkoutPixSucessoBox" style="display: none;" class="py-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-3" style="width: 72px; height: 72px;">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-1">Pagamento PIX Confirmado!</h4>
                    <p class="text-secondary small mb-3" id="checkoutPixSucessoMsg">
                        Sua instância foi ativada automaticamente e as cobranças via WhatsApp estão liberadas.
                    </p>

                    <div class="p-3 rounded-3 bg-body-tertiary border text-start small mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary">Instância Ativada:</span>
                            <strong class="font-monospace text-body" id="checkoutPixSucessoInstancia">--</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary">Método / Módulo:</span>
                            <span class="badge bg-success"><i class="fab fa-pix me-1"></i> PIX • MP Desenvolvedor</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary">Nova Vigência (+30 dias):</span>
                            <strong class="text-success" id="checkoutPixSucessoVigencia">--</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Status da Instância:</span>
                            <span class="badge bg-success"><i class="fas fa-bolt me-1"></i> Ativa &amp; Liberada</span>
                        </div>
                    </div>

                    <div class="alert alert-success py-2 small mb-0">
                        <i class="fas fa-circle-notch fa-spin me-1"></i>
                        Retornando automaticamente ao painel em <strong id="checkoutPixContadorRetorno">3</strong>s...
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-body-tertiary d-flex justify-content-between" id="checkoutPixModalFooter">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="pararPollingPixInstancia()">Fechar</button>
                <button type="button" class="btn btn-success btn-sm fw-bold" id="btnJaEfetueiPagamentoPix" onclick="checarStatusPixManual()">
                    <i class="fas fa-check-circle me-1"></i> Já Efetuei o Pagamento PIX
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var pollingTimer = null;
    var pollingPixTimer = null;
    var retornoPixTimer = null;
    var checkoutInstanciaAtual = null;

    // Alternância dinâmica entre os campos de Produção e Sandbox no Módulo Mercado Pago (Desenvolvedor)
    function alternarAmbienteMpDev(ambiente) {
        var amb = (ambiente === 'sandbox') ? 'sandbox' : 'producao';
        $('#inputDevMpAmbiente').val(amb);

        if (amb === 'sandbox') {
            $('#boxCredenciaisMpDevProd').slideUp(180);
            $('#boxCredenciaisMpDevSandbox').slideDown(220);
            $('#badgeAmbienteAtivoMpDev')
                .removeClass('bg-success')
                .addClass('bg-warning text-dark')
                .html('<i class="fas fa-flask me-1"></i> Modo Sandbox Ativo');
            $('#btnTabAmbSandbox').removeClass('btn-outline-secondary').addClass('btn-warning text-dark');
            $('#btnTabAmbProd').removeClass('btn-success').addClass('btn-outline-secondary');
            $('#txtBtnAmbienteMpDev').text('Sandbox');
        } else {
            $('#boxCredenciaisMpDevSandbox').slideUp(180);
            $('#boxCredenciaisMpDevProd').slideDown(220);
            $('#badgeAmbienteAtivoMpDev')
                .removeClass('bg-warning text-dark')
                .addClass('bg-success')
                .html('<i class="fas fa-check-circle me-1"></i> Modo Produção Ativo');
            $('#btnTabAmbProd').removeClass('btn-outline-secondary').addClass('btn-success');
            $('#btnTabAmbSandbox').removeClass('btn-warning text-dark').addClass('btn-outline-secondary');
            $('#txtBtnAmbienteMpDev').text('Produção');
        }
    }

    function selecionarAmbienteMpDevClick(ambiente) {
        if ($('#inputDevMpAmbiente').prop('disabled')) {
            solicitarSenhaSegurancaWpp();
            return;
        }
        alternarAmbienteMpDev(ambiente);
    }

    // Alternador de visualização de senha
    function toggleVisibilidadeSenha(inputId, btn) {
        var input = document.getElementById(inputId);
        if (input.classList.contains('tarja-dev-bloqueado')) {
            solicitarSenhaSegurancaWpp();
            return;
        }
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function solicitarSenhaSegurancaWpp() {
        Swal.fire({
            title: 'Acesso Restrito do Master',
            html: `
                <div class="text-start mb-2">
                    <p class="small text-secondary mb-3">
                        Informe a <strong>Senha Master Exclusiva</strong> para liberar a visualização e gestão das credenciais do servidor e dados restritos de cobrança das instâncias.
                    </p>
                </div>
            `,
            input: 'password',
            inputLabel: 'Senha Master Exclusiva',
            inputPlaceholder: '••••••••••••••••',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off',
                autocomplete: 'current-password'
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-unlock me-1"></i> Desbloquear Acesso',
            confirmButtonColor: '#d90000',
            cancelButtonText: 'Cancelar',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: (senha) => {
                if (!senha || senha.trim().length === 0) {
                    Swal.showValidationMessage('Informe a senha master!');
                    return false;
                }
                return $.ajax({
                    url: '{{ route("admin.whatsapp.desbloquear_seguranca") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { senha: senha }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Falha ao autenticar.');
                    }
                    return { response: response, senha: senha };
                }).catch(error => {
                    var msg = error.responseJSON?.message || error.message || 'Senha incorreta!';
                    Swal.showValidationMessage(msg);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                var dados = result.value.response.dados;
                var senha = result.value.senha;

                $('#wpp_dev_password_hidden').val(senha);

                $('#inputApiUrl').val(dados.api_url).attr('type', 'url').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputInstance').val(dados.instance).attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputApiKey').val(dados.apikey).removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputGlobalApiKey').val(dados.global_apikey).removeClass('tarja-dev-bloqueado').prop('readonly', false);

                // Desmascara os dados de cobrança de instâncias
                if (dados.pix_email_recorrencia) {
                    $('#inputPixEmailRecorrencia').val(dados.pix_email_recorrencia).attr('type', 'email').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                    $('#txtHeroPixRecorrencia').replaceWith(`<strong class="font-monospace text-primary" id="txtHeroPixRecorrencia">${dados.pix_email_recorrencia}</strong>`);
                    $('#txtGuiaPixRecorrencia').replaceWith(`na conta <strong class="text-primary font-monospace" id="txtGuiaPixRecorrencia">${dados.pix_email_recorrencia}</strong>`);
                    $('#badgePixRecorrenciaLock').removeClass('bg-danger').addClass('bg-success').html('<i class="fas fa-unlock"></i> Liberado');
                    $('#btnPixRecorrenciaLock').attr('onclick', 'bloquearSegurancaWpp()').attr('title', 'Bloquear e Ofuscar').html('<i class="fas fa-lock"></i>');
                }

                if (dados.custo_instancia_extra !== undefined) {
                    $('#inputCustoInstanciaExtra').val(Number(dados.custo_instancia_extra).toFixed(2)).attr('type', 'number').removeClass('tarja-dev-bloqueado text-secondary').addClass('text-danger').prop('readonly', false);
                    $('#badgeCustoExtraLock').removeClass('bg-danger').addClass('bg-success').html('<i class="fas fa-unlock"></i> Liberado');
                    $('#btnCustoExtraLock').attr('onclick', 'bloquearSegurancaWpp()').attr('title', 'Bloquear e Ofuscar').html('<i class="fas fa-lock"></i>');
                }

                if (dados.evolution_mp_access_token) {
                    $('#inputEvolutionMpAccessToken').val(dados.evolution_mp_access_token).attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                    $('#badgeMpTokenLock').removeClass('bg-danger').addClass('bg-success').html('<i class="fas fa-unlock"></i> Liberado');
                    $('#btnMpTokenLock').attr('onclick', 'bloquearSegurancaWpp()').attr('title', 'Bloquear e Ofuscar').html('<i class="fas fa-lock"></i>');
                }

                // Desmascara os campos do Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor)
                $('#inputDevMpAtivo').prop('disabled', false).prop('checked', !!dados.dev_mp_ativo);
                $('#inputDevMpAmbiente').prop('disabled', false).val(dados.dev_mp_ambiente || 'producao');
                alternarAmbienteMpDev(dados.dev_mp_ambiente || 'producao');
                $('#inputDevMpBeneficiario').val(dados.dev_mp_beneficiario || 'FOCO 7 TECNOLOGIA').attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpPublicKeyProd').val(dados.dev_mp_public_key_prod || '').attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpAccessTokenProd').val(dados.dev_mp_access_token_prod || '').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpClientIdProd').val(dados.dev_mp_client_id_prod || '').attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpClientSecretProd').val(dados.dev_mp_client_secret_prod || '').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpPublicKeySandbox').val(dados.dev_mp_public_key_sandbox || '').attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpAccessTokenSandbox').val(dados.dev_mp_access_token_sandbox || '').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpClientIdSandbox').val(dados.dev_mp_client_id_sandbox || '').attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpClientSecretSandbox').val(dados.dev_mp_client_secret_sandbox || '').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputDevMpWebhookSecret').val(dados.dev_mp_webhook_secret || '').removeClass('tarja-dev-bloqueado').prop('readonly', false);

                $('#modalInstanciaToken').val(dados.apikey);

                $('#txtInstanciaNome').text(dados.instance);
                $('#txtApiUrl').text(dados.api_url);

                $('#badgeStatusSegurancaWpp').removeClass('bg-danger').addClass('bg-success');
                $('#badgeStatusSegurancaWpp i').removeClass('fa-lock').addClass('fa-unlock');
                $('#badgeTextoSegurancaWpp').text('Acesso Liberado');
                $('#btnAcaoSegurancaWpp').attr('onclick', 'bloquearSegurancaWpp()').removeClass('btn-danger').addClass('btn-outline-secondary');
                $('#btnTextoSegurancaWpp').text('Bloquear Acesso');
                $('#btnAcaoSegurancaWpp i').removeClass('fa-key').addClass('fa-lock');
                $('#alertaTarjaWpp').addClass('d-none');

                window.foco7Notify.success('Credenciais e Módulo Mercado Pago do Desenvolvedor desbloqueados com sucesso!');
                setTimeout(function() {
                    location.reload();
                }, 900);
            }
        });
    }

    function abrirAbaMpDev() {
        var tabEl = document.getElementById('tab-mp-dev-btn');
        if (tabEl) {
            var tab = new bootstrap.Tab(tabEl);
            tab.show();
            tabEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function copiarWebhookMpDevUrl() {
        var input = document.getElementById('inputWebhookMpDevUrl');
        if (!input) return;
        input.select();
        navigator.clipboard.writeText(input.value).then(function () {
            window.foco7Notify.success('URL de Webhook do Módulo Mercado Pago (Desenvolvedor) copiada!');
        });
    }

    function testarConexaoMpDev() {
        var $btn = $('#btnTestarMpDev');
        var origHtml = $btn.html();
        var ambienteAtivo = $('#inputDevMpAmbiente').val() || 'producao';
        var tokenAtivo = ambienteAtivo === 'sandbox'
            ? $('#inputDevMpAccessTokenSandbox').val()
            : $('#inputDevMpAccessTokenProd').val();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Testando ' + (ambienteAtivo === 'sandbox' ? 'Sandbox' : 'Produção') + '...');

        $.ajax({
            url: '{{ route("admin.whatsapp.testar_mp_dev") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                ambiente: ambienteAtivo,
                token: tokenAtivo
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                Swal.fire({
                    icon: 'success',
                    title: 'Mercado Pago (Desenvolvedor) Conectado!',
                    text: res.message,
                    confirmButtonColor: '#28a745'
                });
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var msg = xhr.responseJSON?.message || 'Falha ao testar conexão com o Mercado Pago do Desenvolvedor.';
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção no Módulo MP (Dev)',
                    text: msg,
                    confirmButtonColor: '#d90000'
                });
            }
        });
    }

    function bloquearSegurancaWpp() {
        $.ajax({
            url: '{{ route("admin.whatsapp.bloquear_seguranca") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                window.foco7Notify.info(res.message);
                location.reload();
            }
        });
    }

    // Teste de latência em milissegundos
    function testarLatenciaServidor() {
        var $btn = $('#btnTestarLatencia');
        var $icon = $('#iconLatencia');
        $icon.addClass('fa-spin');
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.whatsapp.testar_latencia") }}',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                var ms = res.latencia_ms || 0;
                $('#txtLatenciaMs').text('Ping: ' + ms + 'ms');
                window.foco7Notify.success('Latência do Servidor Evolution Go: ' + ms + 'ms');
            },
            error: function() {
                $('#txtLatenciaMs').text('Ping: Erro');
                window.foco7Notify.error('Falha ao medir latência do servidor.');
            },
            complete: function() {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
            }
        });
    }

    // Consulta de status da API
    function verificarStatusApi(showNotification) {
        var $btn = $('#btnVerificarStatus');
        var $icon = $('#iconVerificarStatus');
        $icon.addClass('fa-spin');
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.whatsapp.status") }}',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.franqueado) {
                    atualizarCardFranqueadoAtivoUI(res.franqueado);
                }

                if (res.conectado) {
                    $('#badgeStatusGeral').removeClass('bg-secondary bg-danger bg-warning').addClass('bg-success');
                    $('#badgeStatusPulse').removeClass('pulse-offline pulse-warning').addClass('pulse-online');
                    $('#badgeStatusTexto').text('Conectado');
                    $('#statusIconBox').css('background-color', 'rgba(25, 135, 84, 0.15)');
                    $('#statusIcon').removeClass('text-secondary text-danger text-warning').addClass('text-success');

                    if (res.numero_conectado || res.owner) {
                        var num = res.numero_conectado || res.owner;
                        $('#txtNumeroConectadoHero').text(num);
                        $('#badgeNumeroConectadoHero').removeClass('d-none');
                        $('#txtOwnerPrincipalTabela').text(num).removeClass('d-none');
                    }

                    // Atualiza linha da tabela de instâncias
                    $('#badgeStatusPrincipalTabela').removeClass('bg-secondary bg-danger bg-warning').addClass('bg-success');
                    $('#pulseStatusPrincipalTabela').removeClass('pulse-offline pulse-warning').addClass('pulse-online');
                    $('#txtStatusPrincipalTabela').text('Conectado');

                    if (showNotification) {
                        window.foco7Notify.success('WhatsApp está conectado e pronto para envios!');
                    }
                } else if (res.estado === 'conectando') {
                    $('#badgeStatusGeral').removeClass('bg-secondary bg-success bg-danger').addClass('bg-warning text-dark');
                    $('#badgeStatusPulse').removeClass('pulse-online pulse-offline').addClass('pulse-warning');
                    $('#badgeStatusTexto').text('Conectando...');
                    $('#badgeNumeroConectadoHero').addClass('d-none');
                    $('#txtOwnerPrincipalTabela').text('').addClass('d-none');

                    $('#badgeStatusPrincipalTabela').removeClass('bg-secondary bg-success bg-danger').addClass('bg-warning text-dark');
                    $('#pulseStatusPrincipalTabela').removeClass('pulse-online pulse-offline').addClass('pulse-warning');
                    $('#txtStatusPrincipalTabela').text('Conectando...');

                    if (showNotification) {
                        window.foco7Notify.warning('Instância aguardando leitura do QR Code.');
                    }
                } else {
                    $('#badgeStatusGeral').removeClass('bg-success bg-warning').addClass('bg-secondary');
                    $('#badgeStatusPulse').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
                    $('#badgeStatusTexto').text('Desconectado');
                    $('#statusIconBox').css('background-color', 'rgba(108, 117, 125, 0.15)');
                    $('#statusIcon').removeClass('text-success text-danger text-warning').addClass('text-secondary');
                    $('#badgeNumeroConectadoHero').addClass('d-none');
                    $('#txtNumeroConectadoHero').text('');
                    $('#txtOwnerPrincipalTabela').text('').addClass('d-none');

                    $('#badgeStatusPrincipalTabela').removeClass('bg-success bg-warning').addClass('bg-secondary');
                    $('#pulseStatusPrincipalTabela').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
                    $('#txtStatusPrincipalTabela').text('Desconectado');

                    if (showNotification) {
                        window.foco7Notify.info('WhatsApp está desconectado.');
                    }
                }
            },
            error: function () {
                $('#badgeStatusGeral').removeClass('bg-success bg-warning bg-secondary').addClass('bg-danger');
                $('#badgeStatusPulse').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
                $('#badgeStatusTexto').text('Erro de Conexão');
                $('#badgeNumeroConectadoHero').addClass('d-none');

                $('#badgeStatusPrincipalTabela').removeClass('bg-success bg-warning').addClass('bg-danger');
                $('#pulseStatusPrincipalTabela').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
                $('#txtStatusPrincipalTabela').text('Erro');

                if (showNotification) {
                    window.foco7Notify.error('Não foi possível conectar ao servidor Evolution API.');
                }
            },
            complete: function () {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
            }
        });
    }

    var instanciaModalAtual = '';

    function limparDadosVisuaisInstanciaLocal(nomeInstancia) {
        $('#imgQrCodeDisplay').attr('src', '');
        $('#txtCodigoPareamentoDisplay').text('----');
        $('#boxCodigoPareamentoGerado').hide();
        $('#inputNumeroPareamento').val('');
        $('#txtModalNumeroConectado').text('');

        var instPrincipal = ($('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}').toLowerCase();
        if (!nomeInstancia || String(nomeInstancia).toLowerCase() === instPrincipal) {
            $('#badgeStatusGeral').removeClass('bg-success bg-warning bg-danger').addClass('bg-secondary');
            $('#badgeStatusPulse').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
            $('#badgeStatusTexto').text('Desconectado');
            $('#statusIconBox').css('background-color', 'rgba(108, 117, 125, 0.15)');
            $('#statusIcon').removeClass('text-success text-danger text-warning').addClass('text-secondary');
            $('#badgeNumeroConectadoHero').addClass('d-none');
            $('#txtNumeroConectadoHero').text('');

            $('#badgeStatusPrincipalTabela').removeClass('bg-success bg-warning bg-danger').addClass('bg-secondary');
            $('#pulseStatusPrincipalTabela').removeClass('pulse-online pulse-warning').addClass('pulse-offline');
            $('#txtStatusPrincipalTabela').text('Desconectado');
            $('#txtOwnerPrincipalTabela').text('').addClass('d-none');
        }

        if (nomeInstancia) {
            $('.owner-extra-label[data-instancia="' + nomeInstancia + '"]').text('').addClass('d-none');
        }
    }

    function alternarModoConexaoModal(modo) {
        if (modo === 'numero') {
            $('#btnTabModoQr').removeClass('active');
            $('#btnTabModoNumero').addClass('active');
            $('#subPainelModoQr').hide();
            $('#subPainelModoNumero').fadeIn(150);
            setTimeout(function () {
                $('#inputNumeroPareamento').trigger('focus');
            }, 180);
        } else {
            $('#btnTabModoNumero').removeClass('active');
            $('#btnTabModoQr').addClass('active');
            $('#subPainelModoNumero').hide();
            $('#subPainelModoQr').fadeIn(150);
        }
    }

    function abrirAbaNumeroDiretoNoModal() {
        $('#qrErroBox').hide();
        $('#qrLoadingBox').hide();
        $('#qrConectadoBox').hide();
        $('#qrCodeContentBox').show();
        alternarModoConexaoModal('numero');
        iniciarPollingStatusQr();
    }

    // Conectar e abrir modal com QR Code e opção de Pareamento por Número
    function conectarEabrirQr(instanciaOpcional, forcarQr) {
        instanciaModalAtual = instanciaOpcional || $('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}';
        $('#txtModalNomeInstanciaConexao').text(instanciaModalAtual);

        var modalEl = document.getElementById('modalQrCode');
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();

        $('#txtQrLoadingTitulo').text(forcarQr ? 'Limpando sessão anterior e gerando novo QR Code...' : 'Gerando QR Code na Evolution API Go...');
        $('#qrLoadingBox').show();
        $('#qrCodeContentBox').hide();
        $('#qrConectadoBox').hide();
        $('#qrErroBox').hide();
        $('#boxCodigoPareamentoGerado').hide();
        alternarModoConexaoModal('qr');

        $.ajax({
            url: '{{ route("admin.whatsapp.conectar") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                instancia: instanciaModalAtual,
                forcar_qr: forcarQr ? 1 : 0
            },
            dataType: 'json',
            success: function (res) {
                if (res.token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
                    $('#inputApiKey').val(res.token);
                }
                if (res.instancia) {
                    instanciaModalAtual = res.instancia;
                    $('#txtModalNomeInstanciaConexao').text(res.instancia);
                }
                if (res.franqueado) {
                    preencherDadosFranqueadoModalQr(res.franqueado);
                } else {
                    carregarDadosFranqueadoModalQrRemoto(instanciaModalAtual);
                }
                if (res.conectado && !forcarQr) {
                    $('#qrLoadingBox').hide();
                    $('#qrCodeContentBox').hide();
                    $('#qrErroBox').hide();
                    if (res.numero_conectado || res.owner) {
                        $('#txtModalNumeroConectado').text(res.numero_conectado || res.owner);
                    }
                    $('#qrConectadoBox').show();
                    verificarStatusApi(false);
                    window.foco7Notify.success(res.message || 'Esta instância já está conectada!');
                    return;
                }
                if (ehQrBase64Valido(res.qrcode)) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    recarregarQrCode(false);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao iniciar sessão de conexão.';
                $('#qrLoadingBox').hide();
                $('#txtMensagemErroQr').text(msg);
                $('#qrErroBox').show();
            }
        });
    }

    function ehQrBase64Valido(str) {
        if (!str || typeof str !== 'string') return false;
        var s = str.trim();
        if (s.length < 100 || s.startsWith('http://') || s.startsWith('https://')) return false;
        return s.startsWith('data:image/') || s.startsWith('iVBORw0KGgo');
    }

    function exibirQrCode(base64) {
        if (!ehQrBase64Valido(base64)) {
            recarregarQrCode(false);
            return;
        }
        var src = base64.trim();
        if (!src.startsWith('data:image')) {
            src = 'data:image/png;base64,' + src;
        }
        $('#imgQrCodeDisplay').attr('src', src);
        $('#qrLoadingBox').hide();
        $('#qrErroBox').hide();
        $('#qrConectadoBox').hide();
        $('#qrCodeContentBox').show();
    }

    function recarregarQrCode(forcarNovo) {
        if (forcarNovo) {
            conectarEabrirQr(instanciaModalAtual, true);
            return;
        }
        $('#qrCodeContentBox').hide();
        $('#qrLoadingBox').show();

        $.ajax({
            url: '{{ route("admin.whatsapp.qr") }}',
            type: 'GET',
            data: {
                instancia: instanciaModalAtual,
                forcar_qr: 0
            },
            dataType: 'json',
            success: function (res) {
                if (ehQrBase64Valido(res.qrcode)) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    verificarSeConectou();
                }
            },
            error: function () {
                verificarSeConectou();
            }
        });
    }

    function solicitarCodigoPareamento() {
        var telefone = ($('#inputNumeroPareamento').val() || '').trim();
        if (telefone.replace(/\D/g, '').length < 10) {
            window.foco7Notify.warning('Informe o número do WhatsApp com DDD (ex: 5521999999999).');
            $('#inputNumeroPareamento').trigger('focus');
            return;
        }

        var $btn = $('#btnGerarCodigoPareamento');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Gerando...');
        $('#boxCodigoPareamentoGerado').hide();

        $.ajax({
            url: '{{ route("admin.whatsapp.parear_numero") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                telefone: telefone,
                instancia: instanciaModalAtual
            },
            dataType: 'json',
            success: function (res) {
                if (res.success && res.pairing_code) {
                    $('#txtCodigoPareamentoDisplay').text(res.pairing_code);
                    $('#boxCodigoPareamentoGerado').fadeIn(200);
                    iniciarPollingStatusQr();
                    window.foco7Notify.success(res.message || 'Código gerado! Digite no seu WhatsApp.');
                } else {
                    window.foco7Notify.error(res.message || 'Não foi possível gerar o código de pareamento.');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao solicitar código de pareamento para o número informado.';
                window.foco7Notify.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-key me-1"></i> Gerar Código');
            }
        });
    }

    function copiarCodigoPareamento() {
        var codigo = ($('#txtCodigoPareamentoDisplay').text() || '').trim();
        if (!codigo || codigo === '----') return;
        navigator.clipboard.writeText(codigo).then(function () {
            window.foco7Notify.success('Código "' + codigo + '" copiado para a área de transferência!');
        });
    }

    function iniciarPollingStatusQr() {
        pararPollingQr();
        pollingTimer = setInterval(function () {
            $.ajax({
                url: '{{ route("admin.whatsapp.status") }}',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.franqueado) {
                    atualizarCardFranqueadoAtivoUI(res.franqueado);
                }

                if (res.conectado) {
                        pararPollingQr();
                        $('#qrLoadingBox').hide();
                        $('#qrCodeContentBox').hide();
                        $('#qrErroBox').hide();
                        if (res.numero_conectado || res.owner) {
                            $('#txtModalNumeroConectado').text(res.numero_conectado || res.owner);
                        }
                        $('#qrConectadoBox').show();
                        verificarStatusApi(false);
                        window.foco7Notify.success('WhatsApp pareado e conectado com sucesso!');
                    }
                }
            });
        }, 3500);
    }

    function pararPollingQr() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    function verificarSeConectou() {
        $.ajax({
            url: '{{ route("admin.whatsapp.status") }}',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                $('#qrLoadingBox').hide();
                if (res.franqueado) {
                    atualizarCardFranqueadoAtivoUI(res.franqueado);
                }

                if (res.conectado) {
                    if (res.numero_conectado || res.owner) {
                        $('#txtModalNumeroConectado').text(res.numero_conectado || res.owner);
                    }
                    $('#qrConectadoBox').show();
                    verificarStatusApi(false);
                } else if (ehQrBase64Valido(res.qrcode)) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    $('#txtMensagemErroQr').text('Nenhum QR Code ativo no momento. Clique em "Limpar Sessão & Gerar Novo QR Code" ou conecte com seu número.');
                    $('#qrErroBox').show();
                }
            },
            error: function () {
                $('#qrLoadingBox').hide();
                $('#qrErroBox').show();
            }
        });
    }

    function desconectarELimparParaNovoQr() {
        var alvo = instanciaModalAtual || $('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}';
        $('#qrConectadoBox').hide();
        $('#txtQrLoadingTitulo').text('Desconectando e limpando todos os dados da instância...');
        $('#qrLoadingBox').show();

        $.ajax({
            url: '{{ route("admin.whatsapp.desconectar") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                instancia: alvo
            },
            dataType: 'json',
            complete: function () {
                limparDadosVisuaisInstanciaLocal(alvo);
                conectarEabrirQr(alvo, true);
            }
        });
    }

    function autoCriarOuSincronizarInstanciaModal() {
        $('#qrErroBox').hide();
        $('#qrLoadingBox').show();
        $('#qrLoadingBox h6').text('Sincronizando com o Servidor Evolution API...');

        $.ajax({
            url: '{{ route("admin.whatsapp.sincronizar_instancias") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
                $('#qrLoadingBox').hide();
                if (res.franqueado) {
                    atualizarCardFranqueadoAtivoUI(res.franqueado);
                }

                if (res.conectado) {
                    $('#qrConectadoBox').show();
                    verificarStatusApi(false);
                    window.foco7Notify.success(res.message || 'Instância sincronizada e já conectada!');
                } else {
                    window.foco7Notify.info(res.message);
                    verificarStatusApi(true);
                }
            },
            error: function () {
                var nome = $('#inputInstance').val() || 'foco7';
                var token = $('#inputApiKey').val() || '';
                $.ajax({
                    url: '{{ route("admin.whatsapp.criar_instancia") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nome: nome,
                        token: token
                    },
                    dataType: 'json',
                    success: function (res) {
                        $('#qrLoadingBox').hide();
                        window.foco7Notify.success(res.message);
                        if (res.qrcode) {
                            exibirQrCode(res.qrcode);
                            iniciarPollingStatusQr();
                        } else {
                            verificarSeConectou();
                        }
                    },
                    error: function (xhr) {
                        $('#qrLoadingBox').hide();
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao sincronizar/criar instância.';
                        $('#txtMensagemErroQr').text(msg);
                        $('#qrErroBox').show();
                    }
                });
            }
        });
    }

    function listarInstanciasServidor() {
        Swal.fire({
            title: 'Consultando Instâncias do Painel...',
            html: '<div class="py-3"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i><p class="text-secondary small mb-0">Buscando exclusivamente as instâncias criadas neste painel e os dados dos franqueados...</p></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            url: '{{ route("admin.whatsapp.sincronizar_instancias") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
                if (!res.success || !res.instancias) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nenhuma Instância',
                        text: res.message || 'Nenhuma instância criada por este painel.',
                        confirmButtonColor: '#d90000'
                    });
                    return;
                }

                var lista = (res.instancias && Array.isArray(res.instancias.data)) ? res.instancias.data : res.instancias;
                if (!Array.isArray(lista) && typeof lista === 'object') {
                    lista = Object.values(lista);
                }

                if (lista.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Nenhuma Instância neste Painel',
                        text: 'Nenhuma instância foi provisionada por este painel ainda.',
                        confirmButtonColor: '#d90000'
                    });
                    return;
                }

                var htmlInst = `
                    <div class="alert alert-info py-2 px-3 mb-2 text-start small border-0" style="background: rgba(13, 110, 253, 0.08);">
                        <i class="fas fa-shield-alt text-primary me-1"></i>
                        Exibindo <strong>exclusivamente as instâncias criadas por este painel</strong> (${lista.length}) com identificação do franqueado.
                    </div>
                    <div class="list-group text-start my-2" style="max-height: 380px; overflow-y: auto;">
                `;
                lista.forEach(function (inst) {
                    if (!inst || typeof inst !== 'object') return;
                    var nome = inst.name || (inst.instance && inst.instance.name) || (inst.instanceName) || 'Sem Nome';
                    var status = inst.status || (inst.instance && inst.instance.status) || (inst.state) || (inst.connected ? 'open' : 'close');
                    var token = inst.token || (inst.instance && inst.instance.token) || (inst.apikey) || '';
                    var owner = String(inst.owner || inst.jid || '').replace('@s.whatsapp.net', '').replace('@c.us', '');
                    var isConnected = (inst.connected === true || inst.Connected === true || status === 'open' || status === 'connected' || status === 'conectado');
                    var f = inst.franqueado || {};

                    var statusBadge = isConnected 
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Conectado (open)</span>' 
                        : '<span class="badge bg-danger">Desconectado (close)</span>';
                    var ownerBadge = owner ? `<span class="badge bg-dark-subtle text-body font-monospace ms-1"><i class="fab fa-whatsapp text-success me-1"></i>${owner}</span>` : '';
                    var tipoBadge = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1">${f.tipo_label || (inst.eh_principal_painel ? 'Admin / Painel' : 'Franqueado')}</span>`;

                    var nomeFranq = f.nome_franqueado || 'Administrador do Painel';
                    var empFranq = f.empresa_franqueado && f.empresa_franqueado !== nomeFranq ? ` &bull; <span class="text-secondary">${f.empresa_franqueado}</span>` : '';
                    var telFranq = f.telefone_franqueado ? `<span class="me-2"><i class="fab fa-whatsapp text-success me-1"></i>${f.telefone_franqueado}</span>` : '';
                    var emailFranq = f.email_franqueado ? `<span class="me-2"><i class="fas fa-envelope text-muted me-1"></i>${f.email_franqueado}</span>` : '';
                    var domLic = '';
                    if (f.chave_licenca) {
                        domLic += `<span class="badge bg-secondary-subtle text-body font-monospace me-1" style="font-size: 0.65rem;"><i class="fas fa-key text-warning me-1"></i>${f.chave_licenca}</span>`;
                    }
                    if (f.dominio_franqueado) {
                        domLic += `<span class="badge bg-secondary-subtle text-body me-1" style="font-size: 0.65rem;"><i class="fas fa-globe text-info me-1"></i>${f.dominio_franqueado}</span>`;
                    }
                    if (f.data_vencimento_fmt) {
                        domLic += `<span class="badge bg-light text-dark border" style="font-size: 0.65rem;"><i class="fas fa-calendar-alt text-danger me-1"></i>Vence: ${f.data_vencimento_fmt}</span>`;
                    }

                    htmlInst += `
                        <div class="list-group-item list-group-item-action p-3 mb-2 border rounded shadow-xs">
                            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                <div>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <strong class="text-body font-monospace fs-6">${nome}</strong>
                                        ${tipoBadge}
                                    </div>
                                    <div class="mt-1">${statusBadge}${ownerBadge}</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary fw-semibold" onclick="Swal.close(); abrirModalEditarFranqueadoInstancia('${nome}')" title="Ver / Editar Informações do Franqueado">
                                        <i class="fas fa-user-tie me-1"></i> Franqueado
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-dark fw-semibold" onclick="Swal.close(); conectarEabrirQr('${nome}')" title="Conectar QR Code ou Número">
                                        <i class="fas fa-qrcode me-1"></i> Conectar
                                    </button>
                                    <button type="button" class="btn btn-xs btn-success fw-bold" onclick="selecionarInstanciaServidor('${nome}', '${token}')">
                                        <i class="fas fa-check me-1"></i> Selecionar
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-top small">
                                <div class="fw-semibold text-body">
                                    <i class="fas fa-id-badge text-primary me-1"></i> ${nomeFranq}${empFranq}
                                </div>
                                ${(telFranq || emailFranq) ? `<div class="text-secondary mt-1" style="font-size: 0.75rem;">${telFranq}${emailFranq}</div>` : ''}
                                ${domLic ? `<div class="mt-1">${domLic}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
                htmlInst += '</div>';

                verificarStatusApi(false);

                Swal.fire({
                    title: 'Instâncias Criadas neste Painel',
                    html: htmlInst,
                    width: 640,
                    showCloseButton: true,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao consultar instâncias do painel.';
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Comunicação',
                    text: msg,
                    confirmButtonColor: '#d90000'
                });
            }
        });
    }

    function selecionarInstanciaServidor(nome, token) {
        Swal.close();
        $('#inputInstance').val(nome);
        $('#txtInstanciaNome').text(nome);
        if (token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
            $('#inputApiKey').val(token);
        }
        carregarDadosFranqueadoModalQrRemoto(nome, true);
        window.foco7Notify.success('Instância "' + nome + '" selecionada! Os dados do franqueado foram carregados na conexão.');
    }

    function carregarDadosFranqueadoModalQrRemoto(nomeInstancia, atualizarCardPrincipal) {
        if (!nomeInstancia) return;
        var urlBase = '{{ url("admin/whatsapp/instancias/franqueado") }}/' + encodeURIComponent(nomeInstancia);
        $.ajax({
            url: urlBase,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res && res.franqueado) {
                    preencherDadosFranqueadoModalQr(res.franqueado);
                    if (atualizarCardPrincipal) {
                        atualizarCardFranqueadoAtivoUI(res.franqueado);
                    }
                }
            }
        });
    }

    function preencherDadosFranqueadoModalQr(f) {
        if (!f || typeof f !== 'object') return;
        var nome = f.nome_franqueado || 'Administrador / Operação Local';
        var empresa = f.empresa_franqueado || f.tipo_label || 'Sistema FOCO 7';
        var tipo = f.tipo_label || 'Franqueado / Titular';
        var tel = f.telefone_franqueado || 'Não informado';
        var email = f.email_franqueado || 'Não informado';
        var domLic = f.chave_licenca || f.dominio_franqueado || 'Painel Principal';
        var venc = f.data_vencimento_fmt || '-';

        $('#txtQrFranqueadoNome, #txtModalQrFranqueadoNome').text(nome);
        $('#txtQrFranqueadoEmpresa, #txtModalQrFranqueadoEmpresa').text(empresa);
        $('#txtQrFranqueadoTipoBadge, #badgeModalQrFranqueadoTipo').html('<i class="fas fa-user-tie me-1"></i> ' + tipo);
        $('#txtQrFranqueadoTelefone, #txtModalQrFranqueadoTelefone').text(tel);
        $('#txtModalQrFranqueadoEmail').text(email);
        $('#txtQrFranqueadoDominio, #txtModalQrFranqueadoLicenca').text(domLic);
        $('#txtQrFranqueadoVencimento').text(venc);
        $('#boxInfoFranqueadoModalQr').show();
    }

    function atualizarCardFranqueadoAtivoUI(f) {
        if (!f || typeof f !== 'object') return;
        $('#txtFranqueadoAtivoNome').text(f.nome_franqueado || 'Não informado');
        $('#txtFranqueadoAtivoEmpresa').text(f.empresa_franqueado || 'Operação Direta');
        if (f.documento_franqueado) {
            $('#txtFranqueadoAtivoDoc').text('Doc: ' + f.documento_franqueado);
        }
        $('#txtFranqueadoAtivoTelefone').text(f.telefone_franqueado || 'Sem telefone');
        $('#txtFranqueadoAtivoEmail').text(f.email_franqueado || 'Sem e-mail');
        $('#txtFranqueadoAtivoDominio').text(f.dominio_franqueado || 'Domínio local');
        $('#txtFranqueadoAtivoLicenca').text(f.chave_licenca ? 'Licença: ' + f.chave_licenca : 'Instância Direta do Painel');
    }

    function preencherCamposPorFranqueadoSelecionado(selectEl, prefixo) {
        var $opt = $(selectEl).find('option:selected');
        if (!$opt.length || !$opt.val()) return;

        var nome = $opt.data('nome') || '';
        var empresa = $opt.data('empresa') || '';
        var doc = $opt.data('documento') || '';
        var tel = $opt.data('telefone') || '';
        var email = $opt.data('email') || '';
        var dom = $opt.data('dominio') || '';
        var chave = $opt.data('chave') || '';

        if (prefixo === 'criar') {
            $('#modalInstanciaNomeResp').val(nome);
            $('#modalInstanciaEmpresaFranqueado').val(empresa);
            $('#modalInstanciaDocFranqueado').val(doc);
            $('#modalInstanciaTelResp').val(tel);
            $('#modalInstanciaEmailResp').val(email);
            $('#modalInstanciaDominioFranqueado').val(dom);
            $('#modalInstanciaChaveLicenca').val(chave);
            $('#modalInstanciaTipo').val('franqueado');

            if (!$('#modalInstanciaNome').val()) {
                var slugSugerido = String(empresa || nome)
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
                if (slugSugerido) {
                    $('#modalInstanciaNome').val(slugSugerido);
                }
            }
        } else if (prefixo === 'editar') {
            $('#editFranqueadoNome').val(nome);
            $('#editFranqueadoEmpresa').val(empresa);
            $('#editFranqueadoDoc').val(doc);
            $('#editFranqueadoTelefone').val(tel);
            $('#editFranqueadoEmail').val(email);
            $('#editFranqueadoDominio').val(dom);
            $('#editFranqueadoChave').val(chave);
            $('#editFranqueadoTipo').val('franqueado');
        }
    }

    function aoMudarTipoModalInstancia() {
        var tipo = $('#modalInstanciaTipo').val();
        if (tipo === 'propria_admin') {
            $('#modalInstanciaLicenciadoId').val('');
        }
    }

    function abrirModalEditarFranqueadoInstancia(nomeInstancia) {
        var alvo = nomeInstancia || $('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}';
        $('#editFranqueadoInstanciaNome').val(alvo);
        $('#txtEditFranqueadoInstanciaLabel').text(alvo);

        var modalEl = document.getElementById('modalEditarFranqueadoInstancia');
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();

        var urlBase = '{{ url("admin/whatsapp/instancias/franqueado") }}/' + encodeURIComponent(alvo);
        $.ajax({
            url: urlBase,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                var f = (res && res.franqueado) ? res.franqueado : {};
                $('#editFranqueadoLicenciadoId').val(f.licenciado_id || '');
                $('#editFranqueadoTipo').val(f.tipo || 'franqueado');
                $('#editFranqueadoNome').val(f.nome_franqueado || '');
                $('#editFranqueadoEmpresa').val(f.empresa_franqueado || '');
                $('#editFranqueadoDoc').val(f.documento_franqueado || '');
                $('#editFranqueadoTelefone').val(f.telefone_franqueado || '');
                $('#editFranqueadoEmail').val(f.email_franqueado || '');
                $('#editFranqueadoDominio').val(f.dominio_franqueado || '');
                $('#editFranqueadoChave').val(f.chave_licenca || '');
            },
            error: function () {
                window.foco7Notify.warning('Não foi possível carregar dados prévios do franqueado. Preencha os campos manualmente.');
            }
        });
    }

    function salvarDadosFranqueadoModal() {
        var $btn = $('#btnSalvarDadosFranqueadoInstancia');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

        $.ajax({
            url: '{{ route("admin.whatsapp.instancias.salvar_franqueado") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                instancia: $('#editFranqueadoInstanciaNome').val(),
                licenciado_id: $('#editFranqueadoLicenciadoId').val(),
                tipo: $('#editFranqueadoTipo').val(),
                nome_franqueado: $('#editFranqueadoNome').val(),
                empresa_franqueado: $('#editFranqueadoEmpresa').val(),
                documento_franqueado: $('#editFranqueadoDoc').val(),
                telefone_franqueado: $('#editFranqueadoTelefone').val(),
                email_franqueado: $('#editFranqueadoEmail').val(),
                dominio_franqueado: $('#editFranqueadoDominio').val(),
                chave_licenca: $('#editFranqueadoChave').val()
            },
            dataType: 'json',
            success: function (res) {
                $('#modalEditarFranqueadoInstancia').modal('hide');
                window.foco7Notify.success(res.message || 'Dados do franqueado vinculados à instância com sucesso!');
                if (res.franqueado) {
                    var instAtiva = ($('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}').toLowerCase();
                    if (String(res.franqueado.instancia || '').toLowerCase() === instAtiva) {
                        atualizarCardFranqueadoAtivoUI(res.franqueado);
                    }
                    preencherDadosFranqueadoModalQr(res.franqueado);
                }
                setTimeout(function () {
                    location.reload();
                }, 900);
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao salvar dados do franqueado.';
                window.foco7Notify.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Dados do Franqueado');
            }
        });
    }

    function desconectarInstancia(instanciaOpcional) {
        var nomeAlvo = instanciaOpcional || $('#inputInstance').val() || '{{ $configs["instance"] ?? "foco7" }}';
        window.confirmarAcao(
            'Desconectar e Limpar Instância?',
            'Toda a sessão do aparelho vinculado, QR Code anterior e cache da instância "' + nomeAlvo + '" serão limpos no servidor Evolution Go e no painel.',
            'warning',
            function () {
                Swal.fire({
                    title: 'Desconectando e Limpando...',
                    html: '<div class="py-3"><i class="fas fa-spinner fa-spin fa-2x text-danger mb-2"></i><p class="small text-secondary mb-0">Encerrando sessão do aparelho e limpando dados da instância...</p></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                $.ajax({
                    url: '{{ route("admin.whatsapp.desconectar") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        instancia: nomeAlvo
                    },
                    dataType: 'json',
                    success: function (res) {
                        limparDadosVisuaisInstanciaLocal(nomeAlvo);
                        Swal.fire({
                            icon: 'success',
                            title: 'Instância Desconectada e Limpa!',
                            text: res.message || 'Todos os dados de sessão da instância foram limpos. Agora você pode conectar lendo um novo QR Code ou digitando o número do WhatsApp.',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fas fa-qrcode me-1"></i> Conectar Agora (QR / Número)',
                            cancelButtonText: 'Fechar'
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                conectarEabrirQr(nomeAlvo, true);
                            }
                        });
                    },
                    error: function () {
                        limparDadosVisuaisInstanciaLocal(nomeAlvo);
                        Swal.close();
                        window.foco7Notify.error('Sessão local limpa, mas houve falha ao comunicar com o servidor.');
                    }
                });
            }
        );
    }

    function executarCriacaoInstancia() {
        var nome = $('#modalInstanciaNome').val();
        var token = $('#modalInstanciaToken').val();
        var tipo = $('#modalInstanciaTipo').val();
        var licenciadoId = $('#modalInstanciaLicenciadoId').val();
        var nomeResp = $('#modalInstanciaNomeResp').val();
        var empresaFranqueado = $('#modalInstanciaEmpresaFranqueado').val();
        var docFranqueado = $('#modalInstanciaDocFranqueado').val();
        var telResp = $('#modalInstanciaTelResp').val();
        var emailResp = $('#modalInstanciaEmailResp').val();
        var dominioFranqueado = $('#modalInstanciaDominioFranqueado').val();
        var chaveLicenca = $('#modalInstanciaChaveLicenca').val();

        var $btn = $('#btnConfirmarCriarInstancia');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Criando...');

        $.ajax({
            url: '{{ route("admin.whatsapp.criar_instancia") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nome: nome,
                token: token,
                tipo: tipo,
                licenciado_id: licenciadoId,
                nome_responsavel: nomeResp,
                empresa_franqueado: empresaFranqueado,
                documento_franqueado: docFranqueado,
                telefone_responsavel: telResp,
                email_responsavel: emailResp,
                dominio_franqueado: dominioFranqueado,
                chave_licenca: chaveLicenca
            },
            dataType: 'json',
            success: function (res) {
                $('#modalCriarInstancia').modal('hide');
                window.foco7Notify.success(res.message);

                // Se exigir pagamento inicial via PIX no Módulo Mercado Pago do Desenvolvedor, abre o Checkout PIX automaticamente
                if (res.requer_pagamento_imediato && res.instancia) {
                    setTimeout(function() {
                        abrirCheckoutPixInstancia(res.instancia);
                    }, 400);
                } else {
                    setTimeout(function() { location.reload(); }, 1200);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao criar instância';
                window.foco7Notify.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Confirmar &amp; Criar Instância');
            }
        });
    }

    function copiarLinkPagamentoInstancia(url) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function() {
                window.foco7Notify.success('Link direto de pagamento copiado! Envie ao cliente para pagamento rápido via PIX.');
            }).catch(function() {
                copiarFallback(url);
            });
        } else {
            copiarFallback(url);
        }
    }

    function copiarFallback(texto) {
        var tempInput = document.createElement('input');
        tempInput.value = texto;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        window.foco7Notify.success('Link direto de pagamento copiado com sucesso!');
    }

    function executarCobrancaPreventivaWpp() {
        Swal.fire({
            title: 'Disparar Régua Preventiva?',
            text: 'O sistema varrerá as instâncias com vencimento em até 3 dias e enviará o código PIX e link de pagamento direto no WhatsApp de cada responsável.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Disparar Agora',
            confirmButtonColor: '#e0a800',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processando Envios...',
                    html: '<div class="py-3"><i class="fas fa-spinner fa-spin fa-2x text-warning mb-3"></i><p class="text-secondary small mb-0">Gerando cobranças Mercado Pago e disparando mensagens...</p></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                $.ajax({
                    url: '{{ route("admin.whatsapp.instancias.cobranca_preventiva") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'json',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cobrança Preventiva Executada!',
                            text: res.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.message || 'Falha ao processar régua preventiva.';
                        Swal.fire({ icon: 'error', title: 'Erro', text: msg, confirmButtonColor: '#d90000' });
                    }
                });
            }
        });
    }

    function sincronizarPagamentosMpWpp() {
        Swal.fire({
            title: 'Sincronizando com Mercado Pago...',
            html: '<div class="py-3"><i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i><p class="text-secondary small mb-0">Consultando status bancário na API do Mercado Pago...</p></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            url: '{{ route("admin.whatsapp.instancias.sincronizar_mp") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sincronização Concluída!',
                    text: res.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Falha ao sincronizar com o Mercado Pago.';
                Swal.fire({ icon: 'error', title: 'Erro', text: msg, confirmButtonColor: '#d90000' });
            }
        });
    }

    // =========================================================================
    // RECORRÊNCIA PIX & GESTÃO DE INSTÂNCIAS EXTRAS
    // =========================================================================

    function abrirCheckoutPixInstancia(nome, forcarNova = false) {
        checkoutInstanciaAtual = nome;
        var modalEl = document.getElementById('modalCheckoutPixInstancia');
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();

        $('#checkoutPixSucessoBox').hide();
        $('#checkoutPixConteudoBox').hide();
        $('#checkoutPixLoadingBox').show();
        $('#btnJaEfetueiPagamentoPix').show();

        $.ajax({
            url: '{{ route("admin.whatsapp.instancias.gerar_pix") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                nome: nome,
                forcar_nova: forcarNova ? 1 : 0
            },
            dataType: 'json',
            success: function(res) {
                $('#checkoutPixLoadingBox').hide();
                if (res.success) {
                    $('#checkoutPixNomeInstancia').text(res.instancia);
                    $('#checkoutPixValor').text(res.valor_formatado);
                    $('#checkoutPixDataVenc').text(res.data_vencimento);
                    $('#checkoutPixCodigoFatura').text(res.codigo_fatura || '--');
                    $('#checkoutPixTransacaoId').text(res.transacao_id || '--');
                    $('#checkoutPixReqTokenBadge').text('REQ: ' + (res.req_token ? res.req_token.substring(0, 8).toUpperCase() : 'DINÂMICO'));

                    if (res.ambiente === 'sandbox') {
                        $('#checkoutPixBadgeAmbiente')
                            .removeClass('bg-primary bg-success')
                            .addClass('bg-warning text-dark')
                            .html('<i class="fas fa-flask me-1"></i> Sandbox • Homologação PIX');
                    } else {
                        $('#checkoutPixBadgeAmbiente')
                            .removeClass('bg-warning text-dark')
                            .addClass('bg-primary')
                            .html('<i class="fas fa-shield-halved me-1"></i> Produção • PIX Oficial');
                    }

                    $('#checkoutPixImgQr').attr('src', res.pix_qr_url);
                    $('#checkoutPixCopiaCola').val(res.pix_copia_cola);
                    $('#checkoutPixConteudoBox').show();

                    if (forcarNova) {
                        window.foco7Notify.success('Novo QR Code PIX e Webhook Dinâmico gerados com sucesso!');
                    }

                    iniciarPollingPixInstancia(nome);
                } else {
                    window.foco7Notify.error(res.message || 'Erro ao gerar cobrança PIX.');
                    modal.hide();
                }
            },
            error: function(xhr) {
                $('#checkoutPixLoadingBox').hide();
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao processar checkout PIX.';
                window.foco7Notify.error(msg);
                modal.hide();
            }
        });
    }

    function copiarPixCopiaCola() {
        var input = document.getElementById('checkoutPixCopiaCola');
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            window.foco7Notify.success('Código PIX Copia e Cola copiado com sucesso!');
        });
    }

    function acionarRetornoAutomaticoCheckoutPix(res) {
        pararPollingPixInstancia();
        $('#checkoutPixLoadingBox').hide();
        $('#checkoutPixConteudoBox').fadeOut(200, function() {
            $('#checkoutPixSucessoInstancia').text(res.instancia || checkoutInstanciaAtual || '--');
            $('#checkoutPixSucessoVigencia').text(res.novo_vencimento || '+30 dias');
            if (res.message) {
                $('#checkoutPixSucessoMsg').text(res.message);
            }
            $('#btnJaEfetueiPagamentoPix').hide();
            $('#checkoutPixSucessoBox').fadeIn(250);
        });

        window.foco7Notify.success(res.message || 'Pagamento PIX aprovado! Retornando automaticamente...');

        var segundos = 3;
        $('#checkoutPixContadorRetorno').text(segundos);
        if (retornoPixTimer) clearInterval(retornoPixTimer);
        retornoPixTimer = setInterval(function() {
            segundos--;
            if (segundos <= 0) {
                clearInterval(retornoPixTimer);
                location.reload();
            } else {
                $('#checkoutPixContadorRetorno').text(segundos);
            }
        }, 1000);
    }

    function iniciarPollingPixInstancia(nome) {
        pararPollingPixInstancia();
        pollingPixTimer = setInterval(function() {
            $.ajax({
                url: '/admin/whatsapp/instancias/verificar-pix/' + encodeURIComponent(nome),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.pago) {
                        acionarRetornoAutomaticoCheckoutPix(res);
                    }
                }
            });
        }, 3000);
    }

    function pararPollingPixInstancia() {
        if (pollingPixTimer) {
            clearInterval(pollingPixTimer);
            pollingPixTimer = null;
        }
        if (retornoPixTimer) {
            clearInterval(retornoPixTimer);
            retornoPixTimer = null;
        }
    }

    function checarStatusPixManual() {
        if (!checkoutInstanciaAtual) return;
        $.ajax({
            url: '/admin/whatsapp/instancias/verificar-pix/' + encodeURIComponent(checkoutInstanciaAtual),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.pago) {
                    acionarRetornoAutomaticoCheckoutPix(res);
                } else {
                    window.foco7Notify.info('Pagamento PIX ainda não identificado. Assim que compensado no banco, o checkout retornará automaticamente.');
                }
            }
        });
    }

    function confirmarPagamentoManualInstancia(nome) {
        window.confirmarAcao(
            'Confirmar Pagamento Manual (Restrito Master)?',
            `Você confirma que recebeu a mensalidade de R$ 60,00 da instância "${nome}" no Módulo Mercado Pago do Desenvolvedor? A vigência será renovada por +30 dias.`,
            'question',
            function() {
                $.ajax({
                    url: '{{ route("admin.whatsapp.instancias.confirmar_manual") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { nome: nome },
                    dataType: 'json',
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pagamento Confirmado!',
                            text: res.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.message || 'Falha ao confirmar pagamento manual.';
                        window.foco7Notify.error(msg);
                        if (xhr.status === 403 || xhr.responseJSON?.requer_senha_master) {
                            solicitarSenhaSegurancaWpp();
                        }
                    }
                });
            }
        );
    }

    function suspenderInstanciaManual(nome) {
        window.confirmarAcao(
            'Suspender Instância (Restrito Master)?',
            `A instância "${nome}" terá sua conexão cortada no servidor e não poderá disparar mensagens até ser reativada.`,
            'warning',
            function() {
                $.ajax({
                    url: '{{ route("admin.whatsapp.instancias.suspender") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { nome: nome, motivo: 'Suspensão manual via painel' },
                    dataType: 'json',
                    success: function(res) {
                        window.foco7Notify.success(res.message);
                        setTimeout(() => { location.reload(); }, 1000);
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.message || 'Falha ao suspender instância.';
                        window.foco7Notify.error(msg);
                        if (xhr.status === 403 || xhr.responseJSON?.requer_senha_master) {
                            solicitarSenhaSegurancaWpp();
                        }
                    }
                });
            }
        );
    }

    function reativarInstanciaManual(nome) {
        $.ajax({
            url: '{{ route("admin.whatsapp.instancias.reativar") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { nome: nome },
            dataType: 'json',
            success: function(res) {
                window.foco7Notify.success(res.message);
                setTimeout(() => { location.reload(); }, 1000);
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Falha ao reativar instância.';
                window.foco7Notify.error(msg);
                if (xhr.status === 403 || xhr.responseJSON?.requer_senha_master) {
                    solicitarSenhaSegurancaWpp();
                }
            }
        });
    }

    function processarVarreduraVencidos() {
        Swal.fire({
            title: 'Varrendo Vencimentos...',
            html: '<p class="small text-secondary mb-0">Verificando inadimplência em todas as instâncias adicionais...</p>',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '{{ route("admin.whatsapp.instancias.processar_vencimentos") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(res) {
                Swal.fire({
                    icon: 'info',
                    title: 'Varredura Concluída',
                    text: res.message,
                    confirmButtonColor: '#d90000'
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire('Erro', 'Falha ao executar varredura de vencimentos.', 'error');
            }
        });
    }

    function excluirInstanciaExtra(nome) {
        window.confirmarAcao(
            'Excluir Monitoramento da Instância (Restrito Master)?',
            `A instância "${nome}" será removida do painel de cobrança recorrente.`,
            'warning',
            function() {
                $.ajax({
                    url: '{{ route("admin.whatsapp.instancias.excluir") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { nome: nome },
                    dataType: 'json',
                    success: function(res) {
                        window.foco7Notify.success(res.message);
                        setTimeout(() => { location.reload(); }, 1000);
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.message || 'Falha ao excluir instância.';
                        window.foco7Notify.error(msg);
                        if (xhr.status === 403 || xhr.responseJSON?.requer_senha_master) {
                            solicitarSenhaSegurancaWpp();
                        }
                    }
                });
            }
        );
    }

    // =========================================================================
    // TEMPLATES & SIMULADOR SMARTPHONE
    // =========================================================================

    function trocarTemplateEdicao() {
        var val = $('#selectTemplateAtivo').val();
        $('.box-template-editor').addClass('d-none');
        var $box = $('#box_' + val);
        $box.removeClass('d-none');

        var $ta = $('#textarea_' + val);
        if ($ta.length && !$ta.next('.note-editor').length && window.initSummernote) {
            window.initSummernote($box);
        }

        var texto = '';
        if ($ta.length && $ta.next('.note-editor').length && $.fn.summernote) {
            texto = $ta.summernote('code');
        } else {
            texto = $ta.val();
        }
        atualizarMockupPreview(texto);
    }

    function atualizarMockupPreview(texto) {
        if (!texto) texto = '';
        var preview = texto
            .replace(/{cliente_nome}/g, 'Carlos Silva')
            .replace(/{fatura_id}/g, 'FAT-2026-9812')
            .replace(/{plano_nome}/g, 'Plano Ouro 4G')
            .replace(/{valor}/g, 'R$ 79,90')
            .replace(/{valor_original}/g, 'R$ 79,90')
            .replace(/{valor_atualizado}/g, 'R$ 83,50')
            .replace(/{data_vencimento}/g, '15/11/2026')
            .replace(/{link_fatura}/g, 'https://foco7.com.br/faturas/123')
            .replace(/{pix_copia_cola}/g, '00020126580014br.gov.bcb.pix...')
            .replace(/{empresa_nome}/g, 'Foco 7 Rastreamento')
            .replace(/{dias_atraso}/g, '3');

        // Formatação WhatsApp caso o usuário tenha usado sintaxe markdown nativa (*texto*, _texto_, ~texto~)
        preview = preview.replace(/\*(.*?)\*/g, '<strong>$1</strong>');
        preview = preview.replace(/_(.*?)_/g, '<em>$1</em>');
        preview = preview.replace(/~(.*?)~/g, '<del>$1</del>');

        // Se for texto plano sem quebra HTML, converte \n para <br>
        if (!preview.includes('<p>') && !preview.includes('<br>')) {
            preview = preview.replace(/\n/g, '<br>');
        }

        $('#previewChatBubbleText').html(preview + '<div class="chat-bubble-footer">10:42 <i class="fas fa-check-double text-info ms-1"></i></div>');
    }

    function copiarTag(tag) {
        navigator.clipboard.writeText(tag).then(function () {
            window.foco7Notify.success('Tag ' + tag + ' copiada!');
        });

        // Insere diretamente na posição do cursor no Summernote ativo
        var val = $('#selectTemplateAtivo').val();
        var $ta = $('#textarea_' + val);
        if ($ta.length && $ta.next('.note-editor').length && $.fn.summernote) {
            $ta.summernote('insertText', tag);
            atualizarMockupPreview($ta.summernote('code'));
        }
    }

    function copiarWebhookUrl() {
        var input = document.getElementById('inputWebhookUrl');
        input.select();
        navigator.clipboard.writeText(input.value).then(function () {
            window.foco7Notify.success('URL de Webhook copiada com sucesso!');
        });
    }

    function selecionarFormato(val) {
        $('input[name="tipo_envio"][value="' + val + '"]').prop('checked', true);
        $('.formato-card').removeClass('selecionado border-primary bg-primary bg-opacity-10');
        $('input[name="tipo_envio"]:checked').closest('.formato-card').addClass('selecionado');
    }

    // Disparo de teste via AJAX
    function enviarMensagemTeste() {
        var tel = $('#inputTelefoneTeste').val();
        var tipo = $('#selectTipoTeste').val();
        var $btn = $('#btnDispararTeste');

        if (!tel) {
            window.foco7Notify.warning('Por favor, informe o número de WhatsApp para teste.');
            $('#inputTelefoneTeste').focus();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Enviando Teste...');

        $.ajax({
            url: '{{ route("admin.whatsapp.teste") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                numero: tel,
                tipo: tipo
            },
            dataType: 'json',
            success: function (res) {
                window.foco7Notify.success(res.message);
                $('#boxRespostaTeste').show();
                $('#jsonRespostaTeste').text(JSON.stringify(res, null, 2));
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao disparar teste';
                window.foco7Notify.error(msg);
                $('#boxRespostaTeste').show();
                $('#jsonRespostaTeste').text(JSON.stringify(xhr.responseJSON || {}, null, 2));
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Disparar Mensagem de Teste');
            }
        });
    }

    // Inicialização
    $(function () {
        verificarStatusApi(false);
        trocarTemplateEdicao();

        // Inicializa o Summernote quando o usuário abre a aba Templates
        $('button[data-bs-target="#tab-templates"], a[data-bs-target="#tab-templates"]').on('shown.bs.tab', function () {
            trocarTemplateEdicao();
        });

        // Sincroniza em tempo real com o smartphone mockup enquanto o usuário digita
        $(document).on('input change summernote.change', '.box-template-editor .summernote', function () {
            var val = $('#selectTemplateAtivo').val();
            if ($(this).attr('id') === 'textarea_' + val) {
                var c = $(this).val();
                if ($.fn.summernote && $(this).next('.note-editor').length) {
                    c = $(this).summernote('code');
                }
                atualizarMockupPreview(c);
            }
        });

        $(document).on('click', '.tarja-dev-bloqueado', function () {
            solicitarSenhaSegurancaWpp();
        });

        $('#formWhatsappConfig').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);

            // Garante a sincronização de todas as instâncias do Summernote para os textareas antes do envio AJAX
            $form.find('.summernote').each(function () {
                var $t = $(this);
                if ($.fn.summernote && $t.next('.note-editor').length) {
                    $t.val($t.summernote('code'));
                }
            });

            var $btn = $('#btnSalvarConfigs');
            var origHtml = $btn.html();

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Gravando e Criptografando...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Configurações Gravadas!',
                            text: res.message,
                            confirmButtonColor: '#28a745'
                        });

                        if (res.instance && !$('#inputInstance').hasClass('tarja-dev-bloqueado')) {
                            $('#txtInstanciaNome').text(res.instance);
                        }
                        if (res.api_url && !$('#inputApiUrl').hasClass('tarja-dev-bloqueado')) {
                            $('#txtApiUrl').text(res.api_url);
                        }
                    } else {
                        window.foco7Notify.error(res.message || 'Erro ao salvar.');
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    var msg = xhr.responseJSON?.message || 'Erro ao processar o salvamento.';
                    window.foco7Notify.error(msg);
                }
            });
        });
    });
</script>
@endsection
