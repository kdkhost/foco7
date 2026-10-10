<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Criar Nova Senha | Foco 7 Rastreamento</title>
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
        }
        .reset-card {
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
        .icon-shield-reset {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(46, 213, 115, 0.15);
            border: 1px solid rgba(46, 213, 115, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            color: #2ed573;
            font-size: 28px;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <div class="text-center mb-4">
            <div class="icon-shield-reset">
                <i class="fas fa-lock"></i>
            </div>
            <img src="{{ asset('/img/logo-foco7.png') }}" alt="Foco 7" height="42" class="mb-2" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
            <h5 class="fw-bold mb-1">Cadastrar Nova Senha</h5>
            <p class="small text-secondary mb-0">Digite sua nova senha segura para restaurar o acesso à sua conta.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" id="formResetUpdate">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label small fw-bold">Seu E-mail Cadastrado</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required value="{{ $email ?? old('email') }}" placeholder="seu@email.com">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Nova Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control bg-dark text-white border-secondary" required minlength="8" placeholder="Mínimo 8 caracteres">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Confirmar Nova Senha</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-dark text-white border-secondary" required minlength="8" placeholder="Repita a nova senha">
                </div>
            </div>

            <button type="submit" class="btn-foco" id="btnSalvarNovaSenha">
                <i class="fas fa-save me-1"></i> ATUALIZAR E SALVAR SENHA
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-secondary">
            <a href="{{ route('login') }}" class="small text-secondary text-decoration-none">
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
            $('#formResetUpdate').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                var $btn = $('#btnSalvarNovaSenha');
                var origText = $btn.html();

                if ($('#password').val() !== $('#password_confirmation').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Senhas Diferentes',
                        text: 'A nova senha e a confirmação de senha não coincidem.',
                        confirmButtonColor: '#d90000',
                        confirmButtonText: 'Corrigir'
                    });
                    return;
                }

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Atualizando...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function (res) {
                        $btn.prop('disabled', false).html(origText);
                        Swal.fire({
                            icon: 'success',
                            title: 'Senha Redefinida!',
                            text: res.message || 'Sua senha foi atualizada com sucesso!',
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Acessar Conta'
                        }).then(function () {
                            window.location.href = res.redirect || '{{ route("login") }}';
                        });
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(origText);
                        var msg = 'Ocorreu um erro ao atualizar sua senha.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Não foi possível redefinir',
                            text: msg,
                            confirmButtonColor: '#d90000',
                            confirmButtonText: 'Entendido'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
