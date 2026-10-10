@extends('admin.layouts.app')

@section('title', 'Central de Cron Jobs & Automação')
@section('page_title', 'Central de Tarefas Cron')
@section('page_description', 'Acompanhe as rotinas agendadas no servidor cPanel e execute tarefas sob demanda com monitoramento em tempo real.')

@section('content')
<style>
    .pulse-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        background-color: #198754;
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
        animation: pulseAnimation 2s infinite;
    }
    .pulse-dot-amber {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        background-color: #ffc107;
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        animation: pulseAnimationAmber 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(25, 135, 84, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    @keyframes pulseAnimationAmber {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 193, 7, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    .terminal-container {
        background-color: #121212;
        color: #00ff66;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 13px;
        border-radius: 8px;
        padding: 16px;
        max-height: 280px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
        border: 1px solid #2d2d2d;
    }
</style>

<!-- STATUS HEADER / HEARTBEAT CARD -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i id="heartbeatIcon" class="fas fa-heartbeat fs-3 {{ $cronAtivo ? 'text-danger' : 'text-secondary' }}"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span id="heartbeatDot" class="{{ $cronAtivo ? 'pulse-dot' : 'pulse-dot-amber' }}"></span>
                            <strong id="heartbeatTitle" class="fs-6 text-body">
                                {{ $cronAtivo ? 'Agendador cPanel: Ativo & Operacional' : 'Agendador cPanel: Aguardando Próximo Pulso' }}
                            </strong>
                        </div>
                        <div class="text-secondary small">
                            <span>Último pulso do servidor: </span>
                            <strong id="heartbeatDate" class="text-body font-monospace">{{ $ultimoBatimento }}</strong>
                            <span id="heartbeatAgo" class="badge {{ $cronAtivo ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} ms-1">
                                {{ $cronAtivo ? 'Pulsação contínua (1m)' : 'Aguardando' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="atualizarStatusCron(true)">
                        <i class="fas fa-sync-alt me-1" id="iconSync"></i> Atualizar Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border shadow-sm h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-center">
                <span class="text-secondary small mb-1"><i class="fas fa-clock me-1"></i> Horário Atual do Servidor</span>
                <h5 id="serverTime" class="mb-0 fw-bold text-body font-monospace">{{ date('d/m/Y H:i:s') }}</h5>
                <small class="text-secondary mt-1">Sincronizado com o agendador Laravel</small>
            </div>
        </div>
    </div>
</div>

<!-- COMANDO OFICIAL PARA CPANEL (LINHA ÚNICA) -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold"><i class="fas fa-terminal text-warning me-2"></i> Linha Única do Agendador (Instalada na Hospedagem cPanel)</span>
        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> EXECUTANDO A CADA MINUTO (* * * * *)</span>
    </div>
    <div class="card-body p-4 bg-body-tertiary">
        <p class="small text-secondary mb-2">
            O Laravel gerencia automaticamente todas as tarefas internas e horários através de um único comando disparado a cada minuto pelo cPanel:
        </p>
        <div class="input-group">
            <input type="text" class="form-control font-monospace bg-dark text-white border-0 py-2 fs-6" id="cronCmdLinha" value="{{ $comandoCpanel }}" readonly>
            <button class="btn btn-warning fw-bold px-3" type="button" onclick="copiarComandoCron()">
                <i class="fas fa-copy me-1"></i> Copiar Linha
            </button>
        </div>
    </div>
</div>

<!-- ROTINAS AGENDADAS COM DISPARO SOB DEMANDA -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-body"><i class="fas fa-tasks text-danger me-2"></i> Rotinas Agendadas do Sistema Foco 7</h6>
        <span class="text-secondary small"><i class="fas fa-info-circle me-1"></i> Executadas automaticamente conforme a frequência</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 240px;">Rotina Automática</th>
                    <th>Comando Artisan</th>
                    <th>Frequência</th>
                    <th>Última Execução</th>
                    <th class="text-end" style="min-width: 170px;">Execução Sob Demanda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rotinas as $chave => $rotina)
                <tr>
                    <td>
                        <strong class="text-body d-block">{{ $rotina['nome'] }}</strong>
                        <small class="text-secondary">{{ $rotina['descricao'] }}</small>
                    </td>
                    <td>
                        <code class="text-danger fw-bold bg-danger-subtle px-2 py-1 rounded" style="font-size: 12px;">php artisan {{ $rotina['comando'] }}</code>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-body border">{{ $rotina['frequencia'] }}</span>
                    </td>
                    <td>
                        <span id="lastRun_{{ $chave }}" class="small text-secondary font-monospace">
                            @if($rotina['ultima_execucao'] !== 'Nunca executado')
                                <i class="fas fa-check text-success me-1"></i> {{ $rotina['ultima_execucao'] }}
                            @else
                                <span class="text-muted"><i class="fas fa-hourglass-start me-1"></i> Aguardando horário programado</span>
                            @endif
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold" onclick="executarCronManual('{{ $chave }}', '{{ addslashes($rotina['nome']) }}')">
                            <i class="fas fa-play me-1"></i> Executar Agora
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- CONSOLE DE LOGS EM TEMPO REAL -->
<div class="card border shadow-sm">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-alt text-info"></i>
            <span class="fw-bold">Histórico de Saída do Agendador (storage/logs/cron.log)</span>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-light" onclick="atualizarStatusCron(true)">
                <i class="fas fa-sync-alt me-1"></i> Recarregar Logs
            </button>
        </div>
    </div>
    <div class="card-body p-3 bg-dark">
        <div class="terminal-container" id="terminalLog">{{ $logRecente ?: 'Aguardando primeiro registro de log no arquivo storage/logs/cron.log...' }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copiarComandoCron() {
        var el = document.getElementById('cronCmdLinha');
        if (el) {
            el.select();
            navigator.clipboard.writeText(el.value);
            window.foco7Notify.success('Comando cron copiado com sucesso!');
        }
    }

    function atualizarStatusCron(mostrarNotificacao = false) {
        var $btnIcon = $('#iconSync');
        $btnIcon.addClass('fa-spin');

        $.ajax({
            url: '{{ route("admin.cron.status") }}',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    if (res.cron_ativo) {
                        $('#heartbeatDot').attr('class', 'pulse-dot');
                        $('#heartbeatIcon').attr('class', 'fas fa-heartbeat fs-3 text-danger');
                        $('#heartbeatTitle').text('Agendador cPanel: Ativo & Operacional');
                        $('#heartbeatAgo').attr('class', 'badge bg-success-subtle text-success ms-1').text('Pulsação contínua (1m)');
                    } else {
                        $('#heartbeatDot').attr('class', 'pulse-dot-amber');
                        $('#heartbeatIcon').attr('class', 'fas fa-heartbeat fs-3 text-secondary');
                        $('#heartbeatTitle').text('Agendador cPanel: Aguardando Próximo Pulso');
                        $('#heartbeatAgo').attr('class', 'badge bg-warning-subtle text-warning ms-1').text('Aguardando');
                    }

                    $('#heartbeatDate').text(res.ultimo_batimento);
                    $('#serverTime').text(res.horario_servidor);

                    if (res.rotinas) {
                        $.each(res.rotinas, function(chave, valor) {
                            if (valor !== 'Nunca executado') {
                                $('#lastRun_' + chave).html('<i class="fas fa-check text-success me-1"></i> ' + valor);
                            } else {
                                $('#lastRun_' + chave).html('<span class="text-muted"><i class="fas fa-hourglass-start me-1"></i> Aguardando horário programado</span>');
                            }
                        });
                    }

                    if (res.log_recente) {
                        $('#terminalLog').text(res.log_recente);
                    }

                    if (mostrarNotificacao) {
                        window.foco7Notify.success('Status do agendador e logs atualizados!');
                    }
                }
            },
            error: function() {
                if (mostrarNotificacao) {
                    window.foco7Notify.error('Não foi possível atualizar o status no momento.');
                }
            },
            complete: function() {
                $btnIcon.removeClass('fa-spin');
            }
        });
    }

    function executarCronManual(tarefa, nome) {
        Swal.fire({
            title: 'Executar ' + nome + '?',
            text: 'Deseja disparar esta rotina no servidor agora?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, executar agora!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Executando Rotina...',
                html: '<div class="spinner-border text-danger" role="status"></div><p class="mt-3">Processando rotina <strong>' + nome + '</strong>...</p>',
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            $.ajax({
                url: '{{ route("admin.cron.executar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tarefa: tarefa
                },
                success: function(res) {
                    if (res.success) {
                        $('#lastRun_' + tarefa).html('<i class="fas fa-check text-success me-1"></i> ' + res.data_hora);
                        Swal.fire({
                            icon: 'success',
                            title: 'Rotina Concluída com Sucesso!',
                            html: '<div class="text-start bg-dark text-white p-3 rounded font-monospace small" style="max-height: 250px; overflow-y: auto;">' + (res.output || res.message) + '</div>',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Fechar'
                        });
                        window.foco7Notify.success(res.message);
                        atualizarStatusCron(false);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha na Execução',
                            text: res.message || 'Ocorreu um erro ao processar a tarefa.',
                            confirmButtonColor: '#d90000'
                        });
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro interno do servidor.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Execução',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                }
            });
        });
    }

    // Atualização automática em segundo plano a cada 30 segundos
    setInterval(function() {
        atualizarStatusCron(false);
    }, 30000);
</script>
@endsection
