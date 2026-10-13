@extends('admin.layouts.app')

@section('title', 'Otimização de SEO Multi-Páginas')
@section('page_title', 'Central de SEO & Compartilhamento Social')
@section('page_description', 'Gerencie meta tags, palavras-chave, Open Graph e imagens de compartilhamento de cada página do sistema.')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <!-- SELETOR DE PÁGINAS DO SISTEMA (TABS RESPONSIVAS) -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <ul class="nav nav-pills flex-wrap gap-2" id="seoPaginasTabs" role="tablist">
                        @foreach($paginas as $pChave => $pInfo)
                            <li class="nav-item">
                                <a href="{{ route('admin.seo.index', ['pagina' => $pChave]) }}" 
                                   class="nav-link py-2 px-3 fw-semibold d-inline-flex align-items-center gap-2 {{ $pagina === $pChave ? 'active bg-danger text-white shadow-sm' : 'bg-body-tertiary text-body' }}">
                                    <i class="{{ $pInfo['icone'] }}"></i>
                                    <span>{{ $pInfo['nome'] }}</span>
                                    <span class="badge {{ $pagina === $pChave ? 'bg-white text-danger' : 'bg-secondary bg-opacity-25 text-body' }} small ms-1">
                                        {{ $pInfo['badge'] }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ $paginas[$pagina]['url'] ?? url('/') }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm" title="Abrir página pública em nova aba">
                            <i class="fas fa-external-link-alt text-danger"></i>
                            <span class="d-none d-sm-inline">Testar Página Pública</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Formulário de Meta Tags da Página Selecionada -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header w-100 bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title fw-bold mb-0 text-body">
                    <i class="{{ $paginas[$pagina]['icone'] ?? 'fas fa-sliders-h' }} text-danger me-2"></i> 
                    Meta Tags &amp; Open Graph: <span class="text-danger">{{ $paginas[$pagina]['nome'] ?? ucfirst($pagina) }}</span>
                </h5>
                <div class="card-tools ms-auto">
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 font-monospace">
                        pagina={{ $pagina }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.seo.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formSeo">
                    @csrf
                    <input type="hidden" name="pagina" id="seo_pagina" value="{{ $pagina }}">

                    <!-- META TITLE -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Meta Title (Título no Google e Navegador) *</label>
                            <span class="small font-monospace text-secondary" id="char-count-title">0 / 65 caracteres</span>
                        </div>
                        <input type="text" name="meta_title" id="seo_meta_title" class="form-control" value="{{ $seo->meta_title }}" required maxlength="255" placeholder="Ex: Login & Acesso Seguro | Foco 7 Rastreamento">
                        <div class="form-text small">Recomendado pelo Google: entre <strong>40 e 65</strong> caracteres com a marca no final ou início.</div>
                    </div>

                    <!-- META DESCRIPTION -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Meta Description (Resumo / Snippet nos Buscadores) *</label>
                            <span class="small font-monospace text-secondary" id="char-count-desc">0 / 160 caracteres</span>
                        </div>
                        <textarea name="meta_description" id="seo_meta_description" rows="3" class="form-control" required maxlength="500" placeholder="Insira uma descrição concisa e persuasiva que resuma o objetivo desta página...">{{ $seo->meta_description }}</textarea>
                        <div class="form-text small">Recomendado pelo Google: entre <strong>120 e 160</strong> caracteres para evitar truncamento com reticências.</div>
                    </div>

                    <!-- META KEYWORDS -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Palavras-chave (Meta Keywords separadas por vírgula)</label>
                        <input type="text" name="meta_keywords" id="seo_meta_keywords" class="form-control" value="{{ $seo->meta_keywords }}" placeholder="Ex: login foco 7, rastreador veicular, painel do cliente, bloqueio veicular">
                        <div class="form-text small">Termos estratégicos para indexação e busca orgânica local e nacional.</div>
                        <div id="keywords-tags-container" class="d-flex flex-wrap gap-1 mt-2"></div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- INDEXAÇÃO ROBOTS -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Diretiva de Robôs (Robots)</label>
                            <select name="robots" id="seo_robots" class="form-select">
                                <option value="index, follow" {{ ($seo->robots ?? '') === 'index, follow' ? 'selected' : '' }}>index, follow (Indexar no Google e Seguir Links)</option>
                                <option value="noindex, follow" {{ ($seo->robots ?? '') === 'noindex, follow' ? 'selected' : '' }}>noindex, follow (Não exibir na busca, mas seguir links)</option>
                                <option value="noindex, nofollow" {{ ($seo->robots ?? '') === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow (Bloquear busca e links)</option>
                            </select>
                        </div>

                        <!-- CANONICAL URL -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">URL Canônica (Canonical URL)</label>
                            <input type="url" name="canonical_url" id="seo_canonical_url" class="form-control" value="{{ $seo->canonical_url ?: ($paginas[$pagina]['url'] ?? url('/')) }}" placeholder="https://foco7.com.br/login">
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <h6 class="fw-bold text-body mb-3 d-flex align-items-center gap-2">
                            <i class="fab fa-whatsapp text-success"></i> Open Graph &amp; Redes Sociais (WhatsApp, Facebook, Twitter, LinkedIn)
                        </h6>

                        <!-- OPEN GRAPH TITLE -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Título de Compartilhamento (og:title / twitter:title)</label>
                            <input type="text" name="og_title" id="seo_og_title" class="form-control" value="{{ $seo->og_title ?: $seo->meta_title }}" placeholder="Título chamativo para o card social">
                        </div>

                        <!-- OPEN GRAPH DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descrição de Compartilhamento (og:description)</label>
                            <textarea name="og_description" id="seo_og_description" rows="2" class="form-control" placeholder="Resumo exibido abaixo do título nos cards sociais...">{{ $seo->og_description ?: $seo->meta_description }}</textarea>
                        </div>

                        <!-- IMAGEM DE COMPARTILHAMENTO (OG IMAGE) -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-image text-danger me-1"></i> Imagem de Compartilhamento Social (OG Image)</span>
                                <span class="badge bg-secondary font-monospace" style="font-size: 0.75rem;">1200 &times; 630 px (1.91:1)</span>
                            </label>
                            <div class="foco-dropzone"
                                 data-input-name="og_image_upload"
                                 data-ratio="1200 × 630 px (Open Graph / WhatsApp / Facebook)"
                                 data-max-size="5MB"
                                 data-accept="image/png,image/jpeg,image/webp"
                                 data-current-preview="{{ !empty($seo->og_image) ? asset(ltrim($seo->og_image, '/')) : asset('img/og-foco7.jpg') }}">
                            </div>
                            <div class="form-text small mt-1">
                                Formato ideal: PNG, JPG ou WEBP de alta resolução. Esta é a imagem destacada que aparece ao colar o link no WhatsApp, Telegram, Facebook e Twitter.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>SALVAR SEO DA PÁGINA ({{ strtoupper($pagina) }})</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Coluna de Score, Checklist e Previews Interativos -->
    <div class="col-lg-5">
        <!-- SEO SCORE EM TEMPO REAL -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header w-100 bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-body"><i class="fas fa-chart-line text-danger me-2"></i> SEO Score em Tempo Real</h5>
                <div class="card-tools ms-auto">
                    <span id="seo-score-status" class="badge bg-success">Excelente</span>
                </div>
            </div>
            <div class="card-body p-4 text-center">
                <h1 class="display-4 fw-black text-danger mb-2" id="seo-score-value">{{ $seo->seo_score ?? 90 }}/100</h1>
                <div class="progress mb-3" style="height: 12px;">
                    <div id="seo-score-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: {{ $seo->seo_score ?? 90 }}%;"></div>
                </div>
                <p class="small text-secondary mb-0">Pontuação computada automaticamente com base nas diretrizes oficiais de busca do Google e Open Graph.</p>
            </div>
        </div>

        <!-- PRÉVIA NO GOOGLE (SERP PREVIEW) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header w-100 bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title fw-bold mb-0 text-body"><i class="fab fa-google text-danger me-2"></i> Prévia nos Resultados do Google (SERP)</h6>
                <div class="card-tools ms-auto">
                    <span class="badge bg-secondary font-monospace" style="font-size: 0.7rem;">Snippet Google</span>
                </div>
            </div>
            <div class="card-body p-3 bg-body-tertiary rounded-bottom border-top">
                <div class="p-3 bg-body rounded border shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="{{ asset('img/favicon.png') }}" width="16" height="16" alt="Foco 7" class="rounded">
                        <span class="small text-muted font-monospace" id="preview-google-url" style="font-size: 0.8rem;">
                            {{ $seo->canonical_url ?: ($paginas[$pagina]['url'] ?? 'https://foco7.com.br') }}
                        </span>
                    </div>
                    <h6 class="fw-bold text-primary mb-1 text-truncate" id="preview-google-title" style="cursor: pointer; text-decoration: none;">
                        {{ $seo->meta_title }}
                    </h6>
                    <p class="small text-body mb-0" id="preview-google-desc" style="font-size: 0.85rem; line-height: 1.45;">
                        {{ $seo->meta_description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- PRÉVIA EM REDES SOCIAIS & WHATSAPP (OPEN GRAPH CARD) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header w-100 bg-transparent py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title fw-bold mb-0 text-body">
                    <i class="fab fa-whatsapp text-success me-2"></i> Prévia no WhatsApp &amp; Redes Sociais
                </h6>
                <div class="card-tools ms-auto">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.7rem;">Open Graph</span>
                </div>
            </div>
            <div class="card-body p-3 bg-body-tertiary rounded-bottom border-top">
                <!-- Card Estilo WhatsApp / Facebook Link Preview -->
                <div class="card border-0 shadow-sm overflow-hidden" style="background-color: var(--bs-card-bg); border-radius: 12px;">
                    <div class="position-relative bg-dark d-flex align-items-center justify-content-center overflow-hidden" style="height: 180px;">
                        <img id="preview-social-img" 
                             src="{{ !empty($seo->og_image) ? asset(ltrim($seo->og_image, '/')) : asset('img/og-foco7.jpg') }}" 
                             alt="OG Image Preview" 
                             class="w-100 h-100 object-fit-cover"
                             onerror="this.src='{{ asset('img/og-foco7.jpg') }}'">
                        <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white font-monospace small">
                            <i class="fas fa-image me-1"></i> Imagem OG (1200 &times; 630)
                        </span>
                    </div>
                    <div class="p-3 bg-body">
                        <small class="text-uppercase text-muted fw-bold font-monospace d-block mb-1" id="preview-social-domain" style="font-size: 0.72rem;">
                            foco7.com.br
                        </small>
                        <h6 class="fw-bold text-body mb-1" id="preview-social-title" style="font-size: 0.95rem; line-height: 1.3;">
                            {{ $seo->og_title ?: $seo->meta_title }}
                        </h6>
                        <p class="small text-secondary mb-0 line-clamp-2" id="preview-social-desc" style="font-size: 0.82rem; line-height: 1.35;">
                            {{ $seo->og_description ?: $seo->meta_description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHECKLIST DE OTIMIZAÇÃO -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0 text-body"><i class="fas fa-tasks text-danger me-2"></i> Checklist de Requisitos SEO</h6>
            </div>
            <div class="card-body p-3" id="seo-checklist">
                <!-- Renderizado dinamicamente pelo seo-analyzer.js -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/seo-analyzer.js') }}?v={{ file_exists(public_path('js/seo-analyzer.js')) ? filemtime(public_path('js/seo-analyzer.js')) : time() }}"></script>
@endsection
