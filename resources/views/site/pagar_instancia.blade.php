<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento de Mensalidade • Instância WhatsApp {{ $instancia['nome'] ?? '' }} - FOCO 7</title>
    <meta name="description" content="Checkout direto e transparente para renovação da instância dedicada WhatsApp Cloud.">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Google Fonts & Bootstrap 5 & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --foco-red: #d90000;
            --foco-red-hover: #b80000;
            --foco-green: #25d366;
            --foco-dark: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0b1120 0%, #0f172a 50%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px 10px;
        }

        .checkout-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .qr-wrapper {
            background: #ffffff;
            border-radius: 20px;
            padding: 16px;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .qr-wrapper img {
            width: 220px;
            height: 220px;
            object-fit: contain;
            display: block;
        }

        .btn-pix-copy {
            background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
            color: #000;
            font-weight: 700;
            border: none;
            padding: 14px 24px;
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .btn-pix-copy:hover {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(234, 179, 8, 0.3);
        }

        .pulse-radar {
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: #22c55e;
            border-radius: 50%;
            position: relative;
        }

        .pulse-radar::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            width: 20px;
            height: 20px;
            border: 2px solid #22c55e;
            border-radius: 50%;
            animation: radar 1.5s infinite linear;
        }

        @keyframes radar {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .font-mono {
            font-family: 'Roboto Mono', monospace;
        }

        .success-box {
            display: none;
            animation: fadeInScale 0.5s ease-out forwards;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="container my-auto">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">

            <!-- LOGO & CABEÇALHO -->
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <img src="{{ asset('img/logo.png') }}" alt="FOCO 7" class="img-fluid mb-2" style="max-height: 48px;" onerror="this.style.display='none'">
                    <h5 class="fw-bold text-white mb-0">FOCO 7 <span class="text-danger">•</span> WhatsApp Cloud</h5>
                </a>
                <p class="text-secondary small mt-1">Checkout Transparente via Mercado Pago</p>
            </div>

            <!-- CARD DE PAGAMENTO -->
            <div class="checkout-card p-4 p-sm-5" id="boxCheckout">
                
                <!-- HEADER DO CARD -->
                <div class="text-center mb-4">
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-1 mb-2">
                        <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 px-3 py-1 rounded-pill small">
                            <i class="fas fa-server me-1"></i> Instância Dedicada WhatsApp
                        </span>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-1 rounded-pill small">
                            <i class="fab fa-pix me-1"></i> Somente PIX Transparente
                        </span>
                        <span class="badge {{ ($cobranca['ambiente'] ?? 'producao') === 'sandbox' ? 'bg-warning text-dark' : 'bg-info bg-opacity-25 text-info border border-info border-opacity-50' }} px-3 py-1 rounded-pill small" id="badgeAmbientePublico">
                            <i class="fas {{ ($cobranca['ambiente'] ?? 'producao') === 'sandbox' ? 'fa-flask' : 'fa-bolt' }} me-1"></i>
                            {{ ($cobranca['ambiente'] ?? 'producao') === 'sandbox' ? 'Sandbox • Homologação' : 'Webhook Dinâmico Ativo' }}
                        </span>
                    </div>
                    <h4 class="fw-bold text-white mb-1">
                        {{ $instancia['nome'] ?? 'Minha Instância' }}
                    </h4>
                    <p class="text-secondary small mb-0">
                        Renovação de vigência por <strong class="text-white">+30 dias</strong> com ativação e retorno automático.
                    </p>
                </div>

                <!-- PREÇO EM DESTAQUE -->
                <div class="text-center py-2 px-3 rounded-4 bg-dark bg-opacity-50 border border-white border-opacity-10 mb-4">
                    <span class="text-secondary small d-block mb-1">Valor da Mensalidade (Exclusivo via PIX)</span>
                    <h2 class="display-6 fw-black text-danger mb-0 font-mono" id="txtValorPublico">
                        {{ $cobranca['valor_formatado'] ?? 'R$ 60,00' }}
                    </h2>
                    <small class="text-secondary">Fatura: <span class="font-mono text-light" id="txtFaturaPublico">{{ $cobranca['codigo_fatura'] ?? '--' }}</span> • REQ: <span class="font-mono text-info" id="txtReqTokenPublico">{{ strtoupper(substr($cobranca['req_token'] ?? 'DINAMICO', 0, 8)) }}</span></small>
                </div>

                <!-- QR CODE PIX MERCADO PAGO -->
                <div class="text-center mb-4">
                    <div class="qr-wrapper mb-2">
                        <img src="{{ $cobranca['pix_qr_url'] ?? '' }}" alt="QR Code PIX Mercado Pago" id="imgQrPix">
                    </div>
                    <div class="small text-secondary mb-1">
                        Abra o app do seu banco e escaneie o QR Code acima
                    </div>
                    <button type="button" class="btn btn-link btn-sm text-info text-decoration-none p-0 small" id="btnRegerarPixPublico" onclick="regerarPixPublico()">
                        <i class="fas fa-rotate me-1"></i> Gerar Novo QR Code PIX (Nova Requisição Dinâmica)
                    </button>
                </div>

                <!-- CÓDIGO PIX COPIA E COLA -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary d-flex justify-content-between">
                        <span>Código PIX Copia e Cola:</span>
                        <span class="text-warning"><i class="fab fa-pix"></i> Mercado Pago</span>
                    </label>
                    <div class="input-group">
                        <input type="text" id="inputPixCopiaCola" class="form-control bg-dark border-secondary text-white font-mono small" value="{{ $cobranca['pix_copia_cola'] ?? '' }}" readonly>
                        <button class="btn btn-warning fw-bold text-dark px-3" type="button" onclick="copiarPixDirect()" title="Copiar PIX">
                            <i class="fas fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                </div>

                <!-- BOTÃO DE AÇÃO FULL WIDTH -->
                <button type="button" class="btn btn-pix-copy w-100 py-3 mb-4 d-flex align-items-center justify-content-center gap-2 shadow" onclick="copiarPixDirect()">
                    <i class="fas fa-clone fs-5"></i>
                    <span class="fs-6">COPIAR CÓDIGO PIX COMPLETO</span>
                </button>

                <!-- RADAR DE STATUS AO VIVO -->
                <div class="p-3 rounded-4 bg-dark bg-opacity-40 border border-white border-opacity-10 text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                        <span class="pulse-radar"></span>
                        <span class="small fw-bold text-success" id="txtStatusRadar">Aguardando pagamento no seu banco...</span>
                    </div>
                    <small class="text-secondary d-block" style="font-size: 0.8rem;">
                        A aprovação é detectada automaticamente em segundos. A instância é reativada sem nenhuma interrupção!
                    </small>
                </div>

            </div>

            <!-- BOX DE SUCESSO APÓS APROVAÇÃO (COM RETORNO AUTOMÁTICO) -->
            <div class="checkout-card p-4 p-sm-5 text-center success-box" id="boxSucesso">
                <div class="p-4 bg-success bg-opacity-20 rounded-circle d-inline-block text-success mb-3 shadow">
                    <i class="fas fa-check-circle" style="font-size: 4rem;"></i>
                </div>
                <h3 class="fw-bold text-white mb-2">Pagamento PIX Confirmado!</h3>
                <p class="text-light mb-4" id="txtSucessoMensagemPublico">
                    O pagamento da instância <strong class="text-warning font-mono">{{ $instancia['nome'] ?? '' }}</strong> foi recebido e processado com sucesso pelo Mercado Pago.
                </p>

                <div class="p-3 rounded-4 bg-dark bg-opacity-60 border border-success border-opacity-30 text-start mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Instância:</span>
                        <strong class="text-white font-mono">{{ $instancia['nome'] ?? '' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Método de Pagamento:</span>
                        <span class="badge bg-success"><i class="fab fa-pix me-1"></i> PIX Transparente</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary small">Nova Vigência (+30 dias):</span>
                        <span class="badge bg-success" id="txtSucessoVigenciaPublico">+30 dias adicionados</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary small">Status do Serviço:</span>
                        <strong class="text-success"><i class="fas fa-wifi me-1"></i> Ativo &amp; Liberado</strong>
                    </div>
                </div>

                <div class="p-2 rounded-3 bg-success bg-opacity-25 border border-success border-opacity-50 text-light small mb-3">
                    <i class="fas fa-circle-notch fa-spin me-1"></i>
                    Retorno automático em <strong id="contadorRetornoPublico">5</strong>s...
                </div>

                <a href="{{ request()->query('return_url', url('/admin/whatsapp')) }}" id="btnRetornoPublico" class="btn btn-success fw-bold w-100 py-3 rounded-3 shadow">
                    <i class="fas fa-arrow-left me-2"></i> Voltar ao Painel Agora
                </a>
            </div>

            <!-- RODAPÉ DE SEGURANÇA -->
            <div class="text-center mt-4">
                <p class="text-secondary small mb-1">
                    <i class="fas fa-lock text-success me-1"></i> Ambiente Seguro com Criptografia SSL 256 bits
                </p>
                <small class="text-muted">© {{ date('Y') }} FOCO 7 TECNOLOGIA • Todos os direitos reservados.</small>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<script>
    var hashInstancia = '{{ $instancia["hash_pagamento"] ?? $instancia["nome"] }}';
    var urlStatus = '{{ route("whatsapp.status_pagamento_publico", $instancia["hash_pagamento"] ?? $instancia["nome"]) }}';
    var pollingAtivo = true;

    function copiarPixDirect() {
        var input = document.getElementById('inputPixCopiaCola');
        input.select();
        input.setSelectionRange(0, 99999);

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(sucessoCopia).catch(function() {
                document.execCommand('copy');
                sucessoCopia();
            });
        } else {
            document.execCommand('copy');
            sucessoCopia();
        }
    }

    function sucessoCopia() {
        Swal.fire({
            icon: 'success',
            title: 'Código PIX Copiado!',
            text: 'Abra o app do seu banco e escolha a opção PIX Copia e Cola para pagar.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#22c55e',
            timer: 3500,
            timerProgressBar: true
        });
    }

    // Gera nova requisição PIX com novo Webhook Dinâmico sem recarregar a página
    function regerarPixPublico() {
        var $btn = $('#btnRegerarPixPublico');
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Gerando nova requisição PIX...');

        $.ajax({
            url: window.location.pathname + '?forcar_nova=1&ajax=1',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res && res.success) {
                    $('#imgQrPix').attr('src', res.pix_qr_url);
                    $('#inputPixCopiaCola').val(res.pix_copia_cola);
                    if (res.codigo_fatura) $('#txtFaturaPublico').text(res.codigo_fatura);
                    if (res.req_token) $('#txtReqTokenPublico').text(res.req_token.substring(0, 8).toUpperCase());
                    Swal.fire({
                        icon: 'success',
                        title: 'Novo QR Code PIX Gerado!',
                        text: 'Nova requisição PIX e Webhook Dinâmico injetados com sucesso.',
                        confirmButtonColor: '#22c55e',
                        timer: 2500,
                        timerProgressBar: true
                    });
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(origHtml);
            }
        });
    }

    // Polling contínuo de status via AJAX a cada 3 segundos com Retorno Automático
    function checarStatusPagamento() {
        if (!pollingAtivo) return;

        $.ajax({
            url: urlStatus,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.pago === true) {
                    pollingAtivo = false;
                    if (res.novo_vencimento) {
                        $('#txtSucessoVigenciaPublico').text('Válida até ' + res.novo_vencimento + ' (+30 dias)');
                    }
                    if (res.message) {
                        $('#txtSucessoMensagemPublico').text(res.message);
                    }
                    $('#boxCheckout').fadeOut(300, function() {
                        $('#boxSucesso').fadeIn(400);
                        dispararConfetes();
                        iniciarContagemRetornoPublico(res.retorno_url);
                    });
                } else {
                    setTimeout(checarStatusPagamento, 3000);
                }
            },
            error: function() {
                // Tenta novamente após 5s se houver oscilação de rede
                setTimeout(checarStatusPagamento, 5000);
            }
        });
    }

    function iniciarContagemRetornoPublico(retornoUrl) {
        var destino = '{{ request()->query("return_url", "") }}' || retornoUrl || '{{ url("/admin/whatsapp") }}';
        $('#btnRetornoPublico').attr('href', destino);
        var seg = 5;
        $('#contadorRetornoPublico').text(seg);
        var timer = setInterval(function() {
            seg--;
            if (seg <= 0) {
                clearInterval(timer);
                window.location.href = destino;
            } else {
                $('#contadorRetornoPublico').text(seg);
            }
        }, 1000);
    }

    function dispararConfetes() {
        try {
            confetti({
                particleCount: 80,
                spread: 70,
                origin: { y: 0.6 }
            });
        } catch(e) {}
    }

    // Inicia verificação automática
    $(document).ready(function() {
        setTimeout(checarStatusPagamento, 2500);
    });
</script>

</body>
</html>

