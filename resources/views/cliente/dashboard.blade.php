@extends('cliente.layouts.app')

@section('title', 'Central do Assinante')
@section('page_title', 'Central do Assinante')
@section('page_description', 'Painel exclusivo do cliente Foco 7. Consulte suas mensalidades, agendamentos e suporte.')

@section('page_actions')
    <span class="badge bg-body-secondary text-body border px-3 py-2 small d-inline-flex align-items-center gap-2 shadow-sm me-2" title="Horário Oficial de Brasília em Tempo Real">
        <span class="spinner-grow spinner-grow-sm text-danger" style="width: 7px; height: 7px;" role="status"></span>
        <i class="far fa-calendar-alt text-danger"></i>
        <span id="dataClienteDashboard" class="fw-semibold">{{ date('d/m/Y') }}</span>
        <span class="text-secondary">&bull;</span>
        <i class="far fa-clock text-danger"></i>
        <span id="relogioClienteDashboard" class="font-monospace fw-bold text-danger">{{ date('H:i:s') }}</span>
    </span>
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
            <div class="card-header w-100 bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-file-invoice text-danger me-2"></i> Mensalidades / Faturas</h5>
                <div class="card-tools ms-auto">
                    <a href="{{ route('cliente.faturas.index') }}" class="btn btn-sm btn-outline-danger">Ver Todas</a>
                </div>
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
            <div class="card-header w-100 bg-transparent d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-calendar-alt text-danger me-2"></i> Meus Agendamentos</h5>
                <div class="card-tools ms-auto">
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalAgendamentoCliente()">
                        <i class="fas fa-plus me-1"></i> Solicitar Agendamento
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($agendamentos as $ag)
                    <div class="list-group-item bg-transparent border-bottom px-3 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                @if($ag->categoria === 'estetica')
                                    <span class="badge bg-danger text-white" style="font-size: 0.75rem;">
                                        <i class="fas fa-sparkles me-1"></i> Estética
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #28a745; font-size: 0.75rem;">
                                        <i class="fas fa-satellite-dish me-1"></i> Rastreamento
                                    </span>
                                @endif
                                <h6 class="fw-bold mb-0">{{ $ag->tipo_servico }}</h6>
                            </div>
                            <small class="text-secondary d-block">
                                <i class="fas fa-car me-1"></i> {{ $ag->veiculo_modelo ?? 'Veículo' }} {{ $ag->veiculo_placa ? "({$ag->veiculo_placa})" : '' }}
                            </small>
                            <small class="text-secondary">
                                <i class="fas fa-clock me-1"></i> {{ $ag->data_hora_inicio->format('d/m/Y \à\s H:i') }}
                            </small>
                        </div>
                        <div class="text-end">
                            {!! $ag->status_badge !!}
                            @if($ag->valor)
                                <div class="small fw-bold text-body mt-1">R$ {{ number_format($ag->valor, 2, ',', '.') }}</div>
                            @endif
                            @if(in_array($ag->status, ['pendente', 'confirmado']))
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-cancelar-agendamento-cli" data-id="{{ $ag->id }}" data-servico="{{ $ag->tipo_servico }}">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </button>
                                </div>
                            @endif
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

