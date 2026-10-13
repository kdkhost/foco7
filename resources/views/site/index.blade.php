<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>{{ $seo->meta_title ?? 'Foco 7 | Rastreamento Veicular 24h, Bloqueio e Ronda Tática' }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? 'Proteja seu carro, moto ou frota com o rastreador mais confiável do Norte e Nordeste. Bloqueio imediato pelo celular, central 24h, ronda tática ágil e planos a partir de R$ 49,90/mês.' }}">
    <meta name="keywords" content="{{ $seo->meta_keywords ?? 'rastreador veicular, rastreamento de carros, rastreamento de motos, ronda tática, bloqueio veicular, foco 7' }}">
    <meta name="robots" content="{{ $seo->robots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $seo->canonical_url ?? url('/') }}">

    <!-- Open Graph (Redes Sociais / WhatsApp) -->
    <meta property="og:title" content="{{ $seo->og_title ?? $seo->meta_title ?? 'Foco 7 Rastreamento Veicular' }}">
    <meta property="og:description" content="{{ $seo->og_description ?? $seo->meta_description ?? 'Proteja seu veículo com tecnologia de ponta 24 horas.' }}">
    <meta property="og:image" content="{{ $seo->og_image ?? asset('img/og-foco7.jpg') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">

    <!-- Favicon Oficial -->
    <link rel="icon" type="image/png" href="{{ $configs['favicon'] ?? asset('img/favicon.png') }}">

    <!-- Bootstrap 5.3, FontAwesome 6, Flaticon UIcons & Bootstrap Icons (Suporte Completo e Universal a Ícones) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Swiper 11 Slider (Carrosséis Touch e Drag) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Leaflet.js CSS (Mapa Interativo do Brasil) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    <!-- Toastr & SweetAlert2 (Global) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Estilos Customizados Foco 7 -->
    <link rel="stylesheet" href="{{ asset('css/site-foco7.css') }}?v={{ time() }}">

    <!-- Injeção Dinâmica da Paleta Oficial CMS Foco 7 -->
    <style>
        :root {
            --foco-red: {{ $cmsCorPrimaria ?? '#d90000' }};
            --foco-red-hover: {{ $cmsCorDestaque ?? '#b30000' }};
            --foco-red-light: {{ $cmsCorDestaque ?? '#ff2a2a' }};
            --bs-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
        }

        /* Badge do Google reCAPTCHA v3 e Selo Estilizados Dinamicamente com a Paleta do CMS */
        .grecaptcha-badge {
            border: 2px solid var(--foco-red) !important;
            box-shadow: 0 8px 24px rgba(var(--bs-primary-rgb), 0.35) !important;
            border-radius: 12px 0 0 12px !important;
            transition: all 0.3s ease !important;
            z-index: 9990 !important;
        }
        .grecaptcha-badge:hover {
            box-shadow: 0 12px 32px rgba(var(--bs-primary-rgb), 0.55) !important;
            transform: translateX(-4px);
        }
        .foco-recaptcha-box {
            background: rgba(var(--bs-primary-rgb), 0.04);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.22);
            border-radius: 10px;
            padding: 12px 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .foco-recaptcha-box:hover {
            border-color: var(--foco-red);
            box-shadow: 0 4px 14px rgba(var(--bs-primary-rgb), 0.15);
        }
        .foco-recaptcha-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--bs-primary-rgb), 0.12);
            color: var(--foco-red);
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .foco-recaptcha-badge-pill {
            background-color: var(--foco-red);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 4px 10px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(var(--bs-primary-rgb), 0.3);
            text-transform: uppercase;
        }
        .foco-recaptcha-links a {
            color: var(--foco-red);
            text-decoration: underline;
        }
        .foco-recaptcha-links a:hover {
            color: var(--foco-red-hover);
        }
    </style>

    @if(($configs['recaptcha_ativo'] ?? '0') === '1' && !empty($configs['recaptcha_site_key']))
    <!-- Google reCAPTCHA v3 API -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ $configs['recaptcha_site_key'] }}"></script>
    @endif
