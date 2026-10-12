@extends('cliente.layouts.app')

@section('title', 'Meu Perfil & Senha')
@section('page_title', 'Meu Perfil & Segurança')
@section('page_description', 'Gerencie seus dados cadastrais, endereço e alteração segura de senha de acesso.')

@section('content')
<div class="row g-4">
    <!-- COLUNA DA ESQUERDA: AVATAR & IDENTIFICAÇÃO -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body p-4">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->avatar)
                        <img id="perfilAvatarImg" src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border border-primary p-1 shadow" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div id="perfilAvatarPlaceholder" class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1 mx-auto shadow" style="width: 120px; height: 120px;">
                            {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 badge bg-success rounded-pill p-2" title="Assinante Ativo">
                        <i class="fas fa-check"></i>
                    </span>
                </div>

                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-secondary small mb-2">{{ $user->email }}</p>
                <div>
                    <span class="badge bg-primary text-uppercase px-3 py-2">
                        <i class="fas fa-shield-alt me-1"></i> Cliente Assinante
                    </span>
                </div>
            </div>
        </div>

        <!-- CARD PARA TROCA DE AVATAR COM DRAG & DROP -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-camera text-primary me-2"></i> Foto de Perfil (Avatar)
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('cliente.perfil.avatar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
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
                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">
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
                            <i class="fas fa-user-edit text-primary me-1"></i> Dados Cadastrais &amp; Endereço
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="senha-tab" data-bs-toggle="tab" data-bs-target="#tab-senha" type="button">
                            <i class="fas fa-key text-primary me-1"></i> Segurança &amp; Senha
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="perfilTabsContent">
                    
                    <!-- ABA 1: DADOS CADASTRAIS & ENDEREÇO -->
                    <div class="tab-pane fade show active" id="tab-dados" role="tabpanel">
                        <form action="{{ route('cliente.perfil.dados') }}" method="POST" class="ajax-form keep-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome Completo *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required placeholder="Seu nome completo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">E-mail Cadastrado</label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled title="Para alterar o e-mail cadastrado, entre em contato com o suporte">
                                    <div class="form-text small text-secondary">E-mail de login oficial (não alterável).</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">WhatsApp / Telefone *</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" value="{{ $user->telefone }}" required placeholder="(91) 98051-0736">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">CPF ou CNPJ</label>
                                    <input type="text" name="cpf_cnpj" class="form-control mask-cpf-cnpj" value="{{ $user->cpf_cnpj }}" placeholder="000.000.000-00">
                                </div>

                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-12"><h6 class="fw-bold mb-0 text-primary"><i class="fas fa-map-marker-alt me-1"></i> Endereço de Instalação / Cobrança</h6></div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">CEP</label>
                                    <input type="text" name="cep" id="cepCliente" class="form-control mask-cep" value="{{ $user->cep }}" placeholder="66000-000">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">Logradouro / Rua</label>
                                    <input type="text" name="endereco" id="enderecoCliente" class="form-control" value="{{ $user->endereco }}" placeholder="Av. Nazaré, Rua das Flores...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Número</label>
                                    <input type="text" name="numero" class="form-control" value="{{ $user->numero }}" placeholder="123 / Bloco A">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Bairro</label>
                                    <input type="text" name="bairro" id="bairroCliente" class="form-control" value="{{ $user->bairro }}" placeholder="Centro, Nazaré...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Cidade</label>
                                    <input type="text" name="cidade" id="cidadeCliente" class="form-control" value="{{ $user->cidade }}" placeholder="Belém, Ananindeua...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">UF / Estado</label>
                                    <input type="text" name="estado" id="estadoCliente" class="form-control text-uppercase" maxlength="2" value="{{ $user->estado }}" placeholder="PA">
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> SALVAR DADOS CADASTRAIS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA 2: SEGURANÇA E SENHA -->
                    <div class="tab-pane fade" id="tab-senha" role="tabpanel">
                        <form action="{{ route('cliente.perfil.senha') }}" method="POST" class="ajax-form">
                            @csrf
                            <div class="alert alert-warning py-2 small mb-4">
                                <i class="fas fa-shield-alt me-1"></i> A senha de acesso deve possuir no mínimo 6 caracteres para garantir a segurança da sua conta.
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Senha Atual *</label>
                                    <div class="input-group">
                                        <input type="password" name="senha_atual" id="senhaAtualCli" class="form-control" required placeholder="Digite sua senha atual de acesso">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('senhaAtualCli')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nova Senha *</label>
                                    <div class="input-group">
                                        <input type="password" name="nova_senha" id="novaSenhaCli" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('novaSenhaCli')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Confirmar Nova Senha *</label>
                                    <div class="input-group">
                                        <input type="password" name="nova_senha_confirmation" id="novaSenhaConfCli" class="form-control" required minlength="6" placeholder="Repita a nova senha">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('novaSenhaConfCli')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                                        <i class="fas fa-lock me-1"></i> ATUALIZAR MINHA SENHA
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
        var icon = event.currentTarget.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Auto-preenchimento via CEP (ViaCEP)
    $('#cepCliente').on('blur', function () {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function (dados) {
                if (!("erro" in dados)) {
                    $('#enderecoCliente').val(dados.logradouro);
                    $('#bairroCliente').val(dados.bairro);
                    $('#cidadeCliente').val(dados.localidade);
                    $('#estadoCliente').val(dados.uf);
                }
            });
        }
    });
</script>
@endsection
