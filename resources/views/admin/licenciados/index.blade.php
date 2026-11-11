@extends('admin.layouts.app')

@section('title', 'Programa de Licenciados FOCO 7 & Distribuição White Label')
@section('page_title', 'Licenciados & White Label FOCO 7')
@section('page_description', 'Gerenciamento de licenças de software, distribuição do código-fonte (VPS, cPanel, Cloud), telemetria remota e instâncias WhatsApp dedicadas.')

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

    .card-kpi-licenca {
        border: none;
        border-radius: 12px;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
    }
    .card-kpi-licenca:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important;
    }

    .chave-codigo {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        letter-spacing: 0.5px;
        font-weight: 700;
        background: rgba(var(--bs-primary-rgb, 217, 0, 0), 0.06);
        color: var(--bs-primary);
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px dashed rgba(var(--bs-primary-rgb, 217, 0, 0), 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
    }

    .badge-dominio {
        font-family: monospace;
        font-size: 0.82rem;
        padding: 4px 8px;
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

    .avatar-licenciado {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        background: rgba(var(--bs-primary-rgb, 217, 0, 0), 0.1);
        color: var(--bs-primary);
        flex-shrink: 0;
    }

    .terminal-box {
        background-color: #1e1e24;
        color: #e0e0e0;
        font-family: 'SFMono-Regular', Consolas, Menlo, monospace;
        font-size: 13px;
        border-radius: 8px;
        padding: 14px 18px;
        position: relative;
        overflow-x: auto;
    }
    .terminal-box .btn-copy-code {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 11px;
    }
</style>
@endsection

@section('content')
<div class="row g-4">

    <!-- ===================================================================
         HERO HEADER: STATUS DO PROGRAMA WHITE LABEL & AÇÕES GERAIS
         =================================================================== -->
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden bg-body">
            <div class="card-body p-4 bg-body-secondary border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 64px; height: 64px; background-color: rgba(var(--bs-primary-rgb, 217, 0, 0), 0.12);">
                            <i class="fas fa-crown fs-1 text-danger"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h3 class="fw-bold mb-0 text-body">Programa de Licenciados FOCO 7</h3>
                                <span class="badge {{ ($parametros['ativo'] ?? true) ? 'bg-success' : 'bg-secondary' }} px-3 py-2 fs-6 text-uppercase shadow-xs" id="badgeStatusPrograma">
                                    <span class="{{ ($parametros['ativo'] ?? true) ? 'pulse-online' : 'pulse-offline' }} me-2" id="badgeStatusPulse"></span>
                                    <span id="badgeStatusTexto">{{ ($parametros['ativo'] ?? true) ? 'White Label Ativo no Site' : 'Programa Pausado' }}</span>
                                </span>
                                <span class="badge bg-dark text-white border border-secondary px-2 py-1 small">
                                    <i class="fas fa-code-branch text-warning me-1"></i> {{ $parametros['versao_atual'] ?? 'v3.8.0' }}
                                </span>
                                <span class="badge bg-primary text-white px-2 py-1 small">
                                    <i class="fas fa-tag me-1"></i> Adesão: {{ $parametros['preco_adesao_formatado'] ?? 'R$ 1.497,00' }}
                                </span>
                            </div>
                            <p class="text-secondary small mb-0">
                                Distribuição de código-fonte para servidores próprios dos parceiros (VPS Linux, cPanel, Cloud) sob marca 100% personalizada.
                            </p>
                        </div>
                    </div>

                    <!-- BOTÕES DE AÇÃO RÁPIDA -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="form-check form-switch me-2 mb-0">
                            <input class="form-check-input fs-5 cursor-pointer" type="checkbox" id="switchAtivoRapido" {{ ($parametros['ativo'] ?? true) ? 'checked' : '' }} onchange="toggleProgramaLicenciamento()">
                            <label class="form-check-label small fw-bold cursor-pointer" for="switchAtivoRapido">Visível no Site</label>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="abrirModalNovaLicenca()">
                            <i class="fas fa-plus-circle me-1"></i> Nova Licença Manual
                        </button>
                        <a href="#tab-instalacao" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="$('#tab-instalacao-btn').tab('show')">
                            <i class="fas fa-terminal me-1"></i> Guia VPS / Cloud
                        </a>
                        <a href="#tab-configuracoes" class="btn btn-outline-primary btn-sm fw-semibold" onclick="$('#tab-configuracoes-btn').tab('show')">
                            <i class="fas fa-sliders-h me-1"></i> Configurações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================================
         CARDS DE MÉTRICAS & KPIS GERAIS (4 COLUNAS)
         =================================================================== -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-kpi-licenca shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Total de Licenças</span>
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                        <i class="fas fa-certificate fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-body">{{ number_format($kpis['total']) }}</h3>
                    <small class="text-secondary">parceiros</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> {{ $kpis['ativas'] }} ativas</span>
                    <span class="text-warning fw-semibold">{{ $kpis['pendentes'] }} pendentes</span>
                    <span class="text-danger fw-semibold">{{ $kpis['suspensas'] }} suspensas</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-kpi-licenca shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Servidores Conectados</span>
                    <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                        <i class="fas fa-server fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['online_hoje'] ?? 0) }}</h3>
                    <small class="text-secondary">online nas últimas 24h</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary"><i class="fas fa-shield-alt me-1 text-success"></i> Telemetria Ativa</span>
                    <span class="badge bg-success-subtle text-success">Autenticação AES-256</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-kpi-licenca shadow-sm h-100 bg-body">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Downloads &amp; Instalações</span>
                    <div class="p-2 bg-info bg-opacity-10 text-info rounded-circle">
                        <i class="fas fa-cloud-arrow-down fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-body">{{ number_format($kpis['downloads']) }}</h3>
                    <small class="text-secondary">pacotes baixados</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary"><i class="fas fa-file-zipper me-1"></i> Pacote ZIP &amp; Script VPS</span>
                    <span class="badge bg-info-subtle text-info">Versão {{ $parametros['versao_atual'] ?? 'v3.8.0' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-kpi-licenca shadow-sm h-100 bg-body border-warning border-opacity-50">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold text-uppercase">Receita White Label Estimada</span>
                    <div class="p-2 bg-warning bg-opacity-15 text-warning rounded-circle">
                        <i class="fas fa-coins fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold mb-0 text-danger">R$ {{ number_format($kpis['faturamento_estimado'] ?? 0, 2, ',', '.') }}</h3>
                    <small class="text-secondary">em adesões</small>
                </div>
                <div class="d-flex justify-content-between align-items-center small mt-2 pt-2 border-top">
                    <span class="text-secondary"><i class="fas fa-tag me-1"></i> R$ {{ number_format($parametros['preco_adesao'] ?? 1497.00, 2, ',', '.') }} / licença</span>
                    <span class="badge bg-warning text-dark">{{ $kpis['ativas'] }} ativas</span>
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
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="licenciadosTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-licencas-btn" data-bs-toggle="tab" data-bs-target="#tab-licencas" type="button" role="tab">
                            <i class="fas fa-certificate text-danger me-2"></i> Licenciados &amp; Chaves de Ativação
                            <span class="badge bg-secondary ms-1">{{ $licencas->total() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-configuracoes-btn" data-bs-toggle="tab" data-bs-target="#tab-configuracoes" type="button" role="tab">
                            <i class="fas fa-sliders-h text-primary me-2"></i> Configurações do Programa White Label
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-instalacao-btn" data-bs-toggle="tab" data-bs-target="#tab-instalacao" type="button" role="tab">
                            <i class="fas fa-terminal text-success me-2"></i> Guia de Instalação (VPS / cPanel / Cloud)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-whatsapp-btn" data-bs-toggle="tab" data-bs-target="#tab-whatsapp" type="button" role="tab">
                            <i class="fab fa-whatsapp text-success me-2"></i> Instâncias WhatsApp dos Licenciados
                            <span class="badge bg-danger ms-1">R$ 60/mês</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="licenciadosTabsContent">

                    <!-- ===================================================================
                         GUIA 1: LISTAGEM DE LICENCIADOS & CHAVES
                         =================================================================== -->
                    <div class="tab-pane fade show active" id="tab-licencas" role="tabpanel">
                        
                        <!-- FILTROS & BUSCA RÁPIDA -->
                        <div class="card shadow-xs border bg-body-tertiary mb-4">
                            <div class="card-body p-3">
                                <form method="GET" action="{{ route('admin.licenciados.index') }}" class="row g-2 align-items-center">
                                    <div class="col-md-5 col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-body border-end-0"><i class="fas fa-search text-secondary"></i></span>
                                            <input type="text" name="busca" value="{{ $busca ?? '' }}" class="form-control border-start-0" placeholder="Buscar por chave FOCO7-..., empresa, titular, e-mail, domínio ou IP...">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <select name="status" class="form-select" onchange="this.form.submit()">
                                            <option value="">Todos os status</option>
                                            <option value="ativa" {{ ($status ?? '') === 'ativa' ? 'selected' : '' }}>Ativa</option>
                                            <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="suspensa" {{ ($status ?? '') === 'suspensa' ? 'selected' : '' }}>Suspensa</option>
                                            <option value="bloqueada" {{ ($status ?? '') === 'bloqueada' ? 'selected' : '' }}>Bloqueada</option>
                                            <option value="expirada" {{ ($status ?? '') === 'expirada' ? 'selected' : '' }}>Expirada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                                            <i class="fas fa-filter me-1"></i> Filtrar
                                        </button>
                                        @if(!empty($busca) || !empty($status))
                                            <a href="{{ route('admin.licenciados.index') }}" class="btn btn-outline-secondary" title="Limpar Filtros">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- TABELA DE LICENÇAS -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Licenciado / Empresa</th>
                                        <th>Chave &amp; Servidor</th>
                                        <th>Modo</th>
                                        <th>Taxa de Adesão</th>
                                        <th>Mensalidade</th>
                                        <th>Status da Licença</th>
                                        <th>Telemetria</th>
                                        <th class="text-end pe-3">Ações Rápidas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($licencas as $licenca)
                                        @php
                                            $iniciais = strtoupper(substr($licenca->nome_empresa, 0, 2));
                                            $temCheckinRecente = ($licenca->ultimo_check_in && $licenca->ultimo_check_in->isAfter(now()->subDay()));
                                        @endphp
                                        <tr id="licenca-row-{{ $licenca->id }}">
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-licenciado shadow-xs">
                                                        {{ $iniciais }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-body fs-6">{{ $licenca->nome_empresa }}</div>
                                                        <div class="small text-secondary">
                                                            <i class="fas fa-user text-muted me-1"></i> {{ $licenca->user->name ?? 'Sem Usuário Vinculado' }}
                                                            @if(!empty($licenca->user->telefone))
                                                                &bull; <a href="https://wa.me/{{ preg_replace('/\D/', '', $licenca->user->telefone) }}" target="_blank" class="text-success fw-semibold text-decoration-none" title="Conversar no WhatsApp">
                                                                    <i class="fab fa-whatsapp"></i> {{ $licenca->user->telefone }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div class="small text-muted font-monospace">{{ $licenca->user->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="chave-codigo" title="Chave Criptográfica de Ativação">
                                                        {{ $licenca->chave_licenca }}
                                                        <button type="button" class="btn btn-sm btn-link p-0 text-secondary" onclick="copiarTexto('{{ $licenca->chave_licenca }}', 'Chave de licença copiada!')" title="Copiar Chave com 1 toque">
                                                            <i class="far fa-copy"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                                <div class="mt-1">
                                                    @if(!empty($licenca->dominio_autorizado))
                                                        <a href="http://{{ $licenca->dominio_autorizado }}" target="_blank" class="badge bg-info-subtle text-info border border-info-subtle badge-dominio text-decoration-none" title="Abrir domínio autorizado">
                                                            <i class="fas fa-globe me-1"></i> {{ $licenca->dominio_autorizado }}
                                                            <i class="fas fa-external-link-alt ms-1" style="font-size: 9px;"></i>
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border small">
                                                            <i class="fas fa-link-slash me-1"></i> Auto-Binding
                                                        </span>
                                                    @endif
                                                    @if(!empty($licenca->ip_autorizado))
                                                        <span class="small text-muted font-monospace ms-1">IP: {{ $licenca->ip_autorizado }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div id="col-modo-{{ $licenca->id }}">
                                                    {!! $licenca->modo_ativacao_badge !!}
                                                    <div class="mt-1">
                                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-1 border-0 text-decoration-underline" style="font-size: 0.72rem;" onclick="toggleModoLicenca({{ $licenca->id }})" title="Alternar entre Automático e Manual">
                                                            <i class="fas fa-sync-alt me-1"></i> alternar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div id="col-adesao-{{ $licenca->id }}">
                                                    <div class="fw-bold text-success fs-6">{{ $licenca->valor_adesao_formatado }}</div>
                                                    <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                                        {!! $licenca->status_adesao_badge !!}
                                                        @if($licenca->status_adesao === 'pendente')
                                                            <button type="button" class="btn btn-danger btn-xs py-0 px-2 fw-semibold" style="font-size: 0.75rem;" onclick="cobrarAdesaoPix({{ $licenca->id }})" title="Gerar cobrança PIX para Adesão">
                                                                <i class="fab fa-pix me-1"></i> PIX
                                                            </button>
                                                            <button type="button" class="btn btn-outline-success btn-xs py-0 px-1" style="font-size: 0.75rem;" onclick="confirmarBaixaManualLicenca({{ $licenca->id }}, 'adesao')" title="Confirmar pagamento manualmente">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div id="col-mensalidade-{{ $licenca->id }}">
                                                    @if($licenca->valor_mensalidade > 0)
                                                        <div class="fw-bold text-primary fs-6">{{ $licenca->valor_mensalidade_formatado }} <small class="text-secondary fw-normal">/mês</small></div>
                                                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                                            {!! $licenca->status_mensalidade_badge !!}
                                                            @if($licenca->status_mensalidade !== 'em_dia' && $licenca->status_mensalidade !== 'isento')
                                                                <button type="button" class="btn btn-primary btn-xs py-0 px-2 fw-semibold" style="font-size: 0.75rem;" onclick="cobrarMensalidadePix({{ $licenca->id }})" title="Gerar cobrança PIX para Mensalidade">
                                                                    <i class="fab fa-pix me-1"></i> PIX
                                                                </button>
                                                                <button type="button" class="btn btn-outline-success btn-xs py-0 px-1" style="font-size: 0.75rem;" onclick="confirmarBaixaManualLicenca({{ $licenca->id }}, 'mensalidade')" title="Dar baixa manual na mensalidade">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                                            <i class="far fa-calendar-check me-1"></i> Vencimento: Dia {{ $licenca->dia_vencimento ?? 10 }}
                                                            @if($licenca->proximo_vencimento)
                                                                ({{ date('d/m/Y', strtotime($licenca->proximo_vencimento)) }})
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border">Sem Mensalidade</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div id="col-status-{{ $licenca->id }}">
                                                    <div class="d-flex align-items-center gap-2">
                                                        {!! $licenca->status_badge !!}
                                                        <button type="button" class="btn btn-sm btn-icon {{ $licenca->status === 'ativa' ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-1" onclick="toggleStatusLicenca({{ $licenca->id }})" title="{{ $licenca->status === 'ativa' ? 'Suspender Licença' : 'Ativar Licença' }}">
                                                            <i class="fas {{ $licenca->status === 'ativa' ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                    </div>
                                                    <div class="small text-muted mt-1 font-monospace" style="font-size: 0.75rem;">
                                                        {{ $licenca->tipo_licenca }} &bull; {{ $licenca->versao_liberada ?? 'v3.8.0' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    @if(!empty($licenca->ultimo_check_in))
                                                        <div class="small fw-semibold {{ $temCheckinRecente ? 'text-success' : 'text-secondary' }}">
                                                            <span class="{{ $temCheckinRecente ? 'pulse-online' : 'pulse-offline' }} me-1"></span>
                                                            {{ $licenca->ultimo_check_in->diffForHumans() }}
                                                        </div>
                                                        <div class="small text-muted font-monospace" style="font-size: 0.72rem;">
                                                            IP: {{ $licenca->dados_servidor['ultimo_ip'] ?? ($licenca->ip_autorizado ?? 'N/D') }}
                                                        </div>
                                                    @else
                                                        <span class="small text-muted"><i class="far fa-clock me-1"></i> Sem check-in</span>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="badge bg-light text-dark border small" style="font-size: 0.72rem;">
                                                            <i class="fas fa-download me-1 text-primary"></i> {{ $licenca->total_downloads ?? 0 }} dls
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="abrirModalDetalhes({{ $licenca->id }})" title="Ver Detalhes Técnicos e Telemetria">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="editarLicenca({{ $licenca->id }})" title="Editar Configurações">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li class="dropdown-header small text-uppercase fw-bold text-muted">Ações Financeiras &amp; PIX</li>
                                                            <li>
                                                                <a class="dropdown-item text-danger fw-semibold" href="javascript:void(0)" onclick="cobrarAdesaoPix({{ $licenca->id }})">
                                                                    <i class="fab fa-pix me-2"></i> Cobrar Adesão via PIX
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-primary fw-semibold" href="javascript:void(0)" onclick="cobrarMensalidadePix({{ $licenca->id }})">
                                                                    <i class="fab fa-pix me-2"></i> Cobrar Mensalidade via PIX
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-success" href="javascript:void(0)" onclick="confirmarBaixaManualLicenca({{ $licenca->id }}, 'adesao')">
                                                                    <i class="fas fa-check-double me-2"></i> Dar Baixa Manual na Adesão
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-info" href="javascript:void(0)" onclick="confirmarBaixaManualLicenca({{ $licenca->id }}, 'mensalidade')">
                                                                    <i class="fas fa-calendar-check me-2"></i> Dar Baixa Manual na Mensalidade
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li class="dropdown-header small text-uppercase fw-bold text-muted">Controle da Licença</li>
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" onclick="toggleModoLicenca({{ $licenca->id }})">
                                                                    <i class="fas fa-toggle-on me-2 text-warning"></i> Alternar Modo (Auto / Manual)
                                                                </a>
                                                            </li>
                                                            @if($licenca->status !== 'ativa')
                                                                <li>
                                                                    <a class="dropdown-item text-success" href="javascript:void(0)" onclick="alterarStatusLicenca({{ $licenca->id }}, 'ativa')">
                                                                        <i class="fas fa-check-circle me-2"></i> Ativar Licença Imediatamente
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="alterarStatusLicenca({{ $licenca->id }}, 'suspensa')">
                                                                        <i class="fas fa-pause-circle me-2"></i> Suspender Licença
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('admin.licenciados.download_pacote', $licenca->id) }}">
                                                                    <i class="fas fa-file-archive me-2 text-success"></i> Baixar Pacote (.ZIP)
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('admin.licenciados.download_vps', $licenca->id) }}">
                                                                    <i class="fas fa-terminal me-2 text-dark"></i> Baixar Script VPS (.sh)
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-primary" href="javascript:void(0)" onclick="regenerarChaveLicenca({{ $licenca->id }})">
                                                                    <i class="fas fa-sync-alt me-2"></i> Regenerar Chave
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="excluirLicenca({{ $licenca->id }})">
                                                                    <i class="fas fa-trash-alt me-2"></i> Excluir Licença
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-secondary">
                                                <i class="fas fa-certificate fa-3x mb-3 text-muted"></i>
                                                <p class="mb-1 fw-semibold">Nenhuma licença cadastrada ou encontrada com esses filtros.</p>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="abrirModalNovaLicenca()">
                                                    <i class="fas fa-plus-circle me-1"></i> Emitir Primeira Licença
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($licencas->hasPages())
                            <div class="pt-4 border-top">
                                {{ $licencas->links() }}
                            </div>
                        @endif

                    </div>

                    <!-- ===================================================================
                         GUIA 2: CONFIGURAÇÕES DO PROGRAMA WHITE LABEL
                         =================================================================== -->
                    <div class="tab-pane fade" id="tab-configuracoes" role="tabpanel">
                        <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-sliders-h text-primary me-2"></i> Parâmetros Gerais do Programa de Licenciados</h5>
                                <p class="text-secondary small mb-0">Personalize 100% dos textos, ícones, diferenciais, preços e diretrizes da seção White Label da Landing Page.</p>
                            </div>
                            <button type="button" class="btn btn-primary fw-bold shadow-sm" onclick="$('#btnSalvarConfigProgramaGeral').click()">
                                <i class="fas fa-save me-1"></i> Salvar Todas as Configurações
                            </button>
                        </div>

                        <form id="formConfiguracoesGerais" onsubmit="salvarConfiguracoesPrograma(event)">
                            <div class="row g-4">

                                <!-- BLOCO 1: CABEÇALHO DA SEÇÃO NA LANDING PAGE -->
                                <div class="col-lg-6">
                                    <div class="card shadow-xs border h-100 p-4">
                                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-bullhorn text-danger me-2"></i> Cabeçalho da Seção no Site</h6>

                                        <div class="form-check form-switch p-3 bg-body-secondary rounded-3 mb-3">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" id="licenciamento_ativo_tab" name="licenciamento_ativo" {{ ($parametros['ativo'] ?? true) ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                            <label class="form-check-label fw-bold d-block" for="licenciamento_ativo_tab">
                                                Exibir Seção "Seja um Licenciado FOCO 7" na Landing Page
                                            </label>
                                            <small class="text-secondary">Permite que empreendedores vejam a oportunidade White Label na página principal e entrem em contato.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Badge Superior da Seção</label>
                                            <input type="text" name="licenciamento_badge" class="form-control" value="{{ $parametros['badge'] ?? 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL' }}">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Título Principal da Seção</label>
                                            <input type="text" name="licenciamento_titulo" class="form-control" value="{{ $parametros['titulo'] ?? 'Tenha Sua Própria Empresa de Rastreamento Veicular' }}">
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label fw-semibold">Subtítulo Explicativo da Seção</label>
                                            <textarea name="licenciamento_subtitulo" rows="3" class="form-control">{{ $parametros['subtitulo'] ?? 'Instale o sistema FOCO 7 completo em seu próprio servidor (VPS, cPanel ou Cloud). Faturamento 100% seu, sua própria marca, logo e domínio próprio.' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOCO 2: PRECIFICAÇÃO & REGRAS TÉCNICAS -->
                                <div class="col-lg-6">
                                    <div class="card shadow-xs border h-100 p-4">
                                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-coins text-warning me-2"></i> Precificação &amp; Regras de Instalação</h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Taxa de Adesão / Implantação (R$)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                                    <input type="text" name="licenciamento_preco_adesao" class="form-control fw-bold text-success fs-5" value="{{ number_format($parametros['preco_adesao'] ?? 1497.00, 2, ',', '.') }}">
                                                </div>
                                                <div class="form-text small">Valor da taxa de adesão/licença inicial.</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Mensalidade Opcional de Suporte (R$)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-rotate"></i></span>
                                                    <input type="text" name="licenciamento_preco_mensal" class="form-control fw-bold text-primary fs-5" value="{{ number_format($parametros['preco_mensal'] ?? 0.00, 2, ',', '.') }}">
                                                </div>
                                                <div class="form-text small">Deixe 0,00 caso a licença não tenha mensalidade.</div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Versão Atual Liberada</label>
                                                <input type="text" name="licenciamento_versao_atual" class="form-control font-monospace" value="{{ $parametros['versao_atual'] ?? 'v3.8.0' }}">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">WhatsApp de Atendimento aos Licenciados</label>
                                                <input type="text" name="licenciamento_whatsapp_suporte" class="form-control" value="{{ $parametros['whatsapp_suporte'] ?? '559180510736' }}">
                                            </div>
                                        </div>

                                        <div class="form-check form-switch p-3 bg-body-secondary rounded-3 mb-2">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" id="licenciamento_permitir_auto_binding_tab" name="licenciamento_permitir_auto_binding" {{ ($parametros['permitir_auto_binding'] ?? true) ? 'checked' : '' }} style="width: 2.2em; height: 1.2em;">
                                            <label class="form-check-label fw-semibold d-block" for="licenciamento_permitir_auto_binding_tab">
                                                Vincular Domínio Automaticamente no 1º Acesso (Auto-Binding)
                                            </label>
                                            <small class="text-secondary">Ao rodar o instalador pela 1ª vez no servidor do parceiro, vincula o domínio da chamada à licença.</small>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="p-2 bg-body-secondary rounded-3">
                                                    <label class="form-label fw-bold d-block text-body mb-1 small">Modo Padrão de Licença</label>
                                                    <select name="licenciamento_modo_padrao" class="form-select form-select-sm fw-semibold">
                                                        <option value="automatica" {{ ($parametros['modo_padrao'] ?? 'automatica') === 'automatica' ? 'selected' : '' }}>⚡ Automática (Pós-PIX)</option>
                                                        <option value="manual" {{ ($parametros['modo_padrao'] ?? 'automatica') === 'manual' ? 'selected' : '' }}>👤 Manual (Aprovação Admin)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-2 bg-body-secondary rounded-3">
                                                    <label class="form-label fw-bold d-block text-body mb-1 small">Suspensão por Inadimplência</label>
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input" type="checkbox" id="licenciamento_suspensao_automatica_tab" name="licenciamento_suspensao_automatica" {{ ($parametros['suspensao_automatica'] ?? true) ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-semibold" for="licenciamento_suspensao_automatica_tab">Diária via Cron (06:45)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOCO 3: OS 4 PILARES DE BENEFÍCIOS SUPERIORES -->
                                <div class="col-12">
                                    <div class="card shadow-xs border p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="fw-bold text-body mb-1"><i class="fas fa-cubes text-danger me-2"></i> Os 4 Pilares de Benefícios Superiores (Grade de 4 Cards)</h6>
                                                <small class="text-secondary">Configure os 4 cards de benefícios que aparecem logo abaixo do cabeçalho da seção.</small>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Pilar 1 -->
                                            <div class="col-lg-3 col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100 d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-danger text-white">Pilar 01</span>
                                                        <i class="fas {{ $parametros['pilar1_icone'] ?? 'fa-server' }} text-danger fs-5"></i>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                        <input type="text" name="licenciamento_pilar1_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['pilar1_icone'] ?? 'fa-server' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_pilar1_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['pilar1_titulo'] ?? 'Servidor Próprio' }}">
                                                    </div>
                                                    <div class="mt-auto">
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_pilar1_desc" rows="3" class="form-control form-control-sm">{{ $parametros['pilar1_desc'] ?? 'Instale no seu próprio cPanel, VPS Linux (Ubuntu/Debian), Dedicated ou Cloud (AWS, DigitalOcean). O código e os dados são 100% seus.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pilar 2 -->
                                            <div class="col-lg-3 col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100 d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-danger text-white">Pilar 02</span>
                                                        <i class="fas {{ $parametros['pilar2_icone'] ?? 'fa-paint-roller' }} text-danger fs-5"></i>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                        <input type="text" name="licenciamento_pilar2_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['pilar2_icone'] ?? 'fa-paint-roller' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_pilar2_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['pilar2_titulo'] ?? '100% White Label' }}">
                                                    </div>
                                                    <div class="mt-auto">
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_pilar2_desc" rows="3" class="form-control form-control-sm">{{ $parametros['pilar2_desc'] ?? 'Sua própria marca, domínio, logotipo, cores e CNPJ. Seus clientes utilizam o sistema com a sua identidade, sem nenhuma menção à Foco 7.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pilar 3 -->
                                            <div class="col-lg-3 col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100 d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-danger text-white">Pilar 03</span>
                                                        <i class="fas {{ $parametros['pilar3_icone'] ?? 'fa-hand-holding-dollar' }} text-danger fs-5"></i>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                        <input type="text" name="licenciamento_pilar3_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['pilar3_icone'] ?? 'fa-hand-holding-dollar' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_pilar3_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['pilar3_titulo'] ?? 'Lucro 100% Seu' }}">
                                                    </div>
                                                    <div class="mt-auto">
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_pilar3_desc" rows="3" class="form-control form-control-sm">{{ $parametros['pilar3_desc'] ?? 'Receba mensalidades via Pix, Boleto e Cartão direto na sua conta (Mercado Pago ou Efí Bank). Sem cobrança por veículo rastreado.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pilar 4 -->
                                            <div class="col-lg-3 col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100 d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="badge bg-danger text-white">Pilar 04</span>
                                                        <i class="fas {{ $parametros['pilar4_icone'] ?? 'fa-magic' }} text-danger fs-5"></i>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone FontAwesome</label>
                                                        <input type="text" name="licenciamento_pilar4_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['pilar4_icone'] ?? 'fa-magic' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_pilar4_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['pilar4_titulo'] ?? 'Instalador Web Fácil' }}">
                                                    </div>
                                                    <div class="mt-auto">
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_pilar4_desc" rows="3" class="form-control form-control-sm">{{ $parametros['pilar4_desc'] ?? 'Assistente web gráfico integrado (/instalar). Criação automática de banco de dados, migrações e administrador em poucos cliques.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOCO 4: CARD DA OFERTA (LADO ESQUERDO) & 6 DIFERENCIAIS -->
                                <div class="col-12">
                                    <div class="card shadow-xs border p-4">
                                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-layer-group text-primary me-2"></i> Card da Oferta: Textos &amp; 6 Diferenciais (Lado Esquerdo)</h6>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Badge de Topo do Card</label>
                                                <input type="text" name="licenciamento_card_badge" class="form-control" value="{{ $parametros['card_badge'] ?? 'PACOTE COMPLETO DE DISTRIBUIÇÃO & WHITE LABEL' }}">
                                            </div>
                                            <div class="col-md-9">
                                                <label class="form-label small fw-bold">Título Principal do Card</label>
                                                <input type="text" name="licenciamento_card_titulo" class="form-control fw-bold" value="{{ $parametros['card_titulo'] ?? 'Software FOCO 7 Completo para o Seu Próprio Servidor' }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Descrição Completa da Oferta</label>
                                                <textarea name="licenciamento_card_desc" rows="3" class="form-control">{{ $parametros['card_desc'] ?? 'Receba o pacote completo .ZIP pronto para descompactar, script de instalação automatizada VPS (install-vps.sh) e sua chave de licença criptografada com ativação imediata. Faturamento 100% seu direto na sua conta bancária sem intermediários e sem cobrança por placa rastreada.' }}</textarea>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-secondary mb-3 small text-uppercase letter-spacing-1">
                                            <i class="fas fa-check-double text-success me-1"></i> Os 6 Diferenciais Técnicos &amp; Comerciais Inclusos no Card
                                        </h6>

                                        <div class="row g-3">
                                            <!-- Diferencial 1 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 01</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat1_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat1_icone'] ?? 'fa-server' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat1_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat1_titulo'] ?? 'Servidor & Dados Próprios' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat1_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat1_desc'] ?? 'Instale na sua VPS Ubuntu/Debian, cPanel ou Cloud com total autonomia.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Diferencial 2 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 02</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat2_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat2_icone'] ?? 'fa-paint-roller' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat2_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat2_titulo'] ?? '100% White Label' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat2_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat2_desc'] ?? 'Sua marca, logotipo, cores, domínio próprio e sem nenhuma menção à FOCO 7.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Diferencial 3 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 03</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat3_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat3_icone'] ?? 'fa-whatsapp' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat3_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat3_titulo'] ?? 'WhatsApp Evolution Go' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat3_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat3_desc'] ?? 'Envio de faturas, régua de cobrança automática e botões interativos PIX.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Diferencial 4 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 04</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat4_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat4_icone'] ?? 'fa-qrcode' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat4_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat4_titulo'] ?? 'Gateways Integrados' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat4_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat4_desc'] ?? 'Receba via PIX Copia e Cola, Mercado Pago e Efí Bank direto na sua conta.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Diferencial 5 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 05</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat5_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat5_icone'] ?? 'fa-magic' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat5_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat5_titulo'] ?? 'Instalador Gráfico /instalar' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat5_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat5_desc'] ?? 'Setup em minutos com criação automática do banco de dados e admin.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Diferencial 6 -->
                                            <div class="col-md-4">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <span class="badge bg-secondary mb-2">Item 06</span>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Ícone</label>
                                                        <input type="text" name="licenciamento_feat6_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['feat6_icone'] ?? 'fa-shield-alt' }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Título</label>
                                                        <input type="text" name="licenciamento_feat6_titulo" class="form-control form-control-sm fw-bold" value="{{ $parametros['feat6_titulo'] ?? 'Sem Royalties por Veículo' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Descrição</label>
                                                        <textarea name="licenciamento_feat6_desc" rows="2" class="form-control form-control-sm">{{ $parametros['feat6_desc'] ?? 'Cadastre clientes e veículos ilimitados retendo 100% do seu lucro.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOCO 5: BOX DE CONVERSÃO, PREÇOS, CTAS & TRUST BADGES -->
                                <div class="col-12">
                                    <div class="card shadow-xs border p-4">
                                        <h6 class="fw-bold text-body mb-3"><i class="fas fa-hand-holding-usd text-success me-2"></i> Box de Conversão, Preços &amp; Botões de Ação (Lado Direito)</h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Badge Superior do Box</label>
                                                <input type="text" name="licenciamento_cta_badge" class="form-control" value="{{ $parametros['cta_badge'] ?? 'CONDIÇÃO ESPECIAL DE DISTRIBUIÇÃO' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Label do Preço</label>
                                                <input type="text" name="licenciamento_cta_label" class="form-control" value="{{ $parametros['cta_label'] ?? 'TAXA DE ADESÃO / IMPLANTAÇÃO' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Selo Sem Royalties</label>
                                                <input type="text" name="licenciamento_cta_selo" class="form-control text-success fw-bold" value="{{ $parametros['cta_selo'] ?? 'Pagamento Único • Sem Royalties por Placa' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">Subtexto do Box</label>
                                                <input type="text" name="licenciamento_cta_subtexto" class="form-control" value="{{ $parametros['cta_subtexto'] ?? 'Ativação imediata da licença • Pacote pronto para deploy' }}">
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <!-- Botão WhatsApp (CTA Primário) -->
                                            <div class="col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <label class="form-label small fw-bold text-success mb-2">
                                                        <i class="fab fa-whatsapp me-1"></i> Botão WhatsApp de Conversão (CTA Primário)
                                                    </label>
                                                    <div class="mb-2">
                                                        <label class="form-label small">Texto do Botão</label>
                                                        <input type="text" name="licenciamento_btn_whatsapp_texto" class="form-control fw-bold" value="{{ $parametros['btn_whatsapp_texto'] ?? 'Quero Ser Licenciado Agora' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small">Mensagem Automática Pré-formatada</label>
                                                        <textarea name="licenciamento_whatsapp_msg" rows="2" class="form-control">{{ $parametros['whatsapp_msg'] ?? 'Olá! Gostaria de mais detalhes e adquirir a licença White Label do sistema FOCO 7 para instalar no meu próprio servidor.' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Botão Portal do Licenciado (CTA Secundário) -->
                                            <div class="col-md-6">
                                                <div class="p-3 bg-body-secondary rounded-3 border h-100">
                                                    <label class="form-label small fw-bold text-primary mb-2">
                                                        <i class="fas fa-user-lock me-1"></i> Botão do Portal do Licenciado (CTA Secundário)
                                                    </label>
                                                    <div class="mb-2">
                                                        <label class="form-label small">Texto do Botão</label>
                                                        <input type="text" name="licenciamento_btn_portal_texto" class="form-control fw-bold" value="{{ $parametros['btn_portal_texto'] ?? 'Já sou Licenciado / Acessar Downloads' }}">
                                                    </div>
                                                    <div>
                                                        <label class="form-label small">Link de Destino / Rota</label>
                                                        <input type="text" name="licenciamento_btn_portal_url" class="form-control font-monospace" value="{{ $parametros['btn_portal_url'] ?? '/login' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3 Trust Badges de Rodapé do Box -->
                                            <div class="col-12 mt-3">
                                                <label class="form-label small fw-bold text-secondary text-uppercase letter-spacing-1 mb-2">
                                                    <i class="fas fa-shield-alt text-warning me-1"></i> 3 Micro-Selos de Credibilidade (Rodapé do Box de Conversão)
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                                            <input type="text" name="licenciamento_trust1_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['trust1_icone'] ?? 'fa-bolt' }}" placeholder="fa-bolt" style="max-width: 110px;">
                                                            <input type="text" name="licenciamento_trust1_texto" class="form-control form-control-sm" value="{{ $parametros['trust1_texto'] ?? 'Ativação Imediata' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                                            <input type="text" name="licenciamento_trust2_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['trust2_icone'] ?? 'fa-code' }}" placeholder="fa-code" style="max-width: 110px;">
                                                            <input type="text" name="licenciamento_trust2_texto" class="form-control form-control-sm" value="{{ $parametros['trust2_texto'] ?? 'Código Homologado' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fas fa-icons"></i></span>
                                                            <input type="text" name="licenciamento_trust3_icone" class="form-control form-control-sm font-monospace" value="{{ $parametros['trust3_icone'] ?? 'fa-headset' }}" placeholder="fa-headset" style="max-width: 110px;">
                                                            <input type="text" name="licenciamento_trust3_texto" class="form-control form-control-sm" value="{{ $parametros['trust3_texto'] ?? 'Suporte Técnico' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BLOCO 6: TERMO DE LICENÇA SUMMERNOTE -->
                                <div class="col-12">
                                    <div class="card shadow-xs border p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                            <div>
                                                <h6 class="fw-bold text-body mb-1"><i class="fas fa-file-contract text-danger me-2"></i> Termo de Licença &amp; Diretrizes de Uso (Contrato White Label)</h6>
                                                <small class="text-secondary">Defina o instrumento jurídico com cláusulas de propriedade intelectual, isenção de royalties, LGPD e diretrizes de exploração comercial.</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger shadow-xs" onclick="restaurarMinutaPadraoTermos()">
                                                    <i class="fas fa-undo-alt me-1"></i> Carregar Minuta Oficial Completa
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <textarea name="licenciamento_termo_licenca" id="licenciamento_termo_licenca" rows="12" class="form-control summernote" data-height="450" placeholder="Redija os termos legais de cessão da licença do software...">{{ $parametros['termo_licenca'] ?? '' }}</textarea>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                                            <small class="text-muted"><i class="fas fa-check-circle text-success me-1"></i> Editor Summernote ativo com suporte a formatação jurídica, tabelas, destaques e modo escuro.</small>
                                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" id="btnSalvarConfigProgramaGeral">
                                                <i class="fas fa-save me-1"></i> Salvar Todas as Configurações do Programa
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <!-- ===================================================================
                         GUIA 3: GUIA DE INSTALAÇÃO & SCRIPTS (VPS / CPANEL / CLOUD)
                         =================================================================== -->
                    <div class="tab-pane fade" id="tab-instalacao" role="tabpanel">
                        <div class="mb-4 pb-3 border-bottom">
                            <h5 class="fw-bold text-body mb-1"><i class="fas fa-terminal text-success me-2"></i> Distribuição &amp; Instalação em Servidores Próprios</h5>
                            <p class="text-secondary small mb-0">Instruções para deploy rápido do ecossistema FOCO 7 em qualquer servidor Ubuntu VPS, cPanel ou Cloud Dedicado.</p>
                        </div>

                        <div class="row g-4">
                            <!-- OPÇÃO 1: VPS LINUX AUTOMATIZADO -->
                            <div class="col-lg-6">
                                <div class="card h-100 shadow-xs border p-4">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-success fs-6"><i class="fas fa-bolt me-1"></i> Método 1 (Recomendado)</span>
                                        <h5 class="fw-bold text-body mb-0">VPS Linux (Ubuntu / Debian)</h5>
                                    </div>
                                    <p class="text-secondary small">
                                        Instalação 100% automatizada com Nginx, PHP 8.3-FPM, MariaDB Server, Composer, Certbot SSL e VirtualHost pré-configurado.
                                    </p>

                                    <div class="terminal-box mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-light btn-copy-code" onclick="copiarTexto('curl -sSL {{ url('/install-vps.sh') }} | bash', 'Comando copiado!')">
                                            <i class="far fa-copy me-1"></i> Copiar
                                        </button>
                                        <span class="text-muted"># 1. Conecte ao seu servidor via SSH como root:</span><br>
                                        <span class="text-success">$</span> ssh root@seu_ip_vps<br><br>
                                        <span class="text-muted"># 2. Execute o instalador automático oficial:</span><br>
                                        <span class="text-success">$</span> curl -sSL {{ url('/install-vps.sh') }} | bash
                                    </div>

                                    <ul class="text-secondary small ps-3 mb-0">
                                        <li>Cria automaticamente o diretório <code>/var/www/foco7</code> com permissões corretas.</li>
                                        <li>Configura o Nginx com headers de segurança e reescrita de rotas.</li>
                                        <li>Basta acessar <code>http://seusite.com.br/instalar</code> para concluir pelo assistente gráfico!</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- OPÇÃO 2: CPANEL / HOSPEDAGEM COMPARTILHADA -->
                            <div class="col-lg-6">
                                <div class="card h-100 shadow-xs border p-4">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-primary fs-6"><i class="fas fa-globe me-1"></i> Método 2 (Fácil)</span>
                                        <h5 class="fw-bold text-body mb-0">Hospedagem cPanel / Apache</h5>
                                    </div>
                                    <p class="text-secondary small">
                                        Deploy convencional através do Gerenciador de Arquivos do cPanel sem necessidade de acesso ao terminal SSH.
                                    </p>

                                    <div class="p-3 bg-body-secondary rounded-3 mb-3">
                                        <div class="d-flex align-items-start gap-2 mb-2">
                                            <span class="badge bg-danger rounded-circle p-1">1</span>
                                            <div class="small">Crie um banco de dados MySQL no cPanel (ex: <code>usuario_foco7</code>).</div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2 mb-2">
                                            <span class="badge bg-danger rounded-circle p-1">2</span>
                                            <div class="small">Envie o arquivo <code>FOCO7_DISTRIBUICAO.zip</code> para a pasta <code>public_html</code> e extraia.</div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2">
                                            <span class="badge bg-danger rounded-circle p-1">3</span>
                                            <div class="small">Acesse pelo navegador: <code>https://seusite.com.br/instalar</code>.</div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm flex-fill fw-semibold" onclick="copiarTexto('https://seusite.com.br/instalar', 'Link do instalador copiado!')">
                                            <i class="fas fa-link me-1"></i> Copiar Rota /instalar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- REQUISITOS TÉCNICOS -->
                            <div class="col-12">
                                <div class="card shadow-xs border p-4">
                                    <h6 class="fw-bold text-body mb-3"><i class="fas fa-microchip text-primary me-2"></i> Requisitos Mínimos &amp; Recomendados de Servidor</h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded bg-body-tertiary">
                                                <div class="small text-secondary fw-semibold">Processador (CPU)</div>
                                                <div class="fw-bold fs-6 text-body">1 vCPU (2 vCPUs recomendado)</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded bg-body-tertiary">
                                                <div class="small text-secondary fw-semibold">Memória RAM</div>
                                                <div class="fw-bold fs-6 text-body">2 GB RAM (4 GB recomendado)</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded bg-body-tertiary">
                                                <div class="small text-secondary fw-semibold">Versão do PHP</div>
                                                <div class="fw-bold fs-6 text-body">PHP 8.2 ou 8.3 (com BCMath, GD, cURL)</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 border rounded bg-body-tertiary">
                                                <div class="small text-secondary fw-semibold">Banco de Dados</div>
                                                <div class="fw-bold fs-6 text-body">MySQL 8.0+ ou MariaDB 10.6+</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===================================================================
                         GUIA 4: INSTÂNCIAS WHATSAPP DOS LICENCIADOS (R$ 60/MÊS)
                         =================================================================== -->
                    <div class="tab-pane fade" id="tab-whatsapp" role="tabpanel">
                        <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold text-body mb-1"><i class="fab fa-whatsapp text-success me-2"></i> Instâncias WhatsApp Dedicadas dos Licenciados</h5>
                                <p class="text-secondary small mb-0">Gestão das instâncias da Evolution API Go provisionadas para parceiros, com recorrência PIX de R$ 60,00/mês.</p>
                            </div>
                            <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-outline-danger btn-sm fw-bold">
                                <i class="fab fa-whatsapp me-1"></i> Abrir Central WhatsApp
                            </a>
                        </div>

                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-info-circle fa-lg"></i>
                            <div>Cada licenciado que utiliza disparos de WhatsApp possui uma instância dedicada provisionada no cluster central. A cobrança mensal de <strong>R$ 60,00</strong> é recebida na chave PIX cadastrada, com corte/suspensão automática após o vencimento.</div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Instância Evolution API</th>
                                        <th>Licenciado Vinculado</th>
                                        <th>Mensalidade</th>
                                        <th>Vencimento</th>
                                        <th>Status Pagamento</th>
                                        <th class="text-end pe-3">Ações de Cobrança</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $temInstanciasLicenciados = false;
                                    @endphp
                                    @if(!empty($instanciasExtras) && is_array($instanciasExtras))
                                        @foreach($instanciasExtras as $extra)
                                            @if(($extra['tipo'] ?? '') === 'licenciado' || !empty($extra['licenciado_id']))
                                                @php
                                                    $temInstanciasLicenciados = true;
                                                    $st = $extra['status'] ?? 'ativa';
                                                    $venc = $extra['data_vencimento'] ?? date('Y-m-d', strtotime('+30 days'));
                                                    $isVencida = ($venc < date('Y-m-d'));
                                                    $diasRestantes = (int) round((strtotime($venc) - strtotime('today')) / 86400);
                                                @endphp
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="{{ $st === 'suspensa' ? 'pulse-offline' : ($isVencida ? 'pulse-warning' : 'pulse-online') }}"></span>
                                                            <strong class="font-monospace text-body fs-6">{{ $extra['nome'] ?? 'N/D' }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">Licenciado #{{ $extra['licenciado_id'] ?? '' }}</span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-danger">R$ {{ number_format($extra['valor_recorrente'] ?? 60.00, 2, ',', '.') }}</strong>
                                                        <small class="text-muted">/ mês</small>
                                                    </td>
                                                    <td>
                                                        <strong class="{{ $isVencida ? 'text-danger' : 'text-body' }}">
                                                            {{ date('d/m/Y', strtotime($venc)) }}
                                                        </strong>
                                                        @if($isVencida)
                                                            <span class="badge bg-danger ms-1">Vencida (há {{ abs($diasRestantes) }}d)</span>
                                                        @else
                                                            <small class="text-secondary d-block">Vence em {{ $diasRestantes }} dias</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($st === 'suspensa')
                                                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Suspensa</span>
                                                        @elseif($isVencida)
                                                            <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Aguardando PIX</span>
                                                        @else
                                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Ativa / Em Dia</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a href="{{ route('admin.whatsapp.index') }}#tab-instancias" class="btn btn-sm btn-outline-primary fw-semibold">
                                                            <i class="fab fa-pix me-1"></i> Cobrança PIX &rarr;
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if(!$temInstanciasLicenciados)
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-secondary">
                                                <i class="fab fa-whatsapp fa-2x mb-2 text-muted"></i>
                                                <p class="mb-0 small">Nenhuma instância dedicada de licenciado registrada ainda. As instâncias são criadas automaticamente ou via Central WhatsApp.</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===================================================================
     MODAL: NOVA / EDITAR LICENÇA
     =================================================================== -->
<div class="modal fade" id="modalLicenca" tabindex="-1" aria-labelledby="modalLicencaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formLicenca" onsubmit="salvarLicenca(event)">
                <input type="hidden" id="licenca_id" name="licenca_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalLicencaLabel">
                        <i class="fas fa-certificate text-danger me-2"></i> <span id="modalLicencaTitulo">Nova Licença</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Seletor de Usuário (Apenas visível ao Criar) -->
                    <div id="secaoUsuario" class="mb-3 p-3 bg-body-secondary rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0 text-body">Titular da Licença</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="modo_usuario" id="modo_existente" value="existente" checked onchange="alternarModoUsuario('existente')">
                                <label class="btn btn-outline-primary" for="modo_existente">Cliente Existente</label>

                                <input type="radio" class="btn-check" name="modo_usuario" id="modo_novo" value="novo" onchange="alternarModoUsuario('novo')">
                                <label class="btn btn-outline-primary" for="modo_novo">Novo Titular</label>
                            </div>
                        </div>

                        <div id="blocoUsuarioExistente">
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">-- Selecione o Cliente Cadastrado --</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }}) [{{ strtoupper($u->role) }}]</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="blocoUsuarioNovo" style="display: none;">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="novo_usuario_nome" id="novo_usuario_nome" class="form-control" placeholder="Nome Completo do Titular">
                                </div>
                                <div class="col-md-4">
                                    <input type="email" name="novo_usuario_email" id="novo_usuario_email" class="form-control" placeholder="E-mail de Acesso">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="novo_usuario_telefone" id="novo_usuario_telefone" class="form-control" placeholder="WhatsApp (DDD+Nº)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nome da Empresa / Marca White Label <span class="text-danger">*</span></label>
                            <input type="text" name="nome_empresa" id="nome_empresa" class="form-control" placeholder="Ex: Minha Empresa Rastreamento Veicular" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo da Licença <span class="text-danger">*</span></label>
                            <select name="tipo_licenca" id="tipo_licenca" class="form-select" required>
                                <option value="vitalicia">Vitalícia (Sem expiração)</option>
                                <option value="anual">Anual</option>
                                <option value="mensal">Mensal</option>
                                <option value="teste">Teste / Homologação</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Domínio Autorizado</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe text-secondary"></i></span>
                                <input type="text" name="dominio_autorizado" id="dominio_autorizado" class="form-control font-monospace" placeholder="Ex: rastreamento.meudominio.com.br">
                            </div>
                            <div class="form-text small">Deixe em branco para auto-binding no 1º acesso.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IP Autorizado do Servidor (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-network-wired text-secondary"></i></span>
                                <input type="text" name="ip_autorizado" id="ip_autorizado" class="form-control font-monospace" placeholder="Ex: 147.182.195.42">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Inicial <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="ativa">Ativa</option>
                                <option value="pendente">Pendente</option>
                                <option value="suspensa">Suspensa</option>
                                <option value="bloqueada">Bloqueada</option>
                                <option value="expirada">Expirada</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Versão Liberada</label>
                            <input type="text" name="versao_liberada" id="versao_liberada" class="form-control" value="v3.8.0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Data de Expiração</label>
                            <input type="date" name="data_expiracao" id="data_expiracao" class="form-control">
                        </div>

                        <!-- CONTROLE DE ATIVAÇÃO & FINANCEIRO DA LICENÇA -->
                        <div class="col-12">
                            <div class="p-3 bg-body-secondary rounded-3 border">
                                <h6 class="fw-bold text-body mb-3">
                                    <i class="fas fa-coins text-warning me-2"></i> Ativação, Adesão &amp; Mensalidade Recorrente
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Modo de Ativação / Desativação</label>
                                        <select name="modo_ativacao" id="modo_ativacao" class="form-select">
                                            <option value="automatica">⚡ Automática (Ativa e desativa conforme pagamento)</option>
                                            <option value="manual">👤 Manual (Requer intervenção do Administrador)</option>
                                        </select>
                                        <div class="form-text small">No modo automático, ativa com o PIX e suspende no vencimento.</div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Taxa de Adesão (R$)</label>
                                        <input type="text" name="valor_adesao" id="valor_adesao" class="form-control fw-bold text-success" placeholder="1.497,00">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Status da Adesão</label>
                                        <select name="status_adesao" id="status_adesao" class="form-select">
                                            <option value="pago">Pago (Aprovado)</option>
                                            <option value="pendente">Pendente (Aguardando PIX)</option>
                                            <option value="isento">Isento / Cortesia</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Mensalidade (R$)</label>
                                        <input type="text" name="valor_mensalidade" id="valor_mensalidade" class="form-control fw-bold text-primary" placeholder="0,00">
                                        <div class="form-text small">0,00 se sem mensalidade</div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Status Mensalidade</label>
                                        <select name="status_mensalidade" id="status_mensalidade" class="form-select">
                                            <option value="em_dia">Em Dia</option>
                                            <option value="pendente">Pendente</option>
                                            <option value="vencida">Vencida</option>
                                            <option value="isento">Isento</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Dia de Vencimento</label>
                                        <input type="number" name="dia_vencimento" id="dia_vencimento" class="form-control" min="1" max="31" value="10">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Próximo Vencimento</label>
                                        <input type="date" name="proximo_vencimento" id="proximo_vencimento" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Limite de Veículos</label>
                            <input type="number" name="limite_veiculos" id="limite_veiculos" class="form-control" placeholder="Deixe em branco para Ilimitado">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Limite de Clientes</label>
                            <input type="number" name="limite_clientes" id="limite_clientes" class="form-control" placeholder="Deixe em branco para Ilimitado">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações / Anotações Internas</label>
                            <textarea name="observacoes" id="observacoes" rows="2" class="form-control" placeholder="Anotações internas sobre o contrato, servidor ou personalizações..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold" id="btnSalvarLicenca">
                        <i class="fas fa-save me-1"></i> Salvar Licença
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL: DETALHES 360º DA LICENÇA
     =================================================================== -->
<div class="modal fade" id="modalDetalhesLicenca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-info-circle text-primary me-2"></i> Detalhes da Licença: <span id="detalheChaveTitulo" class="font-monospace text-danger"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="detalheLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary mb-2"></i>
                    <p class="small text-secondary mb-0">Consultando telemetria e registros...</p>
                </div>
                <div id="detalheConteudo" style="display: none;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-body-secondary rounded-3 border">
                                <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Empresa Licenciada</span>
                                <h5 class="fw-bold text-body mb-1" id="detalheEmpresa"></h5>
                                <div class="small text-secondary" id="detalheTitular"></div>
                                <div class="small text-muted font-monospace" id="detalheEmail"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-secondary rounded-3 border">
                                <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Status da Autorização</span>
                                <div id="detalheStatusBadge" class="mb-2"></div>
                                <div class="small text-secondary">
                                    Tipo: <strong id="detalheTipo" class="text-uppercase text-body"></strong> | 
                                    Versão: <strong id="detalheVersao" class="font-monospace text-body"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-body mb-2"><i class="fas fa-server text-success me-1"></i> Dados Técnicos do Servidor Remoto (Telemetria)</h6>
                    <div class="table-responsive border rounded mb-3">
                        <table class="table table-sm table-bordered mb-0 small">
                            <tbody>
                                <tr>
                                    <th class="bg-body-secondary w-25">Domínio Autorizado</th>
                                    <td id="detalheDominio" class="font-monospace"></td>
                                </tr>
                                <tr>
                                    <th class="bg-body-secondary">IP Autorizado</th>
                                    <td id="detalheIp" class="font-monospace"></td>
                                </tr>
                                <tr>
                                    <th class="bg-body-secondary">Último Check-in</th>
                                    <td id="detalheUltimoCheckin"></td>
                                </tr>
                                <tr>
                                    <th class="bg-body-secondary">Total de Downloads</th>
                                    <td id="detalheDownloads"></td>
                                </tr>
                                <tr>
                                    <th class="bg-body-secondary">Ambiente do Servidor</th>
                                    <td id="detalheDadosServidor" class="font-monospace"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light text-dark rounded border mb-0 small">
                        <strong>Arquivo <code>license.key</code>:</strong>
                        <pre class="mb-0 mt-1 font-monospace" id="detalheJsonKey" style="max-height: 120px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL: VISUALIZADOR DE COBRANÇA PIX (ADESÃO & MENSALIDADE)
     =================================================================== -->
<div class="modal fade" id="modalPixLicenca" tabindex="-1" aria-labelledby="modalPixLicencaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalPixLicencaLabel">
                    <i class="fab fa-pix me-2"></i> Cobrança PIX de Licenciado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="badge bg-light text-danger border px-3 py-2 fs-6 mb-3 font-monospace text-uppercase" id="pixLicencaTitulo">
                    COBRANÇA PIX
                </div>

                <div class="p-3 bg-body-secondary rounded-3 mb-3 d-inline-block position-relative" style="min-width: 240px; min-height: 240px;">
                    <img id="pixLicencaQrCode" src="" alt="QR Code PIX" style="width: 210px; height: 210px; object-fit: contain; display: none;" class="img-fluid rounded border bg-white p-1">
                    <div id="pixLicencaQrCodeLoading" class="position-absolute top-50 start-50 translate-middle">
                        <i class="fas fa-spinner fa-spin fa-2x text-danger"></i>
                        <div class="small text-secondary mt-1">Gerando PIX...</div>
                    </div>
                </div>

                <h2 class="fw-bold text-success mb-1" id="pixLicencaValor">R$ 0,00</h2>
                <p class="text-secondary small mb-3" id="pixLicencaDescricao">Pagamento com baixa e liberação automática via Webhook</p>

                <div class="text-start mb-3">
                    <label class="form-label small fw-bold text-secondary">Código PIX Copia e Cola:</label>
                    <div class="input-group">
                        <input type="text" id="pixLicencaCopiaCola" class="form-control font-monospace form-control-sm" readonly>
                        <button class="btn btn-danger btn-sm px-3" type="button" onclick="copiarTexto(document.getElementById('pixLicencaCopiaCola').value, 'Código PIX copiado!')">
                            <i class="far fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mt-3">
                    <a href="#" id="pixLicencaBtnWhatsapp" target="_blank" class="btn btn-success fw-semibold" style="display: none;">
                        <i class="fab fa-whatsapp me-1"></i> Enviar Cobrança no WhatsApp do Licenciado
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm fw-semibold" id="pixLicencaBtnBaixaManual" onclick="darBaixaManualDoModal()">
                        <i class="fas fa-check-double me-1"></i> Confirmar Baixa Manual (Se já pago)
                    </button>
                </div>
            </div>
            <div class="modal-footer bg-body-secondary py-2 justify-content-between">
                <small class="text-muted"><i class="fas fa-shield-alt text-success me-1"></i> Compensação Instantânea</small>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function copiarTexto(texto, msg) {
        navigator.clipboard.writeText(texto).then(() => {
            window.foco7Notify.success(msg || 'Copiado para a área de transferência!');
        });
    }

    function toggleProgramaLicenciamento() {
        $.ajax({
            url: "{{ route('admin.licenciados.toggle_programa') }}",
            type: 'POST',
            data: { _token: csrfToken },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.foco7Notify.success(res.message);
                    if (res.ativo) {
                        $('#badgeStatusPrograma').removeClass('bg-secondary').addClass('bg-success');
                        $('#badgeStatusPulse').removeClass('pulse-offline').addClass('pulse-online');
                        $('#badgeStatusTexto').text('White Label Ativo no Site');
                        $('#switchAtivoRapido').prop('checked', true);
                        $('#licenciamento_ativo_tab').prop('checked', true);
                    } else {
                        $('#badgeStatusPrograma').removeClass('bg-success').addClass('bg-secondary');
                        $('#badgeStatusPulse').removeClass('pulse-online').addClass('pulse-offline');
                        $('#badgeStatusTexto').text('Programa Pausado');
                        $('#switchAtivoRapido').prop('checked', false);
                        $('#licenciamento_ativo_tab').prop('checked', false);
                    }
                }
            },
            error: function() {
                window.foco7Notify.error('Falha ao alternar status do programa.');
            }
        });
    }

    function alternarModoUsuario(modo) {
        if (modo === 'novo') {
            document.getElementById('blocoUsuarioExistente').style.display = 'none';
            document.getElementById('blocoUsuarioNovo').style.display = 'block';
            document.getElementById('user_id').value = '';
        } else {
            document.getElementById('blocoUsuarioExistente').style.display = 'block';
            document.getElementById('blocoUsuarioNovo').style.display = 'none';
            document.getElementById('novo_usuario_nome').value = '';
            document.getElementById('novo_usuario_email').value = '';
            document.getElementById('novo_usuario_telefone').value = '';
        }
    }

    let pixModalLicencaId = null;
    let pixModalTipo = null;

    function abrirModalNovaLicenca() {
        document.getElementById('formLicenca').reset();
        document.getElementById('licenca_id').value = '';
        document.getElementById('modalLicencaTitulo').innerText = 'Nova Licença Manual';
        document.getElementById('secaoUsuario').style.display = 'block';
        alternarModoUsuario('existente');

        // Valores Padrão Financeiros
        document.getElementById('modo_ativacao').value = 'automatica';
        document.getElementById('valor_adesao').value = '{{ number_format($parametros['preco_adesao'] ?? 1497.00, 2, ',', '.') }}';
        document.getElementById('status_adesao').value = 'pago';
        document.getElementById('valor_mensalidade').value = '{{ number_format($parametros['preco_mensal'] ?? 0.00, 2, ',', '.') }}';
        document.getElementById('status_mensalidade').value = 'em_dia';
        document.getElementById('dia_vencimento').value = 10;
        document.getElementById('proximo_vencimento').value = '';

        new bootstrap.Modal(document.getElementById('modalLicenca')).show();
    }

    function editarLicenca(id) {
        Swal.fire({
            title: 'Carregando dados...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: `/admin/licenciados/${id}/detalhes`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                Swal.close();
                if (!data.success) {
                    Swal.fire('Erro', data.message || 'Falha ao carregar licença.', 'error');
                    return;
                }

                const l = data.licenca;
                document.getElementById('formLicenca').reset();
                document.getElementById('licenca_id').value = l.id;
                document.getElementById('modalLicencaTitulo').innerText = `Editar Licença: ${l.chave_licenca}`;
                document.getElementById('secaoUsuario').style.display = 'none';

                document.getElementById('nome_empresa').value = l.nome_empresa || '';
                document.getElementById('tipo_licenca').value = l.tipo_licenca || 'vitalicia';
                document.getElementById('dominio_autorizado').value = l.dominio_autorizado || '';
                document.getElementById('ip_autorizado').value = l.ip_autorizado || '';
                document.getElementById('status').value = l.status || 'ativa';
                document.getElementById('versao_liberada').value = l.versao_liberada || 'v3.8.0';
                document.getElementById('limite_veiculos').value = l.limite_veiculos || '';
                document.getElementById('limite_clientes').value = l.limite_clientes || '';
                document.getElementById('observacoes').value = l.observacoes || '';

                // Campos Financeiros & Modo
                const financeiro = l.dados_servidor?.financeiro || {};
                document.getElementById('modo_ativacao').value = l.modo_ativacao || financeiro.modo_ativacao || 'automatica';
                document.getElementById('valor_adesao').value = l.valor_adesao_formatado ? l.valor_adesao_formatado.replace('R$ ', '') : (l.valor_adesao ? Number(l.valor_adesao).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '1.497,00');
                document.getElementById('status_adesao').value = l.status_adesao || financeiro.status_adesao || 'pago';
                document.getElementById('valor_mensalidade').value = l.valor_mensalidade_formatado ? l.valor_mensalidade_formatado.replace('R$ ', '') : (l.valor_mensalidade ? Number(l.valor_mensalidade).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '0,00');
                document.getElementById('status_mensalidade').value = l.status_mensalidade || financeiro.status_mensalidade || 'em_dia';
                document.getElementById('dia_vencimento').value = l.dia_vencimento || financeiro.dia_vencimento || 10;
                document.getElementById('proximo_vencimento').value = l.proximo_vencimento ? l.proximo_vencimento.substring(0, 10) : '';

                if (l.data_expiracao) {
                    document.getElementById('data_expiracao').value = l.data_expiracao.substring(0, 10);
                } else {
                    document.getElementById('data_expiracao').value = '';
                }

                new bootstrap.Modal(document.getElementById('modalLicenca')).show();
            },
            error: function() {
                Swal.close();
                Swal.fire('Erro', 'Não foi possível se comunicar com o servidor.', 'error');
            }
        });
    }

    function abrirModalDetalhes(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetalhesLicenca'));
        modal.show();

        $('#detalheLoading').show();
        $('#detalheConteudo').hide();

        $.ajax({
            url: `/admin/licenciados/${id}/detalhes`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#detalheLoading').hide();
                if (!data.success) {
                    modal.hide();
                    window.foco7Notify.error('Falha ao obter detalhes da licença.');
                    return;
                }

                const l = data.licenca;
                $('#detalheChaveTitulo').text(l.chave_licenca);
                $('#detalheEmpresa').text(l.nome_empresa || 'Sem nome');
                $('#detalheTitular').html(`<i class="fas fa-user text-muted me-1"></i> ${l.user ? l.user.name : 'N/D'}`);
                $('#detalheEmail').text(l.user ? l.user.email : '');
                $('#detalheStatusBadge').html(l.status_badge || l.status);
                $('#detalheTipo').text(l.tipo_licenca || 'vitalicia');
                $('#detalheVersao').text(l.versao_liberada || 'v3.8.0');
                $('#detalheDominio').text(l.dominio_autorizado || 'Aguardando vinculação');
                $('#detalheIp').text(l.ip_autorizado || 'Qualquer IP permitido');
                $('#detalheUltimoCheckin').text(l.ultimo_check_in ? l.ultimo_check_in : 'Nenhum check-in registrado');
                $('#detalheDownloads').text(`${l.total_downloads || 0} downloads realizados`);

                const dados = l.dados_servidor || {};
                $('#detalheDadosServidor').text(JSON.stringify(dados, null, 2));

                const licenseKeyJson = {
                    "licenca": l.chave_licenca,
                    "empresa": l.nome_empresa,
                    "dominio": l.dominio_autorizado || "auto-bind",
                    "status": l.status,
                    "versao": l.versao_liberada
                };
                $('#detalheJsonKey').text(JSON.stringify(licenseKeyJson, null, 2));

                $('#detalheConteudo').show();
            },
            error: function() {
                modal.hide();
                window.foco7Notify.error('Erro na consulta de detalhes.');
            }
        });
    }

    function salvarLicenca(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarLicenca');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

        const form = document.getElementById('formLicenca');
        const formData = new FormData(form);

        $.ajax({
            url: "{{ route('admin.licenciados.salvar') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Licença';

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalLicenca')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        confirmButtonColor: '#d90000'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Atenção', data.message || 'Erro ao salvar licença.', 'warning');
                }
            },
            error: function(xhr) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Licença';
                const msg = xhr.responseJSON?.message || 'Ocorreu uma falha ao processar a requisição.';
                Swal.fire('Erro', msg, 'error');
            }
        });
    }

    function toggleStatusLicenca(id) {
        window.confirmarAcao(
            'Alternar Status da Licença?',
            'A licença será ativada ou suspensa imediatamente, sincronizando o bloqueio no servidor remoto do parceiro.',
            'question',
            function() {
                $.ajax({
                    url: `/admin/licenciados/${id}/toggle-status`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.foco7Notify.success(data.message);
                            setTimeout(() => location.reload(), 700);
                        } else {
                            window.foco7Notify.error(data.message || 'Falha ao alternar status.');
                        }
                    },
                    error: function() {
                        window.foco7Notify.error('Erro de comunicação com o servidor.');
                    }
                });
            }
        );
    }

    function toggleModoLicenca(id) {
        $.ajax({
            url: `/admin/licenciados/${id}/toggle-modo`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    window.foco7Notify.success(data.message);
                    $(`#col-modo-${id}`).html(`
                        ${data.modo_badge}
                        <div class="mt-1">
                            <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-1 border-0 text-decoration-underline" style="font-size: 0.72rem;" onclick="toggleModoLicenca(${id})" title="Alternar entre Automático e Manual">
                                <i class="fas fa-sync-alt me-1"></i> alternar
                            </button>
                        </div>
                    `);
                } else {
                    window.foco7Notify.error(data.message || 'Erro ao alternar modo.');
                }
            },
            error: function() {
                window.foco7Notify.error('Falha ao alternar modo de ativação.');
            }
        });
    }

    function cobrarAdesaoPix(id) {
        pixModalLicencaId = id;
        pixModalTipo = 'adesao';

        const modalEl = document.getElementById('modalPixLicenca');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        $('#pixLicencaTitulo').text('TAXA DE ADESÃO / IMPLANTAÇÃO');
        $('#pixLicencaQrCode').hide();
        $('#pixLicencaQrCodeLoading').show();
        $('#pixLicencaValor').text('Carregando...');
        $('#pixLicencaCopiaCola').val('');
        $('#pixLicencaBtnWhatsapp').hide();

        $.ajax({
            url: `/admin/licenciados/${id}/pix-adesao`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                $('#pixLicencaQrCodeLoading').hide();
                if (!data.success) {
                    modal.hide();
                    Swal.fire('Atenção', data.message || 'Não foi possível gerar a cobrança PIX.', 'warning');
                    return;
                }

                $('#pixLicencaValor').text(`R$ ${Number(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}`);
                $('#pixLicencaDescricao').text(`Adesão White Label: ${data.empresa || 'Licenciado'} (Licença: ${data.chave})`);
                $('#pixLicencaCopiaCola').val(data.qr_code || '');

                let qrSrc = '';
                if (data.qr_code_base64) {
                    qrSrc = 'data:image/png;base64,' + data.qr_code_base64;
                } else if (data.qr_code) {
                    qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(data.qr_code);
                }
                $('#pixLicencaQrCode').attr('src', qrSrc).show();

                if (data.telefone_whatsapp && data.qr_code) {
                    const cleanPhone = data.telefone_whatsapp.replace(/\D/g, '');
                    const textoWpp = encodeURIComponent(`Olá, segue a cobrança PIX da taxa de adesão da sua licença White Label FOCO 7 no valor de R$ ${Number(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}.\n\nCódigo Copia e Cola:\n${data.qr_code}\n\nA ativação ocorre automaticamente em poucos segundos após o pagamento!`);
                    $('#pixLicencaBtnWhatsapp').attr('href', `https://wa.me/${cleanPhone}?text=${textoWpp}`).show();
                }
            },
            error: function() {
                $('#pixLicencaQrCodeLoading').hide();
                modal.hide();
                window.foco7Notify.error('Falha ao comunicar com a gateway de pagamentos.');
            }
        });
    }

    function cobrarMensalidadePix(id) {
        pixModalLicencaId = id;
        pixModalTipo = 'mensalidade';

        const modalEl = document.getElementById('modalPixLicenca');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        $('#pixLicencaTitulo').text('MENSALIDADE DA LICENÇA WHITE LABEL');
        $('#pixLicencaQrCode').hide();
        $('#pixLicencaQrCodeLoading').show();
        $('#pixLicencaValor').text('Carregando...');
        $('#pixLicencaCopiaCola').val('');
        $('#pixLicencaBtnWhatsapp').hide();

        $.ajax({
            url: `/admin/licenciados/${id}/pix-mensalidade`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                $('#pixLicencaQrCodeLoading').hide();
                if (!data.success) {
                    modal.hide();
                    Swal.fire('Atenção', data.message || 'Não foi possível gerar a cobrança PIX.', 'warning');
                    return;
                }

                $('#pixLicencaValor').text(`R$ ${Number(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}`);
                $('#pixLicencaDescricao').text(`Mensalidade de Suporte White Label (Vencimento +30 dias)`);
                $('#pixLicencaCopiaCola').val(data.qr_code || '');

                let qrSrc = '';
                if (data.qr_code_base64) {
                    qrSrc = 'data:image/png;base64,' + data.qr_code_base64;
                } else if (data.qr_code) {
                    qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(data.qr_code);
                }
                $('#pixLicencaQrCode').attr('src', qrSrc).show();

                if (data.telefone_whatsapp && data.qr_code) {
                    const cleanPhone = data.telefone_whatsapp.replace(/\D/g, '');
                    const textoWpp = encodeURIComponent(`Olá, segue a cobrança PIX da mensalidade da sua licença White Label FOCO 7 no valor de R$ ${Number(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2})}.\n\nCódigo Copia e Cola:\n${data.qr_code}\n\nAo pagar, sua licença e instâncias permanecem 100% ativas automaticamente!`);
                    $('#pixLicencaBtnWhatsapp').attr('href', `https://wa.me/${cleanPhone}?text=${textoWpp}`).show();
                }
            },
            error: function() {
                $('#pixLicencaQrCodeLoading').hide();
                modal.hide();
                window.foco7Notify.error('Falha ao comunicar com a gateway de pagamentos.');
            }
        });
    }

    function confirmarBaixaManualLicenca(id, tipo) {
        const rotulo = (tipo === 'mensalidade') ? 'Mensalidade de Suporte' : 'Taxa de Adesão';
        window.confirmarAcao(
            `Dar Baixa Manual na ${rotulo}?`,
            `Esta ação confirmará o pagamento manual e ${tipo === 'mensalidade' ? 'estenderá a vigência por +30 dias' : 'ativará a licença imediatamente'}.`,
            'question',
            function() {
                $.ajax({
                    url: `/admin/licenciados/${id}/confirmar-pagamento`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: JSON.stringify({ tipo: tipo }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            const modalEl = document.getElementById('modalPixLicenca');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                            window.foco7Notify.success(data.message);
                            setTimeout(() => location.reload(), 700);
                        } else {
                            window.foco7Notify.error(data.message || 'Falha ao confirmar pagamento.');
                        }
                    },
                    error: function() {
                        window.foco7Notify.error('Erro de comunicação com o servidor.');
                    }
                });
            }
        );
    }

    function darBaixaManualDoModal() {
        if (pixModalLicencaId && pixModalTipo) {
            confirmarBaixaManualLicenca(pixModalLicencaId, pixModalTipo);
        }
    }

    function alterarStatusLicenca(id, novoStatus) {
        const labels = {
            'ativa': 'Ativar',
            'suspensa': 'Suspender',
            'bloqueada': 'Bloquear'
        };

        window.confirmarAcao(
            `Deseja ${labels[novoStatus] || 'alterar'} esta licença?`,
            `O servidor remoto do licenciado responderá ao status ${novoStatus.toUpperCase()} na próxima verificação de integridade.`,
            novoStatus === 'ativa' ? 'question' : 'warning',
            function() {
                $.ajax({
                    url: `/admin/licenciados/${id}/status`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: JSON.stringify({ status: novoStatus }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.foco7Notify.success(data.message);
                            setTimeout(() => location.reload(), 800);
                        } else {
                            window.foco7Notify.error(data.message);
                        }
                    },
                    error: function() {
                        window.foco7Notify.error('Falha ao alterar status da licença.');
                    }
                });
            }
        );
    }

    function regenerarChaveLicenca(id) {
        window.confirmarAcao(
            'Regenerar Chave de Licença?',
            'ATENÇÃO: A chave atual será invalidada imediatamente. O licenciado precisará atualizar o arquivo license.key em seu servidor remoto.',
            'warning',
            function() {
                $.ajax({
                    url: `/admin/licenciados/${id}/regenerar-chave`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Chave Regenerada com Sucesso!',
                                html: `<div class="p-3 my-2 bg-light border rounded"><span class="small text-secondary d-block">Nova Chave Criptográfica:</span><strong class="font-monospace text-danger fs-5">${data.nova_chave}</strong></div><p class="small text-muted mb-0">Envie a nova chave ao parceiro para substituição em seu servidor.</p>`,
                                confirmButtonColor: '#d90000'
                            }).then(() => location.reload());
                        } else {
                            window.foco7Notify.error(data.message);
                        }
                    },
                    error: function() {
                        window.foco7Notify.error('Falha ao regenerar chave da licença.');
                    }
                });
            }
        );
    }

    function restaurarMinutaPadraoTermos() {
        Swal.fire({
            title: 'Carregar Minuta Oficial?',
            text: 'Isso carregará no editor o Contrato Oficial de Licenciamento White Label da FOCO 7 com todas as 8 cláusulas completas.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Sim, Carregar Minuta',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const minuta = `{!! addslashes(\App\Services\LicenciamentoService::obterMinutaPadraoContrato()) !!}`;
                const $termo = $('#licenciamento_termo_licenca');
                if ($termo.length) {
                    if ($.fn.summernote && $termo.next('.note-editor').length) {
                        $termo.summernote('code', minuta);
                    } else {
                        $termo.val(minuta);
                    }
                    window.foco7Notify.success('Minuta oficial completa inserida no editor!');
                }
            }
        });
    }

    function salvarConfiguracoesPrograma(e) {
        e.preventDefault();

        // Sincroniza o editor Summernote antes de capturar o FormData
        if ($.fn.summernote) {
            const $termo = $('#licenciamento_termo_licenca');
            if ($termo.length && $termo.next('.note-editor').length) {
                $termo.val($termo.summernote('code'));
            }
        }

        const btn = document.getElementById('btnSalvarConfigProgramaGeral');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Gravando...';
        }

        const form = document.getElementById('formConfiguracoesGerais');
        const formData = new FormData(form);

        $.ajax({
            url: "{{ route('admin.licenciados.configuracoes') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Configurações do Programa';
                }

                if (data.success) {
                    window.foco7Notify.success(data.message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Configurações Gravadas!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    Swal.fire('Atenção', data.message || 'Erro ao salvar.', 'warning');
                }
            },
            error: function(xhr) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Configurações do Programa';
                }
                const msg = xhr.responseJSON?.message || 'Falha ao salvar configurações.';
                window.foco7Notify.error(msg);
            }
        });
    }

    function excluirLicenca(id) {
        window.confirmarAcao(
            'Excluir Licença?',
            'Esta ação é permanente e não poderá ser desfeita. O servidor remoto do licenciado perderá a autorização de operação.',
            'warning',
            function() {
                $.ajax({
                    url: `/admin/licenciados/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.foco7Notify.success(data.message);
                            setTimeout(() => location.reload(), 800);
                        } else {
                            window.foco7Notify.error(data.message);
                        }
                    },
                    error: function() {
                        window.foco7Notify.error('Falha ao excluir licença.');
                    }
                });
            }
        );
    }

    // Inicialização e Renderização Fluida do Summernote na Aba de Configurações
    $(function() {
        if (window.location.hash) {
            const hashTab = $(`[data-bs-target="${window.location.hash}"], [href="${window.location.hash}"]`);
            if (hashTab.length) {
                hashTab.tab('show');
            }
        }

        $('button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).attr('data-bs-target') || $(e.target).attr('href');
            if (target === '#tab-configuracoes' || target === '#tab-configuracoes-btn') {
                if (typeof window.initSummernote === 'function') {
                    window.initSummernote('#tab-configuracoes');
                }
            }
        });
    });
</script>
@endsection
