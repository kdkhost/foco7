<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seo = \App\Models\SeoConfig::paraPagina('login');
        $siteNome = \App\Models\CmsSetting::get('site_nome', 'Foco 7 Rastreamento & Segurança');
        $favicon = \App\Models\CmsSetting::get('site_favicon', '/img/favicon.png');
        $ogImgUrl = !empty($seo->og_image) ? asset(ltrim($seo->og_image, '/')) : asset('img/og-foco7.jpg');
        $canonicalUrl = $seo->canonical_url ?: route('login');
    @endphp

    <!-- SEO Meta Tags Completo (Controlado pelo Painel Admin) -->
    <title>{{ $seo->meta_title ?? 'Login & Acesso Seguro ao Sistema | Foco 7 Rastreamento' }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? 'Acesse o Painel Foco 7 com segurança criptografada. Monitore seus veículos em tempo real e acesse suas faturas.' }}">
    @if(!empty($seo->meta_keywords))
    <meta name="keywords" content="{{ $seo->meta_keywords }}">
    @endif
    <meta name="robots" content="{{ $seo->robots ?? 'index, follow' }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Open Graph (WhatsApp, Facebook, LinkedIn) -->
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->og_title ?: $seo->meta_title }}">
    <meta property="og:description" content="{{ $seo->og_description ?: $seo->meta_description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteNome }}">
    <meta property="og:image" content="{{ $ogImgUrl }}">
    <meta property="og:image:secure_url" content="{{ $ogImgUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $seo->og_title ?: $seo->meta_title }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->og_title ?: $seo->meta_title }}">
    <meta name="twitter:description" content="{{ $seo->og_description ?: $seo->meta_description }}">
    <meta name="twitter:image" content="{{ $ogImgUrl }}">

    <link rel="icon" type="image/png" href="{{ asset(ltrim($favicon, '/')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Toastr & SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --foco-red: #d90000;
            --foco-red-hover: #b80000;
        }
        body {
            background-color: #080a0f;
            color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        body.light-theme {
            background-color: #f4f6f9;
            color: #0f172a;
        }
        body.light-theme .login-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #0f172a;
        }
        body.light-theme .login-card .form-label {
            color: #0f172a !important;
        }
        body.light-theme .form-control {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        body.light-theme .input-group-text {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }
        body.light-theme .link-esqueceu-senha {
            color: #c40000 !important;
        }
        body.light-theme .link-esqueceu-senha:hover {
            color: #990000 !important;
            text-decoration: underline !important;
        }
        .link-esqueceu-senha {
            color: #ff7676;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .link-esqueceu-senha:hover {
            color: #ffffff;
            text-decoration: underline;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 35px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            position: relative;
            color: #f8fafc;
        }
        .login-card .form-label {
            color: #f8fafc;
        }
        .btn-foco {
            background: var(--foco-red);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-foco:hover {
            background: var(--foco-red-hover);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(217, 0, 0, 0.4);
        }
        .theme-toggle-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: inherit;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .tooltip { font-size: 0.8125rem; pointer-events: none; z-index: 1090; }
        .tooltip-inner {
            background: #0f121a;
            color: #f8fafc;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow::before { border-left-color: #0f121a; }
        .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before { border-right-color: #0f121a; }
        .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow::before { border-top-color: #0f121a; }
        .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow::before { border-bottom-color: #0f121a; }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Alternador Dark / Light Mode -->
        <button type="button" class="theme-toggle-btn" id="themeToggle" title="Alternar Modo Escuro / Claro" data-bs-toggle="tooltip" data-bs-placement="left">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <!-- Logomarca Oficial Transparente Dinâmica -->
        <div class="text-center mb-4">
            <a href="{{ route('site.home') }}">
                <img id="loginBrandLogo"
                     src="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-dark="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-light="{{ asset(\App\Models\CmsSetting::getLogo('claro')) }}"
                     alt="Foco 7" height="68" class="mb-2">
            </a>
            <p class="small text-secondary mt-1 mb-0" id="loginSubText">Portal de Gestão &amp; Área do Cliente</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="ajax-form" id="formLogin">
            @csrf
            <!-- Honeypot invisível contra bots de força bruta -->
            <div class="d-none" aria-hidden="true">
                <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">E-mail de Acesso</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required placeholder="seu@email.com" autocomplete="username">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-dark text-white border-secondary" required placeholder="••••••••" autocomplete="current-password">
                    <button class="btn btn-outline-secondary border-secondary btn-toggle-password" type="button" data-target="#password" title="Visualizar / Ocultar Senha" tabindex="-1">
                        <i class="fas fa-eye text-secondary"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small" for="remember">Lembrar acesso</label>
                </div>
                <a href="{{ route('password.request') }}" class="small link-esqueceu-senha text-decoration-none">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn-foco" id="btnSubmitLogin">
                <i class="fas fa-sign-in-alt me-1"></i> ENTRAR NO SISTEMA
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="small text-secondary">Ainda não tem conta? </span>
            <a href="{{ route('register') }}" class="small text-danger fw-bold text-decoration-none">Cadastre-se aqui</a>
        </div>

        <!-- Atalhos Rápidos para Demonstração -->
        <div class="mt-4 pt-3 border-top border-secondary text-center small text-secondary">
            <span class="d-block mb-2">Acessos Rápidos de Demonstração:</span>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="preencherLogin('admin@foco7.com.br', 'foco7@2026')">
                    <i class="fas fa-user-shield"></i> Painel Admin
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="preencherLogin('cliente@foco7.com.br', 'cliente@2026')">
                    <i class="fas fa-user"></i> Área do Cliente
                </button>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('site.home') }}" class="small text-secondary text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Voltar para a Landing Page
            </a>
        </div>
    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>

    <script>
        function preencherLogin(email, senha) {
            $('#email').val(email);
            $('#password').val(senha);
            window.foco7Notify.info('Credenciais preenchidas!');
        }

        // Alternar visualização da senha
        $(document).on('click', '.btn-toggle-password', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            var $input = $(target);
            var $icon = $(this).find('i');
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash text-danger');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('fa-eye-slash text-danger').addClass('fa-eye text-secondary');
            }
        });

        // Alternância de Tema com LocalStorage e sincronização imediata da Logo
        $(function () {
            var currentTheme = localStorage.getItem('foco7_theme') || 'dark';

            function applyTheme(theme) {
                var $logo = $('#loginBrandLogo');
                if (theme === 'light') {
                    $('body').addClass('light-theme');
                    $('#themeIcon').removeClass('fa-moon').addClass('fa-sun');
                    $('#loginSubText').removeClass('text-white').addClass('text-secondary');
                    var logoLight = $logo.data('logo-light');
                    if (logoLight) {
                        $logo.attr('src', logoLight);
                    }
                } else {
                    $('body').removeClass('light-theme');
                    $('#themeIcon').removeClass('fa-sun').addClass('fa-moon');
                    $('#loginSubText').removeClass('text-dark').addClass('text-secondary');
                    var logoDark = $logo.data('logo-dark');
                    if (logoDark) {
                        $logo.attr('src', logoDark);
                    }
                }
            }

            applyTheme(currentTheme);

            $('#themeToggle').on('click', function () {
                currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('foco7_theme', currentTheme);
                applyTheme(currentTheme);
            });

            @if(request('motivo') === 'inatividade' || session('warning'))
            Swal.fire({
                icon: 'info',
                title: 'Sessão Expirada por Inatividade',
                text: "{{ session('warning') ?: 'Sua sessão foi desconectada por inatividade para sua segurança. Faça login novamente para continuar.' }}",
                confirmButtonColor: '#d90000',
                confirmButtonText: '<i class="fas fa-lock-open me-1"></i> Fazer Login Novamente'
            });
            @endif
        });
    </script>
</body>
</html>
