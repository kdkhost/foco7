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
                        <td>
                            <span class="fw-bold text-dark">{{ $fat->codigo_fatura }}</span>
                            @if($fat->isCarne())
                                <br><span class="badge bg-success-subtle text-success border border-success-subtle small mt-1">
                                    <i class="fas fa-book me-1"></i> Carnê {{ $fat->numero_parcela }}/{{ $fat->total_parcelas }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $fat->plano->nome ?? $fat->descricao }}</div>
                            @if($fat->taxa_repassada > 0)
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Inclui taxa bancária de {{ $fat->taxa_repassada_formatada }}</small>
                            @endif
                        </td>
                        <td>{{ $fat->data_vencimento ? $fat->data_vencimento->format('d/m/Y') : '-' }}</td>
                        <td>
                            <div class="fw-bold text-success">{{ $fat->valor_formatado }}</div>
                            @if($fat->taxa_repassada > 0 && $fat->valor_original)
                                <small class="text-muted text-decoration-line-through">Original: {{ $fat->valor_original_formatada }}</small>
                            @endif
                        </td>
                        <td>{!! $fat->status_badge !!}</td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                @if($fat->isCarne())
                                    <a href="{{ route('faturas.carne.pdf', $fat->codigo_carne) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold" title="Baixar Carnê Completo em PDF (A4)">
                                        <i class="fas fa-file-pdf me-1"></i> Carnê PDF
                                    </a>
                                @endif

                                @if($fat->status === 'pendente')
                                    <button class="btn btn-sm btn-danger fw-bold" onclick="abrirPagamentoFatura('{{ $fat->codigo_fatura }}')">
                                        <i class="fab fa-pix me-1"></i> Pagar
                                    </button>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Quitado
                                    </span>
                                @endif
                            </div>
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

<!-- MODAL PAGAMENTO FATURA & CARNÊ -->
<div class="modal fade" id="modalPagamentoPixFatura" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-credit-card text-danger me-2"></i> Pagamento da Fatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="modalFatDesc" class="fw-bold mb-1">Mensalidade</h6>
                <h4 id="modalFatValor" class="text-success fw-black mb-1">R$ 0,00</h4>
                <div id="modalFatTaxaInfo" class="text-muted small mb-3 d-none"></div>

                <!-- SE FOR CARNÊ -->
                <div id="modalFatCarneBox" class="p-3 bg-success bg-opacity-10 rounded border border-success mb-3 d-none text-start">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-success"><i class="fas fa-book me-1"></i> Carnê de Pagamento</strong>
                            <div class="small text-muted" id="modalFatCarneParcela">Parcela 1 de 12</div>
                        </div>
                        <a href="#" id="modalFatBtnPdfCarne" target="_blank" class="btn btn-sm btn-success fw-bold">
                            <i class="fas fa-file-pdf me-1"></i> Baixar PDF (A4)
                        </a>
                    </div>
                </div>

                <!-- QR CODE PIX -->
                <div id="modalFatPixBox">
                    <img id="modalFatQrImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white mb-3" style="max-width: 220px;">

                    <p class="small text-secondary mb-2">Código PIX Copia e Cola:</p>
                    <div class="input-group mb-3">
                        <input type="text" id="modalFatPixCode" class="form-control text-center small fw-semibold" readonly>
                        <button class="btn btn-danger" type="button" id="btnCopiarPixModal">
                            <i class="fas fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                </div>

                <!-- BOLETO SE HOUVER -->
                <div id="modalFatBoletoBox" class="mt-3 pt-3 border-top d-none text-start">
                    <label class="small fw-bold text-secondary mb-1">Linha Digitável do Boleto:</label>
                    <div class="input-group mb-2">
                        <input type="text" id="modalFatLinhaDigitavel" class="form-control form-control-sm font-monospace" readonly>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCopiarBoletoModal">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="text-center">
                        <a href="#" id="modalFatLinkBoleto" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> Visualizar Boleto Bancário
                        </a>
                    </div>
                </div>

                <div class="small text-secondary mt-3"><i class="fas fa-bolt text-warning me-1"></i> Após o pagamento, a baixa é realizada automaticamente via Efí Bank / Webhook!</div>
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

                // Taxa repassada
                if (f.taxa_repassada > 0) {
                    $('#modalFatTaxaInfo').html('<i class="fas fa-info-circle me-1"></i> Inclui taxa bancária repassada de ' + f.taxa_repassada_formatada).removeClass('d-none');
                } else {
                    $('#modalFatTaxaInfo').addClass('d-none');
                }

                // Carnê
                if (f.is_carne && f.pdf_carne_url) {
                    $('#modalFatCarneBox').removeClass('d-none');
                    $('#modalFatCarneParcela').text('Parcela ' + f.numero_parcela + ' de ' + f.total_parcelas);
                    $('#modalFatBtnPdfCarne').attr('href', f.pdf_carne_url);
                } else {
                    $('#modalFatCarneBox').addClass('d-none');
                }

                // Pix
                if (f.qr_code_pix) {
                    $('#modalFatPixBox').removeClass('d-none');
                    $('#modalFatQrImg').attr('src', f.qr_code_pix_url);
                    $('#modalFatPixCode').val(f.qr_code_pix);
                } else {
                    $('#modalFatPixBox').addClass('d-none');
                }

                // Boleto
                if (f.linha_digitavel || f.url_boleto) {
                    $('#modalFatBoletoBox').removeClass('d-none');
                    $('#modalFatLinhaDigitavel').val(f.linha_digitavel || '');
                    if (f.url_boleto) {
                        $('#modalFatLinkBoleto').attr('href', f.url_boleto).removeClass('d-none');
                    } else {
                        $('#modalFatLinkBoleto').addClass('d-none');
                    }
                } else {
                    $('#modalFatBoletoBox').addClass('d-none');
                }

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

        $('#btnCopiarBoletoModal').on('click', function () {
            var copyText = document.getElementById("modalFatLinhaDigitavel");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            window.foco7Notify.success('Linha digitável copiada!');
        });
    });
</script>
@endsection
