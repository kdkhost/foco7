@extends('admin.layouts.app')

@section('title', 'Modo de Manutenção')
@section('page_title', 'Controle do Modo de Manutenção')
@section('page_description', 'Ative ou desative o modo de manutenção, defina IPs liberados e configure a contagem regressiva.')

@section('content')
<div class="row g-4">
    <!-- Card de Ativação 1 Clique -->
    <div class="col-12">
        <div class="card shadow-sm border-0 {{ $statusAtivo ? 'bg-danger text-white' : 'bg-body' }}">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="fas fa-power-off me-2"></i>
                        Status Atual: {{ $statusAtivo ? 'MODO DE MANUTENÇÃO ATIVADO' : 'SITE ONLINE (PÚBLICO)' }}
                    </h4>
                    <p class="mb-0 {{ $statusAtivo ? 'text-white-50' : 'text-secondary' }} small">
                        {{ $statusAtivo ? 'Visitantes comuns visualizam a página de manutenção com contagem regressiva. Administradores e IPs liberados continuam com acesso normal.' : 'A Landing Page oficial está acessível para todos os visitantes da internet.' }}
                    </p>
                </div>
                <div>
                    <button type="button" class="btn {{ $statusAtivo ? 'btn-light text-danger fw-bold' : 'btn-danger fw-bold' }} px-4 py-2" id="btnToggleManutencao">
                        <i class="fas fa-toggle-on me-1"></i> {{ $statusAtivo ? 'DESATIVAR MANUTENÇÃO' : 'ATIVAR MANUTENÇÃO AGORA' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Configuração Detalhada -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-cog text-danger me-2"></i> Configurações da Página de Manutenção</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.manutencao.salvar') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Título da Página de Manutenção *</label>
                        <input type="text" name="manutencao_titulo" class="form-control" value="{{ $titulo }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mensagem Explicativa ao Visitante *</label>
                        <textarea name="manutencao_mensagem" rows="3" class="form-control" required>{{ $mensagem }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Previsão de Término (Contagem Regressiva)</label>
                            <input type="datetime-local" name="manutencao_previsao" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($previsao)) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Token de Bypass para Dispositivos Autorizados *</label>
                            <input type="text" name="manutencao_token" class="form-control" value="{{ $token }}" required>
                            <div class="form-text small">Dispositivos com esse token acessam normalmente.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">IPs Liberados (Separados por vírgula)</label>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" id="btnAddMeuIp" data-ip="{{ $meuIp }}">
                                <i class="fas fa-plus-circle me-1"></i> Adicionar meu IP atual ({{ $meuIp }})
                            </button>
                        </div>
                        <textarea name="manutencao_ips_liberados" id="manutencao_ips_liberados" rows="2" class="form-control" placeholder="127.0.0.1, 177.192.4.34">{{ $ips }}</textarea>
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

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
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
                <p>1. <strong>Administradores Logados:</strong> Enquanto estiver autenticado neste painel, você visualiza a Landing Page normalmente sem bloqueio.</p>
                <p>2. <strong>Bypass por IP:</strong> Se você adicionar seu endereço de IP na lista de autorizados, qualquer navegador na sua rede terá acesso liberado.</p>
                <p>3. <strong>Link de Bypass Direto:</strong> Você pode enviar este link exclusivo para clientes VIP ou técnicos:</p>
                <div class="p-2 bg-dark rounded border border-secondary text-white small text-break user-select-all mb-2">
                    {{ url('/manutencao/bypass/' . $token) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        // Toggle 1 clique
        $('#btnToggleManutencao').on('click', function () {
            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: '{{ route("admin.manutencao.toggle") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    window.foco7Notify.success(res.message);
                    setTimeout(function () { window.location.reload(); }, 1000);
                },
                error: function () {
                    $btn.prop('disabled', false);
                    window.foco7Notify.error('Falha ao alterar status da manutenção.');
                }
            });
        });

        // Adicionar meu IP atual
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
            window.foco7Notify.info('IP ' + meuIp + ' adicionado ao campo!');
        });
    });
</script>
@endsection
