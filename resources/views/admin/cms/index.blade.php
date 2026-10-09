@extends('admin.layouts.app')

@section('title', 'Gerenciador do CMS')
@section('page_title', 'CMS & Identidade Visual')
@section('page_description', 'Gerencie todos os textos, logotipo, carrossel de imagens, banners, FAQs e depoimentos com editor visual Summernote.')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="cmsTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="identidade-tab" data-bs-toggle="tab" data-bs-target="#tab-identidade" type="button">
                    <i class="fas fa-palette text-danger me-1"></i> Identidade & Cores
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="contato-tab" data-bs-toggle="tab" data-bs-target="#tab-contato" type="button">
                    <i class="fas fa-address-book text-danger me-1"></i> Contatos & Horários
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="banners-tab" data-bs-toggle="tab" data-bs-target="#tab-banners" type="button">
                    <i class="fas fa-images text-danger me-1"></i> Banners & Slides
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
                <button class="nav-link fw-semibold" id="secoes-tab" data-bs-toggle="tab" data-bs-target="#tab-secoes" type="button">
                    <i class="fas fa-layer-group text-danger me-1"></i> Textos das Seções
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="cmsTabsContent">

            <!-- ABA 1: IDENTIDADE & CORES -->
            <div class="tab-pane fade show active" id="tab-identidade" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome Oficial da Empresa</label>
                            <input type="text" name="site_nome" class="form-control" value="{{ $settings['site_nome'] ?? 'Foco 7 - Rastreamento e Segurança' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slogan do Site</label>
                            <input type="text" name="site_slogan" class="form-control" value="{{ $settings['site_slogan'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Primária (Hexadecimal)</label>
                            <div class="input-group">
                                <input type="color" name="cor_primaria" class="form-control form-control-color" value="{{ $settings['cor_primaria'] ?? '#d90000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_primaria'] ?? '#d90000' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Secundária (Fundo Escuro)</label>
                            <div class="input-group">
                                <input type="color" name="cor_secundaria" class="form-control form-control-color" value="{{ $settings['cor_secundaria'] ?? '#111111' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_secundaria'] ?? '#111111' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor de Destaque / Botões</label>
                            <div class="input-group">
                                <input type="color" name="cor_destaque" class="form-control form-control-color" value="{{ $settings['cor_destaque'] ?? '#e60000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_destaque'] ?? '#e60000' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="fas fa-image text-danger me-1"></i> Logotipo Principal</label>
                            <div class="foco-dropzone"
                                 data-input-name="logo_upload"
                                 data-ratio="500 × 120 px (Horizontal Transparente)"
                                 data-max-size="5MB"
                                 data-accept="image/png,image/webp,image/jpeg"
                                 data-current-preview="{{ !empty($settings['site_logo']) ? asset($settings['site_logo']) : asset('/img/logo-foco7.png') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold"><i class="fas fa-gem text-danger me-1"></i> Ícone Favicon</label>
                            <div class="foco-dropzone"
                                 data-input-name="favicon_upload"
                                 data-ratio="512 × 512 px (1:1 Quadrado)"
                                 data-max-size="2MB"
                                 data-accept="image/png,image/x-icon,image/webp"
                                 data-current-preview="{{ !empty($settings['site_favicon']) ? asset($settings['site_favicon']) : asset('/img/favicon.png') }}">
                            </div>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR IDENTIDADE & CORES
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 2: CONTATOS & HORÁRIOS -->
            <div class="tab-pane fade" id="tab-contato" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Telefone da Central</label>
                            <input type="text" name="contato_telefone" class="form-control mask-telefone" value="{{ $settings['contato_telefone'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">WhatsApp Oficial (Com DDI e DDD)</label>
                            <input type="text" name="contato_whatsapp" class="form-control" value="{{ $settings['contato_whatsapp'] ?? '559180510736' }}" required placeholder="Ex: 559180510736">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">WhatsApp Formatado (Exibição)</label>
                            <input type="text" name="contato_whatsapp_formatado" class="form-control mask-telefone" value="{{ $settings['contato_whatsapp_formatado'] ?? '(91) 8051-0736' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">E-mail Comercial</label>
                            <input type="email" name="contato_email" class="form-control" value="{{ $settings['contato_email'] ?? 'contato@foco7.com.br' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horário de Atendimento Exibido no Topo</label>
                            <input type="text" name="contato_horario" class="form-control" value="{{ $settings['contato_horario'] ?? 'Central 24 Horas | Plantão de Emergência' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Endereço da Sede</label>
                            <input type="text" name="contato_endereco" class="form-control" value="{{ $settings['contato_endereco'] ?? 'Tv. Quintino Bocaiúva, 1200 - Nazaré, Belém - PA' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                            <input type="url" name="social_instagram" class="form-control" value="{{ $settings['social_instagram'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                            <input type="url" name="social_facebook" class="form-control" value="{{ $settings['social_facebook'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-youtube text-danger me-1"></i> YouTube</label>
                            <input type="url" name="social_youtube" class="form-control" value="{{ $settings['social_youtube'] ?? '' }}">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CONTATOS & REDES
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 3: BANNERS & SLIDES -->
            <div class="tab-pane fade" id="tab-banners" role="tabpanel">
                <!-- SEÇÃO 1: PLANO DE FUNDO & CONTROLE DE OPACIDADE DO HERO -->
                <div class="card border border-light-subtle shadow-sm mb-4 rounded-3 overflow-hidden">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
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
                                    <div class="p-3 rounded-3 bg-light border mb-3">
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

            <!-- ABA 6: TEXTOS DAS SEÇÕES DO SITE -->
            <div class="tab-pane fade" id="tab-secoes" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <!-- 1. SEÇÃO SOBRE NÓS / QUEM SOMOS -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-3">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-shield-alt me-2"></i> Seção Sobre Nós / Autoridade Foco 7</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título Principal da Seção</label>
                                    <input type="text" name="secao_sobre_titulo" class="form-control" value="{{ $settings['secao_sobre_titulo'] ?? 'Mais de 10 Anos de Autoridade em Segurança Veicular' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subtítulo</label>
                                    <input type="text" name="secao_sobre_subtitulo" class="form-control" value="{{ $settings['secao_sobre_subtitulo'] ?? 'A Foco 7 nasceu com a missão de transformar tecnologia de rastreamento em tranquilidade real.' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Texto Narrativo / História da Empresa</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Editor Summernote</span>
                                    </label>
                                    <textarea name="secao_sobre_texto" id="secao_sobre_texto" class="form-control summernote" data-height="180">{{ $settings['secao_sobre_texto'] ?? 'Com sede própria e infraestrutura de ponta, monitoramos milhares de veículos em tempo real, combinando telemetria avançada, bloqueio remoto instantâneo e uma equipe de Ronda Tática presencial pronta para agir.' }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Métrica: Anos de Experiência</label>
                                    <input type="text" name="secao_sobre_anos" class="form-control" value="{{ $settings['secao_sobre_anos'] ?? '10+' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Métrica: Veículos Monitorados</label>
                                    <input type="text" name="secao_sobre_veiculos" class="form-control" value="{{ $settings['secao_sobre_veiculos'] ?? '15.000+' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Métrica: Índice de Recuperação</label>
                                    <input type="text" name="secao_sobre_recuperacao" class="form-control" value="{{ $settings['secao_sobre_recuperacao'] ?? '98.7%' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SEÇÃO ESTÉTICA AUTOMOTIVA & DIFERENCIAIS -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
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
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-certificate text-danger me-2"></i> 4 Diferenciais e Selos de Garantia (Rodapé da Seção Estética)</h6>
                                <div class="row g-3">
                                    <!-- DIFERENCIAL 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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

                    <!-- 3. SEÇÃO RISCOS VS SOLUÇÕES -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-3">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Seção Riscos vs Soluções</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título da Seção</label>
                                    <input type="text" name="secao_riscos_titulo" class="form-control" value="{{ $settings['secao_riscos_titulo'] ?? 'O Risco Não Avisa Antes de Chegar' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Subtítulo</label>
                                    <input type="text" name="secao_riscos_subtitulo" class="form-control" value="{{ $settings['secao_riscos_subtitulo'] ?? 'Todos os dias centenas de veículos são roubados ou furtados. Veja a diferença entre estar desprotegido e ter a Foco 7:' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SEÇÃO RONDA TÁTICA -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-3">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-motorcycle me-2"></i> Seção Ronda Tática & Apoio Presencial</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Título da Ronda Tática</label>
                                    <input type="text" name="secao_ronda_titulo" class="form-control" value="{{ $settings['secao_ronda_titulo'] ?? 'Ronda Tática Foco 7: Apoio Presencial Imediato' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                        <span>Descrição da Atuação Presencial</span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Editor Summernote</span>
                                    </label>
                                    <textarea name="secao_ronda_descricao" id="secao_ronda_descricao" class="form-control summernote" data-height="130">{{ $settings['secao_ronda_descricao'] ?? 'Em caso de roubo ou furto, nossa equipe tática especializada se desloca rapidamente até as coordenadas exatas do veículo para efetuar a recuperação em conjunto com as autoridades policiais.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. SEÇÃO APLICATIVO MÓVEL (ANDROID & IOS) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-mobile-alt me-2"></i> Seção Aplicativo Móvel (Tecnologia Mobile)</h6>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Recursos & Download</span>
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
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-th-large text-danger me-2"></i> 4 Cards de Recursos do Aplicativo</h6>
                                <div class="row g-3">
                                    <!-- RECURSO 1 -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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
                                        <div class="p-3 bg-light rounded-3 border h-100">
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

                            <!-- CARD DE DOWNLOAD & MOCKUP DO APP -->
                            <div class="border-top pt-3">
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-download text-danger me-2"></i> Card Lateral de Download & Imagem do Aplicativo</h6>
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
                                        <label class="form-label small fw-bold"><i class="fab fa-apple me-1 text-dark"></i> Link Apple App Store</label>
                                        <input type="text" name="secao_app_link_appstore" class="form-control" value="{{ $settings['secao_app_link_appstore'] ?? 'https://www.apple.com/app-store/' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold"><i class="fab fa-google-play me-1 text-success"></i> Link Google Play Store</label>
                                        <input type="text" name="secao_app_link_playstore" class="form-control" value="{{ $settings['secao_app_link_playstore'] ?? 'https://play.google.com/store/apps' }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label small fw-bold"><i class="fas fa-image text-danger me-1"></i> Mockup / Imagem Personalizada do Celular (Opcional)</label>
                                        <p class="text-secondary small mb-2">Se nenhuma imagem for enviada, o sistema exibe o ícone mobile oficial com gradiente e estilo responsivo.</p>
                                        <input type="file" name="app_mockup_upload" class="form-control" accept="image/png,image/jpeg,image/webp">
                                        @if(!empty($settings['secao_app_mockup_imagem']))
                                            <div class="d-flex align-items-center gap-3 mt-2 p-2 bg-light rounded border">
                                                <img src="{{ asset($settings['secao_app_mockup_imagem']) }}" alt="Mockup App" style="max-height: 60px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remover_app_mockup" value="1" id="rem_mockup">
                                                    <label class="form-check-label small text-danger fw-bold" for="rem_mockup">Remover imagem personalizada (usar ícone padrão)</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Textos das Seções
                        </button>
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
            <form action="{{ route('admin.cms.salvar_depoimento') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formDepoimento">
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
                        <label class="form-label small fw-bold">Estrelas</label>
                        <select name="estrelas" id="depoimento_estrelas" class="form-select">
                            <option value="5" selected>5 Estrelas (Excelente)</option>
                            <option value="4">4 Estrelas</option>
                            <option value="3">3 Estrelas</option>
                        </select>
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
        $('#modalDepoimentoTitulo').text('Novo Depoimento');
        new bootstrap.Modal(document.getElementById('modalDepoimento')).show();
    }

    function editarDepoimento(id) {
        $.getJSON('/admin/cms/depoimento/obter/' + id, function(res) {
            if (res.success && res.depoimento) {
                var d = res.depoimento;
                $('#depoimento_id').val(d.id);
                $('#depoimento_nome').val(d.nome_cliente);
                $('#depoimento_cidade_uf').val(d.cidade_uf);
                $('#depoimento_veiculo').val(d.veiculo);
                $('#depoimento_estrelas').val(d.estrelas);
                window.setSummernoteContent('#depoimento_texto', d.texto || '');

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
</script>
@endsection
