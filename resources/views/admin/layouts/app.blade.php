<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel Administrativo') | Foco 7</title>
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
    <!-- Summernote BS5 CSS (Global WYSIWYG) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css">

    <!-- Customização Foco 7 para AdminLTE 4 -->
    <link rel="stylesheet" href="{{ asset('css/admin-foco7.css') }}">

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    <div class="app-wrapper">

        <!-- NAVBAR SUPERIOR -->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="{{ route('site.home') }}" target="_blank" class="nav-link">
                            <i class="fas fa-external-link-alt text-danger me-1"></i> Visualizar Landing Page
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <!-- Alternador Dark / Light Mode -->
                    <li class="nav-item">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="adminThemeToggle" title="Alternar Modo Escuro / Claro">
                            <i class="fas fa-moon" id="adminThemeIcon"></i>
                        </button>
                    </li>

                    <!-- Perfil do Administrador -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            @if(!empty(auth()->user()->avatar))
                                <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="rounded-circle border border-danger" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name ?? 'Administrador' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><span class="dropdown-item-text small text-secondary">{{ auth()->user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(auth()->user()->role === 'admin')
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.perfil.*') ? 'active' : '' }}" href="{{ route('admin.perfil.index') }}">
                                        <i class="fas fa-user-circle me-2 text-danger"></i> Meu Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}" href="{{ route('admin.configuracoes.index') }}">
                                        <i class="fas fa-sliders-h me-2 text-danger"></i> Configurações & SMTP
                                    </a>
                                </li>
                            @else
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
                            @endif
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

        <!-- SIDEBAR COM TREEVIEW E PALETA FOCO 7 (ADMINLTE 4) -->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand d-flex align-items-center justify-content-start px-3 py-3 border-bottom border-secondary">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('cliente.dashboard') }}" class="brand-link text-decoration-none d-flex align-items-center gap-2 w-100">
                    <img src="{{ asset('img/logo-foco7.png') }}" alt="Foco 7" height="34" class="brand-image">
                    <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : 'bg-primary' }} ms-auto small">
                        {{ auth()->user()->role === 'admin' ? 'ADMIN' : 'CLIENTE' }}
                    </span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Gestão do Portal & Serviços</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.cms.index') }}" class="nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-laptop-code"></i>
                                    <p>CMS & Seções do Site</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.estetica.index') }}" class="nav-link {{ request()->routeIs('admin.estetica.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-car-side text-warning"></i>
                                    <p>Estética Automotiva</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.planos.index') }}" class="nav-link {{ request()->routeIs('admin.planos.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tags"></i>
                                    <p>Planos & Preços</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.agendamentos.index') }}" class="nav-link {{ request()->routeIs('admin.agendamentos.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Agenda & Ordens</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-search"></i>
                                    <p>SEO & Meta Tags</p>
                                </a>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Clientes & Financeiro</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users text-info"></i>
                                    <p>Base de Clientes</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.faturas.index') }}" class="nav-link {{ request()->routeIs('admin.faturas.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice-dollar text-success"></i>
                                    <p>Faturas & Mensalidades</p>
                                </a>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Sistema & Operações</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.gateways.index') }}" class="nav-link {{ request()->routeIs('admin.gateways.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-credit-card text-success"></i>
                                    <p>Gateways &amp; Carnê</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.emails.index') }}" class="nav-link {{ request()->routeIs('admin.emails.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-envelope-open-text text-danger"></i>
                                    <p>Templates de E-mails</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.cron.index') }}" class="nav-link {{ request()->routeIs('admin.cron.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-clock text-info"></i>
                                    <p>Central de Cron Jobs</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.configuracoes.index') }}" class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-sliders-h text-warning"></i>
                                    <p>Configurações &amp; SMTP</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.manutencao.index') }}" class="nav-link {{ request()->routeIs('admin.manutencao.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tools"></i>
                                    <p>Modo Manutenção</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.perfil.index') }}" class="nav-link {{ request()->routeIs('admin.perfil.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-circle"></i>
                                    <p>Meu Perfil</p>
                                </a>
                            </li>
                        @else
                            <!-- ITENS DA ÁREA DO CLIENTE (MESMO PADRÃO ADMINLTE 4) -->
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
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="app-main p-4">
            <div class="app-content-header mb-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold">@yield('page_title', 'Painel Administrativo')</h3>
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

        <!-- RODAPÉ DO PAINEL -->
        <footer class="app-footer border-top py-3 text-center small text-secondary">
            &copy; {{ date('Y') }} Foco 7 Rastreamento e Segurança. Painel Administrativo de Alta Performance.
        </footer>

    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Summernote BS5 (0.8.20) & Tradução pt-BR -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/lang/summernote-pt-BR.min.js"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>
    <script src="{{ asset('js/global-dropzone.js') }}"></script>
    <script src="{{ asset('js/global-summernote.js') }}"></script>

    <script>
        // Alternador Dark / Light Mode no Admin
        $(function () {
            var theme = localStorage.getItem('foco7_admin_theme') || 'dark';

            function applyAdminTheme(t) {
                if (t === 'dark') {
                    $('html, body').attr('data-bs-theme', 'dark').addClass('dark-mode');
                    $('#adminThemeIcon').removeClass('fa-sun').addClass('fa-moon');
                } else {
                    $('html, body').attr('data-bs-theme', 'light').removeClass('dark-mode');
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
