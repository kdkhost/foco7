@extends('cliente.layouts.app')

@section('title', 'Minhas Faturas')
@section('page_title', 'Minhas Faturas & Mensalidades')
@section('page_description', 'Histórico completo de mensalidades, faturas em aberto e pagamento instantâneo via PIX.')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="fas fa-receipt text-danger me-2"></i> Histórico de Faturas & Recibos</h5>
            <small class="text-secondary">Consulte mensalidades em aberto e comprovantes de quitação.</small>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Descrição / Plano</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faturas as $fat)
                    <tr>
                        <td class="fw-bold">{{ $fat->codigo_fatura }}</td>
                        <td>{{ $fat->plano->nome ?? $fat->descricao }}</td>
                        <td>{{ $fat->data_vencimento->format('d/m/Y') }}</td>
                        <td class="fw-bold text-success">{{ $fat->valor_formatado }}</td>
                        <td>{!! $fat->status_badge !!}</td>
                        <td>
                            @if($fat->status === 'pendente')
                                <button class="btn btn-sm btn-danger fw-bold" onclick="abrirPagamentoFatura('{{ $fat->codigo_fatura }}')">
                                    <i class="fab fa-pix me-1"></i> Pagar via PIX
                                </button>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> Quitado
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-secondary">Nenhuma fatura registrada para sua conta.</td>
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

<!-- MODAL PAGAMENTO PIX -->
<div class="modal fade" id="modalPagamentoPixFatura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fab fa-pix text-success me-2"></i> Pagamento da Fatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="modalFatDesc" class="fw-bold mb-1">Mensalidade</h6>
                <h4 id="modalFatValor" class="text-success fw-black mb-3">R$ 0,00</h4>

                <img id="modalFatQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white mb-3" style="max-width: 220px;">

                <p class="small text-secondary mb-2">Código PIX Copia e Cola:</p>
                <div class="input-group mb-3">
                    <input type="text" id="modalFatPixCode" class="form-control text-center small fw-semibold" readonly>
                    <button class="btn btn-danger" type="button" id="btnCopiarPixModal">
                        <i class="fas fa-copy me-1"></i> Copiar
                    </button>
                </div>

                <div class="small text-secondary"><i class="fas fa-bolt text-warning me-1"></i> Após o pagamento, a baixa é realizada automaticamente via Webhook!</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirPagamentoFatura(codigo) {
        $.getJSON('/cliente/faturas/' + codigo, function (res) {
            if (res.success && res.fatura) {
                var f = res.fatura;
                $('#modalFatDesc').text(f.descricao);
                $('#modalFatValor').text(f.valor_formatado);
                $('#modalFatQrImg').attr('src', f.qr_code_pix_url);
                $('#modalFatPixCode').val(f.qr_code_pix);

                new bootstrap.Modal(document.getElementById('modalPagamentoPixFatura')).show();
            }
        });
    }

    $(function () {
        $('#btnCopiarPixModal').on('click', function () {
            var copyText = document.getElementById("modalFatPixCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            window.foco7Notify.success('Código PIX copiado!');
        });
    });
</script>
@endsection
