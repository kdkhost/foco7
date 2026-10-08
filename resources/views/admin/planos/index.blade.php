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
                <i class="fas {{ $plano->icone ?? 'fa-shield-alt' }} text-danger fs-1 mb-3"></i>
                <h4 class="fw-bold mb-1">{{ $plano->nome }}</h4>
                <p class="small text-secondary mb-3">{{ $plano->subtitulo }}</p>
                <div class="my-3">
                    <span class="fs-2 fw-black text-danger">{{ $plano->preco_formatado }}</span>
                    <span class="text-secondary small">/mês</span>
                    @if($plano->taxa_adesao > 0)
                        <div class="small text-secondary">Adesão: {{ $plano->taxa_adesao_formatada }}</div>
                    @else
                        <div class="small text-success fw-bold">Adesão Grátis</div>
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
            <form action="{{ route('admin.planos.salvar') }}" method="POST" class="ajax-form" id="formPlano">
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
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Preço Mensal (R$) *</label>
                            <input type="text" name="preco_mensal" id="preco_mensal" class="form-control mask-moeda" required placeholder="R$ 69,90">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Taxa de Adesão (R$)</label>
                            <input type="text" name="taxa_adesao" id="taxa_adesao" class="form-control mask-moeda" placeholder="R$ 0,00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ícone FontAwesome</label>
                            <input type="text" name="icone" id="icone" class="form-control" value="fa-car" placeholder="fa-car, fa-motorcycle">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" id="ordem" class="form-control" value="1">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="destaque" id="destaque" value="1">
                                <label class="form-check-label fw-bold small" for="destaque">Plano em Destaque</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tag de Destaque (Badge)</label>
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
    function abrirModalPlano() {
        $('#formPlano')[0].reset();
        $('#plano_id').val('');
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
                $('#preco_mensal').val('R$ ' + parseFloat(p.preco_mensal).toFixed(2).replace('.', ','));
                $('#taxa_adesao').val('R$ ' + parseFloat(p.taxa_adesao).toFixed(2).replace('.', ','));
                $('#icone').val(p.icone);
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
