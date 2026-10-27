@extends('admin.layouts.app')

@section('title', 'Filiais & Localidades | Foco 7')
@section('page_title', 'Filiais & Localidades de Atendimento')
@section('page_description', 'Gerencie as filiais físicas e pontos de atendimento exibidos no mapa interativo da Landing Page pública.')

@section('styles')
<!-- Leaflet.js CSS para OpenStreetMap 100% Gratuito (Sem Google Maps) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #miniMapaFilialAdmin {
        height: 220px;
        width: 100%;
        border-radius: 8px;
        z-index: 1;
    }
</style>
@endsection

@section('content')
<!-- CARDS DE MÉTRICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Filiais</h6>
                    <h3 class="fw-bold mb-0 text-body" id="kpiTotalFiliais">{{ $totalFiliais }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Ativas no Mapa</h6>
                    <h3 class="fw-bold mb-0 text-success" id="kpiFiliaisAtivas">{{ $filiaisAtivas }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-city"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Cidades Atendidas</h6>
                    <h3 class="fw-bold mb-0 text-primary" id="kpiTotalCidades">{{ $totalCidades }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-secondary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-eye-slash"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Ocultas / Inativas</h6>
                    <h3 class="fw-bold mb-0 text-secondary" id="kpiFiliaisInativas">{{ $filiaisInativas }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS & BUSCA -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.filiais.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por filial, cidade, UF ou endereço..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os Status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativas no Mapa</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Ocultas / Desativadas</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill fw-bold">Filtrar</button>
                @if(request()->hasAny(['busca', 'status']))
                    <a href="{{ route('admin.filiais.index') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- CARD PRINCIPAL DE LISTAGEM -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header w-100 bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title fw-bold mb-0 text-body">
                <i class="fas fa-map-marked-alt text-danger me-2"></i> Unidades de Atendimento no Brasil
            </h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                {{ $filiais->total() }} cadastradas
            </span>
        </div>
        <div class="card-tools ms-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalNovaFilial()">
                <i class="fas fa-plus me-1"></i> Nova Filial / Unidade
            </button>
        </div>
    </div>

    <!-- TABELA DE FILIAIS -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary small text-uppercase">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 80px;">Fachada</th>
                    <th>Unidade / Filial</th>
                    <th>Cidade / UF</th>
                    <th>Endereço & Contato</th>
                    <th class="text-center">Coordenadas</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-end" style="width: 140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filiais as $filial)
                <tr id="filial-row-{{ $filial->id }}">
                    <td class="text-center text-secondary small fw-bold">{{ $filial->ordem }}</td>
                    <td>
                        <div class="rounded border p-1 d-inline-block bg-body-tertiary text-center shadow-sm" style="width: 60px; height: 50px;">
                            <img src="{{ $filial->foto_url }}" alt="{{ $filial->nome }}" class="img-fluid h-100 object-fit-cover rounded" onerror="this.src='/img/logo-foco7.png'">
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-body d-flex align-items-center gap-2">
                            {{ $filial->nome }}
                            @if($filial->destaque)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 small">Matriz</span>
                            @endif
                        </div>
                        <div class="text-secondary small">{{ $filial->horario_atendimento ?? 'Horário comercial' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-body border fw-bold">
                            {{ $filial->cidade }} - {{ $filial->estado }}
                        </span>
                    </td>
                    <td>
                        <div class="small text-body">{{ $filial->endereco }}{{ $filial->bairro ? ', ' . $filial->bairro : '' }}</div>
                        <div class="small text-secondary d-flex gap-2 mt-1">
                            @if($filial->whatsapp)
                                <a href="{{ $filial->whatsapp_url }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                    <i class="fab fa-whatsapp me-1"></i>{{ $filial->whatsapp }}
                                </a>
                            @endif
                            @if($filial->telefone)
                                <span class="text-muted"><i class="fas fa-phone-alt me-1"></i>{{ $filial->telefone }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center small">
                        @if($filial->latitude && $filial->longitude)
                            <span class="badge bg-dark-subtle text-body font-monospace" title="Lat: {{ $filial->latitude }} | Lng: {{ $filial->longitude }}">
                                <i class="fas fa-satellite-dish text-success me-1"></i> {{ number_format($filial->latitude, 3) }}, {{ number_format($filial->longitude, 3) }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning">Sem GPS</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-inline-flex align-items-center gap-2">
                            <span id="badge-status-{{ $filial->id }}" class="status-badge-pill {{ $filial->ativo ? 'status-badge-pago' : 'status-badge-cancelado' }}">
                                <span class="status-dot"></span>
                                <i class="fas {{ $filial->ativo ? 'fa-check-circle' : 'fa-eye-slash' }}"></i>
                                <span>{{ $filial->ativo ? 'Ativa' : 'Oculta' }}</span>
                            </span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input switch-filial-status cursor-pointer" type="checkbox" role="switch"
                                       data-id="{{ $filial->id }}" {{ $filial->ativo ? 'checked' : '' }}
                                       title="{{ $filial->ativo ? 'Ocultar no Mapa' : 'Exibir no Mapa' }}">
                            </div>
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ $filial->rota_osm_url ?? $filial->rota_url }}" target="_blank" class="btn btn-sm btn-outline-info" title="Ver Rota no OpenStreetMap" data-bs-toggle="tooltip">
                                <i class="fas fa-directions"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editarFilial({{ $filial->id }})" title="Editar Filial" data-bs-toggle="tooltip">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirFilial({{ $filial->id }}, '{{ addslashes($filial->nome) }}')" title="Excluir Filial" data-bs-toggle="tooltip">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-secondary">
                        <i class="fas fa-map-marked-alt fs-1 text-muted d-block mb-3"></i>
                        <p class="mb-2 fw-semibold">Nenhuma filial ou unidade encontrada com os critérios informados.</p>
                        <button type="button" class="btn btn-danger btn-sm" onclick="abrirModalNovaFilial()">
                            <i class="fas fa-plus me-1"></i> Cadastrar Primeira Filial
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($filiais->hasPages())
    <div class="card-footer bg-transparent border-top py-3">
        {{ $filiais->links() }}
    </div>
    @endif
</div>

<!-- MODAL CADASTRAR / EDITAR FILIAL -->
<div class="modal fade" id="modalFilial" tabindex="-1" aria-labelledby="modalFilialTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalFilialTitulo">Nova Filial / Unidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form action="{{ route('admin.filiais.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formFilial">
                @csrf
                <input type="hidden" name="filial_id" id="filial_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nome da Unidade / Filial *</label>
                            <input type="text" name="nome" id="filial_nome" class="form-control" required placeholder="Ex: Matriz Belém (Nazaré) ou Filial Fortaleza">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="filial_ordem" class="form-control" value="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estado (UF) *</label>
                            <select name="estado" id="filial_estado" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                    <option value="{{ $uf }}">{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Cidade *</label>
                            <input type="text" name="cidade" id="filial_cidade" class="form-control" required placeholder="Ex: Belém, São Luís, Fortaleza">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Endereço Completo *</label>
                            <input type="text" name="endereco" id="filial_endereco" class="form-control" required placeholder="Ex: Tv. Quintino Bocaiúva, 1200">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Bairro</label>
                            <input type="text" name="bairro" id="filial_bairro" class="form-control" placeholder="Ex: Nazaré, Aldeota">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CEP</label>
                            <input type="text" name="cep" id="filial_cep" class="form-control" placeholder="66000-000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Telefone Fixo</label>
                            <input type="text" name="telefone" id="filial_telefone" class="form-control" placeholder="(91) 3222-7000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">WhatsApp de Atendimento</label>
                            <input type="text" name="whatsapp" id="filial_whatsapp" class="form-control" placeholder="(91) 98051-0736">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">E-mail de Contato</label>
                            <input type="email" name="email" id="filial_email" class="form-control" placeholder="unidade@foco7.com.br">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Horário de Funcionamento</label>
                            <input type="text" name="horario_atendimento" id="filial_horario" class="form-control" value="Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00">
                        </div>

                        <!-- COORDENADAS GPS & ROTA OPENSTREETMAP -->
                        <div class="col-12">
                            <div class="p-3 rounded border bg-body-tertiary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-body"><i class="fas fa-map-pin text-danger me-1"></i> Coordenadas Geográficas (GPS OpenStreetMap - Sem Custos)</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnBuscarCoordenadas">
                                        <i class="fas fa-crosshairs me-1"></i> Preencher Coordenadas pelo Endereço
                                    </button>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary">Latitude</label>
                                        <input type="text" name="latitude" id="filial_latitude" class="form-control form-control-sm font-monospace" placeholder="-1.4557549">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary">Longitude</label>
                                        <input type="text" name="longitude" id="filial_longitude" class="form-control form-control-sm font-monospace" placeholder="-48.4901799">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-secondary">Link Personalizado de Rota (Opcional)</label>
                                        <input type="url" name="link_rota" id="filial_link_rota" class="form-control form-control-sm" placeholder="Deixe em branco para OpenStreetMap gratuito">
                                    </div>
                                </div>

                                <!-- MINI-MAPA INTERATIVO OPENSTREETMAP NO MODAL -->
                                <div class="mt-3">
                                    <div id="miniMapaFilialAdmin" class="border shadow-sm rounded"></div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted"><i class="fas fa-info-circle text-primary me-1"></i> <strong>OpenStreetMap Gratuito:</strong> Clique no mapa ou arraste o marcador para ajustar as coordenadas.</small>
                                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted" id="btnRecentrarMiniMapa" title="Centralizar na localização">
                                            <i class="fas fa-location-crosshairs me-1"></i> Focar Coordenadas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FOTO DA FACHADA / UNIDADE -->
                        <div class="col-12">
                            <label class="form-label small fw-bold">Foto da Fachada / Unidade (Opcional)</label>
                            <div class="foco-dropzone"
                                 data-input-name="foto_upload"
                                 data-ratio="16:9 ou 4:3 (Fachada Comercial)"
                                 data-max-size="5MB"
                                 data-accept="image/png,image/jpeg,image/webp">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="destaque" id="filial_destaque" value="1">
                                <label class="form-check-label small fw-semibold" for="filial_destaque">
                                    Definir como Matriz / Unidade Central em Destaque
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="ativo" id="filial_ativo" value="1" checked>
                                <label class="form-check-label small fw-semibold" for="filial_ativo">
                                    Ativo e Visível no Mapa do Site
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fas fa-save me-1"></i> Gravar Filial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet.js JS para OpenStreetMap 100% Gratuito (Sem Google Maps) -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var miniMap = null;
    var miniMarker = null;

    function initMiniMap() {
        if (miniMap) return;
        var mapContainer = document.getElementById('miniMapaFilialAdmin');
        if (!mapContainer) return;

        miniMap = L.map('miniMapaFilialAdmin', {
            center: [-14.235, -51.9253],
            zoom: 4,
            scrollWheelZoom: true
        });

        // Camada Oficial 100% Gratuita OpenStreetMap (Zero Custo / Sem Google Maps)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(miniMap);

        // Ao clicar no mapa, define a posição do marcador e preenche os inputs
        miniMap.on('click', function (e) {
            atualizarPosicaoMarcador(e.latlng.lat, e.latlng.lng, true);
        });
    }

    function atualizarPosicaoMarcador(lat, lng, atualizarInputs) {
        if (!miniMap) return;
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        if (isNaN(lat) || isNaN(lng)) return;

        if (atualizarInputs) {
            $('#filial_latitude').val(lat.toFixed(7));
            $('#filial_longitude').val(lng.toFixed(7));
        }

        if (miniMarker) {
            miniMarker.setLatLng([lat, lng]);
        } else {
            miniMarker = L.marker([lat, lng], { draggable: true }).addTo(miniMap);
            miniMarker.on('dragend', function (e) {
                var pos = e.target.getLatLng();
                $('#filial_latitude').val(pos.lat.toFixed(7));
                $('#filial_longitude').val(pos.lng.toFixed(7));
            });
        }
    }

    function reposicionarMiniMapaParaInputs() {
        var lat = parseFloat($('#filial_latitude').val());
        var lng = parseFloat($('#filial_longitude').val());
        if (!isNaN(lat) && !isNaN(lng) && miniMap) {
            atualizarPosicaoMarcador(lat, lng, false);
            miniMap.setView([lat, lng], 15);
        } else if (miniMap) {
            if (miniMarker) {
                miniMap.removeLayer(miniMarker);
                miniMarker = null;
            }
            miniMap.setView([-14.235, -51.9253], 4);
        }
    }

    // Ajusta o tamanho do mapa Leaflet quando o modal Bootstrap for exibido
    $('#modalFilial').on('shown.bs.modal', function () {
        initMiniMap();
        setTimeout(function () {
            if (miniMap) {
                miniMap.invalidateSize();
                reposicionarMiniMapaParaInputs();
            }
        }, 200);
    });

    $('#btnRecentrarMiniMapa').on('click', function () {
        reposicionarMiniMapaParaInputs();
    });

    function abrirModalNovaFilial() {
        $('#filial_id').val('');
        $('#formFilial')[0].reset();
        $('#filial_ativo').prop('checked', true);
        $('#filial_destaque').prop('checked', false);
        $('#filial_ordem').val('0');
        $('#filial_horario').val('Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00');

        if (miniMarker && miniMap) {
            miniMap.removeLayer(miniMarker);
            miniMarker = null;
        }

        if (window.FocoDropzone) {
            window.FocoDropzone.reset('#formFilial .foco-dropzone');
        }

        $('#modalFilialTitulo').text('Nova Filial / Unidade');
        new bootstrap.Modal(document.getElementById('modalFilial')).show();
    }

    function editarFilial(id) {
        $.getJSON('/admin/filiais/obter/' + id, function(res) {
            if (res.success && res.filial) {
                var f = res.filial;
                $('#filial_id').val(f.id);
                $('#filial_nome').val(f.nome);
                $('#filial_cidade').val(f.cidade);
                $('#filial_estado').val(f.estado);
                $('#filial_endereco').val(f.endereco);
                $('#filial_bairro').val(f.bairro || '');
                $('#filial_cep').val(f.cep || '');
                $('#filial_telefone').val(f.telefone || '');
                $('#filial_whatsapp').val(f.whatsapp || '');
                $('#filial_email').val(f.email || '');
                $('#filial_horario').val(f.horario_atendimento || '');
                $('#filial_latitude').val(f.latitude || '');
                $('#filial_longitude').val(f.longitude || '');
                $('#filial_link_rota').val(f.link_rota || '');
                $('#filial_ordem').val(f.ordem);
                $('#filial_destaque').prop('checked', !!f.destaque);
                $('#filial_ativo').prop('checked', !!f.ativo);

                if (window.FocoDropzone) {
                    var imgUrl = res.foto_url || (f.foto ? ('/' + f.foto.replace(/^\//, '')) : '');
                    if (f.foto) {
                        window.FocoDropzone.setPreview('#formFilial .foco-dropzone', imgUrl);
                    } else {
                        window.FocoDropzone.reset('#formFilial .foco-dropzone');
                    }
                }

                $('#modalFilialTitulo').text('Editar Filial: ' + f.nome);
                new bootstrap.Modal(document.getElementById('modalFilial')).show();
            }
        });
    }

    // Toggle de status ativo/inativo via AJAX
    $(document).on('change', '.switch-filial-status', function () {
        var $switch = $(this);
        var id = $switch.data('id');
        var isChecked = $switch.prop('checked');
        var $badge = $('#badge-status-' + id);

        $.ajax({
            url: '/admin/filiais/toggle/' + id,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    window.foco7Notify.success(res.message);
                    if ($badge.length) {
                        if (isChecked) {
                            $badge.attr('class', 'status-badge-pill status-badge-pago')
                                  .html('<span class="status-dot"></span><i class="fas fa-check-circle"></i><span>Ativa</span>');
                            $switch.attr('title', 'Ocultar no Mapa');
                        } else {
                            $badge.attr('class', 'status-badge-pill status-badge-cancelado')
                                  .html('<span class="status-dot"></span><i class="fas fa-eye-slash"></i><span>Oculta</span>');
                            $switch.attr('title', 'Exibir no Mapa');
                        }
                    }
                } else {
                    $switch.prop('checked', !$switch.prop('checked'));
                    window.foco7Notify.error('Falha ao alterar status.');
                }
            },
            error: function () {
                $switch.prop('checked', !$switch.prop('checked'));
                window.foco7Notify.error('Erro de conexão ao alterar status da filial.');
            }
        });
    });

    // Exclusão com SweetAlert2 (Sem alert() nativo)
    function excluirFilial(id, nome) {
        window.confirmarAcao(
            'Excluir ' + nome + '?',
            'Esta filial deixará de aparecer no mapa e na lista de localidades do site.',
            'warning',
            function () {
                $.ajax({
                    url: '/admin/filiais/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        if (res.success) {
                            window.foco7Notify.success(res.message);
                            $('#filial-row-' + id).fadeOut(300, function () { $(this).remove(); });
                            setTimeout(function () { window.location.reload(); }, 600);
                        } else {
                            window.foco7Notify.error(res.message || 'Falha ao excluir filial.');
                        }
                    },
                    error: function () {
                        window.foco7Notify.error('Erro ao processar exclusão da filial.');
                    }
                });
            }
        );
    }

    // Geocodificação inteligente via OpenStreetMap Nominatim
    $('#btnBuscarCoordenadas').on('click', function () {
        var rua = $('#filial_endereco').val();
        var cidade = $('#filial_cidade').val();
        var uf = $('#filial_estado').val();

        if (!cidade || !uf) {
            window.foco7Notify.warning('Preencha ao menos Cidade e Estado para pesquisar coordenadas.');
            return;
        }

        var query = (rua ? rua + ', ' : '') + cidade + ', ' + uf + ', Brasil';
        var $btn = $(this);
        var origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Buscando...');

        $.getJSON('https://nominatim.openstreetmap.org/search', {
            q: query,
            format: 'json',
            limit: 1,
            countrycodes: 'br'
        }).done(function (data) {
            $btn.prop('disabled', false).html(origText);
            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lon = parseFloat(data[0].lon);
                $('#filial_latitude').val(lat.toFixed(7));
                $('#filial_longitude').val(lon.toFixed(7));
                atualizarPosicaoMarcador(lat, lon, false);
                if (miniMap) miniMap.flyTo([lat, lon], 15);
                window.foco7Notify.success('Coordenadas GPS encontradas via OpenStreetMap!');
            } else {
                // Fallback: busca apenas pela cidade e estado
                $.getJSON('https://nominatim.openstreetmap.org/search', {
                    q: cidade + ', ' + uf + ', Brasil',
                    format: 'json',
                    limit: 1,
                    countrycodes: 'br'
                }).done(function (dataCity) {
                    if (dataCity && dataCity.length > 0) {
                        var latC = parseFloat(dataCity[0].lat);
                        var lonC = parseFloat(dataCity[0].lon);
                        $('#filial_latitude').val(latC.toFixed(7));
                        $('#filial_longitude').val(lonC.toFixed(7));
                        atualizarPosicaoMarcador(latC, lonC, false);
                        if (miniMap) miniMap.flyTo([latC, lonC], 13);
                        window.foco7Notify.info('Coordenadas aproximadas da cidade preenchidas via OpenStreetMap.');
                    } else {
                        window.foco7Notify.error('Não foi possível localizar as coordenadas no OpenStreetMap. Insira manualmente.');
                    }
                });
            }
        }).fail(function () {
            $btn.prop('disabled', false).html(origText);
            window.foco7Notify.error('Falha no serviço de localização.');
        });
    });
</script>
@endsection
