<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Área do Cliente') | Foco 7</title>

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Toastr & SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --foco-red: #d90000;
        }
        body {
            background-color: #0b0e14;
            color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }
        .cliente-navbar {
            background-color: #121722;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .card-cliente {
            background-color: #121722;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark cliente-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('cliente.dashboard') }}">
                <span class="fs-4 fw-black text-danger">FOCO<span class="text-white">7</span></span>
                <span class="badge bg-secondary small">CLIENTE</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCliente">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navCliente">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active text-danger fw-bold' : '' }}" href="{{ route('cliente.dashboard') }}">
                            <i class="fas fa-home me-1"></i> Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cliente.faturas.*') ? 'active text-danger fw-bold' : '' }}" href="{{ route('cliente.faturas.index') }}">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Minhas Faturas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('site.home') }}" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Site Oficial
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="small text-secondary d-none d-md-inline">Olá, <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                    <a href="javascript:void(0)" onclick="document.getElementById('formLogout').submit();" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <form id="formLogout" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>
    <script src="{{ asset('js/global-mascaras.js') }}"></script>

    @yield('scripts')
</body>
</html>
