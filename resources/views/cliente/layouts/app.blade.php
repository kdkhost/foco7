<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Área do Cliente') | Foco 7</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <script>
        (function() {
            var t = localStorage.getItem('foco7_admin_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

    <!-- Toastr & SweetAlert2 (Global) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Customização Foco 7 para AdminLTE 4 -->
    <link rel="stylesheet" href="{{ asset('css/admin-foco7.css') }}?v={{ file_exists(public_path('css/admin-foco7.css')) ? filemtime(public_path('css/admin-foco7.css')) : time() }}">

    <!-- Injeção Dinâmica da Paleta da Empresa para Marcações e Detalhes -->
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
            --bs-focus-ring-color: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.25);
        }
        [data-bs-theme="dark"], body.dark-mode {
            --bs-primary: {{ $cmsCorPrimaria ?? '#d90000' }};
            --bs-primary-rgb: {{ $cmsCorPrimariaRgb ?? '217, 0, 0' }};
            --bs-primary-text-emphasis: {{ $cmsCorDestaque ?? '#ff7676' }};
            --bs-primary-bg-subtle: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.18);
            --bs-primary-border-subtle: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.35);
            --bs-focus-ring-color: rgba({{ $cmsCorPrimariaRgb ?? '217, 0, 0' }}, 0.3);
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
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" title="Recolher / Expandir Menu" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="{{ route('site.home') }}" target="_blank" class="nav-link" title="Visualizar Site Oficial em Nova Aba" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="fas fa-external-link-alt text-danger me-1"></i> Visualizar Site Oficial
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <!-- Alternador Dark / Light Mode (AdminLTE 4) -->
                    <li class="nav-item">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="adminThemeToggle" title="Alternar Modo Escuro / Claro" data-bs-toggle="tooltip" data-bs-placement="bottom">
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
                                <a class="dropdown-item {{ request()->routeIs('cliente.perfil*') ? 'active' : '' }}" href="{{ route('cliente.perfil') }}">
                                    <i class="fas fa-user-cog me-2 text-danger"></i> Meu Perfil &amp; Senha
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
            <div class="sidebar-brand d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-secondary">
                <a href="{{ route('cliente.dashboard') }}" class="brand-link text-decoration-none d-flex align-items-center gap-2">
                    <img src="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}" alt="Foco 7" height="34" class="brand-image logo-sidebar-full" id="sidebarLogoFull">
                    <img src="{{ asset('img/favicon.png') }}" alt="Foco 7" height="32" width="32" class="brand-image logo-sidebar-favicon" id="sidebarLogoFavicon" style="object-fit: contain;">
                    <span class="badge bg-primary ms-1 small brand-badge">CLIENTE</span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('cliente.dashboard') }}" class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Painel do Assinante</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('cliente.perfil') }}" class="nav-link {{ request()->routeIs('cliente.perfil*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Meu Perfil &amp; Senha</p>
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

        <!-- RODAPÉ DO PAINEL DO CLIENTE -->
        @php
            $footerSiteNome = \App\Models\CmsSetting::get('site_nome', 'Foco 7 Rastreamento & Segurança');
            $footerDevNome = \App\Models\CmsSetting::get('desenvolvedor_nome', 'Marcelo Brad');
            $footerDevLink = \App\Models\CmsSetting::get('desenvolvedor_link', 'https://wa.me/5521981325441');
            
            $empresaWhats = \App\Models\CmsSetting::get('contato_whatsapp', '559180510736');
            $empresaWhatsClean = preg_replace('/\D/', '', $empresaWhats);
            $empresaWhatsUrl = 'https://wa.me/' . ($empresaWhatsClean ?: '559180510736') . '?text=' . urlencode('Olá, sou cliente Foco 7 e gostaria de suporte para meu veículo/serviço.');
        @endphp
        <footer class="app-footer foco7-admin-footer">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-3 px-4">
                <!-- LADO ESQUERDO: DIREITOS COM NOME DA EMPRESA -->
                <div class="d-flex align-items-center gap-2 text-footer-brand text-center text-md-start">
                    <img src="{{ asset('img/favicon.png') }}" alt="{{ $footerSiteNome }}" width="20" height="20" class="rounded">
                    <span class="small fw-semibold">&copy; {{ date('Y') }} <strong>{{ $footerSiteNome }}</strong>.</span>
                    <span class="small text-muted d-none d-lg-inline">&bull; Todos os direitos reservados.</span>
                </div>

                <!-- LADO DIREITO: BOTÃO DE SUPORTE COM A EMPRESA + LINK DO DESENVOLVEDOR + BOTÃO VOLTAR AO TOPO -->
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-end gap-3 text-footer-actions">
                    <!-- Botão de suporte para falar com a empresa -->
                    <a href="{{ $empresaWhatsUrl }}" 
                       target="_blank" 
                       class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm px-3" 
                       title="Falar com o Suporte Oficial da Foco 7 via WhatsApp" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top">
                        <i class="fab fa-whatsapp fs-6"></i>
                        <span>Suporte Foco 7</span>
                    </a>

                    <!-- Link com nome do desenvolvedor -->
                    <span class="small text-footer-dev">
                        Desenvolvido por 
                        <a href="{{ $footerDevLink }}" target="_blank" class="fw-bold text-decoration-none footer-dev-link" title="Visitar link do desenvolvedor" data-bs-toggle="tooltip">
                            {{ $footerDevNome }}
                        </a>
                    </span>
                </div>
            </div>
        </footer>

        <!-- BOTÃO FLUTUANTE VOLTAR AO TOPO -->
        <button type="button" id="btnScrollTopFloat" class="btn btn-primary rounded-circle shadow-lg btn-scroll-top-float" title="Voltar ao Topo" data-bs-toggle="tooltip" data-bs-placement="left">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- BOTÃO FLUTUANTE SUPORTE DO CLIENTE COM A EMPRESA -->
        <a href="{{ $empresaWhatsUrl }}" 
           target="_blank" 
           id="btnSuporteFloatCliente" 
           class="btn btn-success rounded-pill shadow-lg btn-suporte-float-cliente d-inline-flex align-items-center gap-2" 
           title="Falar com o Suporte Oficial Foco 7">
            <i class="fab fa-whatsapp fs-5"></i>
            <span class="d-none d-sm-inline fw-semibold small">Suporte 24h</span>
        </a>

    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script>
        window.foco7SessionTimeoutMinutes = {{ (int) \App\Models\CmsSetting::get('sessao_inatividade_minutos', 30) }};
    </script>
    <script src="{{ asset('js/global-ajax.js') }}?v={{ file_exists(public_path('js/global-ajax.js')) ? filemtime(public_path('js/global-ajax.js')) : time() }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}?v={{ file_exists(public_path('js/global-mascaras.js')) ? filemtime(public_path('js/global-mascaras.js')) : time() }}"></script>
    <script src="{{ asset('js/global-dropzone.js') }}?v={{ file_exists(public_path('js/global-dropzone.js')) ? filemtime(public_path('js/global-dropzone.js')) : time() }}"></script>
    <script src="{{ asset('js/global-sessao.js') }}?v={{ file_exists(public_path('js/global-sessao.js')) ? filemtime(public_path('js/global-sessao.js')) : time() }}"></script>

    <script>
        // Alternador Dark / Light Mode no Painel do Cliente (AdminLTE 4)
        $(function () {
            var theme = localStorage.getItem('foco7_admin_theme') || 'dark';

            function applyAdminTheme(t) {
                if (t === 'dark') {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    $('body').addClass('dark-mode').attr('data-bs-theme', 'dark');
                    $('#adminThemeIcon').removeClass('fa-moon').addClass('fa-sun');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    $('body').removeClass('dark-mode').attr('data-bs-theme', 'light');
                    $('#adminThemeIcon').removeClass('fa-sun').addClass('fa-moon');
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

            // Sincronização e Alternância Automática de Logo Completa para Favicon quando a Sidebar for Compactada e no Hover
            function sincronizarLogoSidebar(forceFull) {
                const isCollapsed = document.body.classList.contains('sidebar-collapse');
                const isHovered = $('.app-sidebar:hover').length > 0 || forceFull === true;
                const logoFull = document.getElementById('sidebarLogoFull');
                const logoFavicon = document.getElementById('sidebarLogoFavicon');
                const brandBadge = document.querySelector('.sidebar-brand .brand-badge');

                if (logoFull && logoFavicon) {
                    if (isCollapsed && !isHovered) {
                        logoFull.style.setProperty('display', 'none', 'important');
                        logoFavicon.style.setProperty('display', 'inline-block', 'important');
                        if (brandBadge) brandBadge.style.setProperty('display', 'none', 'important');
                    } else {
                        logoFull.style.setProperty('display', 'inline-block', 'important');
                        logoFavicon.style.setProperty('display', 'none', 'important');
                        if (brandBadge) brandBadge.style.setProperty('display', 'inline-block', 'important');
                    }
                }
            }

            sincronizarLogoSidebar();
            const sidebarObserver = new MutationObserver(function() {
                sincronizarLogoSidebar();
            });
            sidebarObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });

            const $sidebar = $('.app-sidebar');
            $sidebar.on('mouseenter', function() {
                if (document.body.classList.contains('sidebar-collapse')) {
                    sincronizarLogoSidebar(true);
                }
            });

            $sidebar.on('mouseleave', function() {
                if (document.body.classList.contains('sidebar-collapse')) {
                    sincronizarLogoSidebar(false);
                }
            });

            $(document).on('click', '[data-lte-toggle="sidebar"]', function() {
                setTimeout(sincronizarLogoSidebar, 40);
                setTimeout(sincronizarLogoSidebar, 200);
            });

            // COMPORTAMENTO UNIVERSAL DO BOTÃO VOLTAR AO TOPO (SCROLL TO TOP)
            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 180) {
                    $('#btnScrollTopFloat').addClass('show');
                } else {
                    $('#btnScrollTopFloat').removeClass('show');
                }
            });

            $(document).on('click', '.btn-scroll-top, #btnScrollTopFloat', function (e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
