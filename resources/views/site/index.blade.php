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
</head>
<body id="inicio">

    <!-- 1. TOPBAR FIXO DE CONTATO E ÁREA DO CLIENTE -->
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
                    <a href="{{ $configs['instagram'] }}" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                @endif
                @if(!empty($configs['facebook']))
                    <a href="{{ $configs['facebook'] }}" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                <a href="{{ route('login') }}" class="btn-cliente-topo">
                    <i class="fas fa-user-lock me-1"></i> Área do Cliente / Painel
                </a>
            </div>
        </div>
    </div>

    <!-- 2. NAVBAR NAVEGAÇÃO SUAVE -->
    <nav class="navbar navbar-expand-lg foco-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#inicio">
                <img src="{{ asset($configs['logo_dark'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="40" class="logo-foco7 d-inline-block" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navFoco" aria-label="Menu de Navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navFoco">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#riscos">Benefícios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="#planos">Planos</a></li>
                    <li class="nav-item"><a class="nav-link text-nowrap fw-semibold" href="#estetica"><i class="fas fa-sparkles text-danger me-1"></i>Estética</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ronda">Ronda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#app">App</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">Dúvidas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger d-none d-xl-inline-flex align-items-center gap-1" onclick="abrirModalAgendamentoSite()">
                        <i class="fas fa-calendar-check"></i> Agendar
                    </button>
                    <a href="{{ route('login') }}" class="btn-foco-login-nav d-none d-lg-inline-flex">
                        <i class="fas fa-user-lock"></i> Área do Cliente
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp'] ?? '559180510736') }}?text=Ol%C3%A1,%20gostaria%20de%20proteger%20meu%20ve%C3%ADculo%20com%20a%20Foco%207!" target="_blank" class="btn-foco-whatsapp btn-sm px-3 py-2">
                        <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm d-lg-none">
                        <i class="fas fa-lock"></i> Entrar
                    </a>
                </div>
            </div>
        </div>
    </nav>

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

                    <!-- Conteúdo em container centralizado com alinhamento impecável -->
                    <div class="container position-relative hero-slide-content">
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
                                <div class="d-flex flex-wrap gap-3">
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
                @empty
                <div class="carousel-item active">
                    @if(!empty($heroBgImagem))
                        <div class="hero-slide-bg" style="background-image: url('{{ $heroBgImagem }}'); {{ $heroBlur > 0 ? "filter: blur({$heroBlur}px); transform: scale(1.05);" : '' }}"></div>
                    @endif
                    <div class="hero-slide-overlay hero-bg-overlay" style="background: linear-gradient(90deg, {{ $heroOverlayCor }}f5 0%, {{ $heroOverlayCor }}d9 45%, {{ $heroOverlayCor }}8c 75%, {{ $heroOverlayCor }}33 100%); opacity: {{ $heroOpacidadeDecimal }};"></div>
                    <div class="container position-relative hero-slide-content">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="hero-title">Proteger hoje para que você viva o amanhã com tranquilidade</h1>
                                <p class="hero-desc">Monitoramento veicular 24h, bloqueio imediato e Ronda Tática presencial.</p>
                                <a href="#planos" class="btn-foco-primary">PROTEGER MEU VEÍCULO AGORA</a>
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
                        <i class="fas fa-info-circle text-primary me-2"></i> Novos pacotes de estética automotiva sendo adicionados pela equipe.
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
                <span class="text-danger fw-bold text-uppercase small">Apoio Presencial Especializado</span>
                <h2 class="display-6 fw-black mt-2">🛡️ Como Funciona a Ronda Tática Foco 7</h2>
                <p class="text-secondary fs-5">Tecnologia aliada à ação humana imediata para localizar e recuperar seu patrimônio.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">1</div>
                        <h5 class="fw-bold mb-2">Alarme ou Sinistro</h5>
                        <p class="small text-secondary mb-0">Você aciona o botão de emergência pelo app ou a central detecta movimentação ou bloqueio não autorizado.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">2</div>
                        <h5 class="fw-bold mb-2">Central 24h Aciona</h5>
                        <p class="small text-secondary mb-0">Nossos operadores isolam o perímetro, realizam o bloqueio progressivo e geram o mapa da rota do veículo.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">3</div>
                        <h5 class="fw-bold mb-2">Viatura a Caminho</h5>
                        <p class="small text-secondary mb-0">A viatura ou moto tática mais próxima é despachada imediatamente com as coordenadas exatas do alvo.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="etapa-ronda-card">
                        <div class="etapa-numero">4</div>
                        <h5 class="fw-bold mb-2">Recuperação & Apoio</h5>
                        <p class="small text-secondary mb-0">A equipe chega ao local, aciona as autoridades competentes e preserva seu veículo até sua devolução.</p>
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
                        @if(!empty($configs['secao_app_mockup_imagem']))
                            <img src="{{ asset($configs['secao_app_mockup_imagem']) }}" alt="{{ $configs['secao_app_card_titulo'] ?? 'Aplicativo Foco 7' }}" class="img-fluid rounded-4 mb-3" style="max-height: 280px; object-fit: contain;">
                        @else
                            <i class="fas fa-mobile-alt text-danger" style="font-size: 8rem;"></i>
                        @endif
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
                            <div class="bg-danger text-white rounded-circle p-3"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <span class="small text-secondary">Telefone Central</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $configs['telefone'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="bg-success text-white rounded-circle p-3"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <span class="small text-secondary">WhatsApp Direto</span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $configs['whatsapp_formatado'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border">
                            <div class="bg-danger text-white rounded-circle p-3"><i class="fas fa-map-marker-alt"></i></div>
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
                    <img src="{{ asset($configs['logo_dark'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] }}" height="44" class="mb-3 logo-foco7" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
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
                &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Todos os direitos reservados.
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
                                        <label class="form-check-label small fw-bold" for="metodoCartao"><i class="fas fa-credit-card text-primary"></i> Cartão</label>
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

    <!-- BOTÃO VOLTAR AO TOPO FLUTUANTE -->
    <button type="button" id="btnVoltarAoTopo" class="foco-btn-topo" aria-label="Voltar ao início da página" title="Voltar ao Topo">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- BOTÃO FLUTUANTE DO WHATSAPP OFICIAL -->
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $configs['whatsapp'] ?? '559180510736') }}?text=Ol%C3%A1!%20Gostaria%20de%20proteger%20meu%20ve%C3%ADculo%20com%20o%20rastreamento%20Foco%207." 
       class="foco-whatsapp-float" 
       target="_blank" 
       rel="noopener noreferrer" 
       aria-label="Falar pelo WhatsApp com a Foco 7" 
       title="Fale Conosco pelo WhatsApp">
        <span class="whatsapp-tooltip"><i class="fab fa-whatsapp text-success me-1"></i> Fale Conosco: {{ $configs['telefone'] ?? '(91) 8051-0736' }}</span>
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts CDN e Globais -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>

    <script>
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

        function abrirModalAgendamentoSite(categoria, servicoId, servicoNome) {
            $('#formAgendamentoSite')[0].reset();
            var modalEl = document.getElementById('modalAgendamentoSite');
            var modal = new bootstrap.Modal(modalEl);

            if (categoria) {
                $('#site_categoria').val(categoria);
            } else {
                $('#site_categoria').val('estetica');
            }
            aoMudarCategoriaSite();

            if (servicoId) {
                $('#site_servico_estetica_id').val(servicoId);
            }

            modal.show();
        }

        $(function () {
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

            // Modal de contratação
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

            // Resposta personalizada de geração de cobrança
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

            // Submissão do agendamento do site
            $('#formAgendamentoSite').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnEnviarAgendamentoSite');
                var orig = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Enviando...');

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

                            Swal.fire({
                                icon: 'success',
                                title: 'Solicitação Enviada!',
                                text: res.message,
                                confirmButtonColor: '#d90000',
                                confirmButtonText: 'Entendido'
                            });
                        } else {
                            window.foco7Notify.error(res.message || 'Erro ao agendar.');
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(orig);
                        var msg = 'Erro ao processar agendamento.';
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
