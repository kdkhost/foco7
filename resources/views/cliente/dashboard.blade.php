@extends('cliente.layouts.app')

@section('title', 'Central do Assinante')
@section('page_title', 'Central do Assinante')
@section('page_description', 'Painel exclusivo do cliente Foco 7. Consulte suas mensalidades, agendamentos e suporte.')

@section('page_actions')
    <a href="https://wa.me/{{ \App\Models\CmsSetting::get('contato_whatsapp', '559180510736') }}?text={{ urlencode('Olá, sou cliente Foco 7 e preciso de suporte técnico.') }}" target="_blank" class="btn btn-outline-success fw-bold">
        <i class="fab fa-whatsapp me-1"></i> Suporte WhatsApp 24h
    </a>
@endsection

@section('content')
<!-- CARDS DE MÉTRICAS DO CLIENTE (MESMO PADRÃO ADMINLTE 4) -->
<div class="row g-4 mb-4">
    <!-- Faturas Pendentes -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="{{ $faturasPendentes->count() > 0 ? 'bg-danger' : 'bg-secondary' }} text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Faturas Pendentes</h6>
                    <h3 class="fw-bold mb-0 text-{{ $faturasPendentes->count() > 0 ? 'danger' : 'secondary' }}">{{ $faturasPendentes->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Faturas Pagas -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Faturas Quitadas</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ $faturasPagas->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Instalações & Visitas -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Meus Agendamentos</h6>
                    <h3 class="fw-bold mb-0">{{ $agendamentos->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Status do Rastreamento -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Monitoramento</h6>
                    <h6 class="fw-bold mb-0 text-success">PROTEÇÃO ATIVA 24H</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Mensalidades / Faturas -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice text-danger me-2"></i> Mensalidades / Faturas</h5>
                <a href="{{ route('cliente.faturas.index') }}" class="btn btn-sm btn-outline-danger">Ver Todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faturas->take(5) as $fat)
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
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> Quitado
                                        </span>
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
    </div>

    <!-- Meus Agendamentos -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-danger me-2"></i> Minhas Instalações & Visitas</h5>
                <span class="badge bg-secondary">{{ $agendamentos->count() }} Registros</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($agendamentos as $ag)
                    <div class="list-group-item bg-transparent border-bottom px-3 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $ag->tipo_servico }}: {{ $ag->veiculo_modelo ?? 'Veículo' }}</h6>
                            <small class="text-secondary"><i class="fas fa-clock me-1"></i> {{ $ag->data_hora_inicio->format('d/m/Y \à\s H:i') }}</small>
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
</div>

<!-- MODAL PAGAMENTO PIX -->
<div class="modal fade" id="modalPagamentoPix" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fab fa-pix text-success me-2"></i> Pagamento Imediato via PIX</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 id="pixFaturaDescricao" class="fw-bold mb-1">Mensalidade</h6>
                <h4 id="pixFaturaValor" class="text-success fw-black mb-3">R$ 0,00</h4>

                <img id="pixImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white mb-3" style="max-width: 220px;">

                <p class="small text-secondary mb-2">Código PIX Copia e Cola:</p>
                <div class="input-group mb-3">
                    <input type="text" id="pixInputCode" class="form-control text-center small fw-semibold" readonly>
                    <button class="btn btn-danger" type="button" id="btnCopiarPixCliente">
                        <i class="fas fa-copy me-1"></i> Copiar
                    </button>
                </div>

                <div class="small text-secondary"><i class="fas fa-bolt text-warning me-1"></i> A compensação do PIX é automática em menos de 1 minuto!</div>
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
