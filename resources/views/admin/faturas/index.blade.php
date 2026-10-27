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
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header w-100 bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title fw-bold mb-0 text-body">
                <i class="fas fa-file-invoice-dollar text-danger me-2"></i> Histórico de Faturas & Mensalidades
            </h5>
            <span class="badge bg-body-secondary text-body border px-2 py-1 small">
                {{ $faturas->total() }} registros
            </span>
        </div>
        <div class="card-tools ms-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalFatura()">
                <i class="fas fa-file-invoice-dollar me-1"></i> Lançar Nova Fatura
            </button>
        </div>
    </div>
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
                        <td>
                            <span class="fw-bold font-monospace">{{ $fat->codigo_fatura }}</span>
                            @if($fat->isCarne())
                                <br><span class="badge bg-primary-subtle text-primary border border-primary-subtle small mt-1" title="Carnê: {{ $fat->codigo_carne ?? $fat->codigo_fatura }}">
                                    <i class="fas fa-book me-1"></i> Parcela {{ $fat->numero_parcela ?? 1 }}/{{ $fat->total_parcelas ?? 1 }}
                                </span>
                            @endif
                        </td>
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
                            @if($fat->status === 'pago')
                                <div class="dropdown d-inline-block">
                                    <button class="status-badge-btn status-badge-pago dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Fatura Paga (Auditoria Contábil Ativa)">
                                        <span class="status-dot"></span>
                                        <i class="fas fa-check-circle"></i>
                                        <span>Pago</span>
                                        <i class="fas fa-chevron-down badge-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                        <li><h6 class="dropdown-header"><i class="fas fa-shield-alt text-success me-1"></i> Auditoria Contábil</h6></li>
                                        <li><span class="dropdown-item-text small text-muted">Status quitado não pode ser alterado diretamente.</span></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger fw-bold" href="javascript:void(0)" onclick="estornarFatura({{ $fat->id }}, '{{ $fat->codigo_fatura }}', '{{ addslashes($fat->user->name ?? 'Cliente') }}', {{ $fat->valor }})">
                                                <i class="fas fa-undo me-2"></i> Estornar Fatura Manualmente
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($fat->status === 'estornado')
                                <div class="dropdown d-inline-block">
                                    <button class="status-badge-btn status-badge-estornado dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Fatura Estornada (Auditoria Contábil)">
                                        <span class="status-dot"></span>
                                        <i class="fas fa-undo"></i>
                                        <span>Estornado</span>
                                        <i class="fas fa-chevron-down badge-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                        <li><h6 class="dropdown-header"><i class="fas fa-shield-alt text-dark me-1"></i> Auditoria Contábil</h6></li>
                                        <li><span class="dropdown-item-text small text-muted">Fatura estornada e deduzida da receita contábil.</span></li>
                                    </ul>
                                </div>
                            @else
                                <div class="dropdown d-inline-block">
                                    <button class="status-badge-btn {{ $fat->status_badge_class }} dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Alterar status da fatura">
                                        <span class="status-dot"></span>
                                        <i class="fas {{ $fat->status_badge_icon }}"></i>
                                        <span>{{ $fat->status_badge_label }}</span>
                                        <i class="fas fa-chevron-down badge-caret"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                                        <li><h6 class="dropdown-header">Alterar Status</h6></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'pago')"><i class="fas fa-check-circle text-success me-2"></i> Marcar como Paga</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'pendente')"><i class="fas fa-clock text-warning me-2"></i> Marcar como Pendente</a></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="alterarStatusFatura({{ $fat->id }}, 'cancelado')"><i class="fas fa-ban text-danger me-2"></i> Marcar como Cancelada</a></li>
                                        @if($fat->isCarne())
                                            <li><hr class="dropdown-divider"></li>
                                            <li><h6 class="dropdown-header"><i class="fas fa-book me-1"></i> Gestão do Carnê</h6></li>
                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="iniciarCancelamentoFatura({{ $fat->id }})"><i class="fas fa-ban text-danger me-2"></i> Opções de Cancelamento...</a></li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                @if($fat->status === 'pago')
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="estornarFatura({{ $fat->id }}, '{{ $fat->codigo_fatura }}', '{{ addslashes($fat->user->name ?? 'Cliente') }}', {{ $fat->valor }})" title="Estornar Fatura (Recalcula Receita)">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @elseif($fat->status !== 'estornado')
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="abrirModalAnteciparFatura({{ $fat->id }}, '{{ $fat->codigo_fatura }}', '{{ addslashes($fat->user->name ?? 'Cliente') }}', {{ $fat->valor }}, {{ $fat->numero_parcela ?? 1 }})" title="Antecipar / Quitar com Desconto">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarFatura({{ $fat->id }})" title="Editar Fatura">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($fat->isCarne())
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirFatura({{ $fat->id }})" title="Excluir Parcela / Opções do Carnê">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" title="Opções de exclusão do carnê"></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li><h6 class="dropdown-header"><i class="fas fa-book text-danger me-1"></i> Exclusão do Carnê</h6></li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="excluirFatura({{ $fat->id }})">
                                                    <i class="fas fa-trash text-secondary me-2"></i> Excluir somente esta parcela
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger fw-bold" href="javascript:void(0)" onclick="confirmarExclusaoDiretaTodasCarne({{ $fat->id }}, '{{ $fat->codigo_fatura }}')">
                                                    <i class="fas fa-dumpster-fire text-danger me-2"></i> Deletar TODAS de uma única vez
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirFatura({{ $fat->id }})" title="Excluir">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
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

