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
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="recaptcha-tab" data-bs-toggle="tab" data-bs-target="#tab-recaptcha" type="button">
                    <i class="fas fa-shield-alt text-danger me-1"></i> reCAPTCHA v3
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="geoblock-tab" data-bs-toggle="tab" data-bs-target="#tab-geoblock" type="button">
                    <i class="fas fa-globe-americas text-danger me-1"></i> Bloqueio Geográfico (Américas)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="sessao-tab" data-bs-toggle="tab" data-bs-target="#tab-sessao" type="button">
                    <i class="fas fa-user-lock text-danger me-1"></i> Sessão &amp; Desconexão
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('admin.configuracoes.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form keep-data" id="formConfiguracoes">
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

                <!-- ABA 2: IDENTIDADE VISUAL COM ARRASTA E SOLTA (FUNDO PRETO & FUNDO BRANCO) -->
                <div class="tab-pane fade" id="tab-branding" role="tabpanel">
                    <div class="alert alert-secondary border border-secondary border-opacity-25 shadow-sm mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-subtle text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                <i class="fas fa-palette fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1 text-body">Gerenciamento Dinâmico de Logotipos por Fundo</h6>
                                <p class="small text-secondary mb-0">
                                    O sistema alterna automaticamente o logotipo exibido de acordo com a tonalidade do fundo da página ou seção: a <strong>versão para fundo escuro</strong> é aplicada no rodapé institucional e menus escuros, enquanto a <strong>versão para fundo claro</strong> é renderizada no topo da Landing Page, páginas de termos e documentos com fundo branco.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- 1. LOGO PARA FUNDO PRETO / ESCURO -->
                        <div class="col-lg-4">
                            <div class="card bg-dark preview-fundo-escuro border-secondary p-3 h-100 shadow-sm text-white">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold text-white mb-0">
                                        <i class="fas fa-moon text-warning me-1"></i> Logo Fundo Preto
                                    </label>
                                    <span class="badge bg-secondary font-monospace" style="font-size: 0.7rem;">Fundo Escuro</span>
                                </div>
                                <p class="small text-secondary mb-2" style="font-size: 0.78rem;">
                                    Letras brancas/claras. Exibido na barra lateral do painel admin, blocos e cabeçalhos escuros.
                                </p>
                                <div class="foco-dropzone"
                                     data-input-name="logo_upload"
                                     data-ratio="Horizontal (PNG/WEBP Transparente)"
                                     data-max-size="5MB"
                                     data-accept="image/png,image/webp,image/jpeg"
                                     data-current-preview="{{ asset($configs['site_logo']) }}">
                                </div>
                                <div class="small text-secondary mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Preview sobre fundo escuro simulado.
                                </div>
                            </div>
                        </div>

                        <!-- 2. LOGO PARA FUNDO BRANCO / CLARO (SECUNDÁRIA) -->
                        <div class="col-lg-4">
                            <div class="card bg-white preview-fundo-branco border border-2 p-3 h-100 shadow-sm text-dark">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold text-dark mb-0">
                                        <i class="fas fa-sun text-warning me-1"></i> Logo Fundo Branco
                                    </label>
                                    <span class="badge bg-secondary-subtle text-dark border font-monospace" style="font-size: 0.7rem;">Fundo Claro</span>
                                </div>
                                <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                                    Letras pretas/escuras. Exibido no topo/navbar, no rodapé do site, termos de privacidade e páginas claras.
                                </p>
                                <div class="foco-dropzone"
                                     data-input-name="logo_light_upload"
                                     data-ratio="Horizontal (PNG/WEBP Transparente)"
                                     data-max-size="5MB"
                                     data-accept="image/png,image/webp,image/jpeg"
                                     data-current-preview="{{ asset($configs['site_logo_clara']) }}">
                                </div>
                                <div class="small text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Preview sobre fundo branco simulado.
                                </div>
                            </div>
                        </div>

                        <!-- 3. ÍCONE FAVICON -->
                        <div class="col-lg-4">
                            <div class="card bg-dark preview-fundo-escuro border-secondary p-3 h-100 shadow-sm text-white">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold text-white mb-0">
                                        <i class="fas fa-gem text-danger me-1"></i> Ícone Favicon
                                    </label>
                                    <span class="badge bg-danger font-monospace" style="font-size: 0.7rem;">Aba / Celular</span>
                                </div>
                                <p class="small text-secondary mb-2" style="font-size: 0.78rem;">
                                    Proporção 1:1 quadrado (PNG ou ICO). Exibido na aba do navegador e no ícone de atalho mobile.
                                </p>
                                <div class="foco-dropzone"
                                     data-input-name="favicon_upload"
                                     data-ratio="512 × 512 px (Quadrado)"
                                     data-max-size="2MB"
                                     data-accept="image/png,image/x-icon,image/webp"
                                     data-current-preview="{{ asset($configs['site_favicon']) }}">
                                </div>
                                <div class="small text-secondary mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Exibido nas abas dos navegadores.
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

                <!-- ABA 5: GOOGLE RECAPTCHA V3 & SEGURANÇA -->
                <div class="tab-pane fade" id="tab-recaptcha" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card border border-light-subtle shadow-none p-3 bg-body-secondary mb-3">
                                <div class="form-check form-switch fs-5 mb-2">
                                    <input class="form-check-input" type="checkbox" name="recaptcha_ativo" id="recaptchaAtivoSwitch" value="1" {{ $configs['recaptcha_ativo'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold fs-6 text-body" for="recaptchaAtivoSwitch">
                                        Ativar Proteção Google reCAPTCHA v3 no Formulário de Contato
                                    </label>
                                </div>
                                <p class="text-body-secondary small mb-0">
                                    Ao ativar, o sistema valida envios suspeitos e bots silenciosamente sem incomodar os visitantes com testes ou desafios visuais.
                                </p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Chave do Site (Site Key) *</label>
                                    <input type="text" name="recaptcha_site_key" class="form-control" value="{{ $configs['recaptcha_site_key'] }}" placeholder="Ex: 6LdXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                                    <div class="form-text small">Chave pública utilizada no frontend (JavaScript) para gerar o token invisível de segurança.</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Chave Secreta (Secret Key) *</label>
                                    <div class="input-group">
                                        <input type="password" name="recaptcha_secret_key" id="recaptchaSecretKey" class="form-control" value="{{ $configs['recaptcha_secret_key'] }}" placeholder="Ex: 6LdYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('recaptchaSecretKey')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small">Chave privada utilizada pelo backend PHP para validar a pontuação de autenticidade junto à API do Google.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card bg-dark text-white border-secondary p-3">
                                <h6 class="fw-bold text-white mb-2">
                                    <i class="fas fa-shield-alt text-danger me-2"></i> Paleta Dinâmica & Anti-Spam
                                </h6>
                                <p class="small text-white-50 mb-2">
                                    <strong>Cores Sincronizadas:</strong> O selo e o badge do reCAPTCHA herdam dinamicamente a paleta de cores primária configurada no sistema. Se a cor for alterada no painel, o selo se adapta automaticamente.
                                </p>
                                <p class="small text-white-50 mb-3">
                                    <strong>Blindagem Avançada:</strong> O formulário conta com múltiplas camadas de proteção:
                                </p>
                                <ul class="small text-white-50 ps-3 mb-0">
                                    <li>Honeypot invisível para bots</li>
                                    <li>Bloqueio de submissão instantânea por robôs</li>
                                    <li>Sanitização ativa contra injeções SQL e XSS</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 6: BLOQUEIO GEOGRÁFICO & PROTEÇÃO REGIONAL (AMÉRICAS) -->
                <div class="tab-pane fade" id="tab-geoblock" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card border border-light-subtle shadow-none p-3 bg-body-secondary mb-3">
                                <div class="form-check form-switch fs-5 mb-2">
                                    <input class="form-check-input" type="checkbox" name="geoblock_ativo" id="geoblockAtivoSwitch" value="1" {{ $configs['geoblock_ativo'] == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold fs-6 text-body" for="geoblockAtivoSwitch">
                                        Ativar Bloqueio Geográfico Restrito às Américas
                                    </label>
                                </div>
                                <p class="text-body-secondary small mb-0">
                                    Ao manter ativo, visitantes originários de fora das Américas (como países europeus, asiáticos e africanos) são bloqueados preventivamente com status HTTP 403, neutralizando tentativas automatizadas de injeção, varreduras de portas e força bruta.
                                </p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Continentes &amp; Regiões com Acesso Autorizado</label>
                                    <div class="p-3 bg-body rounded border">
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                                <i class="fas fa-check-circle me-1"></i> América do Sul (Brasil, Argentina, Chile, etc.)
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                                <i class="fas fa-check-circle me-1"></i> América do Norte (EUA, Canadá, México)
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                                <i class="fas fa-check-circle me-1"></i> América Central &amp; Caribe
                                            </span>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2">
                                                <i class="fas fa-ban me-1"></i> Europa (Bloqueado)
                                            </span>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2">
                                                <i class="fas fa-ban me-1"></i> Ásia / África / Oceania (Bloqueado)
                                            </span>
                                        </div>
                                        <input type="hidden" name="geoblock_regioes" value="Americas">
                                        <small class="text-secondary">O tráfego de toda a extensão do continente americano permanece 100% livre e fluido.</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold">Lista Branca de IPs Isentos de Bloqueio (Whitelist)</label>
                                    <textarea name="geoblock_ips_liberados" id="geoblockIpsLiberados" rows="3" class="form-control font-monospace small" placeholder="127.0.0.1, ::1, 177.192.4.34">{{ $configs['geoblock_ips_liberados'] }}</textarea>
                                    <div class="form-text small">
                                        Insira endereços IP ou ranges com curinga (ex: <code>177.192.*</code>) separados por vírgula ou linha. Esses IPs nunca serão bloqueados, mesmo que estejam operando via VPN estrangeira.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SIMULADOR & TESTADOR DE GEOIP AO VIVO -->
                        <div class="col-lg-4">
                            <div class="card bg-dark text-white border-secondary p-3">
                                <h6 class="fw-bold text-white mb-2">
                                    <i class="fas fa-vial text-danger me-2"></i> Simulador de Bloqueio GeoIP
                                </h6>
                                <p class="small text-white-50 mb-3">
                                    Teste em tempo real como o sistema reagirá a requisições de diferentes países ou IPs.
                                </p>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-white">Selecione ou Digite um País / IP:</label>
                                    <select class="form-select form-select-sm mb-2" id="selectSimularPais">
                                        <option value="DE">🇩🇪 Alemanha (Europa) - Alvo Comum</option>
                                        <option value="FR">🇫🇷 França (Europa)</option>
                                        <option value="RU">🇷🇺 Rússia (Europa/Ásia)</option>
                                        <option value="NL">🇳🇱 Holanda (Europa)</option>
                                        <option value="GB">🇬🇧 Reino Unido (Europa)</option>
                                        <option value="CN">🇨🇳 China (Ásia)</option>
                                        <option value="BR" selected>🇧🇷 Brasil (América do Sul) - Permitido</option>
                                        <option value="US">🇺🇸 Estados Unidos (América do Norte) - Permitido</option>
                                        <option value="AR">🇦🇷 Argentina (América do Sul) - Permitido</option>
                                    </select>
                                    <input type="text" id="inputSimularIp" class="form-control form-control-sm font-monospace" placeholder="Ou digite um IP específico (ex: 185.220.101.5)">
                                </div>

                                <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold mt-2" id="btnTestarGeoip">
                                    <i class="fas fa-search-location me-1"></i> TESTAR REGRA DE ACESSO
                                </button>

                                <div id="boxResultadoGeoip" class="mt-3 p-2 rounded small d-none border">
                                    <div class="fw-bold mb-1" id="resGeoTitulo"></div>
                                    <div class="text-white-50" id="resGeoDetalhe"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 7: SEGURANÇA DE SESSÃO & AUTO-LOGOUT POR INATIVIDADE -->
                <div class="tab-pane fade" id="tab-sessao" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card border border-light-subtle shadow-none p-3 bg-body-secondary mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-danger text-white rounded-circle p-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                        <i class="fas fa-user-lock fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-body mb-1">Encerramento Automático de Sessão por Inatividade (Anti-Session Hijacking)</h6>
                                        <p class="text-secondary small mb-0">
                                            Desconecta automaticamente o usuário após o tempo sem nenhuma interação no sistema (clique, digitação, rolagem), prevenindo que terceiros acessem dados caso o dispositivo seja deixado aberto.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Tempo Limite de Inatividade (em Minutos) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-stopwatch text-danger"></i></span>
                                        <input type="number" name="sessao_inatividade_minutos" id="sessaoInatividadeInput" 
                                               class="form-control" min="1" max="1440" step="1" 
                                               value="{{ $configs['sessao_inatividade_minutos'] ?? 30 }}" required>
                                        <span class="input-group-text">minutos</span>
                                    </div>
                                    <div class="form-text small">
                                        Recomendado: 15 a 30 minutos em ambientes administrativos de alta segurança.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Atalhos de Seleção Rápida</label>
                                    <div class="d-flex flex-wrap gap-2 pt-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-tempo-sessao" data-minutos="10">10 min</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-tempo-sessao" data-minutos="15">15 min</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-tempo-sessao" data-minutos="30">30 min (Padrão)</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-tempo-sessao" data-minutos="60">1 hora</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-tempo-sessao" data-minutos="120">2 horas</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card bg-dark text-white border-secondary p-3">
                                <h6 class="fw-bold text-white mb-2">
                                    <i class="fas fa-shield-alt text-danger me-2"></i> Proteção Ativa em 2 Níveis
                                </h6>
                                <p class="small text-white-50 mb-2">
                                    <strong>1. Frontend com Aviso Preventivo:</strong> O navegador monitora movimentos, cliques e digitação. Faltando 60 segundos para o limite, um modal de confirmação SweetAlert2 é disparado para permitir continuar conectado ou desconectar imediatamente.
                                </p>
                                <p class="small text-white-50 mb-0">
                                    <strong>2. Backend com Middleware Inviolável:</strong> Mesmo que o JavaScript seja pausado ou a aba seja reaberta horas depois, o servidor valida o timestamp de atividade e barra requisições antigas, forçando novo login seguro.
                                </p>
                            </div>
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
        // Atalhos de seleção rápida do tempo de inatividade
        $('.btn-tempo-sessao').on('click', function () {
            var min = $(this).data('minutos');
            $('#sessaoInatividadeInput').val(min).trigger('change');
            $('.btn-tempo-sessao').removeClass('btn-danger').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('btn-danger');
            if (window.foco7Notify) {
                window.foco7Notify.info('Tempo selecionado: ' + min + ' minutos');
            }
        });

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

        // Teste de GeoIP via AJAX
        $('#btnTestarGeoip').on('click', function () {
            var $btn = $(this);
            var ipDigitado = $('#inputSimularIp').val().trim();
            var termo = ipDigitado || $('#selectSimularPais').val();

            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Analisando Origem...');

            $.ajax({
                url: "{{ route('admin.configuracoes.testar_geoip') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    termo: termo
                },
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success && res.resultado) {
                        var r = res.resultado;
                        var $box = $('#boxResultadoGeoip');
                        $box.removeClass('d-none border-success border-danger bg-success-subtle bg-danger-subtle');

                        if (r.is_americas) {
                            $box.addClass('border-success bg-success bg-opacity-10');
                            $('#resGeoTitulo').html('<i class="fas fa-check-circle text-success me-1"></i> ' + r.resultado).removeClass('text-danger').addClass('text-success');
                        } else {
                            $box.addClass('border-danger bg-danger bg-opacity-10');
                            $('#resGeoTitulo').html('<i class="fas fa-ban text-danger me-1"></i> ' + r.resultado).removeClass('text-success').addClass('text-danger');
                        }

                        var detalhe = (r.nome || r.pais || termo) + ' (' + (r.codigo || '') + ') &bull; ' + (r.continente || 'Continente avaliado');
                        $('#resGeoDetalhe').html(detalhe);
                        window.foco7Notify.info('Teste GeoIP concluído para: ' + (r.nome || r.pais || termo));
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html(origHtml);
                    window.foco7Notify.error('Falha ao simular localização GeoIP.');
                }
            });
        });
    });
</script>
@endsection
