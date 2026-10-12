<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Restrito por Localização | Foco 7 Rastreamento</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --foco-red: #d90000;
            --foco-dark: #080a0f;
            --foco-card: #111827;
        }
        body {
            background-color: var(--foco-dark);
            color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .geoblock-card {
            max-width: 580px;
            width: 100%;
            background: var(--foco-card);
            border: 1px solid rgba(217, 0, 0, 0.35);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .geoblock-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #d90000, #ff4d4d, #d90000);
        }
        .icon-globe-lock {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(217, 0, 0, 0.12);
            border: 2px solid rgba(217, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: #ff4d4d;
            font-size: 36px;
        }
        .badge-regiao {
            background: rgba(217, 0, 0, 0.2);
            color: #ff7676;
            border: 1px solid rgba(217, 0, 0, 0.4);
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .info-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
            text-align: left;
        }
        .btn-whatsapp {
            background-color: #25d366;
            color: #ffffff;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 24px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-whatsapp:hover {
            background-color: #1ebc59;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.35);
        }
    </style>
</head>
<body>

    <div class="geoblock-card">
        <div class="icon-globe-lock">
            <i class="fas fa-globe-americas"></i>
        </div>

        <span class="badge-regiao">
            <i class="fas fa-shield-alt me-1"></i> Proteção Geográfica Ativa (Apenas Américas)
        </span>

        <h3 class="fw-bold mb-2">Acesso Restrito por Localização</h3>
        <p class="text-secondary small mb-3">
            Por diretrizes de segurança cibernética e mitigação de ameaças, o acesso a esta plataforma foi restrito exclusivamente a visitantes originários do continente americano.
        </p>

        <div class="info-box">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Origem Detectada:</span>
                <span class="fw-bold text-danger">{{ $geo['pais_nome'] ?? 'País fora das Américas' }} ({{ $geo['pais_codigo'] ?? '--' }})</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Endereço IP:</span>
                <span class="fw-semibold font-monospace small text-white">{{ $geo['ip'] ?? request()->ip() }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-secondary small">Status de Liberação:</span>
                <span class="badge bg-danger">BLOQUEADO POR POLÍTICA REGIONAL</span>
            </div>
        </div>

        <p class="text-secondary small mb-4">
            Se você é cliente ou colaborador da <strong>Foco 7</strong> em viagem ou utilizando rede VPN e necessita de acesso imediato, entre em contato com nossa Central 24h.
        </p>

        <div>
            <a href="https://wa.me/{{ \App\Models\CmsSetting::get('contato_whatsapp', '559180510736') }}?text={{ urlencode('Olá, estou tendo bloqueio geográfico ao acessar a Foco 7. Meu IP é: ' . request()->ip()) }}" target="_blank" class="btn-whatsapp">
                <i class="fab fa-whatsapp fs-5"></i> Falar com Suporte no WhatsApp
            </a>
        </div>
    </div>

</body>
</html>
