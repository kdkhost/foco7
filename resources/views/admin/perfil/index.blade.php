@extends('admin.layouts.app')

@section('title', 'Meu Perfil')
@section('page_title', 'Meu Perfil de Usuário')
@section('page_description', 'Gerencie seus dados pessoais completos, endereço, foto de perfil e autenticação em 2 etapas (2FA).')

@section('content')
<div class="row g-4">
    <!-- COLUNA DA ESQUERDA: AVATAR & IDENTIFICAÇÃO -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body p-4">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->avatar)
                        <img id="perfilAvatarImg" src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle border border-danger p-1 shadow" style="width: 130px; height: 130px; object-fit: cover;">
                    @else
                        <div id="perfilAvatarPlaceholder" class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1 mx-auto shadow" style="width: 130px; height: 130px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="position-absolute bottom-0 end-0 badge {{ $user->status === 'ativo' ? 'bg-success' : 'bg-secondary' }} rounded-pill p-2" title="{{ $user->status === 'ativo' ? 'Usuário Ativo' : 'Inativo' }}">
                        <i class="fas fa-check"></i>
                    </span>
                </div>

                <h4 class="fw-bold mb-1" id="perfilNomeExibicao">{{ $user->name }}</h4>
                <p class="text-secondary small mb-2">{{ $user->email }}</p>
                
                <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                    <span class="status-badge-pill status-badge-vencida">
                        <span class="status-dot"></span>
                        <i class="fas fa-shield-alt"></i>
                        <span>{{ strtoupper($user->role) }}</span>
                    </span>
                    @if($user->hasTwoFactor())
                        <span class="status-badge-pill status-badge-pago" title="Autenticação em Duas Etapas Ativa">
                            <span class="status-dot"></span>
                            <i class="fas fa-lock"></i>
                            <span>2FA ATIVO</span>
                        </span>
                    @else
                        <span class="status-badge-pill status-badge-cancelado" title="2FA Não Configurado">
                            <span class="status-dot"></span>
                            <i class="fas fa-unlock"></i>
                            <span>2FA INATIVO</span>
                        </span>
                    @endif
                </div>

                @if($user->cargo)
                    <div class="text-muted small">
                        <i class="fas fa-briefcase me-1 text-danger"></i> {{ $user->cargo }}
                    </div>
                @endif
                @if($user->telefone)
                    <div class="text-muted small mt-1">
                        <i class="fab fa-whatsapp me-1 text-success"></i> {{ $user->telefone }}
                    </div>
                @endif
            </div>
        </div>

        <!-- CARD PARA TROCA DE AVATAR COM FOCO DROPZONE -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-camera text-danger me-2"></i> Foto de Perfil (Avatar)
                </h6>
                @if($user->avatar)
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="btnRemoverAvatarPerfil" title="Remover Foto Atual">
                        <i class="fas fa-trash-alt me-1"></i> Remover
                    </button>
                @endif
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.perfil.salvar_avatar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formAvatarPerfil">
                    @csrf
                    <input type="hidden" name="remover_avatar" id="inputRemoverAvatar" value="0">
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
                        <i class="fas fa-upload me-1"></i> ATUALIZAR FOTO DE PERFIL
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- COLUNA DA DIREITA: ABAS COMPLETAS -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="perfilTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="dados-tab" data-bs-toggle="tab" data-bs-target="#tab-dados" type="button">
                            <i class="fas fa-user-edit text-danger me-1"></i> Dados Pessoais
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="endereco-tab" data-bs-toggle="tab" data-bs-target="#tab-endereco" type="button">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i> Endereço Completo
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="senha-tab" data-bs-toggle="tab" data-bs-target="#tab-senha" type="button">
                            <i class="fas fa-key text-danger me-1"></i> Segurança & Senha
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="twofa-tab" data-bs-toggle="tab" data-bs-target="#tab-twofa" type="button">
                            <i class="fas fa-shield-alt text-danger me-1"></i> Autenticação 2FA
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="perfilTabsContent">
                    
                    <!-- ABA 1: DADOS PESSOAIS -->
                    <div class="tab-pane fade show active" id="tab-dados" role="tabpanel">
                        <form action="{{ route('admin.perfil.salvar_dados') }}" method="POST" class="ajax-form keep-data" id="formDadosPessoais">
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
                                    <label class="form-label small fw-bold">CPF ou CNPJ</label>
                                    <input type="text" name="cpf_cnpj" class="form-control mask-cpf-cnpj" value="{{ $user->cpf_cnpj }}" placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Telefone / WhatsApp</label>
                                    <input type="text" name="telefone" class="form-control mask-telefone" value="{{ $user->telefone }}" placeholder="(91) 98051-0736">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Data de Nascimento</label>
                                    <input type="date" name="data_nascimento" class="form-control" value="{{ $user->data_nascimento ? $user->data_nascimento->format('Y-m-d') : '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Gênero</label>
                                    <select name="genero" class="form-select">
                                        <option value="">Selecione...</option>
                                        <option value="Masculino" {{ $user->genero === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Feminino" {{ $user->genero === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                        <option value="Outro" {{ $user->genero === 'Outro' ? 'selected' : '' }}>Outro</option>
                                        <option value="Prefiro não informar" {{ $user->genero === 'Prefiro não informar' ? 'selected' : '' }}>Prefiro não informar</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Cargo / Função</label>
                                    <input type="text" name="cargo" class="form-control" value="{{ $user->cargo }}" placeholder="Ex: Diretor Geral">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Biografia / Observações</label>
                                    <textarea name="bio" class="form-control" rows="3" placeholder="Breve apresentação profissional...">{{ $user->bio }}</textarea>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> SALVAR DADOS PESSOAIS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA 2: ENDEREÇO COMPLETO -->
                    <div class="tab-pane fade" id="tab-endereco" role="tabpanel">
                        <form action="{{ route('admin.perfil.salvar_dados') }}" method="POST" class="ajax-form keep-data" id="formEndereco">
                            @csrf
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">CEP</label>
                                    <div class="input-group">
                                        <input type="text" name="cep" id="perfilCep" class="form-control mask-cep" value="{{ $user->cep }}" placeholder="00000-000">
                                        <button type="button" class="btn btn-outline-secondary" id="btnBuscarCepPerfil" title="Buscar Endereço no ViaCEP">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small text-muted">Digite o CEP para preenchimento automático.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Logradouro / Rua</label>
                                    <input type="text" name="endereco" id="perfilLogradouro" class="form-control" value="{{ $user->endereco }}" placeholder="Avenida, Rua, Travessa...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">Número</label>
                                    <input type="text" name="numero" class="form-control" value="{{ $user->numero }}" placeholder="123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Complemento</label>
                                    <input type="text" name="complemento" class="form-control" value="{{ $user->complemento }}" placeholder="Apto, Sala, Bloco...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Bairro</label>
                                    <input type="text" name="bairro" id="perfilBairro" class="form-control" value="{{ $user->bairro }}" placeholder="Bairro">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Cidade</label>
                                    <input type="text" name="cidade" id="perfilCidade" class="form-control" value="{{ $user->cidade }}" placeholder="Cidade">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small fw-bold">UF</label>
                                    <input type="text" name="estado" id="perfilEstado" class="form-control text-uppercase" maxlength="2" value="{{ $user->estado }}" placeholder="PA">
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                                        <i class="fas fa-save me-1"></i> SALVAR ENDEREÇO COMPLETO
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ABA 3: SEGURANÇA E SENHA -->
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

                    <!-- ABA 4: AUTENTICAÇÃO DE 2 FATORES (2FA) -->
                    <div class="tab-pane fade" id="tab-twofa" role="tabpanel">
                        <div class="row g-4 align-items-center mb-4">
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-1">
                                    <i class="fas fa-shield-alt text-danger me-2"></i> Proteção com Autenticação em Duas Etapas
                                </h5>
                                <p class="text-secondary small mb-0">
                                    Adiciona uma camada extra de segurança robusta à sua conta exigindo um código de 6 dígitos gerado pelo Google Authenticator, Microsoft Authenticator ou Authy a cada novo login.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                @if($user->hasTwoFactor())
                                    <span class="badge bg-success fs-6 py-2 px-3">
                                        <i class="fas fa-check-circle me-1"></i> 2FA ATIVADO
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6 py-2 px-3">
                                        <i class="fas fa-times-circle me-1"></i> 2FA DESATIVADO
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($user->hasTwoFactor())
                            <!-- SE 2FA JÁ ESTÁ ATIVO -->
                            <div class="card bg-body-tertiary border-0 rounded-4 p-4 mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center p-3 shadow-sm">
                                        <i class="fas fa-shield-check fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-success">Sua conta está protegida por 2FA!</h6>
                                        <p class="small text-secondary mb-0">
                                            Configurado em: <strong>{{ $user->two_factor_confirmed_at ? $user->two_factor_confirmed_at->format('d/m/Y H:i') : 'Ativo' }}</strong>.
                                        </p>
                                    </div>
                                </div>

                                <hr class="my-3 opacity-25">

                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btnVerRecoveryCodes">
                                        <i class="fas fa-key me-1"></i> Ver Códigos de Recuperação
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" id="btnRegenerarRecoveryCodes">
                                        <i class="fas fa-sync-alt me-1"></i> Gerar Novos Códigos de Backup
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-semibold ms-auto" id="btnDesativar2fa">
                                        <i class="fas fa-power-off me-1"></i> Desativar 2FA
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- SE 2FA ESTÁ DESATIVADO: ETAPA DE ATIVAÇÃO -->
                            <div id="boxSetup2fa" class="card bg-body-tertiary border-0 rounded-4 p-4">
                                <div id="etapaIniciar2fa">
                                    <div class="text-center py-3">
                                        <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center p-3 mb-3 shadow">
                                            <i class="fas fa-mobile-alt fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Configurar Aplicativo Autenticador</h5>
                                        <p class="text-secondary small mx-auto" style="max-width: 500px;">
                                            Você precisará de um aplicativo como <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, <strong>Authy</strong> ou <strong>1Password</strong> instalado no seu smartphone.
                                        </p>
                                        <button type="button" class="btn btn-danger px-4 py-2 fw-bold" id="btnIniciarConfig2fa">
                                            <i class="fas fa-qrcode me-1"></i> CONFIGURAR 2FA AGORA
                                        </button>
                                    </div>
                                </div>

                                <div id="etapaConfigurar2fa" class="d-none">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-md-5 text-center">
                                            <div class="p-3 bg-white rounded-3 shadow-sm d-inline-block border">
                                                <img id="imgQrCode2fa" src="" alt="QR Code 2FA" style="width: 200px; height: 200px; object-fit: contain;">
                                            </div>
                                            <div class="small text-muted mt-2">Escaneie este QR Code com o aplicativo</div>
                                        </div>
                                        <div class="col-md-7">
                                            <h6 class="fw-bold mb-2">1. Chave Manual de Configuração</h6>
                                            <p class="small text-secondary mb-2">
                                                Se não conseguir ler o QR Code, digite a chave abaixo manualmente no app:
                                            </p>
                                            <div class="input-group mb-3">
                                                <input type="text" id="inputSecret2fa" class="form-control font-monospace fw-bold" readonly>
                                                <button class="btn btn-outline-secondary" type="button" id="btnCopiarSecret2fa" title="Copiar Chave">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>

                                            <h6 class="fw-bold mb-2">2. Digite o código de 6 dígitos</h6>
                                            <p class="small text-secondary mb-2">
                                                Insira o código gerado no aplicativo para confirmar a sincronização:
                                            </p>
                                            <div class="input-group mb-3">
                                                <input type="text" id="inputCodeConfirmar2fa" class="form-control form-control-lg text-center fw-bold font-monospace" maxlength="6" placeholder="000000" style="letter-spacing: 4px;">
                                                <button class="btn btn-danger px-4 fw-bold" type="button" id="btnConfirmarCodigo2fa">
                                                    <i class="fas fa-check-circle me-1"></i> ATIVAR 2FA
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA EXIBIR / SALVAR RECOVERY CODES -->
<div class="modal fade" id="modalRecoveryCodes" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-key me-2"></i> Códigos de Recuperação de Emergência
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-modal-dismiss="modal" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning py-2 small mb-3">
                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>Importante:</strong> Guarde estes códigos em um local seguro. Caso perca acesso ao seu celular, você precisará de um destes códigos para recuperar seu login. Cada código funciona uma única vez.
                </div>

                <div id="listaRecoveryCodes" class="row g-2 font-monospace fw-bold text-center mb-3">
                    <!-- Preenchido via JavaScript -->
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-50" id="btnCopiarRecoveryCodes">
                        <i class="fas fa-copy me-1"></i> Copiar Todos
                    </button>
                    <button type="button" class="btn btn-outline-danger w-50" id="btnBaixarRecoveryCodes">
                        <i class="fas fa-download me-1"></i> Baixar (.txt)
                    </button>
                </div>
            </div>
            <div class="modal-footer bg-body-tertiary">
                <button type="button" class="btn btn-danger px-4 fw-bold" data-bs-dismiss="modal">
                    Entendi e Guardei os Códigos
                </button>
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

    // Busca automática de CEP via ViaCEP
    $('#btnBuscarCepPerfil').on('click', function() {
        var cep = $('#perfilCep').val().replace(/\D/g, '');
        if (cep.length !== 8) {
            Swal.fire({
                icon: 'warning',
                title: 'CEP Inválido',
                text: 'Digite um CEP válido com 8 dígitos para realizar a busca.',
                confirmButtonColor: '#d90000'
            });
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(dados) {
            btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
            if (!("erro" in dados)) {
                $('#perfilLogradouro').val(dados.logradouro);
                $('#perfilBairro').val(dados.bairro);
                $('#perfilCidade').val(dados.localidade);
                $('#perfilEstado').val(dados.uf);
                toastr.success('Endereço preenchido automaticamente com sucesso!');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'CEP Não Encontrado',
                    text: 'Não foram localizados dados para o CEP informado.',
                    confirmButtonColor: '#d90000'
                });
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
            toastr.error('Erro ao consultar ViaCEP.');
        });
    });

    // Remover Avatar via AJAX
    $('#btnRemoverAvatarPerfil').on('click', function() {
        Swal.fire({
            title: 'Remover Foto de Perfil?',
            text: 'Sua foto atual será excluída e voltará ao avatar padrão.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('admin.perfil.salvar_avatar') }}", {
                    _token: "{{ csrf_token() }}",
                    remover_avatar: 1
                }, function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.reload(), 800);
                    }
                }).fail(function() {
                    toastr.error('Erro ao remover avatar.');
                });
            }
        });
    });

    // FLUXO DO 2FA
    var secretTemp = '';
    var recoveryCodesAtuais = [];

    $('#btnIniciarConfig2fa').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> GERANDO QR CODE...');

        $.post("{{ route('admin.perfil.2fa.iniciar') }}", {
            _token: "{{ csrf_token() }}"
        }, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-qrcode me-1"></i> CONFIGURAR 2FA AGORA');
            if (res.success) {
                secretTemp = res.secret;
                $('#imgQrCode2fa').attr('src', res.qrcode_url);
                $('#inputSecret2fa').val(res.secret_formatted);
                $('#etapaIniciar2fa').addClass('d-none');
                $('#etapaConfigurar2fa').removeClass('d-none');
                $('#inputCodeConfirmar2fa').focus();
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<i class="fas fa-qrcode me-1"></i> CONFIGURAR 2FA AGORA');
            toastr.error('Erro ao iniciar configuração do 2FA.');
        });
    });

    // Copiar Secret
    $('#btnCopiarSecret2fa').on('click', function() {
        var input = document.getElementById('inputSecret2fa');
        input.select();
        navigator.clipboard.writeText(secretTemp);
        toastr.info('Chave secreta copiada para a área de transferência!');
    });

    // Confirmar código e ativar 2FA
    $('#btnConfirmarCodigo2fa').on('click', function() {
        var code = $('#inputCodeConfirmar2fa').val().trim();
        if (code.length < 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Código Incompleto',
                text: 'Digite o código de 6 dígitos gerado no app autenticador.',
                confirmButtonColor: '#d90000'
            });
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> VERIFICANDO...');

        $.post("{{ route('admin.perfil.2fa.confirmar') }}", {
            _token: "{{ csrf_token() }}",
            secret: secretTemp,
            code: code
        }, function(res) {
            btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> ATIVAR 2FA');
            if (res.success) {
                recoveryCodesAtuais = res.recovery_codes || [];
                renderizarModalRecoveryCodes(recoveryCodesAtuais);
                var modal = new bootstrap.Modal(document.getElementById('modalRecoveryCodes'));
                modal.show();
                $('#modalRecoveryCodes').on('hidden.bs.modal', function() {
                    window.location.reload();
                });
            }
        }).fail(function(xhr) {
            btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> ATIVAR 2FA');
            var msg = 'Código incorreto ou expirado. Tente novamente.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Código Inválido',
                text: msg,
                confirmButtonColor: '#d90000'
            });
        });
    });

    function renderizarModalRecoveryCodes(codes) {
        var html = '';
        codes.forEach(function(item) {
            var cod = typeof item === 'object' ? item.code : item;
            var isUsed = typeof item === 'object' && item.used;
            html += '<div class="col-6"><div class="p-2 border rounded ' + (isUsed ? 'bg-secondary text-decoration-line-through text-white opacity-50' : 'bg-body-secondary') + '">' + cod + '</div></div>';
        });
        $('#listaRecoveryCodes').html(html);
    }

    // Copiar Recovery Codes
    $('#btnCopiarRecoveryCodes').on('click', function() {
        var texto = recoveryCodesAtuais.map(function(item) {
            return typeof item === 'object' ? item.code : item;
        }).join("\n");
        navigator.clipboard.writeText(texto);
        toastr.success('Códigos copiados para a área de transferência!');
    });

    // Baixar Recovery Codes
    $('#btnBaixarRecoveryCodes').on('click', function() {
        var texto = "FOCO 7 - CODIGOS DE RECUPERACAO 2FA\nData: " + new Date().toLocaleString() + "\n\n" +
            recoveryCodesAtuais.map(function(item) {
                return typeof item === 'object' ? item.code : item;
            }).join("\n") +
            "\n\nGuarde estes codigos em local seguro. Cada um so funciona uma unica vez.";
        var blob = new Blob([texto], { type: 'text/plain;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'foco7_recovery_codes_2fa.txt';
        a.click();
    });

    // Desativar 2FA
    $('#btnDesativar2fa').on('click', function() {
        Swal.fire({
            title: 'Desativar 2FA?',
            text: 'Informe sua senha de acesso ou o código atual de 6 dígitos para autorizar:',
            input: 'password',
            inputPlaceholder: 'Sua senha ou código TOTP',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Desativar 2FA',
            cancelButtonText: 'Cancelar',
            preConfirm: (input) => {
                if (!input) {
                    Swal.showValidationMessage('Informe a senha ou código para confirmar.');
                }
                return input;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('admin.perfil.2fa.desativar') }}", {
                    _token: "{{ csrf_token() }}",
                    senha_ou_codigo: result.value
                }, function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.reload(), 800);
                    }
                }).fail(function(xhr) {
                    var msg = 'Senha ou código inválido.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Não Autorizado',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                });
            }
        });
    });

    // Ver Códigos de Recuperação
    $('#btnVerRecoveryCodes').on('click', function() {
        Swal.fire({
            title: 'Visualizar Códigos de Backup',
            text: 'Por segurança, digite sua senha de acesso atual:',
            input: 'password',
            inputPlaceholder: 'Sua senha atual',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Exibir Códigos',
            cancelButtonText: 'Cancelar',
            preConfirm: (pass) => {
                if (!pass) Swal.showValidationMessage('Digite sua senha.');
                return pass;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('admin.perfil.2fa.recovery_codes') }}", {
                    _token: "{{ csrf_token() }}",
                    password: result.value
                }, function(res) {
                    if (res.success) {
                        recoveryCodesAtuais = res.codes || [];
                        renderizarModalRecoveryCodes(recoveryCodesAtuais);
                        var modal = new bootstrap.Modal(document.getElementById('modalRecoveryCodes'));
                        modal.show();
                    }
                }).fail(function(xhr) {
                    var msg = 'Senha incorreta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    toastr.error(msg);
                });
            }
        });
    });

    // Regenerar Códigos de Recuperação
    $('#btnRegenerarRecoveryCodes').on('click', function() {
        Swal.fire({
            title: 'Gerar Novos Códigos de Backup?',
            text: 'Os códigos anteriores serão invalidados imediatamente. Digite sua senha para confirmar:',
            input: 'password',
            inputPlaceholder: 'Sua senha atual',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Gerar Novos Códigos',
            cancelButtonText: 'Cancelar',
            preConfirm: (pass) => {
                if (!pass) Swal.showValidationMessage('Digite sua senha.');
                return pass;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('admin.perfil.2fa.regenerar_recovery_codes') }}", {
                    _token: "{{ csrf_token() }}",
                    password: result.value
                }, function(res) {
                    if (res.success) {
                        recoveryCodesAtuais = res.codes || [];
                        renderizarModalRecoveryCodes(recoveryCodesAtuais);
                        var modal = new bootstrap.Modal(document.getElementById('modalRecoveryCodes'));
                        modal.show();
                        toastr.success(res.message);
                    }
                }).fail(function(xhr) {
                    var msg = 'Senha incorreta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    toastr.error(msg);
                });
            }
        });
    });
</script>
@endsection
