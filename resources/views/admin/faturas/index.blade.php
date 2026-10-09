@extends('admin.layouts.app')

@section('title', 'Gestão Financeira & Faturas')
@section('page_title', 'Faturas & Mensalidades')
@section('page_description', 'Gerencie faturas de clientes, emita cobranças avulsas, altere status de quitação e vencimentos.')

@section('page_actions')
<button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalFatura()">
    <i class="fas fa-file-invoice-dollar me-1"></i> Lançar Nova Fatura
</button>
@endsection

@section('content')
<!-- CARDS DE MÉTRICAS -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Faturas</h6>
                    <h3 class="fw-bold mb-0">{{ $totalFaturas }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Quitadas / Pagas</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ $totalPagas }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Pendentes</h6>
                    <h3 class="fw-bold mb-0 text-danger">{{ $totalPendentes }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Receita Este Mês</h6>
                    <h4 class="fw-bold mb-0 text-success">R$ {{ number_format($valorRecebidoMes, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS & BUSCA -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.faturas.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por código, cliente ou descrição..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os Status</option>
                    <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendentes</option>
                    <option value="pago" {{ request('status') === 'pago' ? 'selected' : '' }}>Pagas</option>
                    <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Canceladas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill fw-bold">Filtrar</button>
                @if(request('busca') || request('status'))
                    <a href="{{ route('admin.faturas.index') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- TABELA DE FATURAS -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Descrição / Plano</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faturas as $fat)
                    <tr>
                        <td><span class="fw-bold font-monospace">{{ $fat->codigo_fatura }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $fat->user->name ?? 'Cliente Removido' }}</div>
                            <small class="text-secondary">{{ $fat->user->telefone ?? '' }}</small>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;">{{ $fat->descricao }}</div>
                            @if($fat->plano)
                                <small class="badge bg-secondary-subtle text-secondary border">{{ $fat->plano->nome }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $fat->data_vencimento ? $fat->data_vencimento->format('d/m/Y') : '-' }}</span>
                            @if($fat->status === 'pendente' && $fat->data_vencimento && $fat->data_vencimento->isPast())
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-block mt-1" style="width: fit-content;">Vencida</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success fs-6">{{ $fat->valor_formatado }}</td>
                        <td>
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-sm dropdown-toggle border-0 p-0" type="button" data-bs-toggle="dropdown">
                                    {!! $fat->status_badge !!}
                                </button>
                                <ul class="dropdown-menu shadow">
                                    <li><h6 class="dropdown-header">Alterar Status</h6></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'pago')"><i class="fas fa-check-circle text-success me-2"></i> Marcar como Paga</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'pendente')"><i class="fas fa-clock text-warning me-2"></i> Marcar como Pendente</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'cancelado')"><i class="fas fa-ban text-danger me-2"></i> Marcar como Cancelada</a></li>
                                </ul>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarFatura({{ $fat->id }})" title="Editar Fatura">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirFatura({{ $fat->id }})" title="Excluir">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">Nenhuma fatura encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($faturas->hasPages())
    <div class="card-footer bg-transparent border-top py-3">
        {{ $faturas->links() }}
    </div>
    @endif
</div>

<!-- MODAL CADASTRAR / EDITAR FATURA -->
<div class="modal fade" id="modalFatura" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalFaturaTitulo">Lançar Nova Fatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.faturas.salvar') }}" method="POST" class="ajax-form" id="formFatura">
                @csrf
                <input type="hidden" name="fatura_id" id="fatura_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cliente Vinculado *</label>
                            <select name="user_id" id="fat_user_id" class="form-select" required>
                                <option value="">Selecione um cliente...</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->telefone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Plano Associado (Opcional)</label>
                            <select name="plano_id" id="fat_plano_id" class="form-select">
                                <option value="">Sem plano específico / Cobrança avulsa</option>
                                @foreach($planos as $p)
                                    <option value="{{ $p->id }}" data-preco="{{ $p->preco }}">{{ $p->nome }} ({{ $p->preco_formatado }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Descrição da Fatura / Mensalidade *</label>
                            <input type="text" name="descricao" id="fat_descricao" class="form-control" required placeholder="Ex: Mensalidade Rastreamento Veicular - Outubro/2026">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Valor (R$) *</label>
                            <input type="text" name="valor" id="fat_valor" class="form-control mask-dinheiro" required placeholder="R$ 69,90">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Data de Vencimento *</label>
                            <input type="date" name="data_vencimento" id="fat_vencimento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status Inicial *</label>
                            <select name="status" id="fat_status" class="form-select" required>
                                <option value="pendente">Pendente</option>
                                <option value="pago">Quitado / Pago</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar Fatura</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalFatura() {
        $('#fatura_id').val('');
        $('#formFatura')[0].reset();
        $('#fat_status').val('pendente');
        $('#fat_vencimento').val('{{ date('Y-m-d', strtotime('+5 days')) }}');
        $('#modalFaturaTitulo').text('Lançar Nova Fatura');
        new bootstrap.Modal(document.getElementById('modalFatura')).show();
    }

    $('#fat_plano_id').on('change', function() {
        var preco = $(this).find(':selected').data('preco');
        if (preco && !$('#fatura_id').val()) {
            $('#fat_valor').val('R$ ' + parseFloat(preco).toFixed(2).replace('.', ','));
            var planoNome = $(this).find(':selected').text().split('(')[0].trim();
            $('#fat_descricao').val('Mensalidade Foco 7 - ' + planoNome);
        }
    });

    function editarFatura(id) {
        $.getJSON('/admin/faturas/obter/' + id, function(res) {
            if (res.success && res.fatura) {
                var f = res.fatura;
                $('#fatura_id').val(f.id);
                $('#fat_user_id').val(f.user_id);
                $('#fat_plano_id').val(f.plano_id);
                $('#fat_descricao').val(f.descricao);
                $('#fat_valor').val('R$ ' + res.valor_formatado);
                $('#fat_vencimento').val(res.vencimento_iso);
                $('#fat_status').val(f.status);

                $('#modalFaturaTitulo').text('Editar Fatura ' + f.codigo_fatura);
                new bootstrap.Modal(document.getElementById('modalFatura')).show();
            }
        });
    }

    function alterarStatusFatura(id, status) {
        $.post('/admin/faturas/status/' + id, {
            _token: '{{ csrf_token() }}',
            status: status
        }, function(res) {
            if (res.success) {
                window.foco7Notify.success(res.message);
                setTimeout(function() { location.reload(); }, 600);
            }
        });
    }

    function excluirFatura(id) {
        window.foco7Confirm('Excluir Fatura?', 'Tem certeza que deseja remover esta fatura do sistema financeiro?', function() {
            $.ajax({
                url: '/admin/faturas/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { location.reload(); }, 600);
                    }
                }
            });
        });
    }
</script>
@endsection
