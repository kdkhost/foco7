<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel Administrativo') | Foco 7</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <script>
        (function() {
            var t = localStorage.getItem('foco7_admin_theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <!-- Bootstrap 5.3, FontAwesome 6, Flaticon UIcons & Bootstrap Icons (Suporte Completo a Ícones) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-bold-rounded/css/uicons-bold-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">

    <!-- Toastr & SweetAlert2 (Global) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Summernote BS5 CSS (Global WYSIWYG) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css">

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

    @php
        $oneSignalAppId = \App\Models\CmsSetting::get('onesignal_app_id');
        $oneSignalAtivo = \App\Models\CmsSetting::get('onesignal_ativo', '0') == '1';
    @endphp
    @if($oneSignalAtivo && !empty($oneSignalAppId))
    <!-- OneSignal Web Push SDK v16 Oficial -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "{{ $oneSignalAppId }}",
                notifyButton: {
                    enable: false,
                },
                allowLocalhostAsSecureOrigin: true,
            });
        });
    </script>
    @endif

    @yield('styles')
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">

    <div class="app-wrapper">

        <!-- NAVBAR SUPERIOR -->
        <nav class="app-header navbar navbar-expand bg-body sticky-top">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" title="Recolher / Expandir Menu" data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="{{ route('site.home') }}" target="_blank" class="nav-link" title="Visualizar Landing Page Oficial em Nova Aba" data-bs-toggle="tooltip" data-bs-placement="bottom">
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
                        <div class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0" style="width: 340px; max-width: 90vw;">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-body-tertiary rounded-top">
                                <span class="fw-bold small text-body"><i class="fas fa-bell text-danger me-1"></i> Notificações</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger badge-notificacoes-count" style="display: none;">0 novas</span>
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none btn-marcar-todas-dropdown fw-semibold" style="display: none; font-size: 0.75rem;" onclick="marcarTodasNotificacoesLidasHeader(event)" title="Marcar todas como lidas e limpar">
                                        <i class="fas fa-check-double me-1"></i>Marcar lidas
                                    </button>
                                </div>
                            </div>
                            <div class="list-group list-group-flush" id="dropdownNotificacoesLista" style="max-height: 290px; overflow-y: auto;">
                                <div class="p-3 text-center text-muted small">Carregando notificações...</div>
                            </div>
                            <div class="p-2 border-top text-center bg-body-tertiary rounded-bottom">
                                <a href="{{ route('admin.notificacoes.index') }}" class="small text-danger fw-semibold text-decoration-none">
                                    Ver Todas as Notificações <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                    @endif

                    <!-- Alternador Dark / Light Mode -->
                    <li class="nav-item">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="adminThemeToggle" title="Alternar Modo Escuro / Claro" data-bs-toggle="tooltip" data-bs-placement="bottom">
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
                                    <a class="dropdown-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">
                                        <i class="fas fa-users-cog me-2 text-danger"></i> Usuários &amp; Permissões
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}" href="{{ route('admin.configuracoes.index') }}">
                                        <i class="fas fa-sliders-h me-2 text-danger"></i> Configurações do Sistema
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
            <div class="sidebar-brand d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-secondary">
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('cliente.dashboard') }}" class="brand-link text-decoration-none d-flex align-items-center gap-2">
                    <!-- Logo Oficial Completa (Exibida no modo expandido) -->
                    <img src="{{ asset(\App\Models\CmsSetting::getLogo('escuro')) }}" alt="Foco 7" height="34" class="brand-image logo-sidebar-full" id="sidebarLogoFull">

                    <!-- Favicon Oficial da Foco 7 (Exibido automaticamente quando a sidebar for compactada) -->
                    <img src="{{ asset('img/favicon.png') }}" alt="Foco 7" height="32" width="32" class="brand-image logo-sidebar-favicon" id="sidebarLogoFavicon" style="object-fit: contain;">

                    <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : 'bg-primary' }} ms-1 small brand-badge">
                        {{ auth()->user()->role === 'admin' ? 'ADMIN' : 'CLIENTE' }}
                    </span>
                </a>
                <!-- Botão de Fechar no Mobile / Tablet -->
                <button type="button" class="btn btn-sm btn-link text-white-50 d-lg-none p-1 text-decoration-none" data-lte-toggle="sidebar" title="Fechar Menu">
                    <i class="fas fa-times fs-5"></i>
                </button>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" role="menu" data-accordion="false">
                        @if(auth()->user()->role === 'admin')
                            <!-- TOPO DE ACESSO RÁPIDO -->
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.notificacoes.index') }}" class="nav-link {{ request()->routeIs('admin.notificacoes.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-bell"></i>
                                    <p>
                                        Notificações
                                        <span class="badge bg-danger ms-auto badge-notificacoes-count" style="display: none; font-size: 0.65rem;">0</span>
                                    </p>
                                </a>
                            </li>

                            <!-- SEÇÃO 1: OPERAÇÕES & SERVIÇOS -->
                            <li class="nav-header">Operações &amp; Serviços</li>

                            @php
                                $isServicosActive = request()->routeIs('admin.agendamentos.*') || request()->routeIs('admin.estetica.*') || request()->routeIs('admin.planos.*');
                            @endphp
                            <li class="nav-item {{ $isServicosActive ? 'menu-open' : '' }}">
                                <a href="javascript:void(0)" class="nav-link {{ $isServicosActive ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-check"></i>
                                    <p>
                                        Atendimentos
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.agendamentos.index') }}" class="nav-link {{ request()->routeIs('admin.agendamentos.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Agenda &amp; Ordens</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.estetica.index') }}" class="nav-link {{ request()->routeIs('admin.estetica.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Estética Automotiva</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.planos.index') }}" class="nav-link {{ request()->routeIs('admin.planos.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Planos &amp; Preços</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            @php
                                $isFinanceiroActive = request()->routeIs('admin.clientes.*') || request()->routeIs('admin.faturas.*') || request()->routeIs('admin.gateways.*');
                            @endphp
                            <li class="nav-item {{ $isFinanceiroActive ? 'menu-open' : '' }}">
                                <a href="javascript:void(0)" class="nav-link {{ $isFinanceiroActive ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    <p>
                                        Financeiro
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.clientes.index') }}" class="nav-link {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Base de Clientes</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.faturas.index') }}" class="nav-link {{ request()->routeIs('admin.faturas.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Faturas &amp; Mensalidades</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.gateways.index') }}" class="nav-link {{ request()->routeIs('admin.gateways.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Gateways de Pagamento</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- SEÇÃO 2: SITE & CONTEÚDO -->
                            <li class="nav-header">Site &amp; Comunicação</li>

                            @php
                                $isCmsActive = request()->routeIs('admin.cms.*') || request()->routeIs('admin.seo.*') || request()->routeIs('admin.depoimentos.*') || request()->routeIs('admin.parceiros.*') || request()->routeIs('admin.filiais.*');
                            @endphp
                            <li class="nav-item {{ $isCmsActive ? 'menu-open' : '' }}">
                                <a href="javascript:void(0)" class="nav-link {{ $isCmsActive ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-laptop-code"></i>
                                    <p>
                                        Gestão do Site
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cms.index') }}" class="nav-link {{ request()->routeIs('admin.cms.index') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Editor Geral (CMS)</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.filiais.index') }}" class="nav-link {{ request()->routeIs('admin.filiais.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Unidades &amp; Filiais</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.parceiros.index') }}" class="nav-link {{ request()->routeIs('admin.parceiros.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Empresas Parceiras</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.depoimentos.index') }}" class="nav-link {{ request()->routeIs('admin.depoimentos.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>
                                                Depoimentos
                                                @php $pendentesDepCount = \App\Models\Depoimento::where('ativo', false)->count(); @endphp
                                                @if($pendentesDepCount > 0)
                                                    <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.65rem;">{{ $pendentesDepCount }}</span>
                                                @endif
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.seo.index') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>SEO &amp; Google</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            @php
                                $isEmailsActive = request()->routeIs('admin.emails.*');
                            @endphp
                            <li class="nav-item {{ $isEmailsActive ? 'menu-open' : '' }}">
                                <a href="javascript:void(0)" class="nav-link {{ $isEmailsActive ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>
                                        Templates de E-mail
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.emails.index') }}" class="nav-link {{ request()->routeIs('admin.emails.index') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Todos os Modelos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.emails.create') }}" class="nav-link {{ request()->routeIs('admin.emails.create') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Novo Modelo</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- SEÇÃO 3: SISTEMA & CONFIGURAÇÕES -->
                            <li class="nav-header">Sistema &amp; Ajustes</li>

                            @php
                                $isSistemaActive = request()->routeIs('admin.configuracoes.*') || request()->routeIs('admin.cron.*') || request()->routeIs('admin.manutencao.*') || request()->routeIs('admin.perfil.*') || request()->routeIs('admin.usuarios.*');
                            @endphp
                            <li class="nav-item {{ $isSistemaActive ? 'menu-open' : '' }}">
                                <a href="javascript:void(0)" class="nav-link {{ $isSistemaActive ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-cogs"></i>
                                    <p>
                                        Configurações
                                        <i class="nav-arrow fas fa-angle-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Usuários &amp; Permissões</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.configuracoes.index') }}" class="nav-link {{ request()->routeIs('admin.configuracoes.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Configurações do Sistema</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.cron.index') }}" class="nav-link {{ request()->routeIs('admin.cron.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Central de Cron Jobs</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.manutencao.index') }}" class="nav-link {{ request()->routeIs('admin.manutencao.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Modo Manutenção</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.perfil.index') }}" class="nav-link {{ request()->routeIs('admin.perfil.*') ? 'active' : '' }}">
                                            <i class="nav-icon far fa-circle"></i>
                                            <p>Meu Perfil</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <!-- ITENS DA ÁREA DO CLIENTE -->
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
        <main class="app-main">
            <div class="app-content-header mb-3">
                <div class="container-fluid">
                    <div class="row align-items-center g-2">
                        <div class="col-md-5 col-lg-5">
                            <h3 class="mb-0 fw-bold text-body fs-4">@yield('page_title', 'Painel Administrativo')</h3>
                            <p class="text-secondary small mb-0">@yield('page_description', '')</p>
                        </div>
                        <div class="col-md-7 col-lg-7 text-md-end mt-2 mt-md-0">
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

        <!-- RODAPÉ DO PAINEL COM DESIGN DISTINTO & ELEGÂNCIA ENTERPRISE -->
        @php
            $footerSiteNome = \App\Models\CmsSetting::get('site_nome', 'Foco 7 Rastreamento & Segurança');
            $footerDevNome = \App\Models\CmsSetting::get('desenvolvedor_nome', 'Marcelo Brad');
            $empresaWhatsClean = preg_replace('/\D/', '', (string) \App\Models\CmsSetting::get('contato_whatsapp', '559131970219'));

            $footerDevWhats = \App\Models\CmsSetting::get('desenvolvedor_whatsapp', '5521981325441');
            $footerDevWhatsClean = preg_replace('/\D/', '', (string) $footerDevWhats);

            // Blindagem: se o whatsapp do desenvolvedor estiver apontando para o numero corporativo da Foco 7 ou estiver vazio, força o numero real do desenvolvedor Marcelo Brad
            if (empty($footerDevWhatsClean) || $footerDevWhatsClean === $empresaWhatsClean || str_contains($footerDevWhatsClean, '31970219') || str_contains($footerDevWhatsClean, '80510736')) {
                $footerDevWhatsClean = '5521981325441';
            }

            $footerDevLink = \App\Models\CmsSetting::get('desenvolvedor_link', 'https://wa.me/' . $footerDevWhatsClean);
            if (str_contains((string) $footerDevLink, '31970219') || str_contains((string) $footerDevLink, '80510736')) {
                $footerDevLink = 'https://wa.me/5521981325441';
            }

            $footerDevWhatsUrl = 'https://wa.me/' . $footerDevWhatsClean . '?text=' . urlencode('Olá ' . $footerDevNome . ', preciso de suporte técnico referente ao painel administrativo da Foco 7.');

            // Formatação legível com máscara de telefone brasileiro
            $formattedDevWhats = '+' . substr($footerDevWhatsClean, 0, 2) . ' (' . substr($footerDevWhatsClean, 2, 2) . ') ';
            if (strlen($footerDevWhatsClean) >= 13) {
                $formattedDevWhats .= substr($footerDevWhatsClean, 4, 5) . '-' . substr($footerDevWhatsClean, 9);
            } elseif (strlen($footerDevWhatsClean) === 12) {
                $formattedDevWhats .= substr($footerDevWhatsClean, 4, 4) . '-' . substr($footerDevWhatsClean, 8);
            } else {
                $formattedDevWhats .= substr($footerDevWhatsClean, 4);
            }

            $footerDevTelegram = \App\Models\CmsSetting::get('desenvolvedor_telegram', 'MARCELO_BRAD');
            $footerDevTelegramClean = ltrim(preg_replace('/^https?:\/\/t\.me\//i', '', trim($footerDevTelegram)), '@');
            $footerDevTelegramUrl = 'https://t.me/' . ($footerDevTelegramClean ?: 'marcelobrad');
        @endphp
        <footer class="app-footer foco7-admin-footer">
            <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 py-3 px-4">
                <!-- LADO ESQUERDO: DIREITOS COM NOME DA EMPRESA -->
                <div class="d-flex align-items-center gap-2 text-footer-brand text-center text-md-start">
                    <img src="{{ asset('img/favicon.png') }}" alt="{{ $footerSiteNome }}" width="20" height="20" class="rounded">
                    <span class="small fw-semibold">&copy; {{ date('Y') }} <strong>{{ $footerSiteNome }}</strong>.</span>
                    <span class="small text-muted d-none d-lg-inline">&bull; Todos os direitos reservados.</span>
                </div>

                <!-- LADO DIREITO: LINK DO DESENVOLVEDOR (ABRE O BOX DE CONTATO) -->
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-end gap-3 text-footer-actions">
                    <span class="small text-footer-dev">
                        Desenvolvido por 
                        <a href="javascript:void(0)" onclick="toggleBoxDevContato()" class="fw-bold text-decoration-none footer-dev-link text-danger" title="Falar com o desenvolvedor (WhatsApp ou Telegram)" data-bs-toggle="tooltip">
                            {{ $footerDevNome }}
                        </a>
                    </span>
                </div>
            </div>
        </footer>

        <!-- BOTÃO FLUTUANTE VOLTAR AO TOPO (80px ACIMA CONFORME SOLICITADO) -->
        <button type="button" id="btnScrollTopFloat" class="btn btn-primary rounded-circle shadow-lg btn-scroll-top-float" title="Voltar ao Topo" data-bs-toggle="tooltip" data-bs-placement="left">
            <i class="fas fa-chevron-up"></i>
        </button>

        <!-- BOTÃO FLUTUANTE WHATSAPP DO DESENVOLVEDOR -->
        <button type="button" id="btnDevWhatsFloat" class="btn btn-dev-whats-float shadow-lg" title="Falar com o Desenvolvedor" onclick="toggleBoxDevContato()" data-bs-toggle="tooltip" data-bs-placement="left">
            <i class="fab fa-whatsapp fs-4"></i>
        </button>

        <!-- BOX FLUTUANTE DE SELEÇÃO: WHATSAPP VS TELEGRAM -->
        <div id="boxDevContato" class="box-dev-contato shadow-lg border" style="display: none;">
            <div class="box-dev-header d-flex align-items-center justify-content-between p-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 13px;">
                            {{ strtoupper(substr($footerDevNome, 0, 2)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px;" title="Online"></span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-body fs-6">{{ $footerDevNome }}</h6>
                        <small class="text-success fw-semibold" style="font-size: 11px;">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Desenvolvedor &amp; Suporte
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-sm" onclick="fecharBoxDevContato()" aria-label="Fechar"></button>
            </div>

            <div class="box-dev-body p-3">
                <p class="small text-secondary mb-3">
                    Selecione o canal para atendimento direto com o desenvolvedor:
                </p>

                <!-- OPÇÃO 1: WHATSAPP -->
                <a href="{{ $footerDevWhatsUrl }}" target="_blank" class="dev-canal-card dev-canal-whatsapp text-decoration-none d-flex align-items-center justify-content-between p-3 rounded-3 mb-2 shadow-sm" onclick="fecharBoxDevContato()">
                    <div class="d-flex align-items-center gap-3">
                        <div class="canal-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="fab fa-whatsapp fs-4"></i>
                        </div>
                        <div>
                            <strong class="d-block text-body">WhatsApp</strong>
                            <span class="small text-secondary font-monospace">{{ $formattedDevWhats }}</span>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-success fs-5"></i>
                </a>

                <!-- OPÇÃO 2: TELEGRAM -->
                <a href="{{ $footerDevTelegramUrl }}" target="_blank" class="dev-canal-card dev-canal-telegram text-decoration-none d-flex align-items-center justify-content-between p-3 rounded-3 shadow-sm" onclick="fecharBoxDevContato()">
                    <div class="d-flex align-items-center gap-3">
                        <div class="canal-icon text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background-color: #0088cc;">
                            <i class="fab fa-telegram-plane fs-4"></i>
                        </div>
                        <div>
                            <strong class="d-block text-body">Telegram</strong>
                            <span class="small text-secondary font-monospace">{{ '@' . $footerDevTelegramClean }}</span>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-info fs-5"></i>
                </a>
            </div>

            <div class="box-dev-footer p-2 text-center bg-body-tertiary border-top rounded-bottom small text-secondary">
                <i class="fas fa-shield-alt text-danger me-1"></i> Atendimento exclusivo para o administrador
            </div>
        </div>

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

    <!-- Scripts Globais Foco 7 (com quebra automatica de cache) -->
    <script>
        window.foco7SessionTimeoutMinutes = {{ (int) \App\Models\CmsSetting::get('sessao_inatividade_minutos', 30) }};
    </script>
    <script src="{{ asset('js/global-ajax.js') }}?v={{ file_exists(public_path('js/global-ajax.js')) ? filemtime(public_path('js/global-ajax.js')) : time() }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}?v={{ file_exists(public_path('js/global-mascaras.js')) ? filemtime(public_path('js/global-mascaras.js')) : time() }}"></script>
    <script src="{{ asset('js/global-dropzone.js') }}?v={{ file_exists(public_path('js/global-dropzone.js')) ? filemtime(public_path('js/global-dropzone.js')) : time() }}"></script>
    <script src="{{ asset('js/global-summernote.js') }}?v={{ file_exists(public_path('js/global-summernote.js')) ? filemtime(public_path('js/global-summernote.js')) : time() }}"></script>
    <script src="{{ asset('js/global-sessao.js') }}?v={{ file_exists(public_path('js/global-sessao.js')) ? filemtime(public_path('js/global-sessao.js')) : time() }}"></script>
    <script src="{{ asset('js/global-icon-picker.js') }}?v={{ file_exists(public_path('js/global-icon-picker.js')) ? filemtime(public_path('js/global-icon-picker.js')) : time() }}"></script>

    <script>
        // Alternador Dark / Light Mode oficial AdminLTE 4 & Bootstrap 5
        $(function () {
            var theme = localStorage.getItem('foco7_admin_theme') || 'light';

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

                        // Atualiza Badges e Botão de Limpar
                        const count = res.nao_lidas_count;
                        $('.badge-notificacoes-count').text(count);
                        if (count > 0) {
                            $('.badge-notificacoes-count').show();
                            $('.btn-marcar-todas-dropdown').show();
                        } else {
                            $('.badge-notificacoes-count').hide();
                            $('.btn-marcar-todas-dropdown').hide();
                        }

                        // Atualiza Dropdown do Sininho
                        const $lista = $('#dropdownNotificacoesLista');
                        if ($lista.length) {
                            if (res.ultimas.length === 0) {
                                $lista.html(`
                                    <div class="p-4 text-center text-muted small">
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2" style="width: 48px; height: 48px;">
                                            <i class="fas fa-check fs-5"></i>
                                        </div>
                                        <div class="fw-bold text-body">Tudo limpo!</div>
                                        <div class="text-secondary" style="font-size: 0.78rem;">Você não tem notificações pendentes.</div>
                                    </div>
                                `);
                            } else {
                                let html = '';
                                res.ultimas.forEach(function (n) {
                                    const linkDestino = n.link_acao || '{{ route("admin.notificacoes.index") }}';
                                    html += `
                                        <a href="/admin/notificacoes/${n.id}/abrir" 
                                           data-id="${n.id}" 
                                           data-link="${linkDestino}" 
                                           data-lida="0" 
                                           class="list-group-item list-group-item-action p-2 d-flex align-items-start gap-2 border-bottom dropdown-notificacao-item bg-body-tertiary">
                                            <div class="bg-${n.cor} bg-opacity-10 text-${n.cor} rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="${n.icone}" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold small text-truncate" style="max-width: 175px;">${n.titulo}</span>
                                                    <span class="badge bg-danger rounded-pill" style="font-size:0.6rem;">Nova</span>
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

            // CLIQUE NO ITEM DO DROPDOWN: MARCA COMO LIDA IMEDIATAMENTE VIA AJAX E ATUALIZA CONTADORES
            $(document).on('click', '.dropdown-notificacao-item', function (e) {
                const $item = $(this);
                const id = $item.data('id');
                const linkDestino = $item.data('link');
                const jaLida = $item.data('lida') == '1';

                if (!jaLida && id) {
                    e.preventDefault();
                    // Otimização visual instantânea
                    $item.removeClass('bg-body-secondary bg-light bg-opacity-75');
                    $item.find('.badge.bg-danger').remove();
                    $item.data('lida', '1');

                    // Decremento imediato otimista do badge
                    let curCount = parseInt($('.badge-notificacoes-count').first().text()) || 0;
                    curCount = Math.max(0, curCount - 1);
                    $('.badge-notificacoes-count').text(curCount);
                    if (curCount <= 0) {
                        $('.badge-notificacoes-count').hide();
                    }

                    // Notifica o backend via AJAX
                    $.ajax({
                        url: `/admin/notificacoes/${id}/lida`,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        complete: function () {
                            if (linkDestino && linkDestino !== '#' && linkDestino !== 'javascript:void(0)') {
                                window.location.href = linkDestino;
                            }
                        }
                    });
                }
            });

            window.marcarTodasNotificacoesLidasHeader = function (e) {
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                $.ajax({
                    url: '{{ route("admin.notificacoes.marcar_todas") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.success) {
                            window.foco7Notify.success(res.message);
                            sincronizarNotificacoesAdmin();
                            if (window.location.pathname.includes('/admin/notificacoes')) {
                                setTimeout(() => window.location.reload(), 600);
                            }
                        }
                    }
                });
            };

            sincronizarNotificacoesAdmin();
            setInterval(sincronizarNotificacoesAdmin, 25000);
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
            sincronizarLogoSidebar();

            // CONTROLE ACCORDION SUAVE COM FECHAMENTO AUTOMÁTICO DE OUTROS MENUS
            $(document).on('click', '.app-sidebar .sidebar-menu .nav-item > a.nav-link', function (e) {
                var $link = $(this);
                var $parent = $link.closest('.nav-item');
                var $treeview = $parent.children('.nav-treeview');

                if ($treeview.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Se a sidebar estiver compactada, descompacta para exibir o submenu adequadamente
                    if ($('body').hasClass('sidebar-collapse')) {
                        $('body').removeClass('sidebar-collapse');
                        if (typeof sincronizarLogoSidebar === 'function') {
                            sincronizarLogoSidebar(true);
                        }
                    }

                    if ($parent.hasClass('menu-open')) {
                        // Menu clicado já estava aberto: fecha suavemente com slideUp
                        $treeview.stop(true, true).slideUp(220, function () {
                            $parent.removeClass('menu-open');
                            $treeview.removeAttr('style');
                        });
                    } else {
                        // ACCORDION: Fecha todos os outros menus irmãos abertos com animação suave de slideUp
                        var $outrosAbertos = $parent.siblings('.nav-item.menu-open');
                        $outrosAbertos.each(function () {
                            var $outroItem = $(this);
                            $outroItem.children('.nav-treeview').stop(true, true).slideUp(220, function () {
                                $outroItem.removeClass('menu-open');
                                $(this).removeAttr('style');
                            });
                        });

                        // Abre o menu selecionado com animação suave de slideDown
                        $parent.addClass('menu-open');
                        $treeview.hide().stop(true, true).slideDown(220, function () {
                            $treeview.removeAttr('style');
                        });
                    }
                }
            });

            // Limpa resíduos de style inline de todos os treeviews
            $('.app-sidebar .sidebar-menu .nav-treeview').removeAttr('style');

            // COMPORTAMENTO UNIVERSAL DO BOTÃO VOLTAR AO TOPO (SCROLL TO TOP)
            $(window).on('scroll', function () {
                if ($(this).scrollTop() > 180) {
                    $('#btnScrollTopFloat').addClass('show');
                } else {
                    $('#btnScrollTopFloat').removeClass('show');
                }
            });

            $(document).on('click', '#btnScrollTopFloat', function (e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // CONTROLE DO BOX FLUTUANTE DE CONTATO DO DESENVOLVEDOR (WHATSAPP & TELEGRAM)
            window.abrirBoxDevContato = function () {
                $('#boxDevContato').stop(true, true).fadeIn(200);
            };

            window.fecharBoxDevContato = function () {
                $('#boxDevContato').stop(true, true).fadeOut(150);
            };

            window.toggleBoxDevContato = function () {
                $('#boxDevContato').stop(true, true).fadeToggle(200);
            };

            // Fecha ao clicar fora do box
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#boxDevContato, #btnDevWhatsFloat, .footer-dev-link').length) {
                    fecharBoxDevContato();
                }
            });

            // Fecha com tecla ESC
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    fecharBoxDevContato();
                }
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
