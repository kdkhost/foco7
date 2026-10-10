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

            <!-- ===================================================================
                 GUIA 3: COBRANÇA POR CARNÊ (EFÍ PRO PARA CLIENTES EXISTENTES)
                 =================================================================== -->
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
</script>
@endsection
