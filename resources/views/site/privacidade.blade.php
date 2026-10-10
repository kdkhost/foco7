<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Termos de Uso e Política de Privacidade (LGPD)' }} | Foco 7</title>
    <meta name="description" content="Política de Privacidade e Termos de Uso da Foco 7 Rastreamento Veicular em estrita conformidade com a LGPD (Lei Federal nº 13.709/2018).">
    
    <link rel="icon" type="image/png" href="{{ asset($configs['favicon'] ?? '/img/favicon.png') }}">

    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS Oficial Foco 7 -->
    <link rel="stylesheet" href="{{ asset('css/site-foco7.css') }}?v={{ time() }}">

    <style>
        :root {
            --foco-red: {{ $cmsCorPrimaria ?? '#d90000' }};
            --foco-red-hover: {{ $cmsCorDestaque ?? '#b30000' }};
            --foco-red-light: {{ $cmsCorDestaque ?? '#ff2a2a' }};
            --bs-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
        }
        .privacidade-hero {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.92) 100%), url('{{ asset($configs['hero_bg_imagem'] ?? '/img/og-foco7.jpg') }}') center/cover no-repeat;
            padding: 140px 0 60px 0;
            color: #ffffff;
            position: relative;
        }
        .privacidade-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 45px;
            line-height: 1.8;
            color: #334155;
        }
        .privacidade-card h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 36px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--foco-red, #d90000);
            padding-left: 14px;
        }
        .privacidade-card h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #1e293b;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        .privacidade-card p, .privacidade-card li {
            font-size: 0.98rem;
            color: #475569;
        }
        .privacidade-card ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }
        .privacidade-card li {
            margin-bottom: 8px;
        }
        .badge-lgpd-selo {
            background: rgba(34, 197, 94, 0.12);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .dpo-contact-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        @media print {
            .foco-header-fixed, .foco-footer, .foco-whatsapp-float, .foco-btn-topo, .no-print {
                display: none !important;
            }
            .privacidade-hero {
                padding: 20px 0;
                background: #ffffff !important;
                color: #000000 !important;
            }
            .privacidade-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

    <!-- 1. CABEÇALHO FIXO GLOBAL DO SITE PÚBLICO -->
    <header class="foco-header-fixed" id="focoHeader">
        <!-- TOPBAR FIXO DE CONTATO E ÁREA DO CLIENTE -->
        <div class="foco-topbar d-none d-md-block">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <span><i class="fas fa-headset text-danger me-1"></i> {{ $configs['horario'] ?? 'Central 24 Horas' }}</span>
                    <span><i class="fas fa-phone-alt text-danger me-1"></i> {{ $configs['telefone'] ?? '(91) 8051-0736' }}</span>
                    <a href="https://wa.me/{{ $configs['whatsapp'] ?? '559180510736' }}">
                        <i class="fab fa-whatsapp text-success me-1"></i> {{ $configs['whatsapp_formatado'] ?? '(91) 8051-0736' }}
                    </a>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($configs['instagram']))
                        <a href="{{ $configs['instagram'] }}" target="_blank" title="Siga nosso Instagram" data-bs-toggle="tooltip" data-bs-placement="bottom"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if(!empty($configs['facebook']))
                        <a href="{{ $configs['facebook'] }}" target="_blank" title="Curta nossa página no Facebook" data-bs-toggle="tooltip" data-bs-placement="bottom"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    <a href="{{ route('login') }}" class="btn-cliente-topo">
                        <i class="fas fa-user-lock me-1"></i> Área do Cliente / Painel
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. NAVBAR OFICIAL FOCO 7 -->
        <nav class="navbar navbar-expand-lg foco-navbar">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('site.home') }}">
                    <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] ?? 'Foco 7' }}" height="38" class="logo-foco7" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
                </a>

                <!-- BOTÃO HAMBÚRGUER (APENAS MOBILE E TABLET < 992px) -->
                <button class="navbar-toggler d-lg-none btn-android-burger" type="button" id="btnOpenMenuDrawer" data-bs-toggle="offcanvas" data-bs-target="#menuDrawerAndroid" aria-controls="menuDrawerAndroid" aria-label="Abrir Menu">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- MENU HORIZONTAL DESKTOP -->
                <div class="collapse navbar-collapse d-none d-lg-flex" id="navDesktopFoco">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#inicio">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#riscos">Benefícios</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#sobre">Sobre Nós</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#planos">Planos</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#estetica"><i class="fas fa-spray-can-sparkles text-danger me-1"></i>Estética</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#ronda">Ronda 24h</a></li>
                        <li class="nav-item"><a class="nav-link active fw-bold text-danger" href="{{ route('site.privacidade') }}">Privacidade</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#contato">Contato</a></li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center mt-3 mt-lg-0">
                        <a href="{{ route('site.home') }}" class="btn btn-sm btn-outline-secondary d-none d-xl-inline-flex align-items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Voltar ao Site
                        </a>
                        <a href="{{ route('login') }}" class="btn-foco-login-nav d-none d-lg-inline-flex">
                            <i class="fas fa-user-lock"></i> Área do Cliente
                        </a>
                        <a href="https://foco7.plataforma.app.br/login" target="_blank" rel="noopener noreferrer" class="btn-foco-rastreamento btn-sm px-3 py-2 text-center justify-content-center" title="Acessar Plataforma de Rastreamento Online" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="fas fa-satellite-dish"></i> Rastreamento
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- MENU GAVETA DESLIZANTE NATIVO ANDROID (Mobile & Tablet) -->
    <div class="offcanvas offcanvas-start drawer-android" tabindex="-1" id="menuDrawerAndroid" aria-labelledby="menuDrawerAndroidLabel">
        <div class="offcanvas-header drawer-android-header">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] ?? 'Foco 7' }}" height="32" class="drawer-logo" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
            </div>
            <button type="button" class="btn-close-android" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body drawer-android-body">
            <ul class="nav flex-column drawer-nav-list mb-4">
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#inicio"><i class="fas fa-home me-2 text-danger"></i>Início</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#sobre"><i class="fas fa-shield-alt me-2 text-danger"></i>Sobre Nós</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#planos"><i class="fas fa-tags me-2 text-danger"></i>Planos & Preços</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#estetica"><i class="fas fa-spray-can-sparkles me-2 text-danger"></i>Estética Automotiva</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#ronda"><i class="fas fa-motorcycle me-2 text-danger"></i>Ronda Tática 24h</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('site.privacidade') }}"><i class="fas fa-user-shield me-2 text-danger"></i>Privacidade & LGPD</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('site.home') }}#contato"><i class="fas fa-envelope me-2 text-danger"></i>Contato</a></li>
            </ul>
            <div class="d-grid gap-2 pt-2 border-top">
                <a href="{{ route('login') }}" class="btn btn-outline-danger fw-bold py-2">
                    <i class="fas fa-user-lock me-1"></i> Área do Cliente
                </a>
                <a href="https://foco7.plataforma.app.br/login" target="_blank" rel="noopener noreferrer" class="btn-foco-rastreamento py-2 text-center justify-content-center fw-bold" title="Acessar Plataforma de Rastreamento Online">
                    <i class="fas fa-satellite-dish"></i> Plataforma de Rastreamento
                </a>
            </div>
        </div>
    </div>

    <!-- 3. HERO / CABEÇALHO DA PÁGINA DE PRIVACIDADE -->
    <section class="privacidade-hero">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <span class="badge-lgpd-selo">
                            <i class="fas fa-check-shield"></i> LGPD • LEI Nº 13.709/2018
                        </span>
                    </div>
                    <h1 class="display-5 fw-bold mb-3 text-white">Termos de Uso & Política de Privacidade</h1>
                    <p class="lead text-light mb-0" style="opacity: 0.9;">
                        Transparência, integridade e respeito absoluto aos dados dos nossos clientes, parceiros e visitantes.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end no-print">
                    <button type="button" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold me-2 mb-2" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
                    <a href="{{ route('site.home') }}" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold mb-2">
                        <i class="fas fa-arrow-left me-1"></i> Voltar ao Site
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. CONTEÚDO PRINCIPAL (LGPD & TERMOS) -->
    <main class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="privacidade-card">
                        
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                            <div>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">
                                    Vigência e Atualização: {{ date('d/m/Y') }}
                                </span>
                            </div>
                            <small class="text-secondary">
                                <i class="fas fa-lock text-success me-1"></i> Conexão Segura Criptografada SSL
                            </small>
                        </div>

                        @if(!empty($configs['politica_privacidade']))
                            <div class="conteudo-cms-privacidade">
                                {!! $configs['politica_privacidade'] !!}
                            </div>
                        @else
                            <!-- MINUTA JURÍDICA PADRÃO FOCO 7 LGPD -->
                            <p>
                                A <strong>Foco 7 Rastreamento e Segurança Eletrônica</strong> valoriza a sua privacidade e assume o compromisso público de resguardar todos os seus dados pessoais. Este documento explica detalhadamente como coletamos, tratamos, armazenamos e protegemos suas informações, em total conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (Lei Federal nº 13.709/2018 - LGPD)</strong>.
                            </p>

                            <h2>1. Identificação do Controlador</h2>
                            <p>
                                A empresa controladora responsável pelo tratamento dos seus dados pessoais é a <strong>Foco 7 Rastreamento e Serviços de Segurança</strong>, pessoa jurídica de direito privado, inscrita nos órgãos competentes, com sede de operações em Belém - PA.
                            </p>

                            <h2>2. Dados Coletados e Finalidade do Tratamento</h2>
                            <p>Coletamos exclusivamente as informações necessárias para prestar nossos serviços com máxima excelência e precisão operacional:</p>
                            <ul>
                                <li><strong>Dados Cadastrais:</strong> Nome completo, CPF/CNPJ, e-mail, telefone/WhatsApp e endereço residencial ou comercial para celebração de contrato, emissão de faturas e identificação do titular.</li>
                                <li><strong>Dados Veiculares:</strong> Placa, marca, modelo, ano e cor do veículo para vinculação do dispositivo rastreador, vistorias e agendamento de serviços técnicos ou de estética automotiva.</li>
                                <li><strong>Dados Telemáticos de Geolocalização:</strong> Coordenadas GPS em tempo real, telemetria de ignição, rotas, velocidades e cercas virtuais estritamente para o serviço contratado de segurança, alerta e pronta resposta em casos de roubo, furto ou emergência.</li>
                                <li><strong>Dados de Navegação (Cookies):</strong> Endereço IP, data e hora de acesso, tipo de dispositivo e preferências de navegação para aprimoramento de usabilidade e segurança do sistema contra acessos indevidos.</li>
                            </ul>

                            <h2>3. Bases Legais do Tratamento (Art. 7º da LGPD)</h2>
                            <p>Todas as operações de tratamento de dados realizadas pela Foco 7 fundamentam-se nas seguintes hipóteses legais:</p>
                            <ul>
                                <li><strong>Execução de Contrato (Inciso V):</strong> Viabilização dos serviços de rastreamento veicular, bloqueio remoto, agendamentos, emissão de mensalidades e assistência 24h.</li>
                                <li><strong>Proteção da Vida e Incolumidade Física (Inciso VII):</strong> Acionamento emergencial da Ronda Tática e socorro operacional ao cliente.</li>
                                <li><strong>Cumprimento de Obrigação Legal ou Regulatória (Inciso II):</strong> Guarda de registros de acesso nos termos do Marco Civil da Internet (Lei nº 12.965/2014) e emissão de notas fiscais.</li>
                                <li><strong>Legítimo Interesse do Controlador (Inciso IX):</strong> Prevenção contra fraudes, segurança da infraestrutura de TI e melhorias na prestação dos serviços.</li>
                            </ul>

                            <h2>4. Segurança e Criptografia da Informação</h2>
                            <p>
                                Adotamos padrões rígidos de governança da informação:
                            </p>
                            <ul>
                                <li>Comunicação 100% criptografada sob o protocolo <strong>HTTPS / TLS 1.3</strong> com certificados SSL válidos;</li>
                                <li>Armazenamento de senhas em formato Hash irreversível (Bcrypt com fator de custo elevado);</li>
                                <li>Firewall ativo no servidor, prevenção contra injeção SQL, proteção contra ataques de força bruta e cabeçalhos de segurança HTTP rigorosos (<code>X-Frame-Options</code>, <code>X-Content-Type-Options</code>);</li>
                                <li>Controle estrito de acesso físico e lógico aos servidores telemáticos.</li>
                            </ul>

                            <h2>5. Direitos do Titular dos Dados (Art. 18 da LGPD)</h2>
                            <p>
                                Você, na condição de titular dos dados pessoais, pode solicitar a qualquer momento:
                            </p>
                            <ul>
                                <li>Confirmação da existência de tratamento dos seus dados;</li>
                                <li>Acesso aos dados arquivados em sua conta ou contrato;</li>
                                <li>Correção de dados incompletos, inexatos ou desatualizados;</li>
                                <li>Anonimização, bloqueio ou eliminação de dados desnecessários;</li>
                                <li>Portabilidade dos dados para outro fornecedor de serviços;</li>
                                <li>Revogação do consentimento, observadas as restrições contratuais e regulatórias.</li>
                            </ul>

                            <h2>6. Política de Cookies</h2>
                            <p>
                                Utilizamos cookies estritamente necessários para manter sua sessão autenticada, registrar seu consentimento LGPD e prevenir requisições forjadas (CSRF). Não comercializamos nem compartilhamos seus cookies de navegação com terceiros para fins de publicidade invasiva.
                            </p>

                            <h2>7. Canal de Contato com o Encarregado de Dados (DPO)</h2>
                            <p>
                                Para exercer quaisquer dos seus direitos ou esclarecer dúvidas sobre esta Política de Privacidade, entre em contato direto com a nossa equipe de privacidade:
                            </p>
                            <div class="dpo-contact-box">
                                <h6 class="fw-bold text-danger mb-2"><i class="fas fa-envelope-open-text me-2"></i> Encarregado de Proteção de Dados (DPO) - Foco 7</h6>
                                <p class="mb-1 small"><strong>E-mail:</strong> <a href="mailto:{{ $configs['email'] ?? 'notificacoes@foco7.com.br' }}">{{ $configs['email'] ?? 'notificacoes@foco7.com.br' }}</a></p>
                                <p class="mb-1 small"><strong>WhatsApp Institucional:</strong> <a href="https://wa.me/{{ $configs['whatsapp'] ?? '559180510736' }}">{{ $configs['whatsapp_formatado'] ?? '(91) 8051-0736' }}</a></p>
                                <p class="mb-0 small"><strong>Horário de Atendimento:</strong> {{ $configs['horario'] ?? 'Segunda a Sexta, das 08h às 18h' }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 5. RODAPÉ INSTITUCIONAL IDÊNTICO AO SITE PÚBLICO -->
    <footer class="foco-footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <img src="{{ asset($configs['logo_fundo_claro'] ?? '/img/logo-foco7-dark.png') }}" alt="{{ $configs['nome'] ?? 'Foco 7' }}" height="44" class="mb-3 logo-foco7" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
                    <p class="mt-3 text-secondary small leading-relaxed">
                        Líder em rastreamento veicular inteligente, bloqueio remoto e ronda tática presencial. Mais de 10 anos protegendo famílias e frotas corporativas em todo o território nacional.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <span class="badge-footer-ssl"><i class="fas fa-lock text-success me-1"></i> SSL Blindado 256-bit</span>
                        <span class="badge-footer-ssl"><i class="fas fa-server text-danger me-1"></i> LiteSpeed 24h</span>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <h5>Navegação</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('site.home') }}#inicio">Início</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#riscos">Benefícios</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#sobre">Sobre Nós</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#planos">Planos & Preços</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#estetica" class="text-danger fw-bold"><i class="fas fa-sparkles me-1"></i> Estética Automotiva</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#ronda">Ronda Tática</a></li>
                        <li class="mb-2"><a href="{{ route('site.home') }}#faq">Dúvidas Frequentes</a></li>
                        <li class="mb-2"><a href="{{ route('site.privacidade') }}" class="text-danger fw-bold"><i class="fas fa-shield-alt me-1"></i> Termos & LGPD</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-6">
                    <h5>Atendimento</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-phone-alt text-danger me-2"></i> {{ $configs['telefone'] ?? '(91) 8051-0736' }}</li>
                        <li class="mb-2"><i class="fab fa-whatsapp text-success me-2"></i> {{ $configs['whatsapp_formatado'] ?? '(91) 8051-0736' }}</li>
                        <li class="mb-2"><i class="fas fa-envelope text-danger me-2"></i> {{ $configs['email'] ?? 'contato@foco7.com.br' }}</li>
                        <li class="mb-2"><i class="fas fa-clock text-danger me-2"></i> {{ $configs['horario'] ?? 'Central 24 Horas' }}</li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Área Segura</h5>
                    <p class="small text-secondary">Acesso exclusivo para clientes e operadores de frota.</p>
                    <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 py-2 mb-2 fw-semibold">
                        <i class="fas fa-user-lock me-1"></i> Acessar Painel / Área do Cliente
                    </a>
                </div>
            </div>
            <div class="border-top border-light-subtle mt-5 pt-4 text-center small text-secondary">
                &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Todos os direitos reservados. &bull; <a href="{{ route('site.privacidade') }}" class="text-secondary text-decoration-underline ms-1">Política de Privacidade & Termos (LGPD)</a>
            </div>
        </div>
    </footer>

    <!-- BOTÃO VOLTAR AO TOPO FLUTUANTE -->
    <button type="button" id="btnVoltarAoTopo" class="foco-btn-topo" aria-label="Voltar ao início" title="Voltar ao Topo" data-bs-toggle="tooltip" data-bs-placement="left">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- SCRIPTS BÁSICOS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script>
        // Botão Voltar ao Topo
        const btnTopo = document.getElementById('btnVoltarAoTopo');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                btnTopo.classList.add('visible');
            } else {
                btnTopo.classList.remove('visible');
            }
        });
        btnTopo.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
