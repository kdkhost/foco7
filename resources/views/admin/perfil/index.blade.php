@extends('admin.layouts.app')

@section('title', 'Meu Perfil')
@section('page_title', 'Perfil de Usuário')
@section('page_description', 'Gerencie suas informações pessoais, avatar e credenciais de segurança.')

@section('content')
<div class="row g-4">
    <!-- COLUNA DA ESQUERDA: AVATAR & IDENTIFICAÇÃO -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body p-4">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->avatar)
                        <img id="perfilAvatarImg" src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border border-danger p-1 shadow" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div id="perfilAvatarPlaceholder" class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1 mx-auto shadow" style="width: 120px; height: 120px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 badge bg-success rounded-pill p-2" title="Usuário Ativo">
                        <i class="fas fa-check"></i>
                    </span>
                </div>

                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-secondary small mb-2">{{ $user->email }}</p>
                <div>
                    <span class="badge bg-danger text-uppercase px-3 py-2">
                        <i class="fas fa-shield-alt me-1"></i> {{ $user->role === 'admin' ? 'Super Administrador' : 'Cliente' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- CARD PARA TROCA DE AVATAR COM DRAG & DROP -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0 text-white">
                    <i class="fas fa-camera text-danger me-2"></i> Foto de Perfil (Avatar)
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.perfil.salvar_avatar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Enviar Nova Foto</label>
                        <div class="foco-dropzone"
                             data-input-name="avatar_upload"
                             data-ratio="300 × 300 px (1:1 Quadrado)"
                             data-max-size="5MB"
                             data-accept="image/jpeg,image/png,image/webp"
                             data-current-preview="{{ $user->avatar ? asset($user->avatar) : '' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold">
                        <i class="fas fa-upload me-1"></i> ATUALIZAR AVATAR
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- COLUNA DA DIREITA: ABAS DE DADOS E SEGURANÇA -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="perfilTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="dados-tab" data-bs-toggle="tab" data-bs-target="#tab-dados" type="button">
                            <i class="fas fa-user-edit text-danger me-1"></i> Dados Cadastrais
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="senha-tab" data-bs-toggle="tab" data-bs-target="#tab-senha" type="button">
                            <i class="fas fa-key text-danger me-1"></i> Segurança & Senha
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="perfilTabsContent">
                    
                    <!-- ABA 1: DADOS CADASTRAIS -->
                    <div class="tab-pane fade show active" id="tab-dados" role="tabpanel">
                        <form action="{{ route('admin.perfil.salvar_dados') }}" method="POST" class="ajax-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome Completo *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required placeholder="Seu nome completo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">E-mail de Acesso *</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required placeholder="admin@foco7.com.br">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Telefone / WhatsApp</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" value="{{ $user->telefone }}" placeholder="(91) 98051-0736">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">CPF ou CNPJ</label>
                                    <input type="text" name="cpf_cnpj" class="form-control mask-cpf-cnpj" value="{{ $user->cpf_cnpj }}" placeholder="000.000.000-00">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> SALVAR DADOS PESSOAIS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA 2: SEGURANÇA E SENHA -->
                    <div class="tab-pane fade" id="tab-senha" role="tabpanel">
                        <form action="{{ route('admin.perfil.alterar_senha') }}" method="POST" class="ajax-form reset-on-success">
                            @csrf
                            <div class="alert alert-warning py-2 small mb-4">
                                <i class="fas fa-shield-alt me-1"></i> A senha deve possuir no mínimo 8 caracteres, mesclando letras e números.
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Senha Atual *</label>
                                    <div class="input-group">
                                        <input type="password" name="senha_atual" id="senhaAtual" class="form-control" required placeholder="Digite sua senha atual">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('senhaAtual')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nova Senha *</label>
                                    <div class="input-group">
                                        <input type="password" name="nova_senha" id="novaSenha" class="form-control" required minlength="8" placeholder="Mínimo 8 dígitos">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('novaSenha')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Confirmar Nova Senha *</label>
                                    <div class="input-group">
                                        <input type="password" name="nova_senha_confirmation" id="novaSenhaConf" class="form-control" required minlength="8" placeholder="Repita a nova senha">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('novaSenhaConf')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                        <i class="fas fa-lock me-1"></i> ALTERAR SENHA COM SEGURANÇA
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSenha(inputId) {
        var input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
</script>
@endsection
