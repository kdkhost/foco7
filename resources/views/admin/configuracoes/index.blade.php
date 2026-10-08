@extends('admin.layouts.app')

@section('title', 'Configurações do Sistema & SMTP')
@section('page_title', 'Configurações Gerais & E-mail')
@section('page_description', 'Gerencie dados institucionais, identidade visual, redes sociais e servidor SMTP.')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="configTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="empresa-tab" data-bs-toggle="tab" data-bs-target="#tab-empresa" type="button">
                    <i class="fas fa-building text-danger me-1"></i> Dados da Empresa
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="branding-tab" data-bs-toggle="tab" data-bs-target="#tab-branding" type="button">
                    <i class="fas fa-image text-danger me-1"></i> Identidade Visual
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button">
                    <i class="fas fa-envelope-open-text text-danger me-1"></i> Servidor SMTP
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="sociais-tab" data-bs-toggle="tab" data-bs-target="#tab-sociais" type="button">
                    <i class="fas fa-share-alt text-danger me-1"></i> Redes Sociais
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('admin.configuracoes.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form" id="formConfiguracoes">
            @csrf

            <div class="tab-content" id="configTabsContent">

                <!-- ABA 1: DADOS DA EMPRESA -->
                <div class="tab-pane fade show active" id="tab-empresa" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome Fantasia do Sistema *</label>
                            <input type="text" name="site_nome" class="form-control" value="{{ $configs['site_nome'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Razão Social</label>
                            <input type="text" name="razao_social" class="form-control" value="{{ $configs['razao_social'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control mask-cnpj" value="{{ $configs['cnpj'] }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Slogan Institucional</label>
                            <input type="text" name="site_slogan" class="form-control" value="{{ $configs['site_slogan'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Telefone Central / Fixo</label>
                            <input type="text" name="contato_telefone" class="form-control mask-telefone" value="{{ $configs['contato_telefone'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">WhatsApp Oficial (Com DDI e DDD)</label>
                            <input type="text" name="contato_whatsapp" class="form-control" value="{{ $configs['contato_whatsapp'] }}" placeholder="559180510736">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">WhatsApp Formatado (Exibição)</label>
                            <input type="text" name="contato_whatsapp_formatado" class="form-control mask-telefone" value="{{ $configs['contato_whatsapp_formatado'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">E-mail Comercial / Atendimento</label>
                            <input type="email" name="contato_email" class="form-control" value="{{ $configs['contato_email'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Horário de Atendimento</label>
                            <input type="text" name="horario_atendimento" class="form-control" value="{{ $configs['horario_atendimento'] }}">
                        </div>

                        <div class="col-12 mt-3"><h6 class="fw-bold border-bottom pb-2 text-danger">Endereço da Sede</h6></div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Logradouro</label>
                            <input type="text" name="endereco_logradouro" class="form-control" value="{{ $configs['endereco_logradouro'] }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Número</label>
                            <input type="text" name="endereco_numero" class="form-control" value="{{ $configs['endereco_numero'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Bairro</label>
                            <input type="text" name="endereco_bairro" class="form-control" value="{{ $configs['endereco_bairro'] }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Cidade</label>
                            <input type="text" name="endereco_cidade" class="form-control" value="{{ $configs['endereco_cidade'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Estado (UF)</label>
                            <input type="text" name="endereco_estado" class="form-control text-uppercase" maxlength="2" value="{{ $configs['endereco_estado'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CEP</label>
                            <input type="text" name="endereco_cep" class="form-control mask-cep" value="{{ $configs['endereco_cep'] }}">
                        </div>
                    </div>
                </div>

                <!-- ABA 2: IDENTIDADE VISUAL COM ARRASTA E SOLTA -->
                <div class="tab-pane fade" id="tab-branding" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card bg-dark border-secondary p-3 h-100">
                                <label class="form-label fw-bold text-white mb-2">
                                    <i class="fas fa-image text-danger me-1"></i> Logotipo Principal da Marca
                                </label>
                                <div class="foco-dropzone"
                                     data-input-name="logo_upload"
                                     data-ratio="500 × 120 px (Horizontal Transparente)"
                                     data-max-size="5MB"
                                     data-accept="image/png,image/webp,image/jpeg"
                                     data-current-preview="{{ asset($configs['site_logo']) }}">
                                </div>
                                <div class="small text-secondary mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Recomendado fundo transparente (PNG ou WEBP) para perfeita visualização nos modos claro e escuro.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-dark border-secondary p-3 h-100">
                                <label class="form-label fw-bold text-white mb-2">
                                    <i class="fas fa-gem text-danger me-1"></i> Ícone Favicon do Navegador
                                </label>
                                <div class="foco-dropzone"
                                     data-input-name="favicon_upload"
                                     data-ratio="512 × 512 px (Proporção 1:1 Quadrado)"
                                     data-max-size="2MB"
                                     data-accept="image/png,image/x-icon,image/webp"
                                     data-current-preview="{{ asset($configs['site_favicon']) }}">
                                </div>
                                <div class="small text-secondary mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Exibido na aba do navegador, favoritos e atalhos de celular.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 3: SERVIDOR SMTP -->
                <div class="tab-pane fade" id="tab-smtp" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Driver / Protocolo</label>
                                    <input type="text" name="mail_mailer" class="form-control" value="{{ $configs['mail_mailer'] }}" readonly>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold">Servidor Host SMTP *</label>
                                    <input type="text" name="mail_host" id="smtpHost" class="form-control" value="{{ $configs['mail_host'] }}" placeholder="mail.foco7.com.br" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Porta *</label>
                                    <input type="number" name="mail_port" id="smtpPort" class="form-control" value="{{ $configs['mail_port'] }}" placeholder="465 ou 587" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Criptografia</label>
                                    <select name="mail_encryption" id="smtpEncryption" class="form-select">
                                        <option value="ssl" {{ $configs['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL (Porta 465 recomendada)</option>
                                        <option value="tls" {{ $configs['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS (Porta 587)</option>
                                        <option value="nenhum" {{ $configs['mail_encryption'] === 'nenhum' ? 'selected' : '' }}>Nenhuma</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Usuário / Conta SMTP *</label>
                                    <input type="text" name="mail_username" id="smtpUsername" class="form-control" value="{{ $configs['mail_username'] }}" placeholder="noreply@foco7.com.br" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Senha da Conta SMTP</label>
                                    <div class="input-group">
                                        <input type="password" name="mail_password" id="smtpPassword" class="form-control" placeholder="••••••••" value="{{ $configs['mail_password'] }}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('smtpPassword')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">E-mail Remetente (From Address) *</label>
                                    <input type="email" name="mail_from_address" id="smtpFromAddress" class="form-control" value="{{ $configs['mail_from_address'] }}" placeholder="contato@foco7.com.br" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nome do Remetente (From Name) *</label>
                                    <input type="text" name="mail_from_name" id="smtpFromName" class="form-control" value="{{ $configs['mail_from_name'] }}" placeholder="Foco 7 Rastreamento" required>
                                </div>
                            </div>
                        </div>

                        <!-- TESTE DE CONEXÃO SMTP VIA AJAX -->
                        <div class="col-lg-4">
                            <div class="card bg-dark border-secondary p-3">
                                <h6 class="fw-bold text-white mb-2">
                                    <i class="fas fa-paper-plane text-warning me-2"></i> Teste de Disparo de E-mail
                                </h6>
                                <p class="small text-secondary mb-3">
                                    Envie uma mensagem de teste em tempo real para verificar se o servidor de e-mail está autenticando perfeitamente.
                                </p>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-white">E-mail de Destino</label>
                                    <input type="email" id="emailDestinoTeste" class="form-control form-control-sm" value="{{ auth()->user()->email }}" placeholder="seu-email@dominio.com">
                                </div>
                                <button type="button" class="btn btn-warning w-100 fw-bold" id="btnTestarSmtp">
                                    <i class="fas fa-vial me-1"></i> TESTAR ENVIO VIA AJAX
                                </button>
                                <div id="resultadoTesteSmtp" class="mt-3 small d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 4: REDES SOCIAIS -->
                <div class="tab-pane fade" id="tab-sociais" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><i class="fab fa-instagram text-danger me-1"></i> Perfil no Instagram</label>
                            <input type="url" name="social_instagram" class="form-control" value="{{ $configs['social_instagram'] }}" placeholder="https://instagram.com/foco7rastreamento">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><i class="fab fa-facebook text-primary me-1"></i> Página no Facebook</label>
                            <input type="url" name="social_facebook" class="form-control" value="{{ $configs['social_facebook'] }}" placeholder="https://facebook.com/foco7rastreamento">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><i class="fab fa-linkedin text-info me-1"></i> LinkedIn Oficial</label>
                            <input type="url" name="social_linkedin" class="form-control" value="{{ $configs['social_linkedin'] }}" placeholder="https://linkedin.com/company/foco7">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold"><i class="fab fa-youtube text-danger me-1"></i> Canal no YouTube</label>
                            <input type="url" name="social_youtube" class="form-control" value="{{ $configs['social_youtube'] }}" placeholder="https://youtube.com/@foco7">
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-danger px-4 py-2 fw-bold">
                    <i class="fas fa-save me-1"></i> SALVAR TODAS AS CONFIGURAÇÕES
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSenha(inputId) {
        var input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    $(function () {
        // Teste de Disparo SMTP via AJAX
        $('#btnTestarSmtp').on('click', function () {
            var $btn = $(this);
            var emailDestino = $('#emailDestinoTeste').val();

            if (!emailDestino) {
                window.foco7Notify.warning('Digite um e-mail de destino para o teste.');
                return;
            }

            var origText = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Testando Conexão...');

            var dadosTeste = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                email_teste: emailDestino,
                host: $('#smtpHost').val(),
                port: $('#smtpPort').val(),
                encryption: $('#smtpEncryption').val(),
                username: $('#smtpUsername').val(),
                password: $('#smtpPassword').val(),
                from_address: $('#smtpFromAddress').val(),
                from_name: $('#smtpFromName').val()
            };

            $.ajax({
                url: "{{ route('admin.configuracoes.testar_smtp') }}",
                type: 'POST',
                data: dadosTeste,
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(origText);
                    if (res.success) {
                        Swal.fire({
                            title: 'SMTP Conectado com Sucesso!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Ótimo!'
                        });
                        window.foco7Notify.success(res.message);
                    } else {
                        Swal.fire({
                            title: 'Falha no Teste SMTP',
                            text: res.message,
                            icon: 'error',
                            confirmButtonColor: '#d90000'
                        });
                        window.foco7Notify.error(res.message);
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(origText);
                    var msg = 'Erro ao conectar ao servidor SMTP.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Erro de Conexão SMTP',
                        text: msg,
                        icon: 'error',
                        confirmButtonColor: '#d90000'
                    });
                    window.foco7Notify.error(msg);
                }
            });
        });
    });
</script>
@endsection
