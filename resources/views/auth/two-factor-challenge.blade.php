<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Foco 7 | Autenticação em Duas Etapas (2FA)</title>
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
            color: #0f172a;
        }
        body.light-theme .login-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            color: #0f172a;
        }
        body.light-theme .login-card .form-label {
            color: #0f172a !important;
        }
        body.light-theme .form-control {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        body.light-theme .input-group-text {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }
        .login-card {
            background: #11141c;
            border: 1px solid #1e2433;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 440px;
            padding: 36px 30px;
            position: relative;
        }
        .btn-foco {
            background: linear-gradient(135deg, #d90000 0%, #b80000 100%);
            color: #ffffff;
            border: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.2s ease;
        }
        .btn-foco:hover {
            background: linear-gradient(135deg, #f00000 0%, #c80000 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(217, 0, 0, 0.35);
        }
        .otp-input {
            letter-spacing: 8px;
            font-size: 1.5rem;
            text-align: center;
            font-weight: 700;
        }
        .shield-icon-wrapper {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(217, 0, 0, 0.12);
            border: 2px solid rgba(217, 0, 0, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            animation: pulse-shield 2s infinite ease-in-out;
        }
        @keyframes pulse-shield {
            0%, 100% { box-shadow: 0 0 0 0 rgba(217, 0, 0, 0.4); }
            50% { box-shadow: 0 0 0 12px rgba(217, 0, 0, 0); }
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <!-- ÍCONE ESCUDO -->
        <div class="shield-icon-wrapper">
            <i class="fas fa-shield-alt fa-2x text-danger"></i>
        </div>

        <h4 class="fw-bold mb-1">Verificação em 2 Etapas</h4>
        <p class="small text-secondary mb-4">
            Olá, <strong class="text-danger">{{ $user->name }}</strong>! Digite o código de 6 dígitos gerado no seu aplicativo autenticador para confirmar seu acesso.
        </p>

        <!-- FORMULÁRIO 2FA AJAX -->
        <form id="form2FA" action="{{ route('login.2fa.verify') }}" method="POST">
            @csrf

            <!-- MODO NORMAL: CÓDIGO TOTP DE 6 DÍGITOS -->
            <div id="boxCodigoTotp">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold mb-1">
                        <i class="fas fa-mobile-alt me-1 text-danger"></i> Código do Autenticador
                    </label>
                    <input type="text"
                           name="code"
                           id="inputTotpCode"
                           class="form-control form-control-lg otp-input"
                           maxlength="6"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           placeholder="000000"
                           autocomplete="one-time-code"
                           autofocus>
                </div>
            </div>

            <!-- MODO RECUPERAÇÃO: RECOVERY CODE -->
            <div id="boxRecoveryCode" class="d-none">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold mb-1">
                        <i class="fas fa-key me-1 text-warning"></i> Código de Recuperação de Emergência
                    </label>
                    <input type="text"
                           name="recovery_code"
                           id="inputRecoveryCode"
                           class="form-control form-control-lg text-center fw-bold"
                           maxlength="12"
                           placeholder="XXXX-XXXX"
                           style="letter-spacing: 2px;">
                    <div class="form-text small text-secondary">
                        Cada código de emergência pode ser utilizado apenas uma única vez.
                    </div>
                </div>
            </div>

            <input type="hidden" name="usar_recuperacao" id="inputUsarRecuperacao" value="0">

            <div class="d-grid mb-3">
                <button type="submit" id="btnConfirmar2fa" class="btn btn-foco btn-lg">
                    <i class="fas fa-check-circle me-1"></i> VERIFICAR E ACESSAR
                </button>
            </div>

            <!-- ALTERNAR ENTRE TOTP E RECOVERY CODE -->
            <div class="mb-3">
                <button type="button" id="btnToggleModo" class="btn btn-link text-decoration-none small text-danger fw-semibold p-0">
                    <i class="fas fa-life-ring me-1"></i> Perdeu o celular? Usar código de recuperação
                </button>
            </div>

            <hr class="border-secondary opacity-25 my-3">

            <!-- CANCELAR E VOLTAR AO LOGIN -->
            <div>
                <a href="{{ route('login.2fa.cancel') }}" class="btn btn-sm btn-outline-secondary w-100 py-2">
                    <i class="fas fa-arrow-left me-1"></i> Cancelar e Voltar para o Login
                </a>
            </div>
        </form>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 4000
        };

        var modoRecuperacao = false;

        $('#btnToggleModo').on('click', function() {
            modoRecuperacao = !modoRecuperacao;
            if (modoRecuperacao) {
                $('#boxCodigoTotp').addClass('d-none');
                $('#boxRecoveryCode').removeClass('d-none');
                $('#inputUsarRecuperacao').val('1');
                $('#btnToggleModo').html('<i class="fas fa-mobile-alt me-1"></i> Voltar para o código de 6 dígitos');
                $('#inputRecoveryCode').focus();
            } else {
                $('#boxRecoveryCode').addClass('d-none');
                $('#boxCodigoTotp').removeClass('d-none');
                $('#inputUsarRecuperacao').val('0');
                $('#btnToggleModo').html('<i class="fas fa-life-ring me-1"></i> Perdeu o celular? Usar código de recuperação');
                $('#inputTotpCode').focus();
            }
        });

        // Submissão AJAX com tratamento de erro
        $('#form2FA').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var btn = $('#btnConfirmar2fa');
            var originalBtnHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> VALIDANDO...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Código validado com sucesso!');
                        setTimeout(function() {
                            window.location.href = response.redirect || '/';
                        }, 600);
                    } else {
                        btn.prop('disabled', false).html(originalBtnHtml);
                        toastr.error(response.message || 'Código incorreto.');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalBtnHtml);
                    var msg = 'Código incorreto ou expirado. Tente novamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                }
            });
        });
    </script>
</body>
</html>
