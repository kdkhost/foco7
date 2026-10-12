<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Foco 7 | Site em Manutenção' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --cor-logo: #d90000;
            --cor-destaque: #ff2a2a;
        }
        body {
            background-color: #080a0f;
            background-image: url('{{ asset(!empty($bg_imagem) ? $bg_imagem : "/img/manutencao-bg.jpg") }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(18, 23, 34, 0.72) 0%, rgba(8, 10, 15, 0.94) 85%);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 1;
        }
        .manutencao-card {
            position: relative;
            z-index: 2;
            max-width: 740px;
            width: 100%;
            background: rgba(18, 23, 34, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 24px;
            padding: 44px 34px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85), 0 0 35px rgba(217, 0, 0, 0.18);
        }
        .countdown-box {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 16px;
            min-width: 95px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        }
        .countdown-num {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--cor-destaque);
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .countdown-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-top: 6px;
        }
        .btn-whatsapp-manutencao {
            background: #25d366;
            color: #ffffff;
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 30px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
        }
        .btn-whatsapp-manutencao:hover {
            background: #20bd5a;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.6);
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>

    <div class="manutencao-card">
        @if(!empty($is_preview))
        <div class="alert alert-warning py-2 px-3 mb-4 rounded-pill d-inline-flex align-items-center gap-2 small fw-bold shadow-sm">
            <i class="fas fa-eye text-warning fs-6"></i>
            <span>MODO PRÉ-VISUALIZAÇÃO DE ADMINISTRADOR (Visitantes veem exatamente esta tela)</span>
        </div>
        @endif

        <!-- Logomarca Oficial Transparente -->
        <div class="mb-4">
            <img src="{{ asset($logo ?? '/img/logo-foco7.png') }}" alt="Foco 7" height="68" class="mb-2">
        </div>

        <span class="badge bg-danger px-3 py-2 text-uppercase mb-3 letter-spacing-1">
            <i class="fas fa-tools me-1"></i> Manutenção Programada
        </span>

        <h2 class="fw-bold mb-3">{{ $titulo ?? 'SISTEMA EM ATUALIZAÇÃO' }}</h2>

        <p class="text-secondary fs-6 mb-4 leading-relaxed">
            {{ $mensagem ?? 'Estamos aplicando melhorias de alta performance em nossos servidores para garantir a máxima segurança do seu rastreamento.' }}
        </p>

        <!-- Contador Regressivo Dinâmico -->
        @if(!empty($previsao))
        <div class="d-flex justify-content-center gap-3 my-4 flex-wrap" id="countdownWrapper" data-target="{{ $previsao }}">
            <div class="countdown-box">
                <div class="countdown-num" id="cd-dias">00</div>
                <div class="countdown-label">Dias</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-num" id="cd-horas">00</div>
                <div class="countdown-label">Horas</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-num" id="cd-minutos">00</div>
                <div class="countdown-label">Minutos</div>
            </div>
            <div class="countdown-box">
                <div class="countdown-num" id="cd-segundos">00</div>
                <div class="countdown-label">Segundos</div>
            </div>
        </div>
        @endif

        <!-- Botão WhatsApp e Canais de Plantão 24h -->
        <div class="mt-4 pt-3 border-top border-secondary">
            <p class="text-secondary small mb-3">Nossa Central de Monitoramento e Plantão 24h continuam ativos sem interrupções:</p>

            <div class="d-flex justify-content-center gap-3 flex-wrap align-items-center mb-4">
                @if($mostrar_whatsapp && !empty($whatsapp))
                <a href="https://wa.me/{{ $whatsapp }}?text=Ol%C3%A1,%20preciso%20de%20atendimento%20de%20plant%C3%A3o%20Foco%207" target="_blank" class="btn-whatsapp-manutencao">
                    <i class="fab fa-whatsapp fs-5"></i> Plantão no WhatsApp
                </a>
                @endif

                @if(!empty($telefone))
                <a href="tel:{{ preg_replace('/\D/', '', $telefone) }}" class="btn btn-outline-light rounded-pill px-4 py-2">
                    <i class="fas fa-phone-alt me-1 text-danger"></i> {{ $telefone }}
                </a>
                @endif
            </div>

            <!-- Redes Sociais -->
            @if($mostrar_redes)
            <div class="d-flex justify-content-center gap-3 mt-3">
                @if(!empty($instagram))
                <a href="{{ $instagram }}" target="_blank" class="text-secondary fs-5" title="Instagram"><i class="fab fa-instagram"></i></a>
                @endif
                @if(!empty($facebook))
                <a href="{{ $facebook }}" target="_blank" class="text-secondary fs-5" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if(!empty($youtube))
                <a href="{{ $youtube }}" target="_blank" class="text-secondary fs-5" title="YouTube"><i class="fab fa-youtube"></i></a>
                @endif
            </div>
            @endif
        </div>

        <div class="mt-4 pt-3 text-secondary small d-flex justify-content-between align-items-center">
            <span>Seu IP: <code>{{ $client_ip }}</code></span>
            <a href="javascript:void(0)" onclick="abrirBypassModal()" class="text-secondary text-decoration-none">
                <i class="fas fa-key me-1"></i> Acesso Técnico
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Contador regressivo
        var elWrapper = document.getElementById('countdownWrapper');
        if (elWrapper) {
            var targetDate = new Date(elWrapper.getAttribute('data-target')).getTime();

            var timer = setInterval(function () {
                var now = new Date().getTime();
                var distance = targetDate - now;

                if (distance < 0) {
                    clearInterval(timer);
                    document.getElementById('cd-dias').innerText = '00';
                    document.getElementById('cd-horas').innerText = '00';
                    document.getElementById('cd-minutos').innerText = '00';
                    document.getElementById('cd-segundos').innerText = '00';
                    return;
                }

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('cd-dias').innerText = days < 10 ? '0' + days : days;
                document.getElementById('cd-horas').innerText = hours < 10 ? '0' + hours : hours;
                document.getElementById('cd-minutos').innerText = minutes < 10 ? '0' + minutes : minutes;
                document.getElementById('cd-segundos').innerText = seconds < 10 ? '0' + seconds : seconds;
            }, 1000);
        }

        // Modal de Bypass com SweetAlert2 (Sem alert() nativo)
        function abrirBypassModal() {
            Swal.fire({
                title: 'Acesso Técnico de Desenvolvimento',
                text: 'Insira o token de bypass para liberar a visualização prévia neste dispositivo:',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Token de liberação'
                },
                showCancelButton: true,
                confirmButtonText: 'Autorizar Dispositivo',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d90000',
                showLoaderOnConfirm: true,
                preConfirm: (token) => {
                    if (!token) {
                        Swal.showValidationMessage('Por favor, informe o token.');
                        return false;
                    }
                    window.location.href = '/manutencao/bypass/' + encodeURIComponent(token);
                }
            });
        }
    </script>
</body>
</html>
