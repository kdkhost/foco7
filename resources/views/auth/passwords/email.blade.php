<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seo = \App\Models\SeoConfig::paraPagina('reset');
        $siteNome = \App\Models\CmsSetting::get('site_nome', 'Foco 7 Rastreamento & Segurança');
        $favicon = \App\Models\CmsSetting::get('site_favicon', '/img/favicon.png');
        $ogImgUrl = !empty($seo->og_image) ? asset(ltrim($seo->og_image, '/')) : asset('img/og-foco7.jpg');
        $canonicalUrl = $seo->canonical_url ?: route('password.request');
    @endphp

    <!-- SEO Meta Tags Completo (Controlado pelo Painel Admin) -->
    <title>{{ $seo->meta_title ?? 'Recuperação de Senha & Acesso Seguro | Foco 7' }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? 'Esqueceu sua senha? Solicite a redefinição segura de suas credenciais para acessar o Painel Foco 7.' }}">
    @if(!empty($seo->meta_keywords))
    <meta name="keywords" content="{{ $seo->meta_keywords }}">
    @endif
    <meta name="robots" content="{{ $seo->robots ?? 'noindex, follow' }}">
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
        body.light-theme .reset-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #0f172a;
        }
        body.light-theme .reset-card .form-label {
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
        body.light-theme .theme-toggle-btn {
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .reset-card {
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
        .icon-shield-reset {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(217, 0, 0, 0.15);
            border: 1px solid rgba(217, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            color: var(--foco-red);
            font-size: 28px;
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
    </style>
</head>
<body>

    <div class="reset-card">
        <!-- Alternador Dark / Light Mode -->
        <button type="button" class="theme-toggle-btn" id="themeToggle" title="Alternar Modo Escuro / Claro">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <div class="text-center mb-4">
            <div class="icon-shield-reset">
                <i class="fas fa-key"></i>
            </div>
            <a href="{{ route('site.home') }}">
                <img id="emailBrandLogo"
                     src="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-dark="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-light="{{ asset(\App\Models\CmsSetting::getLogo('claro')) }}"
                     alt="Foco 7" height="42" class="mb-2">
            </a>
            <h5 class="fw-bold mb-1">Esqueceu sua senha?</h5>
            <p class="small text-secondary mb-0">Informe o e-mail cadastrado e enviaremos instruções com link seguro para você redefinir sua senha.</p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" id="formResetEmail">
            @csrf
            <!-- Honeypot invisível contra bots de força bruta -->
            <div class="d-none" aria-hidden="true">
                <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">E-mail Cadastrado</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required placeholder="seu@email.com" autofocus autocomplete="email">
                </div>
                <div class="form-text small text-secondary">Você receberá um link temporário com validade de 60 minutos.</div>
            </div>

            <button type="submit" class="btn-foco" id="btnEnviarLink">
                <i class="fas fa-paper-plane me-1"></i> ENVIAR LINK DE REDEFINIÇÃO
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary">
            <a href="{{ route('login') }}" class="small text-danger text-decoration-none fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Voltar para a Tela de Login
            </a>
        </div>
    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/global-ajax.js') }}"></script>

    <script>
        $(function () {
            // Sincronização do tema e da logo
            var currentTheme = localStorage.getItem('foco7_theme') || 'dark';

            function applyTheme(theme) {
                var $logo = $('#emailBrandLogo');
                if (theme === 'light') {
                    $('body').addClass('light-theme');
                    $('#themeIcon').removeClass('fa-moon').addClass('fa-sun');
                    var logoLight = $logo.data('logo-light');
                    if (logoLight) {
                        $logo.attr('src', logoLight);
                    }
                } else {
                    $('body').removeClass('light-theme');
                    $('#themeIcon').removeClass('fa-sun').addClass('fa-moon');
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

            $('#formResetEmail').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnEnviarLink');
                var origText = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(origText);
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitação Enviada!',
                            text: res.message || 'Verifique sua caixa de entrada para redefinir a senha.',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Entendido'
                        }).then(function () {
                            $form[0].reset();
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = 'Ocorreu um erro ao processar sua solicitação.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Atenção',
                            text: msg,
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Fechar'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
