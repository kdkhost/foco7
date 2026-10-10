<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Área do Cliente') | Foco 7</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

    <!-- Toastr & SweetAlert2 (Global) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Customização Foco 7 para AdminLTE 4 -->
    <link rel="stylesheet" href="{{ asset('css/admin-foco7.css') }}">

    <!-- Injeção Dinâmica da Paleta Oficial CMS Foco 7 -->
    <style>
        :root {
            --admin-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --admin-primary-hover: {{ $cmsCorDestaque ?? '#b80000' }};
            --admin-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
            --bs-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
            --bs-primary-text-emphasis: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-bg-subtle: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.12);
            --bs-primary-border-subtle: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.3);
            --bs-link-color: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-link-hover-color: {{ $cmsCorDestaque ?? '#b80000' }};
            --bs-focus-ring-color: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.25);
        }
        [data-bs-theme="dark"] {
            --bs-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
            --bs-link-color: {{ $cmsCorPrimaria ?? '#d90000' }};
        }
    </style>

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    <div class="app-wrapper">

        <!-- NAVBAR SUPERIOR DO ADMINLTE 4 -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" title="Recolher/Expandir Menu">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="{{ route('site.home') }}" target="_blank" class="nav-link">
                            <i class="fas fa-external-link-alt text-danger me-1"></i> Visualizar Site Oficial
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <!-- Alternador Dark / Light Mode (AdminLTE 4) -->
                    <li class="nav-item">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="adminThemeToggle" title="Alternar Modo Escuro / Claro">
                            <i class="fas fa-moon" id="adminThemeIcon"></i>
                        </button>
                    </li>

                    <!-- Perfil do Cliente -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            @if(!empty(auth()->user()->avatar))
                                <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="rounded-circle border border-danger" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                    {{ substr(auth()->user()->name ?? 'C', 0, 1) }}
                                </div>
                            @endif
                            <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name ?? 'Cliente' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text small text-secondary">{{ auth()->user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}" href="{{ route('cliente.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2 text-danger"></i> Painel do Assinante
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('cliente.faturas.*') ? 'active' : '' }}" href="{{ route('cliente.faturas.index') }}">
                                    <i class="fas fa-file-invoice-dollar me-2 text-danger"></i> Minhas Faturas
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="document.getElementById('formLogout').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Encerrar Sessão
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <form id="formLogout" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <!-- SIDEBAR DO ADMINLTE 4 (ÁREA DO CLIENTE) -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-3 border-bottom border-secondary">
                <a href="{{ route('cliente.dashboard') }}" class="brand-link text-decoration-none d-flex align-items-center gap-2 w-100">
                    <img src="{{ asset('img/logo-foco7.png') }}" alt="Foco 7" height="34" class="brand-image">
                    <span class="badge bg-primary ms-auto small">CLIENTE</span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('cliente.dashboard') }}" class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Painel do Assinante</p>
                            </a>
                        </li>

                        <li class="nav-header text-uppercase small text-secondary mt-3">Financeiro & Mensalidades</li>

                        <li class="nav-item">
                            <a href="{{ route('cliente.faturas.index') }}" class="nav-link {{ request()->routeIs('cliente.faturas.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Minhas Faturas</p>
                            </a>
                        </li>

                        <li class="nav-header text-uppercase small text-secondary mt-3">Atendimento & Suporte</li>

                        <li class="nav-item">
                            <a href="https://wa.me/{{ \App\Models\CmsSetting::get('contato_whatsapp', '559180510736') }}?text={{ urlencode('Olá, sou cliente Foco 7 e preciso de suporte técnico.') }}" target="_blank" class="nav-link text-success">
                                <i class="nav-icon fab fa-whatsapp"></i>
                                <p>Suporte WhatsApp 24h</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('site.home') }}" target="_blank" class="nav-link">
                                <i class="nav-icon fas fa-globe"></i>
                                <p>Site Oficial Foco 7</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL (ADMINLTE 4) -->
        <main class="app-main p-4">
            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold">@yield('page_title', 'Central do Assinante')</h3>
                            <p class="text-secondary small mb-0">@yield('page_description', '')</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                            @yield('page_actions')
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- RODAPÉ DO PAINEL (ADMINLTE 4) -->
        <footer class="app-footer border-top py-3 text-center small text-secondary">
            &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança Eletrônica. Painel do Assinante de Alta Performance.
        </footer>

    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>
    <script src="{{ asset('js/global-dropzone.js') }}"></script>

    <script>
        // Alternador Dark / Light Mode no Painel do Cliente (AdminLTE 4)
        $(function () {
            var theme = localStorage.getItem('foco7_admin_theme') || 'dark';

            function applyAdminTheme(t) {
                if (t === 'dark') {
                    $('body').addClass('dark-mode').attr('data-bs-theme', 'dark');
                    $('#adminThemeIcon').removeClass('fa-sun').addClass('fa-moon');
                } else {
                    $('body').removeClass('dark-mode').attr('data-bs-theme', 'light');
                    $('#adminThemeIcon').removeClass('fa-moon').addClass('fa-sun');
                }
            }

            applyAdminTheme(theme);

            $('#adminThemeToggle').on('click', function () {
                theme = theme === 'dark' ? 'light' : 'dark';
                localStorage.setItem('foco7_admin_theme', theme);
                applyAdminTheme(theme);
            });

            // Disparo automático de mensagens flash do Laravel com Toastr
            @if(session('success'))
                window.foco7Notify.success("{{ session('success') }}");
            @endif
            @if(session('error'))
                window.foco7Notify.error("{{ session('error') }}");
            @endif
            @if(session('warning'))
                window.foco7Notify.warning("{{ session('warning') }}");
            @endif
            @if(session('info'))
                window.foco7Notify.info("{{ session('info') }}");
            @endif
        });
    </script>

    @yield('scripts')
</body>
</html>
