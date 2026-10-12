@extends('admin.layouts.app')

@section('title', 'Usuários & Permissões')
@section('page_title', 'Usuários & Permissões')
@section('page_description', 'Gerencie administradores, atendentes, permissões modulares de acesso e segurança 2FA.')

@section('content')
<!-- SMALL BOXES / MÉTRICAS ADMINLTE 4 -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2-4 col-xl">
        <div class="small-box bg-danger rounded-4 shadow-sm h-100 p-3 text-white">
            <div class="inner">
                <h3 class="fw-bold mb-1">{{ $metricas['total'] }}</h3>
                <p class="mb-0 small text-white-50">Total de Usuários</p>
            </div>
            <div class="icon">
                <i class="fas fa-users opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2-4 col-xl">
        <div class="small-box bg-primary rounded-4 shadow-sm h-100 p-3 text-white">
            <div class="inner">
                <h3 class="fw-bold mb-1">{{ $metricas['admins'] }}</h3>
                <p class="mb-0 small text-white-50">Admins &amp; Gestores</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-shield opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2-4 col-xl">
        <div class="small-box bg-info rounded-4 shadow-sm h-100 p-3 text-white">
            <div class="inner">
                <h3 class="fw-bold mb-1">{{ $metricas['equipe'] }}</h3>
                <p class="mb-0 small text-white-50">Equipe / Operadores</p>
            </div>
            <div class="icon">
                <i class="fas fa-headset opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2-4 col-xl">
        <div class="small-box bg-secondary rounded-4 shadow-sm h-100 p-3 text-white">
            <div class="inner">
                <h3 class="fw-bold mb-1">{{ $metricas['clientes'] }}</h3>
                <p class="mb-0 small text-white-50">Clientes / Assinantes</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-tag opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-2-4 col-xl">
        <div class="small-box bg-success rounded-4 shadow-sm h-100 p-3 text-white">
            <div class="inner">
                <h3 class="fw-bold mb-1">{{ $metricas['com_2fa'] }}</h3>
                <p class="mb-0 small text-white-50">Protegidos com 2FA</p>
            </div>
            <div class="icon">
                <i class="fas fa-shield-alt opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<!-- BARRA DE AÇÕES E FILTROS -->
