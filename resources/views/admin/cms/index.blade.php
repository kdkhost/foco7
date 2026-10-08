@extends('admin.layouts.app')

@section('title', 'Gerenciador do CMS')
@section('page_title', 'CMS & Identidade Visual')
@section('page_description', 'Gerencie todos os textos, logotipo, carrossel de imagens, banners, FAQs e depoimentos.')

@section('styles')
<!-- Summernote WYSIWYG -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="cmsTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="identidade-tab" data-bs-toggle="tab" data-bs-target="#tab-identidade" type="button">
                    <i class="fas fa-palette text-danger me-1"></i> Identidade & Cores
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="contato-tab" data-bs-toggle="tab" data-bs-target="#tab-contato" type="button">
                    <i class="fas fa-address-book text-danger me-1"></i> Contatos & Horários
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="banners-tab" data-bs-toggle="tab" data-bs-target="#tab-banners" type="button">
                    <i class="fas fa-images text-danger me-1"></i> Banners & Slides
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="faq-tab" data-bs-toggle="tab" data-bs-target="#tab-faq" type="button">
                    <i class="fas fa-question-circle text-danger me-1"></i> FAQs (Dúvidas)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="depoimentos-tab" data-bs-toggle="tab" data-bs-target="#tab-depoimentos" type="button">
                    <i class="fas fa-star text-danger me-1"></i> Depoimentos
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="cmsTabsContent">

            <!-- ABA 1: IDENTIDADE & CORES -->
            <div class="tab-pane fade show active" id="tab-identidade" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome Oficial da Empresa</label>
                            <input type="text" name="site_nome" class="form-control" value="{{ $settings['site_nome'] ?? 'Foco 7 - Rastreamento e Segurança' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slogan do Site</label>
                            <input type="text" name="site_slogan" class="form-control" value="{{ $settings['site_slogan'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Primária (Hexadecimal)</label>
                            <div class="input-group">
                                <input type="color" name="cor_primaria" class="form-control form-control-color" value="{{ $settings['cor_primaria'] ?? '#d90000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_primaria'] ?? '#d90000' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor Secundária (Fundo Escuro)</label>
                            <div class="input-group">
                                <input type="color" name="cor_secundaria" class="form-control form-control-color" value="{{ $settings['cor_secundaria'] ?? '#111111' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_secundaria'] ?? '#111111' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cor de Destaque / Botões</label>
                            <div class="input-group">
                                <input type="color" name="cor_destaque" class="form-control form-control-color" value="{{ $settings['cor_destaque'] ?? '#e60000' }}">
                                <input type="text" class="form-control" value="{{ $settings['cor_destaque'] ?? '#e60000' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Alterar Logotipo Principal</label>
                            <input type="file" name="logo_upload" class="form-control" accept="image/*">
                            @if(!empty($settings['site_logo']))
                                <small class="text-secondary d-block mt-1">Logo atual: <a href="{{ $settings['site_logo'] }}" target="_blank">Visualizar imagem</a></small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Alterar Ícone Favicon</label>
                            <input type="file" name="favicon_upload" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR IDENTIDADE & CORES
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 2: CONTATOS & HORÁRIOS -->
            <div class="tab-pane fade" id="tab-contato" role="tabpanel">
                <form action="{{ route('admin.cms.salvar_configs') }}" method="POST" class="ajax-form">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Telefone da Central</label>
                            <input type="text" name="contato_telefone" class="form-control mask-telefone" value="{{ $settings['contato_telefone'] ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">WhatsApp Oficial (Com DDI e DDD)</label>
                            <input type="text" name="contato_whatsapp" class="form-control" value="{{ $settings['contato_whatsapp'] ?? '5591981117777' }}" required placeholder="Ex: 5591981117777">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">WhatsApp Formatado (Exibição)</label>
                            <input type="text" name="contato_whatsapp_formatado" class="form-control mask-telefone" value="{{ $settings['contato_whatsapp_formatado'] ?? '(91) 98111-7777' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">E-mail Comercial</label>
                            <input type="email" name="contato_email" class="form-control" value="{{ $settings['contato_email'] ?? 'contato@foco7.com.br' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horário de Atendimento Exibido no Topo</label>
                            <input type="text" name="contato_horario" class="form-control" value="{{ $settings['contato_horario'] ?? 'Central 24 Horas | Plantão de Emergência' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Endereço da Sede</label>
                            <input type="text" name="contato_endereco" class="form-control" value="{{ $settings['contato_endereco'] ?? 'Tv. Quintino Bocaiúva, 1200 - Nazaré, Belém - PA' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                            <input type="url" name="social_instagram" class="form-control" value="{{ $settings['social_instagram'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                            <input type="url" name="social_facebook" class="form-control" value="{{ $settings['social_facebook'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold"><i class="fab fa-youtube text-danger me-1"></i> YouTube</label>
                            <input type="url" name="social_youtube" class="form-control" value="{{ $settings['social_youtube'] ?? '' }}">
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> SALVAR CONTATOS & REDES
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA 3: BANNERS & SLIDES -->
            <div class="tab-pane fade" id="tab-banners" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Slides do Hero Carousel</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalBanner()">
                        <i class="fas fa-plus me-1"></i> Adicionar Novo Slide
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ordem</th>
                                <th>Badge / Título</th>
                                <th>Botão 1</th>
                                <th>Botão 2</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($slides as $slide)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $slide->ordem }}</span></td>
                                <td>
                                    @if($slide->badge)<span class="badge bg-danger mb-1">{{ $slide->badge }}</span><br>@endif
                                    <strong>{{ $slide->titulo }}</strong>
                                </td>
                                <td><span class="badge bg-dark">{{ $slide->texto_botao_1 ?? '-' }}</span></td>
                                <td><span class="badge bg-dark">{{ $slide->texto_botao_2 ?? '-' }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="excluirItem('banner', {{ $slide->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA 4: FAQS -->
            <div class="tab-pane fade" id="tab-faq" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Perguntas Frequentes (FAQ)</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalFaq()">
                        <i class="fas fa-plus me-1"></i> Adicionar Pergunta
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pergunta</th>
                                <th>Categoria</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $faq)
                            <tr>
                                <td class="fw-semibold">{{ $faq->pergunta }}</td>
                                <td><span class="badge bg-secondary">{{ $faq->categoria }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="excluirItem('faq', {{ $faq->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ABA 5: DEPOIMENTOS -->
            <div class="tab-pane fade" id="tab-depoimentos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Depoimentos de Clientes</h5>
                    <button type="button" class="btn btn-sm btn-danger fw-bold" onclick="abrirModalDepoimento()">
                        <i class="fas fa-plus me-1"></i> Adicionar Depoimento
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Cidade / Veículo</th>
                                <th>Estrelas</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($depoimentos as $dep)
                            <tr>
                                <td class="fw-bold">{{ $dep->nome_cliente }}</td>
                                <td>{{ $dep->cidade_uf }} - {{ $dep->veiculo }}</td>
                                <td class="text-warning">
                                    @for($i=0; $i<$dep->estrelas; $i++) <i class="fas fa-star"></i> @endfor
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="excluirItem('depoimento', {{ $dep->id }})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL NOVO SLIDE -->
<div class="modal fade" id="modalBanner" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Novo Slide do Hero Carousel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cms.salvar_banner') }}" method="POST" class="ajax-form">
                @csrf
                <input type="hidden" name="tipo" value="hero_slide">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Badge Superior (Opcional)</label>
                            <input type="text" name="badge" class="form-control" placeholder="Ex: 🛡️ TECNOLOGIA DE PONTA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ordem de Exibição</label>
                            <input type="number" name="ordem" class="form-control" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Título Principal *</label>
                            <input type="text" name="titulo" class="form-control" required placeholder="Título chamativo de alta conversão">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subtítulo Explicativo</label>
                            <textarea name="subtitulo" rows="3" class="form-control" placeholder="Descrição do impacto da proteção veicular"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Texto do Botão 1</label>
                            <input type="text" name="texto_botao_1" class="form-control" value="PROTEGER MEU VEÍCULO AGORA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link do Botão 1</label>
                            <input type="text" name="link_botao_1" class="form-control" value="#planos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Texto do Botão 2</label>
                            <input type="text" name="texto_botao_2" class="form-control" value="FALAR NO WHATSAPP">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link do Botão 2</label>
                            <input type="text" name="link_botao_2" class="form-control" value="https://wa.me/5591981117777">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Salvar Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL NOVA FAQ -->
<div class="modal fade" id="modalFaq" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nova Pergunta Frequente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cms.salvar_faq') }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pergunta *</label>
                        <input type="text" name="pergunta" class="form-control" required placeholder="Como funciona a instalação?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Categoria</label>
                        <select name="categoria" class="form-select">
                            <option value="geral">Geral</option>
                            <option value="instalacao">Instalação</option>
                            <option value="ronda">Ronda Tática</option>
                            <option value="aplicativo">Aplicativo</option>
                            <option value="financeiro">Financeiro / Pagamento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Resposta Completa *</label>
                        <textarea name="resposta" rows="4" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Salvar FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL NOVO DEPOIMENTO -->
<div class="modal fade" id="modalDepoimento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Novo Depoimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.cms.salvar_depoimento') }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nome do Cliente *</label>
                        <input type="text" name="nome_cliente" class="form-control" required placeholder="Ex: Roberto Silva">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Cidade - UF</label>
                            <input type="text" name="cidade_uf" class="form-control" placeholder="Belém - PA">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Veículo</label>
                            <input type="text" name="veiculo" class="form-control" placeholder="Hilux, Fan 160">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Estrelas</label>
                        <select name="estrelas" class="form-select">
                            <option value="5" selected>5 Estrelas (Excelente)</option>
                            <option value="4">4 Estrelas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Depoimento *</label>
                        <textarea name="texto" rows="3" class="form-control" required placeholder="O que o cliente disse sobre a proteção e agilidade?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold">Salvar Depoimento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Summernote WYSIWYG -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>

<script>
    function abrirModalBanner() {
        new bootstrap.Modal(document.getElementById('modalBanner')).show();
    }
    function abrirModalFaq() {
        new bootstrap.Modal(document.getElementById('modalFaq')).show();
    }
    function abrirModalDepoimento() {
        new bootstrap.Modal(document.getElementById('modalDepoimento')).show();
    }

    // Exclusão com SweetAlert2 (Sem alert() nativo)
    function excluirItem(tipo, id) {
        window.confirmarAcao(
            'Confirmar Exclusão?',
            'Este item será removido permanentemente do site.',
            'warning',
            function () {
                var url = '/admin/cms/' + tipo + '/' + id;
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function () { window.location.reload(); }, 800);
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao remover item.');
                    }
                });
            }
        );
    }
</script>
@endsection
