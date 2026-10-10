@extends('admin.layouts.app')

@section('title', 'Central de Notificações | Foco 7')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold fs-3 text-body">
                    <i class="fas fa-bell text-danger me-2"></i> Central de Notificações
                </h1>
                <p class="text-secondary small mb-0">Mensagens do site, agendamentos, avaliações e alertas em tempo real no seu celular e e-mail.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <button type="button" class="btn btn-outline-danger me-2" id="btnAtivarPushNativo">
                    <i class="fas fa-mobile-screen-button me-1"></i> <span id="lblStatusPush">Ativar Notificações no Celular</span>
                </button>
                <button type="button" class="btn btn-primary me-2" onclick="dispararNotificacaoTeste()">
                    <i class="fas fa-paper-plane me-1"></i> Testar Push & E-mail
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="marcarTodasComoLidas()">
                    <i class="fas fa-check-double me-1"></i> Marcar Todas Lidas
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- CARDS DE MÉTRICAS DAS NOTIFICAÇÕES -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Não Lidas</span>
                            <h3 class="fw-bold mb-0 text-danger" id="cardCountNaoLidas">{{ $totalNaoLidas }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                            <i class="fas fa-envelope-open-text fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Contatos do Site</span>
                            <h3 class="fw-bold mb-0 text-warning">{{ $totalContatos }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="fas fa-comment-dots fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Agendamentos</span>
                            <h3 class="fw-bold mb-0 text-primary">{{ $totalAgendamentos }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="fas fa-calendar-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Depoimentos / Estrelas</span>
                            <h3 class="fw-bold mb-0 text-success">{{ $totalDepoimentos }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="fas fa-star fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- COLUNA PRINCIPAL: LISTA DE NOTIFICAÇÕES -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('admin.notificacoes.index', ['status' => 'todas', 'tipo' => $tipo]) }}" class="btn btn-outline-secondary {{ $status === 'todas' ? 'active' : '' }}">
                                Todas ({{ $totalGeral }})
                            </a>
                            <a href="{{ route('admin.notificacoes.index', ['status' => 'nao_lidas', 'tipo' => $tipo]) }}" class="btn btn-outline-danger {{ $status === 'nao_lidas' ? 'active' : '' }}">
                                Não Lidas ({{ $totalNaoLidas }})
                            </a>
                            <a href="{{ route('admin.notificacoes.index', ['status' => 'lidas', 'tipo' => $tipo]) }}" class="btn btn-outline-secondary {{ $status === 'lidas' ? 'active' : '' }}">
                                Lidas
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-secondary mb-0">Tipo:</label>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href = '{{ route('admin.notificacoes.index', ['status' => $status]) }}&tipo=' + this.value">
                                <option value="todos" {{ $tipo === 'todos' ? 'selected' : '' }}>Todos os Tipos</option>
                                <option value="contato" {{ $tipo === 'contato' ? 'selected' : '' }}>Contatos do Site</option>
                                <option value="agendamento" {{ $tipo === 'agendamento' ? 'selected' : '' }}>Agendamentos</option>
                                <option value="depoimento" {{ $tipo === 'depoimento' ? 'selected' : '' }}>Depoimentos</option>
                                <option value="sistema" {{ $tipo === 'sistema' ? 'selected' : '' }}>Sistema</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($notificacoes->isEmpty())
                            <div class="text-center py-5">
                                <div class="bg-light d-inline-block rounded-circle p-4 mb-3">
                                    <i class="fas fa-bell-slash text-muted fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-secondary">Nenhuma notificação encontrada</h5>
                                <p class="text-muted small">Quando clientes enviarem mensagens pelo formulário ou agendarem, as notificações surgirão aqui.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush" id="listaNotificacoesContainer">
                                @foreach($notificacoes as $notif)
                                    <div class="list-group-item p-3 d-flex align-items-start gap-3 border-bottom {{ !$notif->lida ? 'bg-light bg-opacity-50' : '' }}" id="notif-row-{{ $notif->id }}">
                                        <div class="bg-{{ $notif->cor }} bg-opacity-10 text-{{ $notif->cor }} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                            <i class="{{ $notif->icone }} fs-5"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                                                <h6 class="fw-bold mb-0 {{ !$notif->lida ? 'text-dark' : 'text-secondary' }}">
                                                    {{ $notif->titulo }}
                                                    @if(!$notif->lida)
                                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Nova</span>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i> {{ $notif->created_at->diffForHumans() }}
                                                </small>
                                            </div>

                                            <p class="mb-2 text-secondary small leading-relaxed">{{ $notif->mensagem }}</p>

                                            @if(!empty($notif->dados) && is_array($notif->dados))
                                                <div class="bg-light p-2 rounded-2 mb-2 border small">
                                                    <div class="row g-2">
                                                        @foreach($notif->dados as $key => $val)
                                                            @if(is_scalar($val) && $key !== 'mensagem')
                                                                <div class="col-sm-6">
                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                @if(!empty($notif->link_acao))
                                                    <a href="{{ $notif->link_acao }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.8rem;">
                                                        <i class="fas fa-external-link-alt me-1"></i> Acessar Módulo
                                                    </a>
                                                @endif

                                                @if(!$notif->lida)
                                                    <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.8rem;" onclick="marcarComoLida({{ $notif->id }})">
                                                        <i class="fas fa-check me-1"></i> Marcar como Lida
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 ms-auto" style="font-size: 0.8rem;" onclick="excluirNotificacao({{ $notif->id }})" title="Excluir notificação">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-3 border-top d-flex justify-content-center">
                                {{ $notificacoes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- COLUNA LATERAL: CANAIS DE NOTIFICAÇÃO PUSH & E-MAIL -->
            <div class="col-lg-4">
                <!-- CARD PUSH NO CELULAR / NAVEGADOR -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-body">
                            <i class="fas fa-mobile-alt text-danger me-2"></i> Notificações Push no Celular
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="p-3 rounded-3 mb-3 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="small fw-semibold text-secondary">Status no seu Navegador / Celular:</span>
                                <span id="badgePermissaoPush" class="badge bg-secondary">Verificando...</span>
                            </div>
                            <p class="small text-muted mb-0">
                                Permite receber alertas instantâneos no topo da tela do celular ou computador sempre que um cliente enviar mensagem ou fizer agendamento.
                            </p>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger fw-semibold" id="btnAtivarPushLateral">
                                <i class="fas fa-bell me-1"></i> Ativar Alertas no Meu Dispositivo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CARD PREFERÊNCIAS DE E-MAIL DO ADMINISTRADOR -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-body">
                            <i class="fas fa-envelope text-primary me-2"></i> E-mail de Notificações
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <form id="formConfigNotificacoes" action="{{ route('admin.notificacoes.salvar_config') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">E-mail de Destino do Administrador *</label>
                                <input type="email" name="admin_email_notificacoes" class="form-control" value="{{ $adminEmail }}" required placeholder="seu-email@foco7.com.br">
                                <small class="text-muted" style="font-size: 0.75rem;">Todas as novas mensagens e agendamentos serão enviados para este endereço.</small>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="notificar_email_contatos" id="chkNotifContatos" value="1" {{ \App\Models\CmsSetting::get('notificar_email_contatos', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="chkNotifContatos">Notificar por e-mail em novas mensagens de contato</label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="notificar_email_agendamentos" id="chkNotifAgendamentos" value="1" {{ \App\Models\CmsSetting::get('notificar_email_agendamentos', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="chkNotifAgendamentos">Notificar por e-mail em novos agendamentos</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-danger" id="btnSalvarConfigNotif">
                                    <i class="fas fa-save me-1"></i> Salvar Preferências
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    // Inicialização do status do Web Push no dispositivo
    function atualizarStatusPermissaoPush() {
        const badge = document.getElementById('badgePermissaoPush');
        const lbl = document.getElementById('lblStatusPush');

        if (!("Notification" in window)) {
            badge.className = 'badge bg-secondary';
            badge.textContent = 'Não Suportado';
            lbl.textContent = 'Push Não Suportado';
            return;
        }

        if (Notification.permission === 'granted') {
            badge.className = 'badge bg-success';
            badge.textContent = 'Ativo e Autorizado';
            lbl.textContent = 'Push Ativo no Celular';
        } else if (Notification.permission === 'denied') {
            badge.className = 'badge bg-danger';
            badge.textContent = 'Bloqueado no Navegador';
            lbl.textContent = 'Desbloquear no Navegador';
        } else {
            badge.className = 'badge bg-warning text-dark';
            badge.textContent = 'Pendente de Permissão';
            lbl.textContent = 'Ativar no Celular';
        }
    }

    // Solicita permissão para Notificações Web Push
    async function solicitarPermissaoPush() {
        if (!("Notification" in window)) {
            Swal.fire({
                icon: 'warning',
                title: 'Navegador Não Suporta Push',
                text: 'Seu navegador não possui suporte à API de Notificações Push nativas.',
                confirmButtonColor: '#d90000'
            });
            return;
        }

        const permission = await Notification.requestPermission();
        atualizarStatusPermissaoPush();

        if (permission === 'granted') {
            Swal.fire({
                icon: 'success',
                title: 'Notificações Ativadas!',
                text: 'Seu dispositivo agora receberá notificações na tela para cada nova mensagem e agendamento.',
                confirmButtonColor: '#d90000'
            });
            // Notificação de boas-vindas imediata
            new Notification('Foco 7 • Notificações Ativadas', {
                body: 'Você receberá alertas em tempo real direto neste dispositivo!',
                icon: '{{ asset("img/favicon.png") }}'
            });
        } else if (permission === 'denied') {
            Swal.fire({
                icon: 'error',
                title: 'Permissão Negada',
                text: 'As notificações foram bloqueadas. Para ativá-las, libere as permissões no ícone de cadeado na barra de endereços do seu navegador.',
                confirmButtonColor: '#d90000'
            });
        }
    }

    document.getElementById('btnAtivarPushNativo')?.addEventListener('click', solicitarPermissaoPush);
    document.getElementById('btnAtivarPushLateral')?.addEventListener('click', solicitarPermissaoPush);

    // Marca notificação individual como lida
    function marcarComoLida(id) {
        $.ajax({
            url: `/admin/notificacoes/${id}/lida`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    $(`#notif-row-${id}`).removeClass('bg-light bg-opacity-50');
                    $(`#notif-row-${id} h6`).removeClass('text-dark').addClass('text-secondary');
                    $(`#notif-row-${id} .badge.bg-danger`).remove();
                    $(`#notif-row-${id} button:contains("Marcar como Lida")`).remove();
                    atualizarBadgesContador(res.nao_lidas_count);
                }
            },
            error: function () {
                toastr.error('Não foi possível marcar a notificação como lida.');
            }
        });
    }

    // Marca todas as notificações como lidas
    function marcarTodasComoLidas() {
        Swal.fire({
            title: 'Marcar todas como lidas?',
            text: 'Todas as notificações pendentes serão marcadas como lidas.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, marcar todas',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.notificacoes.marcar_todas") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.reload(), 600);
                        }
                    },
                    error: function () {
                        toastr.error('Erro ao marcar todas como lidas.');
                    }
                });
            }
        });
    }

    // Exclui uma notificação
    function excluirNotificacao(id) {
        Swal.fire({
            title: 'Excluir notificação?',
            text: 'Essa notificação será removida do histórico.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/notificacoes/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $(`#notif-row-${id}`).fadeOut(300, function() { $(this).remove(); });
                            atualizarBadgesContador(res.nao_lidas_count);
                        }
                    },
                    error: function () {
                        toastr.error('Erro ao excluir notificação.');
                    }
                });
            }
        });
    }

    // Dispara notificação de teste
    function dispararNotificacaoTeste() {
        $.ajax({
            url: '{{ route("admin.notificacoes.teste") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Teste Enviado!',
                        text: res.message,
                        confirmButtonColor: '#d90000'
                    });

                    if ("Notification" in window && Notification.permission === 'granted') {
                        new Notification(res.notificacao.titulo, {
                            body: res.notificacao.mensagem,
                            icon: '{{ asset("img/favicon.png") }}'
                        });
                    }

                    setTimeout(() => window.location.reload(), 1200);
                }
            },
            error: function () {
                toastr.error('Falha ao disparar notificação de teste.');
            }
        });
    }

    // Salva preferências de e-mail e push
    $('#formConfigNotificacoes').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#btnSalvarConfigNotif');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Preferências');
                if (res.success) {
                    toastr.success(res.message);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Preferências');
                toastr.error('Erro ao salvar preferências.');
            }
        });
    });

    function atualizarBadgesContador(count) {
        $('#cardCountNaoLidas').text(count);
        $('.badge-notificacoes-count').text(count);
        if (count > 0) {
            $('.badge-notificacoes-count').show();
        } else {
            $('.badge-notificacoes-count').hide();
        }
    }

    $(document).ready(function () {
        atualizarStatusPermissaoPush();
    });
</script>
@endpush
