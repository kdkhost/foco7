<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ops! Ocorreu um erro') | Foco 7 Rastreamento</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --foco-red: #d90000;
            --foco-red-hover: #b30000;
            --foco-dark: #0a0c10;
            --foco-card: rgba(18, 22, 30, 0.85);
            --foco-border: rgba(255, 255, 255, 0.08);
            --foco-muted: #8e9aa8;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--foco-dark);
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(217, 0, 0, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(217, 0, 0, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(10, 12, 16, 0.95) 0%, rgba(5, 6, 8, 1) 100%);
            min-height: 100vh;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }

        .error-container {
            max-width: 620px;
            width: 100%;
            background: var(--foco-card);
            border: 1px solid var(--foco-border);
            border-radius: 24px;
            padding: 48px 36px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(217, 0, 0, 0.08);
            backdrop-filter: blur(16px);
            position: relative;
            z-index: 2;
            animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(217, 0, 0, 0.12);
            border: 1px solid rgba(217, 0, 0, 0.3);
            color: #ff4d4d;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .error-code {
            font-size: clamp(5rem, 15vw, 8.5rem);
            font-weight: 900;
            line-height: 0.9;
            background: linear-gradient(135deg, #ffffff 30%, #ff4d4d 70%, var(--foco-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -2px;
            text-shadow: 0 10px 30px rgba(217, 0, 0, 0.3);
        }

        .error-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(217, 0, 0, 0.15);
            border: 1px solid rgba(217, 0, 0, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #ff3333;
            margin-bottom: 20px;
            animation: pulseGlow 2.5s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(217, 0, 0, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 25px rgba(217, 0, 0, 0.3);
            }
        }

        .error-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .error-desc {
            color: var(--foco-muted);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-foco {
            background: linear-gradient(135deg, var(--foco-red) 0%, #a60000 100%);
            color: #ffffff;
            border: none;
            padding: 14px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 10px 25px rgba(217, 0, 0, 0.35);
        }

        .btn-foco:hover {
            background: linear-gradient(135deg, #ff1a1a 0%, var(--foco-red) 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(217, 0, 0, 0.5);
        }

        .btn-foco-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border: 1px solid var(--foco-border);
            padding: 14px 24px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-foco-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .actions-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .footer-note {
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid var(--foco-border);
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="brand-badge">
            <i class="fa-solid fa-satellite-dish"></i> Foco 7 Rastreamento
        </div>

        <div>
            @yield('icon')
        </div>

        <div class="error-code">
            @yield('code', '500')
        </div>

        <h1 class="error-title">
            @yield('title', 'Erro Inesperado')
        </h1>

        <p class="error-desc">
            @yield('message', 'Desculpe, ocorreu um imprevisto durante o processamento. Nossa equipe técnica foi notificada.')
        </p>

        <div class="actions-group">
            <a href="{{ url('/') }}" class="btn-foco">
                <i class="fa-solid fa-house"></i> Ir para o Início
            </a>
            
            <a href="https://wa.me/5598988582020?text=Olá,%20encontrei%20um%20erro%20no%20sistema%20Foco%207" target="_blank" class="btn-foco-secondary">
                <i class="fa-brands fa-whatsapp text-success"></i> Plantão de Suporte
            </a>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} Foco 7 Tecnologia e Rastreamento. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
