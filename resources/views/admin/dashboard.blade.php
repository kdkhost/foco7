@extends('admin.layouts.app')

@section('title', 'Painel de Controle & Analítico')
@section('page_title')
    <span class="d-inline-flex align-items-center gap-2">
        <i class="fas fa-tachometer-alt text-danger"></i>
        <span>Painel de Controle &amp; Analítico</span>
    </span>
@endsection
@section('page_description', 'Visão Geral e métricas em tempo real: clientes, faturamento, agendamentos e tráfego da Foco 7.')

@section('page_actions')
<div class="d-inline-flex flex-wrap align-items-center justify-content-sm-end gap-2">
    <div class="badge bg-body border px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm rounded-pill" id="badgeRelogioTempoReal" title="Horário Oficial de Brasília / São Paulo em Tempo Real" data-server-ts="{{ now()->setTimezone('America/Sao_Paulo')->getTimestamp() * 1000 }}">
        <span class="spinner-grow spinner-grow-sm text-danger" style="width: 7px; height: 7px;" role="status"></span>
        <i class="far fa-calendar-alt text-danger opacity-75"></i>
        <span id="dataAdminDashboard" class="fw-semibold text-body">{{ now()->setTimezone('America/Sao_Paulo')->format('d/m/Y') }}</span>
        <span class="text-secondary opacity-50">&bull;</span>
        <i class="far fa-clock text-danger opacity-75"></i>
        <span id="relogioAdminDashboard" class="font-monospace fw-bold text-danger fs-6">{{ now()->setTimezone('America/Sao_Paulo')->format('H:i:s') }}</span>
    </div>

    <!-- Botões de Ação Rápida -->
    <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-sm btn-outline-danger fw-semibold shadow-sm">
        <i class="fas fa-calendar-plus me-1"></i> Novo Agendamento
    </a>
    <a href="{{ route('admin.faturas.index') }}" class="btn btn-sm btn-outline-success fw-semibold shadow-sm">
        <i class="fas fa-file-invoice-dollar me-1"></i> Lançar Fatura
    </a>
    <a href="{{ route('site.home') }}" target="_blank" class="btn btn-sm btn-danger fw-bold shadow-sm px-3">
        <i class="fas fa-external-link-alt me-1"></i> Ver Site
    </a>
</div>
@endsection

@push('styles')
<style>
    /* Estilos dos KPIs animados e cartões analíticos */
    .kpi-counter {
        display: inline-block;
        font-variant-numeric: tabular-nums;
        transition: color 0.3s ease;
    }
    .chart-container-card {
        position: relative;
        min-height: 280px;
    }
    .device-badge-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
