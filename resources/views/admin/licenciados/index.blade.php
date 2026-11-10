@extends('admin.layouts.app')

@section('title', 'Programa de Licenciados & Distribuição')
@section('page_title', 'Licenciados FOCO 7')
@section('page_description', 'Gerenciamento de licenças White Label, distribuição do código-fonte para servidores próprios (cPanel, VPS, Cloud) e validações remotas.')

@section('page_actions')
    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
        <button type="button" class="btn btn-outline-secondary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfiguracoesPrograma">
            <i class="fas fa-sliders-h me-1 text-primary"></i> Configurações do Programa
        </button>
        <button type="button" class="btn btn-danger fw-semibold shadow-sm" onclick="abrirModalNovaLicenca()">
            <i class="fas fa-plus-circle me-1"></i> Nova Licença Manual
        </button>
    </div>
@endsection

@section('styles')
<style>
    .card-kpi-licenca {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-kpi-licenca:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .chave-codigo {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        letter-spacing: 0.5px;
        font-weight: 700;
        background: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--bs-primary);
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px dashed rgba(var(--bs-primary-rgb), 0.35);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-dominio {
        font-family: monospace;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<!-- KPI CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-kpi-licenca shadow-sm bg-body h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-3 p-3 bg-primary text-white me-3 fs-3">
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Licenças</h6>
                    <h3 class="fw-bold mb-0 text-body">{{ number_format($kpis['total']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-kpi-licenca shadow-sm bg-body h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-3 p-3 bg-success text-white me-3 fs-3">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Licenças Ativas</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($kpis['ativas']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-kpi-licenca shadow-sm bg-body h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-3 p-3 bg-warning text-dark me-3 fs-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Pendentes de Setup</h6>
                    <h3 class="fw-bold mb-0 text-warning">{{ number_format($kpis['pendentes']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card card-kpi-licenca shadow-sm bg-body h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-3 p-3 bg-info text-white me-3 fs-3">
                    <i class="fas fa-download"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Downloads de Pacote</h6>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($kpis['downloads']) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS & BUSCA -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.licenciados.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-7">
                <div class="input-group">
                    <span class="input-group-text bg-body border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" name="busca" value="{{ $busca ?? '' }}" class="form-control border-start-0" placeholder="Buscar por chave FOCO7-..., empresa, cliente, e-mail ou domínio...">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="ativa" {{ ($status ?? '') === 'ativa' ? 'selected' : '' }}>Ativa</option>
                    <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="suspensa" {{ ($status ?? '') === 'suspensa' ? 'selected' : '' }}>Suspensa</option>
                    <option value="bloqueada" {{ ($status ?? '') === 'bloqueada' ? 'selected' : '' }}>Bloqueada</option>
                    <option value="expirada" {{ ($status ?? '') === 'expirada' ? 'selected' : '' }}>Expirada</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                @if(!empty($busca) || !empty($status))
                    <a href="{{ route('admin.licenciados.index') }}" class="btn btn-outline-secondary" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- TABELA DE LICENÇAS -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title fw-bold mb-0">
            <i class="fas fa-server text-danger me-2"></i> Lista de Licenças Emitidas
        </h5>
        <span class="badge bg-secondary small">{{ $licencas->total() }} registros</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Licenciado / Empresa</th>
                        <th>Chave de Licença</th>
                        <th>Servidor / Domínio</th>
                        <th>Tipo / Versão</th>
                        <th>Status</th>
                        <th>Check-in Remoto</th>
                        <th class="text-center">Downloads</th>
                        <th class="text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($licencas as $licenca)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-body">{{ $licenca->nome_empresa }}</div>
                                <div class="small text-secondary">
                                    <i class="fas fa-user me-1 text-muted"></i> {{ $licenca->user->name ?? 'Sem Usuário Vinculado' }}
                                    @if(!empty($licenca->user->telefone))
                                        &bull; <a href="https://wa.me/{{ preg_replace('/\D/', '', $licenca->user->telefone) }}" target="_blank" class="text-success text-decoration-none">
                                            <i class="fab fa-whatsapp"></i> {{ $licenca->user->telefone }}
                                        </a>
                                    @endif
                                </div>
                                <div class="small text-muted">{{ $licenca->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <div>
                                    <span class="chave-codigo" title="Chave de Ativação do Software">
                                        {{ $licenca->chave_licenca }}
                                        <button type="button" class="btn btn-sm btn-link p-0 text-secondary" onclick="copiarTexto('{{ $licenca->chave_licenca }}', 'Chave copiada!')" title="Copiar Chave">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="small text-muted mt-1">
                                    Emitida em: {{ $licenca->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td>
                                @if(!empty($licenca->dominio_autorizado))
                                    <div>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle badge-dominio">
                                            <i class="fas fa-globe me-1"></i> {{ $licenca->dominio_autorizado }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="fas fa-link me-1"></i> Aguardando Vinculação
                                    </span>
                                @endif

                                @if(!empty($licenca->ip_autorizado))
                                    <div class="small text-secondary mt-1 font-monospace">
                                        <i class="fas fa-network-wired me-1"></i> IP: {{ $licenca->ip_autorizado }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-primary text-uppercase">{{ $licenca->tipo_licenca }}</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    <i class="fas fa-code-branch me-1"></i> {{ $licenca->versao_liberada ?? 'v3.8.0' }}
                                </div>
                            </td>
                            <td>
                                {!! $licenca->status_badge !!}
                            </td>
                            <td>
                                @if(!empty($licenca->ultimo_checkin_em))
                                    <div class="small fw-semibold text-success">
                                        <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i> {{ $licenca->ultimo_checkin_em->diffForHumans() }}
                                    </div>
                                    <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                        IP: {{ $licenca->ultimo_checkin_ip ?? 'N/D' }}
                                    </div>
                                @else
                                    <span class="small text-muted"><i class="far fa-clock me-1"></i> Nunca validou</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-download me-1 text-primary"></i> {{ $licenca->total_downloads ?? 0 }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog me-1"></i> Ações
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="editarLicenca({{ $licenca->id }})">
                                                <i class="fas fa-edit me-2 text-primary"></i> Editar Detalhes
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.licenciados.download-zip', $licenca->id) }}">
                                                <i class="fas fa-file-archive me-2 text-success"></i> Baixar Pacote (.ZIP)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.licenciados.download-vps', $licenca->id) }}">
                                                <i class="fas fa-terminal me-2 text-dark"></i> Baixar Script VPS (.sh)
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if($licenca->status !== 'ativa')
                                            <li>
                                                <a class="dropdown-item text-success" href="javascript:void(0)" onclick="alterarStatusLicenca({{ $licenca->id }}, 'ativa')">
                                                    <i class="fas fa-check-circle me-2"></i> Ativar Licença
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item text-warning" href="javascript:void(0)" onclick="alterarStatusLicenca({{ $licenca->id }}, 'suspensa')">
                                                    <i class="fas fa-pause-circle me-2"></i> Suspender Licença
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="regenerarChaveLicenca({{ $licenca->id }})">
                                                <i class="fas fa-sync-alt me-2"></i> Regenerar Chave
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="excluirLicenca({{ $licenca->id }})">
                                                <i class="fas fa-trash-alt me-2"></i> Excluir Licença
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-secondary">
                                <i class="fas fa-certificate fa-3x mb-3 text-muted"></i>
                                <p class="mb-1 fw-semibold">Nenhuma licença cadastrada ou encontrada com esses filtros.</p>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="abrirModalNovaLicenca()">
                                    <i class="fas fa-plus-circle me-1"></i> Emitir Primeira Licença
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($licencas->hasPages())
        <div class="card-footer bg-transparent py-3">
            {{ $licencas->links() }}
        </div>
    @endif
</div>

<!-- MODAL: NOVA / EDITAR LICENÇA -->
<div class="modal fade" id="modalLicenca" tabindex="-1" aria-labelledby="modalLicencaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formLicenca" onsubmit="salvarLicenca(event)">
                <input type="hidden" id="licenca_id" name="licenca_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalLicencaLabel">
                        <i class="fas fa-certificate text-danger me-2"></i> <span id="modalLicencaTitulo">Nova Licença</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Seletor de Usuário (Apenas visível ao Criar) -->
                    <div id="secaoUsuario" class="mb-3 p-3 bg-body-secondary rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">Titular da Licença</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="modo_usuario" id="modo_existente" value="existente" checked onchange="alternarModoUsuario('existente')">
                                <label class="btn btn-outline-primary" for="modo_existente">Cliente Existente</label>

                                <input type="radio" class="btn-check" name="modo_usuario" id="modo_novo" value="novo" onchange="alternarModoUsuario('novo')">
                                <label class="btn btn-outline-primary" for="modo_novo">Novo Titular</label>
                            </div>
                        </div>

                        <div id="blocoUsuarioExistente">
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">-- Selecione o Cliente Cadastrado --</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }}) [{{ strtoupper($u->role) }}]</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="blocoUsuarioNovo" style="display: none;">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" name="novo_usuario_nome" id="novo_usuario_nome" class="form-control" placeholder="Nome Completo do Titular">
                                </div>
                                <div class="col-md-4">
                                    <input type="email" name="novo_usuario_email" id="novo_usuario_email" class="form-control" placeholder="E-mail de Acesso">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="novo_usuario_telefone" id="novo_usuario_telefone" class="form-control" placeholder="WhatsApp (DDD+Nº)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nome da Empresa / Marca White Label <span class="text-danger">*</span></label>
                            <input type="text" name="nome_empresa" id="nome_empresa" class="form-control" placeholder="Ex: Minha Empresa Rastreamento Veicular" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo da Licença <span class="text-danger">*</span></label>
                            <select name="tipo_licenca" id="tipo_licenca" class="form-select" required>
                                <option value="vitalicia">Vitalícia (Sem expiração)</option>
                                <option value="anual">Anual</option>
                                <option value="mensal">Mensal</option>
                                <option value="teste">Teste / Homologação</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Domínio Autorizado</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe text-secondary"></i></span>
                                <input type="text" name="dominio_autorizado" id="dominio_autorizado" class="form-control font-monospace" placeholder="Ex: rastreamento.meudominio.com.br">
                            </div>
                            <div class="form-text small">Pode deixar em branco se o auto-binding no 1º acesso estiver ativado.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IP Autorizado do Servidor (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-network-wired text-secondary"></i></span>
                                <input type="text" name="ip_autorizado" id="ip_autorizado" class="form-control font-monospace" placeholder="Ex: 147.182.195.42">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Inicial <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="ativa">Ativa</option>
                                <option value="pendente">Pendente</option>
                                <option value="suspensa">Suspensa</option>
                                <option value="bloqueada">Bloqueada</option>
                                <option value="expirada">Expirada</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Versão Liberada</label>
                            <input type="text" name="versao_liberada" id="versao_liberada" class="form-control" value="v3.8.0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expiração (Opcional)</label>
                            <input type="date" name="data_expiracao" id="data_expiracao" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Limite de Veículos</label>
                            <input type="number" name="limite_veiculos" id="limite_veiculos" class="form-control" placeholder="Deixe em branco para Ilimitado">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Limite de Clientes</label>
                            <input type="number" name="limite_clientes" id="limite_clientes" class="form-control" placeholder="Deixe em branco para Ilimitado">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações / Anotações Internas</label>
                            <textarea name="observacoes" id="observacoes" rows="2" class="form-control" placeholder="Anotações internas sobre o contrato, servidor ou personalizações..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold" id="btnSalvarLicenca">
                        <i class="fas fa-save me-1"></i> Salvar Licença
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: CONFIGURAÇÕES DO PROGRAMA DE LICENCIAMENTO -->
<div class="modal fade" id="modalConfiguracoesPrograma" tabindex="-1" aria-labelledby="modalConfiguracoesProgramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formConfiguracoesPrograma" onsubmit="salvarConfiguracoesPrograma(event)">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalConfiguracoesProgramaLabel">
                        <i class="fas fa-sliders-h text-primary me-2"></i> Configurações do Programa de Licenciamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check form-switch mb-4 p-3 bg-body-secondary rounded-3">
                        <input class="form-check-input ms-0 me-3" type="checkbox" id="licenciamento_ativo" name="licenciamento_ativo" {{ ($parametros['ativo'] ?? true) ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                        <label class="form-check-label fw-bold d-block" for="licenciamento_ativo">
                            Exibir Módulo "Seja um Licenciado FOCO 7" na Landing Page Oficial
                        </label>
                        <small class="text-secondary">Permite que empreendedores vejam a oportunidade White Label e comprem a licença do sistema.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Badge de Destaque</label>
                            <input type="text" name="licenciamento_badge" class="form-control" value="{{ $parametros['badge'] ?? 'OPORTUNIDADE DE NEGÓCIO • WHITE LABEL' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Versão Atual para Distribuição</label>
                            <input type="text" name="licenciamento_versao_atual" class="form-control font-monospace" value="{{ $parametros['versao_atual'] ?? 'v3.8.0' }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Título Principal da Oferta</label>
                            <input type="text" name="licenciamento_titulo" class="form-control" value="{{ $parametros['titulo'] ?? 'Tenha Sua Própria Empresa de Rastreamento Veicular' }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Subtítulo Explicativo</label>
                            <textarea name="licenciamento_subtitulo" rows="2" class="form-control">{{ $parametros['subtitulo'] ?? 'Sistema completo instalado no seu servidor (VPS, cPanel, Cloud) com sua própria marca, domínio e faturamento 100% seu.' }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Taxa de Adesão / Implantação (R$)</label>
                            <input type="text" name="licenciamento_preco_adesao" class="form-control fw-bold text-success fs-5" value="{{ number_format($parametros['preco_adesao'] ?? 1497.00, 2, ',', '.') }}">
                            <div class="form-text small">Valor cobrado pela licença do software e pacote inicial.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mensalidade de Suporte & Atualizações (R$)</label>
                            <input type="text" name="licenciamento_preco_mensal" class="form-control fw-bold text-primary fs-5" value="{{ number_format($parametros['preco_mensal'] ?? 0.00, 2, ',', '.') }}">
                            <div class="form-text small">Coloque 0,00 caso não cobre mensalidade do parceiro.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">WhatsApp de Atendimento a Licenciados</label>
                            <input type="text" name="licenciamento_whatsapp_suporte" class="form-control" value="{{ $parametros['whatsapp_suporte'] ?? '559180510736' }}">
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="licenciamento_permitir_auto_binding" name="licenciamento_permitir_auto_binding" {{ ($parametros['permitir_auto_binding'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="licenciamento_permitir_auto_binding">
                                    Vincular Domínio Automaticamente no 1º Acesso
                                </label>
                                <div class="form-text small">Ao rodar o instalador pela primeira vez no servidor do cliente, vincula o domínio da chamada à licença.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary fw-semibold" id="btnSalvarConfigPrograma">
                        <i class="fas fa-save me-1"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function copiarTexto(texto, msg) {
        navigator.clipboard.writeText(texto).then(() => {
            if (typeof toastr !== 'undefined') {
                toastr.success(msg || 'Copiado para a área de transferência!');
            } else {
                Swal.fire({
                    icon: 'success',
                    title: msg || 'Copiado!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    function alternarModoUsuario(modo) {
        if (modo === 'novo') {
            document.getElementById('blocoUsuarioExistente').style.display = 'none';
            document.getElementById('blocoUsuarioNovo').style.display = 'block';
            document.getElementById('user_id').value = '';
        } else {
            document.getElementById('blocoUsuarioExistente').style.display = 'block';
            document.getElementById('blocoUsuarioNovo').style.display = 'none';
            document.getElementById('novo_usuario_nome').value = '';
            document.getElementById('novo_usuario_email').value = '';
            document.getElementById('novo_usuario_telefone').value = '';
        }
    }

    function abrirModalNovaLicenca() {
        document.getElementById('formLicenca').reset();
        document.getElementById('licenca_id').value = '';
        document.getElementById('modalLicencaTitulo').innerText = 'Nova Licença Manual';
        document.getElementById('secaoUsuario').style.display = 'block';
        alternarModoUsuario('existente');
        new bootstrap.Modal(document.getElementById('modalLicenca')).show();
    }

    function editarLicenca(id) {
        Swal.fire({
            title: 'Carregando dados...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`/admin/licenciados/${id}/detalhes`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (!data.success) {
                Swal.fire('Erro', data.message || 'Falha ao carregar licença.', 'error');
                return;
            }

            const l = data.licenca;
            document.getElementById('formLicenca').reset();
            document.getElementById('licenca_id').value = l.id;
            document.getElementById('modalLicencaTitulo').innerText = `Editar Licença: ${l.chave_licenca}`;
            document.getElementById('secaoUsuario').style.display = 'none'; // Não altera o titular diretamente

            document.getElementById('nome_empresa').value = l.nome_empresa || '';
            document.getElementById('tipo_licenca').value = l.tipo_licenca || 'vitalicia';
            document.getElementById('dominio_autorizado').value = l.dominio_autorizado || '';
            document.getElementById('ip_autorizado').value = l.ip_autorizado || '';
            document.getElementById('status').value = l.status || 'ativa';
            document.getElementById('versao_liberada').value = l.versao_liberada || 'v3.8.0';
            document.getElementById('limite_veiculos').value = l.limite_veiculos || '';
            document.getElementById('limite_clientes').value = l.limite_clientes || '';
            document.getElementById('observacoes').value = l.observacoes || '';

            if (l.data_expiracao) {
                document.getElementById('data_expiracao').value = l.data_expiracao.substring(0, 10);
            } else {
                document.getElementById('data_expiracao').value = '';
            }

            new bootstrap.Modal(document.getElementById('modalLicenca')).show();
        })
        .catch(err => {
            Swal.close();
            Swal.fire('Erro', 'Não foi possível se comunicar com o servidor.', 'error');
        });
    }

    function salvarLicenca(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarLicenca');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

        const form = document.getElementById('formLicenca');
        const formData = new FormData(form);

        fetch("{{ route('admin.licenciados.salvar') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Licença';

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalLicenca')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: data.message,
                    confirmButtonColor: '#d90000'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Atenção', data.message || 'Erro ao salvar licença.', 'warning');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Licença';
            Swal.fire('Erro', 'Ocorreu uma falha ao processar a requisição.', 'error');
        });
    }

    function alterarStatusLicenca(id, novoStatus) {
        const labels = {
            'ativa': 'Ativar',
            'suspensa': 'Suspender',
            'bloqueada': 'Bloquear'
        };

        Swal.fire({
            title: `Deseja ${labels[novoStatus] || 'alterar'} esta licença?`,
            text: `O servidor remoto do parceiro responderá ao status ${novoStatus.toUpperCase()} na próxima verificação.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: novoStatus === 'ativa' ? '#198754' : '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Sim, ${labels[novoStatus] || 'Alterar'}`,
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Atualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch(`/admin/licenciados/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: novoStatus })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        toastr.success(data.message);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire('Erro', 'Falha ao alterar status.', 'error');
                });
            }
        });
    }

    function regenerarChaveLicenca(id) {
        Swal.fire({
            title: 'Regenerar Chave de Licença?',
            text: 'ATENÇÃO: A chave atual será invalidada imediatamente. O licenciado precisará atualizar a chave no arquivo license.key de seu servidor para continuar operando.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, Regenerar Chave',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Gerando nova chave...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch(`/admin/licenciados/${id}/regenerar-chave`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Chave Regenerada!',
                            html: `Nova Chave: <strong class="text-danger">${data.nova_chave}</strong><br><small class="text-muted">Copie e envie ao parceiro.</small>`,
                            confirmButtonColor: '#d90000'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire('Erro', 'Falha ao regenerar chave.', 'error');
                });
            }
        });
    }

    function salvarConfiguracoesPrograma(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarConfigPrograma');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';

        const form = document.getElementById('formConfiguracoesPrograma');
        const formData = new FormData(form);

        fetch("{{ route('admin.licenciados.configuracoes') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Configurações';

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalConfiguracoesPrograma')).hide();
                toastr.success(data.message);
            } else {
                Swal.fire('Atenção', data.message || 'Erro ao salvar.', 'warning');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Configurações';
            Swal.fire('Erro', 'Falha ao comunicar com o servidor.', 'error');
        });
    }

    function excluirLicenca(id) {
        Swal.fire({
            title: 'Excluir Licença?',
            text: 'Esta ação não poderá ser desfeita. O servidor do licenciado perderá a autorização permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, Excluir',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Excluindo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                fetch(`/admin/licenciados/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        toastr.success(data.message);
                        setTimeout(() => location.reload(), 800);
                    } else {
                        Swal.fire('Erro', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.close();
                    Swal.fire('Erro', 'Falha ao excluir licença.', 'error');
                });
            }
        });
    }
</script>
@endsection

