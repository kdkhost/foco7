@extends('admin.layouts.app')

@section('title', 'Agenda de Instalações')
@section('page_title', 'Agendamentos & Ordens de Serviço')
@section('page_description', 'Calendário interativo FullCalendar 6 com agendamentos de instalações, vistorias e suporte.')

@section('styles')
<!-- FullCalendar 6 CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<style>
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
    }
    .fc-daygrid-event {
        cursor: pointer;
    }
</style>
@endsection

@section('page_actions')
<button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalAgendamento()">
    <i class="fas fa-plus me-1"></i> Novo Agendamento
</button>
@endsection

@section('content')
<div class="row g-4">
    <!-- Legenda de Status e Categorias -->
    <div class="col-12">
        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between p-3 rounded bg-body shadow-sm">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="small fw-bold text-secondary me-1">Categorias:</span>
                <span class="badge" style="background-color: #28a745;"><i class="fas fa-satellite-dish me-1"></i> Rastreamento</span>
                <span class="badge" style="background-color: #1e293b;"><i class="fas fa-spray-can-sparkles me-1 text-danger"></i> Estética Automotiva</span>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="small fw-bold text-secondary me-1">Status:</span>
                <span class="status-badge-pill status-badge-pendente"><span class="status-dot"></span><i class="fas fa-clock"></i><span>Pendente</span></span>
                <span class="status-badge-pill status-badge-pago"><span class="status-dot"></span><i class="fas fa-check-circle"></i><span>Confirmado</span></span>
                <span class="status-badge-pill status-badge-concluido"><span class="status-dot"></span><i class="fas fa-flag-checkered"></i><span>Concluído</span></span>
                <span class="status-badge-pill status-badge-cancelado"><span class="status-dot"></span><i class="fas fa-ban"></i><span>Cancelado</span></span>
            </div>
        </div>
    </div>

    <!-- Calendário FullCalendar 6 -->
    <div class="col-12">
        <div class="card shadow-sm border-0 p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- MODAL CADASTRAR / EDITAR AGENDAMENTO -->
