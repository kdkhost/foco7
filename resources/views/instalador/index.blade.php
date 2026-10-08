<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instalador Web Foco 7 | Assistente cPanel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            background: #080a0f;
            color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            padding: 40px 15px;
        }
        .wizard-card {
            max-width: 780px;
            margin: 0 auto;
            background: #121722;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-item {
            text-align: center;
            flex: 1;
            position: relative;
            z-index: 2;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1e2532;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px auto;
            font-weight: 700;
        }
        .step-item.active .step-circle {
            background: #d90000;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(217, 0, 0, 0.5);
        }
        .step-item.completed .step-circle {
            background: #28a745;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="wizard-card">
        <div class="text-center mb-4">
            <span class="fs-2 fw-black text-danger">FOCO<span class="text-white">7</span></span>
            <h4 class="fw-bold mt-2">Assistente de Instalação e Configuração</h4>
            <p class="small text-secondary">Preparado para hospedagens cPanel com LiteSpeed e MySQL 8.0+</p>
        </div>

        <div class="step-indicator">
            <div class="step-item active" id="indicator-1">
                <div class="step-circle">1</div>
                <div class="small fw-bold">Requisitos</div>
            </div>
            <div class="step-item" id="indicator-2">
                <div class="step-circle">2</div>
                <div class="small fw-bold">Banco MySQL</div>
            </div>
            <div class="step-item" id="indicator-3">
                <div class="step-circle">3</div>
                <div class="small fw-bold">Admin & Instalação</div>
            </div>
        </div>

        <!-- ETAPA 1: REQUISITOS -->
        <div id="step-1" class="step-content">
            <h5 class="fw-bold mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Verificação de Requisitos do Servidor</h5>
            <div class="list-group list-group-flush mb-4">
                @foreach($requisitos as $req => $info)
                <div class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center py-2 border-secondary">
                    <div>
                        <span class="fw-semibold">{{ $req }}</span>
                        <small class="text-secondary ms-2">({{ $info['atual'] }})</small>
                    </div>
                    @if($info['status'])
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Aprovado</span>
                    @else
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Incompatível</span>
                    @endif
                </div>
                @endforeach
            </div>

            @if($todosAprovados)
                <button type="button" class="btn btn-danger w-100 py-3 fw-bold" onclick="irParaPasso(2)">
                    CONTINUAR PARA O BANCO DE DADOS <i class="fas fa-arrow-right ms-1"></i>
                </button>
            @else
                <div class="alert alert-danger py-2">
                    Alguns requisitos do PHP ou permissões de pastas estão ausentes. Ative as extensões no cPanel (PHP Selector) antes de prosseguir.
                </div>
            @endif
        </div>

        <!-- ETAPA 2: BANCO MYSQL -->
        <div id="step-2" class="step-content d-none">
            <h5 class="fw-bold mb-3"><i class="fas fa-database text-danger me-2"></i> Configuração do Banco de Dados MySQL</h5>
            <p class="small text-secondary mb-4">Insira os dados do banco criado no painel do cPanel (Assistente de Banco de Dados MySQL):</p>

            <form id="formBanco">
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small text-secondary">Host do MySQL</label>
                        <input type="text" id="db_host" class="form-control bg-dark text-white border-secondary" value="127.0.0.1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-secondary">Porta</label>
                        <input type="number" id="db_port" class="form-control bg-dark text-white border-secondary" value="3306" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-secondary">Nome do Banco de Dados (Database)</label>
                        <input type="text" id="db_name" class="form-control bg-dark text-white border-secondary" placeholder="ex: fococomb_sistema" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary">Usuário do Banco (Username)</label>
                        <input type="text" id="db_user" class="form-control bg-dark text-white border-secondary" placeholder="ex: fococomb_user" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary">Senha do Banco (Password)</label>
                        <input type="password" id="db_pass" class="form-control bg-dark text-white border-secondary" placeholder="••••••••">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-50 py-2" onclick="irParaPasso(1)">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-outline-success w-50 py-2" id="btnTestarBanco">
                        <i class="fas fa-plug me-1"></i> Testar Conexão
                    </button>
                    <button type="button" class="btn btn-danger w-50 py-2 fw-bold" onclick="irParaPasso(3)">
                        Avançar <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- ETAPA 3: ADMIN E EXECUÇÃO -->
        <div id="step-3" class="step-content d-none">
            <h5 class="fw-bold mb-3"><i class="fas fa-user-shield text-danger me-2"></i> Cadastro do Administrador Master</h5>
            <p class="small text-secondary mb-4">Defina as credenciais para acessar o painel de gerenciamento:</p>

            <form id="formFinal">
                <div class="mb-3">
                    <label class="form-label small text-secondary">Nome do Administrador</label>
                    <input type="text" id="admin_nome" class="form-control bg-dark text-white border-secondary" value="Administrador Master" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-secondary">E-mail do Administrador</label>
                    <input type="email" id="admin_email" class="form-control bg-dark text-white border-secondary" value="admin@foco7.com.br" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small text-secondary">Senha do Administrador</label>
                    <input type="password" id="admin_senha" class="form-control bg-dark text-white border-secondary" value="foco7@2026" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary w-50 py-3" onclick="irParaPasso(2)">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </button>
                    <button type="button" class="btn btn-danger w-50 py-3 fw-bold" id="btnExecutarInstalacao">
                        <i class="fas fa-magic me-1"></i> INSTALAR AGORA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts Globais Foco 7 -->
    <script src="{{ asset('js/global-ajax.js') }}"></script>

    <script>
        function irParaPasso(passo) {
            $('.step-content').addClass('d-none');
            $('#step-' + passo).removeClass('d-none');

            $('.step-item').removeClass('active');
            $('#indicator-' + passo).addClass('active');
        }

        $(function () {
            // Teste de conexão com o MySQL
            $('#btnTestarBanco').on('click', function () {
                var $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testando...');

                $.ajax({
                    url: '{{ route("instalador.testar_banco") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        db_host: $('#db_host').val(),
                        db_port: $('#db_port').val(),
                        db_name: $('#db_name').val(),
                        db_user: $('#db_user').val(),
                        db_pass: $('#db_pass').val()
                    },
                    success: function (res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-plug me-1"></i> Testar Conexão');
                        window.foco7Notify.success(res.message);
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-plug me-1"></i> Testar Conexão');
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Falha na conexão com o banco MySQL.';
                        window.foco7Notify.error(msg);
                    }
                });
            });

            // Executar instalação completa
            $('#btnExecutarInstalacao').on('click', function () {
                window.confirmarAcao(
                    'Iniciar Instalação?',
                    'As tabelas do MySQL serão criadas e os dados iniciais serão configurados.',
                    'question',
                    function () {
                        var $btn = $('#btnExecutarInstalacao');
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Instalando...');

                        $.ajax({
                            url: '{{ route("instalador.executar") }}',
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: {
                                db_host: $('#db_host').val(),
                                db_port: $('#db_port').val(),
                                db_name: $('#db_name').val(),
                                db_user: $('#db_user').val(),
                                db_pass: $('#db_pass').val(),
                                admin_nome: $('#admin_nome').val(),
                                admin_email: $('#admin_email').val(),
                                admin_senha: $('#admin_senha').val()
                            },
                            success: function (res) {
                                if (res.success) {
                                    Swal.fire({
                                        title: 'Instalação Concluída!',
                                        text: 'O sistema Foco 7 foi instalado com sucesso. Você será redirecionado para a tela de login.',
                                        icon: 'success',
                                        confirmButtonColor: '#d90000',
                                        confirmButtonText: 'Acessar o Painel'
                                    }).then(() => {
                                        window.location.href = res.redirect;
                                    });
                                } else {
                                    $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> INSTALAR AGORA');
                                    window.foco7Notify.error(res.message);
                                }
                            },
                            error: function (xhr) {
                                $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> INSTALAR AGORA');
                                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Erro durante a instalação.';
                                window.foco7Notify.error(msg);
                            }
                        });
                    }
                );
            });
        });
    </script>
</body>
</html>
