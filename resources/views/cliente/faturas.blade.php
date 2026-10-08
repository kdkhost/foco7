@extends('cliente.layouts.app')

@section('title', 'Minhas Faturas')

@section('content')
<div class="card-cliente p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-white"><i class="fas fa-receipt text-danger me-2"></i> Histórico de Faturas & Recibos</h4>
            <p class="text-secondary small mb-0">Consulte mensalidades em aberto e comprovantes de pagamento.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
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
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-check me-1"></i> Quitado
                            </button>
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

    <div class="mt-4">
        {{ $faturas->links() }}
    </div>
</div>

<!-- MODAL PAGAMENTO PIX -->
<div class="modal fade" id="modalPagamentoPixFatura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="fab fa-pix text-success me-2"></i> Pagamento da Fatura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="modalFatDesc" class="fw-bold mb-1">Mensalidade</h6>
                <h4 id="modalFatValor" class="text-success fw-black mb-3">R$ 0,00</h4>

                <img id="modalFatQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border border-light p-2 bg-white mb-3" style="max-width: 220px;">

                <p class="small text-secondary mb-2">Código PIX Copia e Cola:</p>
                <div class="input-group mb-3">
                    <input type="text" id="modalFatPixCode" class="form-control bg-black text-white border-secondary small" readonly>
                    <button class="btn btn-danger" type="button" id="btnCopiarPixModal">Copiar</button>
                </div>

                <div class="small text-secondary">Após o pagamento, a baixa é realizada automaticamente via Webhook!</div>
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
