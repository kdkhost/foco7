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
                            @if($fat->status === 'pago')
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm dropdown-toggle border-0 p-0" type="button" data-bs-toggle="dropdown" title="Fatura Paga (Alteração direta bloqueada por auditoria)">
                                        {!! $fat->status_badge !!}
                                    </button>
                                    <ul class="dropdown-menu shadow">
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
                                {!! $fat->status_badge !!}
                            @else
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
