@extends('admin.layouts.app')

@section('title', 'Portal de Pagamentos & Gateways')
@section('page_title', 'Gateways de Pagamento & Cobranças')
@section('page_description', 'Gerencie credenciais de Checkout Transparente (Mercado Pago e Efí PRO) e emissão de carnês recorrentes.')

@section('content')
<div class="card shadow-sm border-0">
    <!-- NAVEGAÇÃO POR GUIAS (TABS) -->
    <div class="card-header bg-transparent p-0 border-bottom">
        <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="gatewaysTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="mp-tab" data-bs-toggle="tab" data-bs-target="#tab-mercadopago" type="button" role="tab">
                    <i class="fab fa-cc-mastercard text-danger me-2"></i> Mercado Pago
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="efi-tab" data-bs-toggle="tab" data-bs-target="#tab-efipro" type="button" role="tab">
                    <i class="fas fa-bolt text-warning me-2"></i> Efí PRO (Gerencianet &amp; Carnês)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="taxas-tab" data-bs-toggle="tab" data-bs-target="#tab-taxas" type="button" role="tab">
                    <i class="fas fa-percentage text-primary me-2"></i> Gateway Padrão & Repasse de Taxas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="regua-tab" data-bs-toggle="tab" data-bs-target="#tab-regua" type="button" role="tab">
                    <i class="fas fa-clock-rotate-left text-danger me-2"></i> Régua de Cobrança & Mensalidades
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
                            <h5 class="fw-bold mb-1 text-danger"><i class="fab fa-cc-mastercard me-2"></i> Integração Mercado Pago</h5>
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
                    <div class="card bg-body-secondary border mb-4">
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
                                <span class="fw-bold small text-body"><i class="fas fa-key text-warning me-1"></i> Credenciais de Testes (Sandbox)</span>
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
                        <div class="card-header bg-body-tertiary py-2">
                            <span class="fw-bold small text-body"><i class="fas fa-sliders-h me-1"></i> Métodos Aceitos & Webhook de Retorno</span>
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
                                        <input type="text" class="form-control bg-body-secondary font-monospace" value="{{ $webhookUrlMp }}" readonly id="urlWebhookMp">
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
                 GUIA 2: EFÍ PRO (GERENCIANET - PIX, BOLETO, CARTÃO & CARNÊS)
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-efipro" role="tabpanel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-warning"><i class="fas fa-bolt me-2"></i> Integração Efí PRO (Gerencianet)</h5>
                        <p class="text-secondary small mb-0">API Oficial do Banco Central para PIX instantâneo, Boletos Bancários, Cartão de Crédito e Emissão de Carnês em PDF.</p>
                    </div>
                </div>

                <!-- SUB-GUIAS INTERNAS EFÍ PRO -->
                <ul class="nav nav-pills mb-4 bg-body-secondary p-1 rounded-3 gap-2" id="efiSubTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-3 py-2" id="efi-credenciais-tab" data-bs-toggle="pill" data-bs-target="#subtab-efi-credenciais" type="button" role="tab">
                            <i class="fas fa-key text-warning me-2"></i> Credenciais &amp; Checkout Transparente
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-3 py-2" id="efi-carne-tab" data-bs-toggle="pill" data-bs-target="#tab-carne" type="button" role="tab">
                            <i class="fas fa-book text-success me-2"></i> Cobrança por Carnê (Efí PRO)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="efiSubTabsContent">
                    <!-- SUB-ABA 1: CREDENCIAIS & CHECKOUT TRANSPARENTE EFÍ PRO -->
                    <div class="tab-pane fade show active" id="subtab-efi-credenciais" role="tabpanel">
                        <form action="{{ route('admin.gateways.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
                            @csrf

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-warning"><i class="fas fa-plug me-2"></i> Status do Gateway &amp; Modos de Operação</h6>
                                    <p class="text-secondary small mb-0">Defina se a integração está ativa e selecione entre ambientes de Homologação ou Produção.</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" name="efi_ativo" id="efi_ativo" value="1" {{ $configs['efi_ativo'] ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6 fw-bold" for="efi_ativo">Gateway Ativo</label>
                                    </div>
                                </div>
                            </div>

                    <!-- SELETOR DINÂMICO DE AMBIENTE EFÍ PRO -->
                    <div class="card bg-body-tertiary border mb-4">
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
                                <span class="fw-bold small text-body"><i class="fas fa-key text-warning me-1"></i> Credenciais & Certificado Homologação (Sandbox)</span>
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
                        <div class="card-header bg-body-secondary py-2">
                            <span class="fw-bold small text-body"><i class="fas fa-sliders-h me-1"></i> Métodos Efí & Condições Comerciais</span>
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
                                        <input type="text" class="form-control bg-body-secondary font-monospace" value="{{ $webhookUrlEfi }}" readonly id="urlWebhookEfi">
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

            <!-- SUB-ABA 2: COBRANÇA POR CARNÊ (EXCLUSIVA EFÍ PRO) -->
            <div class="tab-pane fade" id="tab-carne" role="tabpanel">
                <div class="row g-4">
                    <!-- CONFIGURAÇÕES DE CARNÊ -->
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-body-tertiary py-3">
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
        </div>
    </div>

            <!-- ===================================================================
                 GUIA 4: GATEWAY PADRÃO & REPASSE DE TAXAS AO CLIENTE
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-taxas" role="tabpanel">
                <form action="{{ route('admin.gateways.salvar') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-primary"><i class="fas fa-percentage me-2"></i> Gateway Principal & Repasse de Taxas</h5>
                            <p class="text-secondary small mb-0">Defina o provedor preferencial para o Checkout Transparente e configure o repasse de taxas operacionais aos clientes.</p>
                        </div>
                    </div>

                    <!-- ESCOLHA DO GATEWAY PADRÃO -->
                    <div class="card border mb-4 shadow-sm">
                        <div class="card-header bg-body-tertiary py-3">
                            <h6 class="fw-bold mb-0 text-body"><i class="fas fa-crown text-warning me-2"></i> Gateway Padrão Ativo no Checkout Transparente</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="card h-100 p-3 border cursor-pointer {{ $configs['gateway_padrao'] === 'efi' ? 'border-primary bg-primary bg-opacity-10' : '' }}" style="cursor: pointer;">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="gateway_padrao" value="efi" class="form-check-input me-3 mt-0" {{ $configs['gateway_padrao'] === 'efi' ? 'checked' : '' }}>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-warning"><i class="fas fa-bolt me-1"></i> Efí PRO (Gerencianet)</h6>
                                                <p class="small text-muted mb-0">API Oficial BACEN para PIX instantâneo, Carnê em PDF de alta qualidade e Boletos com baixa taxa.</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="card h-100 p-3 border cursor-pointer {{ $configs['gateway_padrao'] === 'mercadopago' ? 'border-danger bg-danger bg-opacity-10' : '' }}" style="cursor: pointer;">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="gateway_padrao" value="mercadopago" class="form-check-input me-3 mt-0" {{ $configs['gateway_padrao'] === 'mercadopago' ? 'checked' : '' }}>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-danger"><i class="fab fa-cc-mastercard me-1"></i> Mercado Pago</h6>
                                                <p class="small text-muted mb-0">Gateway internacional Mercado Pago para Cartão de Crédito e PIX.</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CARD DE REPASSE DE TAXAS -->
                    <div class="card border mb-4 shadow-sm">
                        <div class="card-header w-100 bg-body-tertiary py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h6 class="fw-bold mb-0 text-body"><i class="fas fa-receipt text-primary me-2"></i> Repasse de Taxas Operacionais ao Cliente</h6>
                            <div class="form-check form-switch fs-5 mb-0 ms-auto">
                                <input class="form-check-input" type="checkbox" name="repassar_taxas_cliente" id="repassar_taxas_cliente" value="1" {{ $configs['repassar_taxas_cliente'] ? 'checked' : '' }}>
                                <label class="form-check-label fs-6 fw-bold text-body" for="repassar_taxas_cliente">Ativar Repasse de Custos</label>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info border-info d-flex align-items-center mb-4">
                                <i class="fas fa-info-circle fs-3 me-3 text-info"></i>
                                <div class="small">
                                    <strong>Como funciona o Repasse Transparente:</strong> Quando ativado, os custos operacionais e tarifas bancárias configuradas abaixo serão calculados em tempo real no checkout e somados ao total a pagar do cliente, discriminando claramente o subtotal e a taxa de emissão no carnê e faturas.
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- PIX -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <h6 class="fw-bold text-body mb-2"><i class="fas fa-qrcode text-success me-1"></i> Taxa do PIX</h6>
                                        <p class="text-muted small mb-3">Defina se haverá tarifa operacional sobre pagamentos via PIX.</p>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Tipo de Taxa</label>
                                                <select name="taxa_pix_tipo" class="form-select form-select-sm">
                                                    <option value="percentual" {{ $configs['taxa_pix_tipo'] === 'percentual' ? 'selected' : '' }}>Percentual (%)</option>
                                                    <option value="fixo" {{ $configs['taxa_pix_tipo'] === 'fixo' ? 'selected' : '' }}>Fixo (R$)</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Valor da Taxa</label>
                                                <input type="number" step="0.01" name="taxa_pix_valor" class="form-control form-control-sm" value="{{ $configs['taxa_pix_valor'] }}" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOLETO BANCÁRIO -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <h6 class="fw-bold text-body mb-2"><i class="fas fa-barcode text-primary me-1"></i> Taxa de Emissão de Boleto</h6>
                                        <p class="text-muted small mb-3">Tarifa fixa por boleto bancário emitido repassada ao cliente.</p>
                                        <label class="form-label small fw-bold">Valor Fixo por Boleto (R$)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" name="taxa_boleto_fixo" class="form-control form-control-sm" value="{{ $configs['taxa_boleto_fixo'] }}" placeholder="2.99">
                                        </div>
                                    </div>
                                </div>

                                <!-- CARNÊ DE MENSALIDADES -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <h6 class="fw-bold text-body mb-2"><i class="fas fa-book text-success me-1"></i> Taxa por Parcela do Carnê</h6>
                                        <p class="text-muted small mb-3">Tarifa bancária por cada lâmina/parcela do carnê Efí PRO.</p>
                                        <label class="form-label small fw-bold">Valor Fixo por Parcela (R$)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" step="0.01" name="taxa_carne_parcela_fixo" class="form-control form-control-sm" value="{{ $configs['taxa_carne_parcela_fixo'] }}" placeholder="2.99">
                                        </div>
                                    </div>
                                </div>

                                <!-- CARTÃO DE CRÉDITO -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <h6 class="fw-bold text-body mb-2"><i class="fas fa-credit-card text-danger me-1"></i> Taxa do Cartão de Crédito</h6>
                                        <p class="text-muted small mb-3">Tarifa de processamento da operadora somada ao valor cobrado.</p>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Taxa (%)</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" name="taxa_cartao_valor" class="form-control form-control-sm" value="{{ $configs['taxa_cartao_valor'] }}" placeholder="3.49">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold">Fixo por Transação</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">R$</span>
                                                    <input type="number" step="0.01" name="taxa_cartao_fixo" class="form-control form-control-sm" value="{{ $configs['taxa_cartao_fixo'] }}" placeholder="0.49">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> Salvar Gateway Padrão & Repasse de Taxas
                        </button>
                    </div>
                </form>
            </div>

            <!-- ===================================================================
                 5. ABA RÉGUA DE COBRANÇA AUTOMATIZADA & MENSALIDADES RECORRENTES
                 =================================================================== -->
            <div class="tab-pane fade" id="tab-regua" role="tabpanel">
                <form action="{{ route('admin.gateways.salvar') }}" method="POST" class="ajax-form">
                    @csrf
                    
                    <!-- CABEÇALHO DO MÓDULO & CONTROLE MESTRE -->
                    <div class="card border mb-4">
                        <div class="card-header w-100 bg-body-tertiary py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-clock-rotate-left me-2"></i> Régua de Cobrança Automatizada para Mensalidades</h6>
                                <small class="text-body-secondary">Disparos preventivos pré-vencimento, cobrança no vencimento e régua de atrasos com cálculo automático de multa e juros de mora.</small>
                            </div>
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" onclick="executarReguaCobrancaAgora()">
                                    <i class="fas fa-play me-1"></i> Processar Régua Agora
                                </button>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" name="regua_cobranca_ativa" value="1" id="regua_cobranca_ativa" {{ $configs['regua_cobranca_ativa'] ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Geração Antecipada de Mensalidades Recorrentes (Dias Antes)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-plus text-danger"></i></span>
                                        <input type="number" name="gerar_mensalidade_dias_antes" class="form-control" min="1" max="60" value="{{ $configs['gerar_mensalidade_dias_antes'] }}">
                                        <span class="input-group-text">dias de antecedência</span>
                                    </div>
                                    <small class="text-body-secondary">Número de dias antes da data base do cliente para emitir a próxima fatura do ciclo mensal.</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Canal: E-mail Transacional</label>
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="regua_enviar_email" value="1" id="regua_enviar_email" {{ $configs['regua_enviar_email'] ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="regua_enviar_email">Disparar por E-mail</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">Canal: WhatsApp</label>
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" name="regua_enviar_whatsapp" value="1" id="regua_enviar_whatsapp" {{ $configs['regua_enviar_whatsapp'] ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="regua_enviar_whatsapp">Habilitar WhatsApp</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 1: LEMBRETES PRÉ-VENCIMENTO (PREVENTIVOS) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3">
                            <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-bell me-2"></i> Lembretes Pré-Vencimento (Preventivos)</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- 1º LEMBRETE -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">1º Lembrete</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_1_ativo" value="1" {{ $configs['regua_lembrete_1_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold">Dias Antes do Vencimento</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_lembrete_1_dias" class="form-control" min="1" max="30" value="{{ $configs['regua_lembrete_1_dias'] }}">
                                            <span class="input-group-text">dias</span>
                                        </div>
                                        <small class="text-body-secondary d-block">Fatura disponível para pagamento antecipado.</small>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="badge bg-light text-dark border"><i class="fas fa-envelope text-primary me-1"></i> cobranca_lembrete_1</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2º LEMBRETE -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-info text-dark">2º Lembrete</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_2_ativo" value="1" {{ $configs['regua_lembrete_2_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold">Dias Antes do Vencimento</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_lembrete_2_dias" class="form-control" min="1" max="30" value="{{ $configs['regua_lembrete_2_dias'] }}">
                                            <span class="input-group-text">dias</span>
                                        </div>
                                        <small class="text-body-secondary d-block">Alerta de que faltam poucos dias.</small>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="badge bg-light text-dark border"><i class="fas fa-envelope text-info me-1"></i> cobranca_lembrete_2</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3º LEMBRETE -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-body-tertiary h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-warning text-dark">3º Lembrete (Véspera)</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_lembrete_3_ativo" value="1" {{ $configs['regua_lembrete_3_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold">Dias Antes do Vencimento</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_lembrete_3_dias" class="form-control" min="1" max="10" value="{{ $configs['regua_lembrete_3_dias'] }}">
                                            <span class="input-group-text">dia(s)</span>
                                        </div>
                                        <small class="text-body-secondary d-block">Aviso de que vence amanhã.</small>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="badge bg-light text-dark border"><i class="fas fa-envelope text-warning me-1"></i> cobranca_lembrete_3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COBRANÇA NO VENCIMENTO -->
                            <div class="p-3 border rounded bg-light-subtle mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-danger me-2">Cobrança no Vencimento (D-0)</span>
                                    <strong class="text-body">Disparar no Dia do Vencimento</strong>
                                    <div class="small text-body-secondary mt-1">Notifica o cliente às 08:00 informando que a fatura vence hoje e orientando o pagamento via PIX para evitar juros.</div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-white text-dark border"><i class="fas fa-envelope text-danger me-1"></i> cobranca_vencimento_hoje</span>
                                    <div class="form-check form-switch mb-0 fs-5">
                                        <input class="form-check-input" type="checkbox" name="regua_vencimento_hoje_ativo" value="1" {{ $configs['regua_vencimento_hoje_ativo'] ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 2: RÉGUA DE ATRASOS & ENCARGOS (MULTA E JUROS) -->
                    <div class="card border mb-4">
                        <div class="card-header bg-body-tertiary py-3">
                            <h6 class="fw-bold mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Lembretes Pós-Vencimento (Atrasos & Multas)</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 mb-4">
                                <!-- 1º AVISO DE ATRASO -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-danger-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-danger">1º Aviso de Atraso</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_1_ativo" value="1" {{ $configs['regua_atraso_1_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold">Dias de Atraso</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_atraso_1_dias" class="form-control" min="1" max="15" value="{{ $configs['regua_atraso_1_dias'] }}">
                                            <span class="input-group-text">dia(s)</span>
                                        </div>
                                        <small class="text-body-secondary d-block">Vencida ontem com cálculo inicial de multa.</small>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="badge bg-white text-dark border"><i class="fas fa-envelope text-danger me-1"></i> cobranca_atraso_1</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2º AVISO DE ATRASO -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-danger-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-danger">2º Aviso de Atraso</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_2_ativo" value="1" {{ $configs['regua_atraso_2_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold">Dias de Atraso</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_atraso_2_dias" class="form-control" min="2" max="30" value="{{ $configs['regua_atraso_2_dias'] }}">
                                            <span class="input-group-text">dias</span>
                                        </div>
                                        <small class="text-body-secondary d-block">Cobrança com multa + juros de mora.</small>
                                        <div class="mt-2 pt-2 border-top">
                                            <span class="badge bg-white text-dark border"><i class="fas fa-envelope text-danger me-1"></i> cobranca_atraso_2</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3º AVISO DE ATRASO (SUSPENSÃO) -->
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-dark text-white h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-danger">3º Aviso & Suspensão</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="regua_atraso_3_ativo" value="1" {{ $configs['regua_atraso_3_ativo'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <label class="form-label small fw-bold text-white">Dias de Atraso</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="number" name="regua_atraso_3_dias" class="form-control" min="5" max="60" value="{{ $configs['regua_atraso_3_dias'] }}">
                                            <span class="input-group-text">dias</span>
                                        </div>
                                        <small class="text-white-50 d-block">Alerta de corte de sinal e negativação SPC/Serasa.</small>
                                        <div class="mt-2 pt-2 border-top border-secondary">
                                            <span class="badge bg-secondary text-white"><i class="fas fa-envelope text-danger me-1"></i> cobranca_atraso_3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PARÂMETROS FINANCEIROS DE ENCARGOS -->
                            <div class="p-3 border rounded bg-body-tertiary">
                                <h6 class="fw-bold text-body mb-3"><i class="fas fa-coins text-warning me-2"></i> Configuração de Encargos por Inadimplência</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Multa por Atraso (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" name="regua_multa_percentual" class="form-control" value="{{ $configs['regua_multa_percentual'] }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-body-secondary">Padrão legal CDC: 2,0%</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Juros de Mora ao Mês (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" name="regua_juros_mensal_percentual" class="form-control" value="{{ $configs['regua_juros_mensal_percentual'] }}">
                                            <span class="input-group-text">% ao mês</span>
                                        </div>
                                        <small class="text-body-secondary">Padrão legal: 1,0% ao mês</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Juros Diários (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.001" name="regua_juros_diario_percentual" class="form-control" value="{{ $configs['regua_juros_diario_percentual'] }}">
                                            <span class="input-group-text">% ao dia</span>
                                        </div>
                                        <small class="text-body-secondary">Cálculo proporcional diário</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Dias de Tolerância</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="regua_dias_tolerancia" class="form-control" min="0" max="10" value="{{ $configs['regua_dias_tolerancia'] }}">
                                            <span class="input-group-text">dia(s)</span>
                                        </div>
                                        <small class="text-body-secondary">Prazo de carência antes dos juros</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">
                            <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DA RÉGUA DE COBRANÇA
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

    // Emissão de Carnê via AJAX com SweetAlert2 e Botão de Download PDF
    $(document).on('submit', '#formGerarCarne', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Gerando Carnê...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalText);
                if (res.success) {
                    Swal.fire({
                        title: 'Carnê Emitido com Sucesso!',
                        html: '<p class="text-muted mb-3">' + res.message + '</p>' +
                              '<div class="p-3 bg-body-secondary rounded text-start small mb-3 border">' +
                              '<strong>Código do Carnê:</strong> ' + res.codigo_carne + '<br>' +
                              '<strong>Parcelamento:</strong> ' + res.total_parcelas + ' parcelas de ' + res.valor_parcela + '<br>' +
                              '<strong>Plataforma:</strong> Efí PRO (Gerencianet)' +
                              '</div>',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-file-pdf me-1"></i> Baixar Carnê em PDF',
                        cancelButtonText: 'Fechar',
                        confirmButtonColor: '#198754'
                    }).then((result) => {
                        if (result.isConfirmed && res.pdf_url) {
                            window.open(res.pdf_url, '_blank');
                        }
                    });
                    $form[0].reset();
                } else {
                    Swal.fire('Erro!', res.message || 'Erro ao gerar carnê.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao conectar com o servidor.';
                Swal.fire('Atenção', msg, 'warning');
            }
        });
    });

    // ========================================================
    // DISPARO MANUAL DA RÉGUA DE COBRANÇA VIA AJAX COM SWEETALERT2
    // ========================================================
    function executarReguaCobrancaAgora() {
        Swal.fire({
            title: 'Processar Régua de Cobrança?',
            text: 'O sistema irá emitir mensalidades antecipadas, disparar os 3 lembretes pré-vencimento, cobrança no vencimento e avisos de atraso com cálculo de multa e juros.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d90000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-play me-1"></i> Sim, processar agora!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processando régua...',
                    text: 'Aguarde a verificação de faturas e disparo das notificações.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ route("admin.gateways.executar_regua") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            var m = res.metricas || {};
                            var preTotal = (m.lembretes_pre_1 || 0) + (m.lembretes_pre_2 || 0) + (m.lembretes_pre_3 || 0);
                            var atrasoTotal = (m.atraso_1 || 0) + (m.atraso_2 || 0) + (m.atraso_3 || 0);

                            var resumoHtml = '<div class="text-start small p-3 bg-body-tertiary rounded border mb-2">' +
                                '<p class="mb-1"><strong><i class="fas fa-plus-circle text-primary me-1"></i> Mensalidades Recorrentes Geradas:</strong> ' + (m.mensalidades_geradas || 0) + '</p>' +
                                '<p class="mb-1"><strong><i class="fas fa-bell text-info me-1"></i> Lembretes Preventivos Disparados:</strong> ' + preTotal + '</p>' +
                                '<p class="mb-1"><strong><i class="fas fa-exclamation-circle text-danger me-1"></i> Vencimento Hoje (D-0):</strong> ' + (m.vencimento_hoje || 0) + '</p>' +
                                '<p class="mb-1"><strong><i class="fas fa-triangle-exclamation text-warning me-1"></i> Avisos de Atraso Disparados:</strong> ' + atrasoTotal + '</p>' +
                                '<p class="mb-0"><strong><i class="fas fa-calculator text-success me-1"></i> Faturas com Encargos Atualizados:</strong> ' + (m.faturas_atualizadas_encargos || 0) + '</p>' +
                                '</div>';

                            Swal.fire({
                                icon: 'success',
                                title: 'Régua Processada com Sucesso!',
                                html: resumoHtml,
                                confirmButtonColor: '#d90000',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Aviso', res.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao processar régua de cobrança.';
                        Swal.fire('Erro', msg, 'error');
                    }
                });
            }
        });
    }

    // Sincronização automática de abas principais e sub-abas via Hash da URL (ex: #tab-carne, #carne, #tab-efipro)
    function sincronizarAbasPorHash() {
        var hash = window.location.hash;
        if (!hash) return;

        if (hash === '#tab-carne' || hash === '#carne') {
            var efiBtn = document.getElementById('efi-tab');
            if (efiBtn) {
                bootstrap.Tab.getOrCreateInstance(efiBtn).show();
            }
            var carneBtn = document.getElementById('efi-carne-tab');
            if (carneBtn) {
                bootstrap.Tab.getOrCreateInstance(carneBtn).show();
            }
            // Rolagem suave até a área de emissão de carnê se necessário
            setTimeout(function() {
                var el = document.getElementById('tab-carne');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 150);
        } else if (hash === '#tab-efipro' || hash === '#efipro') {
            var efiBtn = document.getElementById('efi-tab');
            if (efiBtn) {
                bootstrap.Tab.getOrCreateInstance(efiBtn).show();
            }
        } else if (hash === '#tab-mercadopago' || hash === '#mercadopago') {
            var mpBtn = document.getElementById('mp-tab');
            if (mpBtn) {
                bootstrap.Tab.getOrCreateInstance(mpBtn).show();
            }
        } else if (hash === '#tab-taxas' || hash === '#taxas') {
            var taxasBtn = document.getElementById('taxas-tab');
            if (taxasBtn) {
                bootstrap.Tab.getOrCreateInstance(taxasBtn).show();
            }
        } else if (hash === '#tab-regua' || hash === '#regua') {
            var reguaBtn = document.getElementById('regua-tab');
            if (reguaBtn) {
                bootstrap.Tab.getOrCreateInstance(reguaBtn).show();
            }
        }
    }

    $(document).ready(function() {
        sincronizarAbasPorHash();
        $(window).on('hashchange', sincronizarAbasPorHash);
    });
</script>
@endsection
