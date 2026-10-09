@extends('admin.layouts.app')

@section('title', 'Portal de Pagamentos & Gateways')
@section('page_title', 'Gateways de Pagamento & Cobranças')
@section('page_description', 'Gerencie credenciais de Checkout Transparente (Mercado Pago e Efí PRO), carnês, templates de e-mail e tarefas agendadas.')

@section('content')
<div class="card shadow-sm border-0">
    <!-- NAVEGAÇÃO POR GUIAS (TABS) -->
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="gatewaysTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="mp-tab" data-bs-toggle="tab" data-bs-target="#tab-mercadopago" type="button" role="tab">
                    <i class="fab fa-cc-mastercard text-primary me-2"></i> Mercado Pago
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="efi-tab" data-bs-toggle="tab" data-bs-target="#tab-efipro" type="button" role="tab">
                    <i class="fas fa-bolt text-warning me-2"></i> Efí PRO (Gerencianet)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="carne-tab" data-bs-toggle="tab" data-bs-target="#tab-carne" type="button" role="tab">
                    <i class="fas fa-book text-success me-2"></i> Cobrança por Carnê
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="emails-tab" data-bs-toggle="tab" data-bs-target="#tab-emails" type="button" role="tab">
                    <i class="fas fa-envelope-open-text text-danger me-2"></i> Templates de E-mails
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="cron-tab" data-bs-toggle="tab" data-bs-target="#tab-cron" type="button" role="tab">
                    <i class="fas fa-clock text-info me-2"></i> Central de Cron
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="gatewaysTabsContent">

            <!-- ===================================================================
                 GUIA 1: MERCADO PAGO (COM SELEÇÃO DINÂMICA SANDBOX / PRODUÇÃO)
                 =================================================================== -->
            <div class="tab-pane fade show active" id="tab-mercadopago" role="tabpanel">
                <form action="{{ route('admin.gateways.salvar') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-primary"><i class="fab fa-cc-mastercard me-2"></i> Integração Mercado Pago</h5>
                            <p class="text-secondary small mb-0">Checkout Transparente para PIX instantâneo, Cartão de Crédito e Boleto Bancário.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch fs-5 mb-0">
                                <input class="form-check-input" type="checkbox" name="mp_ativo" id="mp_ativo" value="1" {{ $configs['mp_ativo'] ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 fw-bold" for="mp_ativo">Gateway Ativo</label>
                            </div>
                        </div>
                    </div>

                    <!-- SELETOR DINÂMICO DE AMBIENTE MERCADO PAGO -->
                    <div class="card bg-light border mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <span class="small fw-bold text-uppercase text-secondary d-block">Ambiente de Execução Ativo</span>
                                    <span class="small text-muted">Selecione se as cobranças serão processadas em ambiente de testes ou real.</span>
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="mp_ambiente" id="mp_amb_sandbox" value="sandbox" {{ $configs['mp_ambiente'] === 'sandbox' ? 'checked' : '' }} onchange="alternarAmbienteMp('sandbox')">
                                    <label class="btn btn-outline-warning fw-bold px-3" for="mp_amb_sandbox">
                                        <i class="fas fa-flask me-1"></i> Sandbox (Testes)
                                    </label>

                                    <input type="radio" class="btn-check" name="mp_ambiente" id="mp_amb_prod" value="producao" {{ $configs['mp_ambiente'] === 'producao' ? 'checked' : '' }} onchange="alternarAmbienteMp('producao')">
                                    <label class="btn btn-outline-success fw-bold px-3" for="mp_amb_prod">
                                        <i class="fas fa-check-circle me-1"></i> Produção Real
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ALERTA DINÂMICO DO AMBIENTE ATIVO -->
                    <div id="mpAlertSandbox" class="alert alert-warning border-warning d-flex align-items-center {{ $configs['mp_ambiente'] === 'sandbox' ? '' : 'd-none' }}">
                        <i class="fas fa-exclamation-triangle fs-3 me-3 text-warning"></i>
                        <div>
                            <strong>Modo Sandbox (Homologação) Ativo:</strong> Nenhuma cobrança financeira real será realizada. Utilize as credenciais de teste fornecidas no painel de desenvolvedor do Mercado Pago.
                        </div>
                    </div>
                    <div id="mpAlertProd" class="alert alert-success border-success d-flex align-items-center {{ $configs['mp_ambiente'] === 'producao' ? '' : 'd-none' }}">
                        <i class="fas fa-shield-alt fs-3 me-3 text-success"></i>
                        <div>
                            <strong>Modo Produção Real Ativo:</strong> As cobranças estão operando ao vivo com processamento financeiro real pelo Mercado Pago.
                        </div>
                    </div>

                    <!-- CAMPOS: AMBIENTE SANDBOX -->
                    <div id="mpCamposSandbox" class="{{ $configs['mp_ambiente'] === 'sandbox' ? '' : 'd-none' }}">
                        <div class="card border border-warning mb-4">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="fw-bold small text-dark"><i class="fas fa-key text-warning me-1"></i> Credenciais de Testes (Sandbox)</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Public Key (Sandbox) *</label>
                                        <input type="text" name="mp_public_key_sandbox" class="form-control" value="{{ $configs['mp_public_key_sandbox'] }}" placeholder="TEST-xxxx...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Access Token (Sandbox) *</label>
                                        <div class="input-group">
                                            <input type="password" name="mp_access_token_sandbox" id="mpAccessTokenSandbox" class="form-control" value="{{ $configs['mp_access_token_sandbox'] }}" placeholder="TEST-xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpAccessTokenSandbox')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client ID (Sandbox)</label>
                                        <input type="text" name="mp_client_id_sandbox" class="form-control" value="{{ $configs['mp_client_id_sandbox'] }}" placeholder="Ex: 123456789">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client Secret (Sandbox)</label>
                                        <div class="input-group">
                                            <input type="password" name="mp_client_secret_sandbox" id="mpClientSecretSandbox" class="form-control" value="{{ $configs['mp_client_secret_sandbox'] }}" placeholder="Client_Secret_xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpClientSecretSandbox')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CAMPOS: AMBIENTE PRODUÇÃO -->
                    <div id="mpCamposProd" class="{{ $configs['mp_ambiente'] === 'producao' ? '' : 'd-none' }}">
                        <div class="card border border-success mb-4">
                            <div class="card-header bg-success bg-opacity-10 py-2">
                                <span class="fw-bold small text-success"><i class="fas fa-lock me-1"></i> Credenciais de Produção Real</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Public Key (Produção) *</label>
                                        <input type="text" name="mp_public_key_prod" class="form-control" value="{{ $configs['mp_public_key_prod'] }}" placeholder="APP_USR-xxxx...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Access Token (Produção) *</label>
                                        <div class="input-group">
                                            <input type="password" name="mp_access_token_prod" id="mpAccessTokenProd" class="form-control" value="{{ $configs['mp_access_token_prod'] }}" placeholder="APP_USR-xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpAccessTokenProd')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client ID (Produção)</label>
                                        <input type="text" name="mp_client_id_prod" class="form-control" value="{{ $configs['mp_client_id_prod'] }}" placeholder="Ex: 987654321">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client Secret (Produção)</label>
                                        <div class="input-group">
                                            <input type="password" name="mp_client_secret_prod" id="mpClientSecretProd" class="form-control" value="{{ $configs['mp_client_secret_prod'] }}" placeholder="Client_Secret_xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpClientSecretProd')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MÉTODOS DE PAGAMENTO E WEBHOOK -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-2">
                            <span class="fw-bold small text-dark"><i class="fas fa-sliders-h me-1"></i> Métodos Aceitos & Webhook de Retorno</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mp_aceita_pix" id="mp_aceita_pix" value="1" {{ $configs['mp_aceita_pix'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="mp_aceita_pix">Aceitar PIX</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mp_aceita_cartao" id="mp_aceita_cartao" value="1" {{ $configs['mp_aceita_cartao'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="mp_aceita_cartao">Aceitar Cartão de Crédito</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mp_aceita_boleto" id="mp_aceita_boleto" value="1" {{ $configs['mp_aceita_boleto'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="mp_aceita_boleto">Aceitar Boleto Bancário</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <label class="form-label small fw-bold">Chave Secreta do Webhook (Assinatura HMAC x-signature)</label>
                                    <input type="password" name="mp_webhook_secret" class="form-control" value="{{ $configs['mp_webhook_secret'] }}" placeholder="Cole a assinatura secreta do Webhook gerada no Mercado Pago">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">URL de Webhook para Cadastro no Mercado Pago:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light font-monospace" value="{{ $webhookUrlMp }}" readonly id="urlWebhookMp">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copiarTexto('urlWebhookMp')"><i class="fas fa-copy me-1"></i> Copiar URL</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Configurações do Mercado Pago
                        </button>
                    </div>
                </form>
            </div>

            <!-- ===================================================================
                 GUIA 2: EFÍ PRO (GERENCIANET - PIX, BOLETO, CARTÃO E CERTIFICADO PEM)
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-efipro" role="tabpanel">
                <form action="{{ route('admin.gateways.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                    @csrf

                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-warning"><i class="fas fa-bolt me-2"></i> Integração Efí PRO (Gerencianet)</h5>
                            <p class="text-secondary small mb-0">API Oficial do Banco Central para PIX com confirmação em tempo real, Boletos e Cartão.</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch fs-5 mb-0">
                                <input class="form-check-input" type="checkbox" name="efi_ativo" id="efi_ativo" value="1" {{ $configs['efi_ativo'] ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 fw-bold" for="efi_ativo">Gateway Ativo</label>
                            </div>
                        </div>
                    </div>

                    <!-- SELETOR DINÂMICO DE AMBIENTE EFÍ PRO -->
                    <div class="card bg-light border mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <span class="small fw-bold text-uppercase text-secondary d-block">Ambiente de Execução Ativo</span>
                                    <span class="small text-muted">Defina se a API da Efí operará em Homologação ou Produção.</span>
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="efi_ambiente" id="efi_amb_sandbox" value="sandbox" {{ $configs['efi_ambiente'] === 'sandbox' ? 'checked' : '' }} onchange="alternarAmbienteEfi('sandbox')">
                                    <label class="btn btn-outline-warning fw-bold px-3" for="efi_amb_sandbox">
                                        <i class="fas fa-flask me-1"></i> Homologação (Sandbox)
                                    </label>

                                    <input type="radio" class="btn-check" name="efi_ambiente" id="efi_amb_prod" value="producao" {{ $configs['efi_ambiente'] === 'producao' ? 'checked' : '' }} onchange="alternarAmbienteEfi('producao')">
                                    <label class="btn btn-outline-success fw-bold px-3" for="efi_amb_prod">
                                        <i class="fas fa-check-circle me-1"></i> Produção Oficial
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ALERTA DINÂMICO EFÍ -->
                    <div id="efiAlertSandbox" class="alert alert-warning border-warning d-flex align-items-center {{ $configs['efi_ambiente'] === 'sandbox' ? '' : 'd-none' }}">
                        <i class="fas fa-exclamation-triangle fs-3 me-3 text-warning"></i>
                        <div>
                            <strong>Modo Homologação Efí Ativo:</strong> Utiliza o certificado de testes da Efí PRO e endpoints de sandbox. Não gera débitos bancários reais.
                        </div>
                    </div>
                    <div id="efiAlertProd" class="alert alert-success border-success d-flex align-items-center {{ $configs['efi_ambiente'] === 'producao' ? '' : 'd-none' }}">
                        <i class="fas fa-shield-alt fs-3 me-3 text-success"></i>
                        <div>
                            <strong>Modo Produção Oficial Efí Ativo:</strong> Conexão criptografada diretamente com os servidores da Efí PRO com liquidação financeira instantânea.
                        </div>
                    </div>

                    <!-- CAMPOS: AMBIENTE SANDBOX EFÍ -->
                    <div id="efiCamposSandbox" class="{{ $configs['efi_ambiente'] === 'sandbox' ? '' : 'd-none' }}">
                        <div class="card border border-warning mb-4">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="fw-bold small text-dark"><i class="fas fa-key text-warning me-1"></i> Credenciais & Certificado Homologação (Sandbox)</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client ID (Sandbox) *</label>
                                        <input type="text" name="efi_client_id_sandbox" class="form-control" value="{{ $configs['efi_client_id_sandbox'] }}" placeholder="Client_Id_xxxx...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client Secret (Sandbox) *</label>
                                        <div class="input-group">
                                            <input type="password" name="efi_client_secret_sandbox" id="efiClientSecretSandbox" class="form-control" value="{{ $configs['efi_client_secret_sandbox'] }}" placeholder="Client_Secret_xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('efiClientSecretSandbox')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Chave PIX (Homologação)</label>
                                        <input type="text" name="efi_chave_pix_sandbox" class="form-control" value="{{ $configs['efi_chave_pix_sandbox'] }}" placeholder="Chave de testes gerada na Efí">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold"><i class="fas fa-file-code text-warning me-1"></i> Certificado .PEM (Sandbox / Homologação)</label>
                                        <div class="foco-dropzone"
                                             data-input-name="efi_certificado_sandbox"
                                             data-ratio="Certificado Oficial .PEM"
                                             data-max-size="2MB"
                                             data-accept=".pem"
                                             data-current-preview="{{ !empty($configs['efi_certificado_pem_sandbox']) ? $configs['efi_certificado_pem_sandbox'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CAMPOS: AMBIENTE PRODUÇÃO EFÍ -->
                    <div id="efiCamposProd" class="{{ $configs['efi_ambiente'] === 'producao' ? '' : 'd-none' }}">
                        <div class="card border border-success mb-4">
                            <div class="card-header bg-success bg-opacity-10 py-2">
                                <span class="fw-bold small text-success"><i class="fas fa-lock me-1"></i> Credenciais & Certificado de Produção Oficial</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client ID Oficial (Produção) *</label>
                                        <input type="text" name="efi_client_id_prod" class="form-control" value="{{ $configs['efi_client_id_prod'] }}" placeholder="Client_Id_xxxx...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Client Secret Oficial (Produção) *</label>
                                        <div class="input-group">
                                            <input type="password" name="efi_client_secret_prod" id="efiClientSecretProd" class="form-control" value="{{ $configs['efi_client_secret_prod'] }}" placeholder="Client_Secret_xxxx...">
                                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('efiClientSecretProd')"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Chave PIX Oficial da Conta *</label>
                                        <input type="text" name="efi_chave_pix_prod" class="form-control" value="{{ $configs['efi_chave_pix_prod'] }}" placeholder="Chave EVP, CNPJ, Telefone ou E-mail da Foco 7">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold"><i class="fas fa-certificate text-success me-1"></i> Certificado Oficial .PEM (Produção) *</label>
                                        <div class="foco-dropzone"
                                             data-input-name="efi_certificado_prod"
                                             data-ratio="Certificado Oficial .PEM"
                                             data-max-size="2MB"
                                             data-accept=".pem"
                                             data-current-preview="{{ !empty($configs['efi_certificado_pem_prod']) ? $configs['efi_certificado_pem_prod'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MÉTODOS DE PAGAMENTO E REGRAS FINANCEIRAS EFÍ -->
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-2">
                            <span class="fw-bold small text-dark"><i class="fas fa-sliders-h me-1"></i> Métodos Efí & Condições Comerciais</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="efi_aceita_pix" id="efi_aceita_pix" value="1" {{ $configs['efi_aceita_pix'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="efi_aceita_pix">PIX Instantâneo</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="efi_aceita_boleto" id="efi_aceita_boleto" value="1" {{ $configs['efi_aceita_boleto'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="efi_aceita_boleto">Boleto Bancário</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="efi_aceita_cartao" id="efi_aceita_cartao" value="1" {{ $configs['efi_aceita_cartao'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="efi_aceita_cartao">Cartão de Crédito</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="efi_aceita_carne" id="efi_aceita_carne" value="1" {{ $configs['efi_aceita_carne'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold small" for="efi_aceita_carne">Carnê de Mensalidade</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Desconto no PIX à Vista (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="pix_desconto" class="form-control" value="{{ $configs['pix_desconto'] }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Expiração do QR Code PIX (Minutos)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="pix_expiracao_minutos" class="form-control" value="{{ $configs['pix_expiracao_minutos'] }}">
                                        <span class="input-group-text">min</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Dias para Vencimento do Boleto</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="boleto_dias_vencimento" class="form-control" value="{{ $configs['boleto_dias_vencimento'] }}">
                                        <span class="input-group-text">dias</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Multa por Atraso (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" name="multa_atraso" class="form-control" value="{{ $configs['multa_atraso'] }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Juros de Mora Diário (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.001" name="juros_diario" class="form-control" value="{{ $configs['juros_diario'] }}">
                                        <span class="input-group-text">% ao dia</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Instruções Bancárias no Boleto:</label>
                                    <textarea name="instrucoes_cobranca" rows="2" class="form-control">{{ $configs['instrucoes_cobranca'] }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">URL de Webhook Efí PRO (PIX):</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light font-monospace" value="{{ $webhookUrlEfi }}" readonly id="urlWebhookEfi">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copiarTexto('urlWebhookEfi')"><i class="fas fa-copy me-1"></i> Copiar URL</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Configurações da Efí PRO
                        </button>
                    </div>
                </form>
            </div>

            <!-- ===================================================================
                 GUIA 3: COBRANÇA POR CARNÊ (EFÍ PRO PARA CLIENTES EXISTENTES)
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-carne" role="tabpanel">
                <div class="row g-4">
                    <!-- CONFIGURAÇÕES DE CARNÊ -->
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-3">
                                <h6 class="fw-bold mb-0 text-success"><i class="fas fa-cog me-2"></i> Parâmetros de Carnê de Mensalidades</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('admin.gateways.salvar') }}" method="POST" class="ajax-form">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch fs-5 mb-3">
                                                <input class="form-check-input" type="checkbox" name="carne_ativo" id="carne_ativo" value="1" {{ $configs['carne_ativo'] ? 'checked' : '' }}>
                                                <label class="form-check-label fs-6 fw-bold" for="carne_ativo">Carnê Habilitado no Sistema</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Máximo de Parcelas Permitidas</label>
                                            <input type="number" name="carne_max_parcelas" class="form-control" value="{{ $configs['carne_max_parcelas'] }}" min="2" max="24">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Dias para 1º Vencimento</label>
                                            <input type="number" name="carne_dias_primeiro_vencimento" class="form-control" value="{{ $configs['carne_dias_primeiro_vencimento'] }}" min="1" max="60">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Intervalo Entre Parcelas</label>
                                            <div class="input-group">
                                                <input type="number" name="carne_intervalo_dias" class="form-control" value="{{ $configs['carne_intervalo_dias'] }}" min="15" max="60">
                                                <span class="input-group-text">dias</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Notificações Automáticas</label>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="carne_envio_email" id="carne_envio_email" value="1" {{ $configs['carne_envio_email'] ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="carne_envio_email">Enviar por E-mail</label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="carne_envio_whatsapp" id="carne_envio_whatsapp" value="1" {{ $configs['carne_envio_whatsapp'] ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-bold" for="carne_envio_whatsapp">Notificar via WhatsApp</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Instruções Impressas nas Folhas do Carnê</label>
                                            <textarea name="carne_instrucoes" rows="3" class="form-control">{{ $configs['carne_instrucoes'] }}</textarea>
                                        </div>
                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-success fw-bold">
                                                <i class="fas fa-save me-1"></i> Salvar Regras do Carnê
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- EMISSÃO SOB DEMANDA DE CARNÊ PARA CLIENTES EXISTENTES -->
                    <div class="col-lg-6">
                        <div class="card border border-success h-100 shadow-sm">
                            <div class="card-header bg-success bg-opacity-10 py-3">
                                <h6 class="fw-bold mb-0 text-success"><i class="fas fa-file-invoice-dollar me-2"></i> Emitir Carnê para Cliente Já Existente</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('admin.gateways.gerar_carne') }}" method="POST" class="ajax-form" id="formGerarCarne">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Selecionar Cliente Cadastrado *</label>
                                            <select name="cliente_id" class="form-select select2" required>
                                                <option value="">Selecione o cliente...</option>
                                                @foreach($clientes as $cli)
                                                    <option value="{{ $cli->id }}">{{ $cli->name }} ({{ $cli->email }}) - {{ $cli->cpf_cnpj ?? 'Sem CPF' }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Plano ou Serviço Vinculado (Opcional)</label>
                                            <select name="plano_id" class="form-select">
                                                <option value="">Nenhum plano específico (Mensalidade Personalizada)</option>
                                                @foreach($planos as $pln)
                                                    <option value="{{ $pln->id }}">{{ $pln->nome }} - {{ $pln->preco_formatado }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Descrição da Cobrança *</label>
                                            <input type="text" name="descricao" class="form-control" required placeholder="Ex: Mensalidade Rastreamento Veicular 2026">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Valor de Cada Parcela *</label>
                                            <input type="text" name="valor_parcela" class="form-control mask-dinheiro" required placeholder="R$ 59,90">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Número de Parcelas *</label>
                                            <select name="total_parcelas" class="form-select" required>
                                                <option value="2">2x parcelas (Bimestral)</option>
                                                <option value="3">3x parcelas (Trimestral)</option>
                                                <option value="6">6x parcelas (Semestral)</option>
                                                <option value="12" selected>12x parcelas (Anual - Recomendado)</option>
                                                <option value="24">24x parcelas (Bienal)</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold">Data do 1º Vencimento *</label>
                                            <input type="date" name="data_primeiro_vencimento" class="form-control" required value="{{ date('Y-m-d', strtotime('+10 days')) }}">
                                        </div>
                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                                                <i class="fas fa-print me-1"></i> Gerar Carnê &amp; Enviar ao Cliente
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================================================================
                 GUIA 4: TEMPLATES DE E-MAILS PRÓPRIOS & LAYOUT RESPONSIVO
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-emails" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-danger"><i class="fas fa-envelope-open-text me-2"></i> Templates de E-mails Transacionais &amp; Layout Responsivo</h5>
                        <p class="text-secondary small mb-0">Todos os e-mails utilizam a rota única oficial e o layout master responsivo (Desktop, Tablet e Mobile) com visualização em tempo real sem quebra visual.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-dark fw-bold btn-sm" onclick="abrirModalCriarTemplate()">
                            <i class="fas fa-plus-circle me-1 text-danger"></i> Criar Novo Template
                        </button>
                        <button type="button" class="btn btn-outline-danger fw-bold btn-sm" onclick="abrirModalTesteEmail()">
                            <i class="fas fa-paper-plane me-1"></i> Enviar E-mail de Demonstração
                        </button>
                    </div>
                </div>

                <!-- TAGS DISPONÍVEIS COM CLIQUE PARA COPIAR OU INSERIR -->
                <div class="alert alert-light border mb-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-dark"><i class="fas fa-tags text-danger me-1"></i> Variáveis Dinâmicas do Sistema:</span>
                        <span class="small text-muted" style="font-size: 11px;"><i class="fas fa-mouse-pointer me-1"></i> Clique em qualquer tag para copiar ou inserir no editor ativo</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{cliente_nome}')" title="Nome do Cliente">{cliente_nome}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{cliente_email}')" title="E-mail do Cliente">{cliente_email}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{fatura_id}')" title="Código da Fatura">{fatura_id}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{plano_nome}')" title="Nome do Plano / Serviço">{plano_nome}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{valor}')" title="Valor Formatado">{valor}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{data_vencimento}')" title="Data de Vencimento">{data_vencimento}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{link_fatura}')" title="Link de Pagamento da Fatura">{link_fatura}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{link_carne}')" title="Link do Carnê Efí">{link_carne}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{nome_empresa}')" title="Nome da Empresa">{nome_empresa}</span>
                        <span class="badge bg-secondary-subtle text-dark border font-monospace cursor-pointer px-2 py-1" onclick="copiarOuInserirTag('{whatsapp_empresa}')" title="WhatsApp Oficial">{whatsapp_empresa}</span>
                    </div>
                </div>

                <!-- SUB-GUIAS DOS TEMPLATES (NATIVOS E PERSONALIZADOS) -->
                <ul class="nav nav-pills mb-4 flex-wrap" id="emailTemplatesPills" role="tablist">
                    @foreach($templatesEmail as $key => $tpl)
                    <li class="nav-item me-2 mb-2">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }} fw-semibold btn-sm d-flex align-items-center gap-1" id="pill-{{ $key }}-tab" data-bs-toggle="pill" data-bs-target="#pill-{{ $key }}" type="button" role="tab">
                            <i class="fas fa-file-invoice text-danger me-1"></i>
                            <span>{{ $tpl['nome'] }}</span>
                            @if(empty($tpl['sistema']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle py-0 px-1" style="font-size: 9px;">Custom</span>
                            @endif
                        </button>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="emailTemplatesPillsContent">
                    @foreach($templatesEmail as $key => $tpl)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pill-{{ $key }}" role="tabpanel">
                        <div class="row g-4 align-items-stretch">
                            <!-- COLUNA ESQUERDA: EDITOR DO TEMPLATE (Summernote) -->
                            <div class="col-xl-6 col-lg-12">
                                <form action="{{ route('admin.gateways.salvar_email') }}" method="POST" class="ajax-form form-email-template h-100" data-chave="{{ $key }}">
                                    @csrf
                                    <input type="hidden" name="template_chave" value="{{ $key }}">
                                    <div class="card border shadow-sm h-100 d-flex flex-column">
                                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark small"><i class="fas fa-edit text-danger me-1"></i> {{ $tpl['nome'] }}</span>
                                                @if(!empty($tpl['sistema']))
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 10px;">Sistema</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;">Personalizado</span>
                                                @endif
                                            </div>
                                            @if(empty($tpl['sistema']))
                                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" title="Excluir Template" onclick="excluirTemplatePersonalizado('{{ $key }}', '{{ $tpl['nome'] }}')">
                                                <i class="fas fa-trash-alt me-1"></i> Excluir
                                            </button>
                                            @endif
                                        </div>
                                        <div class="card-body p-3 p-md-4 flex-grow-1">
                                            <p class="text-secondary small mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> {{ $tpl['descricao'] }}</p>
                                            
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Assunto do E-mail</label>
                                                <input type="text" name="assunto" id="assunto_{{ $key }}" class="form-control input-assunto" data-chave="{{ $key }}" value="{{ $tpl['assunto_atual'] }}" required placeholder="Assunto exibido na caixa de entrada...">
                                            </div>

                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label small fw-bold mb-0">Corpo do E-mail (HTML Formatado)</label>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-font me-1"></i> Summernote WYSIWYG</span>
                                                </div>
                                                <small class="text-muted d-block mb-2" style="font-size: 11px;">O corpo é automaticamente embutido no layout master responsivo Foco 7 (cabeçalho tático, suporte WhatsApp e rodapé).</small>
                                                <textarea name="corpo" id="summernote_{{ $key }}" class="form-control summernote summernote-email" data-chave="{{ $key }}" data-height="270">{{ $tpl['corpo_atual'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="dispararTesteIndividual('{{ $key }}')">
                                                <i class="fas fa-vial me-1"></i> Testar Envio Deste Template
                                            </button>
                                            <button type="submit" class="btn btn-danger fw-bold shadow-sm">
                                                <i class="fas fa-save me-1"></i> Salvar Template
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- COLUNA DIREITA: SIMULADOR DE PREVIEW MULTI-DISPOSITIVO EM TEMPO REAL -->
                            <div class="col-xl-6 col-lg-12">
                                <div class="card border shadow-sm h-100 preview-card d-flex flex-column">
                                    <div class="card-header bg-dark text-white py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success text-white py-1 px-2" style="font-size: 11px;"><i class="fas fa-circle text-white me-1 blink-dot"></i> Ao Vivo</span>
                                            <span class="fw-bold small text-light">Simulador em Tempo Real</span>
                                        </div>

                                        <!-- BOTÕES DE SELEÇÃO DE DISPOSITIVOS -->
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Seletor de Dispositivo">
                                            <button type="button" class="btn btn-outline-light active btn-device" data-chave="{{ $key }}" data-device="desktop" title="Computador / Desktop (Largura 100%)">
                                                <i class="fas fa-desktop me-1"></i> <span class="d-none d-sm-inline">Desktop</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-light btn-device" data-chave="{{ $key }}" data-device="tablet" title="Tablet (Largura 520px)">
                                                <i class="fas fa-tablet-alt me-1"></i> <span class="d-none d-sm-inline">Tablet</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-light btn-device" data-chave="{{ $key }}" data-device="mobile" title="Smartphone / Celular (Largura 375px)">
                                                <i class="fas fa-mobile-alt me-1"></i> <span class="d-none d-sm-inline">Mobile</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body p-3 bg-secondary-subtle d-flex flex-column align-items-center justify-content-start overflow-hidden flex-grow-1" style="min-height: 520px;">
                                        <!-- MOLDURA SIMULADORA RESPONSIVA -->
                                        <div id="preview_wrapper_{{ $key }}" class="device-frame-wrapper w-100 shadow rounded bg-white overflow-hidden" style="transition: max-width 0.35s ease, border 0.35s ease, border-radius 0.35s ease; max-width: 100%; border: 1px solid #cbd5e1;">
                                            <!-- BARRA SUPERIOR DO DISPOSITIVO -->
                                            <div class="device-top-bar bg-light py-1 px-3 border-bottom d-flex justify-content-between align-items-center text-secondary" style="font-size: 11px;">
                                                <span id="device_label_{{ $key }}" class="font-monospace text-truncate"><i class="fas fa-desktop text-danger me-1"></i> Modo: Desktop (100%)</span>
                                                <span class="badge bg-light text-dark border">Layout Master Responsivo</span>
                                            </div>
                                            <!-- IFRAME DO E-MAIL COMPILADO -->
                                            <iframe id="iframe_preview_{{ $key }}" class="w-100 border-0 email-preview-iframe" style="height: 480px; display: block; background: #f1f5f9;" sandbox="allow-same-origin allow-scripts"></iframe>
                                        </div>
                                    </div>

                                    <div class="card-footer bg-light py-2 px-3 border-top d-flex justify-content-between align-items-center small text-muted">
                                        <span><i class="fas fa-check-circle text-success me-1"></i> Renderização fiel em tempo real</span>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger fw-bold" onclick="atualizarPreviewTemplate('{{ $key }}', true)">
                                            <i class="fas fa-sync-alt me-1"></i> Sincronizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- ===================================================================
                 GUIA 5: CENTRAL DE CRON & AUTOMAÇÃO
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-cron" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-info"><i class="fas fa-clock me-2"></i> Central de Cron Jobs & Agendamento</h5>
                        <p class="text-secondary small mb-0">Acompanhe as rotinas automáticas de verificação de faturas, reconciliação de pagamentos e lembretes.</p>
                    </div>
                    <div>
                        <span class="badge bg-info-subtle text-info border border-info-subtle p-2">
                            <i class="fas fa-server me-1"></i> Horário do Servidor: {{ date('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>

                <!-- COMANDO PARA CPANEL -->
                <div class="card border mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-terminal me-2 text-warning"></i> Comando Cron Oficial para o cPanel</span>
                        <span class="badge bg-success">EXECUTAR A CADA MINUTO (* * * * *)</span>
                    </div>
                    <div class="card-body p-4 bg-light">
                        <p class="small text-secondary mb-2">
                            Acesse o <strong>cPanel &rarr; Cron Jobs (Tarefas Cron)</strong> da sua hospedagem e cadastre exatamente a linha abaixo para automatizar todas as rotinas da Foco 7:
                        </p>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace bg-dark text-white border-0 py-2 fs-6" id="cronCpanelCmd" value="{{ $cronStatus['comando_cpanel'] }}" readonly>
                            <button class="btn btn-warning fw-bold px-3" type="button" onclick="copiarTexto('cronCpanelCmd')">
                                <i class="fas fa-copy me-1"></i> Copiar Comando
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABELA DE ROTINAS AUTOMÁTICAS COM EXECUÇÃO SOB DEMANDA -->
                <div class="card border shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tasks me-2 text-danger"></i> Rotinas Agendadas do Sistema</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rotina Automática</th>
                                    <th>Comando Artisan</th>
                                    <th>Frequência</th>
                                    <th>Última Execução</th>
                                    <th class="text-end">Execução Sob Demanda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cronStatus['rotinas'] as $tarefaKey => $rotina)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $rotina['nome'] }}</div>
                                        <small class="text-secondary">{{ $rotina['descricao'] }}</small>
                                    </td>
                                    <td><code class="bg-light text-danger p-1 rounded font-monospace">{{ $rotina['comando'] }}</code></td>
                                    <td><span class="badge bg-secondary">{{ $rotina['frequencia'] }}</span></td>
                                    <td>
                                        <span class="small text-dark font-monospace" id="lastRun_{{ $tarefaKey }}">
                                            <i class="fas fa-history text-secondary me-1"></i> {{ $rotina['ultima_execucao'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold" onclick="executarCronManual('{{ $tarefaKey }}', '{{ $rotina['nome'] }}')">
                                            <i class="fas fa-play me-1"></i> Executar Agora
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
</div>

<!-- MODAL PARA CRIAÇÃO DE NOVO TEMPLATE DE E-MAIL -->
<div class="modal fade" id="modalCriarTemplate" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle text-danger me-2"></i> Criar Novo Template de E-mail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.gateways.criar_email_template') }}" method="POST" class="ajax-form" id="formCriarTemplate">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nome do Template <span class="text-danger">*</span></label>
                            <input type="text" name="nome" id="novoTemplateNome" class="form-control" required placeholder="Ex: Boas-Vindas ao Cliente VIP" onkeyup="gerarSlugTemplate(this.value)">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Identificador Único (Chave)</label>
                            <input type="text" name="chave" id="novoTemplateChave" class="form-control font-monospace" placeholder="boas_vindas_vip">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Descrição / Finalidade</label>
                            <input type="text" name="descricao" class="form-control" placeholder="Descreva brevemente quando este e-mail deve ser acionado...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Assunto do E-mail <span class="text-danger">*</span></label>
                            <input type="text" name="assunto" class="form-control" required placeholder="Ex: 🚀 Seja muito bem-vindo à Foco 7, {cliente_nome}!">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold d-flex justify-content-between">
                                <span>Corpo do E-mail (HTML) <span class="text-danger">*</span></span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-magic me-1"></i> Editor Summernote</span>
                            </label>
                            <textarea name="corpo" id="summernote_novo_template" class="form-control summernote" data-height="220"><p>Olá, <strong>{cliente_nome}</strong>!</p><p>É uma grande satisfação ter você como parceiro da <strong>{nome_empresa}</strong>. Sua segurança e tranquilidade são nossa prioridade máxima.</p><p>Em caso de dúvidas técnicas ou suporte, fale conosco:</p><p><a href="https://wa.me/559180510736" class="btn-action">Falar com Suporte WhatsApp</a></p></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-check me-1"></i> Criar &amp; Ativar Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA DISPARO DE TESTE DE E-MAIL -->
<div class="modal fade" id="modalTesteEmail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-paper-plane text-danger me-2"></i> Enviar E-mail de Demonstração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.gateways.email_teste') }}" method="POST" class="ajax-form" id="formTesteEmail">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Template a Testar</label>
                        <select name="template_chave" id="modalTemplateChave" class="form-select" required>
                            @foreach($templatesEmail as $k => $t)
                                <option value="{{ $k }}">{{ $t['nome'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">E-mail de Destino</label>
                        <input type="email" name="email_destinatario" class="form-control" value="{{ auth()->user()->email }}" required placeholder="seuemail@empresa.com">
                        <small class="text-secondary">O e-mail será disparado imediatamente através da rota única com o layout responsivo oficial compilado.</small>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-paper-plane me-1"></i> Disparar Demonstração</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // =========================================================================
    // DADOS MOCK / SIMULADOS PARA PRÉ-VISUALIZAÇÃO IMEDIATA (0ms DE LATÊNCIA)
    // =========================================================================
    const DADOS_SIMULADOS = {
        '{cliente_nome}': 'Carlos Alberto Silva (Demonstração)',
        '{cliente_email}': 'carlos.silva@exemplo.com.br',
        '{fatura_id}': 'FAT-20261009-0824',
        '{plano_nome}': 'FOCO 7 PLUS - Rastreamento Veicular Completo',
        '{valor}': 'R$ 89,90',
        '{data_vencimento}': '15/10/2026',
        '{link_fatura}': 'https://foco7.com.br/cliente/faturas',
        '{link_carne}': 'https://foco7.com.br/cliente/faturas',
        '{nome_empresa}': 'Foco 7 - Rastreamento e Segurança',
        '{whatsapp_empresa}': '(91) 8051-0736'
    };

    // Compilação local instantânea do layout oficial no navegador (0ms)
    function compilarHtmlLocal(corpo, assunto) {
        let corpoProc = corpo || '<p>Digite o conteúdo do e-mail no editor ao lado...</p>';
        let assuntoProc = assunto || 'Notificação Foco 7';
        for (const [tag, val] of Object.entries(DADOS_SIMULADOS)) {
            corpoProc = corpoProc.split(tag).join(val);
            assuntoProc = assuntoProc.split(tag).join(val);
        }

        return `<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${assuntoProc}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        p { margin: 0 0 16px 0; line-height: 1.65; color: #334155; font-size: 15px; }
        h1, h2, h3, h4, h5, h6 { margin: 0 0 14px 0; color: #0f172a; font-weight: 800; }
        a { color: #d90000; text-decoration: underline; font-weight: 600; }
        .btn-action { display: inline-block; background: linear-gradient(135deg, #d90000 0%, #b80000 100%); color: #ffffff !important; text-decoration: none !important; padding: 14px 28px; font-size: 15px; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 12px rgba(217, 0, 0, 0.25); text-align: center; margin: 12px 0 20px 0; }
        .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #d90000; border-radius: 8px; padding: 16px 20px; margin: 16px 0 20px 0; }
        @media only screen and (max-width: 620px) {
            .wrapper-table { width: 100% !important; max-width: 100% !important; border-radius: 0 !important; border: none !important; }
            .header-cell { padding: 22px 18px !important; }
            .content-cell { padding: 24px 18px !important; font-size: 15px !important; }
            .btn-action { display: block !important; width: 100% !important; box-sizing: border-box !important; }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 25px 8px; width: 100%;">
        <tr>
            <td align="center">
                <table role="presentation" class="wrapper-table" width="100%" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="header-cell" style="background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%); padding: 28px 30px; text-align: center; border-bottom: 4px solid #d90000;">
                            <div style="font-size: 26px; font-weight: 900; color: #ffffff; line-height: 1.1;"><span style="color: #d90000;">FOCO</span> 7</div>
                            <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; letter-spacing: 2.5px; margin-top: 6px; font-weight: 600;">Rastreamento &bull; Bloqueio 24h &bull; Ronda Tática</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content-cell" style="padding: 34px 32px 26px 32px; font-size: 15px; line-height: 1.65; color: #334155;">
                            ${corpoProc}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 32px 18px 32px; background-color: #ffffff;">
                            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px 16px; text-align: center; font-size: 13px; color: #475569;">
                                <strong style="color: #0f172a;">Suporte 24h WhatsApp:</strong> <a href="#" style="color: #25d366; text-decoration: none; font-weight: 800;">📱 (91) 8051-0736</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #0f172a; padding: 24px 30px; text-align: center; border-top: 1px solid #1e293b; font-size: 12px; color: #94a3b8; line-height: 1.6;">
                            <p style="margin: 0 0 4px 0; font-weight: 700; color: #f8fafc;">Foco 7 - Rastreamento e Segurança</p>
                            <p style="margin: 0; font-size: 11px; color: #64748b;">Tecnologia de alta precisão e pronta resposta para você e seu veículo.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>`;
    }

    // Timer para sincronização debounce via AJAX
    var previewTimers = {};

    // Atualiza a pré-visualização em tempo real dentro do iframe
    function atualizarPreviewTemplate(chave, forcarAjax) {
        var $assunto = $('#assunto_' + chave);
        var $summernote = $('#summernote_' + chave);
        var iframe = document.getElementById('iframe_preview_' + chave);

        if (!iframe) return;

        var assunto = $assunto.length ? $assunto.val() : '';
        var corpo = '';

        if ($summernote.length && $.fn.summernote && $summernote.next('.note-editor').length) {
            corpo = $summernote.summernote('code');
        } else if ($summernote.length) {
            corpo = $summernote.val();
        }

        // 1. Renderização Local Instantânea (0ms de latência visual)
        var htmlLocal = compilarHtmlLocal(corpo, assunto);
        try {
            var doc = iframe.contentWindow || iframe.contentDocument;
            if (doc.document) doc = doc.document;
            doc.open();
            doc.write(htmlLocal);
            doc.close();
        } catch (e) {
            console.error('Erro ao injetar preview local:', e);
        }

        // 2. Debounce AJAX para renderização 100% fiel pelo backend Blade
        if (previewTimers[chave]) {
            clearTimeout(previewTimers[chave]);
        }

        var delay = forcarAjax ? 0 : 350;
        previewTimers[chave] = setTimeout(function () {
            $.ajax({
                url: '{{ route("admin.gateways.preview_email") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    template_chave: chave,
                    assunto: assunto,
                    corpo: corpo
                },
                success: function (res) {
                    if (res.success && res.html) {
                        try {
                            var d = iframe.contentWindow || iframe.contentDocument;
                            if (d.document) d = d.document;
                            d.open();
                            d.write(res.html);
                            d.close();
                        } catch (err) {}
                    }
                }
            });
        }, delay);
    }

    // =========================================================================
    // INICIALIZAÇÃO E LISTENERS EM TEMPO REAL
    // =========================================================================
    $(function () {
        // Inicializa preview de todos os templates visíveis
        setTimeout(function () {
            $('.form-email-template').each(function () {
                var chave = $(this).data('chave');
                if (chave) {
                    atualizarPreviewTemplate(chave, false);
                }
            });
        }, 500);

        // Atualiza ao digitar no assunto
        $(document).on('input keyup', '.input-assunto', function () {
            var chave = $(this).data('chave');
            if (chave) {
                atualizarPreviewTemplate(chave, false);
            }
        });

        // Atualiza ao digitar no Summernote (através do trigger global)
        $(document).on('summernote.change change input', '.summernote-email', function () {
            var chave = $(this).data('chave');
            if (chave) {
                atualizarPreviewTemplate(chave, false);
            }
        });

        // Ao alternar sub-guias de templates, atualiza o iframe da aba selecionada
        $(document).on('shown.bs.tab', '#emailTemplatesPills button[data-bs-toggle="pill"]', function (e) {
            var target = $(e.target).attr('data-bs-target');
            if (target) {
                var chave = target.replace('#pill-', '');
                setTimeout(function () {
                    atualizarPreviewTemplate(chave, false);
                }, 150);
            }
        });

        // =====================================================================
        // ALTERNÂNCIA DE DISPOSITIVOS RESPONSIVOS (DESKTOP, TABLET, MOBILE)
        // =====================================================================
        $(document).on('click', '.btn-device', function () {
            var $btn = $(this);
            var chave = $btn.data('chave');
            var device = $btn.data('device');
            var $wrapper = $('#preview_wrapper_' + chave);
            var $label = $('#device_label_' + chave);

            $btn.siblings('.btn-device').removeClass('active');
            $btn.addClass('active');

            if (device === 'desktop') {
                $wrapper.css({
                    'max-width': '100%',
                    'border': '1px solid #cbd5e1',
                    'border-radius': '8px'
                });
                $label.html('<i class="fas fa-desktop text-danger me-1"></i> Modo: Desktop (100%)');
            } else if (device === 'tablet') {
                $wrapper.css({
                    'max-width': '520px',
                    'border': '4px solid #334155',
                    'border-radius': '16px'
                });
                $label.html('<i class="fas fa-tablet-alt text-danger me-1"></i> Modo: Tablet (520px)');
            } else if (device === 'mobile') {
                $wrapper.css({
                    'max-width': '375px',
                    'border': '6px solid #0f172a',
                    'border-radius': '26px'
                });
                $label.html('<i class="fas fa-mobile-alt text-danger me-1"></i> Modo: Smartphone Mobile (375px)');
            }
        });
    });

    // Copiar ou Inserir Tag Dinâmica
    function copiarOuInserirTag(tag) {
        navigator.clipboard.writeText(tag);
        
        // Verifica qual aba de template está ativa para colar no editor
        var $abaAtiva = $('#emailTemplatesPillsContent .tab-pane.active');
        if ($abaAtiva.length) {
            var $textarea = $abaAtiva.find('.summernote-email');
            if ($textarea.length && $.fn.summernote && $textarea.next('.note-editor').length) {
                $textarea.summernote('pasteHTML', ' ' + tag + ' ');
                var chave = $abaAtiva.find('.form-email-template').data('chave');
                if (chave) atualizarPreviewTemplate(chave, false);
                window.foco7Notify.success('Tag ' + tag + ' inserida no editor ativo!');
                return;
            }
        }
        window.foco7Notify.success('Tag ' + tag + ' copiada para a área de transferência!');
    }

    // Modal de Criação de Novo Template
    function abrirModalCriarTemplate() {
        new bootstrap.Modal(document.getElementById('modalCriarTemplate')).show();
    }

    function gerarSlugTemplate(nome) {
        var slug = (nome || '').toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]/g, '_')
            .replace(/_+/g, '_')
            .replace(/^_+|_+$/g, '');
        $('#novoTemplateChave').val(slug);
    }

    // Exclusão de Template Personalizado com SweetAlert2
    function excluirTemplatePersonalizado(chave, nome) {
        window.foco7Confirm('Excluir Template?', 'Deseja realmente remover o template personalizado "' + nome + '"?', function() {
            $.ajax({
                url: '{{ url("admin/gateways/excluir-template-email") }}/' + chave,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { window.location.reload(); }, 900);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: res.message || 'Falha ao excluir o template.',
                            confirmButtonColor: '#d90000'
                        });
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro interno do servidor.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                }
            });
        });
    }

    // Alternância Dinâmica de Ambiente Mercado Pago
    function alternarAmbienteMp(ambiente) {
        if (ambiente === 'sandbox') {
            $('#mpAlertSandbox').removeClass('d-none');
            $('#mpAlertProd').addClass('d-none');
            $('#mpCamposSandbox').removeClass('d-none');
            $('#mpCamposProd').addClass('d-none');
        } else {
            $('#mpAlertSandbox').addClass('d-none');
            $('#mpAlertProd').removeClass('d-none');
            $('#mpCamposSandbox').addClass('d-none');
            $('#mpCamposProd').removeClass('d-none');
        }
    }

    // Alternância Dinâmica de Ambiente Efí PRO
    function alternarAmbienteEfi(ambiente) {
        if (ambiente === 'sandbox') {
            $('#efiAlertSandbox').removeClass('d-none');
            $('#efiAlertProd').addClass('d-none');
            $('#efiCamposSandbox').removeClass('d-none');
            $('#efiCamposProd').addClass('d-none');
        } else {
            $('#efiAlertSandbox').addClass('d-none');
            $('#efiAlertProd').removeClass('d-none');
            $('#efiCamposSandbox').addClass('d-none');
            $('#efiCamposProd').removeClass('d-none');
        }
    }

    // Alternador de visualização de senhas/chaves
    function toggleSenha(inputId) {
        var el = document.getElementById(inputId);
        if (el) {
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    }

    // Copiar Texto para o Clipboard com Feedback Toastr
    function copiarTexto(id) {
        var el = document.getElementById(id);
        if (el) {
            el.select();
            el.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(el.value);
            window.foco7Notify.success('Copiado com sucesso para a área de transferência!');
        }
    }

    // Modal de Teste de E-mail
    function abrirModalTesteEmail() {
        var $abaAtiva = $('#emailTemplatesPillsContent .tab-pane.active');
        if ($abaAtiva.length) {
            var chave = $abaAtiva.find('.form-email-template').data('chave');
            if (chave) $('#modalTemplateChave').val(chave);
        }
        new bootstrap.Modal(document.getElementById('modalTesteEmail')).show();
    }

    function dispararTesteIndividual(chave) {
        $('#modalTemplateChave').val(chave);
        new bootstrap.Modal(document.getElementById('modalTesteEmail')).show();
    }

    // Execução Manual de Tarefa Cron com SweetAlert2
    function executarCronManual(tarefa, nome) {
        window.foco7Confirm('Executar ' + nome + '?', 'Deseja disparar esta rotina em segundo plano agora?', function() {
            Swal.fire({
                title: 'Executando Rotina...',
                html: '<div class="spinner-border text-danger" role="status"></div><p class="mt-3">Processando rotina <strong>' + nome + '</strong>...</p>',
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            $.ajax({
                url: '{{ route("admin.gateways.executar_cron") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    tarefa: tarefa
                },
                success: function(res) {
                    if (res.success) {
                        $('#lastRun_' + tarefa).html('<i class="fas fa-check text-success me-1"></i> ' + res.data_hora);
                        Swal.fire({
                            icon: 'success',
                            title: 'Rotina Concluída com Sucesso!',
                            html: '<div class="text-start bg-dark text-white p-3 rounded font-monospace small" style="max-height: 250px; overflow-y: auto;">' + (res.output || res.message) + '</div>',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Fechar'
                        });
                        window.foco7Notify.success(res.message);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha na Execução',
                            text: res.message || 'Ocorreu um erro ao processar a tarefa.',
                            confirmButtonColor: '#d90000'
                        });
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro interno do servidor.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de Execução',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                }
            });
        });
    }
</script>
@endsection
