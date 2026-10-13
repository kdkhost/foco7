@extends('admin.layouts.app')

@section('title', 'Central de Cron Jobs & Automação')
@section('page_title', 'Central de Tarefas Cron')
@section('page_description', 'Gerencie as rotinas agendadas no servidor, cadastre novas tarefas e execute manutenções com monitoramento em tempo real.')

@section('content')
<style>
    /* Pulso verde do Agendador (Heartbeat) */
    .pulse-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        display: inline-block;
        background-color: #00ff88;
        box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7);
        animation: pulseAnimationGreen 1.8s infinite;
    }
    .pulse-dot-amber {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        display: inline-block;
        background-color: #ffc107;
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        animation: pulseAnimationAmber 1.8s infinite;
    }
    @keyframes pulseAnimationGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 9px rgba(0, 255, 136, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
    }
    @keyframes pulseAnimationAmber {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 9px rgba(255, 193, 7, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
    .pulse-dot-inline {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        background-color: #00ff88;
        box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7);
        animation: pulseAnimationGreen 1.8s infinite;
        vertical-align: middle;
    }

    /* Terminal de Logs */
    .terminal-container {
        background-color: #0d1117;
        color: #39d353;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 13px;
        border-radius: 8px;
        padding: 16px;
        max-height: 280px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
        border: 1px solid #30363d;
    }

    /* ANIMAÇÕES DO BOTÃO DO CRON (Executar Agora) */
    .btn-cron-executar {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        padding: 5px 14px;
        font-weight: 600;
        font-size: 12.5px;
        letter-spacing: 0.3px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(217, 0, 0, 0.35);
        background: linear-gradient(135deg, rgba(217, 0, 0, 0.12) 0%, rgba(180, 0, 0, 0.25) 100%);
        color: #ff4d4d;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Efeito Shimmer reflexivo no botão */
    .btn-cron-executar::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -60%;
        width: 40%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(25deg);
        transition: none;
    }

    .btn-cron-executar:hover::after {
        left: 120%;
        transition: all 0.75s ease-in-out;
    }

    .btn-cron-executar:hover {
        background: linear-gradient(135deg, #d90000 0%, #b30000 100%);
        color: #ffffff !important;
        border-color: #d90000;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(217, 0, 0, 0.45);
    }

    /* Ícone interno animado no hover */
    .btn-cron-executar .cron-icon {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    .btn-cron-executar:hover .cron-icon {
        animation: cronIconPulse 0.5s ease-in-out infinite alternate;
    }

    @keyframes cronIconPulse {
        0% { transform: scale(1) translateX(0); }
        100% { transform: scale(1.25) translateX(2px); }
    }

    /* Estado Executando / Loading */
    .btn-cron-executar.is-executing {
        background: linear-gradient(135deg, #b30000 0%, #800000 100%) !important;
        color: #fff !important;
        pointer-events: none;
        box-shadow: 0 0 0 0 rgba(217, 0, 0, 0.7);
        animation: cronRadarGlow 1.2s infinite ease-out;
    }

    .btn-cron-executar.is-executing .cron-icon {
        animation: cronSpin 0.7s linear infinite !important;
    }

    @keyframes cronSpin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes cronRadarGlow {
        0% { box-shadow: 0 0 0 0 rgba(217, 0, 0, 0.8); }
        70% { box-shadow: 0 0 0 10px rgba(217, 0, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 0, 0, 0); }
    }

    /* Botão Cadastrar com gradiente animado */
    .btn-novo-cron {
        background: linear-gradient(135deg, #d90000 0%, #ff2a2a 100%);
        border: none;
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(217, 0, 0, 0.3);
        transition: all 0.25s ease;
    }
    .btn-novo-cron:hover {
        background: linear-gradient(135deg, #b30000 0%, #d90000 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(217, 0, 0, 0.45);
    }
</style>

<!-- STATUS HEADER / HEARTBEAT CARD -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-circle bg-body-secondary border border-secondary border-opacity-25 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
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
                    <button type="button" class="btn btn-novo-cron btn-sm px-3" onclick="abrirModalNovoCron()">
                        <i class="fas fa-plus-circle me-1"></i> Nova Tarefa Cron
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border shadow-sm h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-secondary small"><i class="fas fa-clock text-danger me-1"></i> Horário Atual (São Paulo)</span>
                    <span class="badge bg-success-subtle text-success small py-1 px-2 d-inline-flex align-items-center gap-1">
                        <span class="pulse-dot-inline"></span> Tempo Real
                    </span>
                </div>
                <h5 id="serverTime" class="mb-0 fw-bold text-body font-monospace" data-server-ts="{{ $timestampServidorMs ?? (now()->setTimezone('America/Sao_Paulo')->getTimestamp() * 1000) }}">{{ $horarioServidorSp ?? now()->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s') }}</h5>
                <small class="text-secondary mt-1">Sincronizado continuamente com o fuso de São Paulo (UTC-3)</small>
            </div>
        </div>
    </div>
</div>

<!-- COMANDO OFICIAL PARA CPANEL (LINHA ÚNICA) -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold"><i class="fas fa-terminal text-warning me-2"></i> Linha Mestra do Agendador (Instalada na Hospedagem cPanel)</span>
        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> EXECUTANDO A CADA MINUTO NO CPANEL (* * * * *)</span>
    </div>
    <div class="card-body p-4 bg-body-tertiary">
        <p class="small text-secondary mb-2">
            O Laravel orquestra centralizadamente todas as rotinas internas e tarefas dinâmicas cadastradas através de um único gatilho no Crontab da sua hospedagem:
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
    <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-0 text-body"><i class="fas fa-tasks text-danger me-2"></i> Rotinas Agendadas & Tarefas Cadastradas</h6>
            <small class="text-secondary">Todas as tarefas ativas abaixo são processadas pelo agendador do Laravel automaticamente.</small>
        </div>
        <div>
            <button type="button" class="btn btn-novo-cron btn-sm px-3" onclick="abrirModalNovoCron()">
                <i class="fas fa-plus me-1"></i> Cadastrar Outra Tarefa
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tabelaCronJobs">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 260px;">Rotina / Descrição</th>
                    <th>Comando / Alvo</th>
                    <th>Frequência</th>
                    <th>Status</th>
                    <th>Última Execução</th>
                    <th class="text-end" style="min-width: 210px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cronJobs as $job)
                <tr id="row_job_{{ $job->id }}">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-body d-block">{{ $job->nome }}</strong>
                            @if($job->sistema)
                                <span class="badge bg-secondary-subtle text-secondary border" title="Rotina vital do sistema">Sistema</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border">Manual</span>
                            @endif
                        </div>
                        <small class="text-secondary">{{ $job->descricao ?: 'Sem descrição informada.' }}</small>
                    </td>
                    <td>
                        @if($job->tipo === 'url')
                            <code class="text-danger fw-bold bg-danger-subtle px-2 py-1 rounded small" title="{{ $job->comando }}">
                                <i class="fas fa-globe me-1"></i> GET URL
                            </code>
                        @else
                            <code class="text-danger fw-bold bg-danger-subtle px-2 py-1 rounded" style="font-size: 12px;">php artisan {{ $job->comando }}</code>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-body border" title="Expressão Cron: {{ $job->expressao_cron }}">
                            <i class="fas fa-history me-1 text-secondary"></i> {{ $job->frequencia_descricao ?: $job->expressao_cron }}
                        </span>
                    </td>
                    <td>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggle_job_{{ $job->id }}" {{ $job->ativo ? 'checked' : '' }} onchange="toggleJobStatus({{ $job->id }})">
                            <label class="form-check-label small text-secondary" for="toggle_job_{{ $job->id }}" id="label_toggle_{{ $job->id }}">
                                {{ $job->ativo ? 'Ativo' : 'Pausado' }}
                            </label>
                        </div>
                    </td>
                    <td>
                        <span id="lastRun_{{ $job->id }}" class="small text-secondary font-monospace">
                            @if($job->ultima_execucao)
                                <i class="fas fa-check text-success me-1"></i> {{ $job->ultima_execucao_formatada }}
                            @else
                                <span class="text-muted"><i class="fas fa-hourglass-start me-1"></i> Aguardando</span>
                            @endif
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex align-items-center gap-1">
                            <!-- BOTÃO ANIMADO DE EXECUÇÃO SOB DEMANDA -->
                            <button type="button" class="btn btn-cron-executar" id="btnRun_{{ $job->id }}" onclick="executarCronManual({{ $job->id }}, '{{ addslashes($job->nome) }}')">
                                <i class="fas fa-play cron-icon"></i>
                                <span class="cron-btn-text">Executar Agora</span>
                            </button>

                            <!-- BOTÃO DE EDIÇÃO -->
                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Editar Tarefa" onclick='abrirModalEditarCron(@json($job))'>
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- BOTÃO DE EXCLUSÃO (APENAS NÃO-SISTEMA) -->
                            @if(!$job->sistema)
                            <button type="button" class="btn btn-outline-danger btn-sm" title="Excluir Tarefa" onclick="excluirCronJob({{ $job->id }}, '{{ addslashes($job->nome) }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-secondary">
                        <i class="fas fa-info-circle me-1"></i> Nenhuma rotina cadastrada no momento. Clique em "+ Cadastrar Outra Tarefa" para adicionar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- CONSOLE DE LOGS EM TEMPO REAL -->
<div class="card border shadow-sm">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-alt text-danger"></i>
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

<!-- MODAL CADASTRAR / EDITAR TAREFA CRON -->
<div class="modal fade" id="modalCronJob" tabindex="-1" aria-labelledby="modalCronJobLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="formCronJob" onsubmit="salvarCronJob(event)">
                @csrf
                <input type="hidden" id="cronJobId" name="id" value="">
                
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="modalCronJobLabel">
                        <i class="fas fa-clock text-danger me-2"></i> Cadastrar Nova Tarefa Cron
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="cronNome" class="form-label fw-bold small text-body">Nome da Tarefa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cronNome" name="nome" placeholder="Ex: Limpeza de Arquivos Temporários" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="cronTipo" class="form-label fw-bold small text-body">Tipo de Execução <span class="text-danger">*</span></label>
                            <select class="form-select" id="cronTipo" name="tipo" onchange="ajustarTipoComando()">
                                <option value="artisan">Comando Artisan (CLI)</option>
                                <option value="url">Requisição Web URL (GET)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="cronComando" class="form-label fw-bold small text-body" id="labelComando">
                                Comando Artisan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control font-monospace" id="cronComando" name="comando" placeholder="Ex: cache:clear ou faturas:verificar-vencidas" required>
                            <small class="text-secondary" id="helpComando">Digite o comando do Laravel sem o prefixo "php artisan".</small>
                        </div>

                        <div class="col-12">
                            <label for="cronDescricao" class="form-label fw-bold small text-body">Descrição da Tarefa</label>
                            <textarea class="form-control" id="cronDescricao" name="descricao" rows="2" placeholder="Explique brevemente a função desta rotina no sistema..."></textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="seletorFrequencia" class="form-label fw-bold small text-body">Frequência Programada</label>
                            <select class="form-select" id="seletorFrequencia" onchange="aplicarPresetFrequencia()">
                                <option value="* * * * *|A cada minuto">A cada minuto (* * * * *)</option>
                                <option value="*/5 * * * *|A cada 5 minutos">A cada 5 minutos (*/5 * * * *)</option>
                                <option value="*/15 * * * *|A cada 15 minutos">A cada 15 minutos (*/15 * * * *)</option>
                                <option value="*/30 * * * *|A cada 30 minutos">A cada 30 minutos (*/30 * * * *)</option>
                                <option value="0 * * * *|A cada hora">A cada hora (0 * * * *)</option>
                                <option value="0 0 * * *|Diariamente às 00:00">Diariamente às 00:00 (0 0 * * *)</option>
                                <option value="0 6 * * *|Diariamente às 06:00">Diariamente às 06:00 (0 6 * * *)</option>
                                <option value="0 8 * * *|Diariamente às 08:00">Diariamente às 08:00 (0 8 * * *)</option>
                                <option value="0 0 * * 0|Semanalmente aos Domingos">Semanalmente aos Domingos (0 0 * * 0)</option>
                                <option value="custom">Personalizado (Expressão manual)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="cronExpressao" class="form-label fw-bold small text-body">Expressão Cron <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="cronExpressao" name="expressao_cron" value="* * * * *" required>
                            <input type="hidden" id="cronFrequenciaDesc" name="frequencia_descricao" value="A cada minuto">
                            <small class="text-secondary">Padrão Unix: Minuto Hora Dia Mês DiaSemana</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="cronAtivo" name="ativo" value="1" checked>
                                <label class="form-check-label fw-bold small text-body" for="cronAtivo">Ativar esta rotina imediatamente no agendador</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4" id="btnSalvarCron">
                        <i class="fas fa-save me-1"></i> Salvar Tarefa
                    </button>
                </div>
            </form>
        </div>
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

    function ajustarTipoComando() {
        var tipo = $('#cronTipo').val();
        if (tipo === 'url') {
            $('#labelComando').html('URL Completa (Endpoint GET) <span class="text-danger">*</span>');
            $('#cronComando').attr('placeholder', 'Ex: https://foco7.com.br/api/cron/processar');
            $('#helpComando').text('URL pública ou rota que deve receber uma requisição GET automática.');
        } else {
            $('#labelComando').html('Comando Artisan <span class="text-danger">*</span>');
            $('#cronComando').attr('placeholder', 'Ex: cache:clear ou faturas:verificar-vencidas');
            $('#helpComando').text('Digite o comando do Laravel sem o prefixo "php artisan".');
        }
    }

    function aplicarPresetFrequencia() {
        var val = $('#seletorFrequencia').val();
        if (val !== 'custom') {
            var parts = val.split('|');
            $('#cronExpressao').val(parts[0]);
            $('#cronFrequenciaDesc').val(parts[1]);
        }
    }

    function abrirModalNovoCron() {
        $('#formCronJob')[0].reset();
        $('#cronJobId').val('');
        $('#modalCronJobLabel').html('<i class="fas fa-clock text-danger me-2"></i> Cadastrar Nova Tarefa Cron');
        $('#cronTipo').prop('disabled', false);
        $('#cronComando').prop('readonly', false);
        $('#seletorFrequencia').val('* * * * *|A cada minuto');
        $('#cronExpressao').val('* * * * *');
        $('#cronFrequenciaDesc').val('A cada minuto');
        $('#cronAtivo').prop('checked', true);
        ajustarTipoComando();
        new bootstrap.Modal(document.getElementById('modalCronJob')).show();
    }

    function abrirModalEditarCron(job) {
        $('#formCronJob')[0].reset();
        $('#cronJobId').val(job.id);
        $('#modalCronJobLabel').html('<i class="fas fa-edit text-danger me-2"></i> Editar Tarefa: ' + job.nome);
        $('#cronNome').val(job.nome);
        $('#cronTipo').val(job.tipo);
        $('#cronComando').val(job.comando);
        $('#cronDescricao').val(job.descricao);
        $('#cronExpressao').val(job.expressao_cron);
        $('#cronFrequenciaDesc').val(job.frequencia_descricao);
        $('#cronAtivo').prop('checked', job.ativo);

        if (job.sistema) {
            $('#cronTipo').prop('disabled', true);
            $('#cronComando').prop('readonly', true);
        } else {
            $('#cronTipo').prop('disabled', false);
            $('#cronComando').prop('readonly', false);
        }

        ajustarTipoComando();
        new bootstrap.Modal(document.getElementById('modalCronJob')).show();
    }

    function salvarCronJob(e) {
        e.preventDefault();
        var id = $('#cronJobId').val();
        var isEdit = id ? true : false;
        var url = isEdit ? '{{ url("admin/cron/atualizar") }}/' + id : '{{ route("admin.cron.store") }}';
        var method = isEdit ? 'PUT' : 'POST';

        var dados = {
            _token: '{{ csrf_token() }}',
            nome: $('#cronNome').val(),
            tipo: $('#cronTipo').val(),
            comando: $('#cronComando').val(),
            descricao: $('#cronDescricao').val(),
            expressao_cron: $('#cronExpressao').val(),
            frequencia_descricao: $('#cronFrequenciaDesc').val(),
            ativo: $('#cronAtivo').is(':checked') ? 1 : 0
        };

        var $btn = $('#btnSalvarCron');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

        $.ajax({
            url: url,
            type: method,
            data: dados,
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Tarefa');
                if (res.success) {
                    var modalEl = document.getElementById('modalCronJob');
                    var modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                    window.foco7Notify.success(res.message);
                    setTimeout(function() {
                        window.location.reload();
                    }, 800);
                } else {
                    window.foco7Notify.error(res.message || 'Erro ao salvar rotina.');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Tarefa');
                var msg = 'Ocorreu um erro ao processar.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                window.foco7Notify.error(msg);
            }
        });
    }

    function toggleJobStatus(id) {
        $.ajax({
            url: '{{ url("admin/cron/toggle") }}/' + id,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#label_toggle_' + id).text(res.ativo ? 'Ativo' : 'Pausado');
                    window.foco7Notify.success(res.message);
                }
            },
            error: function() {
                window.foco7Notify.error('Falha ao alternar status da rotina.');
            }
        });
    }

    function excluirCronJob(id, nome) {
        Swal.fire({
            title: 'Excluir ' + nome + '?',
            text: 'Tem certeza que deseja remover esta tarefa agendada do sistema?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: '{{ url("admin/cron/excluir") }}/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#row_job_' + id).fadeOut(300, function() { $(this).remove(); });
                        window.foco7Notify.success(res.message);
                    } else {
                        window.foco7Notify.error(res.message || 'Não foi possível excluir.');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao excluir rotina.';
                    window.foco7Notify.error(msg);
                }
            });
        });
    }

    // DISPARO MANUAL COM ANIMAÇÕES NO BOTÃO DO CRON
    function executarCronManual(jobId, nome) {
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

            // Ativa animação premium dentro do botão
            var $btn = $('#btnRun_' + jobId);
            $btn.addClass('is-executing');
            $btn.find('.cron-icon').attr('class', 'fas fa-cog cron-icon');
            $btn.find('.cron-btn-text').text('Processando...');

            Swal.fire({
                title: 'Executando Rotina...',
                html: '<div class="spinner-border text-danger" role="status"></div><p class="mt-3">Processando <strong>' + nome + '</strong> no servidor...</p>',
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            $.ajax({
                url: '{{ route("admin.cron.executar") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: jobId
                },
                success: function(res) {
                    // Reseta animação do botão
                    $btn.removeClass('is-executing');
                    $btn.find('.cron-icon').attr('class', 'fas fa-play cron-icon');
                    $btn.find('.cron-btn-text').text('Executar Agora');

                    if (res.success) {
                        $('#lastRun_' + jobId).html('<i class="fas fa-check text-success me-1"></i> ' + res.data_hora);
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
                    $btn.removeClass('is-executing');
                    $btn.find('.cron-icon').attr('class', 'fas fa-play cron-icon');
                    $btn.find('.cron-btn-text').text('Executar Agora');

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
                    
                    if (res.timestamp_ms) {
                        serverTimestampBase = res.timestamp_ms;
                        localTimestampBase = Date.now();
                        serverOffsetMs = serverTimestampBase - localTimestampBase;
                        atualizarRelogioServidor();
                    } else if (res.horario_servidor) {
                        $('#serverTime').text(res.horario_servidor);
                    }

                    if (res.jobs) {
                        $.each(res.jobs, function(id, job) {
                            if (job.ultima_execucao && job.ultima_execucao !== 'Nunca executado') {
                                $('#lastRun_' + id).html('<i class="fas fa-check text-success me-1"></i> ' + job.ultima_execucao);
                            }
                        });
                    }

                    if (res.log_recente) {
                        $('#terminalLog').text(res.log_recente);
                    }

                    if (mostrarNotificacao) {
                        window.foco7Notify.success('Status do agendador e logs sincronizados!');
                    }
                }
            },
            error: function() {
                if (mostrarNotificacao) {
                    window.foco7Notify.error('Não foi possível sincronizar o status no momento.');
                }
            },
            complete: function() {
                $btnIcon.removeClass('fa-spin');
            }
        });
    }

    // =========================================================================
    // RELÓGIO EM TEMPO REAL SINCRONIZADO COM SÃO PAULO (UTC-3)
    // =========================================================================
    let serverTimestampBase = parseInt($('#serverTime').data('server-ts')) || Date.now();
    let localTimestampBase = Date.now();
    let serverOffsetMs = serverTimestampBase - localTimestampBase;

    function formatarDataHoraSaoPaulo(dateObj) {
        return new Intl.DateTimeFormat('pt-BR', {
            timeZone: 'America/Sao_Paulo',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).format(dateObj);
    }

    function atualizarRelogioServidor() {
        const agoraServidor = new Date(Date.now() + serverOffsetMs);
        const formatado = formatarDataHoraSaoPaulo(agoraServidor);
        $('#serverTime').text(formatado);
    }

    // Dispara a cada 1 segundo em tempo real
    atualizarRelogioServidor();
    setInterval(atualizarRelogioServidor, 1000);

    // Auto-atualização periódica em background a cada 30 segundos
    setInterval(function() {
        atualizarStatusCron(false);
    }, 30000);
</script>
@endsection