<div class="modal fade" id="modalAgendamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalAgendamentoTitulo">Agendar Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.agendamentos.salvar') }}" method="POST" id="formAgendamento">
                @csrf
                <input type="hidden" name="agendamento_id" id="agendamento_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoria do Atendimento *</label>
                            <select name="categoria" id="categoria" class="form-select" required onchange="aoMudarCategoria()">
                                <option value="rastreamento">📡 Rastreamento Veicular / Segurança</option>
                                <option value="estetica">✨ Estética Automotiva / Detalhamento</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="boxServicoEstetica" style="display: none;">
                            <label class="form-label small fw-bold">Serviço de Estética *</label>
                            <select name="servico_estetica_id" id="servico_estetica_id" class="form-select" onchange="aoSelecionarServicoEstetica()">
                                <option value="">-- Selecione o serviço de estética --</option>
                                @foreach($servicosEstetica as $srv)
                                    <option value="{{ $srv->id }}" data-preco="{{ $srv->preco }}" data-nome="{{ $srv->nome }}">
                                        {{ $srv->nome }} (R$ {{ number_format($srv->preco, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="boxTipoServicoRastreamento">
                            <label class="form-label small fw-bold">Tipo de Serviço *</label>
                            <select name="tipo_servico" id="tipo_servico" class="form-select" required>
                                <option value="Instalação de Rastreador">Instalação de Rastreador</option>
                                <option value="Manutenção / Troca de Chip">Manutenção / Troca de Chip</option>
                                <option value="Vistoria Tática">Vistoria Tática</option>
                                <option value="Suporte Técnico Domiciliar">Suporte Técnico Domiciliar</option>
                                <option value="Retirada / Troca de Veículo">Retirada / Troca de Veículo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Valor do Serviço (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" name="valor" id="valor" class="form-control" placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome do Cliente *</label>
                            <input type="text" name="cliente_nome" id="cliente_nome" class="form-control" required placeholder="Ex: Marcelo Oliveira">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">WhatsApp / Telefone *</label>
                            <input type="text" name="cliente_telefone" id="cliente_telefone" class="form-control mask-telefone" required placeholder="(91) 98111-7777">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">E-mail</label>
                            <input type="email" name="cliente_email" id="cliente_email" class="form-control" placeholder="cliente@email.com">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Modelo do Veículo</label>
                            <input type="text" name="veiculo_modelo" id="veiculo_modelo" class="form-control" placeholder="Ex: Corolla Cross">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Placa do Veículo</label>
                            <input type="text" name="veiculo_placa" id="veiculo_placa" class="form-control text-uppercase" placeholder="ABC-1D23">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data & Hora de Início *</label>
                            <input type="datetime-local" name="data_hora_inicio" id="data_hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data & Hora de Término</label>
                            <input type="datetime-local" name="data_hora_fim" id="data_hora_fim" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Endereço do Atendimento / Loja</label>
                            <input type="text" name="endereco_atendimento" id="endereco_atendimento" class="form-control" placeholder="Rua, número e bairro ou Sede Foco 7">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Cidade</label>
                            <input type="text" name="cidade" id="cidade" class="form-control" value="Belém">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status do Atendimento *</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pendente">Pendente</option>
                                <option value="confirmado" selected>Confirmado</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipo de Pagamento *</label>
                            <select name="tipo_pagamento" id="tipo_pagamento" class="form-select" required>
                                <option value="online">Online (PIX / Cartão)</option>
                                <option value="local">No Local (Instalação / Presencial)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Observações / Detalhes</label>
                            <textarea name="observacoes" id="observacoes" rows="2" class="form-control" placeholder="Instruções para a equipe técnica ou equipe de estética..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger d-none" id="btnExcluirAgendamento" onclick="excluirAgendamentoAtual()">
                            <i class="fas fa-trash-alt me-1"></i> Excluir
                        </button>
                        <button type="button" class="btn btn-warning d-none text-dark fw-semibold" id="btnCancelarAgendamentoModal" onclick="cancelarAgendamentoModalAtual()">
                            <i class="fas fa-ban me-1"></i> Cancelar Agendamento
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm" id="btnSalvarAgendamento">
                            <i class="fas fa-save me-1"></i> Salvar Agendamento
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar 6 Bundle com pt-br -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/pt-br.global.min.js"></script>

<script>
    var calendar;

    function aoMudarCategoria() {
        var cat = $('#categoria').val();
        if (cat === 'estetica') {
            $('#boxServicoEstetica').show();
            $('#boxTipoServicoRastreamento').hide();
            $('#tipo_servico').removeAttr('required');
            $('#servico_estetica_id').attr('required', 'required');
        } else {
            $('#boxServicoEstetica').hide();
            $('#boxTipoServicoRastreamento').show();
            $('#servico_estetica_id').removeAttr('required');
            $('#tipo_servico').attr('required', 'required');
        }
    }

    function aoSelecionarServicoEstetica() {
        var opt = $('#servico_estetica_id option:selected');
        var preco = opt.data('preco');
        var nome = opt.data('nome');
        if (preco !== undefined && preco !== '') {
            $('#valor').val(preco);
        }
        if (nome) {
            $('#tipo_servico').val(nome);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                day: 'Dia'
            },
            events: '{{ route("admin.agendamentos.eventos") }}',
            editable: true,
            eventClick: function (info) {
                var id = info.event.id;
                $.ajax({
                    url: '/admin/agendamentos/obter/' + id,
                    type: 'GET',
                    success: function (res) {
                        if (res.success && res.agendamento) {
                            var ag = res.agendamento;
                            $('#agendamento_id').val(ag.id);
                            $('#categoria').val(ag.categoria || 'rastreamento');
                            aoMudarCategoria();

                            $('#cliente_nome').val(ag.cliente_nome);
                            $('#cliente_telefone').val(ag.cliente_telefone);
                            $('#cliente_email').val(ag.cliente_email || '');
                            $('#servico_estetica_id').val(ag.servico_estetica_id || '');
                            $('#tipo_servico').val(ag.tipo_servico);
                            $('#valor').val(ag.valor || '');
                            $('#veiculo_modelo').val(ag.veiculo_modelo || '');
                            $('#veiculo_placa').val(ag.veiculo_placa || '');
                            $('#endereco_atendimento').val(ag.endereco_atendimento || '');
                            $('#cidade').val(ag.cidade || 'Belém');
                            $('#status').val(ag.status);
                            $('#tipo_pagamento').val(ag.tipo_pagamento || 'online');
                            $('#observacoes').val(ag.observacoes || '');
                            $('#data_hora_inicio').val(res.inicio_iso || '');
                            $('#data_hora_fim').val(res.fim_iso || '');

                            $('#modalAgendamentoTitulo').html('<i class="fas fa-edit me-1 text-danger"></i> Editar Agendamento #' + ag.id);
                            $('#btnExcluirAgendamento').removeClass('d-none');
                            if (ag.status !== 'cancelado') {
                                $('#btnCancelarAgendamentoModal').removeClass('d-none');
                            } else {
                                $('#btnCancelarAgendamentoModal').addClass('d-none');
                            }
                            new bootstrap.Modal(document.getElementById('modalAgendamento')).show();
                        }
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao obter dados do agendamento.');
                    }
                });
            },
            eventDrop: function (info) {
                var s = info.event.start;
                var sStr = s.getFullYear() + '-' + String(s.getMonth()+1).padStart(2, '0') + '-' + String(s.getDate()).padStart(2, '0') + ' ' + String(s.getHours()).padStart(2, '0') + ':' + String(s.getMinutes()).padStart(2, '0') + ':00';

                $.ajax({
                    url: '{{ route("admin.agendamentos.horario") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        id: info.event.id,
                        start: sStr
                    },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao reagendar atendimento.');
                        info.revert();
                    }
                });
            }
        });

        calendar.render();

        // Submissão AJAX do formulário de agendamento
        $('#formAgendamento').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btnSalvarAgendamento');
            var btnHtml = btn.html();

            // Garantir que tipo_servico tenha valor caso estetica
            if ($('#categoria').val() === 'estetica') {
                var srvNome = $('#servico_estetica_id option:selected').data('nome');
                if (srvNome) {
                    $('#tipo_servico').val(srvNome);
                }
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    btn.prop('disabled', false).html(btnHtml);
                    window.foco7Notify.success(res.message || 'Agendamento salvo com sucesso!');
                    var modalEl = document.getElementById('modalAgendamento');
                    var modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    calendar.refetchEvents();
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html(btnHtml);
                    var msg = 'Erro ao salvar agendamento.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    window.foco7Notify.error(msg);
                }
            });
        });
    });

    function abrirModalAgendamento() {
        $('#formAgendamento')[0].reset();
        $('#agendamento_id').val('');
        $('#btnExcluirAgendamento').addClass('d-none');
        $('#btnCancelarAgendamentoModal').addClass('d-none');
        $('#modalAgendamentoTitulo').html('<i class="fas fa-calendar-plus me-1 text-danger"></i> Novo Agendamento');
        $('#categoria').val('rastreamento');
        $('#tipo_pagamento').val('online');
        aoMudarCategoria();

        var now = new Date();
        var nowStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + 'T09:00';
        $('#data_hora_inicio').val(nowStr);

        new bootstrap.Modal(document.getElementById('modalAgendamento')).show();
    }

    function cancelarAgendamentoModalAtual() {
        var id = $('#agendamento_id').val();
        if (!id) return;

        Swal.fire({
            title: 'Cancelar Agendamento?',
            text: 'Deseja realmente cancelar este atendimento? O horário será liberado no calendário.',
            input: 'text',
            inputPlaceholder: 'Motivo do cancelamento (opcional)',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, Cancelar',
            cancelButtonText: 'Manter Agendado'
        }).then(function (result) {
            if (result.isConfirmed) {
                var motivo = result.value || 'Cancelado pelo administrador via calendário';
                $.ajax({
                    url: '/admin/agendamentos/cancelar/' + id,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { motivo: motivo },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        var modalEl = document.getElementById('modalAgendamento');
                        var modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        calendar.refetchEvents();
                    },
                    error: function (xhr) {
                        window.foco7Notify.error(xhr.responseJSON?.message || 'Falha ao cancelar agendamento.');
                    }
                });
            }
        });
    }

    function excluirAgendamentoAtual() {
        var id = $('#agendamento_id').val();
        if (!id) return;

        window.confirmarAcao(
            'Excluir Agendamento?',
            'Este horário será removido do calendário permanentemente.',
            'warning',
            function () {
                $.ajax({
                    url: '/admin/agendamentos/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        var modalEl = document.getElementById('modalAgendamento');
                        var modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        calendar.refetchEvents();
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao remover agendamento.');
                    }
                });
            }
        );
    }
</script>
@endsection
