@extends('admin.layouts.app')

@section('title', 'Planos de Rastreamento')
@section('page_title', 'Gestão de Planos & Preços')
@section('page_description', 'Cadastre, edite mensalidades, taxas de adesão e recursos exibidos na Landing Page.')

@section('page_actions')
<button type="button" class="btn btn-danger fw-bold" onclick="abrirModalPlano()">
    <i class="fas fa-plus me-1"></i> Adicionar Novo Plano
</button>
@endsection

@section('content')
<div class="row g-4">
    @forelse($planos as $plano)
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 position-relative">
            @if($plano->destaque)
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">{{ $plano->tag_destaque ?? 'DESTAQUE' }}</span>
            @endif
            <div class="card-body d-flex flex-column text-center p-4">
                <div class="text-danger fs-1 mb-3">
                    {!! $plano->icone_html !!}
                </div>
                <h4 class="fw-bold mb-1">{{ $plano->nome }}</h4>
                <p class="small text-secondary mb-3">{{ $plano->subtitulo }}</p>
                <div class="my-3">
                    @if($plano->is_sob_consulta)
                        <span class="fs-2 fw-black text-warning">CONSULTAR</span>
                        <div class="mt-1"><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold">VALOR SOB CONSULTA</span></div>
                    @else
                        <span class="fs-2 fw-black text-danger">{{ $plano->preco_formatado }}</span>
                        <span class="text-secondary small">/mês</span>
                        @if($plano->taxa_adesao > 0)
                            <div class="small text-secondary">Adesão: {{ $plano->taxa_adesao_formatada }}</div>
                        @else
                            <div class="small text-success fw-bold">Adesão Grátis</div>
                        @endif
                    @endif
                </div>

                <div class="text-start small text-secondary my-3 flex-grow-1">
                    <strong>Recursos inclusos ({{ count($plano->recursos ?? []) }}):</strong>
                    <ul class="ps-3 mt-2 mb-0">
                        @foreach(array_slice($plano->recursos ?? [], 0, 4) as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                        @if(count($plano->recursos ?? []) > 4)
                            <li class="fst-italic">+ {{ count($plano->recursos) - 4 }} outros...</li>
                        @endif
                    </ul>
                </div>

                <div class="d-flex gap-2 mt-3 pt-3 border-top">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="editarPlano({{ $plano->id }})">
                        <i class="fas fa-edit me-1"></i> Editar
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="excluirPlano({{ $plano->id }})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-secondary">
        <i class="fas fa-tags fs-1 d-block mb-3"></i>
        <h5>Nenhum plano cadastrado.</h5>
    </div>
    @endforelse
</div>

<!-- MODAL CADASTRAR / EDITAR PLANO -->
<div class="modal fade" id="modalPlano" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalPlanoTitulo">Cadastrar Plano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.planos.salvar') }}" method="POST" class="ajax-form" id="formPlano" autocomplete="off">
                @csrf
                <input type="hidden" name="plano_id" id="plano_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome do Plano *</label>
                            <input type="text" name="nome" id="nome" class="form-control" required placeholder="Ex: Plano Carro Protegido">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoria</label>
                            <select name="categoria" id="categoria" class="form-select">
                                <option value="carro">Carro / Passeio</option>
                                <option value="moto">Moto</option>
                                <option value="frota">Frotas / Empresas</option>
                                <option value="carga">Caminhão & Cargas</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subtítulo / Descrição Rápida</label>
                            <input type="text" name="subtitulo" id="subtitulo" class="form-control" placeholder="Ex: O mais contratado para veículos de passeio">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch p-3 bg-warning bg-opacity-10 border border-warning border-opacity-50 rounded-3">
                                <input class="form-check-input ms-0 me-3" type="checkbox" name="sob_consulta" id="sob_consulta" value="1" onchange="toggleSobConsulta(this.checked)" style="transform: scale(1.25); cursor: pointer;">
                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="sob_consulta">
                                    <i class="fas fa-comments-dollar text-warning-emphasis me-1"></i> Exibir "CONSULTAR" no lugar do valor (Sob Consulta)
                                </label>
                                <p class="small text-secondary mb-0 mt-1 ms-1">
                                    Ao marcar esta opção, o plano exibirá a palavra <strong>CONSULTAR</strong> no lugar do valor em Reais e orientará o cliente a entrar em contato com um especialista.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4" id="blocoPrecoMensal">
                            <label class="form-label small fw-bold">Preço Mensal (R$) <span id="labelPrecoObrigatorio" class="text-danger">*</span></label>
                            <input type="text" name="preco_mensal" id="preco_mensal" class="form-control mask-moeda" required placeholder="R$ 69,90">
                        </div>
                        <div class="col-md-4" id="blocoTaxaAdesao">
                            <label class="form-label small fw-bold">Taxa de Adesão (R$)</label>
                            <input type="text" name="taxa_adesao" id="taxa_adesao" class="form-control mask-moeda" placeholder="R$ 0,00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                <span>Ícone do Plano (FontAwesome, Flaticon ou Livre)</span>
                                <span id="iconePreviewContainer" class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded d-inline-flex align-items-center justify-content-center" style="font-size: 1.35rem; min-width: 42px; min-height: 42px; border: 1px solid rgba(217,0,0,0.2);">
                                    <span id="iconePreviewContent"><i class="fas fa-icons text-muted" style="opacity: 0.4;"></i></span>
                                </span>
                            </label>
                            
                            <!-- Seleção rápida por catálogo -->
                            <select id="icone_seletor" class="form-select form-select-sm mb-2" onchange="selecionarIconePredefinido(this.value)">
                                <option value="">-- Escolha um Ícone Popular ou Digite Livremente Abaixo --</option>
                                <optgroup label="🚗 Veículos (FontAwesome & Flaticon)">
                                    <option value="fa-car">🚗 FontAwesome: Carro Passeio (fa-car)</option>
                                    <option value="fa-motorcycle">🏍️ FontAwesome: Moto / Duas Rodas (fa-motorcycle)</option>
                                    <option value="fa-truck">🚚 FontAwesome: Caminhão Pesado (fa-truck)</option>
                                    <option value="fa-truck-pickup">🛻 FontAwesome: Caminhonete / Pickup (fa-truck-pickup)</option>
                                    <option value="fa-van-shuttle">🚐 FontAwesome: Van / Utilitário (fa-van-shuttle)</option>
                                    <option value="fa-bus">🚌 FontAwesome: Ônibus / Passageiros (fa-bus)</option>
                                    <option value="fa-ship">🚤 FontAwesome: Barco / Náutico (fa-ship)</option>
                                    <option value="fa-tractor">🚜 FontAwesome: Trator / Agrícola (fa-tractor)</option>
                                    <option value="fi fi-rr-car">🚘 Flaticon: Carro Moderno (fi fi-rr-car)</option>
                                    <option value="fi fi-rr-motorcycle">🛵 Flaticon: Moto (fi fi-rr-motorcycle)</option>
                                    <option value="fi fi-rr-truck-side">🚛 Flaticon: Caminhão Lateral (fi fi-rr-truck-side)</option>
                                </optgroup>
                                <optgroup label="🛡️ Segurança & Rastreamento">
                                    <option value="fa-shield-alt">🛡️ FontAwesome: Escudo Proteção (fa-shield-alt)</option>
                                    <option value="fa-satellite-dish">📡 FontAwesome: Satélite GPS (fa-satellite-dish)</option>
                                    <option value="fa-location-dot">📍 FontAwesome: GPS Localização (fa-location-dot)</option>
                                    <option value="fa-mobile-screen">📱 FontAwesome: App Celular (fa-mobile-screen)</option>
                                    <option value="fa-key">🔑 FontAwesome: Bloqueio / Ignição (fa-key)</option>
                                    <option value="fa-bell">🔔 FontAwesome: Alertas em Tempo Real (fa-bell)</option>
                                    <option value="fa-lock">🔒 FontAwesome: Bloqueador Remoto (fa-lock)</option>
                                    <option value="fi fi-rr-shield-check">🛡️ Flaticon: Escudo Seguro (fi fi-rr-shield-check)</option>
                                    <option value="fi fi-rr-marker">📍 Flaticon: Pin Localização (fi fi-rr-marker)</option>
                                    <option value="fi fi-rr-smartphone">📱 Flaticon: Smartphone (fi fi-rr-smartphone)</option>
                                </optgroup>
                                <optgroup label="📦 Frotas & Gestão">
                                    <option value="fa-boxes-stacked">📦 FontAwesome: Frotas & Cargas (fa-boxes-stacked)</option>
                                    <option value="fa-route">🗺️ FontAwesome: Rotas & Trajetos (fa-route)</option>
                                    <option value="fa-tachometer-alt">⏱️ FontAwesome: Telemetria (fa-tachometer-alt)</option>
                                    <option value="fa-gas-pump">⛽ FontAwesome: Combustível (fa-gas-pump)</option>
                                    <option value="fi fi-rr-chart-histogram">📊 Flaticon: Relatórios (fi fi-rr-chart-histogram)</option>
                                    <option value="fi fi-rr-box-alt">📦 Flaticon: Pacotes / Cargas (fi fi-rr-box-alt)</option>
                                </optgroup>
                                <optgroup label="⭐ Destaques & Premium">
                                    <option value="fa-crown">👑 FontAwesome: VIP / Supremo (fa-crown)</option>
                                    <option value="fa-star">⭐ FontAwesome: Estrela Destaque (fa-star)</option>
                                    <option value="fa-bolt">⚡ FontAwesome: Alta Performance (fa-bolt)</option>
                                    <option value="fa-gem">💎 FontAwesome: Diamante Exclusivo (fa-gem)</option>
                                    <option value="fi fi-rr-crown">👑 Flaticon: Coroa VIP (fi fi-rr-crown)</option>
                                    <option value="fi fi-rr-sparkles">✨ Flaticon: Brilho Especial (fi fi-rr-sparkles)</option>
                                </optgroup>
                                <option value="custom">✏️ Digitar Classe Livre / Personalizada (FontAwesome, Flaticon, SVG...)</option>
                            </select>

                            <!-- Campo de entrada livre (aceita FontAwesome, Flaticon, SVG ou qualquer classe) -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary"><i class="fas fa-terminal"></i></span>
                                <input type="text" name="icone" id="icone" class="form-control" value="" placeholder="Ex: fa-truck-monster, fi fi-rr-car, bi bi-shield-check" oninput="onIconeInput(this.value)">
                                <button class="btn btn-outline-secondary" type="button" onclick="testarIconeRapido('fa-solid fa-car')" title="Exemplo FontAwesome">FA</button>
                                <button class="btn btn-outline-secondary" type="button" onclick="testarIconeRapido('fi fi-rr-car')" title="Exemplo Flaticon">Flaticon</button>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-1 flex-wrap gap-1">
                                <span class="text-secondary" style="font-size: 0.72rem;">
                                    Aceita <strong>qualquer</strong> classe: FontAwesome (<code>fa-solid fa-car</code>), Flaticon (<code>fi fi-rr-car</code>), Bootstrap ou SVG.
                                </span>
                                <div class="d-flex gap-2" style="font-size: 0.72rem;">
                                    <a href="https://fontawesome.com/icons?m=free" target="_blank" rel="noopener" class="text-decoration-none text-danger fw-bold">
                                        <i class="fab fa-font-awesome"></i> FontAwesome <i class="fas fa-external-link-alt" style="font-size: 0.65rem;"></i>
                                    </a>
                                    <a href="https://www.flaticon.com/uicons" target="_blank" rel="noopener" class="text-decoration-none text-danger fw-bold">
                                        <i class="fas fa-icons"></i> Flaticon <i class="fas fa-external-link-alt" style="font-size: 0.65rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Texto do Botão *</label>
                            <input type="text" name="texto_botao" id="texto_botao" class="form-control" value="" placeholder="Ex: CONTRATAR AGORA, ASSINAR">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="ordem" class="form-control" value="" placeholder="Ex: 1">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="destaque" id="destaque" value="1">
                                <label class="form-check-label fw-bold small" for="destaque">Plano em Destaque (Mais Popular)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tag de Destaque (Badge Superior)</label>
                            <input type="text" name="tag_destaque" id="tag_destaque" class="form-control" placeholder="MAIS POPULAR ⭐">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Recursos Inclusos (Um por linha) *</label>
                            <textarea name="recursos" id="recursos" rows="5" class="form-control" placeholder="Rastreamento em tempo real 24 horas&#10;Bloqueio remoto pelo aplicativo&#10;Ronda Tática presencial"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Salvar Plano</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function atualizarPreviewIcone(icone) {
        var $el = $('#iconePreviewContent');
        if (!icone || !icone.trim()) {
            $el.html('<i class="fas fa-icons text-muted" style="opacity: 0.4;"></i>');
            return;
        }
        icone = icone.trim();
        
        // 1. Se for SVG inline
        if (icone.startsWith('<svg')) {
            $el.html(icone);
            return;
        }

        // 2. Se o usuário colou a tag <i> inteira
        if (icone.startsWith('<i ') && icone.endsWith('</i>')) {
            $el.html(icone);
            return;
        }

        // 3. Se for arquivo de imagem
        if (icone.match(/\.(png|svg|webp|jpg|jpeg)$/i)) {
            $el.html('<img src="' + icone + '" style="max-height: 28px; width: auto;">');
            return;
        }

        // 4. Se for Flaticon (fi fi-rr-..., fi fi-sr-..., flaticon-...)
        if (icone.startsWith('fi ') || icone.startsWith('fi-') || icone.startsWith('flaticon')) {
            $el.html('<i class="' + icone + '"></i>');
            return;
        }

        // 5. Se for Bootstrap Icons (bi bi-..., bi-...)
        if (icone.startsWith('bi ') || icone.startsWith('bi-')) {
            var biClass = icone.startsWith('bi ') ? icone : 'bi ' + icone;
            $el.html('<i class="' + biClass + '"></i>');
            return;
        }

        // 6. Se for FontAwesome completo (já contém fa-solid, fa-regular, fa-brands, fas, far, fab, etc.)
        if (/^(fa[srbld]?\s+|fa-(solid|regular|brands|light|thin|duotone)\s+)/.test(icone)) {
            $el.html('<i class="' + icone + '"></i>');
            return;
        }

        // 7. Se for FontAwesome abreviado (ex: fa-car ou car)
        var faClass = icone;
        if (!faClass.startsWith('fa-')) {
            faClass = 'fa-' + faClass;
        }
        $el.html('<i class="fas ' + faClass + '"></i>');
    }

    function selecionarIconePredefinido(val) {
        if (!val) {
            $('#icone').val('');
            atualizarPreviewIcone('');
            return;
        }
        if (val === 'custom') {
            $('#icone').focus().select();
            return;
        }
        $('#icone').val(val);
        atualizarPreviewIcone(val);
    }

    function onIconeInput(val) {
        atualizarPreviewIcone(val);
        if ($('#icone_seletor option[value="' + val + '"]').length > 0) {
            $('#icone_seletor').val(val);
        } else if (val && val.trim().length > 0) {
            $('#icone_seletor').val('custom');
        } else {
            $('#icone_seletor').val('');
        }
    }

    function testarIconeRapido(classe) {
        $('#icone').val(classe);
        onIconeInput(classe);
    }

    function toggleSobConsulta(ativo) {
        if (ativo) {
            $('#preco_mensal').prop('required', false);
            $('#labelPrecoObrigatorio').hide();
            $('#blocoTaxaAdesao').addClass('opacity-50');
            if (!$('#texto_botao').val() || $('#texto_botao').val() === 'CONTRATAR AGORA') {
                $('#texto_botao').val('CONSULTAR AGORA');
            }
        } else {
            $('#preco_mensal').prop('required', true);
            $('#labelPrecoObrigatorio').show();
            $('#blocoTaxaAdesao').removeClass('opacity-50');
            if ($('#texto_botao').val() === 'CONSULTAR AGORA') {
                $('#texto_botao').val('CONTRATAR AGORA');
            }
        }
    }

    function abrirModalPlano() {
        $('#formPlano')[0].reset();
        $('#formPlano').find('input:not([type="hidden"]):not([type="checkbox"]), textarea').val('');
        $('#formPlano').find('select').prop('selectedIndex', 0);
        $('#destaque').prop('checked', false);
        $('#sob_consulta').prop('checked', false);
        toggleSobConsulta(false);
        $('#plano_id').val('');
        atualizarPreviewIcone('');
        $('#modalPlanoTitulo').text('Cadastrar Plano');
        new bootstrap.Modal(document.getElementById('modalPlano')).show();
    }

    function editarPlano(id) {
        $.getJSON('/admin/planos/obter/' + id, function (res) {
            if (res.success && res.plano) {
                var p = res.plano;
                $('#plano_id').val(p.id);
                $('#nome').val(p.nome);
                $('#categoria').val(p.categoria);
                $('#subtitulo').val(p.subtitulo);

                var isSobConsulta = !!res.sob_consulta;
                $('#sob_consulta').prop('checked', isSobConsulta);
                toggleSobConsulta(isSobConsulta);

                $('#preco_mensal').val(isSobConsulta && (!p.preco_mensal || p.preco_mensal == 0) ? '' : 'R$ ' + parseFloat(p.preco_mensal).toFixed(2).replace('.', ','));
                $('#taxa_adesao').val('R$ ' + parseFloat(p.taxa_adesao).toFixed(2).replace('.', ','));
                
                var iconeVal = p.icone || 'fa-car';
                $('#icone').val(iconeVal);
                if ($('#icone_seletor option[value="' + iconeVal + '"]').length > 0) {
                    $('#icone_seletor').val(iconeVal);
                } else {
                    $('#icone_seletor').val('custom');
                }
                atualizarPreviewIcone(iconeVal);

                $('#texto_botao').val(p.texto_botao || (isSobConsulta ? 'CONSULTAR AGORA' : 'CONTRATAR AGORA'));
                $('#ordem').val(p.ordem);
                $('#destaque').prop('checked', p.destaque);
                $('#tag_destaque').val(p.tag_destaque);
                $('#recursos').val(res.recursos_texto);

                $('#modalPlanoTitulo').text('Editar Plano: ' + p.nome);
                new bootstrap.Modal(document.getElementById('modalPlano')).show();
            }
        });
    }

    function excluirPlano(id) {
        window.confirmarAcao(
            'Excluir Plano?',
            'Este plano não aparecerá mais na Landing Page.',
            'warning',
            function () {
                $.ajax({
                    url: '/admin/planos/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function () { window.location.reload(); }, 800);
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao excluir plano.');
                    }
                });
            }
        );
    }
</script>
@endsection
