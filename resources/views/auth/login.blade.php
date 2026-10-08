<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Foco 7 | Acesso Seguro ao Sistema</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
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
            color: #212529;
        }
        body.light-theme .login-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #212529;
        }
        body.light-theme .form-control {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .login-card {
            max-width: 440px;
            width: 100%;
            background: #121722;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 35px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            position: relative;
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

    <div class="login-card">
        <!-- Alternador Dark / Light Mode -->
        <button type="button" class="theme-toggle-btn" id="themeToggle" title="Alternar Modo Escuro / Claro">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <!-- Logomarca Oficial Transparente -->
        <div class="text-center mb-4">
            <a href="{{ route('site.home') }}">
                <img src="{{ asset('img/logo-foco7.png') }}" alt="Foco 7" height="68" class="mb-2">
            </a>
            <p class="small text-secondary mt-1 mb-0">Portal de Gestão & Área do Cliente</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="ajax-form">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">E-mail de Acesso</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required placeholder="seu@email.com">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control bg-dark text-white border-secondary" required placeholder="••••••••">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small" for="remember">Lembrar acesso</label>
                </div>
                <a href="#contato" class="small text-danger text-decoration-none">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn-foco">
                <i class="fas fa-sign-in-alt me-1"></i> ENTRAR NO SISTEMA
            </button>
        </form>

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

        // Alternância de Tema com LocalStorage
        $(function () {
            var currentTheme = localStorage.getItem('foco7_theme') || 'dark';

            function applyTheme(theme) {
                if (theme === 'light') {
                    $('body').addClass('light-theme');
                    $('#themeIcon').removeClass('fa-moon').addClass('fa-sun');
                    $('#logoSub').removeClass('text-white').addClass('text-dark');
                } else {
                    $('body').removeClass('light-theme');
                    $('#themeIcon').removeClass('fa-sun').addClass('fa-moon');
                    $('#logoSub').removeClass('text-dark').addClass('text-white');
                }
            }

            applyTheme(currentTheme);

            $('#themeToggle').on('click', function () {
                currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('foco7_theme', currentTheme);
                applyTheme(currentTheme);
            });
        });
    </script>
</body>
</html>