</head>
<body id="inicio">

    @if(\App\Models\CmsSetting::get('manutencao_ativa', '0') == '1')
    <!-- BARRA FIXA DE AVISO: MODO DE MANUTENÇÃO ATIVO (Visível para Administradores / IPs Autorizados) -->
    <div class="alert alert-danger mb-0 py-2 px-3 fw-bold rounded-0 border-0 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2" style="position: sticky; top: 0; z-index: 9999999; font-size: 0.88rem; background: linear-gradient(90deg, #8b0000, #dc2626); color: #ffffff;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-triangle fs-5 text-warning"></i>
            <span>
                <strong>MODO DE MANUTENÇÃO ATIVADO:</strong> Visitantes comuns veem a página de manutenção. Seu acesso está liberado por ser Administrador ou IP autorizado.
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.manutencao.preview') }}" target="_blank" class="btn btn-sm btn-light text-danger fw-bold py-1 px-3 shadow-sm">
                <i class="fas fa-eye me-1"></i> Ver Tela de Manutenção
            </a>
            <a href="{{ route('admin.manutencao.index') }}" class="btn btn-sm btn-outline-light fw-bold py-1 px-3">
                <i class="fas fa-tools me-1"></i> Painel de Manutenção
            </a>
        </div>
    </div>
    @endif

    <!-- 1. CABEÇALHO FIXO GLOBAL (TOPBAR + NAVBAR) -->
    <header class="foco-header-fixed" id="focoHeader">
        <!-- TOPBAR FIXO DE CONTATO E ÁREA DO CLIENTE -->
        <div class="foco-topbar d-none d-md-block">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <span><i class="fas fa-headset text-danger me-1"></i> {{ $configs['horario'] }}</span>
                    <span><i class="fas fa-phone-alt text-danger me-1"></i> {{ $configs['telefone'] }}</span>
                    <a href="https://wa.me/{{ $configs['whatsapp'] }}" target="_blank">
                        <i class="fab fa-whatsapp text-success me-1"></i> {{ $configs['whatsapp_formatado'] }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($configs['instagram']))
                        <a href="{{ $configs['instagram'] }}" target="_blank" title="Siga nosso Instagram" data-bs-toggle="tooltip" data-bs-placement="bottom"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if(!empty($configs['facebook']))
                        <a href="{{ $configs['facebook'] }}" target="_blank" title="Curta nossa página no Facebook" data-bs-toggle="tooltip" data-bs-placement="bottom"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    <a href="{{ route('login') }}" class="btn-cliente-topo">
                        <i class="fas fa-user-lock me-1"></i> Área do Cliente / Painel
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. NAVBAR NAVEGAÇÃO SUAVE -->
        <nav class="navbar navbar-expand-lg foco-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#inicio">
                    <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="38" class="logo-foco7" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
                </a>

                <!-- BOTÃO HAMBÚRGUER ESTILO ANDROID (Aciona Gaveta da Esquerda para a Direita) -->
                <button class="navbar-toggler d-lg-none btn-android-burger" type="button" id="btnOpenMenuDrawer" data-bs-toggle="offcanvas" data-bs-target="#menuDrawerAndroid" aria-controls="menuDrawerAndroid" aria-label="Abrir Menu de Navegação">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- MENU HORIZONTAL DESKTOP (Apenas Telas Grandes >= 992px) -->
                <div class="collapse navbar-collapse d-none d-lg-flex" id="navDesktopFoco">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item"><a class="nav-link active" href="#inicio">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="#riscos">Benefícios</a></li>
                        <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                        <li class="nav-item"><a class="nav-link" href="#planos">Planos</a></li>
                        <li class="nav-item"><a class="nav-link text-nowrap fw-semibold" href="#estetica"><i class="fas fa-spray-can-sparkles text-danger me-1"></i>Estética</a></li>
                        <li class="nav-item"><a class="nav-link" href="#ronda">Ronda</a></li>
                        <li class="nav-item"><a class="nav-link" href="#app">App</a></li>
                        @if(isset($parceiros) && $parceiros->count() > 0)
                        <li class="nav-item"><a class="nav-link" href="#parceiros">Parceiros</a></li>
                        @endif
                        @if(isset($filiais) && $filiais->count() > 0)
                        <li class="nav-item"><a class="nav-link" href="#unidades">Onde Estamos</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="#faq">Dúvidas</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center mt-3 mt-lg-0">
                        <button type="button" class="btn btn-sm btn-outline-danger d-none d-xl-inline-flex align-items-center gap-1" onclick="abrirModalAgendamentoSite()">
                            <i class="fas fa-calendar-check"></i> Agendar
                        </button>
                        <a href="{{ route('login') }}" class="btn-foco-login-nav d-none d-lg-inline-flex">
                            <i class="fas fa-user-lock"></i> Área do Cliente
                        </a>
                        <a href="https://foco7.plataforma.app.br/login" target="_blank" rel="noopener noreferrer" class="btn-foco-rastreamento btn-sm px-3 py-2 text-center justify-content-center" title="Acessar Plataforma de Rastreamento Online" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="fas fa-satellite-dish"></i> Rastreamento
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- MENU GAVETA DESLIZANTE NATIVO ANDROID (DESLIZA DA ESQUERDA PARA A DIREITA) -->
    <div class="offcanvas offcanvas-start foco-android-drawer" tabindex="-1" id="menuDrawerAndroid" aria-labelledby="menuDrawerLabel">
        <!-- Cabeçalho do App Android -->
        <div class="offcanvas-header android-drawer-header">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="32" class="drawer-logo" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
            </div>
            <button type="button" class="btn-android-close" data-bs-dismiss="offcanvas" aria-label="Fechar menu">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>

        <!-- Cartão de Status do App Android -->
        <div class="android-drawer-card-status">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="android-pulse-dot"></span>
                    <span class="small fw-bold text-dark">Central 24 Horas Ativa</span>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">Online</span>
            </div>
            <p class="small text-muted mb-0 mt-1" style="font-size: 0.75rem;">Rastreamento, Bloqueio Imediato & Estética</p>
        </div>

        <!-- Lista de Navegação Estilo Android (Gaveta Deslizante com Animações Suaves) -->
        <div class="offcanvas-body android-drawer-body p-0">
            <nav class="android-nav-list">
                <a href="#inicio" class="android-nav-item active">
                    <div class="android-item-icon"><i class="fas fa-house"></i></div>
                    <span class="android-item-text">Início</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#riscos" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-shield-halved"></i></div>
                    <span class="android-item-text">Benefícios & Proteção</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#sobre" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-building-circle-check"></i></div>
                    <span class="android-item-text">Sobre a Foco 7</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#planos" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-tags"></i></div>
                    <span class="android-item-text">Planos & Preços</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#estetica" class="android-nav-item">
                    <div class="android-item-icon text-danger"><i class="fas fa-spray-can-sparkles"></i></div>
                    <span class="android-item-text">Estética Automotiva</span>
                    <span class="badge bg-danger rounded-pill px-2 me-1" style="font-size: 0.65rem;">Novo</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#ronda" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-motorcycle"></i></div>
                    <span class="android-item-text">Ronda Tática 24h</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#app" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-mobile-screen-button"></i></div>
                    <span class="android-item-text">Aplicativo Foco 7</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                @if(isset($parceiros) && $parceiros->count() > 0)
                <a href="#parceiros" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-handshake"></i></div>
                    <span class="android-item-text">Empresas Parceiras</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                @endif
                @if(isset($filiais) && $filiais->count() > 0)
                <a href="#unidades" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-map-location-dot"></i></div>
                    <span class="android-item-text">Nossas Unidades</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                @endif
                <a href="#faq" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-circle-question"></i></div>
                    <span class="android-item-text">Dúvidas Frequentes</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
                <a href="#contato" class="android-nav-item">
                    <div class="android-item-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <span class="android-item-text">Fale Conosco</span>
                    <i class="fas fa-chevron-right android-item-arrow"></i>
                </a>
            </nav>

            <!-- Ações Rápidas no Rodapé da Gaveta Android -->
            <div class="android-drawer-actions">
                <button type="button" class="btn btn-outline-danger w-100 py-2 mb-2 d-flex align-items-center justify-content-center gap-2 fw-bold" onclick="abrirModalAgendamentoSite()">
                    <i class="fas fa-calendar-check"></i> Agendar Atendimento
                </button>
                <a href="{{ route('login') }}" class="btn btn-dark w-100 py-2 mb-2 d-flex align-items-center justify-content-center gap-2 fw-bold">
                    <i class="fas fa-user-lock"></i> Entrar na Área do Cliente
                </a>
                <a href="https://foco7.plataforma.app.br/login" target="_blank" rel="noopener noreferrer" class="btn-foco-rastreamento w-100 py-2 mb-2 text-center justify-content-center fw-bold" title="Acessar Plataforma de Rastreamento Online">
                    <i class="fas fa-satellite-dish"></i> Plataforma de Rastreamento
                </a>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp'] ?? '559180510736') }}?text=Ol%C3%A1,%20gostaria%20de%20proteger%20meu%20ve%C3%ADculo%20com%20a%20Foco%207!" target="_blank" class="btn-foco-whatsapp w-100 py-2 text-center justify-content-center fw-bold">
                    <i class="fab fa-whatsapp"></i> Falar no WhatsApp 24h
                </a>

                <div class="android-drawer-info text-center mt-3 pt-2 border-top">
                    <div class="small text-muted mb-1"><i class="fas fa-phone-alt me-1 text-danger"></i> {{ $configs['telefone'] }}</div>
                    <div class="text-secondary" style="font-size: 0.72rem;">Central Operacional Belém / PA • Foco 7</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. HERO SECTION COM CARROSSEL DE ALTA CONVERSÃO & PELÍCULA CINEMATOGRÁFICA -->
    @php
        $heroBgImagem = !empty($configs['hero_bg_imagem']) ? asset($configs['hero_bg_imagem']) : '';
        $heroOpacidadeDecimal = isset($configs['hero_bg_opacidade']) ? round(((float)$configs['hero_bg_opacidade'] / 100), 2) : 0.70;
        $heroOverlayCor = !empty($configs['hero_overlay_cor']) && strtolower($configs['hero_overlay_cor']) !== '#ffffff' 
            ? $configs['hero_overlay_cor'] 
            : '#0b0f19';
        $heroBlur = (int)($configs['hero_bg_blur'] ?? 0);
    @endphp
    <header class="hero-section" id="inicio">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="7000">
            <div class="carousel-inner">
                @forelse($slides as $index => $slide)
                @php
                    $slideImg = '';
                    if (!empty($slide->imagem)) {
                        $slideImg = asset($slide->imagem);
                    } elseif (!empty($heroBgImagem)) {
                        $slideImg = $heroBgImagem;
                    } else {
                        $slideImg = asset('/img/manutencao-bg.jpg');
                    }
                @endphp
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <!-- Imagem de fundo em tela cheia (100% de ponta a ponta sem corte) -->
                    <div class="hero-slide-bg" style="background-image: url('{{ $slideImg }}'); {{ $heroBlur > 0 ? "filter: blur({$heroBlur}px); transform: scale(1.05);" : '' }}"></div>
                    <!-- Película degradê profissional em tela cheia para contraste cinematográfico -->
                    <div class="hero-slide-overlay hero-bg-overlay" style="background: linear-gradient(90deg, {{ $heroOverlayCor }}f5 0%, {{ $heroOverlayCor }}d9 45%, {{ $heroOverlayCor }}8c 75%, {{ $heroOverlayCor }}33 100%); opacity: {{ $heroOpacidadeDecimal }};"></div>

                    <!-- Conteúdo em container centralizado com alinhamento impecável e respiro lateral -->
                    <div class="hero-slide-content">
                        <div class="container position-relative">
                            <div class="row align-items-center min-vh-50">
                                <div class="col-lg-7">
                                    @if(!empty($slide->badge))
                                        <div class="hero-badge">
                                            <i class="fas fa-shield-alt me-2"></i> {{ $slide->badge }}
                                        </div>
                                    @endif
                                    <h1 class="hero-title">
                                        {{ $slide->titulo }}
                                    </h1>
                                    <div class="hero-desc">
                                        {!! $slide->subtitulo !!}
                                    </div>
                                    <div class="hero-btn-group d-flex flex-wrap gap-3">
                                        @if(!empty($slide->texto_botao_1))
                                            <a href="{{ $slide->link_botao_1 }}" class="btn-foco-primary">
                                                <i class="fas fa-check-circle"></i> {{ $slide->texto_botao_1 }}
                                            </a>
                                        @endif
                                        @if(!empty($slide->texto_botao_2))
                                            <a href="{{ $slide->link_botao_2 }}" class="btn-foco-outline">
                                                {{ $slide->texto_botao_2 }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-5 text-center mt-4 mt-lg-0">
                                    <div class="card-simulacao-hero">
                                        <div class="text-start mb-3">
                                            <span class="badge bg-danger px-3 py-2 rounded-pill">PROTEÇÃO ATIVA 24H</span>
                                        </div>
                                        <h4 class="fw-bold mb-2 text-start" style="color: var(--foco-title);">Simulação Rápida em 1 Minuto</h4>
                                        <p class="small text-secondary mb-4 text-start">Escolha seu tipo de veículo e receba atendimento imediato do nosso especialista.</p>
                                        <div class="row g-2 mb-4">
                                            <div class="col-6">
                                                <a href="#planos" class="btn-tipo-veiculo">
                                                    <i class="fas fa-car text-danger fs-4 d-block mb-1"></i>
                                                    <span class="fw-bold d-block">Carro</span>
                                                    <span class="small text-secondary">A partir de R$ 69,90</span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="#planos" class="btn-tipo-veiculo">
                                                    <i class="fas fa-motorcycle text-danger fs-4 d-block mb-1"></i>
                                                    <span class="fw-bold d-block">Moto</span>
                                                    <span class="small text-secondary">A partir de R$ 49,90</span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="#planos" class="btn-tipo-veiculo">
                                                    <i class="fas fa-truck text-danger fs-4 d-block mb-1"></i>
                                                    <span class="fw-bold d-block">Caminhão</span>
                                                    <span class="small text-secondary">A partir de R$ 99,90</span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="#planos" class="btn-tipo-veiculo">
                                                    <i class="fas fa-cubes text-danger fs-4 d-block mb-1"></i>
                                                    <span class="fw-bold d-block">Frotas</span>
                                                    <span class="small text-secondary">Condições especiais</span>
                                                </a>
                                            </div>
                                        </div>
                                        <a href="#planos" class="btn-foco-primary w-100">
                                            VER TODOS OS PLANOS E RECURSOS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="carousel-item active">
                    <div class="hero-slide-bg" style="background-image: url('{{ !empty($heroBgImagem) ? $heroBgImagem : asset('/img/manutencao-bg.jpg') }}'); {{ $heroBlur > 0 ? "filter: blur({$heroBlur}px); transform: scale(1.05);" : '' }}"></div>
                    <div class="hero-slide-overlay hero-bg-overlay" style="background: linear-gradient(90deg, {{ $heroOverlayCor }}f5 0%, {{ $heroOverlayCor }}d9 45%, {{ $heroOverlayCor }}8c 75%, {{ $heroOverlayCor }}33 100%); opacity: {{ $heroOpacidadeDecimal }};"></div>
                    <div class="hero-slide-content">
                        <div class="container position-relative">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <h1 class="hero-title">Proteger hoje para que você viva o amanhã com tranquilidade</h1>
                                    <p class="hero-desc">Monitoramento veicular 24h, bloqueio imediato e Ronda Tática presencial.</p>
                                    <div class="hero-btn-group d-flex flex-wrap gap-3">
                                        <a href="#planos" class="btn-foco-primary">PROTEGER MEU VEÍCULO AGORA</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            @if($slides->count() > 1)
            <div class="container hero-nav-container">
                <div class="hero-carousel-nav">
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Slide Anterior" title="Slide Anterior">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="carousel-indicators">
                        @foreach($slides as $idx => $s)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-label="Slide {{ $idx + 1 }}"></button>
                        @endforeach
                    </div>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Próximo Slide" title="Próximo Slide">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </header>

    <!-- 4. SEÇÃO O RISCO NÃO AVISA ANTES DE CHEGAR (SOS RASTREAMENTO INSPIRAÇÃO) -->
    <section id="riscos" class="section-risco">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase letter-spacing-1 small">Segurança Preventiva</span>
                <h2 class="display-6 fw-black mt-2">O risco não avisa antes de chegar</h2>
                <p class="text-secondary fs-5">A criminalidade evolui todos os dias. Deixar seu patrimônio desprotegido custa muito mais caro que investir em prevenção.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-risco">
                        <div class="icon-risco"><i class="fas fa-mask"></i></div>
                        <h4 class="fw-bold mb-3">Roubos e Furtos Constantes</h4>
                        <p class="text-secondary">Mais de 1.000 veículos são roubados por dia no Brasil. Sem rastreador, a taxa de recuperação cai para menos de 10%.</p>
                        <div class="d-flex align-items-center text-danger fw-semibold small mt-3">
                            <i class="fas fa-shield-alt me-2"></i> Solução Foco 7: Localização em tempo real com precisão de metros.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-risco">
                        <div class="icon-risco"><i class="fas fa-hourglass-half"></i></div>
                        <h4 class="fw-bold mb-3">Resposta Policial Demorada</h4>
                        <p class="text-secondary">Os primeiros 30 minutos após o sinistro são decisivos. Esperar sem suporte especializado reduz drasticamente as chances de resgate.</p>
                        <div class="d-flex align-items-center text-danger fw-semibold small mt-3">
                            <i class="fas fa-motorcycle me-2"></i> Solução Foco 7: Ronda Tática com viaturas de pronta resposta.
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-risco">
                        <div class="icon-risco"><i class="fas fa-coins"></i></div>
                        <h4 class="fw-bold mb-3">Prejuízo Financeiro Irrecuperável</h4>
                        <p class="text-secondary">O valor do veículo, franquias abusivas de seguro tradicional e o transtorno da perda deixam famílias e empresas desamparadas.</p>
                        <div class="d-flex align-items-center text-danger fw-semibold small mt-3">
                            <i class="fas fa-check-double me-2"></i> Solução Foco 7: Planos acessíveis a partir de R$ 49,90/mês.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. QUEM SOMOS E AUTORIDADE (FOCO 7) -->
    <section id="sobre" class="section-sobre">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase small">Mais de 10 Anos de Experiência</span>
                    <h2 class="display-6 fw-black mt-2 mb-4" style="color: var(--foco-title);">Referência em Rastreamento Veicular e Segurança Eletrônica</h2>
                    <p class="text-secondary fs-5 leading-relaxed">
                        A <strong class="text-dark">Foco 7</strong> nasceu com o compromisso inegociável de salvar patrimônios e devolver a paz de espírito a condutores e empresários. Presente com forte infraestrutura no Norte e Nordeste, combinamos inteligência artificial em mapas, módulos de telemetria homologados pela Anatel e equipe tática altamente treinada.
                    </p>
                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                                <div class="bg-danger text-white rounded-3 p-3 fs-3"><i class="fas fa-history"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-0" style="color: var(--foco-title);">+10 Anos</h4>
                                    <span class="small text-secondary">De Mercado Sólido</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                                <div class="bg-danger text-white rounded-3 p-3 fs-3"><i class="fas fa-car-crash"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-0" style="color: var(--foco-title);">98.4%</h4>
                                    <span class="small text-secondary">Taxa de Recuperação</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-diferenciais p-4 p-md-5">
                        <h4 class="fw-bold mb-4" style="color: var(--foco-title);"><i class="fas fa-shield-virus text-danger me-2"></i> Por Que Somos a Melhor Escolha?</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-light-subtle">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--foco-title);">Central de Operações Blindada 24 Horas</h6>
                                    <p class="small text-secondary mb-0">Operadores de prontidão todos os dias do ano para atender chamados de emergência.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom border-light-subtle">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--foco-title);">Tecnologia 4G Multioperadora</h6>
                                    <p class="small text-secondary mb-0">Chip inteligente que alterna entre as melhores operadoras do país para sinal constante.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--foco-title);">Instalação Discreta e Sigilosa</h6>
                                    <p class="small text-secondary mb-0">Técnicos certificados que instalam sem cortar chicote original e sem comprometer garantias.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. PLANOS E PREÇOS COM CHECKOUT TRANSPARENTE -->
    <section id="planos" class="section-planos">
        <div class="container" style="max-width: 1280px;">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small letter-spacing-1">Investimento Inteligente</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">Escolha o Plano Ideal Para Seu Veículo</h2>
                <p class="text-secondary fs-5">Sem análise de perfil, sem consulta ao SPC/Serasa e sem fidelidade abusiva.</p>
            </div>
            @php
                $totalPlanos = $planos->count();
                $colPlano = match(true) {
                    $totalPlanos === 1 => 'col-lg-6 col-md-8',
                    $totalPlanos === 2 => 'col-lg-6 col-md-6',
                    $totalPlanos === 3 => 'col-lg-4 col-md-6',
                    $totalPlanos === 4 => 'col-xl-3 col-lg-6 col-md-6',
                    default => 'col-xl-3 col-lg-4 col-md-6',
                };
            @endphp
            <div class="row g-4 justify-content-center align-items-stretch">
                @foreach($planos as $plano)
                <div class="{{ $colPlano }} d-flex">
                    <div class="card-plano w-100 {{ $plano->destaque ? 'destaque' : '' }}">
                        @if($plano->destaque)
                            <div class="tag-plano-destaque">
                                <i class="fas fa-fire me-1"></i> {{ !empty($plano->tag_destaque) ? $plano->tag_destaque : 'MAIS ESCOLHIDO' }}
                            </div>
                        @endif
                        <div class="text-center">
                            <div class="icone-plano-box">
                                {!! $plano->icone_html !!}
                            </div>
                            <h4 class="plano-titulo">{{ $plano->nome }}</h4>
                            <p class="plano-subtitulo">{{ $plano->subtitulo }}</p>
                        </div>
                        <div class="bloco-preco">
                            <div>
                                <span class="preco-valor">{{ $plano->preco_formatado }}</span>
                                <span class="preco-periodo">/mês</span>
                            </div>
                            @if($plano->taxa_adesao > 0)
                                <span class="badge-adesao badge-adesao-paga">Adesão: {{ $plano->taxa_adesao_formatada }}</span>
                            @else
                                <span class="badge-adesao badge-adesao-gratis"><i class="fas fa-check-circle me-1"></i> ADESÃO GRÁTIS</span>
                            @endif
                        </div>
                        <ul class="recursos-list">
                            @if(is_array($plano->recursos))
                                @foreach($plano->recursos as $item)
                                    <li><i class="fas fa-check-circle"></i> <span>{{ $item }}</span></li>
                                @endforeach
                            @endif
                        </ul>
                        <button class="btn {{ $plano->destaque ? 'btn-foco-primary' : 'btn-outline-danger' }} w-100 py-3 mt-auto fw-bold btn-contratar text-nowrap"
                                style="white-space: nowrap !important; padding-left: 0.5rem !important; padding-right: 0.5rem !important; font-size: 0.85rem !important;"
                                data-plano-id="{{ $plano->id }}"
                                data-plano-nome="{{ $plano->nome }}"
                                data-plano-preco="{{ $plano->preco_formatado }}">
                            <i class="fas fa-shield-alt me-1"></i> {{ $plano->texto_botao ?: 'CONTRATAR AGORA' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6.1 SEÇÃO ESTÉTICA AUTOMOTIVA PREMIUM -->
    <section id="estetica" class="section-estetica">
        <div class="container" style="max-width: 1280px;">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge-estetica-topo text-uppercase small fw-bold px-3 py-1 rounded-pill">
                    <i class="fas fa-sparkles text-danger me-1"></i> Cuidado & Valorização Automotiva
                </span>
                <h2 class="display-6 fw-black mt-3" style="color: var(--foco-title);">{{ $configs['secao_estetica_titulo'] }}</h2>
                <p class="text-secondary fs-5 mt-2">{{ $configs['secao_estetica_subtitulo'] }}</p>
                @if(!empty($configs['secao_estetica_descricao']))
                    <p class="small text-muted">{{ $configs['secao_estetica_descricao'] }}</p>
                @endif
            </div>

            <!-- Grid de Serviços de Estética Automotiva -->
            <div class="row g-4 justify-content-center">
                @forelse($servicosEstetica as $servico)
                <div class="col-xl-4 col-md-6">
                    <div class="card-estetica">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="icon-estetica-box">
                                    <i class="fas {{ $servico->icone ?? 'fa-spray-can-sparkles' }}"></i>
                                </div>
                                @if(!empty($servico->tempo_estimado))
                                    <span class="badge-estetica-tempo">
                                        <i class="fas fa-clock text-secondary"></i> {{ $servico->tempo_estimado }}
                                    </span>
                                @endif
                            </div>

                            <h4 class="fw-bold mb-2" style="color: var(--foco-title);">{{ $servico->nome }}</h4>
                            <div class="text-secondary small leading-relaxed mb-4">{!! $servico->descricao !!}</div>
                        </div>

                        <div class="pt-3 border-top border-light-subtle">
                            <div class="d-flex align-items-baseline justify-content-between mb-3">
                                <span class="small text-muted fw-semibold">Investimento:</span>
                                <div class="text-end">
                                    <span class="small text-secondary d-block" style="font-size: 0.75rem;">A partir de</span>
                                    <span class="preco-estetica-valor">{{ $servico->preco_formatado }}</span>
                                </div>
                            </div>

                            <button type="button" 
                                    class="btn-agendar-estetica"
                                    onclick="abrirModalAgendamentoSite('estetica', '{{ $servico->id }}', '{{ addslashes($servico->nome) }}')">
                                <i class="fas fa-calendar-check me-2"></i> Agendar Este Serviço
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <div class="alert alert-light border">
                        <i class="fas fa-info-circle text-danger me-2"></i> Novos pacotes de estética automotiva sendo adicionados pela equipe.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Diferenciais da Estética Foco 7 -->
            <div class="row g-3 mt-5 pt-3">
                <div class="col-lg-3 col-sm-6">
                    <div class="card-diferencial-estetica">
                        <i class="fas {{ $configs['secao_estetica_dif1_icone'] ?? 'fa-award' }} text-danger fs-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $configs['secao_estetica_dif1_titulo'] ?? 'Produtos Nobres' }}</h6>
                            <span class="small text-secondary">{{ $configs['secao_estetica_dif1_desc'] ?? 'Vonixx, CarPro e Menzerna' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card-diferencial-estetica">
                        <i class="fas {{ $configs['secao_estetica_dif2_icone'] ?? 'fa-warehouse' }} text-danger fs-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $configs['secao_estetica_dif2_titulo'] ?? 'Box Climatizado' }}</h6>
                            <span class="small text-secondary">{{ $configs['secao_estetica_dif2_desc'] ?? 'Sem poeira ou sol direto' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card-diferencial-estetica">
                        <i class="fas {{ $configs['secao_estetica_dif3_icone'] ?? 'fa-certificate' }} text-danger fs-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $configs['secao_estetica_dif3_titulo'] ?? 'Certificado de Garantia' }}</h6>
                            <span class="small text-secondary">{{ $configs['secao_estetica_dif3_desc'] ?? 'Proteção com laudo técnico' }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card-diferencial-estetica">
                        <i class="fas {{ $configs['secao_estetica_dif4_icone'] ?? 'fa-user-clock' }} text-danger fs-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $configs['secao_estetica_dif4_titulo'] ?? 'Horário Marcado' }}</h6>
                            <span class="small text-secondary">{{ $configs['secao_estetica_dif4_desc'] ?? 'Entrega pontual sem filas' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. SEÇÃO RONDA TÁTICA (FLUXOGRAMA EM 4 ETAPAS) -->
    <section id="ronda" class="section-ronda">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">{{ $configs['secao_ronda_badge'] ?? 'Apoio Presencial Especializado' }}</span>
                <h2 class="display-6 fw-black mt-2">🛡️ {{ $configs['secao_ronda_titulo'] ?? 'Como Funciona a Ronda Tática Foco 7' }}</h2>
                <p class="text-secondary fs-5">{{ $configs['secao_ronda_subtitulo'] ?? 'Tecnologia aliada à ação humana imediata para localizar e recuperar seu patrimônio.' }}</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">1</div>
                        <h5 class="fw-bold mb-2">{{ $configs['secao_ronda_etapa1_titulo'] ?? 'Alarme ou Sinistro' }}</h5>
                        <p class="small text-secondary mb-0">{{ $configs['secao_ronda_etapa1_desc'] ?? 'Você aciona o botão de emergência pelo app ou a central detecta movimentação ou bloqueio não autorizado.' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">2</div>
                        <h5 class="fw-bold mb-2">{{ $configs['secao_ronda_etapa2_titulo'] ?? 'Central 24h Aciona' }}</h5>
                        <p class="small text-secondary mb-0">{{ $configs['secao_ronda_etapa2_desc'] ?? 'Nossos operadores isolam o perímetro, realizam o bloqueio progressivo e geram o mapa da rota do veículo.' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">3</div>
                        <h5 class="fw-bold mb-2">{{ $configs['secao_ronda_etapa3_titulo'] ?? 'Viatura a Caminho' }}</h5>
                        <p class="small text-secondary mb-0">{{ $configs['secao_ronda_etapa3_desc'] ?? 'A viatura ou moto tática mais próxima é despachada imediatamente com as coordenadas exatas do alvo.' }}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">4</div>
                        <h5 class="fw-bold mb-2">{{ $configs['secao_ronda_etapa4_titulo'] ?? 'Recuperação & Apoio' }}</h5>
                        <p class="small text-secondary mb-0">{{ $configs['secao_ronda_etapa4_desc'] ?? 'A equipe chega ao local, aciona as autoridades competentes e preserva seu veículo até sua devolução.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. RECURSOS DO APLICATIVO -->
    <section id="app" class="section-app">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase small letter-spacing-1">{{ $configs['secao_app_badge'] ?? 'Tecnologia Mobile' }}</span>
                    <h2 class="display-6 fw-black mt-2 mb-4" style="color: var(--foco-title);">{{ $configs['secao_app_titulo'] ?? 'Controle Total do Seu Veículo na Palma da Mão' }}</h2>
                    <p class="text-secondary fs-5 leading-relaxed mb-4">
                        {!! nl2br(e($configs['secao_app_descricao'] ?? 'Disponível para Android e iOS, o app Foco 7 foi projetado para oferecer rapidez instantânea em situações de risco e relatórios completos para o dia a dia.')) !!}
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="card-recurso-app">
                                <h6 class="fw-bold text-dark mb-1"><i class="fas {{ $configs['secao_app_rec1_icone'] ?? 'fa-lock' }} text-danger me-2"></i> {{ $configs['secao_app_rec1_titulo'] ?? 'Bloqueio Remoto' }}</h6>
                                <p class="small text-secondary mb-0">{{ $configs['secao_app_rec1_desc'] ?? 'Corte de combustível com 1 toque no celular.' }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card-recurso-app">
                                <h6 class="fw-bold text-dark mb-1"><i class="fas {{ $configs['secao_app_rec2_icone'] ?? 'fa-draw-polygon' }} text-danger me-2"></i> {{ $configs['secao_app_rec2_titulo'] ?? 'Cerca Virtual' }}</h6>
                                <p class="small text-secondary mb-0">{{ $configs['secao_app_rec2_desc'] ?? 'Receba avisos imediatos se o carro sair do perímetro.' }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card-recurso-app">
                                <h6 class="fw-bold text-dark mb-1"><i class="fas {{ $configs['secao_app_rec3_icone'] ?? 'fa-key' }} text-danger me-2"></i> {{ $configs['secao_app_rec3_titulo'] ?? 'Alerta de Ignição' }}</h6>
                                <p class="small text-secondary mb-0">{{ $configs['secao_app_rec3_desc'] ?? 'Notificação push assim que a chave for virada.' }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card-recurso-app">
                                <h6 class="fw-bold text-dark mb-1"><i class="fas {{ $configs['secao_app_rec4_icone'] ?? 'fa-route' }} text-danger me-2"></i> {{ $configs['secao_app_rec4_titulo'] ?? 'Histórico de 90 Dias' }}</h6>
                                <p class="small text-secondary mb-0">{{ $configs['secao_app_rec4_desc'] ?? 'Rotas, paradas e velocidade com mapa 3D.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="mockup-app-box text-center">
                        @php
                            $appMockupImg = !empty($configs['secao_app_mockup_imagem']) ? $configs['secao_app_mockup_imagem'] : '/img/app-mockup-foco7.jpg';
                        @endphp
                        <img src="{{ asset($appMockupImg) }}" alt="{{ $configs['secao_app_card_titulo'] ?? 'Aplicativo Foco 7' }}" class="img-fluid rounded-4 mb-3 shadow-lg" style="max-height: 380px; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('/img/app-mockup-foco7.jpg') }}';">
                        <h4 class="fw-bold mt-4" style="color: var(--foco-title);">{{ $configs['secao_app_card_titulo'] ?? 'Aplicativo Oficial Foco 7' }}</h4>
                        <p class="text-secondary small">{{ $configs['secao_app_card_subtitulo'] ?? 'Baixe gratuitamente na App Store e Google Play Store' }}</p>
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <a href="{{ $configs['secao_app_link_appstore'] ?? 'https://www.apple.com/app-store/' }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm">
                                <i class="fab fa-apple me-1"></i> App Store
                            </a>
                            <a href="{{ $configs['secao_app_link_playstore'] ?? 'https://play.google.com/store/apps' }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm">
                                <i class="fab fa-google-play me-1"></i> Google Play
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. DEPOIMENTOS DE CLIENTES (PROVA SOCIAL) -->
    <section id="depoimentos" class="section-depoimentos">
        <div class="container position-relative">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Quem Já Dorme Tranquilo</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">Depoimentos Reais de Nossos Clientes</h2>
            </div>

            <!-- CARROSSEL TOUCH & DRAG DE DEPOIMENTOS -->
            <div class="depoimentos-carousel-container position-relative">
                <!-- Botão Anterior -->
                <button type="button" class="swiper-btn-depoimentos swiper-btn-prev" id="btnDepoimentosPrev" aria-label="Depoimento Anterior" title="Depoimento Anterior">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <!-- Container Principal Swiper -->
                <div class="swiper swiper-depoimentos">
                    <div class="swiper-wrapper">
                        @foreach($depoimentos as $dep)
                        <div class="swiper-slide h-auto">
                            <div class="card-depoimento h-100">
                                <div>
                                    <div class="text-warning mb-3">
                                        @for($i = 0; $i < $dep->estrelas; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                    </div>
                                    <div class="text-secondary small fst-italic card-depoimento-texto">"{!! $dep->texto !!}"</div>
                                </div>
                                <div class="mt-4 pt-3 border-top border-light-subtle d-flex align-items-center gap-3">
                                    <div class="depoimento-avatar-wrapper">
                                        @if(!empty($dep->foto))
                                            <img src="{{ asset($dep->foto) }}" alt="{{ $dep->nome_cliente }}" class="depoimento-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                            <div class="depoimento-avatar-inicial d-none">
                                                {{ mb_strtoupper(mb_substr($dep->nome_cliente, 0, 1, 'UTF-8')) }}
                                            </div>
                                        @else
                                            <div class="depoimento-avatar-inicial">
                                                {{ mb_strtoupper(mb_substr($dep->nome_cliente, 0, 1, 'UTF-8')) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="depoimento-info min-w-0 flex-grow-1">
                                        <h6 class="fw-bold mb-0 text-dark text-truncate" title="{{ $dep->nome_cliente }}">{{ $dep->nome_cliente }}</h6>
                                        <span class="small text-secondary text-truncate d-block" title="{{ $dep->cidade_uf }} - {{ $dep->veiculo }}">{{ $dep->cidade_uf }} - {{ $dep->veiculo }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Indicadores de Paginação (Bullets) -->
                    <div class="swiper-pagination swiper-pagination-depoimentos mt-4 position-relative"></div>
                </div>

                <!-- Botão Próximo -->
                <button type="button" class="swiper-btn-depoimentos swiper-btn-next" id="btnDepoimentosNext" aria-label="Próximo Depoimento" title="Próximo Depoimento">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="text-center mt-5">
                <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold rounded-pill shadow-sm" onclick="abrirModalAvaliacaoSite()">
                    <i class="fas fa-star text-warning me-2"></i> Avaliar Nosso Atendimento / Deixar Depoimento
                </button>
            </div>
        </div>
    </section>

    @if(isset($parceiros) && $parceiros->count() > 0)
    <!-- 10. EMPRESAS PARCEIRAS & TECNOLOGIAS HOMOLOGADAS (CARROSSEL INFINITO) -->
    <section id="parceiros" class="section-parceiros">
        <div class="container position-relative">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">{{ $configs['secao_parceiros_badge'] ?? 'Ecossistema & Homologações' }}</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">{{ $configs['secao_parceiros_titulo'] ?? 'Empresas Parceiras & Tecnologias Homologadas' }}</h2>
                <p class="text-secondary small mt-2 mb-0">{{ $configs['secao_parceiros_subtitulo'] ?? 'Conectados às principais seguradoras, fabricantes de rastreadores e operadoras de telecomunicação do Brasil.' }}</p>
            </div>

            <!-- CARROSSEL TOUCH & DRAG INFINITO DE PARCEIROS -->
            <div class="parceiros-carousel-container position-relative">
                <!-- Botão Anterior -->
                <button type="button" class="swiper-btn-parceiros swiper-btn-prev" id="btnParceirosPrev" aria-label="Parceiro Anterior" title="Parceiro Anterior">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <!-- Container Principal Swiper -->
                <div class="swiper swiper-parceiros">
                    <div class="swiper-wrapper">
                        @foreach($parceiros as $parceiro)
                        <div class="swiper-slide h-auto">
                            @if(!empty($parceiro->link))
                            <a href="{{ $parceiro->link }}" target="_blank" rel="noopener noreferrer" class="card-parceiro h-100" title="{{ $parceiro->nome }} - Visitar site oficial">
                            @else
                            <div class="card-parceiro h-100" title="{{ $parceiro->nome }}">
                            @endif
                                <div class="parceiro-logo-wrapper">
                                    <img src="{{ $parceiro->logo_url }}" alt="{{ $parceiro->nome }}" class="parceiro-logo-img" loading="lazy" onerror="this.src='/img/logo.png'">
                                </div>
                                <h6 class="parceiro-nome mb-1">{{ $parceiro->nome }}</h6>
                                @if(!empty($parceiro->categoria))
                                <span class="parceiro-categoria">{{ $parceiro->categoria }}</span>
                                @endif
                            @if(!empty($parceiro->link))
                            </a>
                            @else
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Indicadores de Paginação (Bullets) -->
                    <div class="swiper-pagination swiper-pagination-parceiros mt-4 position-relative"></div>
                </div>

                <!-- Botão Próximo -->
                <button type="button" class="swiper-btn-parceiros swiper-btn-next" id="btnParceirosNext" aria-label="Próximo Parceiro" title="Próximo Parceiro">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>
    @endif

    @if(isset($filiais) && $filiais->count() > 0)
    <!-- 10.1 NOSSAS UNIDADES & ONDE ESTAMOS (MAPA INTERATIVO DO BRASIL) -->
    <section id="unidades" class="section-filiais py-5 position-relative">
        <div class="container position-relative">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small"><i class="fas fa-map-location-dot me-1"></i> {{ $configs['secao_filiais_badge'] ?? 'Presença Nacional' }}</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">{{ $configs['secao_filiais_titulo'] ?? 'Nossas Unidades & Cobertura de Atendimento' }}</h2>
                <p class="text-secondary small mt-2 mb-0">{{ $configs['secao_filiais_subtitulo'] ?? 'Encontre a unidade Foco 7 mais próxima de você com suporte presencial, instalação rápida e ronda tática.' }}</p>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- COLUNA ESQUERDA: LISTA DE FILIAIS E BUSCA -->
                <div class="col-lg-4 col-xl-4 d-flex flex-column">
                    <div class="filiais-sidebar p-3 p-md-4 h-100 d-flex flex-column border">
                        <div class="mb-3">
                            <label for="filtroFiliaisInput" class="form-label fw-bold small text-muted text-uppercase mb-2">
                                <i class="fas fa-search me-1 text-danger"></i> Localizar Unidade
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-magnifying-glass text-muted"></i></span>
                                <input type="text" id="filtroFiliaisInput" class="form-control bg-light border-start-0" placeholder="Buscar por cidade, estado ou nome..." autocomplete="off">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="small text-muted fw-semibold">
                                <strong id="filiaisContador" class="text-danger">{{ $filiais->count() }}</strong> {{ $filiais->count() == 1 ? 'unidade ativa' : 'unidades ativas' }}
                            </span>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0" id="btnResetarMapa" title="Ver todo o Brasil">
                                <i class="fas fa-compress-arrows-alt me-1"></i> Ver Todas
                            </button>
                        </div>

                        <!-- LISTAGEM COM SCROLL -->
                        <div class="filiais-scroll-list flex-grow-1 overflow-auto pe-1" id="listaFiliaisCards" style="max-height: 480px;">
                            @foreach($filiais as $f)
                            <div class="card-filial-item p-3 mb-3 rounded-3 cursor-pointer" 
                                 id="card-filial-{{ $f->id }}"
                                 data-id="{{ $f->id }}"
                                 data-lat="{{ $f->latitude }}"
                                 data-lng="{{ $f->longitude }}"
                                 data-cidade="{{ strtolower($f->cidade) }}"
                                 data-estado="{{ strtolower($f->estado) }}"
                                 data-nome="{{ strtolower($f->nome) }}"
                                 onclick="focarFilialNoMapa({{ $f->id }}, {{ $f->latitude ?? 'null' }}, {{ $f->longitude ?? 'null' }})">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 text-dark filial-item-title">{{ $f->nome }}</h6>
                                    @if($f->destaque)
                                    <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 0.65rem;">Matriz</span>
                                    @else
                                    <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.65rem;">{{ $f->estado }}</span>
                                    @endif
                                </div>

                                <p class="small text-muted mb-2 lh-sm">
                                    <i class="fas fa-location-dot text-danger me-1"></i> {{ $f->endereco_completo }}
                                </p>

                                @if($f->telefone || $f->whatsapp)
                                <div class="d-flex flex-wrap gap-2 small text-secondary mb-2">
                                    @if($f->telefone)
                                    <span><i class="fas fa-phone-alt text-muted me-1"></i> {{ $f->telefone }}</span>
                                    @endif
                                    @if($f->whatsapp)
                                    <span><i class="fab fa-whatsapp text-success me-1"></i> {{ $f->whatsapp }}</span>
                                    @endif
                                </div>
                                @endif

                                @if($f->horario_atendimento)
                                <div class="small text-muted mb-3">
                                    <i class="fas fa-clock text-muted me-1"></i> {{ $f->horario_atendimento }}
                                </div>
                                @endif

                                <div class="d-flex gap-2 mt-2 pt-2 border-top">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-50 py-1 fw-bold" onclick="event.stopPropagation(); focarFilialNoMapa({{ $f->id }}, {{ $f->latitude ?? 'null' }}, {{ $f->longitude ?? 'null' }})">
                                        <i class="fas fa-crosshairs me-1"></i> Ver no Mapa
                                    </button>
                                    <a href="{{ $f->rota_osm_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-danger w-50 py-1 fw-bold text-white text-center text-decoration-none" onclick="event.stopPropagation();" title="Traçar Rota no OpenStreetMap (100% Gratuito)">
                                        <i class="fas fa-route me-1"></i> Como Chegar
                                    </a>
                                </div>
                            </div>
                            @endforeach

                            <div id="filiaisVazioMsg" class="text-center py-4 text-muted d-none">
                                <i class="fas fa-map-marker-slash fa-2x mb-2 text-secondary opacity-50"></i>
                                <p class="small mb-0">Nenhuma unidade encontrada para esta busca.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLUNA DIREITA: MAPA DO BRASIL INTERATIVO LEAFLET -->
                <div class="col-lg-8 col-xl-8">
                    <div class="filiais-map-wrapper position-relative rounded-4 overflow-hidden border shadow-sm h-100" style="min-height: 540px;">
                        <div id="mapaBrasilFiliais" style="width: 100%; height: 100%; min-height: 540px; z-index: 1;"></div>
                        
                        <!-- BOTÃO FLUTUANTE DE RESETAR VISÃO DO BRASIL -->
                        <div class="position-absolute bottom-0 start-0 m-3 z-3">
                            <button type="button" class="btn btn-sm btn-light border shadow-sm fw-bold px-3 py-2 d-flex align-items-center gap-2" id="btnMapaVisaoGeral" title="Visualizar todo o Brasil">
                                <i class="fas fa-globe-americas text-danger"></i> Visão Geral Brasil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- 11. PERGUNTAS FREQUENTES (FAQ) -->
    <section id="faq" class="section-faq">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Tire Suas Dúvidas</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">Perguntas Frequentes</h2>
            </div>
            <div class="max-w-800 mx-auto">
                <div class="accordion accordion-foco" id="faqAccordion">
                    @foreach($faqs as $idx => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $idx !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}">
                                <i class="fas fa-question-circle text-danger me-2"></i> {{ $faq->pergunta }}
                            </button>
                        </h2>
                        <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {!! $faq->resposta !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- 11. FORMULÁRIO DE CONTATO & COTAÇÃO RÁPIDA (AJAX) -->
    <section id="contato" class="section-contato">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <span class="text-danger fw-bold text-uppercase small">Fale Conosco</span>
                    <h2 class="display-6 fw-black mt-2 mb-4" style="color: var(--foco-title);">Solicite uma Cotação ou Instalação Imediata</h2>
                    <p class="text-secondary fs-5 mb-4">Preencha o formulário e receba a ligação da nossa equipe técnica em menos de 10 minutos.</p>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="card-contato-icon bg-danger text-white"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <span class="small text-secondary">Telefone Central</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $configs['telefone'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="card-contato-icon bg-success text-white"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <span class="small text-secondary">WhatsApp Direto</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $configs['whatsapp_formatado'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="card-contato-icon bg-danger text-white"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <span class="small text-secondary">Nossa Sede</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $configs['endereco'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card-contato-form">
                        <h4 class="fw-bold mb-4" style="color: var(--foco-title);">Envie Sua Mensagem</h4>
                        <form action="{{ route('site.contato') }}" method="POST" class="ajax-form reset-on-success" id="formContatoSite">
                            @csrf

                            {{-- Blindagem Anti-Bot: Honeypot invisível para enganar robôs/crawlers automatizados --}}
                            <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                                <label for="_foco_hp_website">Deixe este campo vazio</label>
                                <input type="text" name="_foco_hp_website" id="_foco_hp_website" tabindex="-1" autocomplete="off">
                            </div>

                            {{-- Blindagem Anti-Bot: Carimbo de tempo para bloquear disparos ultra-rápidos de scripts --}}
                            <input type="hidden" name="_foco_form_time" value="{{ time() }}">

                            {{-- Token de Segurança Google reCAPTCHA v3 --}}
                            <input type="hidden" name="g-recaptcha-response" id="gRecaptchaContatoResponse">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">Seu Nome Completo *</label>
                                    <input type="text" name="nome" class="form-control" required placeholder="Ex: Roberto Silva">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">Seu WhatsApp *</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" required placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">E-mail</label>
                                    <input type="email" name="email" class="form-control" placeholder="seuemail@exemplo.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">Modelo do Veículo</label>
                                    <input type="text" name="veiculo" class="form-control" placeholder="Ex: Onix 2022, Titan 160">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-secondary fw-semibold">Dúvida ou Mensagem *</label>
                                    <textarea name="mensagem" rows="4" class="form-control" required placeholder="Como podemos ajudar você?"></textarea>
                                </div>

                                {{-- Selo Visual de Segurança reCAPTCHA v3 Sincronizado com a Paleta Dinâmica do Site --}}
                                <div class="col-12 mt-2">
                                    <div class="foco-recaptcha-box d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="foco-recaptcha-icon">
                                                <i class="fas fa-shield-halved"></i>
                                            </div>
                                            <div style="line-height: 1.25;">
                                                <div class="fw-bold small text-dark">Protegido por Google reCAPTCHA v3</div>
                                                <div class="text-secondary foco-recaptcha-links" style="font-size: 0.73rem;">
                                                    Blindagem ativa anti-spam &amp; injeção &bull; 
                                                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacidade</a> &bull; 
                                                    <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Termos</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="foco-recaptcha-badge-pill">
                                            <i class="fas fa-check-circle me-1"></i> Seguro
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn-foco-primary w-100 py-3" id="btnEnviarContatoSite">
                                        <i class="fas fa-paper-plane me-2"></i> ENVIAR SOLICITAÇÃO AGORA
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 12. RODAPÉ INSTITUCIONAL -->
    <footer class="foco-footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="44" class="mb-3 logo-foco7" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
                    <p class="mt-3 text-secondary small leading-relaxed">
                        Líder em rastreamento veicular inteligente, bloqueio remoto e ronda tática presencial. Mais de 10 anos protegendo famílias e frotas corporativas em todo o território nacional.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <span class="badge-footer-ssl"><i class="fas fa-lock text-success me-1"></i> SSL Blindado 256-bit</span>
                        <span class="badge-footer-ssl"><i class="fas fa-server text-danger me-1"></i> LiteSpeed 24h</span>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h5>Navegação</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#inicio">Início</a></li>
                        <li class="mb-2"><a href="#riscos">Benefícios</a></li>
                        <li class="mb-2"><a href="#sobre">Sobre Nós</a></li>
                        <li class="mb-2"><a href="#planos">Planos &amp; Preços</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h5>Atendimento</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-phone-alt text-danger me-2"></i> {{ $configs['telefone'] }}</li>
                        <li class="mb-2"><i class="fab fa-whatsapp text-success me-2"></i> {{ $configs['whatsapp_formatado'] }}</li>
                        <li class="mb-2"><i class="fas fa-envelope text-danger me-2"></i> {{ $configs['email'] }}</li>
                        <li class="mb-2"><i class="fas fa-clock text-danger me-2"></i> {{ $configs['horario'] }}</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-12">
                    <h5>Serviços &amp; Recursos</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#estetica" class="text-danger fw-bold"><i class="fas fa-spray-can-sparkles me-1"></i> Estética Automotiva</a></li>
                        <li class="mb-2"><a href="#ronda">Ronda Tática</a></li>
                        <li class="mb-2"><a href="#app">Aplicativo</a></li>
                        @if(isset($parceiros) && $parceiros->count() > 0)
                        <li class="mb-2"><a href="#parceiros">Empresas Parceiras</a></li>
                        @endif
                        @if(isset($filiais) && $filiais->count() > 0)
                        <li class="mb-2"><a href="#unidades">Onde Estamos / Filiais</a></li>
                        @endif
                        <li class="mb-2"><a href="#faq">Dúvidas Frequentes</a></li>
                        <li class="mb-2"><a href="{{ route('site.privacidade') }}" class="text-secondary"><i class="fas fa-shield-alt text-danger me-1"></i> Termos &amp; LGPD</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-light-subtle mt-5 pt-4 text-center small text-secondary">
                &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Todos os direitos reservados. &bull; <a href="{{ route('site.privacidade') }}" class="text-secondary text-decoration-underline ms-1">Política de Privacidade &amp; Termos (LGPD)</a>
            </div>
        </div>
    </footer>

    <!-- MODAL DE CHECKOUT TRANSPARENTE -->
    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-white text-dark border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" style="color: var(--foco-title);"><i class="fas fa-shield-alt text-danger me-2"></i> Contratar Plano & Ativação Imediata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="checkoutStep1">
                        <!-- RESUMO DO PLANO -->
                        <div class="p-3 rounded-3 mb-3 border bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <span class="small text-secondary d-block">Plano Escolhido:</span>
                                <h5 class="fw-bold mb-0 text-danger" id="checkoutPlanoNome">Plano</h5>
                            </div>
                            <div class="text-end">
                                <span class="small text-secondary d-block">Mensalidade Base:</span>
                                <span class="fw-bold text-dark fs-5" id="checkoutPlanoPreco">R$ 0,00</span>
                            </div>
                        </div>

                        <form id="formCheckout" action="{{ route('checkout.gerar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plano_id" id="checkoutPlanoId">
                            <input type="hidden" name="parcelas" id="checkoutParcelasHidden" value="1">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">Nome Completo *</label>
                                    <input type="text" name="nome" class="form-control" required placeholder="Seu nome completo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">E-mail *</label>
                                    <input type="email" name="email" class="form-control" required placeholder="seu@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">WhatsApp *</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" required placeholder="(91) 99999-9999">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary fw-semibold">CPF ou CNPJ *</label>
                                    <input type="text" name="cpf_cnpj" class="form-control mask-cpf-cnpj" required placeholder="000.000.000-00">
                                </div>
                            </div>

                            <!-- SELEÇÃO DA FORMA DE PAGAMENTO -->
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold mb-2">Forma de Pagamento Preferencial *</label>
                                <div class="row g-2" id="gridMetodosPagamento">
                                    <!-- PIX -->
                                    <div class="col-6 col-md-3">
                                        <label class="card h-100 p-2 text-center border cursor-pointer metodo-card active" style="cursor: pointer;">
                                            <input class="form-check-input d-none radio-metodo-pagamento" type="radio" name="metodo_pagamento" id="metodoPix" value="pix" checked>
                                            <i class="fab fa-pix text-success fs-3 my-1"></i>
                                            <span class="small fw-bold d-block text-dark">PIX Imediato</span>
                                            <span class="badge bg-success-subtle text-success small mt-1">Liberação 24h</span>
                                        </label>
                                    </div>

                                    <!-- BOLETO BANCÁRIO -->
                                    <div class="col-6 col-md-3">
                                        <label class="card h-100 p-2 text-center border cursor-pointer metodo-card" style="cursor: pointer;">
                                            <input class="form-check-input d-none radio-metodo-pagamento" type="radio" name="metodo_pagamento" id="metodoBoleto" value="boleto">
                                            <i class="fas fa-barcode text-primary fs-3 my-1"></i>
                                            <span class="small fw-bold d-block text-dark">Boleto Bancário</span>
                                            <span class="badge bg-secondary-subtle text-secondary small mt-1">3 dias úteis</span>
                                        </label>
                                    </div>

                                    <!-- CARNÊ EFÍ -->
                                    <div class="col-6 col-md-3">
                                        <label class="card h-100 p-2 text-center border cursor-pointer metodo-card" style="cursor: pointer;">
                                            <input class="form-check-input d-none radio-metodo-pagamento" type="radio" name="metodo_pagamento" id="metodoCarne" value="carne">
                                            <i class="fas fa-book text-warning fs-3 my-1"></i>
                                            <span class="small fw-bold d-block text-dark">Carnê em PDF</span>
                                            <span class="badge bg-warning-subtle text-dark small mt-1">Até 12x s/ cartão</span>
                                        </label>
                                    </div>

                                    <!-- CARTÃO DE CRÉDITO -->
                                    <div class="col-6 col-md-3">
                                        <label class="card h-100 p-2 text-center border cursor-pointer metodo-card" style="cursor: pointer;">
                                            <input class="form-check-input d-none radio-metodo-pagamento" type="radio" name="metodo_pagamento" id="metodoCartao" value="cartao">
                                            <i class="fas fa-credit-card text-danger fs-3 my-1"></i>
                                            <span class="small fw-bold d-block text-dark">Cartão de Crédito</span>
                                            <span class="badge bg-danger-subtle text-danger small mt-1">Até 12x</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- PARCELAMENTO DO CARNÊ -->
                            <div id="boxCarneParcelas" class="mb-3 p-3 bg-light border rounded-3 d-none">
                                <label class="form-label small fw-bold text-dark mb-1"><i class="fas fa-layer-group text-warning me-1"></i> Parcelamento do Carnê de Mensalidade</label>
                                <select id="selectCarneParcelas" class="form-select form-select-sm">
                                    <option value="2">2x parcelas mensais</option>
                                    <option value="3">3x parcelas mensais</option>
                                    <option value="6">6x parcelas mensais</option>
                                    <option value="12" selected>12x parcelas anuais (Recomendado)</option>
                                </select>
                                <small class="text-muted mt-1 d-block">Será gerado um carnê em PDF de alta qualidade com QR Code Pix em cada lâmina para quitação.</small>
                            </div>

                            <!-- CAMPOS DO CARTÃO DE CRÉDITO -->
                            <div id="boxCartaoCampos" class="mb-3 p-3 bg-light border rounded-3 d-none">
                                <h6 class="fw-bold small text-danger mb-2"><i class="fas fa-lock me-1"></i> Dados Seguros do Cartão (Criptografia Efí Transparente)</h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label small text-secondary mb-1">Número do Cartão</label>
                                        <input type="text" name="card_numero" id="cardNumero" class="form-control form-control-sm" placeholder="0000 0000 0000 0000" maxlength="19">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-secondary mb-1">Nome Impresso no Cartão</label>
                                        <input type="text" name="card_titular" id="cardTitular" class="form-control form-control-sm" placeholder="NOME COMO NO CARTÃO">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-secondary mb-1">Validade (MM/AA)</label>
                                        <input type="text" name="card_expiracao" id="cardExpiracao" class="form-control form-control-sm" placeholder="MM/AA" maxlength="5">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-secondary mb-1">Código CVV</label>
                                        <input type="password" name="card_cvv" id="cardCvv" class="form-control form-control-sm" placeholder="123" maxlength="4">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small text-secondary mb-1">Parcelas no Cartão</label>
                                        <select id="selectCartaoParcelas" class="form-select form-select-sm">
                                            <option value="1" selected>1x à vista</option>
                                            <option value="2">2x parcelas</option>
                                            <option value="3">3x parcelas</option>
                                            <option value="6">6x parcelas</option>
                                            <option value="12">12x parcelas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- RESUMO FINANCEIRO TRANSPARENTE DE VALORES & TAXAS REPASSADAS -->
                            <div class="p-3 rounded-3 mb-4 border" style="background: #f1f5f9;">
                                <div class="d-flex justify-content-between small text-secondary mb-1">
                                    <span>Valor Original do Plano:</span>
                                    <span id="resumoValorOriginal" class="fw-semibold">R$ 0,00</span>
                                </div>
                                <div id="linhaTaxaRepassada" class="d-flex justify-content-between small text-secondary mb-1 d-none">
                                    <span>Taxa Bancária / Emissão Repassada:</span>
                                    <span id="resumoTaxaRepassada" class="fw-semibold text-danger">+ R$ 0,00</span>
                                </div>
                                <div id="linhaDescontoPix" class="d-flex justify-content-between small text-secondary mb-1 d-none">
                                    <span>Desconto PIX Especial:</span>
                                    <span id="resumoDescontoPix" class="fw-semibold text-success">- R$ 0,00</span>
                                </div>
                                <hr class="my-2 border-secondary-subtle">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">Total a Pagar:</strong>
                                    <span id="resumoValorFinal" class="fs-4 fw-black text-danger">R$ 0,00</span>
                                </div>
                                <div id="resumoDetalhe" class="small text-muted mt-1 text-end"></div>
                            </div>

                            <button type="submit" id="btnSubmitCheckout" class="btn-foco-primary w-100 py-3 shadow-sm">
                                <i class="fas fa-lock me-1"></i> FINALIZAR CONTRATAÇÃO & ATIVAR
                            </button>
                        </form>
                    </div>

                    <!-- STEP 2: RESULTADO DO CHECKOUT TRANSPARENTE -->
                    <div id="checkoutStep2" class="d-none">
                        <!-- SUCESSO PIX -->
                        <div id="resPixBox" class="text-center d-none">
                            <div class="alert alert-success py-2 mb-3"><i class="fas fa-check-circle me-1"></i> Cobrança PIX Gerada com Sucesso!</div>
                            <img id="pixQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white mb-3 shadow-sm" style="max-width: 210px;">
                            <p class="small text-secondary mb-2">Escaneie o QR Code no app do seu banco ou copie o código abaixo:</p>
                            <div class="input-group mb-3">
                                <input type="text" id="pixCopiaCola" class="form-control bg-light small font-monospace" readonly>
                                <button class="btn btn-danger" type="button" id="btnCopiarPix"><i class="fas fa-copy me-1"></i> Copiar</button>
                            </div>
                        </div>

                        <!-- SUCESSO BOLETO -->
                        <div id="resBoletoBox" class="text-start d-none">
                            <div class="alert alert-success py-2 mb-3 text-center"><i class="fas fa-barcode me-1"></i> Boleto Bancário Emitido!</div>
                            <div class="p-3 bg-light rounded border mb-3">
                                <label class="small fw-bold text-secondary mb-1">Linha Digitável:</label>
                                <div class="input-group mb-3">
                                    <input type="text" id="boletoLinhaDigitavel" class="form-control form-control-sm font-monospace bg-white" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCopiarBoleto"><i class="fas fa-copy"></i></button>
                                </div>
                                <div class="text-center">
                                    <a href="#" id="btnAbrirBoleto" target="_blank" class="btn btn-primary btn-lg w-100 fw-bold">
                                        <i class="fas fa-print me-1"></i> Imprimir / Visualizar Boleto Bancário
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- SUCESSO CARNÊ EM PDF -->
                        <div id="resCarneBox" class="text-center d-none">
                            <div class="alert alert-success py-2 mb-3"><i class="fas fa-book me-1"></i> Carnê Emitido com Sucesso!</div>
                            <div class="p-4 bg-light rounded border mb-3 text-start">
                                <h6 class="fw-bold text-success mb-2"><i class="fas fa-file-invoice-dollar me-2"></i> Carnê de Mensalidades Efí Bank</h6>
                                <p class="small text-muted mb-2" id="resCarneInfo">Carnê com 12 parcelas mensais.</p>
                                <div class="d-grid gap-2 mt-3">
                                    <a href="#" id="btnBaixarCarnePdf" target="_blank" class="btn btn-success btn-lg fw-bold shadow-sm">
                                        <i class="fas fa-file-pdf me-2"></i> BAIXAR CARNÊ COMPLETO EM PDF (A4)
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- SUCESSO CARTÃO -->
                        <div id="resCartaoBox" class="text-center d-none">
                            <div class="alert alert-success py-3 mb-3">
                                <i class="fas fa-check-circle fs-1 text-success d-block mb-2"></i>
                                <h5 class="fw-bold mb-1">Pagamento com Cartão Aprovado!</h5>
                                <p class="small mb-0">Sua contratação foi processada e confirmada instantaneamente.</p>
                            </div>
                        </div>

                        <div class="alert alert-light border small text-secondary text-center mt-3 mb-0">
                            <i class="fas fa-headset text-danger me-1"></i> Nossa central entrará em contato via WhatsApp para o agendamento da instalação do seu rastreador.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE AGENDAMENTO ONLINE (RASTREAMENTO & ESTÉTICA AUTOMOTIVA) -->
    <div class="modal fade" id="modalAgendamentoSite" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-white text-dark border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" style="color: var(--foco-title);" id="modalAgendamentoSiteTitulo">
                        <i class="fas fa-calendar-check text-danger me-2"></i> Agendamento Online Foco 7
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAgendamentoSite" action="{{ route('site.agendar') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">Categoria de Atendimento *</label>
                                <select name="categoria" id="site_categoria" class="form-select" onchange="aoMudarCategoriaSite()" required>
                                    <option value="estetica">✨ Estética Automotiva / Detalhamento</option>
                                    <option value="rastreamento">📡 Rastreamento Veicular / Segurança</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="boxSiteServicoEstetica">
                                <label class="form-label small text-secondary fw-semibold">Serviço de Estética Desejado *</label>
                                <select name="servico_estetica_id" id="site_servico_estetica_id" class="form-select">
                                    <option value="">-- Selecione o serviço --</option>
                                    @foreach($servicosEstetica as $srv)
                                        <option value="{{ $srv->id }}" data-preco="{{ $srv->preco_formatado }}">
                                            {{ $srv->nome }} ({{ $srv->preco_formatado }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="boxSiteTipoRastreamento" style="display: none;">
                                <label class="form-label small text-secondary fw-semibold">Tipo de Atendimento *</label>
                                <select name="tipo_servico" id="site_tipo_servico" class="form-select">
                                    <option value="Instalação de Rastreador">Instalação de Rastreador Novo</option>
                                    <option value="Manutenção ou Troca de Chip">Manutenção / Troca de Chip</option>
                                    <option value="Vistoria Técnica">Vistoria Técnica Presencial</option>
                                    <option value="Troca de Veículo">Troca de Veículo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">Seu Nome Completo *</label>
                                <input type="text" name="cliente_nome" id="site_cliente_nome" class="form-control" required placeholder="Ex: Carlos Mendes">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">WhatsApp para Confirmação *</label>
                                <input type="text" name="cliente_telefone" id="site_cliente_telefone" class="form-control mask-telefone" required placeholder="(91) 98000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">E-mail</label>
                                <input type="email" name="cliente_email" id="site_cliente_email" class="form-control" placeholder="cliente@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">Modelo do Veículo (Carro/Moto) *</label>
                                <input type="text" name="veiculo_modelo" id="site_veiculo_modelo" class="form-control" required placeholder="Ex: Hilux 2023, Civic, CB 500">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">Sua Cidade *</label>
                                <input type="text" name="cidade" id="site_cidade" class="form-control" value="Belém" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-secondary fw-semibold">Data de Preferência</label>
                                <input type="date" name="data_preferencial" id="site_data_preferencial" class="form-control" min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-secondary fw-semibold">Observações / Detalhes Adicionais</label>
                                <textarea name="observacoes" id="site_observacoes" rows="2" class="form-control" placeholder="Algum detalhe importante sobre o veículo, mancha, estado da pintura ou horário preferido..."></textarea>
                            </div>

                            <!-- OPÇÃO DE PAGAMENTO: ONLINE OU NO LOCAL -->
                            <div class="col-12 mt-2">
                                <label class="form-label small text-dark fw-bold d-block mb-2">
                                    <i class="fas fa-credit-card text-danger me-1"></i> Escolha a Forma de Pagamento *
                                </label>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="foco-payment-option selected d-flex align-items-center gap-3" id="optPagamentoOnline" onclick="selecionarTipoPagamentoSite('online')">
                                            <div class="payment-check"><i class="fas fa-check fa-xs"></i></div>
                                            <div>
                                                <strong class="d-block text-dark small"><i class="fas fa-bolt text-danger me-1"></i> Pagamento Online</strong>
                                                <span class="text-secondary" style="font-size: 0.76rem;">PIX Instantâneo ou Cartão de Crédito</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="foco-payment-option d-flex align-items-center gap-3" id="optPagamentoLocal" onclick="selecionarTipoPagamentoSite('local')">
                                            <div class="payment-check"><i class="fas fa-check fa-xs" style="display:none;"></i></div>
                                            <div>
                                                <strong class="d-block text-dark small"><i class="fas fa-hand-holding-dollar text-success me-1"></i> Pagar no Local</strong>
                                                <span class="text-secondary" style="font-size: 0.76rem;">Dinheiro, Cartão ou PIX na instalação</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="tipo_pagamento" id="site_tipo_pagamento" value="online">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-bold" id="btnEnviarAgendamentoSite">
                            <i class="fas fa-check-circle me-1"></i> Confirmar Solicitação de Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DE AUTENTICAÇÃO OBRIGATÓRIA PARA AGENDAMENTO (LOGIN OU CADASTRO RÁPIDO) -->
    <div class="modal fade" id="modalAuthAgendamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white text-dark border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-3 border-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0 text-white">Identificação Necessária</h6>
                            <small class="text-white-50" style="font-size: 0.75rem;">Para prosseguir com o agendamento seguro</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="p-3 bg-light border-bottom">
                    <p class="small text-secondary mb-0">
                        <i class="fas fa-shield-alt text-danger me-1"></i> Para sua segurança e vinculação do veículo à sua conta, é necessário estar conectado. Entre com seus dados ou cadastre-se em segundos:
                    </p>
                </div>

                <!-- Abas Login / Cadastro -->
                <div class="p-3 pb-0">
                    <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold btn-sm" id="tab-login-btn" data-bs-toggle="pill" data-bs-target="#pill-login" type="button">
                                <i class="fas fa-sign-in-alt me-1"></i> Já Tenho Cadastro
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold btn-sm" id="tab-cadastro-btn" data-bs-toggle="pill" data-bs-target="#pill-cadastro" type="button">
                                <i class="fas fa-user-plus me-1"></i> Criar Conta Rápida
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="modal-body p-4 pt-3">
                    <div class="tab-content">
                        <!-- ABA 1: LOGIN RÁPIDO -->
                        <div class="tab-pane fade show active" id="pill-login" role="tabpanel">
                            <form id="formLoginRapido" action="{{ route('auth.rapido.login') }}" method="POST">
                                @csrf
                                <div class="d-none" aria-hidden="true">
                                    <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">E-mail Cadastrado</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" id="loginRapidoEmail" class="form-control" required placeholder="seu@email.com">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Senha</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password" id="loginRapidoPassword" class="form-control" required placeholder="••••••••">
                                        <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="#loginRapidoPassword" title="Visualizar / Ocultar Senha" tabindex="-1">
                                            <i class="fas fa-eye text-secondary"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <a href="{{ route('password.request') }}" target="_blank" class="small text-danger text-decoration-none">Esqueceu a senha?</a>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 fw-bold py-2" id="btnLoginRapidoSubmit">
                                    <i class="fas fa-sign-in-alt me-1"></i> ENTRAR E CONTINUAR AGENDAMENTO
                                </button>
                            </form>
                        </div>

                        <!-- ABA 2: CADASTRO RÁPIDO -->
                        <div class="tab-pane fade" id="pill-cadastro" role="tabpanel">
                            <form id="formCadastroRapido" action="{{ route('auth.rapido.cadastro') }}" method="POST">
                                @csrf
                                <div class="d-none" aria-hidden="true">
                                    <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Seu Nome Completo *</label>
                                    <input type="text" name="name" id="cadRapidoNome" class="form-control form-control-sm" required placeholder="Ex: Roberto Carlos">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">WhatsApp / Telefone *</label>
                                    <input type="text" name="telefone" id="cadRapidoTelefone" class="form-control form-control-sm mask-telefone" required placeholder="(91) 98000-0000">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">E-mail para Acesso *</label>
                                    <input type="email" name="email" id="cadRapidoEmail" class="form-control form-control-sm" required placeholder="seu@email.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Criar uma Senha *</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password" id="cadRapidoPassword" class="form-control form-control-sm" required minlength="6" placeholder="Mínimo 6 caracteres">
                                        <button class="btn btn-outline-secondary btn-toggle-password" type="button" data-target="#cadRapidoPassword" title="Visualizar / Ocultar Senha" tabindex="-1">
                                            <i class="fas fa-eye text-secondary"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger w-100 fw-bold py-2" id="btnCadastroRapidoSubmit">
                                    <i class="fas fa-check-circle me-1"></i> CRIAR CONTA E CONTINUAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ENVIO DE NOVO DEPOIMENTO COM ESTRELAS DINÂMICAS -->
    <div class="modal fade" id="modalNovoDepoimentoSite" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white text-dark border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark">Avalie o Atendimento Foco 7</h5>
                            <small class="text-secondary">Sua opinião é fundamental para nossa equipe!</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNovoDepoimentoSite" action="{{ route('site.depoimento.enviar') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 text-center py-2 bg-light rounded-3 border">
                                <label class="form-label small fw-bold text-dark d-block mb-1">Qual a sua nota para a Foco 7?</label>
                                <div class="foco-star-interactive my-1" id="starRatingSite">
                                    <i class="fas fa-star star-item active" data-rating="1" title="1 Estrela"></i>
                                    <i class="fas fa-star star-item active" data-rating="2" title="2 Estrelas"></i>
                                    <i class="fas fa-star star-item active" data-rating="3" title="3 Estrelas"></i>
                                    <i class="fas fa-star star-item active" data-rating="4" title="4 Estrelas"></i>
                                    <i class="fas fa-star star-item active" data-rating="5" title="5 Estrelas"></i>
                                </div>
                                <div id="starRatingSiteLabel" class="small fw-bold text-warning mt-1">5 de 5 estrelas - Atendimento Excelente!</div>
                                <input type="hidden" name="estrelas" id="depoimentoSiteEstrelas" value="5">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Seu Nome *</label>
                                <input type="text" name="nome_cliente" id="depSiteNome" class="form-control" required placeholder="Ex: Marcelo Oliveira">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Cidade - UF</label>
                                <input type="text" name="cidade_uf" id="depSiteCidade" class="form-control" value="Belém - PA" placeholder="Belém - PA">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Veículo Protegido ou Atendido (Opcional)</label>
                                <input type="text" name="veiculo" id="depSiteVeiculo" class="form-control" placeholder="Ex: Corolla Cross, Fan 160, Hilux">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Seu Depoimento / Avaliação *</label>
                                <textarea name="texto" id="depSiteTexto" rows="3" class="form-control" required placeholder="Conte em poucas palavras como foi sua experiência com a Ronda Tática, Bloqueio ou Atendimento..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-bold" id="btnEnviarDepoimentoSite">
                            <i class="fas fa-paper-plane me-1"></i> Publicar Avaliação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BANNER LGPD FLUTUANTE PREMIUM (CONSENTIMENTO DE COOKIES & PRIVACIDADE) -->
    @if(($configs['lgpd_ativo'] ?? '1') == '1')
    <div class="foco-lgpd-banner" id="bannerLgpdFoco7" role="dialog" aria-labelledby="lgpdCardTitle" aria-describedby="lgpdCardDesc" aria-modal="false">
        <div class="foco-lgpd-glow-line"></div>
        <div class="foco-lgpd-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="foco-lgpd-icon-shield">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="foco-lgpd-title" id="lgpdCardTitle">Privacidade & Cookies</h4>
                        <span class="foco-lgpd-badge-law">
                            <i class="fas fa-lock me-1"></i>LGPD 13.709/18
                        </span>
                    </div>
                    <span class="foco-lgpd-subtitle">Seus dados protegidos com criptografia</span>
                </div>
            </div>
            <button type="button" class="foco-lgpd-btn-close" id="btnFecharLgpd" aria-label="Fechar aviso de privacidade" title="Dispensar aviso">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="foco-lgpd-card-body">
            <p class="foco-lgpd-text" id="lgpdCardDesc">
                {{ $configs['lgpd_texto'] ?? 'Utilizamos cookies essenciais e tecnologias semelhantes para garantir o funcionamento seguro do nosso sistema, personalizar sua experiência e analisar o tráfego em conformidade com a LGPD (Lei nº 13.709/2018).' }}
            </p>
            <div class="foco-lgpd-tags-row">
                <span class="foco-lgpd-tag-item"><i class="fas fa-check-circle text-success me-1"></i>Essenciais</span>
                <span class="foco-lgpd-tag-item"><i class="fas fa-check-circle text-success me-1"></i>Segurança 24h</span>
                <span class="foco-lgpd-tag-item"><i class="fas fa-check-circle text-success me-1"></i>Performance</span>
            </div>
            <div class="foco-lgpd-policy-link">
                <a href="{{ route('site.privacidade') }}" class="foco-lgpd-link" target="_blank" rel="noopener">
                    <i class="fas fa-file-contract me-1"></i>{{ $configs['lgpd_btn_termos'] ?? 'Ler Política de Privacidade' }}
                    <i class="fas fa-arrow-up-right-from-square ms-1 text-xs"></i>
                </a>
            </div>
        </div>

        <div class="foco-lgpd-card-actions">
            <button type="button" class="foco-lgpd-btn-sec" id="btnEssenciaisLgpd" title="Permitir apenas os cookies estritamente necessários">
                <i class="fas fa-sliders me-1"></i>Apenas Essenciais
            </button>
            <button type="button" class="foco-lgpd-btn-pri" id="btnAceitarLgpd" title="Aceitar todos e continuar com a melhor experiência">
                <i class="fas fa-circle-check me-1"></i>{{ $configs['lgpd_btn_aceitar'] ?? 'Aceitar Todos' }}
            </button>
        </div>
    </div>
    @endif

    <!-- BOTÃO VOLTAR AO TOPO FLUTUANTE -->
    <button type="button" id="btnVoltarAoTopo" class="foco-btn-topo" aria-label="Voltar ao início da página" title="Voltar ao Topo" data-bs-toggle="tooltip" data-bs-placement="left">
        <i class="fas fa-arrow-up"></i>
    </button>

    @if(($configs['whatsapp_box_ativo'] ?? '1') == '1')
    <!-- BOTÃO FLUTUANTE DO WHATSAPP OFICIAL NA COR DA PALETA -->
    <button type="button" id="btnFocoWhatsappFloat" class="foco-whatsapp-float" aria-label="Abrir canais de suporte Foco 7" title="Central de Atendimento pelo WhatsApp" data-bs-toggle="tooltip" data-bs-placement="left">
        <span class="whatsapp-badge-pulse"></span>
        <i class="fab fa-whatsapp"></i>
    </button>

    <!-- BOX POP-UP MULTICANAIS DE SUPORTE -->
    <div class="foco-support-popup shadow-lg" id="popupSupportWhatsapp">
        <div class="foco-support-header">
            <button type="button" class="btn-close-support" id="btnCloseSupportPopup" aria-label="Fechar box">
                <i class="fas fa-times"></i>
            </button>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="android-pulse-dot" style="background:#22c55e;"></span>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size:0.68rem;">Online</span>
            </div>
            <h6 class="fw-bold mb-0 text-white">{{ $configs['whatsapp_box_titulo'] ?? 'Central de Atendimento Foco 7' }}</h6>
            <small class="text-white-50" style="font-size:0.75rem;">{{ $configs['whatsapp_box_subtitulo'] ?? 'Atendimento Humanizado • Selecione o canal desejado:' }}</small>
        </div>
        <div class="foco-support-body">
            @if(($configs['whatsapp_canal_1_ativo'] ?? '1') == '1')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp_canal_1_num'] ?? '559180510736') }}?text={{ urlencode($configs['whatsapp_canal_1_msg'] ?? 'Emergência Foco 7: Preciso de apoio da Ronda Tática ou Central 24h.') }}" target="_blank" class="foco-channel-card">
                <div class="foco-channel-icon text-danger bg-danger-subtle"><i class="fas {{ $configs['whatsapp_canal_1_icone'] ?? 'fa-shield-halved' }}"></i></div>
                <div class="foco-channel-info">
                    <p class="foco-channel-title">{{ $configs['whatsapp_canal_1_nome'] ?? 'Plantão Ronda Tática & Emergência 24h' }}</p>
                    <p class="foco-channel-desc">{{ $configs['whatsapp_canal_1_desc'] ?? 'Sinistros, roubo, furto ou assistência imediata' }}</p>
                </div>
                <i class="fas fa-chevron-right foco-channel-arrow"></i>
            </a>
            @endif

            @if(($configs['whatsapp_canal_2_ativo'] ?? '1') == '1')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp_canal_2_num'] ?? '559180510736') }}?text={{ urlencode($configs['whatsapp_canal_2_msg'] ?? 'Olá! Gostaria de uma cotação de rastreamento veicular com a Foco 7.') }}" target="_blank" class="foco-channel-card">
                <div class="foco-channel-icon text-danger bg-danger-subtle"><i class="fas {{ $configs['whatsapp_canal_2_icone'] ?? 'fa-tags' }}"></i></div>
                <div class="foco-channel-info">
                    <p class="foco-channel-title">{{ $configs['whatsapp_canal_2_nome'] ?? 'Comercial & Novos Planos' }}</p>
                    <p class="foco-channel-desc">{{ $configs['whatsapp_canal_2_desc'] ?? 'Cotações de carros, motos, frotas e promoções' }}</p>
                </div>
                <i class="fas fa-chevron-right foco-channel-arrow"></i>
            </a>
            @endif

            @if(($configs['whatsapp_canal_3_ativo'] ?? '1') == '1')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp_canal_3_num'] ?? '559180510736') }}?text={{ urlencode($configs['whatsapp_canal_3_msg'] ?? 'Olá! Preciso de suporte técnico sobre o rastreador / aplicativo.') }}" target="_blank" class="foco-channel-card">
                <div class="foco-channel-icon text-danger bg-danger-subtle"><i class="fas {{ $configs['whatsapp_canal_3_icone'] ?? 'fa-headset' }}"></i></div>
                <div class="foco-channel-info">
                    <p class="foco-channel-title">{{ $configs['whatsapp_canal_3_nome'] ?? 'Suporte Técnico & Aplicativo' }}</p>
                    <p class="foco-channel-desc">{{ $configs['whatsapp_canal_3_desc'] ?? 'Dúvidas sobre o app, bloqueio, login ou sinal GPS' }}</p>
                </div>
                <i class="fas fa-chevron-right foco-channel-arrow"></i>
            </a>
            @endif

            @if(($configs['whatsapp_canal_4_ativo'] ?? '1') == '1')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp_canal_4_num'] ?? '559180510736') }}?text={{ urlencode($configs['whatsapp_canal_4_msg'] ?? 'Olá! Gostaria de agendar um serviço de Estética Automotiva.') }}" target="_blank" class="foco-channel-card">
                <div class="foco-channel-icon text-warning bg-warning-subtle"><i class="fas {{ $configs['whatsapp_canal_4_icone'] ?? 'fa-spray-can-sparkles' }}"></i></div>
                <div class="foco-channel-info">
                    <p class="foco-channel-title">{{ $configs['whatsapp_canal_4_nome'] ?? 'Estética Automotiva & Detalhamento' }}</p>
                    <p class="foco-channel-desc">{{ $configs['whatsapp_canal_4_desc'] ?? 'Vitrificação, polimento técnico e higienização' }}</p>
                </div>
                <i class="fas fa-chevron-right foco-channel-arrow"></i>
            </a>
            @endif

            @if(($configs['whatsapp_canal_5_ativo'] ?? '1') == '1')
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp_canal_5_num'] ?? '559180510736') }}?text={{ urlencode($configs['whatsapp_canal_5_msg'] ?? 'Olá! Preciso de auxílio com minha fatura/mensalidade Foco 7.') }}" target="_blank" class="foco-channel-card">
                <div class="foco-channel-icon text-success bg-success-subtle"><i class="fas {{ $configs['whatsapp_canal_5_icone'] ?? 'fa-file-invoice-dollar' }}"></i></div>
                <div class="foco-channel-info">
                    <p class="foco-channel-title">{{ $configs['whatsapp_canal_5_nome'] ?? 'Financeiro & 2ª Via de Boletos' }}</p>
                    <p class="foco-channel-desc">{{ $configs['whatsapp_canal_5_desc'] ?? 'Segunda via de mensalidades, PIX e notas fiscais' }}</p>
                </div>
                <i class="fas fa-chevron-right foco-channel-arrow"></i>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Scripts CDN e Globais -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>

    <script>
        window.usuarioAutenticado = false;
        window.dadosUsuario = null;

        function aoMudarCategoriaSite() {
            var cat = $('#site_categoria').val();
            if (cat === 'estetica') {
                $('#boxSiteServicoEstetica').show();
                $('#boxSiteTipoRastreamento').hide();
                $('#site_servico_estetica_id').attr('required', 'required');
                $('#site_tipo_servico').removeAttr('required');
            } else {
                $('#boxSiteServicoEstetica').hide();
                $('#boxSiteTipoRastreamento').show();
                $('#site_servico_estetica_id').removeAttr('required');
                $('#site_tipo_servico').attr('required', 'required');
            }
        }

        function selecionarTipoPagamentoSite(tipo) {
            $('#site_tipo_pagamento').val(tipo);
            if (tipo === 'online') {
                $('#optPagamentoOnline').addClass('selected').find('.payment-check i').show();
                $('#optPagamentoLocal').removeClass('selected').find('.payment-check i').hide();
            } else {
                $('#optPagamentoLocal').addClass('selected').find('.payment-check i').show();
                $('#optPagamentoOnline').removeClass('selected').find('.payment-check i').hide();
            }
        }

        function abrirModalAgendamentoSite(categoria, servicoId, servicoNome) {
            $('#formAgendamentoSite')[0].reset();
            selecionarTipoPagamentoSite('online');

            if (categoria) {
                $('#site_categoria').val(categoria);
            } else {
                $('#site_categoria').val('estetica');
            }
            aoMudarCategoriaSite();

            if (servicoId) {
                $('#site_servico_estetica_id').val(servicoId);
            }

            // Se usuário já estiver logado, auto-preenche
            if (window.usuarioAutenticado && window.dadosUsuario) {
                $('#site_cliente_nome').val(window.dadosUsuario.name || '');
                $('#site_cliente_telefone').val(window.dadosUsuario.telefone || '');
                $('#site_cliente_email').val(window.dadosUsuario.email || '');
            }

            var modalEl = document.getElementById('modalAgendamentoSite');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        // ==========================================
        // DEPOIMENTOS COM ESTRELAS DINÂMICAS
        // ==========================================
        function abrirModalAvaliacaoSite() {
            $('#formNovoDepoimentoSite')[0].reset();
            setEstrelasSite(5);
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNovoDepoimentoSite'));
            modal.show();
        }

        function setEstrelasSite(nota) {
            nota = parseInt(nota) || 5;
            $('#depoimentoSiteEstrelas').val(nota);
            var labels = {
                1: '1 de 5 estrelas - Atendimento Regular',
                2: '2 de 5 estrelas - Atendimento Bom',
                3: '3 de 5 estrelas - Atendimento Muito Bom',
                4: '4 de 5 estrelas - Atendimento Ótimo!',
                5: '5 de 5 estrelas - Atendimento Excelente!'
            };
            $('#starRatingSiteLabel').text(labels[nota] || (nota + ' de 5 estrelas'));
            renderizarEstrelasVisualSite(nota);
        }

        function renderizarEstrelasVisualSite(nota) {
            $('#starRatingSite .star-item').each(function () {
                var r = parseInt($(this).data('rating'));
                if (r <= nota) {
                    $(this).removeClass('far text-muted').addClass('fas active text-warning');
                } else {
                    $(this).removeClass('fas active text-warning').addClass('far text-muted');
                }
            });
        }

        $(function () {
            // Checagem prévia de autenticação do cliente
            $.getJSON('{{ route("auth.status") }}', function (res) {
                if (res && res.authenticated) {
                    window.usuarioAutenticado = true;
                    window.dadosUsuario = res.user;
                }
            });

            // Botão Hambúrguer estilo Android (Abre a gaveta deslizante da esquerda para a direita)
            $('#btnOpenMenuDrawer').on('click', function (e) {
                e.preventDefault();
                var drawerEl = document.getElementById('menuDrawerAndroid');
                if (drawerEl) {
                    var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
                    bsOffcanvas.show();
                }
            });

            // ========================================================
            // BANNER LGPD PREMIUM: PERFORMANCE, PERSISTÊNCIA & UX
            // ========================================================
            (function () {
                var consentKey = 'foco7_lgpd_aceito';
                var $bannerLgpd = $('#bannerLgpdFoco7');
                if (!$bannerLgpd.length) return;

                var getCookie = function (name) {
                    var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
                    return match ? decodeURIComponent(match[2]) : null;
                };

                var savedConsent = localStorage.getItem(consentKey) || getCookie(consentKey);

                // Performance: se já aceito, remove imediatamente do DOM sem layout shift ou repaints
                if (savedConsent) {
                    $bannerLgpd.remove();
                    return;
                }

                // Exibição suave após carregamento dos recursos críticos (750ms)
                setTimeout(function () {
                    $bannerLgpd.addClass('show');
                }, 750);

                var saveAndDismiss = function (type, notifyMsg) {
                    try {
                        localStorage.setItem(consentKey, type);
                    } catch (e) {}

                    // Grava cookie por 1 ano (365 dias)
                    var d = new Date();
                    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = consentKey + '=' + encodeURIComponent(type) + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';

                    $bannerLgpd.addClass('closing');
                    setTimeout(function () {
                        $bannerLgpd.remove();
                    }, 420);

                    if (notifyMsg && window.foco7Notify) {
                        window.foco7Notify.success(notifyMsg);
                    }
                };

                $('#btnAceitarLgpd').on('click', function () {
                    saveAndDismiss('all', 'Preferências salvas! Todos os cookies foram autorizados com sucesso.');
                });

                $('#btnEssenciaisLgpd').on('click', function () {
                    saveAndDismiss('essential', 'Preferências salvas! Apenas cookies essenciais foram mantidos.');
                });

                $('#btnFecharLgpd').on('click', function () {
                    saveAndDismiss('essential', 'Aviso dispensado. Cookies essenciais mantidos.');
                });
            })();

            // Botão WhatsApp Flutuante na Cor da Paleta & Box Multicanais
            var $btnWhatsappFloat = $('#btnFocoWhatsappFloat');
            var $popupSupport = $('#popupSupportWhatsapp');
            var $btnCloseSupport = $('#btnCloseSupportPopup');

            $btnWhatsappFloat.on('click', function (e) {
                e.stopPropagation();
                $popupSupport.toggleClass('show');
            });

            $btnCloseSupport.on('click', function (e) {
                e.stopPropagation();
                $popupSupport.removeClass('show');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#popupSupportWhatsapp, #btnFocoWhatsappFloat').length) {
                    $popupSupport.removeClass('show');
                }
            });

            // Interação dinâmica das estrelas da avaliação pública
            $(document).on('mouseenter', '#starRatingSite .star-item', function () {
                var hoverRating = parseInt($(this).data('rating'));
                renderizarEstrelasVisualSite(hoverRating);
            });

            $(document).on('mouseleave', '#starRatingSite', function () {
                var currentRating = parseInt($('#depoimentoSiteEstrelas').val()) || 5;
                renderizarEstrelasVisualSite(currentRating);
            });

            $(document).on('click', '#starRatingSite .star-item', function () {
                var clickRating = parseInt($(this).data('rating'));
                setEstrelasSite(clickRating);
            });

            // Envio do formulário de novo depoimento do site
            $('#formNovoDepoimentoSite').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnEnviarDepoimentoSite');
                var origText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Enviando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(origText);
                        var modalEl = document.getElementById('modalNovoDepoimentoSite');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Muito Obrigado!',
                            text: res.message || 'Sua avaliação foi enviada com sucesso.',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Fechar'
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao enviar avaliação.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        if (window.foco7Notify) {
                            window.foco7Notify.error(msg);
                        }
                    }
                });
            });

            // Controle dinâmico do cabeçalho fixo (reduz topbar e adiciona elevação)
            var $headerFoco = $('#focoHeader');
            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 25) {
                    $headerFoco.addClass('scrolled');
                } else {
                    $headerFoco.removeClass('scrolled');
                }
            });

            // Fechamento suave do Drawer Android ao clicar em qualquer item
            $('#menuDrawerAndroid .android-nav-item, #menuDrawerAndroid .btn-foco-whatsapp, #menuDrawerAndroid .btn-dark, #menuDrawerAndroid .btn-outline-danger').on('click', function () {
                var drawerEl = document.getElementById('menuDrawerAndroid');
                if (drawerEl) {
                    var bsOffcanvas = bootstrap.Offcanvas.getInstance(drawerEl);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
            });

            // ========================================================
            // SCROLLSPY INTELIGENTE & NAVEGAÇÃO SUAVE DA LANDING PAGE
            // Sincroniza em tempo real o item ativo com a seção na tela
            // ========================================================
            var secoesScroll = ['inicio', 'riscos', 'sobre', 'planos', 'estetica', 'ronda', 'app', 'parceiros', 'unidades', 'faq', 'contato'];

            function definirMenuAtivo(hash) {
                $('.foco-navbar .nav-link').each(function () {
                    var href = $(this).attr('href');
                    if (href === hash) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });

                $('#menuDrawerAndroid .android-nav-item').each(function () {
                    var href = $(this).attr('href');
                    if (href === hash) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            }

            function atualizarScrollSpy() {
                var scrollTop = $(window).scrollTop();
                var headerH = ($('#focoHeader').outerHeight() || 80) + 40;
                var docHeight = $(document).height();
                var winHeight = $(window).height();

                // Topo da página (< 120px)
                if (scrollTop < 120) {
                    definirMenuAtivo('#inicio');
                    return;
                }

                // Fim da página / rodapé atingido: ativa a última seção (contato)
                if (scrollTop + winHeight >= docHeight - 80) {
                    definirMenuAtivo('#contato');
                    return;
                }

                // Determina a seção ativa mais próxima do topo da viewport
                var secaoAtiva = 'inicio';
                for (var i = 0; i < secoesScroll.length; i++) {
                    var id = secoesScroll[i];
                    var $el = $('#' + id);
                    if ($el.length) {
                        var topSecao = $el.offset().top - headerH;
                        if (scrollTop >= topSecao) {
                            secaoAtiva = id;
                        }
                    }
                }
                definirMenuAtivo('#' + secaoAtiva);
            }

            var scrollSpyTicking = false;
            $(window).on('scroll', function () {
                if (!scrollSpyTicking) {
                    window.requestAnimationFrame(function () {
                        atualizarScrollSpy();
                        scrollSpyTicking = false;
                    });
                    scrollSpyTicking = true;
                }
            });

            // Clique suave nos links de navegação com compensação exata do header fixo
            $(document).on('click', '.foco-navbar .nav-link[href^="#"], #menuDrawerAndroid .android-nav-item[href^="#"]', function (e) {
                var targetHash = $(this).attr('href');
                if (!targetHash || targetHash === '#' || targetHash === '#!') return;

                var $target = $(targetHash);
                if ($target.length) {
                    e.preventDefault();
                    var headerHeight = $('#focoHeader').outerHeight() || 80;
                    var posFinal = targetHash === '#inicio' ? 0 : Math.max(0, $target.offset().top - headerHeight + 5);

                    definirMenuAtivo(targetHash);

                    $('html, body').stop().animate({
                        scrollTop: posFinal
                    }, 450, 'swing');

                    var drawerEl = document.getElementById('menuDrawerAndroid');
                    if (drawerEl) {
                        var bsOffcanvas = bootstrap.Offcanvas.getInstance(drawerEl);
                        if (bsOffcanvas) bsOffcanvas.hide();
                    }
                }
            });

            // Executa no carregamento da página
            atualizarScrollSpy();

            // Controle dinâmico do botão Voltar ao Topo
            var $btnTopo = $('#btnVoltarAoTopo');
            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 320) {
                    $btnTopo.addClass('show');
                } else {
                    $btnTopo.removeClass('show');
                }
            });

            $btnTopo.on('click', function () {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // ==========================================
            // FLUXO DE CHECKOUT TRANSPARENTE EFÍ PRO
            // ==========================================
            function atualizarResumoTaxas() {
                var planoId = $('#checkoutPlanoId').val();
                if (!planoId) return;

                var metodo = $('input[name="metodo_pagamento"]:checked').val() || 'pix';
                var parcelas = 1;
                if (metodo === 'carne') {
                    parcelas = parseInt($('#selectCarneParcelas').val()) || 12;
                    $('#checkoutParcelasHidden').val(parcelas);
                } else if (metodo === 'cartao') {
                    parcelas = parseInt($('#selectCartaoParcelas').val()) || 1;
                    $('#checkoutParcelasHidden').val(parcelas);
                } else {
                    $('#checkoutParcelasHidden').val(1);
                }

                $.ajax({
                    url: '{{ route("checkout.calcular_taxas") }}',
                    type: 'GET',
                    data: {
                        plano_id: planoId,
                        metodo_pagamento: metodo,
                        parcelas: parcelas
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success && res.taxas) {
                            var t = res.taxas;
                            $('#resumoValorOriginal').text(t.valor_original_formatado);

                            if (t.taxa_repassada > 0) {
                                $('#linhaTaxaRepassada').removeClass('d-none');
                                $('#resumoTaxaRepassada').text('+ ' + t.taxa_repassada_formatada);
                            } else {
                                $('#linhaTaxaRepassada').addClass('d-none');
                            }

                            if (t.desconto > 0) {
                                $('#linhaDescontoPix').removeClass('d-none');
                                $('#resumoDescontoPix').text('- ' + t.desconto_formatado);
                            } else {
                                $('#linhaDescontoPix').addClass('d-none');
                            }

                            $('#resumoValorFinal').text(t.valor_final_formatado);
                            $('#resumoDetalhe').text(t.detalhe || '');
                        }
                    }
                });
            }

            // Seleção visual das formas de pagamento
            $(document).on('click', '.metodo-card', function () {
                var $card = $(this);
                $('.metodo-card').removeClass('active');
                $card.addClass('active');

                var $radio = $card.find('.radio-metodo-pagamento');
                $radio.prop('checked', true);
                var metodo = $radio.val();

                // Alternar campos contextuais
                if (metodo === 'carne') {
                    $('#boxCarneParcelas').removeClass('d-none');
                    $('#boxCartaoCampos').addClass('d-none');
                    $('#btnSubmitCheckout').html('<i class="fas fa-file-pdf me-1"></i> EMITIR CARNÊ EM PDF & ATIVAR');
                } else if (metodo === 'cartao') {
                    $('#boxCarneParcelas').addClass('d-none');
                    $('#boxCartaoCampos').removeClass('d-none');
                    $('#btnSubmitCheckout').html('<i class="fas fa-credit-card me-1"></i> PAGAR COM CARTÃO & ATIVAR');
                } else if (metodo === 'boleto') {
                    $('#boxCarneParcelas').addClass('d-none');
                    $('#boxCartaoCampos').addClass('d-none');
                    $('#btnSubmitCheckout').html('<i class="fas fa-barcode me-1"></i> GERAR BOLETO BANCÁRIO & ATIVAR');
                } else {
                    $('#boxCarneParcelas').addClass('d-none');
                    $('#boxCartaoCampos').addClass('d-none');
                    $('#btnSubmitCheckout').html('<i class="fab fa-pix me-1"></i> GERAR QR CODE PIX & ATIVAR');
                }

                atualizarResumoTaxas();
            });

            $('#selectCarneParcelas, #selectCartaoParcelas').on('change', function () {
                atualizarResumoTaxas();
            });

            // Modal de contratação de planos
            $('.btn-contratar').on('click', function () {
                var id = $(this).data('plano-id');
                var nome = $(this).data('plano-nome');
                var preco = $(this).data('plano-preco');

                $('#checkoutPlanoId').val(id);
                $('#checkoutPlanoNome').text(nome);
                $('#checkoutPlanoPreco').text(preco);

                // Reset do estado do modal
                $('#checkoutStep1').removeClass('d-none');
                $('#checkoutStep2').addClass('d-none');
                $('#resPixBox, #resBoletoBox, #resCarneBox, #resCartaoBox').addClass('d-none');

                // Garante que o Pix esteja selecionado como padrão
                $('#metodoPix').prop('checked', true);
                $('.metodo-card').removeClass('active');
                $('#metodoPix').closest('.metodo-card').addClass('active');
                $('#boxCarneParcelas, #boxCartaoCampos').addClass('d-none');
                $('#btnSubmitCheckout').html('<i class="fab fa-pix me-1"></i> GERAR QR CODE PIX & ATIVAR');

                atualizarResumoTaxas();

                var modal = new bootstrap.Modal(document.getElementById('modalCheckout'));
                modal.show();
            });

            // Resposta de geração de cobrança (Checkout rápido)
            $('#formCheckout').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnSubmitCheckout');
                var orig = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processando Checkout Seguro...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(orig);
                        if (res.success) {
                            window.foco7Notify.success(res.message);

                            $('#checkoutStep1').addClass('d-none');
                            $('#checkoutStep2').removeClass('d-none');
                            $('#resPixBox, #resBoletoBox, #resCarneBox, #resCartaoBox').addClass('d-none');

                            if (res.metodo === 'pix' && res.fatura) {
                                $('#resPixBox').removeClass('d-none');
                                $('#pixQrImg').attr('src', res.fatura.qr_code_pix_url);
                                $('#pixCopiaCola').val(res.fatura.qr_code_pix);
                            } else if (res.metodo === 'boleto' && res.fatura) {
                                $('#resBoletoBox').removeClass('d-none');
                                $('#boletoLinhaDigitavel').val(res.fatura.linha_digitavel || '');
                                $('#btnAbrirBoleto').attr('href', res.fatura.url_boleto || '#');
                            } else if (res.metodo === 'carne' && res.carne) {
                                $('#resCarneBox').removeClass('d-none');
                                $('#resCarneInfo').text('Carnê (' + res.carne.codigo + ') com ' + res.carne.total_parcelas + ' parcelas de ' + res.carne.valor_parcela);
                                $('#btnBaixarCarnePdf').attr('href', res.carne.pdf_url);
                            } else if (res.metodo === 'cartao') {
                                $('#resCartaoBox').removeClass('d-none');
                            }
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Atenção',
                                text: res.message || 'Erro ao processar cobrança.'
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = 'Erro ao processar dados de cobrança.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha no Checkout',
                            text: msg
                        });
                    }
                });
            });

            // Botão Copiar PIX
            $('#btnCopiarPix').on('click', function () {
                var copyText = document.getElementById("pixCopiaCola");
                if (copyText) {
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(copyText.value);
                    window.foco7Notify.success('Código PIX copiado com sucesso!');
                }
            });

            // Botão Copiar Boleto
            $('#btnCopiarBoleto').on('click', function () {
                var copyText = document.getElementById("boletoLinhaDigitavel");
                if (copyText) {
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(copyText.value);
                    window.foco7Notify.success('Linha digitável copiada!');
                }
            });

            // ==========================================
            // FLUXO DE AGENDAMENTO COM AUTH OBRIGATÓRIA
            // ==========================================
            function dispararAgendamentoAjax() {
                var $form = $('#formAgendamentoSite');
                var $btn = $('#btnEnviarAgendamentoSite');
                var orig = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Confirmando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(orig);
                        if (res.success) {
                            var modalEl = document.getElementById('modalAgendamentoSite');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();

                            var tipoNome = res.tipo_pagamento === 'online' ? 'Pagamento Online' : 'Pagamento no Local';

                            Swal.fire({
                                icon: 'success',
                                title: 'Agendamento Confirmado!',
                                html: '<p class="mb-2">' + res.message + '</p><span class="badge bg-danger p-2 px-3 mt-1">' + tipoNome + '</span>',
                                confirmButtonColor: '#d90000',
                                confirmButtonText: 'Entendido'
                            });
                        } else if (res.auth_required) {
                            exigirAutenticacaoAgendamento();
                        } else {
                            window.foco7Notify.error(res.message || 'Erro ao agendar.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        if (xhr.status === 401 || (xhr.responseJSON && xhr.responseJSON.auth_required)) {
                            exigirAutenticacaoAgendamento();
                            return;
                        }

                        var msg = 'Erro ao processar agendamento.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        window.foco7Notify.error(msg);
                    }
                });
            }

            function exigirAutenticacaoAgendamento() {
                // Preenche o e-mail no login se o visitante já digitou no agendamento
                var emailDigitado = $('#site_cliente_email').val();
                if (emailDigitado) {
                    $('#loginRapidoEmail').val(emailDigitado);
                    $('#cadRapidoEmail').val(emailDigitado);
                }
                var nomeDigitado = $('#site_cliente_nome').val();
                if (nomeDigitado) {
                    $('#cadRapidoNome').val(nomeDigitado);
                }
                var telDigitado = $('#site_cliente_telefone').val();
                if (telDigitado) {
                    $('#cadRapidoTelefone').val(telDigitado);
                }

                var modalAgendamento = bootstrap.Modal.getInstance(document.getElementById('modalAgendamentoSite'));
                if (modalAgendamento) modalAgendamento.hide();

                var modalAuth = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAuthAgendamento'));
                modalAuth.show();

                window.foco7Notify.info('Por favor, entre ou crie sua conta para finalizar o agendamento com segurança.');
            }

            // Submissão do agendamento do site
            $('#formAgendamentoSite').on('submit', function (e) {
                e.preventDefault();
                if (!window.usuarioAutenticado) {
                    exigirAutenticacaoAgendamento();
                    return;
                }
                dispararAgendamentoAjax();
            });

            // Login rápido via AJAX
            $('#formLoginRapido').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnLoginRapidoSubmit');
                var orig = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Conectando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(orig);
                        if (res.success) {
                            window.usuarioAutenticado = true;
                            window.dadosUsuario = res.user;

                            $('#site_cliente_nome').val(res.user.name);
                            $('#site_cliente_telefone').val(res.user.telefone || $('#site_cliente_telefone').val());
                            $('#site_cliente_email').val(res.user.email);

                            var modalAuth = bootstrap.Modal.getInstance(document.getElementById('modalAuthAgendamento'));
                            if (modalAuth) modalAuth.hide();

                            var modalAgendamento = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAgendamentoSite'));
                            modalAgendamento.show();

                            window.foco7Notify.success('Conectado com sucesso! Concluindo agendamento...');
                            dispararAgendamentoAjax();
                        } else {
                            window.foco7Notify.error(res.message || 'Erro ao efetuar login.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'E-mail ou senha incorretos.';
                        window.foco7Notify.error(msg);
                    }
                });
            });

            // Cadastro rápido via AJAX
            $('#formCadastroRapido').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnCadastroRapidoSubmit');
                var orig = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Criando conta...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(orig);
                        if (res.success) {
                            window.usuarioAutenticado = true;
                            window.dadosUsuario = res.user;

                            $('#site_cliente_nome').val(res.user.name);
                            $('#site_cliente_telefone').val(res.user.telefone || $('#site_cliente_telefone').val());
                            $('#site_cliente_email').val(res.user.email);

                            var modalAuth = bootstrap.Modal.getInstance(document.getElementById('modalAuthAgendamento'));
                            if (modalAuth) modalAuth.hide();

                            var modalAgendamento = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAgendamentoSite'));
                            modalAgendamento.show();

                            window.foco7Notify.success('Conta criada com sucesso! Concluindo agendamento...');
                            dispararAgendamentoAjax();
                        } else {
                            window.foco7Notify.error(res.message || 'Erro ao realizar cadastro.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = 'Erro ao cadastrar.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        window.foco7Notify.error(msg);
                    }
                });
            });

            // Proteção Google reCAPTCHA v3 no Formulário de Contato
            var recaptchaSiteKey = '{{ $configs['recaptcha_site_key'] ?? '' }}';
            var recaptchaAtivo = '{{ $configs['recaptcha_ativo'] ?? '0' }}';

            $('#formContatoSite').on('submit', function (e) {
                var $form = $(this);
                if (recaptchaAtivo === '1' && recaptchaSiteKey && typeof grecaptcha !== 'undefined') {
                    if (!$form.data('recaptcha-verified')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        var $btn = $form.find('[type="submit"]');
                        var origHtml = $btn.html();
                        $btn.prop('disabled', true).html('<i class="fas fa-shield-alt fa-spin me-2"></i> Verificando segurança...');

                        grecaptcha.ready(function () {
                            grecaptcha.execute(recaptchaSiteKey, { action: 'contato' }).then(function (token) {
                                $('#gRecaptchaContatoResponse').val(token);
                                $form.data('recaptcha-verified', true);
                                $btn.prop('disabled', false).html(origHtml);
                                $form.trigger('submit');
                            }).catch(function (err) {
                                console.warn('Aviso: falha na API reCAPTCHA, prosseguindo com segurança secundária:', err);
                                $form.data('recaptcha-verified', true);
                                $btn.prop('disabled', false).html(origHtml);
                                $form.trigger('submit');
                            });
                        });
                        return false;
                    } else {
                        // Reset para submissões subsequentes
                        $form.removeData('recaptcha-verified');
                    }
                }
            });

            // Botão copiar PIX
            $('#btnCopiarPix').on('click', function () {
                var copyText = document.getElementById("pixCopiaCola");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
                window.foco7Notify.success('Código PIX copiado para a área de transferência!');
            });

            // ========================================================
            // CARROSSEL DE DEPOIMENTOS: TOUCH, MOUSE DRAG & RESPONSIVO
            // ========================================================
            if (typeof Swiper !== 'undefined' && document.querySelector('.swiper-depoimentos')) {
                var swiperDepoimentos = new Swiper('.swiper-depoimentos', {
                    slidesPerView: 1, // 1 depoimento por vez por padrão (mobile / telas menores)
                    spaceBetween: 16,
                    grabCursor: true, // Cursor de mãozinha e grabbing ao clicar e arrastar
                    simulateTouch: true, // Permite clicar e arrastar com o mouse no computador
                    touchRatio: 1.2, // Resposta física rápida ao toque/arrasto
                    threshold: 5,
                    watchOverflow: true,
                    speed: 600,
                    autoplay: {
                        delay: 5500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: '.swiper-pagination-depoimentos',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '#btnDepoimentosNext',
                        prevEl: '#btnDepoimentosPrev',
                    },
                    breakpoints: {
                        // Telas menores que 768px (smartphones): exatamente 1 por vez na tela
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 16,
                        },
                        // Tablets (>= 768px): 2 por vez na tela
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        // Desktops (>= 1024px): 3 por vez na tela
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 24,
                        },
                        // Telas widescreen (>= 1280px): 4 por vez na tela
                        1280: {
                            slidesPerView: 4,
                            spaceBetween: 24,
                        }
                    }
                });
            }

            // ========================================================
            // CARROSSEL DE PARCEIROS: INFINITO, TOUCH, MOUSE DRAG & RESPONSIVO
            // ========================================================
            if (typeof Swiper !== 'undefined' && document.querySelector('.swiper-parceiros')) {
                var swiperParceiros = new Swiper('.swiper-parceiros', {
                    slidesPerView: 2,
                    spaceBetween: 16,
                    loop: true,
                    grabCursor: true,
                    simulateTouch: true,
                    touchRatio: 1.2,
                    speed: 700,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },
                    pagination: {
                        el: '.swiper-pagination-parceiros',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '#btnParceirosNext',
                        prevEl: '#btnParceirosPrev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 2,
                            spaceBetween: 12,
                        },
                        576: {
                            slidesPerView: 3,
                            spaceBetween: 16,
                        },
                        768: {
                            slidesPerView: 4,
                            spaceBetween: 20,
                        },
                        1024: {
                            slidesPerView: 5,
                            spaceBetween: 24,
                        },
                        1280: {
                            slidesPerView: 6,
                            spaceBetween: 24,
                        }
                    }
                });
            }
        });
    </script>

    @if(isset($filiais) && $filiais->count() > 0)
    <!-- Leaflet.js JS (Mapa Interativo do Brasil) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filiaisData = @json($filiais);
            const mapaContainer = document.getElementById('mapaBrasilFiliais');
            if (!mapaContainer || !filiaisData || filiaisData.length === 0) return;

            // Inicializa mapa centrado no Brasil
            const map = L.map('mapaBrasilFiliais', {
                center: [-14.235, -51.9253],
                zoom: 4,
                scrollWheelZoom: false
            });

            // Camada Oficial 100% Gratuita OpenStreetMap (Zero Custo / Sem Google Maps)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            map.on('focus', function () { map.scrollWheelZoom.enable(); });
            map.on('blur', function () { map.scrollWheelZoom.disable(); });

            const markers = {};
            const bounds = [];

            // Ícone estilizado Foco 7 (Pin Vermelho com Pulso)
            function criarIconeFoco7(destaque) {
                return L.divIcon({
                    className: 'custom-foco-div-icon',
                    html: `
                        <div class="foco-marker-container">
                            <div class="foco-map-marker-pin ${destaque ? 'foco-pin-matriz' : ''}"></div>
                            <div class="foco-map-marker-pulse"></div>
                        </div>
                    `,
                    iconSize: [36, 48],
                    iconAnchor: [18, 44],
                    popupAnchor: [0, -42]
                });
            }

            // Adiciona marcadores para cada filial com latitude e longitude válidas
            filiaisData.forEach(function (filial) {
                if (filial.latitude && filial.longitude) {
                    const lat = parseFloat(filial.latitude);
                    const lng = parseFloat(filial.longitude);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        bounds.push([lat, lng]);

                        const icone = criarIconeFoco7(filial.destaque);
                        const marker = L.marker([lat, lng], { icon: icone }).addTo(map);

                        const rotaOsmUrl = `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&to=${lat}%2C${lng}#map=17/${lat}/${lng}`;
                        const enderecoCompleto = `${filial.endereco || ''}, ${filial.bairro ? filial.bairro + ', ' : ''}${filial.cidade} - ${filial.estado}${filial.cep ? ', ' + filial.cep : ''}`;
                        const rotaGoogleUrl = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(enderecoCompleto)}`;
                        const rotaUrl = (filial.link_rota && !filial.link_rota.includes('-1.4557549')) ? filial.link_rota : rotaOsmUrl;
                        const wazeUrl = `https://waze.com/ul?ll=${lat},${lng}&navigate=yes`;
                        const telLimpo = filial.whatsapp ? filial.whatsapp.replace(/\D/g, '') : '';
                        const waUrl = telLimpo ? `https://wa.me/55${telLimpo}?text=Ol%C3%A1!%20Gostaria%20de%20informa%C3%A7%C3%B5es%20sobre%20a%20unidade%20${encodeURIComponent(filial.nome)}` : '';

                        let popupHtml = `
                            <div style="min-width: 260px; max-width: 320px; color: #fff;">
                                ${filial.foto ? `<div style="margin: -14px -16px 12px -16px; height: 110px; overflow: hidden; border-bottom: 2px solid #d90000;"><img src="/storage/${filial.foto}" style="width: 100%; height: 100%; object-fit: cover;" alt="${filial.nome}"></div>` : ''}
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                                    <h6 style="margin: 0; font-weight: 700; font-size: 0.95rem; color: #fff; line-height: 1.2;">${filial.nome}</h6>
                                    ${filial.destaque ? '<span style="background: #d90000; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 999px; margin-left: 6px; text-transform: uppercase;">Matriz</span>' : ''}
                                </div>
                                <div style="font-size: 0.78rem; color: #cbd5e1; margin-bottom: 8px;">
                                    <i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 4px;"></i>
                                    ${filial.endereco ? filial.endereco : ''}${filial.bairro ? ', ' + filial.bairro : ''}<br>
                                    <strong>${filial.cidade} - ${filial.estado}</strong>${filial.cep ? ' | CEP ' + filial.cep : ''}
                                </div>
                                ${filial.horario_atendimento ? `
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 8px;">
                                    <i class="fas fa-clock" style="margin-right: 4px;"></i> ${filial.horario_atendimento}
                                </div>` : ''}
                                ${filial.telefone || filial.whatsapp ? `
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-bottom: 12px;">
                                    ${filial.telefone ? `<span><i class="fas fa-phone-alt" style="margin-right: 3px;"></i> ${filial.telefone}</span> ` : ''}
                                    ${filial.whatsapp ? `<span><i class="fab fa-whatsapp" style="color: #22c55e; margin-left: 6px; margin-right: 3px;"></i> ${filial.whatsapp}</span>` : ''}
                                </div>` : ''}
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 10px;">
                                    <a href="${rotaOsmUrl}" target="_blank" rel="noopener noreferrer" style="background: #d90000; color: #fff; padding: 8px 10px; border-radius: 8px; text-decoration: none; font-size: 0.78rem; font-weight: 700; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 8px rgba(217,0,0,0.4);" title="Traçar rota no OpenStreetMap gratuito">
                                        <i class="fas fa-route"></i> Traçar Rota no OpenStreetMap
                                    </a>
                                    <div style="display: flex; gap: 6px;">
                                        <a href="${rotaGoogleUrl}" target="_blank" rel="noopener noreferrer" style="flex: 1; background: #1e293b; color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15); padding: 6px 8px; border-radius: 6px; text-decoration: none; font-size: 0.72rem; font-weight: 600; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" title="Abrir endereço exato no Google Maps">
                                            <i class="fab fa-google text-warning"></i> Google Maps
                                        </a>
                                        <a href="${wazeUrl}" target="_blank" rel="noopener noreferrer" style="flex: 1; background: #1e293b; color: #cbd5e1; border: 1px solid rgba(255,255,255,0.15); padding: 6px 8px; border-radius: 6px; text-decoration: none; font-size: 0.72rem; font-weight: 600; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" title="Abrir no Waze">
                                            <i class="fab fa-waze text-info"></i> Waze
                                        </a>
                                        ${waUrl ? `
                                        <a href="${waUrl}" target="_blank" rel="noopener noreferrer" style="background: #16a34a; color: #fff; padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;" title="Falar no WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupHtml, { maxWidth: 340 });
                        markers[filial.id] = marker;

                        marker.on('click', function () {
                            destacarCardFilial(filial.id);
                        });
                    }
                }
            });

            // Ajusta o enquadramento inicial para englobar todas as filiais
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
            }

            // Ação de focar filial a partir do card
            window.focarFilialNoMapa = function (id, lat, lng) {
                destacarCardFilial(id);
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    map.flyTo([lat, lng], 15, { duration: 1.2 });
                    if (markers[id]) {
                        setTimeout(() => markers[id].openPopup(), 600);
                    }
                }
            };

            function destacarCardFilial(id) {
                document.querySelectorAll('.card-filial-item').forEach(c => c.classList.remove('active'));
                const card = document.getElementById('card-filial-' + id);
                if (card) {
                    card.classList.add('active');
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            function resetarVisaoGeral() {
                document.querySelectorAll('.card-filial-item').forEach(c => c.classList.remove('active'));
                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
                } else {
                    map.flyTo([-14.235, -51.9253], 4);
                }
            }

            const btnResetar = document.getElementById('btnResetarMapa');
            if (btnResetar) btnResetar.addEventListener('click', resetarVisaoGeral);

            const btnVisaoGeral = document.getElementById('btnMapaVisaoGeral');
            if (btnVisaoGeral) btnVisaoGeral.addEventListener('click', resetarVisaoGeral);

            // Busca em tempo real nos cards
            const filtroInput = document.getElementById('filtroFiliaisInput');
            if (filtroInput) {
                filtroInput.addEventListener('input', function () {
                    const termo = this.value.toLowerCase().trim();
                    let visiveis = 0;
                    document.querySelectorAll('.card-filial-item').forEach(function (card) {
                        const cidade = card.getAttribute('data-cidade') || '';
                        const estado = card.getAttribute('data-estado') || '';
                        const nome = card.getAttribute('data-nome') || '';
                        if (cidade.includes(termo) || estado.includes(termo) || nome.includes(termo)) {
                            card.classList.remove('d-none');
                            visiveis++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    const contador = document.getElementById('filiaisContador');
                    if (contador) contador.textContent = visiveis;

                    const msgVazio = document.getElementById('filiaisVazioMsg');
                    if (msgVazio) {
                        if (visiveis === 0) {
                            msgVazio.classList.remove('d-none');
                        } else {
                            msgVazio.classList.add('d-none');
                        }
                    }
                });
            }
        });
    </script>
    @endif
</body>
</html>
