@extends('admin.layouts.app')

@section('title', 'Estética Automotiva')
@section('page_title', 'Estética Automotiva & Detalhamento')
@section('page_description', 'Gerencie os serviços, preços, pacotes e tratamentos estéticos oferecidos pela Foco 7.')

@section('page_actions')
<button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalServico()">
    <i class="fas fa-plus me-1"></i> Adicionar Novo Serviço
</button>
@endsection

@section('content')
<!-- CARDS DE MÉTRICAS -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-car-side"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Serviços</h6>
                    <h3 class="fw-bold mb-0">{{ $servicos->count() }}</h3>
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
                    <h6 class="text-secondary small text-uppercase mb-1">Serviços Ativos</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ $servicos->where('ativo', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning text-dark rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Serviços em Destaque</h6>
                    <h3 class="fw-bold mb-0 text-warning">{{ $servicos->where('destaque', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LISTA DE CARDS DE SERVIÇOS -->
<div class="row g-4">
    @forelse($servicos as $servico)
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0 position-relative">
            @if($servico->destaque)
                <span class="badge bg-danger position-absolute top-0 end-0 m-3 shadow-sm">
                    <i class="fas fa-star me-1"></i> DESTAQUE
                </span>
            @endif

            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3 fs-3">
                        <i class="fas {{ $servico->icone ?: 'fa-car-side' }}"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $servico->nome }}</h5>
                        <span class="badge {{ $servico->ativo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $servico->ativo ? 'Ativo no Site' : 'Inativo' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary small mb-3 flex-grow-0">{{ $servico->subtitulo }}</p>

                <div class="p-3 bg-body-tertiary rounded-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-secondary fw-semibold">Valor Estimado:</span>
                        <span class="fs-5 fw-bold text-danger">{{ $servico->preco_formatado }}</span>
                    </div>
                    @if($servico->tempo_estimado)
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="small text-secondary"><i class="fas fa-clock me-1"></i> Duração:</span>
                        <span class="small fw-semibold">{{ $servico->tempo_estimado }}</span>
                    </div>
                    @endif
                </div>

                <div class="small text-secondary mb-3 flex-grow-1">
                    <strong>Etapas & Recursos:</strong>
                    <ul class="ps-3 mt-1 mb-0">
                        @foreach(array_slice($servico->recursos ?? [], 0, 3) as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                        @if(count($servico->recursos ?? []) > 3)
                            <li class="fst-italic text-secondary">+ {{ count($servico->recursos) - 3 }} outros itens...</li>
                        @endif
                    </ul>
                </div>

                <div class="d-flex gap-2 pt-3 border-top mt-auto">
                    <button type="button" class="btn btn-sm btn-outline-primary flex-fill fw-bold" onclick="editarServico({{ $servico->id }})">
                        <i class="fas fa-edit me-1"></i> Editar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-{{ $servico->ativo ? 'warning' : 'success' }} flex-fill fw-bold" onclick="toggleStatusServico({{ $servico->id }})">
                        <i class="fas {{ $servico->ativo ? 'fa-eye-slash' : 'fa-eye' }} me-1"></i> {{ $servico->ativo ? 'Desativar' : 'Ativar' }}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirServico({{ $servico->id }})" title="Excluir">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="fas fa-car-side text-secondary fs-1 d-block mb-3"></i>
        <h5 class="fw-bold text-secondary">Nenhum serviço de estética cadastrado ainda.</h5>
        <button type="button" class="btn btn-danger mt-2" onclick="abrirModalServico()">Cadastrar Primeiro Serviço</button>
    </div>
    @endforelse
</div>

<!-- DIFERENCIAIS E SELOS DE GARANTIA DA ESTÉTICA NO SITE -->
<div class="card shadow-sm border-0 mt-5">
    <div class="card-header bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-danger">
            <i class="fas fa-certificate me-2"></i> Diferenciais Técnicos e Selos de Garantia (Exibidos no Site)
        </h5>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Rodapé da Seção Estética Automotiva</span>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
            @csrf
            <p class="text-secondary small mb-4">
                Personalize os 4 cards de diferenciais e garantias que aparecem logo abaixo dos pacotes de serviços de estética automotiva na Landing Page.
            </p>
            <div class="row g-3">
                <!-- DIFERENCIAL 1 -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 bg-body-secondary rounded-3 border border-secondary border-opacity-25 h-100">
                        <span class="badge bg-danger mb-2">Diferencial 1</span>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="secao_estetica_dif1_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_icone'] ?? 'fa-award' }}" placeholder="ex: fa-award">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Título</label>
                            <input type="text" name="secao_estetica_dif1_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_titulo'] ?? 'Produtos Nobres' }}">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Descrição / Marcas</label>
                            <input type="text" name="secao_estetica_dif1_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif1_desc'] ?? 'Vonixx, CarPro e Menzerna' }}">
                        </div>
                    </div>
                </div>
                <!-- DIFERENCIAL 2 -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 bg-body-secondary rounded-3 border border-secondary border-opacity-25 h-100">
                        <span class="badge bg-danger mb-2">Diferencial 2</span>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="secao_estetica_dif2_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_icone'] ?? 'fa-warehouse' }}" placeholder="ex: fa-warehouse">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Título</label>
                            <input type="text" name="secao_estetica_dif2_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_titulo'] ?? 'Box Climatizado' }}">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Descrição</label>
                            <input type="text" name="secao_estetica_dif2_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif2_desc'] ?? 'Sem poeira ou sol direto' }}">
                        </div>
                    </div>
                </div>
                <!-- DIFERENCIAL 3 -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 bg-body-secondary rounded-3 border border-secondary border-opacity-25 h-100">
                        <span class="badge bg-danger mb-2">Diferencial 3</span>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="secao_estetica_dif3_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_icone'] ?? 'fa-certificate' }}" placeholder="ex: fa-certificate">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Título</label>
                            <input type="text" name="secao_estetica_dif3_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_titulo'] ?? 'Certificado de Garantia' }}">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Descrição</label>
                            <input type="text" name="secao_estetica_dif3_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif3_desc'] ?? 'Proteção com laudo técnico' }}">
                        </div>
                    </div>
                </div>
                <!-- DIFERENCIAL 4 -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 bg-body-secondary rounded-3 border border-secondary border-opacity-25 h-100">
                        <span class="badge bg-danger mb-2">Diferencial 4</span>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="secao_estetica_dif4_icone" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_icone'] ?? 'fa-user-clock' }}" placeholder="ex: fa-user-clock">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Título</label>
                            <input type="text" name="secao_estetica_dif4_titulo" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_titulo'] ?? 'Horário Marcado' }}">
                        </div>
                        <div>
                            <label class="form-label small fw-bold">Descrição</label>
                            <input type="text" name="secao_estetica_dif4_desc" class="form-control form-control-sm" value="{{ $settings['secao_estetica_dif4_desc'] ?? 'Entrega pontual sem filas' }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                    <i class="fas fa-save me-1"></i> Salvar Diferenciais da Estética
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CADASTRAR / EDITAR SERVIÇO -->
<div class="modal fade" id="modalServico" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalServicoTitulo">Novo Serviço de Estética Automotiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.estetica.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formServicoEstetica">
                @csrf
                <input type="hidden" name="servico_id" id="servico_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nome do Serviço *</label>
                            <input type="text" name="nome" id="servico_nome" class="form-control" required placeholder="Ex: Vitrificação Cerâmica 9H">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="icone" id="servico_icone" class="form-control" placeholder="fa-shield-halved">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subtítulo / Resumo Comercial</label>
                            <input type="text" name="subtitulo" id="servico_subtitulo" class="form-control" placeholder="Ex: Blindagem química e hidrofóbica com durabilidade de até 3 anos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Preço Sugerido (R$) (Deixe vazio para 'Sob Consulta')</label>
                            <input type="text" name="preco" id="servico_preco" class="form-control mask-dinheiro" placeholder="R$ 890,00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempo Estimado de Aplicação</label>
                            <input type="text" name="tempo_estimado" id="servico_tempo" class="form-control" placeholder="Ex: 4 a 6 horas ou 1 dia útil">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold d-flex justify-content-between align-items-center">
                                <span>Descrição Detalhada do Tratamento</span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-edit me-1"></i> Editor Summernote</span>
                            </label>
                            <textarea name="descricao" id="servico_descricao" class="form-control summernote" data-height="150" placeholder="Explique os benefícios técnicos e resultados esperados"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Itens Inclusos / Etapas (Um por linha)</label>
                            <textarea name="recursos" id="servico_recursos" class="form-control" rows="4" placeholder="Descontaminação ferrosa&#10;Polimento técnico de preparação&#10;Aplicação cerâmica 9H"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="servico_ordem" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-center gap-4 pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destaque" id="servico_destaque" value="1">
                                <label class="form-check-label fw-bold small" for="servico_destaque">Destaque na Página</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ativo" id="servico_ativo" value="1" checked>
                                <label class="form-check-label fw-bold small" for="servico_ativo">Serviço Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar Serviço</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalServico() {
        $('#servico_id').val('');
        $('#formServicoEstetica')[0].reset();
        window.clearSummernote('#servico_descricao');
        $('#servico_ativo').prop('checked', true);
        $('#modalServicoTitulo').text('Novo Serviço de Estética Automotiva');
        new bootstrap.Modal(document.getElementById('modalServico')).show();
    }

    function editarServico(id) {
        $.getJSON('/admin/estetica/obter/' + id, function(res) {
            if (res.success && res.servico) {
                var s = res.servico;
                $('#servico_id').val(s.id);
                $('#servico_nome').val(s.nome);
                $('#servico_subtitulo').val(s.subtitulo);
                $('#servico_icone').val(s.icone);
                $('#servico_preco').val(s.preco ? 'R$ ' + parseFloat(s.preco).toFixed(2).replace('.', ',') : '');
                $('#servico_tempo').val(s.tempo_estimado);
                window.setSummernoteContent('#servico_descricao', s.descricao || '');
                $('#servico_ordem').val(s.ordem);
                $('#servico_destaque').prop('checked', s.destaque == 1);
                $('#servico_ativo').prop('checked', s.ativo == 1);

                if (Array.isArray(s.recursos)) {
                    $('#servico_recursos').val(s.recursos.join('\n'));
                } else {
                    $('#servico_recursos').val('');
                }

                $('#modalServicoTitulo').text('Editar Serviço: ' + s.nome);
                new bootstrap.Modal(document.getElementById('modalServico')).show();
            }
        });
    }

    function toggleStatusServico(id) {
        $.post('/admin/estetica/toggle/' + id, { _token: '{{ csrf_token() }}' }, function(res) {
            if (res.success) {
                window.foco7Notify.success(res.message);
                setTimeout(function() { location.reload(); }, 600);
            }
        });
    }

    function excluirServico(id) {
        window.foco7Confirm('Excluir Serviço?', 'Tem certeza que deseja remover este serviço de estética automotiva?', function() {
            $.ajax({
                url: '/admin/estetica/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { location.reload(); }, 600);
                    }
                }
            });
        });
    }
</script>
@endsection