<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4 col-lg-3">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por nome, e-mail, CPF..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="role" class="form-select">
                    <option value="todos">Todos os Papéis</option>
                    @foreach($papeis as $chave => $papel)
                        <option value="{{ $chave }}" {{ request('role') === $chave ? 'selected' : '' }}>
                            {{ $papel['nome'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <select name="status" class="form-select">
                    <option value="todos">Todos os Status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <select name="two_factor" class="form-select">
                    <option value="">Status 2FA (Todos)</option>
                    <option value="ativo" {{ request('two_factor') === 'ativo' ? 'selected' : '' }}>Com 2FA Ativo</option>
                    <option value="inativo" {{ request('two_factor') === 'inativo' ? 'selected' : '' }}>Sem 2FA</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['busca', 'role', 'status', 'two_factor']))
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
            <div class="col text-end">
                <button type="button" class="btn btn-danger fw-bold px-3" id="btnNovoUsuario">
                    <i class="fas fa-user-plus me-1"></i> NOVO USUÁRIO
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TABELA DE USUÁRIOS -->
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3 py-3">Usuário</th>
                    <th>Papel / Cargo</th>
                    <th>Contato &amp; Cidade</th>
                    <th class="text-center">Segurança 2FA</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                <tr id="usuario-row-{{ $u->id }}">
                    <!-- USUÁRIO (AVATAR + NOME + EMAIL) -->
                    <td class="ps-3 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="position-relative">
                                @if($u->avatar)
                                    <img src="{{ asset(ltrim($u->avatar, '/')) }}" alt="{{ $u->name }}" class="rounded-circle border border-danger p-05 object-fit-cover shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                @else
                                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px;">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold">{{ $u->name }}</div>
                                <div class="text-secondary small">{{ $u->email }}</div>
                                @if($u->cpf_cnpj)
                                    <div class="text-muted small" style="font-size: 0.75rem;">CPF: {{ $u->cpf_cnpj }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- PAPEL / CARGO -->
                    <td>
                        @php
                            $badgePapel = $papeis[$u->role]['badge'] ?? 'secondary';
                            $nomePapel = $papeis[$u->role]['nome'] ?? ucfirst($u->role);
                        @endphp
                        <span class="badge bg-{{ $badgePapel }} text-uppercase px-2 py-1">
                            {{ $nomePapel }}
                        </span>
                        @if($u->cargo)
                            <div class="text-secondary small mt-1">
                                <i class="fas fa-briefcase me-1 text-danger"></i> {{ $u->cargo }}
                            </div>
                        @endif
                    </td>

                    <!-- CONTATO & CIDADE -->
                    <td>
                        @if($u->telefone)
                            <div><i class="fab fa-whatsapp text-success me-1"></i> {{ $u->telefone }}</div>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                        @if($u->cidade || $u->estado)
                            <div class="text-secondary small">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $u->cidade ? $u->cidade . '/' : '' }}{{ $u->estado }}
                            </div>
                        @endif
                    </td>

                    <!-- 2FA -->
                    <td class="text-center">
                        @if($u->hasTwoFactor())
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" title="Autenticação de 2 Fatores Ativada">
                                <i class="fas fa-shield-alt me-1"></i> 2FA ATIVO
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1" title="Sem 2FA">
                                <i class="fas fa-shield-slash me-1"></i> Inativo
                            </span>
                        @endif
                    </td>

                    <!-- STATUS (TOGGLE AJAX) -->
                    <td class="text-center">
                        @if($u->id === auth()->id())
                            <span class="badge bg-success">Conectado</span>
                        @else
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input btn-toggle-status-usuario"
                                       type="checkbox"
                                       role="switch"
                                       data-id="{{ $u->id }}"
                                       {{ $u->status === 'ativo' ? 'checked' : '' }}
                                       title="Alternar Ativo/Inativo">
                            </div>
                        @endif
                    </td>

                    <!-- AÇÕES -->
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-editar-usuario" data-id="{{ $u->id }}" title="Editar Usuário & Permissões">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-reset-senha" data-id="{{ $u->id }}" data-nome="{{ $u->name }}" title="Redefinir Senha">
                                <i class="fas fa-key"></i>
                            </button>
                            @if($u->id !== auth()->id())
                                <button type="button" class="btn btn-outline-danger btn-excluir-usuario" data-id="{{ $u->id }}" data-nome="{{ $u->name }}" title="Excluir Usuário">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0">Nenhum usuário encontrado para os critérios de busca informados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
    <div class="card-footer bg-transparent py-3 border-top">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">
                Exibindo {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuários
            </span>
            <div>
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- ============================================================== -->
<!-- MODAL DE CADASTRO E EDIÇÃO DE USUÁRIO COM MATRIZ DE PERMISSÕES -->
<!-- ============================================================== -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalUsuarioTitulo">
                    <i class="fas fa-user-plus me-2"></i> Novo Usuário
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formUsuario" action="{{ route('admin.usuarios.store') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                @csrf
                <input type="hidden" name="usuario_id" id="usuarioId" value="">
                <input type="hidden" name="remover_avatar" id="usuarioRemoverAvatar" value="0">

                <div class="modal-body p-0">
                    <!-- NAVEGAÇÃO POR ABAS NO MODAL -->
                    <ul class="nav nav-tabs nav-fill border-bottom bg-body-tertiary px-3 pt-2" id="modalUsuarioTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold" id="tab-acesso-btn" data-bs-toggle="tab" data-bs-target="#tab-acesso" type="button">
                                <i class="fas fa-id-badge text-danger me-1"></i> Acesso &amp; Perfil
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" id="tab-pessoais-btn" data-bs-toggle="tab" data-bs-target="#tab-pessoais" type="button">
                                <i class="fas fa-user text-danger me-1"></i> Dados Pessoais
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" id="tab-endereco-btn" data-bs-toggle="tab" data-bs-target="#tab-end-usuario" type="button">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> Endereço
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" id="tab-permissoes-btn" data-bs-toggle="tab" data-bs-target="#tab-permissoes" type="button">
                                <i class="fas fa-user-shield text-danger me-1"></i> Permissões
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="modalUsuarioTabsContent">
                        
                        <!-- ABA 1: ACESSO & FUNÇÃO -->
                        <div class="tab-pane fade show active" id="tab-acesso" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome Completo *</label>
                                    <input type="text" name="name" id="userName" class="form-control" required placeholder="Nome do usuário">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">E-mail de Acesso *</label>
                                    <input type="email" name="email" id="userEmail" class="form-control" required placeholder="usuario@foco7.com.br">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold" id="labelPassword">Senha de Acesso *</label>
                                    <input type="password" name="password" id="userPassword" class="form-control" placeholder="Mínimo 8 dígitos">
                                    <div class="form-text small text-muted" id="helpPassword">Obrigatória no cadastro inicial.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Papel / Função *</label>
                                    <select name="role" id="userRole" class="form-select" required>
                                        @foreach($papeis as $chave => $papel)
                                            <option value="{{ $chave }}">{{ $papel['nome'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Status *</label>
                                    <select name="status" id="userStatus" class="form-select" required>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>

                                <!-- FOTO DE PERFIL / AVATAR -->
                                <div class="col-12 mt-3 pt-3 border-top">
                                    <label class="form-label small fw-bold">Foto de Perfil (Avatar)</label>
                                    <div class="foco-dropzone"
                                         data-input-name="avatar_upload"
                                         data-ratio="300 × 300 px (1:1 Quadrado)"
                                         data-max-size="5MB"
                                         data-accept="image/jpeg,image/png,image/webp">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ABA 2: DADOS PESSOAIS -->
                        <div class="tab-pane fade" id="tab-pessoais" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">CPF ou CNPJ</label>
                                    <input type="text" name="cpf_cnpj" id="userCpfCnpj" class="form-control mask-cpf-cnpj" placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Telefone / WhatsApp</label>
                                    <input type="text" name="telefone" id="userTelefone" class="form-control mask-telefone" placeholder="(91) 98051-0736">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Data de Nascimento</label>
                                    <input type="date" name="data_nascimento" id="userDataNascimento" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Gênero</label>
                                    <select name="genero" id="userGenero" class="form-select">
                                        <option value="">Selecione...</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Outro">Outro</option>
                                        <option value="Prefiro não informar">Prefiro não informar</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Cargo / Função Exibida</label>
                                    <input type="text" name="cargo" id="userCargo" class="form-control" placeholder="Ex: Supervisor Operacional">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Observações / Bio</label>
                                    <textarea name="bio" id="userBio" class="form-control" rows="3" placeholder="Informações complementares sobre o usuário..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ABA 3: ENDEREÇO COMPLETO -->
                        <div class="tab-pane fade" id="tab-end-usuario" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">CEP</label>
                                    <div class="input-group">
                                        <input type="text" name="cep" id="userCep" class="form-control mask-cep" placeholder="00000-000">
                                        <button type="button" class="btn btn-outline-secondary" id="btnBuscarCepUsuario" title="Buscar no ViaCEP">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Logradouro / Rua</label>
                                    <input type="text" name="endereco" id="userLogradouro" class="form-control" placeholder="Rua, Avenida...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Número</label>
                                    <input type="text" name="numero" id="userNumero" class="form-control" placeholder="123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Complemento</label>
                                    <input type="text" name="complemento" id="userComplemento" class="form-control" placeholder="Apto, Sala, Bloco...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Bairro</label>
                                    <input type="text" name="bairro" id="userBairro" class="form-control" placeholder="Bairro">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Cidade</label>
                                    <input type="text" name="cidade" id="userCidade" class="form-control" placeholder="Cidade">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small fw-bold">UF</label>
                                    <input type="text" name="estado" id="userEstado" class="form-control text-uppercase" maxlength="2" placeholder="PA">
                                </div>
                            </div>
                        </div>

                        <!-- ABA 4: MATRIZ DE PERMISSÕES -->
                        <div class="tab-pane fade" id="tab-permissoes" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-0">Controle Granular de Permissões</h6>
                                    <p class="small text-muted mb-0">Selecione quais módulos e operações este usuário poderá acessar.</p>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary" id="btnMarcarTodasPermissoes">Marcar Todas</button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnDesmarcarTodasPermissoes">Desmarcar Todas</button>
                                </div>
                            </div>

                            <div id="alertaSuperAdmin" class="alert alert-info py-2 small mb-3 d-none">
                                <i class="fas fa-info-circle me-1"></i> <strong>Nota:</strong> Usuários com papel de <strong>Super Administrador</strong> possuem acesso total a todos os módulos por padrão.
                            </div>

                            <div class="row g-3">
                                @foreach($modulosPermissoes as $catChave => $categoria)
                                <div class="col-md-6">
                                    <div class="card h-100 border shadow-none bg-body-tertiary rounded-3">
                                        <div class="card-header bg-transparent py-2 border-bottom fw-bold small text-uppercase">
                                            <i class="fas fa-folder me-1 text-danger"></i> {{ $categoria['titulo'] }}
                                        </div>
                                        <div class="card-body p-3">
                                            @foreach($categoria['permissoes'] as $permChave => $permDados)
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input check-permissao"
                                                           type="checkbox"
                                                           name="permissoes[]"
                                                           value="{{ $permChave }}"
                                                           id="perm_{{ $permChave }}">
                                                    <label class="form-check-label small fw-semibold" for="perm_{{ $permChave }}">
                                                        {{ $permDados['nome'] }}
                                                    </label>
                                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                                        {{ $permDados['descricao'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold" id="btnSalvarUsuario">
                        <i class="fas fa-save me-1"></i> SALVAR USUÁRIO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL DE REDEFINIÇÃO RÁPIDA DE SENHA PELO ADMINISTRADOR        -->
<!-- ============================================================== -->
<div class="modal fade" id="modalResetSenha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-key me-2"></i> Redefinir Senha do Usuário
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResetSenha" method="POST" class="ajax-form">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">
                        Definindo nova senha para: <strong id="resetNomeUsuario" class="text-danger"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nova Senha *</label>
                        <div class="input-group">
                            <input type="password" name="nova_senha" id="resetNovaSenha" class="form-control" required minlength="8" placeholder="Mínimo 8 dígitos">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha('resetNovaSenha')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Confirmar Nova Senha *</label>
                        <div class="input-group">
                            <input type="password" name="nova_senha_confirmation" id="resetNovaSenhaConf" class="form-control" required minlength="8" placeholder="Repita a senha">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha('resetNovaSenhaConf')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">
                        <i class="fas fa-check-circle me-1"></i> DEFINIR SENHA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSenha(inputId) {
        var input = document.getElementById(inputId);
        if (input.type === 'password') input.type = 'text';
        else input.type = 'password';
    }

    var papeisConfig = @json($papeis);

    // Ajusta permissões recomendadas ao alterar o papel
    $('#userRole').on('change', function() {
        var role = $(this).val();
        if (role === 'admin') {
            $('#alertaSuperAdmin').removeClass('d-none');
            $('.check-permissao').prop('checked', true);
        } else {
            $('#alertaSuperAdmin').addClass('d-none');
            var padroes = papeisConfig[role] && papeisConfig[role].permissoes_padrao ? papeisConfig[role].permissoes_padrao : [];
            $('.check-permissao').each(function() {
                var val = $(this).val();
                $(this).prop('checked', padroes.includes(val) || padroes.includes('*'));
            });
        }
    });

    $('#btnMarcarTodasPermissoes').on('click', function() {
        $('.check-permissao').prop('checked', true);
    });

    $('#btnDesmarcarTodasPermissoes').on('click', function() {
        $('.check-permissao').prop('checked', false);
    });

    // Abrir Modal para NOVO USUÁRIO
    $('#btnNovoUsuario').on('click', function() {
        $('#formUsuario')[0].reset();
        $('#usuarioId').val('');
        $('#formUsuario').attr('action', "{{ route('admin.usuarios.store') }}");
        $('#modalUsuarioTitulo').html('<i class="fas fa-user-plus me-2"></i> Novo Usuário');
        $('#userPassword').prop('required', true);
        $('#labelPassword').text('Senha de Acesso *');
        $('#helpPassword').text('Obrigatória no cadastro inicial (mínimo 8 dígitos).');
        
        // Reset Dropzone
        if (window.FocoDropzone) {
            $('.foco-dropzone').each(function() {
                window.FocoDropzone.reset(this);
            });
        }

        $('#modalUsuarioTabs button:first').tab('show');
        $('#userRole').trigger('change');

        var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        modal.show();
    });

    // Abrir Modal para EDITAR USUÁRIO
    $(document).on('click', '.btn-editar-usuario', function() {
        var id = $(this).data('id');
        var btn = $(this);
        btn.prop('disabled', true);

        $.getJSON("/admin/usuarios/" + id, function(res) {
            btn.prop('disabled', false);
            if (res.success) {
                var u = res.usuario;
                $('#usuarioId').val(u.id);
                $('#formUsuario').attr('action', "/admin/usuarios/" + u.id);
                $('#modalUsuarioTitulo').html('<i class="fas fa-user-edit me-2"></i> Editar Usuário: ' + u.name);
                
                $('#userName').val(u.name);
                $('#userEmail').val(u.email);
                $('#userPassword').val('').prop('required', false);
                $('#labelPassword').text('Nova Senha (Opcional)');
                $('#helpPassword').text('Deixe em branco para manter a senha atual.');

                $('#userRole').val(u.role);
                $('#userStatus').val(u.status);
                $('#userCpfCnpj').val(u.cpf_cnpj);
                $('#userTelefone').val(u.telefone);
                $('#userDataNascimento').val(u.data_nascimento);
                $('#userGenero').val(u.genero);
                $('#userCargo').val(u.cargo);
                $('#userBio').val(u.bio);

                $('#userCep').val(u.cep);
                $('#userLogradouro').val(u.endereco);
                $('#userNumero').val(u.numero);
                $('#userComplemento').val(u.complemento);
                $('#userBairro').val(u.bairro);
                $('#userCidade').val(u.cidade);
                $('#userEstado').val(u.estado);

                // Carrega permissões
                var userPerms = u.permissoes || [];
                if (u.role === 'admin') {
                    $('#alertaSuperAdmin').removeClass('d-none');
                } else {
                    $('#alertaSuperAdmin').addClass('d-none');
                }
                $('.check-permissao').each(function() {
                    var val = $(this).val();
                    $(this).prop('checked', userPerms.includes(val) || (u.role === 'admin'));
                });

                // Dropzone Preview
                if (window.FocoDropzone) {
                    var dzEl = $('.foco-dropzone')[0];
                    if (dzEl) {
                        window.FocoDropzone.reset(dzEl);
                        if (u.avatar_url) {
                            window.FocoDropzone.setPreview(dzEl, u.avatar_url);
                        }
                    }
                }

                $('#modalUsuarioTabs button:first').tab('show');
                var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
                modal.show();
            }
        }).fail(function() {
            btn.prop('disabled', false);
            toastr.error('Erro ao carregar dados do usuário.');
        });
    });

    // Busca de CEP no Modal
    $('#btnBuscarCepUsuario').on('click', function() {
        var cep = $('#userCep').val().replace(/\D/g, '');
        if (cep.length !== 8) {
            Swal.fire({
                icon: 'warning',
                title: 'CEP Inválido',
                text: 'Digite um CEP com 8 dígitos para consultar.',
                confirmButtonColor: '#d90000'
            });
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(dados) {
            btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
            if (!("erro" in dados)) {
                $('#userLogradouro').val(dados.logradouro);
                $('#userBairro').val(dados.bairro);
                $('#userCidade').val(dados.localidade);
                $('#userEstado').val(dados.uf);
                toastr.success('Endereço preenchido!');
            } else {
                toastr.error('CEP não encontrado.');
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
            toastr.error('Erro ao consultar ViaCEP.');
        });
    });

    // Toggle Status Ativo/Inativo (AJAX)
    $(document).on('change', '.btn-toggle-status-usuario', function() {
        var id = $(this).data('id');
        var input = $(this);

        $.post("/admin/usuarios/toggle/" + id, {
            _token: "{{ csrf_token() }}"
        }, function(res) {
            if (res.success) {
                toastr.success(res.message);
            }
        }).fail(function(xhr) {
            input.prop('checked', !input.prop('checked'));
            var msg = 'Não foi possível alterar o status.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            toastr.error(msg);
        });
    });

    // Redefinir Senha (Modal Rápido)
    $(document).on('click', '.btn-reset-senha', function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        $('#resetNomeUsuario').text(nome);
        $('#formResetSenha')[0].reset();
        $('#formResetSenha').attr('action', "/admin/usuarios/reset-senha/" + id);

        var modal = new bootstrap.Modal(document.getElementById('modalResetSenha'));
        modal.show();
    });

    // Excluir Usuário (SweetAlert2 - Zero alert nativo)
    $(document).on('click', '.btn-excluir-usuario', function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');

        Swal.fire({
            title: 'Excluir Usuário?',
            html: 'Tem certeza que deseja excluir o usuário <strong>' + nome + '</strong>?<br><span class="text-danger small">Esta ação não poderá ser desfeita.</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/admin/usuarios/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            $('#usuario-row-' + id).fadeOut(400, function() {
                                $(this).remove();
                            });
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Erro ao excluir usuário.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Ação Bloqueada',
                            text: msg,
                            confirmButtonColor: '#d90000'
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
