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
                <a href="{{ route('admin.configuracoes.index') }}#tab-notificacoes" class="btn btn-outline-danger me-2" title="Configurar credenciais OneSignal e preferências de e-mail nas Configurações">
                    <i class="fas fa-sliders-h me-1"></i> Configurações &amp; OneSignal
                </a>
                <button type="button" class="btn btn-outline-danger me-2" id="btnAtivarPushNativo">
                    <i class="fas fa-mobile-screen-button me-1"></i> <span id="lblStatusPush">Ativar Notificações no Celular</span>
                </button>
                <button type="button" class="btn btn-primary me-2" onclick="dispararNotificacaoTeste()">
                    <i class="fas fa-paper-plane me-1"></i> Testar Push &amp; E-mail
                </button>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="marcarTodasComoLidas()">
                    <i class="fas fa-check-double me-1"></i> Marcar Todas Lidas
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="limparTodasNotificacoesLidas()" title="Remover permanentemente do histórico as notificações já lidas">
                    <i class="fas fa-trash-alt me-1"></i> Limpar Lidas
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .notif-item-clicavel {
        cursor: pointer;
        transition: background-color 0.15s ease, box-shadow 0.15s ease;
    }
    .notif-item-clicavel:hover {
        background-color: rgba(13, 110, 253, 0.04) !important;
    }
</style>
@endpush

