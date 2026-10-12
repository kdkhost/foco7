@extends('admin.layouts.app')

@section('title', 'Filiais & Localidades | Foco 7')
@section('page_title', 'Filiais & Localidades de Atendimento')
@section('page_description', 'Gerencie as filiais físicas e pontos de atendimento exibidos no mapa interativo da Landing Page pública.')

@section('content')
<!-- KPIS DE FILIAIS (SMALL BOXES ADMINLTE 4) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="small-box text-bg-danger shadow-sm">
            <div class="inner">
                <h3 id="kpiTotalFiliais">{{ $totalFiliais }}</h3>
                <p>Total de Filiais</p>
            </div>
            <i class="small-box-icon fas fa-building"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="small-box text-bg-success shadow-sm">
            <div class="inner">
                <h3 id="kpiFiliaisAtivas">{{ $filiaisAtivas }}</h3>
                <p>Ativas no Mapa</p>
            </div>
            <i class="small-box-icon fas fa-map-marker-alt"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="small-box text-bg-info shadow-sm">
            <div class="inner">
                <h3 id="kpiTotalCidades">{{ $totalCidades }}</h3>
                <p>Cidades Atendidas</p>
            </div>
            <i class="small-box-icon fas fa-city"></i>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="small-box text-bg-secondary shadow-sm">
            <div class="inner">
                <h3 id="kpiFiliaisInativas">{{ $filiaisInativas }}</h3>
                <p>Ocultas / Desativadas</p>
            </div>
            <i class="small-box-icon fas fa-eye-slash"></i>
        </div>
    </div>
</div>

<!-- CARD PRINCIPAL DE LISTAGEM -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-body">
                <i class="fas fa-map-marked-alt text-danger me-2"></i> Unidades de Atendimento no Brasil
            </h5>
            <span class="badge bg-body-secondary text-body border px-2 py-1 small">
                {{ $filiais->total() }} cadastradas
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold" onclick="abrirModalNovaFilial()">
                <i class="fas fa-plus me-1"></i> Nova Filial / Unidade
            </button>
        </div>
    </div>

    <!-- FILTRO E BUSCA -->
    <div class="card-body border-bottom bg-body-tertiary p-3">
        <form method="GET" action="{{ route('admin.filiais.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0 text-secondary"><i class="fas fa-search"></i></span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por filial, cidade, UF ou endereço..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Apenas Ativas no Mapa</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Apenas Ocultas</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['busca', 'status']))
                    <a href="{{ route('admin.filiais.index') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>
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
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input switch-filial-status cursor-pointer" type="checkbox" role="switch"
                                   data-id="{{ $filial->id }}" {{ $filial->ativo ? 'checked' : '' }}
                                   title="{{ $filial->ativo ? 'Ativo no Mapa' : 'Oculto no Mapa' }}">
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ $filial->rota_url }}" target="_blank" class="btn btn-outline-info" title="Ver Rota no Google Maps" data-bs-toggle="tooltip">
                                <i class="fas fa-directions"></i>
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="editarFilial({{ $filial->id }})" title="Editar Filial" data-bs-toggle="tooltip">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="excluirFilial({{ $filial->id }}, '{{ addslashes($filial->nome) }}')" title="Excluir Filial" data-bs-toggle="tooltip">
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-secondary">
                Exibindo {{ $filiais->firstItem() }} até {{ $filiais->lastItem() }} de {{ $filiais->total() }} filiais
            </div>
            <div>
                {{ $filiais->links() }}
            </div>
        </div>
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

                        <!-- COORDENADAS GPS & ROTA -->
                        <div class="col-12">
                            <div class="p-3 rounded border bg-body-tertiary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-body"><i class="fas fa-map-pin text-danger me-1"></i> Coordenadas Geográficas (GPS para o Marcador do Mapa)</span>
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
                                        <input type="url" name="link_rota" id="filial_link_rota" class="form-control form-control-sm" placeholder="Deixe em branco para gerar Google Maps automático">
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
<script>
    function abrirModalNovaFilial() {
        $('#filial_id').val('');
        $('#formFilial')[0].reset();
        $('#filial_ativo').prop('checked', true);
        $('#filial_destaque').prop('checked', false);
        $('#filial_ordem').val('0');
        $('#filial_horario').val('Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00');

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

        $.ajax({
            url: '/admin/filiais/toggle/' + id,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    window.foco7Notify.success(res.message);
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
                $('#filial_latitude').val(parseFloat(data[0].lat).toFixed(7));
                $('#filial_longitude').val(parseFloat(data[0].lon).toFixed(7));
                window.foco7Notify.success('Coordenadas GPS encontradas e preenchidas!');
            } else {
                // Fallback: busca apenas pela cidade e estado
                $.getJSON('https://nominatim.openstreetmap.org/search', {
                    q: cidade + ', ' + uf + ', Brasil',
                    format: 'json',
                    limit: 1,
                    countrycodes: 'br'
                }).done(function (dataCity) {
                    if (dataCity && dataCity.length > 0) {
                        $('#filial_latitude').val(parseFloat(dataCity[0].lat).toFixed(7));
                        $('#filial_longitude').val(parseFloat(dataCity[0].lon).toFixed(7));
                        window.foco7Notify.info('Coordenadas aproximadas da cidade preenchidas.');
                    } else {
                        window.foco7Notify.error('Não foi possível localizar as coordenadas. Insira manualmente.');
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
