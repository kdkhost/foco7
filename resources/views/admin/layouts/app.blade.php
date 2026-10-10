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

        <!-- NAVBAR SUPERIOR -->
        <nav class="app-header navbar navbar-expand bg-body sticky-top">
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
                    @if(auth()->user()->role === 'admin')
                    <!-- Central de Notificações com Sininho e Badges em Tempo Real -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative d-flex align-items-center justify-content-center p-2 text-body" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownSinoNotificacoes" title="Central de Notificações">
                            <i class="fas fa-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-notificacoes-count" style="display: none; font-size: 0.65rem;">
                                0
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0" style="width: 330px; max-width: 90vw;">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                                <span class="fw-bold small text-body"><i class="fas fa-bell text-danger me-1"></i> Notificações</span>
                                <span class="badge bg-danger badge-notificacoes-count" style="display: none;">0 novas</span>
                            </div>
                            <div class="list-group list-group-flush" id="dropdownNotificacoesLista" style="max-height: 280px; overflow-y: auto;">
                                <div class="p-3 text-center text-muted small">Carregando notificações...</div>
                            </div>
                            <div class="p-2 border-top text-center bg-light rounded-bottom">
                                <a href="{{ route('admin.notificacoes.index') }}" class="small text-danger fw-semibold text-decoration-none">
                                    Ver Todas as Notificações <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endif

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

                            <li class="nav-item">
                                <a href="{{ route('admin.notificacoes.index') }}" class="nav-link {{ request()->routeIs('admin.notificacoes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-bell text-danger"></i>
                                    <p>
                                        Notificações
                                        <span class="badge bg-danger ms-auto badge-notificacoes-count" style="display: none;">0</span>
                                    </p>
                                </a>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Gestão do Portal & Serviços</li>

                            <li class="nav-item {{ request()->routeIs('admin.cms.*') ? 'menu-open' : '' }}">
                                <a href="{{ route('admin.cms.index') }}" class="nav-link {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-laptop-code"></i>
                                    <p>
                                        CMS & Landing Page
                                        <i class="nav-arrow fas fa-angle-right ms-auto"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview ps-2">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-cores" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-cores', event); }">
                                            <i class="fas fa-palette nav-icon text-danger" style="font-size: 0.75rem;"></i>
                                            <p class="small">Cores & Tema</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-banners" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-banners', event); }">
                                            <i class="fas fa-images nav-icon text-secondary" style="font-size: 0.75rem;"></i>
                                            <p class="small">Banners & Hero</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-app" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-app', event); }">
                                            <i class="fas fa-mobile-alt nav-icon text-danger" style="font-size: 0.75rem;"></i>
                                            <p class="small">Aplicativo Mobile</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-ronda" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-ronda', event); }">
                                            <i class="fas fa-shield-alt nav-icon text-danger" style="font-size: 0.75rem;"></i>
                                            <p class="small">Ronda Tática</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-secoes" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-secoes', event); }">
                                            <i class="fas fa-layer-group nav-icon text-secondary" style="font-size: 0.75rem;"></i>
                                            <p class="small">Sobre & Diferenciais</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-faq" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-faq', event); }">
                                            <i class="fas fa-question-circle nav-icon text-warning" style="font-size: 0.75rem;"></i>
                                            <p class="small">FAQs & Dúvidas</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-depoimentos" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-depoimentos', event); }">
                                            <i class="fas fa-star nav-icon text-warning" style="font-size: 0.75rem;"></i>
                                            <p class="small">Depoimentos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-lgpd" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-lgpd', event); }">
                                            <i class="fas fa-user-shield nav-icon text-danger" style="font-size: 0.75rem;"></i>
                                            <p class="small">LGPD & Termos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}#tab-whatsapp" class="nav-link py-1" onclick="if(window.ativarAbaCms) { window.ativarAbaCms('tab-whatsapp', event); }">
                                            <i class="fab fa-whatsapp nav-icon text-success" style="font-size: 0.75rem;"></i>
                                            <p class="small">WhatsApp Flutuante</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.estetica.index') }}" class="nav-link {{ request()->routeIs('admin.estetica.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-car-side text-warning"></i>
                                    <p>Estética Automotiva</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.planos.index') }}" class="nav-link {{ request()->routeIs('admin.planos.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tags text-danger"></i>
                                    <p>Planos & Preços</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.agendamentos.index') }}" class="nav-link {{ request()->routeIs('admin.agendamentos.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-alt text-danger"></i>
                                    <p>Agenda & Ordens</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-search text-warning"></i>
                                    <p>SEO & Meta Tags</p>
                                </a>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Clientes & Financeiro</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users text-danger"></i>
                                    <p>Base de Clientes</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.faturas.index') }}" class="nav-link {{ request()->routeIs('admin.faturas.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice-dollar text-success"></i>
                                    <p>Faturas & Mensalidades</p>
                                </a>
                            </li>

                            <li class="nav-item {{ request()->routeIs('admin.gateways.*') ? 'menu-open' : '' }}">
                                <a href="{{ route('admin.gateways.index') }}" class="nav-link {{ request()->routeIs('admin.gateways.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-credit-card text-success"></i>
                                    <p>
                                        Gateways & Carnê
                                        <i class="nav-arrow fas fa-angle-right ms-auto"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview ps-2">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.gateways.index') }}" class="nav-link py-1">
                                            <i class="fas fa-sliders-h nav-icon text-success" style="font-size: 0.75rem;"></i>
                                            <p class="small">Mercado Pago & EFI</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.gateways.index') }}#tab-carne" class="nav-link py-1">
                                            <i class="fas fa-book-open nav-icon text-warning" style="font-size: 0.75rem;"></i>
                                            <p class="small">Gerador de Carnês (PDF)</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-header text-uppercase small text-secondary mt-3">Sistema & Operações</li>

                            <li class="nav-item {{ request()->routeIs('admin.emails.*') ? 'menu-open' : '' }}">
                                <a href="{{ route('admin.emails.index') }}" class="nav-link {{ request()->routeIs('admin.emails.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-envelope-open-text text-danger"></i>
                                    <p>
                                        Templates de E-mails
                                        <i class="nav-arrow fas fa-angle-right ms-auto"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview ps-2">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.emails.index') }}" class="nav-link py-1">
                                            <i class="fas fa-list nav-icon text-secondary" style="font-size: 0.75rem;"></i>
                                            <p class="small">Todos os Modelos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.emails.create') }}" class="nav-link py-1">
                                            <i class="fas fa-plus-circle nav-icon text-danger" style="font-size: 0.75rem;"></i>
                                            <p class="small">Novo Modelo</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.cron.index') }}" class="nav-link {{ request()->routeIs('admin.cron.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-clock text-danger"></i>
                                    <p>Central de Cron Jobs</p>
                                </a>
                            </li>

                            <li class="nav-item {{ request()->routeIs('admin.configuracoes.*') ? 'menu-open' : '' }}">
                                <a href="{{ route('admin.configuracoes.index') }}" class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-sliders-h text-warning"></i>
                                    <p>
                                        Configurações & SMTP
                                        <i class="nav-arrow fas fa-angle-right ms-auto"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview ps-2">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.configuracoes.index') }}" class="nav-link py-1">
                                            <i class="fas fa-building nav-icon text-secondary" style="font-size: 0.75rem;"></i>
                                            <p class="small">Dados & Logotipos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.configuracoes.index') }}#smtp" class="nav-link py-1">
                                            <i class="fas fa-server nav-icon text-warning" style="font-size: 0.75rem;"></i>
                                            <p class="small">Servidor SMTP</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.manutencao.index') }}" class="nav-link {{ request()->routeIs('admin.manutencao.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tools text-secondary"></i>
                                    <p>Modo Manutenção</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.perfil.index') }}" class="nav-link {{ request()->routeIs('admin.perfil.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-circle text-secondary"></i>
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

            // SINTETIZADOR DE SOM DE ALERTA SUAVE (Web Audio API nativo)
            function tocarSomNotificacao() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(1320, audioCtx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.25);
                } catch (e) {}
            }

            // POLLING INTELIGENTE DA CENTRAL DE NOTIFICAÇÕES (Navbar, Badges e Web Push)
            @if(auth()->user()->role === 'admin')
            function sincronizarNotificacoesAdmin() {
                $.ajax({
                    url: '{{ route("admin.notificacoes.ultimas_ajax") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (!res.success) return;

                        // Atualiza Badges
                        const count = res.nao_lidas_count;
                        $('.badge-notificacoes-count').text(count);
                        if (count > 0) {
                            $('.badge-notificacoes-count').show();
                        } else {
                            $('.badge-notificacoes-count').hide();
                        }

                        // Atualiza Dropdown do Sininho
                        const $lista = $('#dropdownNotificacoesLista');
                        if ($lista.length) {
                            if (res.ultimas.length === 0) {
                                $lista.html('<div class="p-3 text-center text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Nenhuma notificação recente.</div>');
                            } else {
                                let html = '';
                                res.ultimas.forEach(function (n) {
                                    const bgClass = !n.lida ? 'bg-light bg-opacity-75' : '';
                                    const dot = !n.lida ? '<span class="badge bg-danger rounded-pill" style="font-size:0.6rem;">Nova</span>' : '';
                                    html += `
                                        <a href="${n.link_acao || '{{ route("admin.notificacoes.index") }}'}" class="list-group-item list-group-item-action p-2 d-flex align-items-start gap-2 border-bottom ${bgClass}">
                                            <div class="bg-${n.cor} bg-opacity-10 text-${n.cor} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="${n.icone}" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold small text-truncate" style="max-width: 170px;">${n.titulo}</span>
                                                    ${dot}
                                                </div>
                                                <p class="small text-secondary mb-1 text-truncate" style="font-size: 0.78rem;">${n.mensagem}</p>
                                                <span class="text-muted" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i>${n.tempo_relativo}</span>
                                            </div>
                                        </a>
                                    `;
                                });
                                $lista.html(html);
                            }
                        }

                        // Se houver nova notificação recebida em tempo real
                        if (res.nova_notificacao_push) {
                            tocarSomNotificacao();
                            toastr.info(res.nova_notificacao_push.mensagem, res.nova_notificacao_push.titulo, {
                                timeOut: 8000,
                                onclick: function () {
                                    window.location.href = res.nova_notificacao_push.link_acao;
                                }
                            });

                            // Notificação Push nativa no celular / computador
                            if ("Notification" in window && Notification.permission === 'granted') {
                                try {
                                    const pushObj = new Notification(res.nova_notificacao_push.titulo, {
                                        body: res.nova_notificacao_push.mensagem,
                                        icon: res.nova_notificacao_push.icone,
                                        tag: 'foco7-push-' + res.nova_notificacao_push.id,
                                        data: { url: res.nova_notificacao_push.link_acao }
                                    });
                                    pushObj.onclick = function (e) {
                                        e.preventDefault();
                                        window.focus();
                                        window.location.href = res.nova_notificacao_push.link_acao;
                                    };
                                } catch (err) {}
                            }
                        }
                    }
                });
            }

            sincronizarNotificacoesAdmin();
            setInterval(sincronizarNotificacoesAdmin, 25000);
            @endif
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
