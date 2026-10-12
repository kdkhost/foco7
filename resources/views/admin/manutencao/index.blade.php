@extends('admin.layouts.app')

@section('title', 'Modo de Manutenção')
@section('page_title', 'Controle do Modo de Manutenção')
@section('page_description', 'Ative ou desative o modo de manutenção, personalize o wallpaper com upload drag & drop, defina IPs liberados e configure a contagem regressiva.')

@section('content')
<div class="row g-4">
    <!-- Card de Ativação Rápida & Status Global -->
    <div class="col-12">
        <div class="card shadow-sm border-0 {{ $statusAtivo ? 'bg-danger text-white' : 'bg-body' }}">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="fas fa-power-off"></i>
                        Status Atual: <span class="badge {{ $statusAtivo ? 'bg-light text-danger' : 'bg-success text-white' }} px-3 py-2 text-uppercase fs-6">{{ $statusAtivo ? 'MODO DE MANUTENÇÃO ATIVADO' : 'SITE ONLINE (PÚBLICO)' }}</span>
                    </h4>
                    <p class="mb-0 {{ $statusAtivo ? 'text-white-50' : 'text-secondary' }} small">
                        {{ $statusAtivo ? 'Visitantes comuns visualizam a página de manutenção com contagem regressiva e wallpaper customizado. Administradores logados e IPs liberados têm acesso normal.' : 'A Landing Page oficial está 100% acessível para todos os visitantes da internet.' }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('admin.manutencao.preview') }}" target="_blank" class="btn {{ $statusAtivo ? 'btn-outline-light' : 'btn-outline-secondary' }} fw-bold px-3 py-2">
                        <i class="fas fa-eye me-1"></i> Visualizar Página
                    </a>
                    <button type="button" class="btn {{ $statusAtivo ? 'btn-light text-danger fw-bold' : 'btn-danger fw-bold' }} px-4 py-2" id="btnToggleManutencao" data-status="{{ $statusAtivo ? '1' : '0' }}">
                        <i class="fas fa-power-off me-1"></i> {{ $statusAtivo ? 'DESATIVAR MANUTENÇÃO' : 'ATIVAR MANUTENÇÃO AGORA' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Configuração Detalhada -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-cog text-danger me-2"></i> Configurações da Página de Manutenção</h5>
                <span class="badge bg-secondary-subtle text-secondary small">Salvamento AJAX sem perda de campos</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.manutencao.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form keep-data" id="formManutencao">
                    @csrf

                    <!-- Interruptor de Status no Formulário -->
                    <div class="card p-3 mb-4 bg-body-tertiary border border-secondary border-opacity-25 rounded-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    <i class="fas fa-toggle-on text-danger me-2"></i> Ativação do Modo de Manutenção
                                </h6>
                                <p class="small text-secondary mb-0">
                                    Marque para manter o site bloqueado para o público geral ou desmarque para deixá-lo online.
                                </p>
                            </div>
                            <div class="form-check form-switch fs-4 mb-0">
                                <input class="form-check-input" type="checkbox" name="manutencao_ativa" id="switchManutencaoAtiva" value="1" {{ $statusAtivo ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Upload de Imagem de Fundo (Wallpaper) com Drag & Drop (FocoDropzone) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">
                                <i class="fas fa-image text-danger me-1"></i> Imagem de Fundo da Manutenção (Wallpaper / Background)
                            </label>
                            @if(!empty($bgImagem))
                            <span class="badge bg-dark-subtle text-body small font-monospace">Ativo: {{ basename($bgImagem) }}</span>
                            @endif
                        </div>
                        <div class="foco-dropzone"
                             data-input-name="manutencao_bg"
                             data-ratio="1920 × 1080 px (16:9 Panorâmica Alta Definição)"
                             data-max-size="5MB"
                             data-accept="image/jpeg,image/png,image/webp"
                             data-current-preview="{{ !empty($bgImagem) ? asset($bgImagem) : '' }}">
                        </div>
                        <div class="form-text small mt-1">
                            Arraste e solte o wallpaper aqui ou clique para selecionar. Formatos permitidos: JPG, PNG, WEBP. Tamanho máximo: 5MB.
                        </div>

                        @if(!empty($bgImagem) && $bgImagem !== '/img/manutencao-bg.jpg')
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remover_manutencao_bg" value="1" id="chkRemoverBg">
                            <label class="form-check-label small text-danger fw-semibold" for="chkRemoverBg">
                                <i class="fas fa-undo-alt me-1"></i> Remover imagem personalizada e restaurar wallpaper padrão do sistema
                            </label>
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Título da Página de Manutenção *</label>
                        <input type="text" name="manutencao_titulo" class="form-control" value="{{ $titulo }}" placeholder="Ex: SITE EM MANUTENÇÃO PROGRAMADA" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mensagem Explicativa ao Visitante *</label>
                        <textarea name="manutencao_mensagem" rows="3" class="form-control" placeholder="Mensagem exibida aos visitantes..." required>{{ $mensagem }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Previsão de Término (Contagem Regressiva)</label>
                            <input type="datetime-local" name="manutencao_previsao" class="form-control" value="{{ !empty($previsao) ? date('Y-m-d\TH:i', strtotime($previsao)) : '' }}">
                            <div class="form-text small">Deixe em branco para ocultar os blocos de contagem regressiva.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Token de Bypass para Dispositivos Autorizados *</label>
                            <input type="text" name="manutencao_token" class="form-control" value="{{ $token }}" required>
                            <div class="form-text small">Dispositivos com esse token acessam normalmente sem bloqueio.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">IPs Liberados (Separados por vírgula)</label>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" id="btnAddMeuIp" data-ip="{{ $meuIp }}">
                                <i class="fas fa-plus-circle me-1"></i> Adicionar meu IP atual ({{ $meuIp }})
                            </button>
                        </div>
                        <textarea name="manutencao_ips_liberados" id="manutencao_ips_liberados" rows="2" class="form-control" placeholder="177.192.4.34, 201.55.3.10">{{ $ips }}</textarea>
                        <div class="form-text small">Deixe sem IPs de rede local (127.0.0.1) se desejar testar a tela de manutenção no próprio computador em aba anônima.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="manutencao_mostrar_whatsapp" id="chkWhats" value="1" {{ $mostrarWhatsApp ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="chkWhats">Exibir Botão do WhatsApp na Manutenção</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="manutencao_mostrar_redes" id="chkRedes" value="1" {{ $mostrarRedes ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="chkRedes">Exibir Links das Redes Sociais</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold shadow-sm" id="btnSalvarManutencao">
                        <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DE MANUTENÇÃO
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instruções e Bypass -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-shield-alt text-danger me-2"></i> Como Funciona a Liberação</h6>
            </div>
            <div class="card-body p-4 small text-secondary">
                <p>1. <strong>Administradores Logados:</strong> Enquanto autenticado neste painel, você visualiza a Landing Page normalmente. Uma barra vermelha no topo da página avisará que a manutenção está ativa para visitantes comuns.</p>
                <p>2. <strong>Bypass por IP:</strong> Se adicionar seu IP na lista de autorizados, qualquer navegador na sua rede acessa sem bloqueio.</p>
                <p>3. <strong>Link de Bypass Direto:</strong> Envie este link exclusivo para clientes VIP ou técnicos liberarem o dispositivo:</p>
                <div class="p-2 bg-dark rounded border border-secondary text-white small text-break user-select-all mb-2" id="bypassLinkText">
                    {{ url('/manutencao/bypass/' . $token) }}
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger w-100 mb-3" id="btnCopiarBypassLink">
                    <i class="fas fa-copy me-1"></i> Copiar Link de Bypass
                </button>

                <hr class="border-secondary border-opacity-25 my-3">

                <h6 class="fw-bold text-body mb-2"><i class="fas fa-flask text-danger me-1"></i> Como Testar Como Visitante</h6>
                <p class="mb-2">Para ver a página exatamente como um visitante da internet enxerga:</p>
                <ul class="ps-3 mb-3">
                    <li class="mb-1">Clique no botão <a href="{{ route('admin.manutencao.preview') }}" target="_blank" class="fw-bold text-danger text-decoration-underline">Visualizar Página</a> no topo.</li>
                    <li>Ou abra uma <strong>janela anônima</strong> sem estar com seu IP na lista de IPs liberados.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        // Alternância rápida de manutenção com SweetAlert2 de confirmação
        $('#btnToggleManutencao').on('click', function () {
            var $btn = $(this);
            var statusAtual = $btn.data('status') === '1';
            var tituloAcao = statusAtual ? 'Desativar Modo de Manutenção?' : 'Ativar Modo de Manutenção?';
            var textoAcao = statusAtual 
                ? 'O site voltará a ficar 100% público e acessível para todos os visitantes da internet.'
                : 'A Landing Page oficial será bloqueada para visitantes comuns e exibirá a página de manutenção com contagem regressiva.';
            var corConfirmar = statusAtual ? '#28a745' : '#d90000';
            var txtBtn = statusAtual ? 'Sim, Desativar e Deixar Online' : 'Sim, Ativar Manutenção Agora';

            Swal.fire({
                title: tituloAcao,
                text: textoAcao,
                icon: statusAtual ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: corConfirmar,
                cancelButtonColor: '#6c757d',
                confirmButtonText: txtBtn,
                cancelButtonText: 'Cancelar'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $btn.prop('disabled', true);
                    $.ajax({
                        url: '{{ route("admin.manutencao.toggle") }}',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (res) {
                            if (res.success) {
                                window.foco7Notify.success(res.message);
                                setTimeout(function () { window.location.reload(); }, 900);
                            } else {
                                $btn.prop('disabled', false);
                                window.foco7Notify.error(res.message || 'Falha ao alterar status da manutenção.');
                            }
                        },
                        error: function (xhr) {
                            $btn.prop('disabled', false);
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Falha ao alterar status da manutenção.';
                            window.foco7Notify.error(msg);
                        }
                    });
                }
            });
        });

        // Adicionar meu IP atual ao campo
        $('#btnAddMeuIp').on('click', function () {
            var meuIp = $(this).data('ip');
            var $txt = $('#manutencao_ips_liberados');
            var cur = $txt.val().trim();
            if (cur) {
                if (cur.indexOf(meuIp) === -1) {
                    $txt.val(cur + ', ' + meuIp);
                }
            } else {
                $txt.val(meuIp);
            }
            window.foco7Notify.info('IP ' + meuIp + ' adicionado à lista de liberação!');
        });

        // Copiar Link de Bypass para a área de transferência
        $('#btnCopiarBypassLink').on('click', function () {
            var link = $('#bypassLinkText').text().trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(link).then(function () {
                    window.foco7Notify.success('Link de bypass copiado com sucesso!');
                }).catch(function () {
                    window.foco7Notify.info('Selecione e copie o link no quadro acima.');
                });
            } else {
                window.foco7Notify.info('Selecione e copie o link no quadro acima.');
            }
        });
    });
</script>
@endsection