<section class="content">
    <div class="container-fluid">

        <!-- CARDS DE MÉTRICAS DAS NOTIFICAÇÕES -->
        <div class="row g-3 mb-4">
            <!-- 1. Não Lidas -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('admin.notificacoes.index', ['status' => 'nao_lidas', 'tipo' => $tipo]) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                                <i class="fas fa-envelope-open-text"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary small text-uppercase mb-1">Não Lidas</h6>
                                <h3 class="fw-bold mb-0 text-body" id="cardCountNaoLidas">{{ $totalNaoLidas }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 2. Contatos do Site -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('admin.notificacoes.index', ['status' => $status, 'tipo' => 'contato']) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary small text-uppercase mb-1">Contatos do Site</h6>
                                <h3 class="fw-bold mb-0 text-body">{{ $totalContatos }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 3. Agendamentos -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('admin.notificacoes.index', ['status' => $status, 'tipo' => 'agendamento']) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary small text-uppercase mb-1">Agendamentos</h6>
                                <h3 class="fw-bold mb-0 text-body">{{ $totalAgendamentos }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 4. Depoimentos / Estrelas -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('admin.notificacoes.index', ['status' => $status, 'tipo' => 'depoimento']) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <h6 class="text-secondary small text-uppercase mb-1">Depoimentos / Estrelas</h6>
                                <h3 class="fw-bold mb-0 text-body">{{ $totalDepoimentos }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- COLUNA PRINCIPAL: LISTA DE NOTIFICAÇÕES EM LARGURA TOTAL -->
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header w-100 bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
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

                        <div class="card-tools ms-auto d-flex align-items-center gap-2">
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
                                <div class="bg-body-secondary d-inline-block rounded-circle p-4 mb-3">
                                    <i class="fas fa-bell-slash text-muted fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-secondary">Nenhuma notificação encontrada</h5>
                                <p class="text-muted small">Quando clientes enviarem mensagens pelo formulário ou agendarem, as notificações surgirão aqui.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush" id="listaNotificacoesContainer">
                                @foreach($notificacoes as $notif)
                                    <div class="list-group-item p-3 d-flex align-items-start gap-3 border-bottom notif-item-clicavel {{ !$notif->lida ? 'bg-body-tertiary' : '' }}" id="notif-row-{{ $notif->id }}" onclick="abrirModalNotificacao({{ $notif->id }})" title="Clique para ler os detalhes da notificação">
                                        <div class="bg-{{ $notif->cor }} bg-opacity-10 text-{{ $notif->cor }} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                            <i class="{{ $notif->icone }} fs-5"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                                                <h6 class="fw-bold mb-0 {{ !$notif->lida ? 'text-body' : 'text-body-secondary' }}">
                                                    {{ $notif->titulo }}
                                                    @if(!$notif->lida)
                                                        <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Nova</span>
                                                    @endif
                                                </h6>
                                                <small class="text-body-secondary">
                                                    <i class="far fa-clock me-1"></i> {{ $notif->created_at->diffForHumans() }}
                                                </small>
                                            </div>

                                            <p class="mb-2 text-body-secondary small leading-relaxed">{{ $notif->mensagem }}</p>

                                            @if(!empty($notif->dados) && is_array($notif->dados))
                                                <div class="bg-body-secondary p-2 rounded-2 mb-2 border border-secondary border-opacity-25 small text-body">
                                                    <div class="row g-2">
                                                        @foreach($notif->dados as $key => $val)
                                                             @if(is_scalar($val) && $key !== 'mensagem')
                                                                <div class="col-sm-6 col-md-4">
                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 0.8rem;" onclick="event.stopPropagation(); abrirModalNotificacao({{ $notif->id }})" title="Ler mensagem completa">
                                                    <i class="fas fa-eye me-1"></i> Ler
                                                </button>

                                                @if(!empty($notif->link_acao))
                                                    <a href="{{ route('admin.notificacoes.abrir', $notif->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2 btn-abrir-modulo" data-id="{{ $notif->id }}" style="font-size: 0.8rem;" onclick="event.stopPropagation();">
                                                        <i class="fas fa-external-link-alt me-1"></i> Acessar Módulo
                                                    </a>
                                                @endif

                                                @if(!$notif->lida)
                                                    <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 btn-marcar-lida" style="font-size: 0.8rem;" onclick="event.stopPropagation(); marcarComoLida({{ $notif->id }})" title="Marcar como lida diretamente">
                                                        <i class="fas fa-check me-1"></i> Marcar como Lida
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 ms-auto" style="font-size: 0.8rem;" onclick="event.stopPropagation(); excluirNotificacao({{ $notif->id }})" title="Excluir notificação">
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
        </div>

    </div>

    <!-- MODAL DE LEITURA COMPLETA DA NOTIFICAÇÃO -->
    <div class="modal fade" id="modalLerNotificacao" tabindex="-1" aria-labelledby="modalLerNotificacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div id="modalNotifIconeBg" class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                            <i id="modalNotifIcone" class="fas fa-bell fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-body" id="modalLerNotificacaoLabel">Detalhes da Notificação</h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span id="modalNotifTipoBadge" class="badge bg-secondary small text-uppercase" style="font-size: 0.7rem;">Notificação</span>
                                <span class="text-body-secondary small" id="modalNotifTempo"><i class="far fa-clock me-1"></i> --</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="modalNotifLoading" class="text-center py-5">
                        <div class="spinner-border text-danger mb-2" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="text-body-secondary small mb-0">Carregando detalhes da notificação...</p>
                    </div>

                    <div id="modalNotifConteudo" style="display: none;">
                        <div class="mb-3">
                            <h4 class="fw-bold text-body mb-2" id="modalNotifTitulo"></h4>
                            <div class="text-body-secondary small mb-3">
                                <i class="far fa-calendar-alt me-1 text-danger"></i> <span id="modalNotifDataTexto"></span>
                            </div>
                        </div>
                        
                        <div class="p-3 rounded-3 bg-body-secondary border border-secondary border-opacity-25 mb-4">
                            <h6 class="fw-semibold text-body mb-2 small text-uppercase"><i class="fas fa-align-left me-1"></i> Mensagem:</h6>
                            <p class="mb-0 text-body fs-6 lh-base" id="modalNotifMensagem" style="white-space: pre-line;"></p>
                        </div>

                        <div id="modalNotifDadosContainer" style="display: none;" class="mb-3">
                            <h6 class="fw-semibold text-body mb-2 small text-uppercase"><i class="fas fa-info-circle me-1"></i> Detalhes Adicionais:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0 bg-body">
                                    <tbody id="modalNotifDadosTbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-0 small" role="alert">
                            <i class="fas fa-check-circle fs-5 flex-shrink-0"></i>
                            <div>Esta notificação foi marcada como lida automaticamente.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small" id="modalNotifIdInfo">ID: #0</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" id="modalNotifBtnAcao" class="btn btn-primary" style="display: none;">
                            <i class="fas fa-external-link-alt me-1"></i> Acessar Módulo
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
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

    let notificacaoLidaNoModal = null;

    // Abre o modal de leitura detalhada e marca a notificação como lida automaticamente
    function abrirModalNotificacao(id) {
        const modalEl = document.getElementById('modalLerNotificacao');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        // Resetar campos do modal
        $('#modalNotifLoading').show();
        $('#modalNotifConteudo').hide();
        $('#modalNotifBtnAcao').hide().attr('href', '#');
        $('#modalNotifIdInfo').text(`ID: #${id}`);
        $('#modalNotifDadosContainer').hide();
        $('#modalNotifDadosTbody').empty();

        modal.show();

        $.ajax({
            url: `/admin/notificacoes/${id}/obter`,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.notificacao) {
                    const n = res.notificacao;
                    $('#modalNotifTitulo').text(n.titulo);
                    $('#modalNotifMensagem').text(n.mensagem);
                    $('#modalNotifTempo').html(`<i class="far fa-clock me-1"></i> ${n.tempo_relativo}`);
                    $('#modalNotifDataTexto').text(n.data_formatada);
                    $('#modalNotifTipoBadge').text(n.tipo_formatado);

                    // Estilo de ícone e categoria
                    const cor = n.cor || 'primary';
                    const icone = n.icone || 'fas fa-bell';
                    $('#modalNotifIconeBg').attr('class', `bg-${cor} bg-opacity-10 text-${cor} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0`);
                    $('#modalNotifIcone').attr('class', `${icone} fs-5`);

                    // Detalhes extras
                    if (n.dados && typeof n.dados === 'object') {
                        let rows = '';
                        for (const [chave, valor] of Object.entries(n.dados)) {
                            if (chave !== 'mensagem' && typeof valor !== 'object' && valor !== null) {
                                const chaveFormatada = chave.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                rows += `<tr>
                                    <th class="text-body-secondary fw-semibold" style="width: 35%;">${chaveFormatada}</th>
                                    <td class="text-body fw-normal">${valor}</td>
                                </tr>`;
                            }
                        }
                        if (rows) {
                            $('#modalNotifDadosTbody').html(rows);
                            $('#modalNotifDadosContainer').show();
                        }
                    }

                    // Botão de acesso ao módulo
                    if (n.link_acao) {
                        $('#modalNotifBtnAcao').attr('href', n.link_acao).show();
                    } else {
                        $('#modalNotifBtnAcao').hide();
                    }

                    // Atualizar visual da notificação na lista (se estiver visível)
                    $(`#notif-row-${id}`).removeClass('bg-body-tertiary bg-light bg-opacity-50');
                    $(`#notif-row-${id} h6`).removeClass('text-dark text-body').addClass('text-body-secondary');
                    $(`#notif-row-${id} .badge.bg-danger`).remove();
                    $(`#notif-row-${id} .btn-marcar-lida`).remove();
                    $(`#notif-row-${id} button:contains("Marcar como Lida")`).remove();

                    // Atualizar contadores
                    atualizarBadgesContador(res.nao_lidas_count);

                    if (res.foi_marcada_agora) {
                        notificacaoLidaNoModal = id;
                    }

                    $('#modalNotifLoading').hide();
                    $('#modalNotifConteudo').fadeIn(150);
                } else {
                    $('#modalNotifLoading').html('<p class="text-danger py-3">Não foi possível carregar os detalhes da notificação.</p>');
                }
            },
            error: function () {
                $('#modalNotifLoading').html('<p class="text-danger py-3">Erro de comunicação com o servidor ao carregar a notificação.</p>');
            }
        });
    }

    // Ao fechar o modal, se estivermos na visualização de 'Não Lidas', remove suavemente a notificação da lista
    $('#modalLerNotificacao').on('hidden.bs.modal', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'nao_lidas' && notificacaoLidaNoModal) {
            const id = notificacaoLidaNoModal;
            notificacaoLidaNoModal = null;
            $(`#notif-row-${id}`).fadeOut(300, function() {
                $(this).remove();
                if ($('#listaNotificacoesContainer .list-group-item').length === 0) {
                    window.location.reload();
                }
            });
        }
    });

    // Marca notificação individual como lida
    function marcarComoLida(id) {
        $.ajax({
            url: `/admin/notificacoes/${id}/lida`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('status') === 'nao_lidas') {
                        $(`#notif-row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                            if ($('#listaNotificacoesContainer .list-group-item').length === 0) {
                                window.location.reload();
                            }
                        });
                    } else {
                        $(`#notif-row-${id}`).removeClass('bg-body-tertiary bg-light bg-opacity-50');
                        $(`#notif-row-${id} h6`).removeClass('text-dark text-body').addClass('text-body-secondary');
                        $(`#notif-row-${id} .badge.bg-danger`).remove();
                        $(`#notif-row-${id} .btn-marcar-lida`).remove();
                        $(`#notif-row-${id} button:contains("Marcar como Lida")`).remove();
                    }
                    atualizarBadgesContador(res.nao_lidas_count);
                }
            },
            error: function () {
                toastr.error('Não foi possível marcar a notificação como lida.');
            }
        });
    }

    // Ao clicar em Acessar Módulo, atualiza a interface antes de navegar
    $(document).on('click', '.btn-abrir-modulo', function () {
        const id = $(this).data('id');
        $(`#notif-row-${id}`).removeClass('bg-body-tertiary bg-light bg-opacity-50');
        $(`#notif-row-${id} h6`).removeClass('text-dark text-body').addClass('text-body-secondary');
        $(`#notif-row-${id} .badge.bg-danger`).remove();
        $(`#notif-row-${id} button:contains("Marcar como Lida")`).remove();
        let cur = parseInt($('#cardCountNaoLidas').text()) || 0;
        atualizarBadgesContador(Math.max(0, cur - 1));
    });

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

    // Limpa todas as notificações que já foram lidas do histórico
    function limparTodasNotificacoesLidas() {
        Swal.fire({
            title: 'Limpar notificações lidas?',
            text: 'Todas as notificações que já foram lidas serão removidas permanentemente do histórico.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, limpar histórico',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.notificacoes.limpar_lidas") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            setTimeout(() => window.location.reload(), 600);
                        }
                    },
                    error: function () {
                        toastr.error('Erro ao limpar notificações lidas.');
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
@endsection