</style>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">

        <!-- ALERTA DE MODO MANUTENÇÃO CASO ATIVO -->
        @if($modoManutencao)
            <div class="card border border-warning border-opacity-25 bg-warning bg-opacity-10 shadow-sm mb-4 rounded-3 p-3" role="alert">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fas fa-tools fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-body">O Site está atualmente em Modo de Manutenção</h6>
                            <p class="text-secondary small mb-0">Visitantes comuns estão sendo direcionados para a tela de manutenção. Apenas IPs autorizados e administradores têm acesso público.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.manutencao.index') }}" class="btn btn-sm btn-warning text-dark fw-bold px-3">
                        <i class="fas fa-cog me-1"></i> Gerenciar Manutenção
                    </a>
                </div>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- LINHA 1: KPIS PRINCIPAIS ANIMADOS (SMALL BOXES OFICIAIS ADMINLTE 4)      -->
        <!-- ========================================================================= -->
        <div class="row g-3 mb-4">
            <!-- 1. Clientes Ativos -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="small-box text-bg-danger shadow-sm">
                    <div class="inner">
                        <h3><span class="kpi-counter" data-target="{{ $totalClientes }}">0</span></h3>
                        <p>Clientes Ativos</p>
                    </div>
                    <i class="small-box-icon fas fa-users"></i>
                    <a href="{{ route('admin.clientes.index') }}" class="small-box-footer link-light">
                        Ver Clientes <i class="fas fa-arrow-circle-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Receita Este Mês -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="small-box text-bg-success shadow-sm">
                    <div class="inner">
                        <h3>R$ <span class="kpi-counter" data-target="{{ $faturasPagasMes }}" data-is-money="true">0,00</span></h3>
                        <p>Receita Este Mês</p>
                    </div>
                    <i class="small-box-icon fas fa-dollar-sign"></i>
                    <a href="{{ route('admin.faturas.index', ['status' => 'pago']) }}" class="small-box-footer link-light">
                        Ver Recebimentos <i class="fas fa-arrow-circle-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Instalações & Agendamentos -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="small-box text-bg-primary shadow-sm">
                    <div class="inner">
                        <h3><span class="kpi-counter" data-target="{{ $agendamentosPendentes }}">0</span></h3>
                        <p>Instalações Pendentes</p>
                    </div>
                    <i class="small-box-icon fas fa-calendar-alt"></i>
                    <a href="{{ route('admin.agendamentos.index') }}" class="small-box-footer link-light">
                        Agenda Completa <i class="fas fa-arrow-circle-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Visitantes Únicos Hoje -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="small-box text-bg-info shadow-sm">
                    <div class="inner">
                        <h3><span class="kpi-counter" data-target="{{ $analytics['unicos_hoje'] ?? 0 }}">0</span></h3>
                        <p>Visitantes Únicos Hoje</p>
                    </div>
                    <i class="small-box-icon fas fa-chart-line"></i>
                    <a href="#secaoAnaliticoCompleto" class="small-box-footer link-light">
                        Ver Analítico <i class="fas fa-arrow-circle-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- LINHA 2: KPIS COMPLEMENTARES (INFO BOXES OFICIAIS ADMINLTE 4)             -->
        <!-- ========================================================================= -->
        <div class="row g-3 mb-4">
            <!-- Total de Visitas no Mês -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon text-bg-danger shadow-sm"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary small text-uppercase fw-semibold">Visitas no Mês</span>
                        <span class="info-box-number fs-4 fw-bold text-body">
                            <span class="kpi-counter" data-target="{{ $analytics['visitas_mes'] ?? 0 }}">0</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Faturas Pendentes -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon text-bg-warning shadow-sm"><i class="fas fa-file-invoice-dollar text-dark"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary small text-uppercase fw-semibold">Faturas Pendentes</span>
                        <span class="info-box-number fs-4 fw-bold text-body">
                            <span class="kpi-counter" data-target="{{ $faturasPendentes }}">0</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notificações Não Lidas -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon text-bg-danger shadow-sm"><i class="fas fa-bell"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary small text-uppercase fw-semibold">Alertas &amp; Mensagens</span>
                        <span class="info-box-number fs-4 fw-bold text-body">
                            <span class="kpi-counter" data-target="{{ $notificacoesPendentes }}">0</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Taxa de Conversão -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="info-box shadow-sm border-0">
                    <span class="info-box-icon text-bg-success shadow-sm"><i class="fas fa-bolt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary small text-uppercase fw-semibold">Taxa de Conversão</span>
                        <span class="info-box-number fs-4 fw-bold text-body">
                            <span class="kpi-counter" data-target="{{ $analytics['taxa_conversao'] ?? 0 }}" data-is-percent="true">0</span>%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SEÇÃO ANALÍTICA COMPLETA: GRÁFICOS INTERATIVOS CHART.JS                   -->
        <!-- ========================================================================= -->
        <div class="row g-4 mb-4" id="secaoAnaliticoCompleto">
            <!-- Gráfico de Visitas dos Últimos Dias -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-body">
                            <i class="fas fa-chart-area text-danger me-2"></i> Analítico de Tráfego &bull; Últimos 7 Dias
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 small">
                                <i class="fas fa-eye me-1"></i> {{ $analytics['visitas_hoje'] ?? 0 }} visualizações hoje
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart-container-card" style="height: 290px;">
                            <canvas id="graficoVisitasDias"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Dispositivos (Mobile vs Desktop) & Páginas Populares -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h6 class="fw-bold mb-0 text-body">
                            <i class="fas fa-mobile-screen-button text-primary me-2"></i> Dispositivos dos Visitantes
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div style="height: 180px; position: relative;">
                            <canvas id="graficoDispositivos"></canvas>
                        </div>
                        <div class="d-flex justify-content-around text-center mt-3 pt-2 border-top">
                            <div>
                                <span class="device-badge-dot bg-danger"></span>
                                <span class="small fw-semibold text-secondary">Mobile</span>
                                <h6 class="fw-bold mb-0">{{ $analytics['mobile_pct'] ?? 55 }}%</h6>
                            </div>
                            <div>
                                <span class="device-badge-dot bg-primary"></span>
                                <span class="small fw-semibold text-secondary">Desktop</span>
                                <h6 class="fw-bold mb-0">{{ $analytics['desktop_pct'] ?? 40 }}%</h6>
                            </div>
                            <div>
                                <span class="device-badge-dot bg-success"></span>
                                <span class="small fw-semibold text-secondary">Tablet</span>
                                <h6 class="fw-bold mb-0">{{ $analytics['tablet_pct'] ?? 5 }}%</h6>
                            </div>
                        </div>

                        <!-- Top Rotas / Páginas -->
                        <div class="mt-3 pt-2 border-top">
                            <h6 class="small fw-bold text-uppercase text-secondary mb-2">Páginas Mais Acessadas:</h6>
                            <ul class="list-unstyled mb-0 small">
                                @forelse($analytics['paginas_populares'] ?? [] as $pag)
                                    <li class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-body text-truncate" style="max-width: 75%;">
                                            <i class="fas fa-link text-danger me-1"></i> {{ $pag->rota ?: '/' }}
                                        </span>
                                        <span class="badge bg-body-secondary text-body fw-bold">{{ $pag->total }}</span>
                                    </li>
                                @empty
                                    <li class="text-secondary small">Nenhum dado registrado ainda.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- PRÓXIMAS INSTALAÇÕES & SUPORTES (COM OPÇÃO DE CANCELAMENTO VIA SWEETALERT2) -->
        <!-- ========================================================================= -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
                        <h5 class="fw-bold mb-0 text-body">
                            <i class="fas fa-calendar-check text-danger me-2"></i> Próximas Instalações &amp; Suportes
                        </h5>
                        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-sm btn-outline-danger">
                            Agenda Completa <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente / Veículo</th>
                                        <th>Serviço</th>
                                        <th>Data &amp; Horário</th>
                                        <th>Status</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proximosAgendamentos as $ag)
                                    <tr id="linha-agendamento-{{ $ag->id }}">
                                        <td>
                                            <div class="fw-bold text-body">{{ $ag->cliente_nome }}</div>
                                            <small class="text-secondary">{{ $ag->veiculo_modelo ?? 'Veículo' }} &bull; {{ $ag->cliente_telefone }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ag->categoria === 'estetica' ? 'bg-dark' : 'bg-primary' }}">
                                                {{ $ag->tipo_servico }}
                                            </span>
                                        </td>
                                        <td class="small text-body">{{ $ag->data_hora_inicio->format('d/m/Y H:i') }}</td>
                                        <td id="status-agendamento-{{ $ag->id }}">{!! $ag->status_badge !!}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-danger" onclick="cancelarAgendamentoDashboard({{ $ag->id }}, '{{ addslashes($ag->cliente_nome) }}')" title="Cancelar este agendamento">
                                                    <i class="fas fa-ban me-1"></i> Cancelar
                                                </button>
                                                <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-outline-secondary" title="Ver no Calendário">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">
                                            <i class="far fa-calendar-times fs-3 d-block mb-2 text-muted"></i>
                                            Nenhum agendamento futuro pendente.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- ÚLTIMAS FATURAS & PARCELAS (COM OPÇÃO DE ANTECIPAÇÃO RÁPIDA)               -->
            <!-- ========================================================================= -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <h5 class="fw-bold mb-0 text-body">
                                <i class="fas fa-file-invoice-dollar text-success me-2"></i> Faturas &amp; Mensalidades
                            </h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger">{{ $faturasPendentes }} Pendentes</span>
                            <a href="{{ route('admin.faturas.index') }}" class="btn btn-sm btn-outline-secondary">
                                Ver Todas <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código / Cliente</th>
                                        <th>Descrição / Parcela</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th class="text-end">Antecipação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimasFaturas as $fat)
                                    <tr id="linha-fatura-{{ $fat->id }}">
                                        <td>
                                            <div class="fw-bold text-body">{{ $fat->codigo_fatura }}</div>
                                            <small class="text-secondary">{{ $fat->user->name ?? 'Cliente' }}</small>
                                        </td>
                                        <td>
                                            <span class="small text-body">{{ $fat->descricao }}</span>
                                            @if($fat->numero_parcela && $fat->total_parcelas)
                                                <span class="badge bg-info text-dark ms-1" style="font-size: 0.7rem;">
                                                    Parcela {{ $fat->numero_parcela }}/{{ $fat->total_parcelas }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-success">{{ $fat->valor_formatado }}</td>
                                        <td id="status-fatura-{{ $fat->id }}">{!! $fat->status_badge !!}</td>
                                        <td class="text-end">
                                            @if($fat->status !== 'pago')
                                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.8rem;" onclick="abrirModalAntecipar({{ $fat->id }}, '{{ $fat->codigo_fatura }}', '{{ addslashes($fat->user->name ?? 'Cliente') }}', {{ $fat->valor }}, {{ $fat->numero_parcela ?? 1 }})" title="Antecipar e liquidar esta parcela com desconto opcional">
                                                    <i class="fas fa-hand-holding-usd me-1"></i> Antecipar
                                                </button>
                                            @else
                                                <span class="badge bg-success bg-opacity-25 text-success small py-1 px-2">Quitada</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">
                                            Nenhuma fatura registrada no momento.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ========================================================================= -->
<!-- MODAL DE ANTECIPAÇÃO DE PARCELA COM DESCONTO CONFIGURÁVEL                  -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalAnteciparParcela" tabindex="-1" aria-labelledby="modalAnteciparParcelaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="modalAnteciparParcelaLabel">
                    <i class="fas fa-hand-holding-usd me-2"></i> Antecipação de Parcela
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formAnteciparParcela" onsubmit="confirmarAntecipacao(event)">
                @csrf
                <input type="hidden" id="anteciparFaturaId" name="fatura_id">
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 bg-body-secondary border mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary small">Código da Fatura:</span>
                            <span class="fw-bold text-body" id="anteciparCodigo">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-secondary small">Cliente:</span>
                            <span class="fw-semibold text-body" id="anteciparCliente">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary small">Valor Original:</span>
                            <span class="fw-bold fs-5 text-success" id="anteciparValorOriginal">R$ 0,00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-body">Desconto por Antecipação (R$)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-secondary">R$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="anteciparDesconto" name="desconto" value="0.00" oninput="calcularTotalAntecipado()">
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Defina o abatimento concedido ao cliente para liquidação imediata.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-body">Método de Quitação</label>
                        <select class="form-select" id="anteciparMetodo" name="metodo_pagamento">
                            <option value="pix" selected>PIX Instantâneo</option>
                            <option value="dinheiro">Dinheiro em Espécie</option>
                            <option value="cartao">Cartão de Crédito / Débito</option>
                            <option value="transferencia">Transferência Bancária</option>
                            <option value="boleto">Boleto Quitado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-body">Observações da Baixa</label>
                        <input type="text" class="form-control" id="anteciparObs" name="observacao" placeholder="Ex: Antecipação solicitada pelo cliente via WhatsApp">
                    </div>

                    <!-- Resumo Final -->
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success">Total a Liquidar:</span>
                        <span class="fw-bold fs-4 text-success" id="anteciparValorFinal">R$ 0,00</span>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold" id="btnConfirmarAntecipacao">
                        <i class="fas fa-check-circle me-1"></i> Confirmar &amp; Liquidar Parcela
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js Oficial CDN para os Gráficos Analíticos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // =========================================================================
    // RELÓGIO & DATA EM TEMPO REAL DE ALTA PRECISÃO (PADRÃO BRASILEIRO)
    // =========================================================================
    // RELÓGIO & DATA EM TEMPO REAL SINCRONIZADO COM SÃO PAULO / BRASÍLIA (UTC-3)
    // =========================================================================
    const badgeRelogio = document.getElementById('badgeRelogioTempoReal');
    let dashServerTsBase = badgeRelogio ? (parseInt(badgeRelogio.getAttribute('data-server-ts')) || Date.now()) : Date.now();
    let dashLocalTsBase = Date.now();
    let dashServerOffsetMs = dashServerTsBase - dashLocalTsBase;

    const dashFmtData = new Intl.DateTimeFormat('pt-BR', {
        timeZone: 'America/Sao_Paulo',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    const dashFmtHora = new Intl.DateTimeFormat('pt-BR', {
        timeZone: 'America/Sao_Paulo',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
    const dashFmtSemana = new Intl.DateTimeFormat('pt-BR', {
        timeZone: 'America/Sao_Paulo',
        weekday: 'long'
    });

    function atualizarRelogioTempoReal() {
        const agoraSp = new Date(Date.now() + dashServerOffsetMs);
        const elData = document.getElementById('dataAdminDashboard');
        const elRelogio = document.getElementById('relogioAdminDashboard');

        if (elData) {
            const dataStr = dashFmtData.format(agoraSp);
            let diaSemana = dashFmtSemana.format(agoraSp);
            diaSemana = diaSemana.charAt(0).toUpperCase() + diaSemana.slice(1);
            elData.textContent = dataStr;
            elData.parentElement.setAttribute('title', `${diaSemana}, ${dataStr} - Horário Oficial de Brasília / São Paulo`);
        }
        if (elRelogio) {
            elRelogio.textContent = dashFmtHora.format(agoraSp);
        }
    }
    atualizarRelogioTempoReal();
    setInterval(atualizarRelogioTempoReal, 1000);

    // =========================================================================
    // ANIMAÇÃO DE NÚMEROS SUAVE (COUNTUP ANIMADO) PARA OS KPIS
    // =========================================================================
    function animarContadores() {
        $('.kpi-counter').each(function () {
            const $this = $(this);
            const targetVal = parseFloat($this.data('target')) || 0;
            const isMoney = $this.data('is-money') === true;
            const isPercent = $this.data('is-percent') === true;

            $({ countNum: 0 }).animate({ countNum: targetVal }, {
                duration: 1200,
                easing: 'swing',
                step: function () {
                    if (isMoney) {
                        $this.text(this.countNum.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    } else if (isPercent) {
                        $this.text(this.countNum.toFixed(1));
                    } else {
                        $this.text(Math.floor(this.countNum).toLocaleString('pt-BR'));
                    }
                },
                complete: function () {
                    if (isMoney) {
                        $this.text(targetVal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    } else if (isPercent) {
                        $this.text(targetVal.toFixed(1));
                    } else {
                        $this.text(Math.floor(targetVal).toLocaleString('pt-BR'));
                    }
                }
            });
        });
    }

    // =========================================================================
    // INICIALIZAÇÃO DOS GRÁFICOS ANALÍTICOS COM CHART.JS
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function () {
        animarContadores();

        // 1. Gráfico de Visitas (Últimos 7 Dias)
        const ctxVisitas = document.getElementById('graficoVisitasDias');
        if (ctxVisitas) {
            new Chart(ctxVisitas, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['dias_labels'] ?? ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom']) !!},
                    datasets: [
                        {
                            label: 'Total de Visualizações',
                            data: {!! json_encode($analytics['visitas_dias_data'] ?? [12, 19, 24, 32, 28, 45, 52]) !!},
                            borderColor: '#d90000',
                            backgroundColor: 'rgba(217, 0, 0, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#d90000',
                            pointRadius: 4,
                        },
                        {
                            label: 'Visitantes Únicos',
                            data: {!! json_encode($analytics['unicos_dias_data'] ?? [8, 14, 18, 22, 20, 31, 38]) !!},
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            borderDash: [4, 4],
                            pointBackgroundColor: '#0d6efd',
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }

        // 2. Gráfico de Dispositivos (Doughnut)
        const ctxDisp = document.getElementById('graficoDispositivos');
        if (ctxDisp) {
            new Chart(ctxDisp, {
                type: 'doughnut',
                data: {
                    labels: ['Mobile', 'Desktop', 'Tablet'],
                    datasets: [{
                        data: [
                            {{ $analytics['mobile_pct'] ?? 55 }},
                            {{ $analytics['desktop_pct'] ?? 40 }},
                            {{ $analytics['tablet_pct'] ?? 5 }}
                        ],
                        backgroundColor: ['#d90000', '#0d6efd', '#198754'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '70%'
                }
            });
        }
    });

    // =========================================================================
    // CANCELAMENTO DE AGENDAMENTO VIA SWEETALERT2
    // =========================================================================
    function cancelarAgendamentoDashboard(id, clienteNome) {
        Swal.fire({
            title: 'Cancelar Agendamento?',
            text: `Deseja realmente cancelar o agendamento de "${clienteNome}"? O horário será liberado no calendário.`,
            input: 'text',
            inputPlaceholder: 'Motivo do cancelamento (opcional)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, Cancelar Agendamento',
            cancelButtonText: 'Manter Agendado'
        }).then((result) => {
            if (result.isConfirmed) {
                const motivo = result.value || 'Cancelado pelo administrador via Dashboard';
                $.ajax({
                    url: `/admin/agendamentos/cancelar/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        motivo: motivo
                    },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $(`#status-agendamento-${id}`).html('<span class="badge bg-danger">Cancelado</span>');
                            $(`#linha-agendamento-${id} button`).prop('disabled', true);
                            setTimeout(() => {
                                $(`#linha-agendamento-${id}`).fadeOut(350, function() { $(this).remove(); });
                            }, 1200);
                        }
                    },
                    error: function () {
                        toastr.error('Erro ao cancelar o agendamento.');
                    }
                });
            }
        });
    }

    // =========================================================================
    // ANTECIPAÇÃO DE PARCELA (MODAL & LIQUIDAÇÃO COM DESCONTO)
    // =========================================================================
    let faturaValorOriginalAtual = 0;

    function abrirModalAntecipar(id, codigo, cliente, valor, parcela) {
        faturaValorOriginalAtual = valor;
        $('#anteciparFaturaId').val(id);
        $('#anteciparCodigo').text(`${codigo} (Parc. ${parcela})`);
        $('#anteciparCliente').text(cliente);
        $('#anteciparValorOriginal').text(`R$ ${valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
        $('#anteciparDesconto').val('0.00');
        $('#anteciparObs').val(`Antecipação da parcela ${parcela}`);
        calcularTotalAntecipado();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAnteciparParcela'));
        modal.show();
    }

    function calcularTotalAntecipado() {
        const desconto = parseFloat($('#anteciparDesconto').val()) || 0;
        const final = Math.max(0, faturaValorOriginalAtual - desconto);
        $('#anteciparValorFinal').text(`R$ ${final.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
    }

    function confirmarAntecipacao(e) {
        e.preventDefault();
        const id = $('#anteciparFaturaId').val();
        const $btn = $('#btnConfirmarAntecipacao');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Liquidando...');

        $.ajax({
            url: `/admin/faturas/antecipar/${id}`,
            type: 'POST',
            data: $('#formAnteciparParcela').serialize(),
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Confirmar & Liquidar Parcela');
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAnteciparParcela')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Parcela Liquidada!',
                        text: res.message,
                        confirmButtonColor: '#28a745'
                    });
                    $(`#status-fatura-${id}`).html('<span class="badge bg-success">Pago</span>');
                    $(`#linha-fatura-${id} button`).replaceWith('<span class="badge bg-success bg-opacity-25 text-success small py-1 px-2">Quitada</span>');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Confirmar & Liquidar Parcela');
                toastr.error(xhr.responseJSON?.message || 'Erro ao processar antecipação.');
            }
        });
    }
</script>
@endsection
