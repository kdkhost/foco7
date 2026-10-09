@extends('admin.layouts.app')

@section('title', 'Central de Cron Jobs & Automação')
@section('page_title', 'Central de Tarefas Cron')
@section('page_description', 'Acompanhe as rotinas agendadas no servidor cPanel e execute tarefas sob demanda.')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <p class="text-secondary small mb-0">
            <i class="fas fa-check-circle text-success me-1"></i> A linha única de agendamento está instalada e ativa no crontab do servidor cPanel.
        </p>
    </div>
    <div>
        <span class="badge bg-info-subtle text-info border border-info-subtle p-2">
            <i class="fas fa-server me-1"></i> Horário do Servidor: {{ date('d/m/Y H:i:s') }}
        </span>
    </div>
</div>

<!-- COMANDO OFICIAL PARA CPANEL (LINHA ÚNICA) -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fas fa-terminal text-warning me-2"></i> Linha Única do Agendador (Instalada na Hospedagem cPanel)</span>
        <span class="badge bg-success">EXECUTANDO A CADA MINUTO (* * * * *)</span>
    </div>
    <div class="card-body p-4 bg-body-tertiary">
        <p class="small text-secondary mb-2">
            O Laravel gerencia todas as rotinas internas através de um único ponto de entrada disparado a cada minuto pelo cPanel:
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
<div class="card border shadow-sm">
    <div class="card-header bg-transparent py-3 border-bottom">
        <h6 class="fw-bold mb-0 text-body"><i class="fas fa-tasks text-danger me-2"></i> Rotinas Agendadas do Sistema Foco 7</h6>
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
                                <span class="text-muted">Nunca executado</span>
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

    function executarCronManual(tarefa, nome) {
        window.foco7Confirm('Executar ' + nome + '?', 'Deseja disparar esta rotina em segundo plano agora?', function() {
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
</script>
@endsection
