@extends('admin.layouts.app')

@section('title', 'WhatsApp Cobranças & Evolution API')
@section('page_title', 'WhatsApp Cobranças & Evolution API Go (v3.0)')
@section('page_description', 'Gerencie instâncias do WhatsApp, conexões via QR Code e automação de cobranças com PIX nativo e botões interativos.')

@section('styles')
<style>
    .tarja-dev-bloqueado {
        letter-spacing: 2px !important;
        font-weight: 700 !important;
        background-color: rgba(108, 117, 125, 0.08) !important;
        color: var(--bs-secondary-color) !important;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="row g-4">

    <!-- BANNER DE STATUS DA CONEXÃO & CRIPTOGRAFIA -->
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-4 bg-body-secondary border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" id="statusIconBox" style="width: 58px; height: 58px; background-color: rgba(25, 135, 84, 0.15);">
                            <i class="fab fa-whatsapp fs-2 text-success" id="statusIcon"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="fw-bold mb-0 text-body">WhatsApp Evolution API Go</h4>
                                <span class="badge {{ $statusCache === 'conectado' ? 'bg-success' : 'bg-secondary' }} px-3 py-1 fs-6 text-uppercase" id="badgeStatusGeral">
                                    <i class="fas {{ $statusCache === 'conectado' ? 'fa-check-circle' : 'fa-circle-xmark' }} me-1" id="badgeStatusIcon"></i>
                                    <span id="badgeStatusTexto">{{ $statusCache === 'conectado' ? 'Conectado' : ucfirst($statusCache) }}</span>
                                </span>
                                <span class="badge bg-dark bg-opacity-75 text-white border border-secondary px-2 py-1 small" title="Credenciais criptografadas no banco via AES-256">
                                    <i class="fas fa-lock text-warning me-1"></i> AES-256 Protegido
                                </span>
                            </div>
                            <p class="text-secondary small mb-0">
                                Instância ativa: <strong class="text-body font-monospace" id="txtInstanciaNome">{{ $configs['instance'] }}</strong> | 
                                Servidor: <a href="https://whatsapp.km.site.nom.br/api/docs" target="_blank" class="text-primary font-monospace fw-semibold" id="txtApiUrl">{{ $configs['api_url'] }}</a> | 
                                Instâncias Extras: <strong class="text-danger">{{ $totalInstanciasExtras ?? 0 }} (R$ {{ number_format($custoTotalRecorrente ?? 0, 2, ',', '.') }}/mês)</strong> | 
                                Cobranças Disparadas: <strong class="text-primary">{{ $totalCobrancasWpp }}</strong>
                            </p>
                        </div>
                    </div>

                    <!-- BOTÕES DE AÇÃO RÁPIDA -->
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" id="btnVerificarStatus" onclick="verificarStatusApi(true)">
                            <i class="fas fa-rotate me-1" id="iconVerificarStatus"></i> Verificar Status
                        </button>
                        <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" id="btnAbrirModalQr" onclick="conectarEabrirQr()">
                            <i class="fas fa-qrcode me-1"></i> Conectar / QR Code
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm fw-semibold" id="btnDesconectarWpp" onclick="desconectarInstancia()">
                            <i class="fas fa-power-off me-1"></i> Desconectar
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCriarInstancia">
                            <i class="fas fa-plus-circle me-1"></i> Criar Instância
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVEGAÇÃO POR GUIAS (TABS) -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent p-0 border-bottom">
                <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="whatsappTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="tab-conexao-btn" data-bs-toggle="tab" data-bs-target="#tab-conexao" type="button" role="tab">
                            <i class="fas fa-network-wired text-primary me-2"></i> Conexão & Credenciais
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-pix-btn" data-bs-toggle="tab" data-bs-target="#tab-pix" type="button" role="tab">
                            <i class="fab fa-pix text-success me-2"></i> Cobrança PIX & Formato
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-regua-btn" data-bs-toggle="tab" data-bs-target="#tab-regua" type="button" role="tab">
                            <i class="fas fa-clock-rotate-left text-danger me-2"></i> Régua de Cobrança
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-templates-btn" data-bs-toggle="tab" data-bs-target="#tab-templates" type="button" role="tab">
                            <i class="fas fa-comments text-info me-2"></i> Templates de Mensagens
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-teste-btn" data-bs-toggle="tab" data-bs-target="#tab-teste" type="button" role="tab">
                            <i class="fas fa-vial text-warning me-2"></i> Disparo de Teste
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-webhook-btn" data-bs-toggle="tab" data-bs-target="#tab-webhook" type="button" role="tab">
                            <i class="fas fa-satellite-dish text-secondary me-2"></i> Webhook & Callbacks
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-instancias-btn" data-bs-toggle="tab" data-bs-target="#tab-instancias" type="button" role="tab">
                            <i class="fas fa-layer-group text-warning me-2"></i> Instâncias & Licenciados <span class="badge bg-danger ms-1">R$ 60/mês</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.whatsapp.salvar') }}" method="POST" id="formWhatsappConfig" class="ajax-form keep-data">
                    @csrf

                    <div class="tab-content" id="whatsappTabsContent">

                        <!-- ===================================================================
                             GUIA 1: CONEXÃO & CREDENCIAIS (CRIPTOGRAFIA TOTAL NO BANCO)
                             =================================================================== -->
                        <div class="tab-pane fade show active" id="tab-conexao" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <h5 class="fw-bold text-body mb-0"><i class="fas fa-key text-primary me-2"></i> Parâmetros da Evolution API Go (v3.0)</h5>
                                        <span class="badge {{ $configs['dev_desbloqueado'] ? 'bg-success' : 'bg-danger' }} px-3 py-1 fs-6" id="badgeStatusSegurancaWpp">
                                            <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-unlock' : 'fa-lock' }} me-1"></i>
                                            <span id="badgeTextoSegurancaWpp">{{ $configs['dev_desbloqueado'] ? 'Acesso Liberado' : 'Protegido por Senha Master' }}</span>
                                        </span>
                                    </div>
                                    <p class="text-secondary small mb-0">Todas as chaves e tokens são gravados criptografados com AES-256 no banco de dados.</p>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <button type="button" class="btn btn-sm {{ $configs['dev_desbloqueado'] ? 'btn-outline-secondary' : 'btn-danger' }} fw-bold shadow-sm" id="btnAcaoSegurancaWpp" onclick="{{ $configs['dev_desbloqueado'] ? 'bloquearSegurancaWpp()' : 'solicitarSenhaSegurancaWpp()' }}">
                                        <i class="fas {{ $configs['dev_desbloqueado'] ? 'fa-lock' : 'fa-key' }} me-1"></i>
                                        <span id="btnTextoSegurancaWpp">{{ $configs['dev_desbloqueado'] ? 'Bloquear Acesso' : 'Desbloquear com Senha' }}</span>
                                    </button>
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" name="ativo" id="wpp_ativo" value="1" {{ $configs['ativo'] ? 'checked' : '' }}>
                                        <label class="form-check-label fs-6 fw-bold" for="wpp_ativo">WhatsApp Ativo no Sistema</label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="dev_password" id="wpp_dev_password_hidden" value="">

                            <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-center gap-2 {{ $configs['dev_desbloqueado'] ? 'd-none' : '' }}" id="alertaTarjaWpp">
                                <i class="fas fa-shield-alt text-warning fa-lg"></i>
                                <div>
                                    <strong>Dados do Servidor Protegidos por Senha Particular:</strong> A URL da API, Nome da Instância, Token e Chave Mestra estão bloqueados contra acesso de administradores comuns. Para visualizar ou alterar, utilize a Senha Master Particular.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">URL Base do Servidor Evolution API <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        <input type="{{ $configs['dev_desbloqueado'] ? 'url' : 'password' }}" 
                                               name="api_url" 
                                               id="inputApiUrl" 
                                               class="form-control font-monospace {{ $configs['dev_desbloqueado'] ? '' : 'tarja-dev-bloqueado' }}" 
                                               value="{{ $configs['api_url'] }}" 
                                               {{ $configs['dev_desbloqueado'] ? '' : 'readonly' }} 
                                               required 
                                               placeholder="https://api.seuservidor.com ou http://localhost:8080">
                                    </div>
                                    <div class="form-text small">Sem barra no final. Exemplo: <code>http://localhost:8080</code> ou <code>https://whatsapp.meudominio.com</code></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold mb-0">Nome da Instância (Instance Name) <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 fw-semibold" onclick="listarInstanciasServidor()" style="font-size: 0.75rem;" title="Consultar instâncias no servidor Evolution">
                                            <i class="fas fa-cloud-arrow-down me-1"></i> Ver no Servidor
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-robot"></i></span>
                                        <input type="{{ $configs['dev_desbloqueado'] ? 'text' : 'password' }}" 
                                               name="instance" 
                                               id="inputInstance" 
                                               class="form-control font-monospace {{ $configs['dev_desbloqueado'] ? '' : 'tarja-dev-bloqueado' }}" 
                                               value="{{ $configs['instance'] }}" 
                                               {{ $configs['dev_desbloqueado'] ? '' : 'readonly' }} 
                                               required 
                                               placeholder="foco7">
                                    </div>
                                    <div class="form-text small">Identificador único da instância na Evolution API. Ex: <code>foco7</code>, <code>foco7-cobrancas</code></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Token da Instância (Instance API Key) <span class="text-danger">*</span>
                                        <span class="badge bg-warning text-dark small ms-1"><i class="fas fa-lock"></i> Criptografado</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                        <input type="password" 
                                               name="apikey" 
                                               id="inputApiKey" 
                                               class="form-control font-monospace {{ $configs['dev_desbloqueado'] ? '' : 'tarja-dev-bloqueado' }}" 
                                               value="{{ $configs['apikey'] }}" 
                                               {{ $configs['dev_desbloqueado'] ? '' : 'readonly' }} 
                                               placeholder="Token de autenticação da instância">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputApiKey', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small">Enviado no header <code>apikey: &lt;token&gt;</code> para enviar mensagens, conectar e consultar status.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Global API Key (Chave Mestra Admin) <span class="badge bg-secondary-subtle text-secondary small ms-1">Opcional</span>
                                        <span class="badge bg-warning text-dark small ms-1"><i class="fas fa-lock"></i> Criptografado</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-shield-halved"></i></span>
                                        <input type="password" 
                                               name="global_apikey" 
                                               id="inputGlobalApiKey" 
                                               class="form-control font-monospace {{ $configs['dev_desbloqueado'] ? '' : 'tarja-dev-bloqueado' }}" 
                                               value="{{ $configs['global_apikey'] }}" 
                                               {{ $configs['dev_desbloqueado'] ? '' : 'readonly' }} 
                                               placeholder="Chave mestra (opcional se já conectada)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibilidadeSenha('inputGlobalApiKey', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small">
                                        <strong>Não é necessária se a instância já estiver conectada</strong> no outro sistema. Preencha apenas para criar/listar instâncias globais.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Delay entre Disparos (Milissegundos)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-stopwatch"></i></span>
                                        <input type="number" name="delay" class="form-control" value="{{ $configs['delay'] }}" min="0" max="10000" step="100" placeholder="0">
                                        <span class="input-group-text">ms</span>
                                    </div>
                                    <div class="form-text small">Intervalo simulado antes de enviar (Ex: 1200ms para humanizar envios).</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Anexo de Documento PDF (Boleto / Carnê)</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="enviar_pdf" id="enviar_pdf" value="1" {{ $configs['enviar_pdf'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="enviar_pdf">
                                            Enviar automaticamente PDF da fatura/carnê via WhatsApp
                                        </label>
                                    </div>
                                    <div class="form-text small">Se marcado, além do texto/botão, envia o arquivo PDF do carnê/fatura quando disponível.</div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 2: COBRANÇA PIX & FORMATO DE ENVIO
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-pix" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fab fa-pix text-success me-2"></i> Configurações de Pagamento PIX no WhatsApp</h5>
                                <p class="text-secondary small mb-0">Escolha o formato interativo que seus clientes receberão no aplicativo do WhatsApp.</p>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold d-block">Formato de Envio das Cobranças</label>
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <div class="card h-100 border p-3 cursor-pointer formato-card {{ $configs['tipo_envio'] === 'botoes_interativos' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selecionarFormato('botoes_interativos')">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="tipo_envio" id="fmt_botoes" value="botoes_interativos" {{ $configs['tipo_envio'] === 'botoes_interativos' ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="fmt_botoes">Botões Interativos (Recomendado)</label>
                                                </div>
                                                <p class="small text-secondary mb-0">Envia a mensagem com botão de <strong>Pagar Online</strong> (Link) e botão de <strong>Copiar Código PIX</strong> em um toque.</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="card h-100 border p-3 cursor-pointer formato-card {{ $configs['tipo_envio'] === 'pix_nativo' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selecionarFormato('pix_nativo')">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="tipo_envio" id="fmt_pix" value="pix_nativo" {{ $configs['tipo_envio'] === 'pix_nativo' ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="fmt_pix">Botão PIX Nativo (/send/pix)</label>
                                                </div>
                                                <p class="small text-secondary mb-0">Usa o endpoint dedicado <code>/send/pix</code> com cabeçalho de pagamento nativo e chave PIX direta.</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="card h-100 border p-3 cursor-pointer formato-card {{ $configs['tipo_envio'] === 'review_and_pay' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selecionarFormato('review_and_pay')">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="tipo_envio" id="fmt_review" value="review_and_pay" {{ $configs['tipo_envio'] === 'review_and_pay' ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="fmt_review">Revisar e Pagar (/send/button)</label>
                                                </div>
                                                <p class="small text-secondary mb-0">Exibe botão 'Revisar e Pagar' com lista de itens, SKU e valor em centavos integrado com o WhatsApp Pay.</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-lg-3">
                                            <div class="card h-100 border p-3 cursor-pointer formato-card {{ $configs['tipo_envio'] === 'texto' ? 'border-primary bg-primary bg-opacity-10' : '' }}" onclick="selecionarFormato('texto')">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="tipo_envio" id="fmt_texto" value="texto" {{ $configs['tipo_envio'] === 'texto' ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="fmt_texto">Texto Completo com Link</label>
                                                </div>
                                                <p class="small text-secondary mb-0">Mensagem de texto formatada tradicional, compatível com 100% dos aparelhos e versões de WhatsApp.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nome do Comerciante / Empresa <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                        <input type="text" name="merchant_name" class="form-control" value="{{ $configs['merchant_name'] }}" required placeholder="Foco 7 Rastreamento">
                                    </div>
                                    <div class="form-text small">Exibido nos botões de pagamento e cabeçalhos PIX.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tipo da Chave PIX <span class="text-danger">*</span></label>
                                    <select name="pix_key_type" class="form-select fw-semibold">
                                        <option value="CPF" {{ $configs['pix_key_type'] === 'CPF' ? 'selected' : '' }}>CPF</option>
                                        <option value="CNPJ" {{ $configs['pix_key_type'] === 'CNPJ' ? 'selected' : '' }}>CNPJ</option>
                                        <option value="EMAIL" {{ $configs['pix_key_type'] === 'EMAIL' ? 'selected' : '' }}>E-mail</option>
                                        <option value="PHONE" {{ $configs['pix_key_type'] === 'PHONE' ? 'selected' : '' }}>Telefone / Celular</option>
                                        <option value="EVP" {{ $configs['pix_key_type'] === 'EVP' ? 'selected' : '' }}>EVP (Chave Aleatória)</option>
                                    </select>
                                    <div class="form-text small">Exigido pela Evolution API para botões de pagamento.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Chave PIX da Empresa
                                        <span class="badge bg-warning text-dark small ms-1"><i class="fas fa-lock"></i> Criptografado</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-pix"></i></span>
                                        <input type="text" name="pix_key" class="form-control font-monospace" value="{{ $configs['pix_key'] }}" placeholder="Chave PIX cadastrada">
                                    </div>
                                    <div class="form-text small">Chave usada para gerar os botões diretos de PIX no WhatsApp.</div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 3: RÉGUA DE COBRANÇA AUTOMATIZADA
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-regua" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-clock-rotate-left text-danger me-2"></i> Disparos Automatizados da Régua de Cobrança</h5>
                                <p class="text-secondary small mb-0">Defina quais etapas da régua financeira enviarão notificações automáticas via WhatsApp.</p>
                            </div>

                            <div class="alert alert-info border-info d-flex align-items-center mb-4">
                                <i class="fas fa-info-circle fs-3 me-3 text-info"></i>
                                <div>
                                    <strong>Integração com o Cron do Sistema:</strong> Quando a Régua de Cobrança for executada (automaticamente via Cron Job diário ou manualmente), todas as faturas que atingirem as datas de vencimento configuradas serão notificadas pelo WhatsApp além do e-mail.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border h-100">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_lembrete_1" id="regua_lembrete_1" value="1" {{ $configs['regua_lembrete_1'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="regua_lembrete_1">1º Lembrete (10 dias antes)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Avisa o cliente que a fatura do mês já está emitida e disponível.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border h-100">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_lembrete_2" id="regua_lembrete_2" value="1" {{ $configs['regua_lembrete_2'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="regua_lembrete_2">2º Lembrete (5 dias antes)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Lembrete amigável avisando sobre o vencimento próximo.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border h-100">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_lembrete_3" id="regua_lembrete_3" value="1" {{ $configs['regua_lembrete_3'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="regua_lembrete_3">3º Lembrete (Véspera - 1 dia antes)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Alerta de véspera para evitar que o cliente esqueça a data de vencimento.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border border-warning h-100 bg-warning bg-opacity-10">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_vencimento_hoje" id="regua_vencimento_hoje" value="1" {{ $configs['regua_vencimento_hoje'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="regua_vencimento_hoje">Vencimento Hoje (D-0)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Mensagem disparada na manhã do dia do vencimento.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border border-danger h-100 bg-danger bg-opacity-10">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_atraso_1" id="regua_atraso_1" value="1" {{ $configs['regua_atraso_1'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger" for="regua_atraso_1">1º Aviso de Atraso (1 dia após)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Notifica sobre o vencimento expirado com valor atualizado.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border border-danger h-100 bg-danger bg-opacity-10">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_atraso_2" id="regua_atraso_2" value="1" {{ $configs['regua_atraso_2'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger" for="regua_atraso_2">2º Aviso de Atraso (5 dias após)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Cobrança formal com cálculo automático de multa e juros diários.</p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card p-3 border border-danger h-100 bg-danger bg-opacity-15">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="regua_atraso_3" id="regua_atraso_3" value="1" {{ $configs['regua_atraso_3'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger" for="regua_atraso_3">3º Aviso (10 dias - Risco Suspensão)</label>
                                        </div>
                                        <p class="small text-secondary mb-0">Notificação grave informando sobre o risco iminente de suspensão do rastreamento.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 4: TEMPLATES DAS MENSAGENS DE COBRANÇA
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-templates" role="tabpanel">
                            <div class="mb-3 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-comments text-info me-2"></i> Modelos das Mensagens de WhatsApp</h5>
                                <p class="text-secondary small mb-0">Personalize o texto de cada etapa. Clique em uma variável abaixo para copiá-la.</p>
                            </div>

                            <!-- PAINEL DE TAGS / VARIÁVEIS DISPONÍVEIS -->
                            <div class="card bg-body-tertiary border mb-4">
                                <div class="card-body p-3">
                                    <span class="small fw-bold text-uppercase text-secondary d-block mb-2">Variáveis Disponíveis para os Textos:</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{cliente_nome}')" title="Nome do cliente">{cliente_nome}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{fatura_id}')" title="Código da fatura">{fatura_id}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{plano_nome}')" title="Nome do plano">{plano_nome}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{valor}')" title="Valor base da fatura">{valor}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{valor_atualizado}')" title="Valor com encargos de atraso">{valor_atualizado}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{multa}')" title="Valor calculado da multa">{multa}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{juros}')" title="Valor calculado dos juros">{juros}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{data_vencimento}')" title="Data no formato DD/MM/AAAA">{data_vencimento}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{dias_atraso}')" title="Dias em atraso">{dias_atraso}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{link_fatura}')" title="Link direto para pagamento">{link_fatura}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{pix_copia_cola}')" title="Código PIX copia-e-cola">{pix_copia_cola}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{chave_pix}')" title="Chave PIX da empresa">{chave_pix}</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-monospace" onclick="copiarTag('{empresa_nome}')" title="Nome da empresa">{empresa_nome}</button>
                                    </div>
                                </div>
                            </div>

                            <!-- ACCORDION COM OS TEMPLATES -->
                            <div class="accordion" id="accordionTemplatesWpp">

                                <!-- 1º Lembrete -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#acc-lembrete-1">
                                            <i class="fas fa-bell text-primary me-2"></i> 1º Lembrete Pré-Vencimento (10 dias antes)
                                        </button>
                                    </h2>
                                    <div id="acc-lembrete-1" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_lembrete_1" rows="6" class="form-control font-monospace">{{ $configs['tpl_lembrete_1'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2º Lembrete -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#acc-lembrete-2">
                                            <i class="fas fa-clock text-primary me-2"></i> 2º Lembrete Pré-Vencimento (5 dias antes)
                                        </button>
                                    </h2>
                                    <div id="acc-lembrete-2" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_lembrete_2" rows="6" class="form-control font-monospace">{{ $configs['tpl_lembrete_2'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3º Lembrete -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#acc-lembrete-3">
                                            <i class="fas fa-hourglass-half text-warning me-2"></i> 3º Lembrete Pré-Vencimento (Véspera - 1 dia antes)
                                        </button>
                                    </h2>
                                    <div id="acc-lembrete-3" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_lembrete_3" rows="6" class="form-control font-monospace">{{ $configs['tpl_lembrete_3'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vencimento Hoje -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#acc-vencimento-hoje">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i> Cobrança no Vencimento (Vence HOJE / D-0)
                                        </button>
                                    </h2>
                                    <div id="acc-vencimento-hoje" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_vencimento_hoje" rows="6" class="form-control font-monospace">{{ $configs['tpl_vencimento_hoje'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 1º Aviso de Atraso -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#acc-atraso-1">
                                            <i class="fas fa-calendar-xmark text-danger me-2"></i> 1º Aviso de Atraso (1 dia vencido)
                                        </button>
                                    </h2>
                                    <div id="acc-atraso-1" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_atraso_1" rows="6" class="form-control font-monospace">{{ $configs['tpl_atraso_1'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2º Aviso de Atraso -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#acc-atraso-2">
                                            <i class="fas fa-fire text-danger me-2"></i> 2º Aviso de Atraso (5 dias com Multa e Juros)
                                        </button>
                                    </h2>
                                    <div id="acc-atraso-2" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_atraso_2" rows="6" class="form-control font-monospace">{{ $configs['tpl_atraso_2'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3º Aviso de Atraso -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold text-danger" type="button" data-bs-toggle="collapse" data-bs-target="#acc-atraso-3">
                                            <i class="fas fa-ban text-danger me-2"></i> 3º Aviso de Atraso &amp; Suspensão de Sinal (10 dias)
                                        </button>
                                    </h2>
                                    <div id="acc-atraso-3" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_atraso_3" rows="6" class="form-control font-monospace">{{ $configs['tpl_atraso_3'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nova Fatura Gerada -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#acc-fatura-gerada">
                                            <i class="fas fa-file-invoice-dollar text-success me-2"></i> Notificação de Nova Fatura Gerada
                                        </button>
                                    </h2>
                                    <div id="acc-fatura-gerada" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_fatura_gerada" rows="6" class="form-control font-monospace">{{ $configs['tpl_fatura_gerada'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagamento Confirmado -->
                                <div class="accordion-item border mb-2 rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold text-success" type="button" data-bs-toggle="collapse" data-bs-target="#acc-pagamento-confirmado">
                                            <i class="fas fa-check-circle text-success me-2"></i> Notificação de Pagamento Confirmado (Baixa Automática)
                                        </button>
                                    </h2>
                                    <div id="acc-pagamento-confirmado" class="accordion-collapse collapse" data-bs-parent="#accordionTemplatesWpp">
                                        <div class="accordion-body">
                                            <textarea name="tpl_pagamento_confirmado" rows="6" class="form-control font-monospace">{{ $configs['tpl_pagamento_confirmado'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 5: DISPARO DE TESTE & DIAGNÓSTICO
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-teste" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-vial text-warning me-2"></i> Disparo de Teste em Tempo Real</h5>
                                <p class="text-secondary small mb-0">Envie uma mensagem de teste para o seu próprio WhatsApp para validar a conexão e a renderização dos botões.</p>
                            </div>

                            <div class="row g-3 max-w-700">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">Número de WhatsApp para Teste <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="text" id="inputTelefoneTeste" class="form-control mascara-telefone" placeholder="(11) 99999-9999 ou 5511999999999">
                                    </div>
                                    <div class="form-text small">Pode digitar com ou sem máscara. O sistema ajusta para o formato internacional.</div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Tipo do Teste <span class="text-danger">*</span></label>
                                    <select id="selectTipoTeste" class="form-select fw-semibold">
                                        <option value="texto">Texto Formatado</option>
                                        <option value="pix">Botão PIX Nativo (/send/pix)</option>
                                        <option value="botoes">Botões Interativos (URL + Copiar)</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="button" class="btn btn-warning fw-bold px-4 shadow-sm" id="btnDispararTeste" onclick="enviarMensagemTeste()">
                                        <i class="fas fa-paper-plane me-1"></i> Disparar Mensagem de Teste
                                    </button>
                                </div>

                                <div class="col-12 mt-4" id="boxRespostaTeste" style="display: none;">
                                    <label class="form-label fw-semibold small text-secondary">Retorno da API:</label>
                                    <pre class="bg-body-secondary p-3 rounded font-monospace small mb-0" id="jsonRespostaTeste" style="max-height: 250px; overflow-y: auto;"></pre>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 6: WEBHOOK & CALLBACKS
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-webhook" role="tabpanel">
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold text-body mb-1"><i class="fas fa-satellite-dish text-secondary me-2"></i> Webhook da Evolution API Go</h5>
                                <p class="text-secondary small mb-0">Receba atualizações em tempo real sobre status de conexão, entregas e respostas de clientes.</p>
                            </div>

                            <div class="card bg-body-tertiary border p-4 mb-4">
                                <label class="form-label fw-semibold">URL de Webhook do Seu Sistema</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control font-monospace" id="inputWebhookUrl" value="{{ $webhookUrl }}" readonly>
                                    <button class="btn btn-primary" type="button" onclick="copiarWebhookUrl()">
                                        <i class="fas fa-copy me-1"></i> Copiar URL
                                    </button>
                                </div>
                                <div class="form-text small">
                                    Esta URL já é configurada automaticamente ao clicar no botão <strong>Conectar / QR Code</strong> via <code>POST /instance/connect</code>.
                                </div>
                            </div>

                            <div class="card border">
                                <div class="card-header bg-transparent py-2">
                                    <strong class="small text-body"><i class="fas fa-list-check me-1"></i> Eventos Sincronizados via Webhook:</strong>
                                </div>
                                <div class="card-body p-3">
                                    <ul class="mb-0 small text-secondary">
                                        <li><code>CONNECTION</code>: Monitora mudanças de estado da conexão (open, connecting, close) e atualiza os badges em tempo real.</li>
                                        <li><code>QRCODE</code>: Captura o QR Code para leitura no painel.</li>
                                        <li><code>SEND_MESSAGE</code>: Notificações de confirmação de envio no WhatsApp.</li>
                                        <li><code>MESSAGE</code>: Mensagens recebidas dos clientes.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- ===================================================================
                             GUIA 7: GESTÃO DE INSTÂNCIAS (EMPRESA & LICENCIADOS - R$ 60/MÊS)
                             =================================================================== -->
                        <div class="tab-pane fade" id="tab-instancias" role="tabpanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
                                <div>
                                    <h5 class="fw-bold text-body mb-1"><i class="fas fa-network-wired text-warning me-2"></i> Controle Central de Instâncias WhatsApp Evolution Go</h5>
                                    <p class="text-secondary small mb-0">Servidor oficial: <a href="https://whatsapp.km.site.nom.br/api/docs" target="_blank" class="fw-bold text-primary font-monospace">https://whatsapp.km.site.nom.br</a> | Documentação: <a href="https://whatsapp.km.site.nom.br/api/docs" target="_blank" class="fw-bold text-info">/api/docs</a></p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick="listarInstanciasServidor()">
                                        <i class="fas fa-rotate me-1"></i> Sincronizar Servidor
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCriarInstancia">
                                        <i class="fas fa-plus-circle me-1"></i> Nova Instância (+ R$ 60,00/mês)
                                    </button>
                                </div>
                            </div>

                            <!-- CARDS DE RESUMO DE TARIFAS -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="card border-0 bg-success bg-opacity-10 h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small fw-semibold text-success text-uppercase">1ª Instância (Principal)</span>
                                                <i class="fas fa-shield-halved text-success fs-5"></i>
                                            </div>
                                            <h4 class="fw-bold text-success mb-1">INCLUSA</h4>
                                            <small class="text-secondary">Instância padrão <strong class="font-monospace text-body">{{ $configs['instance'] }}</strong> da empresa Foco 7.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-warning bg-opacity-10 h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small fw-semibold text-warning text-uppercase">Instâncias Extras / Licenciados</span>
                                                <i class="fas fa-layer-group text-warning fs-5"></i>
                                            </div>
                                            <h4 class="fw-bold text-body mb-1">{{ $totalInstanciasExtras ?? 0 }} <small class="fs-6 text-muted">instâncias extras</small></h4>
                                            <small class="text-secondary">Custo fixo por instância extra: <strong>R$ {{ number_format($custoInstanciaExtra ?? 60.00, 2, ',', '.') }} / mês</strong>.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-danger bg-opacity-10 h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small fw-semibold text-danger text-uppercase">Recorrência Mensal Extra</span>
                                                <i class="fas fa-receipt text-danger fs-5"></i>
                                            </div>
                                            <h4 class="fw-bold text-danger mb-1">R$ {{ number_format($custoTotalRecorrente ?? 0, 2, ',', '.') }} <small class="fs-6 text-muted">/mês</small></h4>
                                            <small class="text-secondary">Faturamento total gerado pelas instâncias extras contratadas.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TABELA DE INSTÂNCIAS REGISTRADAS -->
                            <div class="card border">
                                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-body"><i class="fas fa-list me-2 text-primary"></i> Instâncias Provisionadas no Servidor Central</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Identificador / Nome</th>
                                                <th>Tipo / Proprietário</th>
                                                <th>Custo Recorrente</th>
                                                <th>Criado em</th>
                                                <th>Status</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success table-opacity-25">
                                                <td>
                                                    <strong class="font-monospace text-primary">{{ $configs['instance'] }}</strong>
                                                    <span class="badge bg-success ms-1">Principal Foco 7</span>
                                                </td>
                                                <td><span class="badge bg-dark">Empresa Master</span></td>
                                                <td><strong class="text-success">Inclusa no Plano</strong></td>
                                                <td>-</td>
                                                <td><span class="badge {{ $statusCache === 'conectado' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($statusCache) }}</span></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="conectarEabrirQr()">
                                                        <i class="fas fa-qrcode me-1"></i> Conectar / QR Code
                                                    </button>
                                                </td>
                                            </tr>
                                            @if(!empty($instanciasExtras) && is_array($instanciasExtras))
                                                @foreach($instanciasExtras as $extra)
                                                <tr>
                                                    <td>
                                                        <strong class="font-monospace text-body">{{ $extra['nome'] ?? 'N/D' }}</strong>
                                                    </td>
                                                    <td>
                                                        @if(($extra['tipo'] ?? '') === 'licenciado')
                                                            <span class="badge bg-primary">Licenciado FOCO 7</span>
                                                        @else
                                                            <span class="badge bg-secondary">Instância Extra Empresa</span>
                                                        @endif
                                                    </td>
                                                    <td><strong class="text-danger">R$ {{ number_format($extra['valor_recorrente'] ?? 60.00, 2, ',', '.') }} / mês</strong></td>
                                                    <td><small class="text-muted">{{ !empty($extra['criado_em']) ? date('d/m/Y H:i', strtotime($extra['criado_em'])) : '-' }}</small></td>
                                                    <td><span class="badge bg-success">Ativa</span></td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarInstanciaServidor('{{ $extra['nome'] }}', '{{ $extra['token'] ?? '' }}')">
                                                            <i class="fas fa-arrow-right me-1"></i> Selecionar
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- BOTÃO FIXO DE SALVAR CONFIGURAÇÕES -->
                    <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" id="btnSalvarConfigs">
                            <i class="fas fa-save me-1"></i> Salvar Configurações &amp; Criptografar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<!-- ===================================================================
     MODAL DE CONEXÃO & QR CODE
     =================================================================== -->
<div class="modal fade" id="modalQrCode" tabindex="-1" aria-labelledby="modalQrCodeLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="modalQrCodeLabel"><i class="fab fa-whatsapp me-2"></i> Conectar Aparelho WhatsApp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="pararPollingQr()"></button>
            </div>
            <div class="modal-body p-4 text-center">

                <!-- ÁREA DE CARREGANDO / SPINNER -->
                <div id="qrLoadingBox" class="py-4">
                    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h6 class="fw-bold text-body">Gerando QR Code na Evolution API...</h6>
                    <p class="text-secondary small mb-0">Por favor, aguarde enquanto comunicamos com a instância.</p>
                </div>

                <!-- ÁREA DO QR CODE -->
                <div id="qrCodeContentBox" style="display: none;">
                    <div class="p-3 bg-white d-inline-block rounded-3 shadow-sm border mb-3">
                        <img id="imgQrCodeDisplay" src="" alt="QR Code WhatsApp" class="img-fluid" style="width: 260px; height: 260px; object-fit: contain;">
                    </div>
                    <div class="alert alert-light border small text-start mb-3">
                        <ol class="mb-0 ps-3">
                            <li>Abra o <strong>WhatsApp</strong> no seu celular.</li>
                            <li>Toque no menu (3 pontinhos) ou <strong>Configurações</strong>.</li>
                            <li>Selecione <strong>Aparelhos conectados</strong> e depois <strong>Conectar um aparelho</strong>.</li>
                            <li>Aponte a câmera do celular para este QR Code.</li>
                        </ol>
                    </div>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="recarregarQrCode()">
                            <i class="fas fa-rotate me-1"></i> Atualizar QR Code
                        </button>
                    </div>
                </div>

                <!-- ÁREA DE JÁ CONECTADO -->
                <div id="qrConectadoBox" style="display: none;" class="py-4">
                    <div class="bg-success bg-opacity-15 text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px;">
                        <i class="fas fa-check-circle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-success mb-1">WhatsApp Conectado com Sucesso!</h5>
                    <p class="text-secondary small mb-3">Sua instância está online e pronta para disparar cobranças.</p>
                    <button type="button" class="btn btn-success fw-bold px-4" data-bs-dismiss="modal">
                        Concluído
                    </button>
                </div>

                <!-- ÁREA DE ERRO -->
                <div id="qrErroBox" style="display: none;" class="py-3">
                    <div class="bg-danger bg-opacity-15 text-danger rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-triangle-exclamation fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-danger mb-1">Não foi possível obter o QR Code</h6>
                    <p class="text-secondary small mb-3" id="txtMensagemErroQr">Verifique se o servidor Evolution API está online e se as credenciais estão corretas.</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" onclick="conectarEabrirQr()">
                            <i class="fas fa-rotate me-1"></i> Tentar Novamente
                        </button>
                        <button type="button" class="btn btn-primary btn-sm fw-bold" onclick="autoCriarOuSincronizarInstanciaModal()">
                            <i class="fas fa-wand-magic-sparkles me-1"></i> Criar / Sincronizar no Servidor
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ===================================================================
     MODAL DE CRIAÇÃO ADMINISTRATIVA DA INSTÂNCIA (RECORRÊNCIA R$ 60/MÊS)
     =================================================================== -->
<div class="modal fade" id="modalCriarInstancia" tabindex="-1" aria-labelledby="modalCriarInstanciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="modalCriarInstanciaLabel"><i class="fas fa-plus-circle me-2"></i> Criar Nova Instância na Evolution API</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- CARD DE PRECIFICAÇÃO E RECORRÊNCIA DA INSTÂNCIA -->
                <div class="card border-warning bg-warning bg-opacity-10 mb-3 shadow-none">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <span class="badge bg-danger text-white px-2 py-1 fs-6">
                                <i class="fas fa-tag me-1"></i> R$ 60,00 / mês por Instância Adicional
                            </span>
                            <span class="badge bg-success text-white">Evolution Go Dedicado</span>
                        </div>
                        <p class="small text-secondary mb-2">
                            A primeira instância (<code class="fw-bold text-dark">foco7</code>) está inclusa no plano. A criação de instâncias extras para a empresa ou licenciados possui cobrança recorrente de <strong>R$ 60,00 cada</strong> provisionada diretamente no cluster oficial central (<a href="https://whatsapp.km.site.nom.br/api/docs" target="_blank" class="fw-bold text-primary font-monospace">whatsapp.km.site.nom.br</a>).
                        </p>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="checkConcordoValorExtra" checked>
                            <label class="form-check-label small fw-semibold text-body" for="checkConcordoValorExtra">
                                Estou ciente do custo recorrente de <strong>R$ 60,00/mês</strong> para instâncias extras criadas.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Instância</label>
                    <input type="text" id="modalInstanciaNome" class="form-control font-monospace" value="{{ (!empty($configs['instance']) && !str_contains($configs['instance'], '•')) ? $configs['instance'] : 'foco7' }}" placeholder="Ex: foco7_cobranca ou foco7_vendas">
                    <div class="form-text small">Identificador único no servidor <code>https://whatsapp.km.site.nom.br</code></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Token da Instância (API Key)</label>
                    <input type="text" id="modalInstanciaToken" class="form-control font-monospace" value="{{ (!empty($configs['apikey']) && !str_contains($configs['apikey'], '•')) ? $configs['apikey'] : 'iuDQ1F1GwW1KIDP1GqzrjQ2JutKd0gnarmWlmcnQ2SVj3aNL2d' }}" placeholder="Chave de segurança da instância">
                </div>

                <div class="alert alert-info border-info small mb-0">
                    <i class="fas fa-info-circle text-info me-1"></i> Servidor Central Evolution Go: <strong>https://whatsapp.km.site.nom.br</strong> (<a href="https://whatsapp.km.site.nom.br/api/docs" target="_blank" class="text-primary fw-bold">Swagger Docs /api/docs</a>).
                </div>
            </div>
            <div class="modal-footer bg-body-tertiary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary fw-bold" id="btnConfirmarCriarInstancia" onclick="executarCriacaoInstancia()">
                    <i class="fas fa-check me-1"></i> Confirmar &amp; Criar Instância (R$ 60,00/mês se extra)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var pollingTimer = null;

    // Alternador de visualização de senha
    function toggleVisibilidadeSenha(inputId, btn) {
        var input = document.getElementById(inputId);
        if (input.classList.contains('tarja-dev-bloqueado')) {
            solicitarSenhaSegurancaWpp();
            return;
        }
        var icon = btn.querySelector('i');
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

    function solicitarSenhaSegurancaWpp() {
        Swal.fire({
            title: 'Parâmetros Restritos do Servidor',
            html: `
                <div class="text-start mb-2">
                    <p class="small text-secondary mb-3">
                        Informe a <strong>Senha Master Particular</strong> para liberar a visualização e edição das credenciais e URL do servidor Evolution API Go.
                    </p>
                </div>
            `,
            input: 'password',
            inputLabel: 'Senha Master Particular',
            inputPlaceholder: 'Digite sua senha master...',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off',
                autocomplete: 'current-password'
            },
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-unlock me-1"></i> Desbloquear Acesso',
            confirmButtonColor: '#d90000',
            cancelButtonText: 'Cancelar',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: (senha) => {
                if (!senha || senha.trim().length === 0) {
                    Swal.showValidationMessage('Informe a senha master!');
                    return false;
                }
                return $.ajax({
                    url: '{{ route("admin.whatsapp.desbloquear_seguranca") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { senha: senha }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Falha ao autenticar.');
                    }
                    return { response: response, senha: senha };
                }).catch(error => {
                    var msg = error.responseJSON?.message || error.message || 'Senha incorreta!';
                    Swal.showValidationMessage(msg);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                var dados = result.value.response.dados;
                var senha = result.value.senha;

                $('#wpp_dev_password_hidden').val(senha);

                // Preenche os campos reais e remove a tarja
                $('#inputApiUrl').val(dados.api_url).attr('type', 'url').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputInstance').val(dados.instance).attr('type', 'text').removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputApiKey').val(dados.apikey).removeClass('tarja-dev-bloqueado').prop('readonly', false);
                $('#inputGlobalApiKey').val(dados.global_apikey).removeClass('tarja-dev-bloqueado').prop('readonly', false);

                $('#modalInstanciaNome').val(dados.instance);
                $('#modalInstanciaToken').val(dados.apikey);

                $('#txtInstanciaNome').text(dados.instance);
                $('#txtApiUrl').text(dados.api_url);

                // Atualiza badges e botões
                $('#badgeStatusSegurancaWpp').removeClass('bg-danger').addClass('bg-success');
                $('#badgeStatusSegurancaWpp i').removeClass('fa-lock').addClass('fa-unlock');
                $('#badgeTextoSegurancaWpp').text('Acesso Liberado');
                $('#btnAcaoSegurancaWpp').attr('onclick', 'bloquearSegurancaWpp()').removeClass('btn-danger').addClass('btn-outline-secondary');
                $('#btnTextoSegurancaWpp').text('Bloquear Acesso');
                $('#btnAcaoSegurancaWpp i').removeClass('fa-key').addClass('fa-lock');
                $('#alertaTarjaWpp').addClass('d-none');

                Swal.fire({
                    icon: 'success',
                    title: 'Acesso Liberado!',
                    text: 'As credenciais do servidor foram liberadas para visualização e edição nesta sessão.',
                    confirmButtonColor: '#28a745'
                });
            }
        });
    }

    function bloquearSegurancaWpp() {
        $.ajax({
            url: '{{ route("admin.whatsapp.bloquear_seguranca") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(res) {
                Swal.fire({
                    icon: 'info',
                    title: 'Servidor Bloqueado!',
                    text: res.message || 'As credenciais do servidor foram bloqueadas novamente.',
                    confirmButtonColor: '#d90000'
                }).then(() => {
                    location.reload();
                });
            }
        });
    }

    // Seleção de cards de formato
    function selecionarFormato(val) {
        $('input[name="tipo_envio"][value="' + val + '"]').prop('checked', true);
        $('.formato-card').removeClass('border-primary bg-primary bg-opacity-10');
        $('input[name="tipo_envio"]:checked').closest('.formato-card').addClass('border-primary bg-primary bg-opacity-10');
    }

    // Copia tags do painel para o clipboard
    function copiarTag(tag) {
        navigator.clipboard.writeText(tag).then(function () {
            window.foco7Notify.success('Tag ' + tag + ' copiada para a área de transferência!');
        });
    }

    // Copia URL de webhook
    function copiarWebhookUrl() {
        var input = document.getElementById('inputWebhookUrl');
        input.select();
        navigator.clipboard.writeText(input.value).then(function () {
            window.foco7Notify.success('URL de Webhook copiada com sucesso!');
        });
    }

    // Consulta de status da API
    function verificarStatusApi(showNotification) {
        var $btn = $('#btnVerificarStatus');
        var $icon = $('#iconVerificarStatus');
        $icon.addClass('fa-spin');
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.whatsapp.status") }}',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.conectado) {
                    $('#badgeStatusGeral').removeClass('bg-secondary bg-danger bg-warning').addClass('bg-success');
                    $('#badgeStatusIcon').removeClass('fa-circle-xmark fa-spinner').addClass('fa-check-circle');
                    $('#badgeStatusTexto').text('Conectado');
                    $('#statusIconBox').css('background-color', 'rgba(25, 135, 84, 0.15)');
                    $('#statusIcon').removeClass('text-secondary text-danger text-warning').addClass('text-success');

                    if (showNotification) {
                        window.foco7Notify.success('WhatsApp está conectado e pronto para envios!');
                    }
                } else if (res.estado === 'conectando') {
                    $('#badgeStatusGeral').removeClass('bg-secondary bg-success bg-danger').addClass('bg-warning text-dark');
                    $('#badgeStatusIcon').removeClass('fa-check-circle fa-circle-xmark').addClass('fa-spinner fa-spin');
                    $('#badgeStatusTexto').text('Conectando...');
                    if (showNotification) {
                        window.foco7Notify.warning('Instância aguardando leitura do QR Code.');
                    }
                } else {
                    $('#badgeStatusGeral').removeClass('bg-success bg-warning').addClass('bg-secondary');
                    $('#badgeStatusIcon').removeClass('fa-check-circle fa-spinner').addClass('fa-circle-xmark');
                    $('#badgeStatusTexto').text('Desconectado');
                    $('#statusIconBox').css('background-color', 'rgba(108, 117, 125, 0.15)');
                    $('#statusIcon').removeClass('text-success text-danger text-warning').addClass('text-secondary');

                    if (showNotification) {
                        window.foco7Notify.info('WhatsApp está desconectado.');
                    }
                }
            },
            error: function () {
                $('#badgeStatusGeral').removeClass('bg-success bg-warning bg-secondary').addClass('bg-danger');
                $('#badgeStatusTexto').text('Erro de Conexão');
                if (showNotification) {
                    window.foco7Notify.error('Não foi possível conectar ao servidor Evolution API.');
                }
            },
            complete: function () {
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
            }
        });
    }

    // Conectar e abrir modal com QR Code
    function conectarEabrirQr() {
        var modal = new bootstrap.Modal(document.getElementById('modalQrCode'));
        modal.show();

        $('#qrLoadingBox').show();
        $('#qrCodeContentBox').hide();
        $('#qrConectadoBox').hide();
        $('#qrErroBox').hide();

        $.ajax({
            url: '{{ route("admin.whatsapp.conectar") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
                if (res.token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
                    $('#inputApiKey').val(res.token);
                }
                if (res.instancia) {
                    $('#txtInstanciaNome').text(res.instancia);
                }
                if (res.qrcode) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    // Verifica se já está conectado
                    verificarSeConectou();
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao conectar';
                $('#qrLoadingBox').hide();
                $('#txtMensagemErroQr').text(msg);
                $('#qrErroBox').show();
            }
        });
    }

    // Auto-criação ou sincronização inteligente a partir do modal de QR Code
    function autoCriarOuSincronizarInstanciaModal() {
        $('#qrErroBox').hide();
        $('#qrLoadingBox').show();
        $('#qrLoadingBox h6').text('Sincronizando / Criando Instância no Servidor...');

        var nome = $('#inputInstance').val() || 'carlos_americo_cavalcante_silva_8_1';

        $.ajax({
            url: '{{ route("admin.whatsapp.criar_instancia") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nome: nome
            },
            dataType: 'json',
            success: function (res) {
                window.foco7Notify.success(res.message);
                if (res.token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
                    $('#inputApiKey').val(res.token);
                }
                if (res.qrcode) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    conectarEabrirQr();
                }
            },
            error: function (xhr) {
                $('#qrLoadingBox').hide();
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao sincronizar/criar instância.';
                $('#txtMensagemErroQr').text(msg);
                $('#qrErroBox').show();
            }
        });
    }

    // Consulta e exibe as instâncias diretamente do servidor Evolution API
    function listarInstanciasServidor() {
        Swal.fire({
            title: 'Consultando Servidor...',
            html: '<div class="py-3"><i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i><p class="text-secondary small mb-0">Buscando instâncias cadastradas na Evolution API...</p></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.ajax({
            url: '{{ route("admin.whatsapp.sincronizar_instancias") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            dataType: 'json',
            success: function (res) {
                if (!res.success || !res.instancias) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nenhuma Instância',
                        text: res.message || 'Nenhuma instância retornada pelo servidor.',
                        confirmButtonColor: '#d90000'
                    });
                    return;
                }

                var lista = res.instancias;
                if (!Array.isArray(lista) && typeof lista === 'object') {
                    lista = Object.values(lista);
                }

                if (lista.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Nenhuma Instância Encontrada',
                        html: `
                            <p class="small text-secondary mb-3">O servidor Evolution API está online, mas nenhuma instância foi criada ainda.</p>
                            <button type="button" class="btn btn-primary btn-sm fw-bold" onclick="Swal.close(); abrirModalCriarInstancia();">
                                <i class="fas fa-plus me-1"></i> Criar Instância Agora
                            </button>
                        `,
                        showConfirmButton: false
                    });
                    return;
                }

                var htmlInst = '<div class="list-group text-start my-2" style="max-height: 280px; overflow-y: auto;">';
                lista.forEach(function (inst) {
                    var nome = inst.name || (inst.instance && inst.instance.name) || (inst.instanceName) || 'Sem Nome';
                    var status = inst.status || (inst.instance && inst.instance.status) || (inst.state) || 'desconectado';
                    var token = inst.token || (inst.instance && inst.instance.token) || (inst.apikey) || '';
                    var isConnected = (status === 'open' || status === 'connected' || status === 'conectado');
                    var statusBadge = isConnected 
                        ? '<span class="badge bg-success">Conectado</span>' 
                        : '<span class="badge bg-secondary">' + status + '</span>';

                    htmlInst += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2 mb-1 border rounded">
                            <div>
                                <strong class="d-block text-body font-monospace">${nome}</strong>
                                <small class="text-secondary">${statusBadge}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="selecionarInstanciaServidor('${nome}', '${token}')">
                                <i class="fas fa-check me-1"></i> Selecionar
                            </button>
                        </div>
                    `;
                });
                htmlInst += '</div>';

                Swal.fire({
                    title: 'Instâncias no Servidor Evolution',
                    html: htmlInst,
                    showCloseButton: true,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Falha ao consultar servidor.';
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Comunicação',
                    text: msg,
                    confirmButtonColor: '#d90000'
                });
            }
        });
    }

    function selecionarInstanciaServidor(nome, token) {
        Swal.close();
        $('#inputInstance').val(nome);
        $('#txtInstanciaNome').text(nome);
        if (token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
            $('#inputApiKey').val(token);
        }
        window.foco7Notify.success('Instância "' + nome + '" selecionada! Salve as configurações ou clique em Conectar / QR Code.');
    }

    // Exibe imagem do QR Code
    function exibirQrCode(base64) {
        var src = base64;
        if (!src.startsWith('data:image')) {
            src = 'data:image/png;base64,' + src;
        }
        $('#imgQrCodeDisplay').attr('src', src);
        $('#qrLoadingBox').hide();
        $('#qrCodeContentBox').show();
    }

    // Recarrega o QR Code
    function recarregarQrCode() {
        $('#qrCodeContentBox').hide();
        $('#qrLoadingBox').show();

        $.ajax({
            url: '{{ route("admin.whatsapp.qr") }}',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.qrcode) {
                    exibirQrCode(res.qrcode);
                } else {
                    verificarSeConectou();
                }
            },
            error: function () {
                verificarSeConectou();
            }
        });
    }

    // Polling contínuo enquanto o modal de QR code estiver aberto
    function iniciarPollingStatusQr() {
        pararPollingQr();
        pollingTimer = setInterval(function () {
            $.ajax({
                url: '{{ route("admin.whatsapp.status") }}',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.conectado) {
                        pararPollingQr();
                        $('#qrLoadingBox').hide();
                        $('#qrCodeContentBox').hide();
                        $('#qrErroBox').hide();
                        $('#qrConectadoBox').show();
                        verificarStatusApi(false);
                        window.foco7Notify.success('WhatsApp pareado com sucesso!');
                    }
                }
            });
        }, 3500);
    }

    function pararPollingQr() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    function verificarSeConectou() {
        $.ajax({
            url: '{{ route("admin.whatsapp.status") }}',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                $('#qrLoadingBox').hide();
                if (res.conectado) {
                    $('#qrConectadoBox').show();
                    verificarStatusApi(false);
                } else if (res.qrcode) {
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    $('#txtMensagemErroQr').text('Nenhum QR Code retornado. A instância pode já estar conectada ou precisando de reinicialização.');
                    $('#qrErroBox').show();
                }
            },
            error: function () {
                $('#qrLoadingBox').hide();
                $('#qrErroBox').show();
            }
        });
    }

    // Desconectar instância com SweetAlert2
    function desconectarInstancia() {
        window.confirmarAcao(
            'Desconectar WhatsApp?',
            'A instância será desconectada e não enviará notificações até ser reconectada.',
            'warning',
            function () {
                $.ajax({
                    url: '{{ route("admin.whatsapp.desconectar") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            window.foco7Notify.success(res.message);
                            verificarStatusApi(false);
                        } else {
                            window.foco7Notify.error(res.message);
                        }
                    },
                    error: function () {
                        window.foco7Notify.error('Falha ao desconectar instância.');
                    }
                });
            }
        );
    }

    // Criar instância administrativamente
    function executarCriacaoInstancia() {
        var nome = $('#modalInstanciaNome').val();
        var token = $('#modalInstanciaToken').val();
        var $btn = $('#btnConfirmarCriarInstancia');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Criando...');

        $.ajax({
            url: '{{ route("admin.whatsapp.criar_instancia") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                nome: nome,
                token: token
            },
            dataType: 'json',
            success: function (res) {
                $('#modalCriarInstancia').modal('hide');
                window.foco7Notify.success(res.message);
                if (res.instancia) {
                    $('#txtInstanciaNome').text(res.instancia);
                    $('#inputInstance').val(res.instancia);
                }
                if (res.token && !$('#inputApiKey').hasClass('tarja-dev-bloqueado')) {
                    $('#inputApiKey').val(res.token);
                }
                verificarStatusApi(false);
                if (res.qrcode) {
                    var modalQr = new bootstrap.Modal(document.getElementById('modalQrCode'));
                    modalQr.show();
                    exibirQrCode(res.qrcode);
                    iniciarPollingStatusQr();
                } else {
                    conectarEabrirQr();
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao criar instância';
                window.foco7Notify.error(msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Criar Instância Agora');
            }
        });
    }

    // Disparo de teste via AJAX
    function enviarMensagemTeste() {
        var tel = $('#inputTelefoneTeste').val();
        var tipo = $('#selectTipoTeste').val();
        var $btn = $('#btnDispararTeste');

        if (!tel) {
            window.foco7Notify.warning('Por favor, informe o número de WhatsApp para teste.');
            $('#inputTelefoneTeste').focus();
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Enviando Teste...');

        $.ajax({
            url: '{{ route("admin.whatsapp.teste") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                numero: tel,
                tipo: tipo
            },
            dataType: 'json',
            success: function (res) {
                window.foco7Notify.success(res.message);
                $('#boxRespostaTeste').show();
                $('#jsonRespostaTeste').text(JSON.stringify(res, null, 2));
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao disparar teste';
                window.foco7Notify.error(msg);
                $('#boxRespostaTeste').show();
                $('#jsonRespostaTeste').text(JSON.stringify(xhr.responseJSON || {}, null, 2));
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Disparar Mensagem de Teste');
            }
        });
    }

    // Auto-checagem suave de status no carregamento da página e interceptação AJAX do formulário
    $(function () {
        verificarStatusApi(false);

        // Ao clicar em qualquer campo protegido por tarja, solicita a senha master
        $(document).on('click', '.tarja-dev-bloqueado', function () {
            solicitarSenhaSegurancaWpp();
        });

        // Submissão do formulário de configurações via AJAX com SweetAlert2 e Toastr
        $('#formWhatsappConfig').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $('#btnSalvarConfigs');
            var origHtml = $btn.html();

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Salvando e Criptografando...');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Configurações Salvas!',
                            text: res.message,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Entendido'
                        });

                        if (res.instance && !$('#inputInstance').hasClass('tarja-dev-bloqueado')) {
                            $('#txtInstanciaNome').text(res.instance);
                        }
                        if (res.api_url && !$('#inputApiUrl').hasClass('tarja-dev-bloqueado')) {
                            $('#txtApiUrl').text(res.api_url);
                        }
                    } else {
                        window.foco7Notify.error(res.message || 'Erro ao salvar configurações.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao Salvar',
                            text: res.message || 'Ocorreu um erro ao gravar as alterações.',
                            confirmButtonColor: '#d90000'
                        });
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    var msg = 'Erro ao processar o salvamento das configurações.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    window.foco7Notify.error(msg);
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no Salvamento',
                        text: msg,
                        confirmButtonColor: '#d90000'
                    });
                }
            });
        });
    });
</script>
@endsection

