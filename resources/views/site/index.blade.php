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
    <link rel="stylesheet" href="{{ asset('css/site-foco7.css') }}">
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
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light px-3 py-1 rounded-pill" style="font-size: 0.78rem;">
                    <i class="fas fa-user-lock me-1"></i> Área do Cliente / Painel
                </a>
            </div>
        </div>
    </div>

    <!-- 2. NAVBAR NAVEGAÇÃO SUAVE -->
    <nav class="navbar navbar-expand-lg navbar-dark foco-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#inicio">
                <span class="fs-3 fw-black text-danger">FOCO<span class="text-white">7</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navFoco">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navFoco">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#riscos">Por que Proteger</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Quem Somos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#planos">Planos & Preços</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ronda">Ronda Tática</a></li>
                    <li class="nav-item"><a class="nav-link" href="#app">Aplicativo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#depoimentos">Depoimentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">Dúvidas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="https://wa.me/{{ $configs['whatsapp'] }}?text=Ol%C3%A1,%20gostaria%20de%20proteger%20meu%20ve%C3%ADculo%20com%20a%20Foco%207!" target="_blank" class="btn-foco-whatsapp btn-sm px-3 py-2">
                        <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm d-lg-none">
                        <i class="fas fa-lock"></i> Entrar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 3. HERO SECTION COM CARROSSEL DE ALTA CONVERSÃO -->
    <header class="hero-section">
        <div class="container">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                <div class="carousel-inner">
                    @forelse($slides as $index => $slide)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
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
                                <p class="hero-desc">
                                    {{ $slide->subtitulo }}
                                </p>
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
                                <div class="position-relative p-4 rounded-4" style="background: rgba(22, 27, 36, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px);">
                                    <div class="text-start mb-3">
                                        <span class="badge bg-danger px-3 py-2">PROTEÇÃO ATIVA 24H</span>
                                    </div>
                                    <h4 class="fw-bold mb-3">Simulação Rápida em 1 Minuto</h4>
                                    <p class="small text-secondary mb-4">Escolha seu tipo de veículo e receba atendimento imediato do nosso especialista.</p>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <a href="#planos" class="btn btn-outline-light w-100 py-3 text-start">
                                                <i class="fas fa-car text-danger fs-4 d-block mb-1"></i>
                                                <span class="fw-bold d-block">Carro</span>
                                                <span class="small text-secondary">A partir de R$ 69,90</span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="#planos" class="btn btn-outline-light w-100 py-3 text-start">
                                                <i class="fas fa-motorcycle text-danger fs-4 d-block mb-1"></i>
                                                <span class="fw-bold d-block">Moto</span>
                                                <span class="small text-secondary">A partir de R$ 49,90</span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="#planos" class="btn btn-outline-light w-100 py-3 text-start">
                                                <i class="fas fa-truck text-danger fs-4 d-block mb-1"></i>
                                                <span class="fw-bold d-block">Caminhão</span>
                                                <span class="small text-secondary">A partir de R$ 99,90</span>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="#planos" class="btn btn-outline-light w-100 py-3 text-start">
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
                    @empty
                    <div class="carousel-item active">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="hero-title">Proteger hoje para que você viva o amanhã com tranquilidade</h1>
                                <p class="hero-desc">Monitoramento veicular 24h, bloqueio imediato e Ronda Tática presencial.</p>
                                <a href="#planos" class="btn-foco-primary">PROTEGER MEU VEÍCULO AGORA</a>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                @if($slides->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                @endif
            </div>
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
    <section id="sobre" class="py-5" style="background: #080a0f;">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase small">Mais de 10 Anos de Experiência</span>
                    <h2 class="display-6 fw-black mt-2 mb-4">Referência em Rastreamento Veicular e Segurança Eletrônica</h2>
                    <p class="text-secondary fs-5 leading-relaxed">
                        A <strong class="text-white">Foco 7</strong> nasceu com o compromisso inegociável de salvar patrimônios e devolver a paz de espírito a condutores e empresários. Presente com forte infraestrutura no Norte e Nordeste, combinamos inteligência artificial em mapas, módulos de telemetria homologados pela Anatel e equipe tática altamente treinada.
                    </p>
                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger text-white rounded-3 p-3 fs-3"><i class="fas fa-history"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-0">+10 Anos</h4>
                                    <span class="small text-secondary">De Mercado Sólido</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger text-white rounded-3 p-3 fs-3"><i class="fas fa-car-crash"></i></div>
                                <div>
                                    <h4 class="fw-bold mb-0">98.4%</h4>
                                    <span class="small text-secondary">Taxa de Recuperação</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-4 p-md-5 rounded-4 position-relative" style="background: linear-gradient(145deg, #161b24, #0f131a); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h4 class="fw-bold mb-4"><i class="fas fa-shield-virus text-danger me-2"></i> Por Que Somos a Melhor Escolha?</h4>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Central de Operações Blindada 24 Horas</h6>
                                    <p class="small text-secondary mb-0">Operadores de prontidão todos os dias do ano para atender chamados de emergência.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Tecnologia 4G Multioperadora</h6>
                                    <p class="small text-secondary mb-0">Chip inteligente que alterna entre as melhores operadoras do país para sinal constante.</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <i class="fas fa-check-circle text-danger mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Instalação Discreta e Sigilosa</h6>
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
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Investimento Inteligente</span>
                <h2 class="display-6 fw-black mt-2">Escolha o Plano Ideal Para Seu Veículo</h2>
                <p class="text-secondary fs-5">Sem análise de perfil, sem consulta ao SPC/Serasa e sem fidelidade abusiva.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($planos as $plano)
                <div class="col-lg-3 col-md-6">
                    <div class="card-plano {{ $plano->destaque ? 'destaque' : '' }}">
                        @if($plano->destaque && !empty($plano->tag_destaque))
                            <div class="tag-plano-destaque">{{ $plano->tag_destaque }}</div>
                        @endif
                        <div class="text-center mb-3">
                            <i class="fas {{ $plano->icone ?? 'fa-shield-alt' }} text-danger fs-2 mb-2"></i>
                            <h4 class="fw-bold mb-1">{{ $plano->nome }}</h4>
                            <p class="small text-secondary mb-0">{{ $plano->subtitulo }}</p>
                        </div>
                        <div class="text-center my-3">
                            <span class="preco-valor">{{ $plano->preco_formatado }}</span>
                            <span class="preco-periodo">/mês</span>
                            @if($plano->taxa_adesao > 0)
                                <div class="small text-secondary mt-1">Taxa de adesão: {{ $plano->taxa_adesao_formatada }}</div>
                            @else
                                <div class="small text-success fw-bold mt-1">ADESÃO GRÁTIS</div>
                            @endif
                        </div>
                        <ul class="recursos-list">
                            @if(is_array($plano->recursos))
                                @foreach($plano->recursos as $item)
                                    <li><i class="fas fa-check"></i> <span>{{ $item }}</span></li>
                                @endforeach
                            @endif
                        </ul>
                        <button class="btn {{ $plano->destaque ? 'btn-foco-primary' : 'btn-outline-danger' }} w-100 py-3 mt-auto fw-bold btn-contratar"
                                data-plano-id="{{ $plano->id }}"
                                data-plano-nome="{{ $plano->nome }}"
                                data-plano-preco="{{ $plano->preco_formatado }}">
                            <i class="fas fa-lock me-1"></i> CONTRATAR AGORA
                        </button>
                    </div>
                </div>
                @endforeach
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
    <section id="app" class="py-5" style="background: #080a0f;">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-danger fw-bold text-uppercase small">Tecnologia Mobile</span>
                    <h2 class="display-6 fw-black mt-2 mb-4">Controle Total do Seu Veículo na Palma da Mão</h2>
                    <p class="text-secondary fs-5 leading-relaxed mb-4">
                        Disponível para Android e iOS, o app Foco 7 foi projetado para oferecer rapidez instantânea em situações de risco e relatórios completos para o dia a dia.
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #121722; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-white mb-1"><i class="fas fa-lock text-danger me-2"></i> Bloqueio Remoto</h6>
                                <p class="small text-secondary mb-0">Corte de combustível com 1 toque no celular.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #121722; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-white mb-1"><i class="fas fa-draw-polygon text-danger me-2"></i> Cerca Virtual</h6>
                                <p class="small text-secondary mb-0">Receba avisos imediatos se o carro sair do perímetro.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #121722; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-white mb-1"><i class="fas fa-key text-danger me-2"></i> Alerta de Ignição</h6>
                                <p class="small text-secondary mb-0">Notificação push assim que a chave for virada.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #121722; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-white mb-1"><i class="fas fa-route text-danger me-2"></i> Histórico de 90 Dias</h6>
                                <p class="small text-secondary mb-0">Rotas, paradas e velocidade com mapa 3D.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="p-5 rounded-4 text-center position-relative" style="background: radial-gradient(circle, rgba(217, 0, 0, 0.15) 0%, #121722 80%); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <i class="fas fa-mobile-alt text-danger" style="font-size: 8rem;"></i>
                        <h4 class="fw-bold mt-4">Aplicativo Oficial Foco 7</h4>
                        <p class="text-secondary small">Baixe gratuitamente na App Store e Google Play Store</p>
                        <div class="d-flex justify-content-center gap-3 mt-3">
                            <button class="btn btn-outline-light btn-sm"><i class="fab fa-apple me-1"></i> App Store</button>
                            <button class="btn btn-outline-light btn-sm"><i class="fab fa-google-play me-1"></i> Google Play</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. DEPOIMENTOS DE CLIENTES (PROVA SOCIAL) -->
    <section id="depoimentos" class="py-5" style="background: #0b0e14;">
        <div class="container py-5">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Quem Já Dorme Tranquilo</span>
                <h2 class="display-6 fw-black mt-2">Depoimentos Reais de Nossos Clientes</h2>
            </div>
            <div class="row g-4">
                @foreach($depoimentos as $dep)
                <div class="col-md-6 col-lg-3">
                    <div class="p-4 rounded-4 h-100 d-flex flex-direction-column justify-content-between" style="background: #121722; border: 1px solid rgba(255, 255, 255, 0.08);">
                        <div>
                            <div class="text-warning mb-3">
                                @for($i = 0; $i < $dep->estrelas; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <p class="text-secondary small fst-italic">"{{ $dep->texto }}"</p>
                        </div>
                        <div class="mt-3 pt-3 border-top border-dark d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                {{ substr($dep->nome_cliente, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-white">{{ $dep->nome_cliente }}</h6>
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
    <section id="faq" class="py-5" style="background: #080a0f;">
        <div class="container py-5">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-danger fw-bold text-uppercase small">Tire Suas Dúvidas</span>
                <h2 class="display-6 fw-black mt-2">Perguntas Frequentes</h2>
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
                                {{ $faq->resposta }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- 11. FORMULÁRIO DE CONTATO & COTAÇÃO RÁPIDA (AJAX) -->
    <section id="contato" class="py-5" style="background: #11151e;">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <span class="text-danger fw-bold text-uppercase small">Fale Conosco</span>
                    <h2 class="display-6 fw-black mt-2 mb-4">Solicite uma Cotação ou Instalação Imediata</h2>
                    <p class="text-secondary fs-5 mb-4">Preencha o formulário e receba a ligação da nossa equipe técnica em menos de 10 minutos.</p>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle p-3"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <span class="small text-secondary">Telefone Central</span>
                                <h6 class="fw-bold mb-0 text-white">{{ $configs['telefone'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success text-white rounded-circle p-3"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <span class="small text-secondary">WhatsApp Direto</span>
                                <h6 class="fw-bold mb-0 text-white">{{ $configs['whatsapp_formatado'] }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger text-white rounded-circle p-3"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <span class="small text-secondary">Nossa Sede</span>
                                <h6 class="fw-bold mb-0 text-white">{{ $configs['endereco'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="p-4 p-md-5 rounded-4" style="background: #161b24; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h4 class="fw-bold mb-4">Envie Sua Mensagem</h4>
                        <form action="{{ route('site.contato') }}" method="POST" class="ajax-form reset-on-success">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">Seu Nome Completo *</label>
                                    <input type="text" name="nome" class="form-control bg-dark text-white border-secondary" required placeholder="Ex: Roberto Silva">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">Seu WhatsApp *</label>
                                    <input type="text" name="telefone" class="form-control bg-dark text-white border-secondary mask-telefone" required placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">E-mail</label>
                                    <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="seuemail@exemplo.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-secondary">Modelo do Veículo</label>
                                    <input type="text" name="veiculo" class="form-control bg-dark text-white border-secondary" placeholder="Ex: Onix 2022, Titan 160">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-secondary">Dúvida ou Mensagem *</label>
                                    <textarea name="mensagem" rows="4" class="form-control bg-dark text-white border-secondary" required placeholder="Como podemos ajudar você?"></textarea>
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
                    <span class="fs-3 fw-black text-danger">FOCO<span class="text-white">7</span></span>
                    <p class="mt-3 text-secondary small">
                        Líder em rastreamento veicular inteligente, bloqueio remoto e ronda tática presencial. Há mais de 10 anos protegendo famílias e frotas corporativas em todo o território nacional.
                    </p>
                    <div class="d-flex gap-2 mt-4">
                        <span class="badge bg-secondary p-2"><i class="fas fa-lock"></i> SSL Blindado 256-bit</span>
                        <span class="badge bg-secondary p-2"><i class="fas fa-server"></i> Servidores LiteSpeed 24h</span>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h5>Navegação</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#inicio">Início</a></li>
                        <li class="mb-2"><a href="#riscos">Por que Proteger</a></li>
                        <li class="mb-2"><a href="#sobre">Quem Somos</a></li>
                        <li class="mb-2"><a href="#planos">Planos & Preços</a></li>
                        <li class="mb-2"><a href="#ronda">Ronda Tática</a></li>
                        <li class="mb-2"><a href="#faq">Dúvidas</a></li>
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
                    <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 py-2 mb-2">
                        <i class="fas fa-user-lock me-1"></i> Acessar Área do Cliente
                    </a>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 text-center small text-secondary">
                &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- MODAL DE CHECKOUT TRANSPARENTE -->
    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold"><i class="fas fa-shield-alt text-danger me-2"></i> Contratar Plano</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="checkoutStep1">
                        <div class="p-3 rounded-3 mb-3" style="background: #121722;">
                            <span class="small text-secondary">Plano Selecionado:</span>
                            <h5 class="fw-bold mb-0 text-danger" id="checkoutPlanoNome">Plano</h5>
                            <span class="fw-bold text-white" id="checkoutPlanoPreco">R$ 0,00</span>
                        </div>
                        <form id="formCheckout" action="{{ route('checkout.gerar') }}" method="POST" class="ajax-form">
                            @csrf
                            <input type="hidden" name="plano_id" id="checkoutPlanoId">
                            <div class="mb-3">
                                <label class="form-label small text-secondary">Nome Completo *</label>
                                <input type="text" name="nome" class="form-control bg-black text-white border-secondary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-secondary">E-mail *</label>
                                <input type="email" name="email" class="form-control bg-black text-white border-secondary" required>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small text-secondary">WhatsApp *</label>
                                    <input type="text" name="telefone" class="form-control bg-black text-white border-secondary mask-telefone" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-secondary">CPF ou CNPJ *</label>
                                    <input type="text" name="cpf_cnpj" class="form-control bg-black text-white border-secondary mask-cpf-cnpj" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small text-secondary">Forma de Pagamento *</label>
                                <div class="d-flex gap-2">
                                    <div class="form-check flex-fill p-2 border border-secondary rounded">
                                        <input class="form-check-input" type="radio" name="metodo_pagamento" id="metodoPix" value="pix" checked>
                                        <label class="form-check-label small fw-bold" for="metodoPix"><i class="fab fa-pix text-success"></i> PIX Imediato</label>
                                    </div>
                                    <div class="form-check flex-fill p-2 border border-secondary rounded">
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
                        <img id="pixQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border border-light p-2 bg-white mb-3" style="max-width: 200px;">
                        <p class="small text-secondary mb-2">Escaneie o QR Code no app do seu banco ou use a chave Copia e Cola:</p>
                        <div class="input-group mb-3">
                            <input type="text" id="pixCopiaCola" class="form-control bg-black text-white border-secondary small" readonly>
                            <button class="btn btn-danger" type="button" id="btnCopiarPix">Copiar</button>
                        </div>
                        <div class="small text-secondary">Após a confirmação do pagamento, entraremos em contato para agendar sua instalação!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts CDN e Globais -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>

    <script>
        $(function () {
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
