@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Visão Geral & Métricas')
@section('page_description', 'Acompanhamento em tempo real de clientes, faturas, agendamentos e status da plataforma.')

@section('content')
<div class="row g-4 mb-4">
    <!-- Card Total de Clientes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Clientes Ativos</h6>
                    <h3 class="fw-bold mb-0">{{ $totalClientes }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Faturas Pagas no Mês -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Receita Este Mês</h6>
                    <h3 class="fw-bold mb-0">R$ {{ number_format($faturasPagasMes, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Agendamentos Pendentes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Instalações Pendentes</h6>
                    <h3 class="fw-bold mb-0">{{ $agendamentosPendentes }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Modo Manutenção -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="{{ $modoManutencao ? 'bg-danger' : 'bg-primary' }} text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-tools"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Status do Site</h6>
                    <h5 class="fw-bold mb-0">{{ $modoManutencao ? 'EM MANUTENÇÃO' : 'ONLINE' }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Próximos Agendamentos -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-danger me-2"></i> Próximas Instalações & Suportes</h5>
                <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-sm btn-outline-danger">Ver Agenda Completa</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente / Veículo</th>
                                <th>Serviço</th>
                                <th>Data & Horário</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proximosAgendamentos as $ag)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $ag->cliente_nome }}</div>
                                    <small class="text-secondary">{{ $ag->veiculo_modelo ?? 'Veículo' }} ({{ $ag->cliente_telefone }})</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $ag->tipo_servico }}</span></td>
                                <td>{{ $ag->data_hora_inicio->format('d/m/Y H:i') }}</td>
                                <td>{!! $ag->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">Nenhum agendamento futuro encontrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Faturas Geradas -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-danger me-2"></i> Últimas Faturas Registradas</h5>
                <span class="badge bg-danger">{{ $faturasPendentes }} Pendentes</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código / Cliente</th>
                                <th>Plano</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasFaturas as $fat)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $fat->codigo_fatura }}</div>
                                    <small class="text-secondary">{{ $fat->user->name ?? 'Cliente' }}</small>
                                </td>
                                <td>{{ $fat->plano->nome ?? $fat->descricao }}</td>
                                <td class="fw-bold text-success">{{ $fat->valor_formatado }}</td>
                                <td>{!! $fat->status_badge !!}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-secondary">Nenhuma fatura registrada no momento.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