<!-- MODAL DE ANTECIPAÇÃO DE PARCELA -->
<div class="modal fade" id="modalAnteciparParcela" tabindex="-1" aria-labelledby="modalAnteciparParcelaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="modalAnteciparParcelaLabel">
                    <i class="fas fa-hand-holding-usd me-2"></i> Antecipação de Parcela
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formAnteciparParcela" onsubmit="confirmarAntecipacaoFatura(event)">
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
                            <input type="number" step="0.01" min="0" class="form-control" id="anteciparDesconto" name="desconto" value="0.00" oninput="calcularTotalAntecipadoFatura()">
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

                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success">Total a Liquidar:</span>
                        <span class="fw-bold fs-4 text-success" id="anteciparValorFinal">R$ 0,00</span>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm" id="btnConfirmarAntecipacao">
                        <i class="fas fa-check-circle me-1"></i> Confirmar &amp; Liquidar Parcela
                    </button>
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
        if (status === 'cancelado') {
            iniciarCancelamentoFatura(id);
            return;
        }

        const statusTexto = status === 'pago' ? 'como Paga (Quitada)' : 'como Pendente';
        Swal.fire({
            title: 'Alterar Status?',
            text: `Deseja realmente marcar esta fatura ${statusTexto}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'pago' ? '#28a745' : '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, alterar',
            cancelButtonText: 'Cancelar'
        }).then((resConfirm) => {
            if (resConfirm.isConfirmed) {
                $.post('/admin/faturas/status/' + id, {
                    _token: '{{ csrf_token() }}',
                    status: status
                }, function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { location.reload(); }, 600);
                    }
                }).fail(function(xhr) {
                    window.foco7Notify.error(xhr.responseJSON?.message || 'Falha ao alterar status da fatura.');
                });
            }
        });
    }

    /**
     * Lógica de Cancelamento:
     * - Fatura avulsa: confirmação padrão.
     * - Carnê:
     *   - Se houver alguma paga: pergunta se deseja cancelar somente essa ou a sequência ainda pendente.
     *   - Se NÃO houver nenhuma paga: pergunta se quer cancelar somente essa ou o carnê / todas as parcelas.
     */
    function iniciarCancelamentoFatura(id) {
        Swal.fire({
            title: 'Consultando carnê...',
            text: 'Verificando dados contábeis e status das parcelas...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.getJSON('/admin/faturas/carne-info/' + id, function(res) {
            Swal.close();

            if (!res.success) {
                window.foco7Notify.error('Não foi possível obter dados da fatura.');
                return;
            }

            const fat = res.fatura;
            const carne = res.carne;

            if (fat.is_paga) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fatura Já Quitada',
                    text: 'Faturas quitadas não podem ser canceladas diretamente. Para anular a receita, utilize a função formal de "Estornar Fatura".',
                    confirmButtonColor: '#d90000'
                });
                return;
            }

            // Fatura comum avulsa (não é carnê)
            if (!carne || !carne.is_carne) {
                Swal.fire({
                    title: 'Cancelar Fatura?',
                    html: `Deseja realmente cancelar a fatura <strong>${fat.codigo_fatura}</strong> (${fat.valor_formatado}) do cliente <strong>${fat.cliente_nome}</strong>?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d90000',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-ban me-1"></i> Sim, Cancelar Fatura',
                    cancelButtonText: 'Manter Fatura'
                }).then((r) => {
                    if (r.isConfirmed) {
                        executarCancelamentoFatura(id, 'somente_esta');
                    }
                });
                return;
            }

            // É CARNÊ: avalia se há parcelas pagas
            if (carne.tem_pagas) {
                // HÁ parcelas já pagas -> Pergunta se cancela somente essa ou a sequência ainda pendente
                Swal.fire({
                    title: 'Cancelar Parcela do Carnê',
                    html: `
                        <div class="text-start mb-2">
                            <div class="alert alert-info py-2 px-3 small mb-3">
                                <i class="fas fa-info-circle me-1"></i> Parcela <strong>#${carne.numero_parcela}/${carne.total_parcelas}</strong> do carnê <code>${carne.codigo_carne}</code>.<br>
                                <span class="badge bg-success text-white mt-1 me-1"><i class="fas fa-check-circle me-1"></i> ${carne.total_pagas} parcela(s) já paga(s)</span>
                                <span class="badge bg-warning text-dark mt-1"><i class="fas fa-clock me-1"></i> ${carne.total_pendentes} pendente(s)</span>
                            </div>
                            <p class="mb-2 fw-semibold text-body">Existem parcelas já pagas neste carnê. Como você deseja cancelar?</p>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-danger text-start p-3 rounded-3 shadow-sm" onclick="executarCancelamentoFatura(${id}, 'somente_esta')">
                                    <div class="fw-bold fs-6"><i class="fas fa-file-invoice me-2"></i> Cancelar SOMENTE esta parcela (#${carne.numero_parcela})</div>
                                    <div class="small text-muted ms-4">Cancela apenas a parcela ${fat.codigo_fatura}. As demais parcelas continuam ativas normalmente.</div>
                                </button>
                                <button type="button" class="btn btn-danger text-start p-3 rounded-3 shadow-sm text-white" onclick="executarCancelamentoFatura(${id}, 'sequencia_pendente')">
                                    <div class="fw-bold fs-6"><i class="fas fa-forward me-2"></i> Cancelar esta e a SEQUÊNCIA ainda pendente (${carne.total_sequencia} parcelas)</div>
                                    <div class="small text-white-50 ms-4">Cancela a parcela atual (#${carne.numero_parcela}) e todas as seguintes pendentes até o final do carnê. As parcelas já quitadas permanecem salvas.</div>
                                </button>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Voltar / Fechar Sem Alterar',
                    cancelButtonColor: '#6c757d',
                    width: '620px'
                });
            } else {
                // NÃO HÁ nenhuma paga -> Pergunta se quer cancelar somente esta ou o carnê / todas as parcelas
                Swal.fire({
                    title: 'Cancelar Fatura / Carnê Completo',
                    html: `
                        <div class="text-start mb-2">
                            <div class="alert alert-warning py-2 px-3 small mb-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> Parcela <strong>#${carne.numero_parcela}/${carne.total_parcelas}</strong> do carnê <code>${carne.codigo_carne}</code>.<br>
                                <span class="badge bg-secondary text-white mt-1"><i class="fas fa-info-circle me-1"></i> Nenhuma parcela deste carnê foi paga até o momento</span>
                            </div>
                            <p class="mb-2 fw-semibold text-body">Como nenhuma parcela foi paga ainda, você pode cancelar somente esta parcela ou cancelar o carnê inteiro:</p>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-danger text-start p-3 rounded-3 shadow-sm" onclick="executarCancelamentoFatura(${id}, 'somente_esta')">
                                    <div class="fw-bold fs-6"><i class="fas fa-file-invoice me-2"></i> Cancelar SOMENTE esta parcela (#${carne.numero_parcela})</div>
                                    <div class="small text-muted ms-4">Cancela apenas a parcela individual selecionada. As demais ${carne.total_geral - 1} parcelas continuam abertas.</div>
                                </button>
                                <button type="button" class="btn btn-danger text-start p-3 rounded-3 shadow-sm text-white" onclick="executarCancelamentoFatura(${id}, 'todas_parcelas')">
                                    <div class="fw-bold fs-6"><i class="fas fa-ban me-2"></i> Cancelar o CARNÊ COMPLETO / TODAS as parcelas (${carne.total_geral} parcelas)</div>
                                    <div class="small text-white-50 ms-4">Cancela integralmente todas as ${carne.total_geral} parcelas do carnê de uma única vez.</div>
                                </button>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-times me-1"></i> Voltar / Fechar Sem Alterar',
                    cancelButtonColor: '#6c757d',
                    width: '620px'
                });
            }
        }).fail(function() {
            Swal.close();
            window.foco7Notify.error('Falha ao comunicar com o servidor.');
        });
    }

    function executarCancelamentoFatura(id, escopo) {
        Swal.close();
        $.ajax({
            url: '/admin/faturas/cancelar-carne/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                escopo: escopo
            },
            success: function(res) {
                if (res.success) {
                    window.foco7Notify.success(res.message);
                    setTimeout(function() { location.reload(); }, 600);
                }
            },
            error: function(xhr) {
                window.foco7Notify.error(xhr.responseJSON?.message || 'Falha ao cancelar fatura.');
            }
        });
    }

    /**
     * Lógica de Exclusão de Faturas & Carnê:
     * - Fatura avulsa: confirmação padrão.
     * - Carnê: oferece opção de excluir somente esta parcela, a sequência pendente, ou DELETAR TODAS DE UMA ÚNICA VEZ.
     */
    function excluirFatura(id) {
        Swal.fire({
            title: 'Consultando fatura...',
            text: 'Verificando vínculos do carnê...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.getJSON('/admin/faturas/carne-info/' + id, function(res) {
            Swal.close();

            if (!res.success) {
                window.foco7Notify.error('Não foi possível obter dados da fatura.');
                return;
            }

            const fat = res.fatura;
            const carne = res.carne;

            if (fat.is_paga) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fatura Quitada',
                    text: 'Faturas quitadas não podem ser excluídas diretamente sem estorno prévio para segurança contábil.',
                    confirmButtonColor: '#d90000'
                });
                return;
            }

            // Não é carnê
            if (!carne || !carne.is_carne) {
                Swal.fire({
                    title: 'Excluir Fatura?',
                    html: `Tem certeza que deseja remover permanentemente a fatura <strong>${fat.codigo_fatura}</strong> (${fat.valor_formatado})?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d90000',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sim, Excluir',
                    cancelButtonText: 'Cancelar'
                }).then((r) => {
                    if (r.isConfirmed) {
                        executarExclusaoFatura(id, 'somente_esta');
                    }
                });
                return;
            }

            // É Carnê: apresenta opção para excluir esta, sequência, ou DELETAR TODAS DE UMA ÚNICA VEZ
            let htmlOpcoes = `
                <div class="text-start mb-2">
                    <div class="alert alert-secondary py-2 px-3 small mb-3">
                        <i class="fas fa-book me-1"></i> Parcela <strong>#${carne.numero_parcela}/${carne.total_parcelas}</strong> do carnê <code>${carne.codigo_carne}</code>.<br>
                        Total de parcelas: <strong>${carne.total_geral}</strong> (${carne.total_pagas > 0 ? carne.total_pagas + ' quitada(s), ' : ''}${carne.total_pendentes} pendente(s)).
                    </div>
                    <p class="mb-2 fw-semibold text-body">Selecione o escopo da exclusão:</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger text-start p-3 rounded-3 shadow-sm" onclick="executarExclusaoFatura(${id}, 'somente_esta')">
                            <div class="fw-bold fs-6"><i class="fas fa-trash me-2"></i> Excluir SOMENTE esta parcela (#${carne.numero_parcela})</div>
                            <div class="small text-muted ms-4">Remove apenas o registro da fatura ${fat.codigo_fatura}. As demais parcelas continuam salvas.</div>
                        </button>`;

            if (carne.total_sequencia > 1) {
                htmlOpcoes += `
                        <button type="button" class="btn btn-outline-danger text-start p-3 rounded-3 shadow-sm" onclick="executarExclusaoFatura(${id}, 'sequencia_pendente')">
                            <div class="fw-bold fs-6"><i class="fas fa-forward me-2"></i> Excluir esta e a SEQUÊNCIA pendente (${carne.total_sequencia} parcelas)</div>
                            <div class="small text-muted ms-4">Exclui a parcela #${carne.numero_parcela} e todas as parcelas pendentes posteriores.</div>
                        </button>`;
            }

            htmlOpcoes += `
                        <button type="button" class="btn btn-danger text-start p-3 rounded-3 shadow-sm text-white" onclick="executarExclusaoFatura(${id}, 'todas_parcelas')">
                            <div class="fw-bold fs-6"><i class="fas fa-dumpster-fire me-2"></i> DELETAR TODAS AS PARCELAS DE UMA ÚNICA VEZ (${carne.total_geral} parcelas)</div>
                            <div class="small text-white-50 ms-4">Apaga permanentemente todas as parcelas vinculadas a este carnê de uma única vez.${carne.total_pagas > 0 ? ' (Parcelas já pagas são preservadas por segurança contábil)' : ''}</div>
                        </button>
                    </div>
                </div>`;

            Swal.fire({
                title: 'Excluir Parcela / Carnê',
                html: htmlOpcoes,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancelar Ação',
                cancelButtonColor: '#6c757d',
                width: '620px'
            });
        }).fail(function() {
            Swal.close();
            window.foco7Notify.error('Falha ao comunicar com o servidor.');
        });
    }

    function executarExclusaoFatura(id, escopo) {
        Swal.close();

        let tituloConfirm = 'Confirmar Exclusão';
        let textoConfirm = 'Tem certeza que deseja prosseguir com a exclusão?';

        if (escopo === 'todas_parcelas') {
            tituloConfirm = 'DELETAR TODAS AS PARCELAS?';
            textoConfirm = 'ATENÇÃO: Todas as parcelas deste carnê serão removidas permanentemente do sistema financeiro de uma única vez!';
        } else if (escopo === 'sequencia_pendente') {
            tituloConfirm = 'Excluir Sequência Pendente?';
            textoConfirm = 'A parcela selecionada e todas as seguintes que ainda não foram pagas serão removidas!';
        }

        Swal.fire({
            title: tituloConfirm,
            text: textoConfirm,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Sim, Confirmar Exclusão',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/faturas/excluir-carne/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        escopo: escopo
                    },
                    success: function(res) {
                        if (res.success) {
                            window.foco7Notify.success(res.message);
                            setTimeout(function() { location.reload(); }, 600);
                        }
                    },
                    error: function(xhr) {
                        window.foco7Notify.error(xhr.responseJSON?.message || 'Falha ao excluir faturas.');
                    }
                });
            }
        });
    }

    function confirmarExclusaoDiretaTodasCarne(id, codigo) {
        executarExclusaoFatura(id, 'todas_parcelas');
    }

    function iniciarCancelamentoCarne(id) {
        iniciarCancelamentoFatura(id);
    }

    // Funções de Antecipação de Parcela
    let faturaValorOriginalAtual = 0;

    function abrirModalAnteciparFatura(id, codigo, cliente, valor, parcela) {
        faturaValorOriginalAtual = valor;
        $('#anteciparFaturaId').val(id);
        $('#anteciparCodigo').text(`${codigo} (Parc. ${parcela})`);
        $('#anteciparCliente').text(cliente);
        $('#anteciparValorOriginal').text(`R$ ${valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
        $('#anteciparDesconto').val('0.00');
        $('#anteciparObs').val(`Antecipação da parcela ${parcela}`);
        calcularTotalAntecipadoFatura();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAnteciparParcela'));
        modal.show();
    }

    function calcularTotalAntecipadoFatura() {
        const desconto = parseFloat($('#anteciparDesconto').val()) || 0;
        const final = Math.max(0, faturaValorOriginalAtual - desconto);
        $('#anteciparValorFinal').text(`R$ ${final.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
    }

    function confirmarAntecipacaoFatura(e) {
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
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Confirmar & Liquidar Parcela');
                window.foco7Notify.error(xhr.responseJSON?.message || 'Erro ao processar antecipação.');
            }
        });
    }

    // Função de Estorno Manual de Fatura Paga com Recálculo Contábil
    function estornarFatura(id, codigo, cliente, valor) {
        const valorFormatado = parseFloat(valor || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        Swal.fire({
            title: 'Estornar Fatura Paga?',
            html: `Você está prestes a estornar a fatura quitada <strong>${codigo}</strong> do cliente <strong>${cliente}</strong> no valor de <strong>R$ ${valorFormatado}</strong>.<br><br><div class="alert alert-warning text-start p-2 mb-0 small"><i class="fas fa-exclamation-triangle me-1"></i> <strong>Auditoria Contábil:</strong> O valor será deduzido do faturamento da receita do sistema e o status será gravado como Estornado.</div>`,
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Justificativa do estorno (obrigatório, mín. 3 letras)',
            inputValidator: (value) => {
                if (!value || value.trim().length < 3) {
                    return 'Informe uma justificativa válida com pelo menos 3 caracteres!';
                }
            },
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-undo me-1"></i> Confirmar Estorno',
            cancelButtonText: 'Cancelar Ação'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/faturas/estornar/${id}`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        motivo: result.value.trim()
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Fatura Estornada!',
                                text: res.message,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        window.foco7Notify.error(xhr.responseJSON?.message || 'Falha ao processar estorno da fatura.');
                    }
                });
            }
        });
    }
</script>
@endsection
