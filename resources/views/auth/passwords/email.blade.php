<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperação de Senha | Foco 7 Rastreamento</title>
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
            background: rgba(217, 0, 0, 0.15);
            border: 1px solid rgba(217, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            color: var(--foco-red);
            font-size: 28px;
        }
    </style>
</head>
<body>

    <div class="reset-card">
        <div class="text-center mb-4">
            <div class="icon-shield-reset">
                <i class="fas fa-key"></i>
            </div>
            <img src="{{ asset('/img/logo-foco7.png') }}" alt="Foco 7" height="42" class="mb-2" onerror="this.onerror=null; this.src='{{ asset('/img/logo-foco7-dark.png') }}';">
            <h5 class="fw-bold mb-1">Esqueceu sua senha?</h5>
            <p class="small text-secondary mb-0">Informe o e-mail cadastrado e enviaremos instruções com link seguro para você redefinir sua senha.</p>
        </div>

        <form action="{{ route('password.email') }}" method="POST" id="formResetEmail">
            @csrf
            <div class="mb-4">
                <label class="form-label small fw-bold">E-mail Cadastrado</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-secondary">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-control bg-dark text-white border-secondary" required placeholder="seu@email.com" autofocus>
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
