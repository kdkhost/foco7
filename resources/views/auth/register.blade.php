<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seo = \App\Models\SeoConfig::paraPagina('cadastro');
        $siteNome = \App\Models\CmsSetting::get('site_nome', 'Foco 7 Rastreamento & Segurança');
        $favicon = \App\Models\CmsSetting::get('site_favicon', '/img/favicon.png');
        $ogImgUrl = !empty($seo->og_image) ? asset(ltrim($seo->og_image, '/')) : asset('img/og-foco7.jpg');
        $canonicalUrl = $seo->canonical_url ?: route('register');
    @endphp

    <!-- SEO Meta Tags Completo (Controlado pelo Painel Admin) -->
    <title>{{ $seo->meta_title ?? 'Criar Conta & Cadastro de Assinante | Foco 7 Rastreamento' }}</title>
    <meta name="description" content="{{ $seo->meta_description ?? 'Cadastre-se na Foco 7 e blinde seu veículo com a melhor tecnologia de rastreamento 24h e equipe de ronda tática.' }}">
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
        body.light-theme .register-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #0f172a;
        }
        body.light-theme .register-card .form-label {
            color: #0f172a !important;
        }
        body.light-theme .form-control {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        body.light-theme .input-group-text,
        body.light-theme .btn-toggle-password {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }
        body.light-theme .theme-toggle-btn {
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .register-card {
            max-width: 480px;
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

    <div class="register-card">
        <!-- Alternador Dark / Light Mode -->
        <button type="button" class="theme-toggle-btn" id="themeToggle" title="Alternar Modo Escuro / Claro">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <!-- Logomarca Oficial Dinâmica -->
        <div class="text-center mb-4">
            <a href="{{ route('site.home') }}">
                <img id="registerBrandLogo"
                     src="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-dark="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}"
                     data-logo-light="{{ asset(\App\Models\CmsSetting::getLogo('claro')) }}"
                     alt="Foco 7" height="64" class="mb-2">
            </a>
            <h5 class="fw-bold mb-1">Crie sua Conta</h5>
            <p class="small text-secondary mb-0">Cadastre-se para gerenciar seus veículos, rastreadores e faturas.</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" id="formCadastro">
            @csrf
            <!-- Honeypot invisível contra bots de força bruta -->
            <div class="d-none" aria-hidden="true">
                <input type="text" name="_hp_security_check" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Nome Completo</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-user"></i></span>
                    <input type="text" name="name" id="name" class="form-control bg-dark text-white border-secondary" required placeholder="Seu Nome Completo" autocomplete="name">
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">WhatsApp / Telefone</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fab fa-whatsapp"></i></span>
                        <input type="text" name="telefone" id="telefone" class="form-control bg-dark text-white border-secondary" required placeholder="(91) 99999-9999" autocomplete="tel">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required placeholder="seu@email.com" autocomplete="email">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Senha de Acesso</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-dark text-white border-secondary" required minlength="6" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                    <button class="btn btn-outline-secondary border-secondary btn-toggle-password" type="button" data-target="#password" title="Visualizar / Ocultar Senha" tabindex="-1">
                        <i class="fas fa-eye text-secondary"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Confirmar Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-check-double"></i></span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-dark text-white border-secondary" required minlength="6" placeholder="Repita sua senha" autocomplete="new-password">
                    <button class="btn btn-outline-secondary border-secondary btn-toggle-password" type="button" data-target="#password_confirmation" title="Visualizar / Ocultar Senha" tabindex="-1">
                        <i class="fas fa-eye text-secondary"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-foco" id="btnSubmitCadastro">
                <i class="fas fa-user-plus me-1"></i> FINALIZAR CADASTRO
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary">
            <span class="small text-secondary">Já possui uma conta? </span>
            <a href="{{ route('login') }}" class="small text-danger fw-bold text-decoration-none">Fazer Login</a>
        </div>
    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/global-ajax.js') }}"></script>

    <script>
        $(function () {
            // Sincronização de Tema e Logo Dinâmica
            var currentTheme = localStorage.getItem('foco7_theme') || 'dark';

            function applyTheme(theme) {
                var $logo = $('#registerBrandLogo');
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

            $('#formCadastro').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnSubmitCadastro');
                var origText = $btn.html();

                if ($('#password').val() !== $('#password_confirmation').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Senhas Não Coincidem',
                        text: 'A senha e a confirmação de senha digitadas são diferentes.',
                        confirmButtonColor: '#d90000',
                        confirmButtonText: 'Corrigir'
                    });
                    return;
                }

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Cadastrando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(origText);
                        Swal.fire({
                            icon: 'success',
                            title: 'Conta Criada com Sucesso!',
                            text: res.message || 'Seja bem-vindo à Foco 7!',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Acessar Área do Cliente'
                        }).then(function () {
                            window.location.href = res.redirect || '{{ route("cliente.dashboard") }}';
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = 'Falha ao processar o cadastro.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Atenção',
                            html: msg,
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
