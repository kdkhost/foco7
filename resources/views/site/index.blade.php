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

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    </style>
</head>
<body id="inicio">

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
                    $cleanPath = !empty($slide->imagem) ? (parse_url($slide->imagem, PHP_URL_PATH) ?: $slide->imagem) : '';
                    if (!empty($slide->imagem) && !str_contains($slide->imagem, 'hero-banner-') && file_exists(public_path(ltrim($cleanPath, '/')))) {
                        $slideImg = asset($slide->imagem);
                    } elseif (!empty($heroBgImagem)) {
                        $slideImg = $heroBgImagem;
                    }
                @endphp
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    @if(!empty($slideImg))
                        <!-- Imagem de fundo em tela cheia (100% de ponta a ponta sem corte) -->
                        <div class="hero-slide-bg" style="background-image: url('{{ $slideImg }}'); {{ $heroBlur > 0 ? "filter: blur({$heroBlur}px); transform: scale(1.05);" : '' }}"></div>
                    @endif
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
                    @if(!empty($heroBgImagem))
                        <div class="hero-slide-bg" style="background-image: url('{{ $heroBgImagem }}'); {{ $heroBlur > 0 ? "filter: blur({$heroBlur}px); transform: scale(1.05);" : '' }}"></div>
                    @endif
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
            <div class="container position-relative hero-nav-container">
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
                                <i class="fas {{ $plano->icone ?? 'fa-shield-alt' }}"></i>
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
                        <button class="btn {{ $plano->destaque ? 'btn-foco-primary' : 'btn-outline-danger' }} w-100 py-3 mt-auto fw-bold btn-contratar"
                                data-plano-id="{{ $plano->id }}"
                                data-plano-nome="{{ $plano->nome }}"
                                data-plano-preco="{{ $plano->preco_formatado }}">
                            <i class="fas fa-shield-alt me-1"></i> CONTRATAR AGORA
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
                        <img src="{{ asset($appMockupImg) }}" alt="{{ $configs['secao_app_card_titulo'] ?? 'Aplicativo Foco 7' }}" class="img-fluid rounded-4 mb-3 shadow-lg" style="max-height: 380px; object-fit: contain;">
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
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Quem Já Dorme Tranquilo</span>
                <h2 class="display-6 fw-black mt-2" style="color: var(--foco-title);">Depoimentos Reais de Nossos Clientes</h2>
            </div>
            <div class="row g-4">
                @foreach($depoimentos as $dep)
                <div class="col-md-6 col-lg-3">
                    <div class="card-depoimento h-100">
                        <div>
                            <div class="text-warning mb-3">
                                @for($i = 0; $i < $dep->estrelas; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <div class="text-secondary small fst-italic">"{!! $dep->texto !!}"</div>
                        </div>
                        <div class="mt-3 pt-3 border-top border-light-subtle d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                {{ substr($dep->nome_cliente, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $dep->nome_cliente }}</h6>
                                <span class="small text-secondary">{{ $dep->cidade_uf }} - {{ $dep->veiculo }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold rounded-pill shadow-sm" onclick="abrirModalAvaliacaoSite()">
                    <i class="fas fa-star text-warning me-2"></i> Avaliar Nosso Atendimento / Deixar Depoimento
                </button>
            </div>
        </div>
    </section>

    <!-- 10. PERGUNTAS FREQUENTES (FAQ) -->
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
                        <form action="{{ route('site.contato') }}" method="POST" class="ajax-form reset-on-success">
                            @csrf
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
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn-foco-primary w-100 py-3">
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
                        <li class="mb-2"><a href="#planos">Planos & Preços</a></li>
                        <li class="mb-2"><a href="#estetica" class="text-danger fw-bold"><i class="fas fa-sparkles me-1"></i> Estética Automotiva</a></li>
                        <li class="mb-2"><a href="#ronda">Ronda Tática</a></li>
                        <li class="mb-2"><a href="#app">Aplicativo</a></li>
                        <li class="mb-2"><a href="#faq">Dúvidas Frequentes</a></li>
                        <li class="mb-2"><a href="{{ route('site.privacidade') }}" class="text-secondary"><i class="fas fa-shield-alt text-danger me-1"></i> Termos & LGPD</a></li>
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
                <div class="col-lg-3">
                    <h5>Área Segura</h5>
                    <p class="small text-secondary">Acesso exclusivo para clientes e operadores de frota.</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 py-2 mb-2 fw-semibold">
                        <i class="fas fa-user-lock me-1"></i> Acessar Painel / Área do Cliente
                    </a>
                </div>
            </div>
            <div class="border-top border-light-subtle mt-5 pt-4 text-center small text-secondary">
                &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Todos os direitos reservados. &bull; <a href="{{ route('site.privacidade') }}" class="text-secondary text-decoration-underline ms-1">Política de Privacidade & Termos (LGPD)</a>
            </div>
        </div>
    </footer>

    <!-- MODAL DE CHECKOUT TRANSPARENTE -->
    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white text-dark border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" style="color: var(--foco-title);"><i class="fas fa-shield-alt text-danger me-2"></i> Contratar Plano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="checkoutStep1">
                        <div class="p-3 rounded-3 mb-3 border" style="background: #f8fafc;">
                            <span class="small text-secondary">Plano Selecionado:</span>
                            <h5 class="fw-bold mb-0 text-danger" id="checkoutPlanoNome">Plano</h5>
                            <span class="fw-bold text-dark fs-5" id="checkoutPlanoPreco">R$ 0,00</span>
                        </div>
                        <form id="formCheckout" action="{{ route('checkout.gerar') }}" method="POST" class="ajax-form">
                            @csrf
                            <input type="hidden" name="plano_id" id="checkoutPlanoId">
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">Nome Completo *</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-secondary fw-semibold">E-mail *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small text-secondary fw-semibold">WhatsApp *</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-secondary fw-semibold">CPF ou CNPJ *</label>
                                    <input type="text" name="cpf_cnpj" class="form-control mask-cpf-cnpj" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small text-secondary fw-semibold">Forma de Pagamento *</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check flex-fill p-2 border rounded bg-light">
                                        <input class="form-check-input" type="radio" name="metodo_pagamento" id="metodoPix" value="pix" checked>
                                        <label class="form-check-label small fw-bold" for="metodoPix"><i class="fab fa-pix text-success"></i> PIX Imediato</label>
                                    </div>
                                    <div class="form-check flex-fill p-2 border rounded bg-light">
                                        <input class="form-check-input" type="radio" name="metodo_pagamento" id="metodoCartao" value="cartao">
                                        <label class="form-check-label small fw-bold" for="metodoCartao"><i class="fas fa-credit-card text-danger"></i> Cartão</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn-foco-primary w-100 py-3">
                                <i class="fas fa-arrow-right me-1"></i> GERAR COBRANÇA & ATIVAR
                            </button>
                        </form>
                    </div>

                    <div id="checkoutStep2" class="d-none text-center">
                        <div class="alert alert-success py-2 mb-3">Cobrança PIX Gerada com Sucesso!</div>
                        <img id="pixQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white mb-3 shadow-sm" style="max-width: 200px;">
                        <p class="small text-secondary mb-2">Escaneie o QR Code no app do seu banco ou use a chave Copia e Cola:</p>
                        <div class="input-group mb-3">
                            <input type="text" id="pixCopiaCola" class="form-control bg-light small" readonly>
                            <button class="btn btn-danger" type="button" id="btnCopiarPix">Copiar</button>
                        </div>
                        <div class="small text-secondary">Após a confirmação do pagamento, entraremos em contato para agendar sua instalação!</div>
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
                                    <input type="password" name="password" id="cadRapidoPassword" class="form-control form-control-sm" required minlength="6" placeholder="Mínimo 6 caracteres">
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

    <!-- BANNER LGPD FLUTUANTE (CONSENTIMENTO DE COOKIES) -->
    @if(($configs['lgpd_ativo'] ?? '1') == '1')
    <div class="foco-lgpd-banner" id="bannerLgpdFoco7">
        <div class="foco-lgpd-content">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle p-2 d-none d-sm-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <p class="foco-lgpd-text">
                    {{ $configs['lgpd_texto'] ?? 'Utilizamos cookies essenciais e tecnologias semelhantes para garantir o funcionamento seguro do nosso sistema, personalizar sua experiência e analisar o tráfego em conformidade com a LGPD (Lei nº 13.709/2018).' }}
                    <a href="{{ route('site.privacidade') }}" class="ms-1">{{ $configs['lgpd_btn_termos'] ?? 'Ler Política de Privacidade' }}</a>
                </p>
            </div>
            <button type="button" class="foco-lgpd-btn" id="btnAceitarLgpd">
                <i class="fas fa-check-circle me-1"></i> {{ $configs['lgpd_btn_aceitar'] ?? 'Aceitar e Continuar' }}
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

            // Banner LGPD com cookie/localStorage
            var lgpdAceito = localStorage.getItem('foco7_lgpd_aceito');
            var $bannerLgpd = $('#bannerLgpdFoco7');
            if (!lgpdAceito && $bannerLgpd.length) {
                setTimeout(function () {
                    $bannerLgpd.addClass('show');
                }, 1200);
            }

            $('#btnAceitarLgpd').on('click', function () {
                localStorage.setItem('foco7_lgpd_aceito', '1');
                $bannerLgpd.fadeOut(300, function () {
                    $(this).removeClass('show');
                });
                if (window.foco7Notify) {
                    window.foco7Notify.success('Suas preferências de privacidade foram salvas com sucesso.');
                }
            });

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

            // Modal de contratação de planos
            $('.btn-contratar').on('click', function () {
                var id = $(this).data('plano-id');
                var nome = $(this).data('plano-nome');
                var preco = $(this).data('plano-preco');

                $('#checkoutPlanoId').val(id);
                $('#checkoutPlanoNome').text(nome);
                $('#checkoutPlanoPreco').text(preco);

                $('#checkoutStep1').removeClass('d-none');
                $('#checkoutStep2').addClass('d-none');

                var modal = new bootstrap.Modal(document.getElementById('modalCheckout'));
                modal.show();
            });

            // Resposta de geração de cobrança (Checkout rápido)
            $('#formCheckout').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $form.find('[type="submit"]');
                var orig = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Gerando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(orig);
                        if (res.success && res.fatura) {
                            window.foco7Notify.success(res.message);
                            $('#pixQrImg').attr('src', res.fatura.qr_code_pix_url);
                            $('#pixCopiaCola').val(res.fatura.qr_code_pix);
                            $('#checkoutStep1').addClass('d-none');
                            $('#checkoutStep2').removeClass('d-none');
                        } else {
                            window.foco7Notify.error(res.message || 'Erro ao gerar cobrança.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = 'Erro ao processar dados de cobrança.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        window.foco7Notify.error(msg);
                    }
                });
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

            // Botão copiar PIX
            $('#btnCopiarPix').on('click', function () {
                var copyText = document.getElementById("pixCopiaCola");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
                window.foco7Notify.success('Código PIX copiado para a área de transferência!');
            });
        });
    </script>
</body>
</html>
