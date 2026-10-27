@extends('admin.layouts.app')

@section('title', 'Gerenciador do CMS')
@section('page_title', 'CMS & Conteúdo da Landing Page')
@section('page_description', 'Gerencie a paleta de cores do tema, carrossel de imagens, banners do hero, seções, FAQs e depoimentos.')

@section('content')
<!-- CARDS KPIS DO CMS & LANDING PAGE -->
<div class="row g-3 mb-4">
    <!-- KPI 1: Banners & Slides -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0 cursor-pointer" onclick="if(window.ativarAbaCms) ativarAbaCms('tab-banners');">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Banners &amp; Hero</h6>
                    <h3 class="fw-bold mb-0 text-body">{{ $slides->count() }} <small class="text-secondary fs-6 fw-normal">slides</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 2: FAQs & Dúvidas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0 cursor-pointer" onclick="if(window.ativarAbaCms) ativarAbaCms('tab-faq');">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">FAQs &amp; Dúvidas</h6>
                    <h3 class="fw-bold mb-0 text-body">{{ $faqs->count() }} <small class="text-secondary fs-6 fw-normal">perguntas</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 3: Depoimentos -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0 cursor-pointer" onclick="if(window.ativarAbaCms) ativarAbaCms('tab-depoimentos');">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Depoimentos</h6>
                    <h3 class="fw-bold mb-0 text-body">{{ $depoimentos->count() }} <small class="text-secondary fs-6 fw-normal">relatos</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI 4: Canais WhatsApp -->
    @php
        $whatsappCanaisAtivos = 0;
        for ($i = 1; $i <= 5; $i++) {
            if (($settings['whatsapp_canal_' . $i . '_ativo'] ?? '1') == '1') {
                $whatsappCanaisAtivos++;
            }
        }
    @endphp
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0 cursor-pointer" onclick="if(window.ativarAbaCms) ativarAbaCms('tab-whatsapp');">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">WhatsApp Flutuante</h6>
                    <h3 class="fw-bold mb-0 text-body">{{ $whatsappCanaisAtivos }} <small class="text-secondary fs-6 fw-normal">canais</small></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="cmsTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="cores-tab" data-bs-toggle="tab" data-bs-target="#tab-cores" type="button">
                    <i class="fas fa-palette text-danger me-1"></i> Cores do Tema
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="banners-tab" data-bs-toggle="tab" data-bs-target="#tab-banners" type="button">
                    <i class="fas fa-images text-danger me-1"></i> Banners & Hero
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="simulacao-tab" data-bs-toggle="tab" data-bs-target="#tab-simulacao" type="button">
                    <i class="fas fa-calculator text-danger me-1"></i> Simulação Hero (Box 1)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="secoes-tab" data-bs-toggle="tab" data-bs-target="#tab-secoes" type="button">
                    <i class="fas fa-layer-group text-danger me-1"></i> Sobre & Riscos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="metricas-tab" data-bs-toggle="tab" data-bs-target="#tab-metricas" type="button">
                    <i class="fas fa-chart-line text-danger me-1"></i> Métricas em Números
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="app-tab" data-bs-toggle="tab" data-bs-target="#tab-app" type="button">
                    <i class="fas fa-mobile-alt text-danger me-1"></i> Aplicativo Mobile
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="ronda-tab" data-bs-toggle="tab" data-bs-target="#tab-ronda" type="button">
                    <i class="fas fa-headset text-danger me-1"></i> Central 24h & Etapas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="faq-tab" data-bs-toggle="tab" data-bs-target="#tab-faq" type="button">
                    <i class="fas fa-question-circle text-danger me-1"></i> FAQs (Dúvidas)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="depoimentos-tab" data-bs-toggle="tab" data-bs-target="#tab-depoimentos" type="button">
                    <i class="fas fa-star text-danger me-1"></i> Depoimentos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="lgpd-tab" data-bs-toggle="tab" data-bs-target="#tab-lgpd" type="button">
                    <i class="fas fa-user-shield text-danger me-1"></i> LGPD & Termos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#tab-whatsapp" type="button">
                    <i class="fab fa-whatsapp text-success me-1"></i> WhatsApp Flutuante
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="cmsTabsContent">

            <!-- ABA 1: CORES DO TEMA DA LANDING PAGE (COM CENTRALIZAÇÃO EXPLICADA) -->
            <div class="tab-pane fade show active" id="tab-cores" role="tabpanel">
                <!-- BOX INFORMATIVO DE CENTRALIZAÇÃO DO SISTEMA -->
                <div class="alert alert-secondary border border-secondary border-opacity-25 shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-sliders-h fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-body">Centralização de Identidade Visual & Dados Oficiais</h6>
                                <p class="small text-secondary mb-0">
                                    O <strong>Logotipo Oficial</strong>, <strong>Ícone Favicon</strong>, <strong>Telefones</strong>, <strong>WhatsApp</strong>, <strong>E-mails</strong>, <strong>Horários</strong>, <strong>Endereço</strong> e <strong>Redes Sociais</strong> são gerenciados centralizadamente no módulo de Configurações, eliminando duplicidades cadastrais.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.configuracoes.index') }}" class="btn btn-outline-danger btn-sm fw-bold">
                            <i class="fas fa-sliders-h me-1"></i> Ir para Configurações & SMTP
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Primária (Hexadecimal)</label>
                            <div class="input-group">
                                <input type="color" name="cor_primaria" class="form-control form-control-color" value="{{ $settings['cor_primaria'] ?? '#d90000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_primaria'] ?? '#d90000' }}" readonly>
                            </div>
                            <small class="text-secondary">Cor dos destaques primários e botões principais da Landing Page.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Secundária (Fundo Escuro)</label>
                            <div class="input-group">
                                <input type="color" name="cor_secundaria" class="form-control form-control-color" value="{{ $settings['cor_secundaria'] ?? '#111111' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_secundaria'] ?? '#111111' }}" readonly>
                            </div>
                            <small class="text-secondary">Base dos blocos escuros e cabeçalhos táticos.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor de Destaque / Hover</label>
                            <div class="input-group">
                                <input type="color" name="cor_destaque" class="form-control form-control-color" value="{{ $settings['cor_destaque'] ?? '#e60000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_destaque'] ?? '#e60000' }}" readonly>
                            </div>
                            <small class="text-secondary">Usada em estados hover e badges luminosos.</small>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CORES DO TEMA
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 3: BANNERS & SLIDES -->
            <div class="tab-pane fade" id="tab-banners" role="tabpanel">
                <!-- SEÇÃO 1: PLANO DE FUNDO & CONTROLE DE OPACIDADE DO HERO -->
                <div class="card border border-light-subtle shadow-sm mb-4 rounded-3 overflow-hidden">
                    <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-body">
                                <i class="fas fa-sliders-h text-danger me-2"></i> Plano de Fundo & Controle de Opacidade do Hero Carousel
                            </h5>
                            <small class="text-secondary">Defina a imagem de fundo panorâmica e controle com precisão a opacidade da camada de sobreposição no site.</small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 fw-semibold">
                            <i class="fas fa-sliders text-danger me-1"></i> Controle Interativo
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formHeroBg">
                            @csrf
                            <div class="row g-4 align-items-center">
                                <div class="col-lg-6">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-image text-danger me-1"></i> Imagem de Fundo Panorâmica (Hero Banner)
                                    </label>
                                    <div class="foco-dropzone"
                                         data-input-name="hero_bg_upload"
                                         data-ratio="1920 × 800 px (Panorâmica Alta Resolução)"
                                         data-max-size="5MB"
                                         data-accept="image/jpeg,image/png,image/webp"
                                         data-current-preview="{{ !empty($settings['hero_bg_imagem']) ? asset($settings['hero_bg_imagem']) : '' }}">
                                    </div>
                                    @if(!empty($settings['hero_bg_imagem']))
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remover_hero_bg" value="1" id="removerHeroBg">
                                        <label class="form-check-label small text-danger fw-semibold" for="removerHeroBg">
                                            <i class="fas fa-trash-alt me-1"></i> Remover imagem de fundo e voltar ao gradiente padrão
                                        </label>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="col-lg-6">
                                    <!-- Controle Deslizante de Opacidade -->
                                    <div class="p-3 rounded-3 bg-body-secondary border border-secondary border-opacity-25 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="hero_bg_opacidade" class="form-label fw-bold mb-0">
                                                <i class="fas fa-adjust text-danger me-1"></i> Opacidade da Camada de Sobreposição:
                                            </label>
                                            <span id="badgeOpacidade" class="badge bg-danger fs-6 px-3 py-1">
                                                {{ $settings['hero_bg_opacidade'] ?? 70 }}%
                                            </span>
                                        </div>
                                        <input type="range" class="form-range" min="0" max="100" step="1"
                                               name="hero_bg_opacidade" id="hero_bg_opacidade"
                                               value="{{ $settings['hero_bg_opacidade'] ?? 70 }}"
                                               oninput="atualizarPreviewOpacidade(this.value)">
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>0% (Mais transparente / Foto nítida)</span>
                                            <span class="fw-bold text-danger">Recomendado: 60%–75% (Equilíbrio foto + texto)</span>
                                            <span>100% (Totalmente opaco)</span>
                                        </div>
                                    </div>

                                    <!-- Cor da Sobreposição & Efeito Desfoque -->
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">Cor da Película / Sobreposição (Overlay)</label>
                                            <div class="input-group mb-2">
                                                <input type="color" name="hero_overlay_cor" id="hero_overlay_cor"
                                                       class="form-control form-control-color"
                                                       value="{{ (!empty($settings['hero_overlay_cor']) && strtolower($settings['hero_overlay_cor']) !== '#ffffff') ? $settings['hero_overlay_cor'] : '#0b0f19' }}"
                                                       onchange="atualizarCorOverlay(this.value)">
                                                <input type="text" class="form-control form-control-sm"
                                                       id="hero_overlay_cor_text"
                                                       value="{{ (!empty($settings['hero_overlay_cor']) && strtolower($settings['hero_overlay_cor']) !== '#ffffff') ? $settings['hero_overlay_cor'] : '#0b0f19' }}" readonly>
                                            </div>
                                            <!-- Presets Rápidos de Película -->
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <span class="small text-muted me-1">Presets:</span>
                                                <button type="button" class="btn btn-xs btn-outline-dark py-0 px-2 rounded-pill small" onclick="selecionarPresetCor('#0b0f19')">Grafite Escuro</button>
                                                <button type="button" class="btn btn-xs btn-outline-dark py-0 px-2 rounded-pill small" onclick="selecionarPresetCor('#000000')">Preto Puro</button>
                                                <button type="button" class="btn btn-xs btn-outline-dark py-0 px-2 rounded-pill small" onclick="selecionarPresetCor('#0f172a')">Azul Noturno</button>
                                                <button type="button" class="btn btn-xs btn-outline-dark py-0 px-2 rounded-pill small" onclick="selecionarPresetCor('#18181b')">Zinco Premium</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">
                                                Desfoque do Fundo (Blur): <span id="badgeBlur" class="badge bg-secondary">{{ $settings['hero_bg_blur'] ?? 0 }}px</span>
                                            </label>
                                            <input type="range" class="form-range" min="0" max="15" step="1"
                                                   name="hero_bg_blur" id="hero_bg_blur"
                                                   value="{{ $settings['hero_bg_blur'] ?? 0 }}"
                                                   oninput="atualizarBlur(this.value)">
                                            <span class="small text-muted d-block mt-1">Dica: 0px mantém a foto do veículo 100% nítida.</span>
                                        </div>
                                    </div>

                                    <!-- Mini Preview Interativo -->
                                    <div class="mt-3">
                                        <label class="form-label fw-bold small text-muted">Pré-visualização Instantânea do Contraste:</label>
                                        <div id="heroPreviewBox" class="p-3 rounded-3 position-relative overflow-hidden border shadow-sm"
                                             style="height: 120px; background-color: #0b0f19; background-image: url('{{ !empty($settings['hero_bg_imagem']) ? asset($settings['hero_bg_imagem']) : asset('/img/og-foco7.jpg') }}'); background-size: cover; background-position: center;">
                                            <div id="heroPreviewOverlay" class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="background: linear-gradient(90deg, {{ (!empty($settings['hero_overlay_cor']) && strtolower($settings['hero_overlay_cor']) !== '#ffffff') ? $settings['hero_overlay_cor'] : '#0b0f19' }}f5 0%, {{ (!empty($settings['hero_overlay_cor']) && strtolower($settings['hero_overlay_cor']) !== '#ffffff') ? $settings['hero_overlay_cor'] : '#0b0f19' }}c0 50%, {{ (!empty($settings['hero_overlay_cor']) && strtolower($settings['hero_overlay_cor']) !== '#ffffff') ? $settings['hero_overlay_cor'] : '#0b0f19' }}40 100%); opacity: {{ ((float)($settings['hero_bg_opacidade'] ?? 70)) / 100 }}; transition: opacity 0.15s ease;"></div>
                                            <div class="position-relative" style="z-index: 2;">
                                                <h6 class="fw-black mb-1 text-white" id="heroPreviewText">Proteger hoje para que você viva o amanhã</h6>
                                                <p class="small text-light-50 text-white-50 mb-0">Monitoramento veicular 24h com Ronda Tática presencial.</p>
                                                <span class="badge bg-danger mt-1">Simulação de Legibilidade & Contraste</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end pt-2 border-top">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> SALVAR PLANO DE FUNDO & OPACIDADE
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SEÇÃO 2: SLIDES DO HERO CAROUSEL -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Slides Individuais do Hero Carousel</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalBanner()">
                        <i class="fas fa-plus me-1"></i> Adicionar Novo Slide
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ordem</th>
                                <th>Badge / Título</th>
                                <th>Botão 1</th>
                                <th>Botão 2</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slides as $slide)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $slide->ordem }}</span></td>
                                <td>
                                    @if($slide->badge)<span class="badge bg-danger mb-1">{{ $slide->badge }}</span><br>@endif
                                    <strong>{{ $slide->titulo }}</strong>
                                </td>
                                <td><span class="badge bg-dark">{{ $slide->texto_botao_1 ?? '-' }}</span></td>
                                <td><span class="badge bg-dark">{{ $slide->texto_botao_2 ?? '-' }}</span></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarBanner({{ $slide->id }})" title="Editar Slide">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirItem('banner', {{ $slide->id }})" title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA 4: FAQS -->
            <div class="tab-pane fade" id="tab-faq" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Perguntas Frequentes (FAQ)</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalFaq()">
                        <i class="fas fa-plus me-1"></i> Adicionar Pergunta
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pergunta</th>
                                <th>Categoria</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $faq)
                            <tr>
                                <td class="fw-semibold">{{ $faq->pergunta }}</td>
                                <td><span class="badge bg-secondary">{{ $faq->categoria }}</span></td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarFaq({{ $faq->id }})" title="Editar FAQ">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirItem('faq', {{ $faq->id }})" title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA 5: DEPOIMENTOS -->
            <div class="tab-pane fade" id="tab-depoimentos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Depoimentos de Clientes</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalDepoimento()">
                        <i class="fas fa-plus me-1"></i> Adicionar Depoimento
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Cidade / Veículo</th>
                                <th>Estrelas</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depoimentos as $dep)
                            <tr>
                                <td class="fw-bold">{{ $dep->nome_cliente }}</td>
                                <td>{{ $dep->cidade_uf }} - {{ $dep->veiculo }}</td>
                                <td class="text-warning">
                                    @for($i=0; $i<$dep->estrelas; $i++) <i class="fas fa-star"></i> @endfor
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarDepoimento({{ $dep->id }})" title="Editar Depoimento">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirItem('depoimento', {{ $dep->id }})" title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA: SIMULAÇÃO HERO (CARD DO HERO BANNER - ITEM 1) -->
            <div class="tab-pane fade" id="tab-simulacao" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-calculator me-2"></i> Box Hero: Simulação Rápida em 1 Minuto</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Topo da Página / Banner Hero</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Exibir Box de Simulação</label>
                                    <select name="hero_simulacao_ativo" class="form-select">
                                        <option value="1" {{ ($settings['hero_simulacao_ativo'] ?? '1') == '1' ? 'selected' : '' }}>Sim, exibir no topo</option>
                                        <option value="0" {{ ($settings['hero_simulacao_ativo'] ?? '1') == '0' ? 'selected' : '' }}>Ocultar box</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Badge Superior</label>
                                    <input type="text" name="hero_simulacao_badge" class="form-control" value="{{ $settings['hero_simulacao_badge'] ?? 'PROTEÇÃO ATIVA 24H' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título do Box</label>
                                    <input type="text" name="hero_simulacao_titulo" class="form-control" value="{{ $settings['hero_simulacao_titulo'] ?? 'Simulação Rápida em 1 Minuto' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Subtítulo / Descrição</label>
                                    <input type="text" name="hero_simulacao_subtitulo" class="form-control" value="{{ $settings['hero_simulacao_subtitulo'] ?? 'Escolha seu tipo de veículo e receba atendimento imediato do nosso especialista.' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Texto do Botão Principal</label>
                                    <input type="text" name="hero_simulacao_btn_texto" class="form-control" value="{{ $settings['hero_simulacao_btn_texto'] ?? 'VER TODOS OS PLANOS E RECURSOS' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Link do Botão Principal</label>
                                    <input type="text" name="hero_simulacao_btn_link" class="form-control" value="{{ $settings['hero_simulacao_btn_link'] ?? '#planos' }}" placeholder="ex: #planos ou link whatsapp">
                                </div>
                            </div>

                            <!-- 4 CARDS DE VEÍCULOS DA SIMULAÇÃO -->
                            <div class="border-top pt-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-th-large text-danger me-2"></i> 4 Opções de Veículos da Simulação</h6>
                                <div class="row g-3">
                                    <!-- VEÍCULO 1 (CARRO) -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Opção 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nome do Veículo</label>
                                                <input type="text" name="hero_simulacao_v1_nome" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v1_nome'] ?? 'Carro' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto de Preço / Condição</label>
                                                <input type="text" name="hero_simulacao_v1_preco" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v1_preco'] ?? 'A partir de R$ 69,90' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="hero_simulacao_v1_icone" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v1_icone'] ?? 'fa-car' }}" placeholder="ex: fa-car">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Link / Destino</label>
                                                <input type="text" name="hero_simulacao_v1_link" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v1_link'] ?? '#planos' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- VEÍCULO 2 (MOTO) -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Opção 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nome do Veículo</label>
                                                <input type="text" name="hero_simulacao_v2_nome" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v2_nome'] ?? 'Moto' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto de Preço / Condição</label>
                                                <input type="text" name="hero_simulacao_v2_preco" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v2_preco'] ?? 'A partir de R$ 49,90' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="hero_simulacao_v2_icone" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v2_icone'] ?? 'fa-motorcycle' }}" placeholder="ex: fa-motorcycle">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Link / Destino</label>
                                                <input type="text" name="hero_simulacao_v2_link" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v2_link'] ?? '#planos' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- VEÍCULO 3 (CAMINHÃO) -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Opção 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nome do Veículo</label>
                                                <input type="text" name="hero_simulacao_v3_nome" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v3_nome'] ?? 'Caminhão' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto de Preço / Condição</label>
                                                <input type="text" name="hero_simulacao_v3_preco" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v3_preco'] ?? 'A partir de R$ 99,90' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="hero_simulacao_v3_icone" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v3_icone'] ?? 'fa-truck' }}" placeholder="ex: fa-truck">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Link / Destino</label>
                                                <input type="text" name="hero_simulacao_v3_link" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v3_link'] ?? '#planos' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- VEÍCULO 4 (FROTAS) -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Opção 4</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nome do Veículo</label>
                                                <input type="text" name="hero_simulacao_v4_nome" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v4_nome'] ?? 'Frotas' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto de Preço / Condição</label>
                                                <input type="text" name="hero_simulacao_v4_preco" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v4_preco'] ?? 'Condições especiais' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="hero_simulacao_v4_icone" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v4_icone'] ?? 'fa-cubes' }}" placeholder="ex: fa-cubes">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Link / Destino</label>
                                                <input type="text" name="hero_simulacao_v4_link" class="form-control form-control-sm" value="{{ $settings['hero_simulacao_v4_link'] ?? '#planos' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Box de Simulação Hero
                        </button>
                    </div>
                </form>
            </div>

            <!-- ABA: TEXTOS DAS SEÇÕES SOBRE & RISCOS (ITEM 2 E ITEM 3) -->
            <div class="tab-pane fade" id="tab-secoes" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <!-- 1. SEÇÃO SOBRE NÓS / REFERÊNCIA EM RASTREAMENTO (ITEM 3) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-building-circle-check me-2"></i> Box Sobre Nós & Autoridade (Item 3)</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Quem Somos & Diferenciais</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Badge / Tag Superior</label>
                                    <input type="text" name="secao_sobre_badge" class="form-control" value="{{ $settings['secao_sobre_badge'] ?? 'Mais de 10 Anos de Experiência' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Título Principal da Seção</label>
                                    <input type="text" name="secao_sobre_titulo" class="form-control" value="{{ $settings['secao_sobre_titulo'] ?? 'Referência em Rastreamento Veicular e Segurança Eletrônica' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Texto Narrativo / História da Empresa</label>
                                    <textarea name="secao_sobre_texto" class="form-control" rows="4">{{ $settings['secao_sobre_texto'] ?? 'A Foco 7 nasceu com o compromisso inegociável de salvar patrimônios e devolver a paz de espírito a condutores e empresários. Presente com forte infraestrutura no Norte e Nordeste, combinamos inteligência artificial em mapas, módulos de telemetria homologados pela Anatel e equipe tática altamente treinada.' }}</textarea>
                                </div>
                            </div>

                            <!-- BADGES ESTATÍSTICOS DO SOBRE NÓS -->
                            <div class="border-top pt-3 mb-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-award text-danger me-2"></i> 2 Badges Estatísticos Inferiores</h6>
                                <div class="row g-3">
                                    <!-- ESTATÍSTICA 1 -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-body-secondary rounded-3 border">
                                            <span class="badge bg-danger mb-2">Estatística 1</span>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Ícone</label>
                                                    <input type="text" name="secao_sobre_stat1_icone" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat1_icone'] ?? 'fa-history' }}" placeholder="ex: fa-history">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Valor</label>
                                                    <input type="text" name="secao_sobre_stat1_valor" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat1_valor'] ?? '+10 Anos' }}">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Rótulo / Texto</label>
                                                    <input type="text" name="secao_sobre_stat1_label" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat1_label'] ?? 'De Mercado Sólido' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ESTATÍSTICA 2 -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-body-secondary rounded-3 border">
                                            <span class="badge bg-danger mb-2">Estatística 2</span>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Ícone</label>
                                                    <input type="text" name="secao_sobre_stat2_icone" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat2_icone'] ?? 'fa-car-crash' }}" placeholder="ex: fa-car-crash">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Valor</label>
                                                    <input type="text" name="secao_sobre_stat2_valor" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat2_valor'] ?? '98.4%' }}">
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Rótulo / Texto</label>
                                                    <input type="text" name="secao_sobre_stat2_label" class="form-control form-control-sm" value="{{ $settings['secao_sobre_stat2_label'] ?? 'Taxa de Recuperação' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD DIREITO: POR QUE SOMOS A MELHOR ESCOLHA? -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-shield-virus text-danger me-2"></i> Card Direito: "Por Que Somos a Melhor Escolha?"</h6>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Título do Card</label>
                                        <input type="text" name="secao_sobre_dif_titulo" class="form-control" value="{{ $settings['secao_sobre_dif_titulo'] ?? 'Por Que Somos a Melhor Escolha?' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Ícone do Título</label>
                                        <input type="text" name="secao_sobre_dif_icone" class="form-control" value="{{ $settings['secao_sobre_dif_icone'] ?? 'fa-shield-virus' }}" placeholder="ex: fa-shield-virus">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <!-- DIFERENCIAL 1 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Item 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_sobre_dif1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_sobre_dif1_titulo'] ?? 'Central de Operações Blindada 24 Horas' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <textarea name="secao_sobre_dif1_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_sobre_dif1_desc'] ?? 'Operadores de prontidão todos os dias do ano para atender chamados de emergência.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DIFERENCIAL 2 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Item 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_sobre_dif2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_sobre_dif2_titulo'] ?? 'Tecnologia 4G Multioperadora' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <textarea name="secao_sobre_dif2_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_sobre_dif2_desc'] ?? 'Chip inteligente que alterna entre as melhores operadoras do país para sinal constante.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DIFERENCIAL 3 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Item 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_sobre_dif3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_sobre_dif3_titulo'] ?? 'Instalação Discreta e Sigilosa' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <textarea name="secao_sobre_dif3_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_sobre_dif3_desc'] ?? 'Técnicos certificados que instalam sem cortar chicote original e sem comprometer garantias.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SEÇÃO RISCOS PREVENTIVOS / O RISCO NÃO AVISA ANTES DE CHEGAR (ITEM 2) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-shield-alt me-2"></i> Box Segurança Preventiva: Riscos vs Soluções (Item 2)</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Benefícios & Prevenção</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Badge / Tag Superior</label>
                                    <input type="text" name="secao_riscos_badge" class="form-control" value="{{ $settings['secao_riscos_badge'] ?? 'Segurança Preventiva' }}">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label small fw-bold">Título da Seção</label>
                                    <input type="text" name="secao_riscos_titulo" class="form-control" value="{{ $settings['secao_riscos_titulo'] ?? 'O risco não avisa antes de chegar' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Subtítulo / Descrição</label>
                                    <input type="text" name="secao_riscos_subtitulo" class="form-control" value="{{ $settings['secao_riscos_subtitulo'] ?? 'A criminalidade evolui todos os dias. Deixar seu patrimônio desprotegido custa muito mais caro que investir em prevenção.' }}">
                                </div>
                            </div>

                            <!-- 3 CARDS DE RISCOS COM SOLUÇÕES -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-th-large text-danger me-2"></i> 3 Cards de Riscos e Solução Foco 7</h6>
                                <div class="row g-3">
                                    <!-- CARD 1 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Card 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone do Risco</label>
                                                <input type="text" name="secao_riscos_c1_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c1_icone'] ?? 'fa-mask' }}" placeholder="ex: fa-mask">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título do Risco</label>
                                                <input type="text" name="secao_riscos_c1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c1_titulo'] ?? 'Roubos e Furtos Constantes' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Descrição do Risco</label>
                                                <textarea name="secao_riscos_c1_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_riscos_c1_desc'] ?? 'Mais de 1.000 veículos são roubados por dia no Brasil. Sem rastreador, a taxa de recuperação cai para menos de 10%.' }}</textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto da Solução Foco 7</label>
                                                <input type="text" name="secao_riscos_c1_solucao" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c1_solucao'] ?? 'Solução Foco 7: Localização em tempo real com precisão de metros.' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Ícone da Solução</label>
                                                <input type="text" name="secao_riscos_c1_solucao_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c1_solucao_icone'] ?? 'fa-shield-alt' }}" placeholder="ex: fa-shield-alt">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- CARD 2 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Card 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone do Risco</label>
                                                <input type="text" name="secao_riscos_c2_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c2_icone'] ?? 'fa-hourglass-half' }}" placeholder="ex: fa-hourglass-half">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título do Risco</label>
                                                <input type="text" name="secao_riscos_c2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c2_titulo'] ?? 'Resposta Policial Demorada' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Descrição do Risco</label>
                                                <textarea name="secao_riscos_c2_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_riscos_c2_desc'] ?? 'Os primeiros 30 minutos após o sinistro são decisivos. Esperar sem suporte especializado reduz drasticamente as chances de resgate.' }}</textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto da Solução Foco 7</label>
                                                <input type="text" name="secao_riscos_c2_solucao" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c2_solucao'] ?? 'Solução Foco 7: Ronda Tática com viaturas de pronta resposta.' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Ícone da Solução</label>
                                                <input type="text" name="secao_riscos_c2_solucao_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c2_solucao_icone'] ?? 'fa-motorcycle' }}" placeholder="ex: fa-motorcycle">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- CARD 3 -->
                                    <div class="col-md-4">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Card 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone do Risco</label>
                                                <input type="text" name="secao_riscos_c3_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c3_icone'] ?? 'fa-coins' }}" placeholder="ex: fa-coins">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título do Risco</label>
                                                <input type="text" name="secao_riscos_c3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c3_titulo'] ?? 'Prejuízo Financeiro Irrecuperável' }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Descrição do Risco</label>
                                                <textarea name="secao_riscos_c3_desc" class="form-control form-control-sm" rows="3">{{ $settings['secao_riscos_c3_desc'] ?? 'O valor do veículo, franquias abusivas de seguro tradicional e o transtorno da perda deixam famílias e empresas desamparadas.' }}</textarea>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Texto da Solução Foco 7</label>
                                                <input type="text" name="secao_riscos_c3_solucao" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c3_solucao'] ?? 'Solução Foco 7: Planos acessíveis a partir de R$ 49,90/mês.' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Ícone da Solução</label>
                                                <input type="text" name="secao_riscos_c3_solucao_icone" class="form-control form-control-sm" value="{{ $settings['secao_riscos_c3_solucao_icone'] ?? 'fa-check-double' }}" placeholder="ex: fa-check-double">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. SEÇÃO ESTÉTICA AUTOMOTIVA & DIFERENCIAIS -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-car-side me-2"></i> Seção Estética Automotiva no Site</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Landing Page & Diferenciais</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título da Seção de Estética</label>
                                    <input type="text" name="secao_estetica_titulo" class="form-control" value="{{ $settings['secao_estetica_titulo'] ?? 'Estética Automotiva de Alta Performance' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subtítulo da Seção</label>
                                    <input type="text" name="secao_estetica_subtitulo" class="form-control" value="{{ $settings['secao_estetica_subtitulo'] ?? 'Além de proteger com tecnologia, cuidamos do visual, valorização e conservação do seu veículo com padrão premium.' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Texto de Chamada de Ação / Descrição</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Editor Summernote</span>
                                    </label>
                                    <textarea name="secao_estetica_chamada" id="secao_estetica_chamada" class="form-control summernote" data-height="130">{{ $settings['secao_estetica_chamada'] ?? 'Serviços especializados com produtos nobres, vitrificação técnica, polimento com brilho espelhado e higienização profunda.' }}</textarea>
                                </div>
                            </div>

                            <!-- 4 CARDS DE DIFERENCIAIS / GARANTIAS DA ESTÉTICA AUTOMOTIVA -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-certificate text-danger me-2"></i> 4 Diferenciais e Selos de Garantia (Rodapé da Seção Estética)</h6>
                                <div class="row g-3">
                                    <!-- DIFERENCIAL 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Diferencial 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_estetica_dif1_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_icone'] ?? 'fa-award' }}" placeholder="ex: fa-award">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_estetica_dif1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_titulo'] ?? 'Produtos Nobres' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição / Marcas</label>
                                                <input type="text" name="secao_estetica_dif1_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_desc'] ?? 'Vonixx, CarPro e Menzerna' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DIFERENCIAL 2 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Diferencial 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_estetica_dif2_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_icone'] ?? 'fa-warehouse' }}" placeholder="ex: fa-warehouse">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_estetica_dif2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_titulo'] ?? 'Box Climatizado' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_estetica_dif2_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_desc'] ?? 'Sem poeira ou sol direto' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DIFERENCIAL 3 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Diferencial 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_estetica_dif3_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_icone'] ?? 'fa-certificate' }}" placeholder="ex: fa-certificate">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_estetica_dif3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_titulo'] ?? 'Certificado de Garantia' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_estetica_dif3_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_desc'] ?? 'Proteção com laudo técnico' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DIFERENCIAL 4 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Diferencial 4</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_estetica_dif4_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_icone'] ?? 'fa-user-clock' }}" placeholder="ex: fa-user-clock">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_estetica_dif4_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_titulo'] ?? 'Horário Marcado' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_estetica_dif4_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_desc'] ?? 'Entrega pontual sem filas' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Sobre Nós, Riscos & Diferenciais
                        </button>
                    </div>
                </form>
            </div>

            <!-- ABA: MÉTRICAS & ESTATÍSTICAS DE IMPACTO EM NÚMEROS (ITEM 7) -->
            <div class="tab-pane fade" id="tab-metricas" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-chart-line me-2"></i> Box de Métricas & Impacto em Números (Item 7)</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Autoridade & Resultados</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Exibir Box de Métricas</label>
                                    <select name="secao_metricas_ativo" class="form-select">
                                        <option value="1" {{ ($settings['secao_metricas_ativo'] ?? '1') == '1' ? 'selected' : '' }}>Sim, exibir na página</option>
                                        <option value="0" {{ ($settings['secao_metricas_ativo'] ?? '1') == '0' ? 'selected' : '' }}>Ocultar box</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Badge Superior</label>
                                    <input type="text" name="secao_metricas_badge" class="form-control" value="{{ $settings['secao_metricas_badge'] ?? 'Autoridade Comprovada' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título da Seção</label>
                                    <input type="text" name="secao_metricas_titulo" class="form-control" value="{{ $settings['secao_metricas_titulo'] ?? 'A Força da Foco 7 em Números' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Subtítulo / Descrição</label>
                                    <input type="text" name="secao_metricas_subtitulo" class="form-control" value="{{ $settings['secao_metricas_subtitulo'] ?? 'Resultados expressivos e infraestrutura sólida para proteger quem você ama e o que você conquistou.' }}">
                                </div>
                            </div>

                            <!-- OS 5 CONTADORES ESTATÍSTICOS -->
                            <div class="border-top pt-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-calculator text-danger me-2"></i> 5 Contadores Estatísticos (Veículos Rastreados, Recuperados, Parceiros, Franqueados, Estados)</h6>
                                <div class="row g-3">
                                    <!-- MÉTRICA 1: VEÍCULOS RASTREADOS -->
                                    <div class="col-md-6 col-lg">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Contador 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone</label>
                                                <input type="text" name="metrica_1_icone" class="form-control form-control-sm" value="{{ $settings['metrica_1_icone'] ?? 'fa-satellite-dish' }}" placeholder="ex: fa-satellite-dish">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Número / Valor</label>
                                                <input type="text" name="metrica_1_valor" class="form-control form-control-sm" value="{{ $settings['metrica_1_valor'] ?? '+25.000' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Rótulo / Legenda</label>
                                                <input type="text" name="metrica_1_label" class="form-control form-control-sm" value="{{ $settings['metrica_1_label'] ?? 'Veículos Rastreados' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- MÉTRICA 2: VEÍCULOS RECUPERADOS -->
                                    <div class="col-md-6 col-lg">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Contador 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone</label>
                                                <input type="text" name="metrica_2_icone" class="form-control form-control-sm" value="{{ $settings['metrica_2_icone'] ?? 'fa-shield-halved' }}" placeholder="ex: fa-shield-halved">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Número / Valor</label>
                                                <input type="text" name="metrica_2_valor" class="form-control form-control-sm" value="{{ $settings['metrica_2_valor'] ?? '+3.800' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Rótulo / Legenda</label>
                                                <input type="text" name="metrica_2_label" class="form-control form-control-sm" value="{{ $settings['metrica_2_label'] ?? 'Veículos Recuperados' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- MÉTRICA 3: PARCEIROS HOMOLOGADOS -->
                                    <div class="col-md-6 col-lg">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Contador 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone</label>
                                                <input type="text" name="metrica_3_icone" class="form-control form-control-sm" value="{{ $settings['metrica_3_icone'] ?? 'fa-handshake' }}" placeholder="ex: fa-handshake">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Número / Valor</label>
                                                <input type="text" name="metrica_3_valor" class="form-control form-control-sm" value="{{ $settings['metrica_3_valor'] ?? '+150' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Rótulo / Legenda</label>
                                                <input type="text" name="metrica_3_label" class="form-control form-control-sm" value="{{ $settings['metrica_3_label'] ?? 'Parceiros Homologados' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- MÉTRICA 4: FRANQUEADOS E POLOS -->
                                    <div class="col-md-6 col-lg">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Contador 4</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone</label>
                                                <input type="text" name="metrica_4_icone" class="form-control form-control-sm" value="{{ $settings['metrica_4_icone'] ?? 'fa-building-shield' }}" placeholder="ex: fa-building-shield">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Número / Valor</label>
                                                <input type="text" name="metrica_4_valor" class="form-control form-control-sm" value="{{ $settings['metrica_4_valor'] ?? '+45' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Rótulo / Legenda</label>
                                                <input type="text" name="metrica_4_label" class="form-control form-control-sm" value="{{ $settings['metrica_4_label'] ?? 'Franqueados e Polos' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- MÉTRICA 5: ESTADOS PRESENTES -->
                                    <div class="col-md-6 col-lg">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Contador 5</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone</label>
                                                <input type="text" name="metrica_5_icone" class="form-control form-control-sm" value="{{ $settings['metrica_5_icone'] ?? 'fa-map-location-dot' }}" placeholder="ex: fa-map-location-dot">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Número / Valor</label>
                                                <input type="text" name="metrica_5_valor" class="form-control form-control-sm" value="{{ $settings['metrica_5_valor'] ?? '12 Estados' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Rótulo / Legenda</label>
                                                <input type="text" name="metrica_5_label" class="form-control form-control-sm" value="{{ $settings['metrica_5_label'] ?? 'Estados Atendidos' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Métricas & Estatísticas de Impacto
                        </button>
                    </div>
                </form>
            </div>

            <!-- ABA 4: APLICATIVO MOBILE (TECNOLOGIA MOBILE & DOWNLOAD) -->
            <div class="tab-pane fade" id="tab-app" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf
                    
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-mobile-alt me-2"></i> Seção do Aplicativo Mobile Foco 7</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tecnologia & Download</span>
                        </div>
                        <div class="card-body p-4">
                            <!-- CABEÇALHO DO APP -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Badge / Tag Superior</label>
                                    <input type="text" name="secao_app_badge" class="form-control" value="{{ $settings['secao_app_badge'] ?? 'Tecnologia Mobile' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Título Principal da Seção</label>
                                    <input type="text" name="secao_app_titulo" class="form-control" value="{{ $settings['secao_app_titulo'] ?? 'Controle Total do Seu Veículo na Palma da Mão' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Texto Narrativo / Descrição Explicativa</label>
                                    <textarea name="secao_app_descricao" rows="2" class="form-control">{{ $settings['secao_app_descricao'] ?? 'Disponível para Android e iOS, o app Foco 7 foi projetado para oferecer rapidez instantânea em situações de risco e relatórios completos para o dia a dia.' }}</textarea>
                                </div>
                            </div>

                            <!-- 4 CARDS DE RECURSOS DO APP -->
                            <div class="border-top pt-3 mb-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-th-large text-danger me-2"></i> 4 Cards de Recursos do Aplicativo (Lado Esquerdo)</h6>
                                <div class="row g-3">
                                    <!-- RECURSO 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Recurso 1</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_app_rec1_icone" class="form-control form-control-sm" value="{{ $settings['secao_app_rec1_icone'] ?? 'fa-lock' }}" placeholder="ex: fa-lock">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_app_rec1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_app_rec1_titulo'] ?? 'Bloqueio Remoto' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_app_rec1_desc" class="form-control form-control-sm" value="{{ $settings['secao_app_rec1_desc'] ?? 'Corte de combustível com 1 toque no celular.' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- RECURSO 2 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Recurso 2</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_app_rec2_icone" class="form-control form-control-sm" value="{{ $settings['secao_app_rec2_icone'] ?? 'fa-draw-polygon' }}" placeholder="ex: fa-draw-polygon">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_app_rec2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_app_rec2_titulo'] ?? 'Cerca Virtual' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_app_rec2_desc" class="form-control form-control-sm" value="{{ $settings['secao_app_rec2_desc'] ?? 'Receba avisos imediatos se o carro sair do perímetro.' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- RECURSO 3 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Recurso 3</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_app_rec3_icone" class="form-control form-control-sm" value="{{ $settings['secao_app_rec3_icone'] ?? 'fa-key' }}" placeholder="ex: fa-key">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_app_rec3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_app_rec3_titulo'] ?? 'Alerta de Ignição' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_app_rec3_desc" class="form-control form-control-sm" value="{{ $settings['secao_app_rec3_desc'] ?? 'Notificação push assim que a chave for virada.' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- RECURSO 4 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <span class="badge bg-danger mb-2">Recurso 4</span>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                <input type="text" name="secao_app_rec4_icone" class="form-control form-control-sm" value="{{ $settings['secao_app_rec4_icone'] ?? 'fa-route' }}" placeholder="ex: fa-route">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título</label>
                                                <input type="text" name="secao_app_rec4_titulo" class="form-control form-control-sm" value="{{ $settings['secao_app_rec4_titulo'] ?? 'Histórico de 90 Dias' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição</label>
                                                <input type="text" name="secao_app_rec4_desc" class="form-control form-control-sm" value="{{ $settings['secao_app_rec4_desc'] ?? 'Rotas, paradas e velocidade com mapa 3D.' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD DE DOWNLOAD & IMAGEM OFICIAL DO APLICATIVO -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-download text-danger me-2"></i> Card Lateral de Download & Imagem Oficial do Aplicativo (Lado Direito)</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Título do Card de Download</label>
                                        <input type="text" name="secao_app_card_titulo" class="form-control" value="{{ $settings['secao_app_card_titulo'] ?? 'Aplicativo Oficial Foco 7' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Subtítulo do Card</label>
                                        <input type="text" name="secao_app_card_subtitulo" class="form-control" value="{{ $settings['secao_app_card_subtitulo'] ?? 'Baixe gratuitamente na App Store e Google Play Store' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold"><i class="fab fa-apple me-1 text-body"></i> Link Apple App Store</label>
                                        <input type="text" name="secao_app_link_appstore" class="form-control" value="{{ $settings['secao_app_link_appstore'] ?? 'https://www.apple.com/app-store/' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold"><i class="fab fa-google-play me-1 text-success"></i> Link Google Play Store</label>
                                        <input type="text" name="secao_app_link_playstore" class="form-control" value="{{ $settings['secao_app_link_playstore'] ?? 'https://play.google.com/store/apps' }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label small fw-bold"><i class="fas fa-image text-danger me-1"></i> Imagem Oficial / Mockup do Celular</label>
                                        <p class="text-secondary small mb-2">Envie a imagem oficial do aplicativo em formato vertical (screenshot do app ou mockup do smartphone). Caso não envie, o sistema utiliza o mockup padrão de alta resolução.</p>
                                        <div class="foco-dropzone"
                                             data-input-name="app_mockup_upload"
                                             data-ratio="Vertical Celular (ex: 500 × 800 px ou 9:16)"
                                             data-max-size="5MB"
                                             data-accept="image/png,image/webp,image/jpeg"
                                             data-current-preview="{{ !empty($settings['secao_app_mockup_imagem']) ? asset($settings['secao_app_mockup_imagem']) : asset('/img/app-mockup-foco7.jpg') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-body-tertiary p-3 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DO APLICATIVO
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 5: RONDA TÁTICA (4 ETAPAS DE RESPOSTA) -->
            <div class="tab-pane fade" id="tab-ronda" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-shield-alt me-2"></i> Seção Ronda Tática & Fluxograma de Resposta Presencial</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">4 Etapas de Resposta</span>
                        </div>
                        <div class="card-body p-4">
                            <!-- CABEÇALHO DA SEÇÃO -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Badge / Tag Superior</label>
                                    <input type="text" name="secao_ronda_badge" class="form-control" value="{{ $settings['secao_ronda_badge'] ?? 'Apoio Presencial Especializado' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Título Principal da Seção</label>
                                    <input type="text" name="secao_ronda_titulo" class="form-control" value="{{ $settings['secao_ronda_titulo'] ?? 'Como Funciona a Ronda Tática Foco 7' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Subtítulo / Descrição da Seção</label>
                                    <input type="text" name="secao_ronda_subtitulo" class="form-control" value="{{ $settings['secao_ronda_subtitulo'] ?? 'Tecnologia aliada à ação humana imediata para localizar e recuperar seu patrimônio.' }}">
                                </div>
                            </div>

                            <!-- AS 4 ETAPAS DA RONDA TÁTICA -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-list-ol text-danger me-2"></i> As 4 Etapas do Fluxo da Ronda Tática</h6>
                                <div class="row g-3">
                                    <!-- ETAPA 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-danger rounded-circle badge-circle fw-bold">1</span>
                                                <span class="fw-bold text-body">Etapa 1</span>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título da Etapa</label>
                                                <input type="text" name="secao_ronda_etapa1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_ronda_etapa1_titulo'] ?? 'Alarme ou Sinistro' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição da Etapa</label>
                                                <textarea name="secao_ronda_etapa1_desc" rows="3" class="form-control form-control-sm">{{ $settings['secao_ronda_etapa1_desc'] ?? 'Você aciona o botão de emergência pelo app ou a central detecta movimentação ou bloqueio não autorizado.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ETAPA 2 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-danger rounded-circle badge-circle fw-bold">2</span>
                                                <span class="fw-bold text-body">Etapa 2</span>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título da Etapa</label>
                                                <input type="text" name="secao_ronda_etapa2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_ronda_etapa2_titulo'] ?? 'Central 24h Aciona' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição da Etapa</label>
                                                <textarea name="secao_ronda_etapa2_desc" rows="3" class="form-control form-control-sm">{{ $settings['secao_ronda_etapa2_desc'] ?? 'Nossos operadores isolam o perímetro, realizam o bloqueio progressivo e geram o mapa da rota do veículo.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ETAPA 3 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-danger rounded-circle badge-circle fw-bold">3</span>
                                                <span class="fw-bold text-body">Etapa 3</span>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título da Etapa</label>
                                                <input type="text" name="secao_ronda_etapa3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_ronda_etapa3_titulo'] ?? 'Viatura a Caminho' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição da Etapa</label>
                                                <textarea name="secao_ronda_etapa3_desc" rows="3" class="form-control form-control-sm">{{ $settings['secao_ronda_etapa3_desc'] ?? 'A viatura ou moto tática mais próxima é despachada imediatamente com as coordenadas exatas do alvo.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ETAPA 4 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-danger rounded-circle badge-circle fw-bold">4</span>
                                                <span class="fw-bold text-body">Etapa 4</span>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Título da Etapa</label>
                                                <input type="text" name="secao_ronda_etapa4_titulo" class="form-control form-control-sm" value="{{ $settings['secao_ronda_etapa4_titulo'] ?? 'Recuperação & Apoio' }}">
                                            </div>
                                            <div>
                                                <label class="form-label small fw-bold">Descrição da Etapa</label>
                                                <textarea name="secao_ronda_etapa4_desc" rows="3" class="form-control form-control-sm">{{ $settings['secao_ronda_etapa4_desc'] ?? 'A equipe chega ao local, aciona as autoridades competentes e preserva seu veículo até sua devolução.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-body-tertiary p-3 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR RONDA TÁTICA & ETAPAS
                            </button>
                        </div>
                    </div>

                    <!-- 5. SEÇÃO PARCEIROS & HOMOLOGAÇÕES (CARROSSEL INFINITO) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-handshake me-2"></i> Seção Parceiros & Tecnologias Homologadas (Carrossel Infinito)</h6>
                            <a href="{{ route('admin.parceiros.index') }}" class="btn btn-sm btn-outline-danger fw-bold">
                                <i class="fas fa-external-link-alt me-1"></i> Gerenciar Logotipos dos Parceiros
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Badge Superior</label>
                                    <input type="text" name="secao_parceiros_badge" class="form-control" value="{{ $settings['secao_parceiros_badge'] ?? 'Ecossistema & Homologações' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Título Principal da Seção</label>
                                    <input type="text" name="secao_parceiros_titulo" class="form-control" value="{{ $settings['secao_parceiros_titulo'] ?? 'Empresas Parceiras & Tecnologias Homologadas' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Subtítulo Explicativo</label>
                                    <input type="text" name="secao_parceiros_subtitulo" class="form-control" value="{{ $settings['secao_parceiros_subtitulo'] ?? 'Conectados às principais seguradoras, fabricantes de rastreadores e operadoras de telecomunicação do Brasil.' }}">
                                </div>
                            </div>
                            <div class="alert alert-light border mt-3 mb-0 d-flex align-items-center gap-3">
                                <div class="text-danger fs-3"><i class="fas fa-info-circle"></i></div>
                                <div class="small">
                                    <strong>Carrossel Infinito na Landing Page:</strong> Os parceiros cadastrados em <a href="{{ route('admin.parceiros.index') }}" class="fw-bold text-danger">Empresas Parceiras</a> serão exibidos dinamicamente com suporte a rolagem contínua infinita, touch & drag (mouse e celular) e pausa ao passar o mouse.
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-body-tertiary p-3 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR TEXTOS DA SEÇÃO DE PARCEIROS
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 7: BANNER LGPD & TERMOS DE PRIVACIDADE -->
            <div class="tab-pane fade" id="tab-lgpd" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex align-items-center">
                            <h6 class="card-title fw-bold mb-0 text-danger"><i class="fas fa-user-shield me-2"></i> Banner de Consentimento LGPD (Cookies) &amp; Termos Legais</h6>
                            <div class="card-tools ms-auto">
                                <a href="{{ route('site.privacidade') }}" target="_blank" class="btn btn-outline-danger btn-sm fw-bold">
                                    <i class="fas fa-external-link-alt me-1"></i> Visualizar Página de Termos
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- CONTROLE DO BANNER FLUTUANTE -->
                            <div class="p-3 bg-body-secondary rounded-3 border mb-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-cookie-bite text-warning me-2"></i> Configurações do Banner de Cookies (LGPD)</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Exibir Banner LGPD no Site?</label>
                                        <select name="lgpd_ativo" class="form-select">
                                            <option value="1" {{ ($settings['lgpd_ativo'] ?? '1') == '1' ? 'selected' : '' }}>Sim (Ativo)</option>
                                            <option value="0" {{ ($settings['lgpd_ativo'] ?? '1') == '0' ? 'selected' : '' }}>Não (Ocultar)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Texto do Botão de Aceitar</label>
                                        <input type="text" name="lgpd_btn_aceitar" class="form-control" value="{{ $settings['lgpd_btn_aceitar'] ?? 'Aceitar e Continuar' }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Texto do Link para os Termos</label>
                                        <input type="text" name="lgpd_btn_termos" class="form-control" value="{{ $settings['lgpd_btn_termos'] ?? 'Ler Política de Privacidade' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Texto Resumido do Banner de Cookies</label>
                                        <textarea name="lgpd_texto" rows="2" class="form-control">{{ $settings['lgpd_texto'] ?? 'Utilizamos cookies essenciais e tecnologias semelhantes para garantir o funcionamento seguro do nosso sistema, personalizar sua experiência e analisar o tráfego em conformidade com a LGPD (Lei nº 13.709/2018).' }}</textarea>
                                        <small class="text-secondary">Mensagem explicativa rápida exibida na barra flutuante no rodapé do site.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTEÚDO OFICIAL DA PÁGINA DE TERMOS & PRIVACIDADE -->
                            <div>
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-file-contract text-danger me-2"></i> Página Oficial de Política de Privacidade & Termos de Uso</h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold">Título da Página de Privacidade</label>
                                        <input type="text" name="privacidade_titulo" class="form-control" value="{{ $settings['privacidade_titulo'] ?? 'Termos de Uso e Política de Privacidade de Dados' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Data de Revisão / Atualização</label>
                                        <input type="text" name="privacidade_atualizacao" class="form-control" value="{{ $settings['privacidade_atualizacao'] ?? date('d/m/Y') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                            <span>Conteúdo Completo dos Termos (Texto Legal e Diretrizes LGPD)</span>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Editor Summernote</span>
                                        </label>
                                        <textarea name="privacidade_conteudo" id="privacidade_conteudo" class="form-control summernote" data-height="350">{{ $settings['privacidade_conteudo'] ?? '' }}</textarea>
                                        <small class="text-secondary">Se deixado em branco, o sistema exibirá automaticamente a minuta jurídica padrão completa com enquadramento da LGPD, telemetria veicular e direitos do titular.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-body-tertiary p-3 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DE LGPD & TERMOS
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 8: WHATSAPP FLUTUANTE & BOX MULTICANAIS DE SUPORTE -->
            <div class="tab-pane fade" id="tab-whatsapp" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0 text-success"><i class="fab fa-whatsapp me-2"></i> Botão Flutuante & Box Multicanais de Suporte</h6>
                                <small class="text-secondary">Configure o botão flutuante oficial e ative ou oculte individualmente cada canal de atendimento dentro do box.</small>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">5 Canais de Atendimento</span>
                        </div>
                        <div class="card-body p-4">
                            <!-- CONFIGURAÇÃO GERAL DO BOX -->
                            <div class="p-3 bg-body-secondary rounded-3 border mb-4">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-sliders-h text-danger me-2"></i> Cabeçalho do Box de Atendimento</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Botão Flutuante Ativo no Site?</label>
                                        <select name="whatsapp_box_ativo" class="form-select">
                                            <option value="1" {{ ($settings['whatsapp_box_ativo'] ?? '1') == '1' ? 'selected' : '' }}>Sim (Exibir Botão)</option>
                                            <option value="0" {{ ($settings['whatsapp_box_ativo'] ?? '1') == '0' ? 'selected' : '' }}>Não (Ocultar)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Título do Box Pop-up</label>
                                        <input type="text" name="whatsapp_box_titulo" class="form-control" value="{{ $settings['whatsapp_box_titulo'] ?? 'Central de Atendimento Foco 7' }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Subtítulo / Mensagem Inicial</label>
                                        <input type="text" name="whatsapp_box_subtitulo" class="form-control" value="{{ $settings['whatsapp_box_subtitulo'] ?? 'Atendimento Humanizado • Selecione o canal desejado:' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- OS 5 CANAIS DE SUPORTE GERENCIÁVEIS INDIVIDUALMENTE -->
                            <h6 class="fw-bold text-body mb-3"><i class="fas fa-headset text-danger me-2"></i> Canais de Atendimento (Exibir / Ocultar Individualmente)</h6>
                            <div class="row g-4">
                                
                                <!-- CANAL 1: RONDA & EMERGÊNCIA 24H -->
                                <div class="col-md-6">
                                    <div class="card border border-danger-subtle h-100 shadow-sm">
                                        <div class="card-header bg-danger-subtle d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-danger"><i class="fas fa-shield-halved me-1"></i> Canal 1: Ronda Tática & Emergência 24h</span>
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="whatsapp_canal_1_ativo" value="0">
                                                <input class="form-check-input" type="checkbox" name="whatsapp_canal_1_ativo" value="1" id="swCanal1" {{ ($settings['whatsapp_canal_1_ativo'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="swCanal1">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Nome do Canal</label>
                                                    <input type="text" name="whatsapp_canal_1_nome" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_1_nome'] ?? 'Plantão Ronda Tática & Emergência 24h' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Número WhatsApp (com DDD)</label>
                                                    <input type="text" name="whatsapp_canal_1_num" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_1_num'] ?? '559180510736' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Descrição Curta</label>
                                                    <input type="text" name="whatsapp_canal_1_desc" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_1_desc'] ?? 'Sinistros, roubo, furto ou assistência imediata' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Mensagem Automática</label>
                                                    <input type="text" name="whatsapp_canal_1_msg" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_1_msg'] ?? 'Emergência Foco 7: Preciso de apoio da Ronda Tática ou Central 24h.' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CANAL 2: COMERCIAL & NOVOS PLANOS -->
                                <div class="col-md-6">
                                    <div class="card border border-primary-subtle h-100 shadow-sm">
                                        <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-primary"><i class="fas fa-tags me-1"></i> Canal 2: Comercial & Novos Planos</span>
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="whatsapp_canal_2_ativo" value="0">
                                                <input class="form-check-input" type="checkbox" name="whatsapp_canal_2_ativo" value="1" id="swCanal2" {{ ($settings['whatsapp_canal_2_ativo'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="swCanal2">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Nome do Canal</label>
                                                    <input type="text" name="whatsapp_canal_2_nome" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_2_nome'] ?? 'Comercial & Novos Planos' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Número WhatsApp (com DDD)</label>
                                                    <input type="text" name="whatsapp_canal_2_num" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_2_num'] ?? '559180510736' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Descrição Curta</label>
                                                    <input type="text" name="whatsapp_canal_2_desc" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_2_desc'] ?? 'Cotações de carros, motos, frotas e promoções' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Mensagem Automática</label>
                                                    <input type="text" name="whatsapp_canal_2_msg" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_2_msg'] ?? 'Olá! Gostaria de uma cotação de rastreamento veicular com a Foco 7.' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CANAL 3: SUPORTE TÉCNICO & APP -->
                                <div class="col-md-6">
                                    <div class="card border border-info-subtle h-100 shadow-sm">
                                        <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-body"><i class="fas fa-headset me-1 text-info"></i> Canal 3: Suporte Técnico & Aplicativo</span>
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="whatsapp_canal_3_ativo" value="0">
                                                <input class="form-check-input" type="checkbox" name="whatsapp_canal_3_ativo" value="1" id="swCanal3" {{ ($settings['whatsapp_canal_3_ativo'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="swCanal3">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Nome do Canal</label>
                                                    <input type="text" name="whatsapp_canal_3_nome" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_3_nome'] ?? 'Suporte Técnico & Aplicativo' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Número WhatsApp (com DDD)</label>
                                                    <input type="text" name="whatsapp_canal_3_num" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_3_num'] ?? '559180510736' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Descrição Curta</label>
                                                    <input type="text" name="whatsapp_canal_3_desc" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_3_desc'] ?? 'Dúvidas sobre o app, bloqueio, login ou sinal GPS' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Mensagem Automática</label>
                                                    <input type="text" name="whatsapp_canal_3_msg" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_3_msg'] ?? 'Olá! Preciso de suporte técnico sobre o rastreador / aplicativo.' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CANAL 4: ESTÉTICA AUTOMOTIVA -->
                                <div class="col-md-6">
                                    <div class="card border border-warning-subtle h-100 shadow-sm">
                                        <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-body"><i class="fas fa-spray-can-sparkles me-1 text-warning"></i> Canal 4: Estética Automotiva & Serviços</span>
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="whatsapp_canal_4_ativo" value="0">
                                                <input class="form-check-input" type="checkbox" name="whatsapp_canal_4_ativo" value="1" id="swCanal4" {{ ($settings['whatsapp_canal_4_ativo'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="swCanal4">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Nome do Canal</label>
                                                    <input type="text" name="whatsapp_canal_4_nome" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_4_nome'] ?? 'Estética Automotiva & Detalhamento' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Número WhatsApp (com DDD)</label>
                                                    <input type="text" name="whatsapp_canal_4_num" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_4_num'] ?? '559180510736' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Descrição Curta</label>
                                                    <input type="text" name="whatsapp_canal_4_desc" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_4_desc'] ?? 'Vitrificação, polimento técnico e higienização' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Mensagem Automática</label>
                                                    <input type="text" name="whatsapp_canal_4_msg" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_4_msg'] ?? 'Olá! Gostaria de agendar um serviço de Estética Automotiva.' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CANAL 5: FINANCEIRO & 2ª VIA -->
                                <div class="col-12">
                                    <div class="card border border-success-subtle shadow-sm">
                                        <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center py-2">
                                            <span class="fw-bold text-success"><i class="fas fa-file-invoice-dollar me-1"></i> Canal 5: Financeiro & 2ª Via de Boletos</span>
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="whatsapp_canal_5_ativo" value="0">
                                                <input class="form-check-input" type="checkbox" name="whatsapp_canal_5_ativo" value="1" id="swCanal5" {{ ($settings['whatsapp_canal_5_ativo'] ?? '1') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="swCanal5">Ativo</label>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Nome do Canal</label>
                                                    <input type="text" name="whatsapp_canal_5_nome" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_5_nome'] ?? 'Financeiro & 2ª Via de Boletos' }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Número WhatsApp (com DDD)</label>
                                                    <input type="text" name="whatsapp_canal_5_num" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_5_num'] ?? '559180510736' }}">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-bold">Descrição Curta</label>
                                                    <input type="text" name="whatsapp_canal_5_desc" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_5_desc'] ?? 'Segunda via de mensalidades, PIX e notas fiscais' }}">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Mensagem Automática</label>
                                                    <input type="text" name="whatsapp_canal_5_msg" class="form-control form-control-sm" value="{{ $settings['whatsapp_canal_5_msg'] ?? 'Olá! Preciso de auxílio com minha fatura/mensalidade Foco 7.' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer bg-body-tertiary p-3 text-end">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DO WHATSAPP & CANAIS
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- MODAL CADASTRAR / EDITAR SLIDE -->
<div class="modal fade" id="modalBanner" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalBannerTitulo">Slide do Hero Carousel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cms.salvar_banner') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formBannerSlide">
                @csrf
                <input type="hidden" name="tipo" value="hero_slide">
                <input type="hidden" name="banner_id" id="banner_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Badge Superior (Opcional)</label>
                            <input type="text" name="badge" id="banner_badge" class="form-control" placeholder="Ex: 🛡️ TECNOLOGIA DE PONTA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="banner_ordem" class="form-control" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Título Principal *</label>
                            <input type="text" name="titulo" id="banner_titulo" class="form-control" required placeholder="Título chamativo de alta conversão">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                <span>Subtítulo Explicativo</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Summernote</span>
                            </label>
                            <textarea name="subtitulo" id="banner_subtitulo" rows="3" class="form-control summernote" data-height="120" placeholder="Descrição do impacto da proteção veicular"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Imagem de Fundo do Slide (Opcional)</label>
                            <div class="foco-dropzone"
                                 data-input-name="imagem_upload"
                                 data-ratio="1920 × 650 px (Banner 16:9)"
                                 data-max-size="5MB"
                                 data-accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Texto do Botão 1</label>
                            <input type="text" name="texto_botao_1" id="banner_texto_botao_1" class="form-control" value="PROTEGER MEU VEÍCULO AGORA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link do Botão 1</label>
                            <input type="text" name="link_botao_1" id="banner_link_botao_1" class="form-control" value="#planos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Texto do Botão 2</label>
                            <input type="text" name="texto_botao_2" id="banner_texto_botao_2" class="form-control" value="FALAR NO WHATSAPP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link do Botão 2</label>
                            <input type="text" name="link_botao_2" id="banner_link_botao_2" class="form-control" value="https://wa.me/559180510736">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CADASTRAR / EDITAR FAQ -->
<div class="modal fade" id="modalFaq" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalFaqTitulo">Pergunta Frequente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cms.salvar_faq') }}" method="POST" class="ajax-form" id="formFaq">
                @csrf
                <input type="hidden" name="faq_id" id="faq_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pergunta *</label>
                        <input type="text" name="pergunta" id="faq_pergunta" class="form-control" required placeholder="Como funciona a instalação?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Categoria</label>
                        <select name="categoria" id="faq_categoria" class="form-select">
                            <option value="geral">Geral</option>
                            <option value="instalacao">Instalação</option>
                            <option value="estetica">Estética Automotiva</option>
                            <option value="ronda">Ronda Tática</option>
                            <option value="aplicativo">Aplicativo</option>
                            <option value="financeiro">Financeiro / Pagamento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                            <span>Resposta Completa *</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Summernote</span>
                        </label>
                        <textarea name="resposta" id="faq_resposta" rows="4" class="form-control summernote" data-height="180" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CADASTRAR / EDITAR DEPOIMENTO -->
<div class="modal fade" id="modalDepoimento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalDepoimentoTitulo">Depoimento de Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.depoimentos.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formDepoimento">
                @csrf
                <input type="hidden" name="depoimento_id" id="depoimento_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nome do Cliente *</label>
                        <input type="text" name="nome_cliente" id="depoimento_nome" class="form-control" required placeholder="Ex: Roberto Silva">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Cidade - UF</label>
                            <input type="text" name="cidade_uf" id="depoimento_cidade_uf" class="form-control" placeholder="Belém - PA">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Veículo</label>
                            <input type="text" name="veiculo" id="depoimento_veiculo" class="form-control" placeholder="Hilux, Fan 160">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto do Cliente (Opcional)</label>
                        <div class="foco-dropzone"
                             data-input-name="foto_upload"
                             data-ratio="200 × 200 px (Foto 1:1 Quadrado)"
                             data-max-size="3MB"
                             data-accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold d-flex justify-content-between">
                            <span>Avaliação em Estrelas *</span>
                            <span id="labelEstrelasAdmin" class="text-warning fw-bold small">5 de 5 estrelas - Excelente!</span>
                        </label>
                        <div class="foco-star-rating-box p-2 bg-body-secondary border rounded d-flex align-items-center gap-2" id="starPickerAdmin">
                            <i class="fas fa-star fa-lg text-warning cursor-pointer star-pick-item" data-rating="1" title="1 Estrela"></i>
                            <i class="fas fa-star fa-lg text-warning cursor-pointer star-pick-item" data-rating="2" title="2 Estrelas"></i>
                            <i class="fas fa-star fa-lg text-warning cursor-pointer star-pick-item" data-rating="3" title="3 Estrelas"></i>
                            <i class="fas fa-star fa-lg text-warning cursor-pointer star-pick-item" data-rating="4" title="4 Estrelas"></i>
                            <i class="fas fa-star fa-lg text-warning cursor-pointer star-pick-item" data-rating="5" title="5 Estrelas"></i>
                        </div>
                        <input type="hidden" name="estrelas" id="depoimento_estrelas" value="5">
                        <small class="text-secondary">Passe o mouse para pré-visualizar ou clique na estrela desejada.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                            <span>Depoimento *</span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Summernote</span>
                        </label>
                        <textarea name="texto" id="depoimento_texto" rows="3" class="form-control summernote" data-height="140" required placeholder="O que o cliente disse sobre a proteção e agilidade?"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar Depoimento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalBanner() {
        $('#banner_id').val('');
        $('#formBannerSlide')[0].reset();
        window.clearSummernote('#banner_subtitulo');
        if (window.FocoDropzone) {
            window.FocoDropzone.reset('#formBannerSlide .foco-dropzone');
        }
        $('#modalBannerTitulo').text('Novo Slide do Hero Carousel');
        new bootstrap.Modal(document.getElementById('modalBanner')).show();
    }

    function editarBanner(id) {
        $.getJSON('/admin/cms/banner/obter/' + id, function(res) {
            if (res.success && res.banner) {
                var b = res.banner;
                $('#banner_id').val(b.id);
                $('#banner_badge').val(b.badge);
                $('#banner_ordem').val(b.ordem);
                $('#banner_titulo').val(b.titulo);
                window.setSummernoteContent('#banner_subtitulo', b.subtitulo || '');
                $('#banner_texto_botao_1').val(b.texto_botao_1);
                $('#banner_link_botao_1').val(b.link_botao_1);
                $('#banner_texto_botao_2').val(b.texto_botao_2);
                $('#banner_link_botao_2').val(b.link_botao_2);

                if (window.FocoDropzone) {
                    var imgUrl = res.imagem_url || (b.imagem ? ('/' + b.imagem.replace(/^\//, '')) : '');
                    window.FocoDropzone.setPreview('#formBannerSlide .foco-dropzone', imgUrl);
                }

                $('#modalBannerTitulo').text('Editar Slide #' + b.id);
                new bootstrap.Modal(document.getElementById('modalBanner')).show();
            }
        });
    }

    function abrirModalFaq() {
        $('#faq_id').val('');
        $('#formFaq')[0].reset();
        window.clearSummernote('#faq_resposta');
        $('#modalFaqTitulo').text('Nova Pergunta Frequente');
        new bootstrap.Modal(document.getElementById('modalFaq')).show();
    }

    function editarFaq(id) {
        $.getJSON('/admin/cms/faq/obter/' + id, function(res) {
            if (res.success && res.faq) {
                var f = res.faq;
                $('#faq_id').val(f.id);
                $('#faq_pergunta').val(f.pergunta);
                $('#faq_categoria').val(f.categoria);
                window.setSummernoteContent('#faq_resposta', f.resposta || '');

                $('#modalFaqTitulo').text('Editar Pergunta Frequente');
                new bootstrap.Modal(document.getElementById('modalFaq')).show();
            }
        });
    }

    function abrirModalDepoimento() {
        $('#depoimento_id').val('');
        $('#formDepoimento')[0].reset();
        window.clearSummernote('#depoimento_texto');
        if (window.FocoDropzone) {
            window.FocoDropzone.reset('#formDepoimento .foco-dropzone');
        }
        setEstrelasAdmin(5);
        $('#modalDepoimentoTitulo').text('Novo Depoimento');
        new bootstrap.Modal(document.getElementById('modalDepoimento')).show();
    }

    function setEstrelasAdmin(nota) {
        nota = parseInt(nota) || 5;
        $('#depoimento_estrelas').val(nota);
        var labels = {
            1: '1 de 5 estrelas - Regular',
            2: '2 de 5 estrelas - Bom',
            3: '3 de 5 estrelas - Muito Bom',
            4: '4 de 5 estrelas - Ótimo',
            5: '5 de 5 estrelas - Excelente!'
        };
        $('#labelEstrelasAdmin').text(labels[nota] || (nota + ' de 5 estrelas'));
        renderizarEstrelasVisualAdmin(nota);
    }

    function renderizarEstrelasVisualAdmin(nota) {
        $('#starPickerAdmin .star-pick-item').each(function () {
            var r = parseInt($(this).data('rating'));
            if (r <= nota) {
                $(this).removeClass('far text-muted').addClass('fas text-warning');
            } else {
                $(this).removeClass('fas text-warning').addClass('far text-muted');
            }
        });
    }

    // Hover dinâmico no seletor de estrelas do admin
    $(document).on('mouseenter', '#starPickerAdmin .star-pick-item', function () {
        var hoverRating = parseInt($(this).data('rating'));
        renderizarEstrelasVisualAdmin(hoverRating);
    });

    $(document).on('mouseleave', '#starPickerAdmin', function () {
        var currentRating = parseInt($('#depoimento_estrelas').val()) || 5;
        renderizarEstrelasVisualAdmin(currentRating);
    });

    $(document).on('click', '#starPickerAdmin .star-pick-item', function () {
        var clickRating = parseInt($(this).data('rating'));
        setEstrelasAdmin(clickRating);
    });

    function editarDepoimento(id) {
        $.getJSON('/admin/cms/depoimento/obter/' + id, function(res) {
            if (res.success && res.depoimento) {
                var d = res.depoimento;
                $('#depoimento_id').val(d.id);
                $('#depoimento_nome').val(d.nome_cliente);
                $('#depoimento_cidade_uf').val(d.cidade_uf);
                $('#depoimento_veiculo').val(d.veiculo);
                setEstrelasAdmin(d.estrelas || 5);
                window.setSummernoteContent('#depoimento_texto', d.texto || '');

                if (window.FocoDropzone) {
                    var fotoUrl = res.foto_url || (d.foto ? ('/' + d.foto.replace(/^\//, '')) : '');
                    window.FocoDropzone.setPreview('#formDepoimento .foco-dropzone', fotoUrl);
                }

                $('#modalDepoimentoTitulo').text('Editar Depoimento: ' + d.nome_cliente);
                new bootstrap.Modal(document.getElementById('modalDepoimento')).show();
            }
        });
    }

    // Exclusão com SweetAlert2 (Sem alert() nativo)
    function excluirItem(tipo, id) {
        window.confirmarAcao(
            'Confirmar Exclusão?',
            'Este item será removido permanentemente do site.',
            'warning',
            function () {
                var url = '/admin/cms/' + tipo + '/' + id;
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function () { window.location.reload(); }, 600);
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao remover item.');
                    }
                });
            }
        );
    }

    // Controle em Tempo Real da Opacidade e Overlay do Hero
    function atualizarPreviewOpacidade(val) {
        var badge = document.getElementById('badgeOpacidade');
        if (badge) badge.innerText = val + '%';
        var decimal = (parseFloat(val) / 100);
        var overlay = document.getElementById('heroPreviewOverlay');
        if (overlay) {
            overlay.style.opacity = decimal;
        }
    }

    function atualizarCorOverlay(cor) {
        var textInput = document.getElementById('hero_overlay_cor_text');
        if (textInput) textInput.value = cor;
        var overlay = document.getElementById('heroPreviewOverlay');
        if (overlay) {
            overlay.style.backgroundColor = cor;
        }
        var textTitle = document.getElementById('heroPreviewText');
        if (textTitle) {
            var isDark = cor.toLowerCase() === '#000000' || cor.toLowerCase() === '#0b0f19';
            textTitle.className = isDark ? 'fw-black mb-1 text-white' : 'fw-black mb-1 text-dark';
        }
    }

    function atualizarBlur(val) {
        var badge = document.getElementById('badgeBlur');
        if (badge) badge.innerText = val + 'px';
        var box = document.getElementById('heroPreviewBox');
        if (box) {
            box.style.filter = val > 0 ? 'blur(' + val + 'px)' : 'none';
        }
    }

    // Função Global para Ativação Instantânea de Abas do CMS (pelo sidebar ou pelos cards de KPIs)
    window.ativarAbaCms = function (tabId, event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        var cleanId = tabId.replace(/^#/, '');
        var tabBtn = document.querySelector('#cmsTabs button[data-bs-target="#' + cleanId + '"]');
        if (tabBtn) {
            var tab = bootstrap.Tab.getOrCreateInstance(tabBtn);
            tab.show();
            if (history.replaceState) {
                history.replaceState(null, null, '#' + cleanId);
            }
            var el = document.getElementById('cmsTabs');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    };

    function aplicarAbaPelaHash() {
        var hash = window.location.hash;
        if (hash) {
            var cleanId = hash.replace(/^#/, '');
            var tabBtn = document.querySelector('#cmsTabs button[data-bs-target="#' + cleanId + '"]');
            if (tabBtn) {
                var tab = bootstrap.Tab.getOrCreateInstance(tabBtn);
                tab.show();
            }
        }
    }

    window.addEventListener('hashchange', aplicarAbaPelaHash);

    $(function() {
        aplicarAbaPelaHash();

        // Atualiza a hash na URL ao alternar de aba
        $('#cmsTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).data('bs-target');
            if (target && history.replaceState) {
                history.replaceState(null, null, target);
            }
        });
    });
</script>
@endsection

