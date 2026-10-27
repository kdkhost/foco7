@extends('admin.layouts.app')

@section('title', 'Empresas Parceiras | Foco 7')
@section('page_title', 'Empresas Parceiras & Homologadas')
@section('page_description', 'Gerencie as marcas e tecnologias exibidas no carrossel infinito da Landing Page pública.')

@section('content')
<!-- CARDS DE MÉTRICAS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Marcas</h6>
                    <h3 class="fw-bold mb-0 text-body" id="kpiTotalParceiros">{{ $totalParceiros }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Ativos no Carrossel Infinito</h6>
                    <h3 class="fw-bold mb-0 text-success" id="kpiParceirosAtivos">{{ $parceirosAtivos }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-secondary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-eye-slash"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Pausadas / Ocultas</h6>
                    <h3 class="fw-bold mb-0 text-secondary" id="kpiParceirosInativos">{{ $parceirosInativos }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS & BUSCA -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.parceiros.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por nome ou descrição..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Apenas Ativos</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Apenas Inativos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill fw-bold">Filtrar</button>
                @if(request()->hasAny(['busca', 'status']))
                    <a href="{{ route('admin.parceiros.index') }}" class="btn btn-outline-secondary">Limpar</a>
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
                <i class="fas fa-layer-group text-danger me-2"></i> Marcas Homologadas
            </h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                {{ $parceiros->total() }} no total
            </span>
        </div>
        <div class="card-tools ms-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalNovoParceiro()">
                <i class="fas fa-plus me-1"></i> Nova Marca / Parceiro
            </button>
        </div>
    </div>

    <!-- TABELA DE PARCEIROS -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;" class="text-center">Ordem</th>
                        <th style="width: 140px;">Logomarca</th>
                        <th>Empresa / Marca</th>
                        <th>Website / Link</th>
                        <th style="width: 120px;" class="text-center">Exibir no Site</th>
                        <th style="width: 130px;" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parceiros as $p)
                    <tr id="linha-parceiro-{{ $p->id }}">
                        <td class="text-center fw-bold text-secondary">
                            #{{ $p->ordem }}
                        </td>
                        <td>
                            <div class="p-2 bg-white rounded border d-flex align-items-center justify-content-center shadow-sm" style="width: 120px; height: 50px;">
                                <img src="{{ $p->logo_url }}" alt="{{ $p->nome }}" style="max-height: 38px; max-width: 105px; object-fit: contain;">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-body fs-6">{{ $p->nome }}</div>
                            @if($p->descricao)
                                <small class="text-secondary d-block">{{ $p->descricao }}</small>
                            @endif
                        </td>
                        <td>
                            @if($p->link)
                                <a href="{{ $p->link }}" target="_blank" class="text-decoration-none small fw-semibold text-danger">
                                    <i class="fas fa-external-link-alt me-1"></i> {{ Str::limit(str_replace(['https://', 'http://'], '', $p->link), 30) }}
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex align-items-center gap-2">
                                <span id="badge-status-parceiro-{{ $p->id }}" class="status-badge-pill {{ $p->ativo ? 'status-badge-pago' : 'status-badge-cancelado' }}">
                                    <span class="status-dot"></span>
                                    <i class="fas {{ $p->ativo ? 'fa-check-circle' : 'fa-eye-slash' }}"></i>
                                    <span>{{ $p->ativo ? 'Ativo' : 'Oculto' }}</span>
                                </span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input btn-toggle-parceiro cursor-pointer" type="checkbox" role="switch" data-id="{{ $p->id }}" {{ $p->ativo ? 'checked' : '' }} title="{{ $p->ativo ? 'Ocultar no Site' : 'Exibir no Site' }}">
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editarParceiro({{ $p->id }})" title="Editar Informações">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirParceiro({{ $p->id }}, '{{ addslashes($p->nome) }}')" title="Excluir Marca">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <i class="fas fa-handshake fs-1 d-block mb-2 opacity-50"></i>
                            Nenhuma empresa parceira cadastrada no momento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($parceiros->hasPages())
    <div class="card-footer bg-transparent border-top py-3">
        {{ $parceiros->links() }}
    </div>
    @endif
</div>

<!-- MODAL CADASTRAR / EDITAR PARCEIRO -->
<div class="modal fade" id="modalParceiro" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalParceiroTitulo">
                    <i class="fas fa-handshake text-danger me-2"></i> Nova Marca Parceira
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formParceiro" action="{{ route('admin.parceiros.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                @csrf
                <input type="hidden" name="parceiro_id" id="parceiro_id" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nome da Empresa / Marca *</label>
                            <input type="text" name="nome" id="parceiro_nome" class="form-control" required placeholder="Ex: Sascar, Cobli, Porto Seguro">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="parceiro_ordem" class="form-control" value="0" min="0" placeholder="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Website / Link de Redirecionamento (Opcional)</label>
                            <input type="url" name="link" id="parceiro_link" class="form-control" placeholder="https://www.parceiro.com.br">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Descrição Curta / Segmento (Opcional)</label>
                            <input type="text" name="descricao" id="parceiro_descricao" class="form-control" placeholder="Ex: Líder em Telemetria Veicular e IoT">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">Logomarca do Parceiro (SVG, PNG ou WEBP)</label>
                            <div class="foco-dropzone preview-fundo-branco"
                                 data-input-name="logo_upload"
                                 data-ratio="Horizontal (200 × 60 px ideal)"
                                 data-max-size="5MB"
                                 data-accept="image/svg+xml,image/png,image/webp,image/jpeg"
                                 id="dropzoneParceiro">
                            </div>
                            <small class="text-secondary d-block mt-1">Recomendamos logomarcas vetorizadas (SVG) ou com fundo transparente (PNG).</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch fs-6 mt-2">
                                <input class="form-check-input" type="checkbox" name="ativo" id="parceiro_ativo" value="1" checked>
                                <label class="form-check-label fw-bold text-body" for="parceiro_ativo">
                                    Exibir imediatamente no Carrossel Infinito da Landing Page
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm" id="btnSalvarParceiro">
                        <i class="fas fa-save me-1"></i> SALVAR MARCA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalNovoParceiro() {
        $('#formParceiro')[0].reset();
        $('#parceiro_id').val('');
        $('#modalParceiroTitulo').html('<i class="fas fa-handshake text-danger me-2"></i> Nova Marca Parceira');
        $('#parceiro_ativo').prop('checked', true);

        // Reseta o componente de upload FocoDropzone
        if (window.FocoDropzone) {
            window.FocoDropzone.reset('#dropzoneParceiro');
        }

        new bootstrap.Modal(document.getElementById('modalParceiro')).show();
    }

    function editarParceiro(id) {
        $.getJSON('/admin/parceiros/obter/' + id, function (res) {
            if (res.success && res.parceiro) {
                var p = res.parceiro;
                $('#parceiro_id').val(p.id);
                $('#parceiro_nome').val(p.nome);
                $('#parceiro_ordem').val(p.ordem);
                $('#parceiro_link').val(p.link || '');
                $('#parceiro_descricao').val(p.descricao || '');
                $('#parceiro_ativo').prop('checked', p.ativo == 1);

                $('#modalParceiroTitulo').html('<i class="fas fa-edit text-danger me-2"></i> Editar Parceiro: ' + p.nome);

                // Define o preview da logo existente via FocoDropzone
                var logoUrl = res.logo_url || p.logo_url || (p.logo ? (p.logo.startsWith('http') ? p.logo : '/' + p.logo.replace(/^\//, '')) : '');
                if (logoUrl && window.FocoDropzone) {
                    window.FocoDropzone.setPreview('#dropzoneParceiro', logoUrl, p.nome);
                } else if (window.FocoDropzone) {
                    window.FocoDropzone.reset('#dropzoneParceiro');
                }

                new bootstrap.Modal(document.getElementById('modalParceiro')).show();
            }
        });
    }

    function excluirParceiro(id, nome) {
        Swal.fire({
            title: 'Excluir Parceiro?',
            text: 'Deseja realmente remover "' + nome + '" do carrossel de parceiros?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/parceiros/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Excluído!',
                            text: res.message,
                            confirmButtonColor: '#d90000'
                        }).then(function () {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao excluir parceiro.';
                        window.foco7Notify.error(msg);
                    }
                });
            }
        });
    }

    $(function () {
        // Toggle de ativação rápida AJAX
        $(document).on('change', '.btn-toggle-parceiro', function () {
            var id = $(this).data('id');
            var $check = $(this);
            var isChecked = $check.prop('checked');
            var $badge = $('#badge-status-parceiro-' + id);

            $.ajax({
                url: '/admin/parceiros/toggle/' + id,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    window.foco7Notify.success(res.message);
                    if ($badge.length) {
                        if (isChecked) {
                            $badge.attr('class', 'status-badge-pill status-badge-pago')
                                  .html('<span class="status-dot"></span><i class="fas fa-check-circle"></i><span>Ativo</span>');
                            $check.attr('title', 'Ocultar no Site');
                        } else {
                            $badge.attr('class', 'status-badge-pill status-badge-cancelado')
                                  .html('<span class="status-dot"></span><i class="fas fa-eye-slash"></i><span>Oculto</span>');
                            $check.attr('title', 'Exibir no Site');
                        }
                    }
                },
                error: function () {
                    $check.prop('checked', !$check.prop('checked'));
                    window.foco7Notify.error('Falha ao alterar o status do parceiro.');
                }
            });
        });
    });
</script>
@endsection
