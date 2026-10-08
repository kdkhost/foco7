@extends('admin.layouts.app')

@section('title', 'Otimização de SEO')
@section('page_title', 'Módulo de SEO & Score em Tempo Real')
@section('page_description', 'Gerencie meta tags, títulos, descrições e monitore a pontuação de ranqueamento da Landing Page.')

@section('content')
<div class="row g-4">
    <!-- Formulário de Meta Tags -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-sliders-h text-danger me-2"></i> Configuração das Meta Tags (Home)</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.seo.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Meta Title (Título da Página no Google) *</label>
                        <input type="text" name="meta_title" id="seo_meta_title" class="form-control" value="{{ $seo->meta_title }}" required maxlength="255">
                        <div class="form-text small">Recomendado: entre 40 e 65 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Meta Description (Resumo no Google) *</label>
                        <textarea name="meta_description" id="seo_meta_description" rows="3" class="form-control" required maxlength="500">{{ $seo->meta_description }}</textarea>
                        <div class="form-text small">Recomendado: entre 120 e 160 caracteres.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Meta Keywords (Palavras-chave separadas por vírgula)</label>
                        <input type="text" name="meta_keywords" id="seo_meta_keywords" class="form-control" value="{{ $seo->meta_keywords }}" placeholder="rastreador, bloqueio veicular, foco 7">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Open Graph Title (WhatsApp / Facebook)</label>
                            <input type="text" name="og_title" id="seo_og_title" class="form-control" value="{{ $seo->og_title }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Indexação de Robôs (Robots)</label>
                            <select name="robots" class="form-select">
                                <option value="index, follow" {{ $seo->robots === 'index, follow' ? 'selected' : '' }}>Index, Follow (Recomendado)</option>
                                <option value="noindex, nofollow" {{ $seo->robots === 'noindex, nofollow' ? 'selected' : '' }}>NoIndex, NoFollow (Bloquear Google)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Open Graph Description (Resumo em Redes)</label>
                        <textarea name="og_description" id="seo_og_description" rows="2" class="form-control">{{ $seo->og_description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Imagem de Compartilhamento (OG Image - 1200x630)</label>
                        <input type="file" name="og_image_upload" class="form-control" accept="image/*">
                        @if(!empty($seo->og_image))
                            <small class="text-secondary d-block mt-1">Imagem atual: <a href="{{ $seo->og_image }}" target="_blank">Ver imagem configurada</a></small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
                        <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DE SEO
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Indicador de SEO Score em Tempo Real & Preview -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-line text-danger me-2"></i> SEO Score em Tempo Real</h5>
                <span id="seo-score-status" class="badge bg-success">Excelente</span>
            </div>
            <div class="card-body p-4 text-center">
                <h1 class="display-4 fw-black text-danger mb-2" id="seo-score-value">{{ $seo->seo_score ?? 85 }}/100</h1>
                <div class="progress mb-3" style="height: 12px;">
                    <div id="seo-score-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: {{ $seo->seo_score ?? 85 }}%;"></div>
                </div>
                <p class="small text-secondary mb-0">Pontuação calculada com base nas diretrizes oficiais dos algoritmos do Google para indexação.</p>
            </div>
        </div>

        <!-- Checklist de Otimização -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-tasks text-danger me-2"></i> Checklist de Requisitos SEO</h6>
            </div>
            <div class="card-body p-3" id="seo-checklist">
                <!-- Renderizado dinamicamente pelo seo-analyzer.js -->
            </div>
        </div>

        <!-- Google Snippet Preview -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="fab fa-google text-danger me-2"></i> Prévia do Google (SERP Preview)</h6>
            </div>
            <div class="card-body p-4 bg-white text-dark rounded-bottom">
                <div class="small text-success mb-1">https://foco7.com.br</div>
                <h5 class="fw-bold text-primary mb-1 text-truncate" id="preview-google-title">{{ $seo->meta_title }}</h5>
                <p class="small text-secondary mb-0" id="preview-google-desc" style="line-height: 1.4;">{{ $seo->meta_description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/seo-analyzer.js') }}"></script>
@endsection