<!-- MODAL SOLICITAR AGENDAMENTO PELO CLIENTE -->
<div class="modal fade" id="modalAgendamentoCliente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus text-danger me-2"></i> Solicitar Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgendamentoCliente" action="{{ route('cliente.agendamentos.solicitar') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoria *</label>
                            <select name="categoria" id="cli_categoria" class="form-select" onchange="aoMudarCategoriaCliente()" required>
                                <option value="estetica">✨ Estética Automotiva / Detalhamento</option>
                                <option value="rastreamento">📡 Rastreamento / Manutenção</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="boxCliServicoEstetica">
                            <label class="form-label small fw-bold">Serviço de Estética Desejado *</label>
                            <select name="servico_estetica_id" id="cli_servico_estetica_id" class="form-select">
                                <option value="">-- Selecione o serviço --</option>
                                @foreach($servicosEstetica as $srv)
                                    <option value="{{ $srv->id }}">{{ $srv->nome }} ({{ $srv->preco_formatado }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="boxCliTipoRastreamento" style="display: none;">
                            <label class="form-label small fw-bold">Tipo de Serviço *</label>
                            <select name="tipo_servico" id="cli_tipo_servico" class="form-select">
                                <option value="Manutenção do Rastreador">Manutenção do Rastreador</option>
                                <option value="Troca de Veículo">Troca de Veículo</option>
                                <option value="Vistoria Técnica">Vistoria Técnica</option>
                                <option value="Novo Rastreador Adicional">Instalar Rastreador em Novo Veículo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Modelo do Veículo *</label>
                            <input type="text" name="veiculo_modelo" id="cli_veiculo_modelo" class="form-control" required placeholder="Ex: Corolla 2023, Titan 160">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Placa do Veículo</label>
                            <input type="text" name="veiculo_placa" id="cli_veiculo_placa" class="form-control text-uppercase" placeholder="ABC-1D23">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data & Horário Preferencial *</label>
                            <input type="datetime-local" name="data_hora_inicio" id="cli_data_hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Observações / Detalhes</label>
                            <textarea name="observacoes" id="cli_observacoes" rows="2" class="form-control" placeholder="Descreva suas preferências ou necessidades especiais..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold" id="btnSalvarAgendamentoCli">
                        <i class="fas fa-check-circle me-1"></i> Enviar Solicitação
                    </button>
                </div>
            </form>
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

                <img id="pixImg" src="" alt="QR Code PIX" class="img-fluid rounded border p-2 bg-white qr-code-white mb-3" style="max-width: 220px;">

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

    function aoMudarCategoriaCliente() {
        var cat = $('#cli_categoria').val();
        if (cat === 'estetica') {
            $('#boxCliServicoEstetica').show();
            $('#boxCliTipoRastreamento').hide();
            $('#cli_servico_estetica_id').attr('required', 'required');
            $('#cli_tipo_servico').removeAttr('required');
        } else {
            $('#boxCliServicoEstetica').hide();
            $('#boxCliTipoRastreamento').show();
            $('#cli_servico_estetica_id').removeAttr('required');
            $('#cli_tipo_servico').attr('required', 'required');
        }
    }

    function abrirModalAgendamentoCliente() {
        $('#formAgendamentoCliente')[0].reset();
        $('#cli_categoria').val('estetica');
        aoMudarCategoriaCliente();

        var now = new Date();
        now.setDate(now.getDate() + 1);
        var nowStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + 'T09:00';
        $('#cli_data_hora_inicio').val(nowStr);

        new bootstrap.Modal(document.getElementById('modalAgendamentoCliente')).show();
    }

    $(function () {
        $('#btnCopiarPixCliente').on('click', function () {
            var copyText = document.getElementById("pixInputCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            window.foco7Notify.success('Código PIX copiado!');
        });

        // Submit AJAX da solicitação de agendamento do cliente
        $('#formAgendamentoCliente').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#btnSalvarAgendamentoCli');
            var btnHtml = $btn.html();

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Enviando...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    $btn.prop('disabled', false).html(btnHtml);
                    var modalEl = document.getElementById('modalAgendamentoCliente');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Agendamento Solicitado!',
                        text: res.message,
                        confirmButtonColor: '#d90000',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(btnHtml);
                    var msg = 'Erro ao solicitar agendamento.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    window.foco7Notify.error(msg);
                }
            });
        });

        // Cancelamento de agendamento pelo cliente com SweetAlert2
        $(document).on('click', '.btn-cancelar-agendamento-cli', function () {
            var agId = $(this).data('id');
            var servicoNome = $(this).data('servico') || 'este atendimento';

            Swal.fire({
                title: 'Cancelar Agendamento?',
                text: 'Deseja realmente cancelar o agendamento de ' + servicoNome + '? O horário reservado será liberado.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d90000',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, cancelar agendamento',
                cancelButtonText: 'Voltar',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Cancelando...',
                        text: 'Aguarde um instante...',
                        allowOutsideClick: false,
                        didOpen: function () {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/cliente/agendamentos/' + agId + '/cancelar',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Agendamento Cancelado!',
                                text: res.message,
                                confirmButtonColor: '#d90000'
                            }).then(function () {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Erro ao processar o cancelamento.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: msg,
                                confirmButtonColor: '#d90000'
                            });
                        }
                    });
                }
            });
        });

        // Relógio em Tempo Real
        function atualizarRelogioCliente() {
            const agora = new Date();
            const dia = String(agora.getDate()).padStart(2, '0');
            const mes = String(agora.getMonth() + 1).padStart(2, '0');
            const ano = agora.getFullYear();
            const horas = String(agora.getHours()).padStart(2, '0');
            const minutos = String(agora.getMinutes()).padStart(2, '0');
            const segundos = String(agora.getSeconds()).padStart(2, '0');

            const elData = document.getElementById('dataClienteDashboard');
            const elRelogio = document.getElementById('relogioClienteDashboard');

            if (elData) elData.textContent = `${dia}/${mes}/${ano}`;
            if (elRelogio) elRelogio.textContent = `${horas}:${minutos}:${segundos}`;
        }
        atualizarRelogioCliente();
        setInterval(atualizarRelogioCliente, 1000);
    });
</script>
@endsection
