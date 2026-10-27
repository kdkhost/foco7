@extends('admin.layouts.app')

@section('title', 'Templates de E-mails Transacionais')
@section('page_title', 'Central de Templates de E-mails')
@section('page_description', 'Gerencie todos os e-mails transacionais do sistema com rota única e layout master responsivo.')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <p class="text-secondary small mb-0">
            <i class="fas fa-shield-alt text-success me-1"></i> Todos os e-mails utilizam a rota única oficial e o layout master da Foco 7 com dimensionamento dinâmico e responsivo.
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.emails.create') }}" class="btn btn-danger fw-bold shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Criar Novo Template
        </a>
        <button type="button" class="btn btn-outline-danger fw-bold" onclick="abrirModalTesteGlobal()">
            <i class="fas fa-paper-plane me-1"></i> Enviar Demonstração
        </button>
    </div>
</div>

<!-- STATUS DO SERVIDOR SMTP EXCLUSIVO -->
<div class="card border shadow-sm mb-4">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger-subtle text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-at fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-body">Conta SMTP Exclusiva do Sistema</h6>
                        <p class="text-secondary small mb-0 font-monospace">
                            <span class="status-badge-pill status-badge-pago me-2"><span class="status-dot"></span><span>SMTP Ativo</span></span>
                            Remetente Oficial: <strong>{{ $smtpConfig['from'] }}</strong> &bull; Servidor: {{ $smtpConfig['host'] }}:{{ $smtpConfig['port'] }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('admin.configuracoes.index') }}#tab-smtp" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-sliders-h me-1"></i> Configurar SMTP
                </a>
            </div>
        </div>
    </div>
</div>

<!-- LISTA ORGANIZADA DE TEMPLATES -->
<div class="card border shadow-sm">
    <div class="card-header w-100 bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
        <h6 class="fw-bold mb-0 text-body"><i class="fas fa-list-alt text-danger me-2"></i> Modelos de E-mails Cadastrados ({{ count($templates) }})</h6>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-auto">Layout Master Foco 7</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 220px;">Nome do Template</th>
                    <th style="min-width: 250px;">Assunto Atual</th>
                    <th>Finalidade / Gatilho</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-end" style="min-width: 180px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $chave => $tpl)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-secondary-subtle p-2 rounded text-danger">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <strong class="text-body d-block">{{ $tpl['nome'] }}</strong>
                                <span class="font-monospace small text-muted" style="font-size: 11px;">chave: {{ $chave }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-body small text-truncate d-inline-block" style="max-width: 320px;" title="{{ $tpl['assunto_atual'] }}">
                            {{ $tpl['assunto_atual'] }}
                        </span>
                    </td>
                    <td>
                        <span class="text-secondary small">{{ $tpl['descricao'] }}</span>
                    </td>
                    <td class="text-center">
                        @if(!empty($tpl['sistema']))
                            <span class="status-badge-pill status-badge-cancelado">
                                <span class="status-dot"></span>
                                <i class="fas fa-lock"></i>
                                <span>Sistema</span>
                            </span>
                        @else
                            <span class="status-badge-pill status-badge-concluido">
                                <span class="status-dot"></span>
                                <i class="fas fa-magic"></i>
                                <span>Custom</span>
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.emails.edit', $chave) }}" class="btn btn-outline-danger fw-semibold" title="Abrir em Página Dedicada com Preview em Tempo Real">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            <button type="button" class="btn btn-outline-secondary" title="Disparar Teste Deste Template" onclick="dispararTesteIndividual('{{ $chave }}', '{{ addslashes($tpl['nome']) }}')">
                                <i class="fas fa-vial"></i>
                            </button>
                            @if(empty($tpl['sistema']))
                            <button type="button" class="btn btn-outline-danger" title="Excluir Template" onclick="excluirTemplate('{{ $chave }}', '{{ addslashes($tpl['nome']) }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE ENVIO DE TESTE -->
<div class="modal fade" id="modalTesteEmail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-paper-plane text-danger me-2"></i> Enviar Demonstração de E-mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.emails.testar') }}" method="POST" class="ajax-form" id="formTesteEmail">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Template a Disparar</label>
                        <select name="template_chave" id="modalTemplateChave" class="form-select" required>
                            @foreach($templates as $k => $t)
                                <option value="{{ $k }}">{{ $t['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-mail de Destino</label>
                        <input type="email" name="email_destinatario" id="modalEmailDestinatario" class="form-control" value="" autocomplete="off" data-lpignore="true" required placeholder="Digite o e-mail de destino...">
                        <small class="text-secondary">O e-mail será compilado na rota única oficial com o layout master responsivo e enviado via SMTP exclusivo.</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-paper-plane me-1"></i> Disparar Teste Agora</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalTesteGlobal() {
        $('#modalEmailDestinatario').val('');
        new bootstrap.Modal(document.getElementById('modalTesteEmail')).show();
    }

    function dispararTesteIndividual(chave, nome) {
        $('#modalTemplateChave').val(chave);
        $('#modalEmailDestinatario').val('');
        new bootstrap.Modal(document.getElementById('modalTesteEmail')).show();
    }

    $(function () {
        $('#modalEmailDestinatario').val('');
        var modalEl = document.getElementById('modalTesteEmail');
        if (modalEl) {
            modalEl.addEventListener('show.bs.modal', function () {
                $('#modalEmailDestinatario').val('');
            });
            modalEl.addEventListener('hidden.bs.modal', function () {
                $('#modalEmailDestinatario').val('');
            });
        }
    });

    function excluirTemplate(chave, nome) {
        window.foco7Confirm('Excluir Template?', 'Deseja realmente remover o template personalizado "' + nome + '"?', function() {
            $.ajax({
                url: '{{ url("admin/emails") }}/' + chave,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { window.location.reload(); }, 800);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: res.message || 'Falha ao excluir o template.',
                            confirmButtonColor: '#d90000'
                        });
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro interno do servidor.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                }
            });
        });
    }
</script>
@endsection
