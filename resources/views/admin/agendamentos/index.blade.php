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
<button type="button" class="btn btn-danger fw-bold" onclick="abrirModalAgendamento()">
    <i class="fas fa-plus me-1"></i> Novo Agendamento
</button>
@endsection

@section('content')
<div class="row g-4">
    <!-- Legenda de Status -->
    <div class="col-12">
        <div class="d-flex flex-wrap gap-3 align-items-center p-3 rounded bg-body shadow-sm">
            <span class="small fw-bold text-secondary">Status dos Atendimentos:</span>
            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pendente</span>
            <span class="badge bg-success"><i class="fas fa-check me-1"></i> Confirmado</span>
            <span class="badge bg-primary"><i class="fas fa-tools me-1"></i> Concluído</span>
            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> Cancelado</span>
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
                <h5 class="modal-title fw-bold" id="modalAgendamentoTitulo">Agendar Atendimento Técnico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.agendamentos.salvar') }}" method="POST" class="ajax-form" id="formAgendamento">
                @csrf
                <input type="hidden" name="agendamento_id" id="agendamento_id">
                <div class="modal-body">
                    <div class="row g-3">
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
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipo de Serviço *</label>
                            <select name="tipo_servico" id="tipo_servico" class="form-select" required>
                                <option value="Instalação">Instalação de Rastreador</option>
                                <option value="Manutenção">Manutenção / Troca de Chip</option>
                                <option value="Vistoria">Vistoria Tática</option>
                                <option value="Suporte Técnico">Suporte Técnico Domiciliar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Modelo do Veículo</label>
                            <input type="text" name="veiculo_modelo" id="veiculo_modelo" class="form-control" placeholder="Ex: Corolla Cross 2024">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Placa do Veículo</label>
                            <input type="text" name="veiculo_placa" id="veiculo_placa" class="form-control mask-placa" placeholder="ABC-1D23">
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
                            <label class="form-label small fw-bold">Endereço do Atendimento</label>
                            <input type="text" name="endereco_atendimento" id="endereco_atendimento" class="form-control" placeholder="Rua, número e bairro">
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
                        <div class="col-12">
                            <label class="form-label small fw-bold">Observações Técnicas</label>
                            <textarea name="observacoes" id="observacoes" rows="3" class="form-control" placeholder="Instruções para o técnico instalador..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger d-none" id="btnExcluirAgendamento" onclick="excluirAgendamentoAtual()">
                        <i class="fas fa-trash-alt me-1"></i> Excluir
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-danger fw-bold">Salvar Agendamento</button>
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
                var props = info.event.extendedProps;
                $('#agendamento_id').val(info.event.id);
                $('#cliente_nome').val(props.cliente_nome);
                $('#cliente_telefone').val(props.cliente_telefone);
                $('#tipo_servico').val(props.tipo_servico);
                $('#veiculo_modelo').val(props.veiculo_modelo);
                $('#veiculo_placa').val(props.veiculo_placa);
                $('#endereco_atendimento').val(props.endereco);
                $('#cidade').val(props.cidade);
                $('#status').val(props.status);
                $('#observacoes').val(props.observacoes);

                // Formatar datas para input datetime-local
                if (info.event.start) {
                    var s = info.event.start;
                    var sStr = s.getFullYear() + '-' + String(s.getMonth()+1).padStart(2, '0') + '-' + String(s.getDate()).padStart(2, '0') + 'T' + String(s.getHours()).padStart(2, '0') + ':' + String(s.getMinutes()).padStart(2, '0');
                    $('#data_hora_inicio').val(sStr);
                }

                $('#modalAgendamentoTitulo').text('Editar Agendamento #' + info.event.id);
                $('#btnExcluirAgendamento').removeClass('d-none');
                new bootstrap.Modal(document.getElementById('modalAgendamento')).show();
            },
            eventDrop: function (info) {
                // Reagendamento por arrastar e soltar
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
    });

    function abrirModalAgendamento() {
        $('#formAgendamento')[0].reset();
        $('#agendamento_id').val('');
        $('#btnExcluirAgendamento').addClass('d-none');
        $('#modalAgendamentoTitulo').text('Novo Agendamento');

        var now = new Date();
        var nowStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + 'T09:00';
        $('#data_hora_inicio').val(nowStr);

        new bootstrap.Modal(document.getElementById('modalAgendamento')).show();
    }

    function excluirAgendamentoAtual() {
        var id = $('#agendamento_id').val();
        if (!id) return;

        window.confirmarAcao(
            'Excluir Agendamento?',
            'Este horário será removido do calendário.',
            'warning',
            function () {
                $.ajax({
                    url: '/admin/agendamentos/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        bootstrap.Modal.getInstance(document.getElementById('modalAgendamento')).hide();
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
