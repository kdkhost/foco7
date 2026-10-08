@extends('admin.layouts.app')

@section('title', 'Portal de Pagamentos')
@section('page_title', 'Gateways & Portal de Pagamentos')
@section('page_description', 'Configure credenciais de Checkout Transparente para Mercado Pago, Efí PRO (PIX/Boleto) e regras financeiras.')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.gateways.salvar') }}" method="POST" enctype="multipart/form-data" class="ajax-form">
            @csrf

            <!-- 1. MERCADO PAGO (CHECKOUT TRANSPARENTE) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fab fa-cc-mastercard me-2"></i> Mercado Pago (Cartão, PIX & Boleto)
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="mp_ativo" id="mp_ativo" value="1" {{ $configs['mp_ativo'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="mp_ativo">Gateway Ativo</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Public Key (Chave Pública) *</label>
                            <input type="text" name="mp_public_key" class="form-control" value="{{ $configs['mp_public_key'] }}" placeholder="APP_USR-xxxx...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Access Token (Token de Acesso) *</label>
                            <div class="input-group">
                                <input type="password" name="mp_access_token" id="mpAccessToken" class="form-control" value="{{ $configs['mp_access_token'] }}" placeholder="APP_USR-xxxx...">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpAccessToken')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client ID</label>
                            <input type="text" name="mp_client_id" class="form-control" value="{{ $configs['mp_client_id'] }}" placeholder="Ex: 1234567890">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client Secret</label>
                            <div class="input-group">
                                <input type="password" name="mp_client_secret" id="mpClientSecret" class="form-control" value="{{ $configs['mp_client_secret'] }}" placeholder="Client_Secret_xxxx...">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('mpClientSecret')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Chave Secreta do Webhook (Assinatura HMAC)</label>
                            <input type="password" name="mp_webhook_secret" class="form-control" value="{{ $configs['mp_webhook_secret'] }}" placeholder="Assinatura enviada no cabeçalho x-signature">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="mp_sandbox" id="mp_sandbox" value="1" {{ $configs['mp_sandbox'] ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="mp_sandbox">
                                    Modo Sandbox (Ambiente de Testes do Mercado Pago)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. EFÍ PRO (GERENCIANET - PIX EM TEMPO REAL) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-bolt me-2"></i> Efí PRO (Gerencianet - PIX Instantâneo)
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="efi_ativo" id="efi_ativo" value="1" {{ $configs['efi_ativo'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="efi_ativo">Gateway Ativo</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client ID da Aplicação *</label>
                            <input type="text" name="efi_client_id" class="form-control" value="{{ $configs['efi_client_id'] }}" placeholder="Client_Id_xxxx...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Client Secret *</label>
                            <div class="input-group">
                                <input type="password" name="efi_client_secret" id="efiClientSecret" class="form-control" value="{{ $configs['efi_client_secret'] }}" placeholder="Client_Secret_xxxx...">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleSenha('efiClientSecret')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Chave PIX Cadastrada na Conta Efí *</label>
                            <input type="text" name="efi_chave_pix" class="form-control" value="{{ $configs['efi_chave_pix'] }}" placeholder="Chave Aleatória EVP, CNPJ, E-mail ou Telefone">
                            <small class="text-secondary">Chave PIX associada à conta bancária para receber os créditos automáticos.</small>
                        </div>

                        <!-- UPLOAD DO CERTIFICADO .PEM COM ARRASTA E SOLTA -->
                        <div class="col-12 mt-3">
                            <label class="form-label small fw-bold text-white">
                                <i class="fas fa-shield-alt text-warning me-1"></i> Certificado Digital Oficial (.PEM)
                            </label>
                            
                            @if(!empty($configs['efi_certificado_pem']))
                                <div class="alert alert-success d-flex align-items-center justify-content-between p-2 mb-2">
                                    <span class="small font-monospace">
                                        <i class="fas fa-certificate text-success me-1"></i> Certificado atual: <strong>{{ $configs['efi_certificado_pem'] }}</strong>
                                    </span>
                                    <span class="badge bg-success">INSTALADO</span>
                                </div>
                            @endif

                            <div class="foco-dropzone"
                                 data-input-name="efi_certificado"
                                 data-ratio="Formato .PEM (Chave privada / Certificado Gerencianet)"
                                 data-max-size="2MB"
                                 data-accept=".pem">
                            </div>
                            <small class="text-secondary d-block mt-1">O certificado .pem é gerado dentro do painel da Efí PRO na aba API PIX.</small>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="efi_sandbox" id="efi_sandbox" value="1" {{ $configs['efi_sandbox'] ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="efi_sandbox">
                                    Modo Homologação (Sandbox Efí)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. REGRAS COMERCIAIS & FINANCEIRAS -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent py-3">
                    <h5 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-calculator me-2"></i> Condições Comerciais, Parcelas & Prazos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Desconto no PIX à Vista (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="pix_desconto" class="form-control" value="{{ $configs['pix_desconto'] }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Expiração do QR Code PIX (Minutos)</label>
                            <div class="input-group">
                                <input type="number" name="pix_expiracao_minutos" class="form-control" value="{{ $configs['pix_expiracao_minutos'] }}">
                                <span class="input-group-text">min</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Máximo de Parcelas no Cartão</label>
                            <input type="number" name="cartao_max_parcelas" class="form-control" value="{{ $configs['cartao_max_parcelas'] }}" max="24">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Parcelas Sem Juros</label>
                            <input type="number" name="cartao_sem_juros" class="form-control" value="{{ $configs['cartao_sem_juros'] }}" max="12">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Juros Mensal Parcelas com Juros (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="cartao_juros_mensal" class="form-control" value="{{ $configs['cartao_juros_mensal'] }}">
                                <span class="input-group-text">% ao mês</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dias para Vencimento do Boleto</label>
                            <div class="input-group">
                                <input type="number" name="boleto_dias_vencimento" class="form-control" value="{{ $configs['boleto_dias_vencimento'] }}">
                                <span class="input-group-text">dias</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Multa por Atraso (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="multa_atraso" class="form-control" value="{{ $configs['multa_atraso'] }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Juros de Mora Diário (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.001" name="juros_diario" class="form-control" value="{{ $configs['juros_diario'] }}">
                                <span class="input-group-text">% ao dia</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Instruções de Pagamento (Impressas na Cobrança)</label>
                            <textarea name="instrucoes_cobranca" rows="2" class="form-control">{{ $configs['instrucoes_cobranca'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
                <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES COMPLETAS DO PORTAL DE PAGAMENTOS
            </button>
        </form>
    </div>

    <!-- URLs de Webhook e Informações -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-network-wired text-danger me-2"></i> Webhooks de Retorno Automático</h6>
            </div>
            <div class="card-body p-4 small text-secondary">
                <p>Cadastre estas URLs nos painéis dos gateways para que as baixas ocorram automaticamente em tempo real:</p>

                <label class="fw-bold text-white mb-1">Webhook Mercado Pago:</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $webhookUrlMp }}" readonly id="urlMp">
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copiarTexto('urlMp')">Copiar</button>
                </div>

                <label class="fw-bold text-white mb-1">Webhook Efí PRO (PIX):</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $webhookUrlEfi }}" readonly id="urlEfi">
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copiarTexto('urlEfi')">Copiar</button>
                </div>

                <div class="alert alert-info py-2 small mb-0">
                    <i class="fas fa-shield-alt me-1"></i> Os pagamentos via PIX são aprovados em menos de 5 segundos via Webhook seguro.
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
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function copiarTexto(id) {
        var el = document.getElementById(id);
        el.select();
        el.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(el.value);
        window.foco7Notify.success('URL de Webhook copiada com sucesso!');
    }
</script>
@endsection
