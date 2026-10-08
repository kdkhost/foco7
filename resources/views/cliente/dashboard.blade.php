@extends('cliente.layouts.app')

@section('title', 'Painel do Cliente')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card-cliente p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="fw-bold mb-1">Bem-vindo, {{ $user->name }}!</h3>
                <p class="text-secondary small mb-0">Central do Assinante Foco 7. Consulte suas mensalidades e status dos serviços.</p>
            </div>
            <div>
                <a href="https://wa.me/{{ \App\Models\CmsSetting::get('contato_whatsapp', '559180510736') }}?text={{ urlencode('Olá, sou cliente Foco 7 e preciso de suporte técnico.') }}" target="_blank" class="btn btn-outline-success fw-bold">
                    <i class="fab fa-whatsapp me-1"></i> Suporte Técnico 24h
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Faturas em Aberto -->
    <div class="col-lg-6">
        <div class="card-cliente p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-white"><i class="fas fa-file-invoice text-danger me-2"></i> Mensalidades / Faturas</h5>
                <a href="{{ route('cliente.faturas.index') }}" class="btn btn-sm btn-outline-danger">Ver Todas</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faturas->take(4) as $fat)
                        <tr>
                            <td class="fw-bold small">{{ $fat->codigo_fatura }}</td>
                            <td>{{ $fat->data_vencimento->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">{{ $fat->valor_formatado }}</td>
                            <td>
                                @if($fat->status === 'pendente')
                                    <button class="btn btn-sm btn-danger fw-bold" onclick="abrirPagamento('{{ $fat->codigo_fatura }}')">
                                        <i class="fab fa-pix me-1"></i> Pagar
                                    </button>
                                @else
                                    <span class="badge bg-success">Pago</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">Nenhuma fatura encontrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Meus Agendamentos -->
    <div class="col-lg-6">
        <div class="card-cliente p-4 h-100">
            <h5 class="fw-bold mb-3 text-white"><i class="fas fa-calendar-alt text-danger me-2"></i> Minhas Instalações & Visitas</h5>
            <div class="list-group list-group-flush">
                @forelse($agendamentos as $ag)
                <div class="list-group-item bg-transparent text-white border-secondary px-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $ag->tipo_servico }}: {{ $ag->veiculo_modelo ?? 'Veículo' }}</h6>
                        <span class="small text-secondary"><i class="fas fa-clock me-1"></i> {{ $ag->data_hora_inicio->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                    <div>
                        {!! $ag->status_badge !!}
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-secondary">
                    Nenhum agendamento ativo no momento.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL PAGAMENTO PIX -->
<div class="modal fade" id="modalPagamentoPix" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold"><i class="fab fa-pix text-success me-2"></i> Pagamento Imediato via PIX</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="pixFaturaDescricao" class="fw-bold mb-1">Mensalidade</h6>
                <h4 id="pixFaturaValor" class="text-success fw-black mb-3">R$ 0,00</h4>

                <img id="pixImg" src="" alt="QR Code PIX" class="img-fluid rounded border border-light p-2 bg-white mb-3" style="max-width: 220px;">

                <p class="small text-secondary mb-2">Código PIX Copia e Cola:</p>
                <div class="input-group mb-3">
                    <input type="text" id="pixInputCode" class="form-control bg-black text-white border-secondary small" readonly>
                    <button class="btn btn-danger" type="button" id="btnCopiarPixCliente">Copiar</button>
                </div>

                <div class="small text-secondary">A compensação do PIX é automática em menos de 1 minuto!</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirPagamento(codigo) {
        $.getJSON('/cliente/faturas/' + codigo, function (res) {
            if (res.success && res.fatura) {
                var f = res.fatura;
                $('#pixFaturaDescricao').text(f.descricao);
                $('#pixFaturaValor').text(f.valor_formatado);
                $('#pixImg').attr('src', f.qr_code_pix_url);
                $('#pixInputCode').val(f.qr_code_pix);

                new bootstrap.Modal(document.getElementById('modalPagamentoPix')).show();
            }
        });
    }

    $(function () {
        $('#btnCopiarPixCliente').on('click', function () {
            var copyText = document.getElementById("pixInputCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            window.foco7Notify.success('Código PIX copiado!');
        });
    });
</script>
@endsection
