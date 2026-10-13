@extends('admin.layouts.app')

@section('title', 'Depoimentos & Moderação de Avaliações | Foco 7')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm-6">
                <h1 class="m-0 fw-bold fs-3 text-body">
                    <i class="fas fa-star text-warning me-2"></i> Depoimentos & Avaliações
                </h1>
                <p class="text-secondary small mb-0">Moderação de avaliações enviadas por clientes no site e cadastro manual de relatos institucionais.</p>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <button type="button" class="btn btn-primary shadow-sm" onclick="abrirModalDepoimento()">
                    <i class="fas fa-plus-circle me-1"></i> Novo Depoimento
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- CARDS DE MÉTRICAS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Pendentes de Moderação</span>
                            <h3 class="fw-bold mb-0 text-warning" id="cardCountPendentes">{{ $totalPendentes }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle icon-circle-lg">
                            <i class="fas fa-hourglass-half fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Aprovados & Publicados</span>
                            <h3 class="fw-bold mb-0 text-success" id="cardCountAprovados">{{ $totalAprovados }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle icon-circle-lg">
                            <i class="fas fa-check-circle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Total de Avaliações</span>
                            <h3 class="fw-bold mb-0 text-danger">{{ $totalGeral }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle icon-circle-lg">
                            <i class="fas fa-comments fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-secondary small text-uppercase fw-semibold">Média das Notas</span>
                            <h3 class="fw-bold mb-0 text-primary">{{ number_format($mediaEstrelas, 1, ',', '.') }} <small class="text-warning fs-6">★</small></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle icon-circle-lg">
                            <i class="fas fa-award fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTAGEM E MODERAÇÃO -->
        <div class="card shadow-sm border-0">
            <div class="card-header w-100 bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('admin.depoimentos.index', ['status' => 'todos', 'busca' => $busca]) }}" class="btn btn-outline-secondary {{ $status === 'todos' ? 'active' : '' }}">
                        Todos ({{ $totalGeral }})
                    </a>
                    <a href="{{ route('admin.depoimentos.index', ['status' => 'pendentes', 'busca' => $busca]) }}" class="btn btn-outline-warning {{ $status === 'pendentes' ? 'active' : '' }}">
                        <i class="fas fa-clock me-1"></i> Pendentes ({{ $totalPendentes }})
                    </a>
                    <a href="{{ route('admin.depoimentos.index', ['status' => 'aprovados', 'busca' => $busca]) }}" class="btn btn-outline-success {{ $status === 'aprovados' ? 'active' : '' }}">
                        <i class="fas fa-check me-1"></i> Aprovados ({{ $totalAprovados }})
                    </a>
                </div>

                <form action="{{ route('admin.depoimentos.index') }}" method="GET" class="d-flex align-items-center gap-2 ms-auto">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="busca" class="form-control" placeholder="Buscar por cliente ou texto..." value="{{ $busca }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    @if(!empty($busca))
                        <a href="{{ route('admin.depoimentos.index', ['status' => $status]) }}" class="btn btn-sm btn-link text-danger text-decoration-none">Limpar</a>
                    @endif
                </form>
            </div>

            <div class="card-body p-0">
                @if($depoimentos->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-body-secondary d-inline-block rounded-circle p-4 mb-3">
                            <i class="fas fa-star-half-alt text-muted fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-secondary">Nenhum depoimento encontrado</h5>
                        <p class="text-muted small">
                            @if($status === 'pendentes')
                                Parabéns! Todas as avaliações foram moderadas. Novos relatos de clientes surgirão aqui.
                            @else
                                Cadastre novos relatos institucionais ou aguarde avaliações do site público.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Foto</th>
                                    <th>Cliente / Local</th>
                                    <th style="width: 140px;">Classificação</th>
                                    <th>Depoimento</th>
                                    <th style="width: 150px;" class="text-center">Status / Moderação</th>
                                    <th style="width: 130px;" class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depoimentos as $dep)
                                    @php
                                        $partesNome = array_filter(explode(' ', trim($dep->nome_cliente)));
                                        $primeiraLetra = !empty($partesNome) ? mb_substr(array_shift($partesNome), 0, 1, 'UTF-8') : 'C';
                                        $segundaLetra = !empty($partesNome) ? mb_substr(array_pop($partesNome), 0, 1, 'UTF-8') : '';
                                        $iniciais = mb_strtoupper($primeiraLetra . $segundaLetra, 'UTF-8');
                                    @endphp
                                    <tr id="dep-row-{{ $dep->id }}" class="{{ !$dep->ativo ? 'bg-warning bg-opacity-10' : '' }}">
                                        <td>
                                            <div class="avatar-cliente-container position-relative d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                @if(!empty($dep->foto))
                                                    <img src="{{ asset($dep->foto) }}" 
                                                         alt="{{ $dep->nome_cliente }}" 
                                                         class="rounded-circle border" 
                                                         style="width: 42px; height: 42px; object-fit: cover;" 
                                                         onerror="this.style.setProperty('display', 'none', 'important'); if(this.nextElementSibling) this.nextElementSibling.style.setProperty('display', 'inline-flex', 'important');">
                                                    <div class="bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fw-bold rounded-circle align-items-center justify-content-center" 
                                                         style="width: 42px; height: 42px; font-size: 0.9rem; display: none !important;">
                                                        {{ $iniciais }}
                                                    </div>
                                                @else
                                                    <div class="bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                         style="width: 42px; height: 42px; font-size: 0.9rem;">
                                                        {{ $iniciais }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-body">{{ $dep->nome_cliente }}</div>
                                            <small class="text-secondary d-block">
                                                <i class="fas fa-car-side me-1 text-danger"></i> {{ $dep->veiculo ?: 'Cliente Foco 7' }}
                                                @if($dep->cidade_uf)
                                                    <span class="mx-1">•</span> <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $dep->cidade_uf }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $dep->estrelas ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <span class="small text-muted">{{ $dep->estrelas }} de 5 estrelas</span>
                                        </td>
                                        <td>
                                            <p class="mb-1 text-secondary small leading-relaxed" style="max-width: 480px;">
                                                "{{ $dep->texto }}"
                                            </p>
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <i class="far fa-calendar-alt me-1"></i> {{ $dep->created_at ? $dep->created_at->format('d/m/Y H:i') : 'Data não informada' }}
                                            </span>
                                        </td>
                                        <td class="text-center" id="status-cell-{{ $dep->id }}">
                                            @if($dep->ativo)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aprovado
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                    <i class="fas fa-clock me-1"></i> Pendente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Botão de Aprovação Rápida (Toggle) -->
                                                <button type="button" 
                                                        class="btn {{ $dep->ativo ? 'btn-outline-warning' : 'btn-success' }}" 
                                                        id="btn-toggle-{{ $dep->id }}" 
                                                        onclick="toggleAprovacao({{ $dep->id }})" 
                                                        title="{{ $dep->ativo ? 'Desativar / Colocar em Moderação' : 'Aprovar e Publicar no Site' }}">
                                                    <i class="fas {{ $dep->ativo ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                                <!-- Botão de Edição -->
                                                <button type="button" class="btn btn-outline-secondary" onclick="editarDepoimento({{ $dep->id }})" title="Editar Depoimento">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <!-- Botão de Exclusão -->
                                                <button type="button" class="btn btn-outline-danger" onclick="excluirDepoimento({{ $dep->id }})" title="Excluir Depoimento">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $depoimentos->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>

<!-- MODAL DE CADASTRO E EDIÇÃO DE DEPOIMENTO -->
<div class="modal fade" id="modalDepoimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalDepoimentoTitle">
                    <i class="fas fa-star me-2"></i> Novo Depoimento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formDepoimento" action="{{ route('admin.depoimentos.salvar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="depoimento_id" id="depoimento_id" value="">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Nome do Cliente *</label>
                            <input type="text" name="nome_cliente" id="dep_nome_cliente" class="form-control" required placeholder="Ex: Roberto Carlos">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Veículo / Modelo</label>
                            <input type="text" name="veiculo" id="dep_veiculo" class="form-control" placeholder="Ex: Hilux SRX 2023 / Frota de Caminhões">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Cidade / Estado</label>
                            <input type="text" name="cidade_uf" id="dep_cidade_uf" class="form-control" placeholder="Ex: Belém - PA">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Classificação (Estrelas) *</label>
                            <select name="estrelas" id="dep_estrelas" class="form-select" required>
                                <option value="5">★★★★★ (5 Estrelas)</option>
                                <option value="4">★★★★☆ (4 Estrelas)</option>
                                <option value="3">★★★☆☆ (3 Estrelas)</option>
                                <option value="2">★★☆☆☆ (2 Estrelas)</option>
                                <option value="1">★☆☆☆☆ (1 Estrela)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold text-secondary">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="dep_ordem" class="form-control" value="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Texto do Relato / Depoimento *</label>
                            <textarea name="texto" id="dep_texto" rows="4" class="form-control" required placeholder="Digite o relato ou avaliação do cliente..."></textarea>
                        </div>

                        <!-- Upload de Foto do Cliente com Drag & Drop -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Foto do Cliente / Avatar (Opcional)</label>
                            <div class="foco-dropzone-box p-3 text-center" id="dropzoneFotoDepoimento" onclick="document.getElementById('inputFotoDepoimento').click()">
                                <input type="file" name="foto_upload" id="inputFotoDepoimento" class="d-none" accept="image/*">
                                <div class="foco-dropzone-icon mb-1">
                                    <i class="fas fa-camera text-danger fs-3"></i>
                                </div>
                                <div class="fw-semibold small foco-dropzone-title">Clique ou arraste a foto do cliente aqui</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Formatos recomendados: JPG, PNG ou WEBP (Proporção quadrada 1:1)</div>
                                <div id="previewFotoDepoimento" class="mt-2 d-none">
                                    <img src="" id="imgPreviewDepoimento" class="rounded-circle border border-danger shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch p-2 bg-body-secondary rounded-3 border border-secondary border-opacity-25">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="ativo" id="dep_ativo" value="1" checked>
                                <label class="form-check-label fw-semibold small" for="dep_ativo">
                                    Aprovado e publicado imediatamente no site
                                </label>
                                <small class="text-muted d-block ms-4" style="font-size: 0.75rem;">Se desmarcado, ficará retido como "Pendente de Moderação".</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm" id="btnSalvarDepoimento">
                        <i class="fas fa-save me-1"></i> Salvar Depoimento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inicialização do Modal de Cadastro
    function abrirModalDepoimento() {
        $('#formDepoimento')[0].reset();
        $('#depoimento_id').val('');
        $('#modalDepoimentoTitle').html('<i class="fas fa-plus-circle me-2"></i> Novo Depoimento');
        $('#previewFotoDepoimento').addClass('d-none');
        $('#dep_ativo').prop('checked', true);
        const modal = new bootstrap.Modal(document.getElementById('modalDepoimento'));
        modal.show();
    }

    // Edição de Depoimento existente via AJAX
    function editarDepoimento(id) {
        $.ajax({
            url: `/admin/depoimentos/obter/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    const dep = res.depoimento;
                    $('#depoimento_id').val(dep.id);
                    $('#dep_nome_cliente').val(dep.nome_cliente);
                    $('#dep_veiculo').val(dep.veiculo);
                    $('#dep_cidade_uf').val(dep.cidade_uf);
                    $('#dep_estrelas').val(dep.estrelas);
                    $('#dep_ordem').val(dep.ordem);
                    $('#dep_texto').val(dep.texto);
                    $('#dep_ativo').prop('checked', dep.ativo);

                    if (res.foto_url) {
                        $('#imgPreviewDepoimento').attr('src', res.foto_url);
                        $('#previewFotoDepoimento').removeClass('d-none');
                    } else {
                        $('#previewFotoDepoimento').addClass('d-none');
                    }

                    $('#modalDepoimentoTitle').html('<i class="fas fa-pencil-alt me-2"></i> Editar Depoimento');
                    const modal = new bootstrap.Modal(document.getElementById('modalDepoimento'));
                    modal.show();
                }
            },
            error: function () {
                toastr.error('Não foi possível carregar os dados do depoimento.');
            }
        });
    }

    // Alternador de Aprovação / Moderação Rápida via AJAX
    function toggleAprovacao(id) {
        $.ajax({
            url: `/admin/depoimentos/toggle/${id}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.success) {
                    toastr.success(res.message);
                    const $row = $(`#dep-row-${id}`);
                    const $statusCell = $(`#status-cell-${id}`);
                    const $btn = $(`#btn-toggle-${id}`);

                    if (res.ativo) {
                        $row.removeClass('bg-warning bg-opacity-10');
                        $statusCell.html('<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> Aprovado</span>');
                        $btn.removeClass('btn-success').addClass('btn-outline-warning').html('<i class="fas fa-ban"></i>').attr('title', 'Desativar / Colocar em Moderação');
                    } else {
                        $row.addClass('bg-warning bg-opacity-10');
                        $statusCell.html('<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fas fa-clock me-1"></i> Pendente</span>');
                        $btn.removeClass('btn-outline-warning').addClass('btn-success').html('<i class="fas fa-check"></i>').attr('title', 'Aprovar e Publicar no Site');
                    }

                    // Atualiza cards
                    $('#cardCountPendentes').text(res.pendentes_count);
                    $('#cardCountAprovados').text(res.aprovados_count);
                }
            },
            error: function () {
                toastr.error('Erro ao alternar status de aprovação.');
            }
        });
    }

    // Exclusão com confirmação SweetAlert2 (Sem alert nativo)
    function excluirDepoimento(id) {
        Swal.fire({
            title: 'Excluir este depoimento?',
            text: 'Esta avaliação será removida definitivamente do sistema e da Landing Page.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/depoimentos/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $(`#dep-row-${id}`).fadeOut(300, function () { $(this).remove(); });
                            $('#cardCountPendentes').text(res.pendentes_count);
                            $('#cardCountAprovados').text(res.aprovados_count);
                        }
                    },
                    error: function () {
                        toastr.error('Erro ao excluir depoimento.');
                    }
                });
            }
        });
    }

    // Preview de foto ao selecionar arquivo
    $('#inputFotoDepoimento').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#imgPreviewDepoimento').attr('src', e.target.result);
                $('#previewFotoDepoimento').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Submissão do formulário de cadastro/edição via AJAX
    $('#formDepoimento').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#btnSalvarDepoimento');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando...');

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Depoimento');
                if (res.success) {
                    toastr.success(res.message);
                    bootstrap.Modal.getInstance(document.getElementById('modalDepoimento')).hide();
                    setTimeout(() => window.location.reload(), 600);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Salvar Depoimento');
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    const firstError = Object.values(errors)[0][0];
                    toastr.error(firstError);
                } else {
                    toastr.error('Ocorreu um erro ao salvar o depoimento.');
                }
            }
        });
    });
</script>
@endsection
