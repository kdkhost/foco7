@extends('admin.layouts.app')

@section('title', 'Gestão de Clientes')
@section('page_title', 'Base de Clientes & Assinantes')
@section('page_description', 'Gerencie dados cadastrais, veículos vinculados, status de acesso e redefinição de senhas.')

@section('page_actions')
<button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalCliente()">
    <i class="fas fa-user-plus me-1"></i> Cadastrar Novo Cliente
</button>
@endsection

@section('content')
<!-- CARDS DE MÉTRICAS -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Total de Clientes</h6>
                    <h3 class="fw-bold mb-0">{{ $totalClientes }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Clientes Ativos</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ $clientesAtivos }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger text-white rounded-3 p-3 me-3 fs-3">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1">Bloqueados / Inativos</h6>
                    <h3 class="fw-bold mb-0 text-danger">{{ $clientesBloqueados }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FILTROS & BUSCA -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.clientes.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-secondary"></i></span>
                    <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar por nome, e-mail, telefone ou CPF..." value="{{ request('busca') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Todos os Status</option>
                    <option value="ativo" {{ request('status') === 'ativo' ? 'selected' : '' }}>Ativos</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Bloqueados</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-danger flex-fill fw-bold">Filtrar</button>
                @if(request('busca') || request('status'))
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary">Limpar</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- TABELA DE CLIENTES -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header w-100 bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title fw-bold mb-0 text-body">
                <i class="fas fa-users text-danger me-2"></i> Base de Clientes Cadastrados
            </h5>
            <span class="badge bg-body-secondary text-body border px-2 py-1 small">
                {{ $clientes->total() }} clientes
            </span>
        </div>
        <div class="card-tools ms-auto d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger fw-bold shadow-sm" onclick="abrirModalCliente()">
                <i class="fas fa-user-plus me-1"></i> Cadastrar Novo Cliente
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Contato</th>
                        <th>CPF / CNPJ</th>
                        <th>Cidade / UF</th>
                        <th>Vínculos</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cli)
                    <tr>
                        <td>
                            @php
                                $partesCli = array_filter(explode(' ', trim($cli->name)));
                                $pLetra = !empty($partesCli) ? mb_substr(array_shift($partesCli), 0, 1, 'UTF-8') : 'C';
                                $sLetra = !empty($partesCli) ? mb_substr(array_pop($partesCli), 0, 1, 'UTF-8') : '';
                                $iniciaisCli = mb_strtoupper($pLetra . $sLetra, 'UTF-8');
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                @if(!empty($cli->avatar))
                                    <img src="{{ asset(ltrim($cli->avatar, '/')) }}" alt="{{ $cli->name }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.style.setProperty('display', 'none', 'important'); if(this.nextElementSibling) this.nextElementSibling.style.setProperty('display', 'inline-flex', 'important');">
                                    <div class="bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-circle align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.85rem; display: none !important;">
                                        {{ $iniciaisCli }}
                                    </div>
                                @else
                                    <div class="bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                        {{ $iniciaisCli }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $cli->name }}</div>
                                    <small class="text-secondary">Cadastrado em {{ $cli->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="fas fa-envelope text-secondary me-1 small"></i> {{ $cli->email }}</div>
                            <small class="text-secondary"><i class="fab fa-whatsapp text-success me-1"></i> {{ $cli->telefone }}</small>
                        </td>
                        <td class="small fw-semibold">{{ $cli->cpf_cnpj ?: '-' }}</td>
                        <td class="small">{{ $cli->cidade ? "{$cli->cidade} - {$cli->estado}" : '-' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border me-1" title="Faturas">{{ $cli->faturas_count }} Faturas</span>
                            <span class="badge bg-primary-subtle text-primary border" title="Agendamentos">{{ $cli->agendamentos_count }} Agendamentos</span>
                        </td>
                        <td>
                            @if($cli->status === 'ativo')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Ativo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Bloqueado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarCliente({{ $cli->id }})" title="Editar Dados Cadastrais">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="abrirModalSenha({{ $cli->id }}, '{{ addslashes($cli->name) }}')" title="Redefinir Senha">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirCliente({{ $cli->id }})" title="Excluir">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary">Nenhum cliente encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($clientes->hasPages())
    <div class="card-footer bg-transparent border-top py-3">
        {{ $clientes->links() }}
    </div>
    @endif
</div>

<!-- MODAL CADASTRAR / EDITAR CLIENTE -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalClienteTitulo">Novo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.clientes.salvar') }}" method="POST" class="ajax-form" id="formCliente">
                @csrf
                <input type="hidden" name="cliente_id" id="cliente_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome Completo *</label>
                            <input type="text" name="name" id="cli_name" class="form-control" required placeholder="Ex: Roberto Carlos">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">E-mail de Acesso *</label>
                            <input type="email" name="email" id="cli_email" class="form-control" required placeholder="cliente@email.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Telefone / WhatsApp *</label>
                            <input type="text" name="telefone" id="cli_telefone" class="form-control mask-telefone" required placeholder="(91) 98111-7777">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CPF ou CNPJ</label>
                            <input type="text" name="cpf_cnpj" id="cli_cpf_cnpj" class="form-control mask-cpf" placeholder="000.000.000-00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Status do Acesso *</label>
                            <select name="status" id="cli_status" class="form-select" required>
                                <option value="ativo">Ativo (Acesso Liberado)</option>
                                <option value="inativo">Bloqueado / Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">CEP</label>
                            <input type="text" name="cep" id="cli_cep" class="form-control mask-cep" placeholder="66000-000">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Endereço (Rua, Avenida)</label>
                            <input type="text" name="endereco" id="cli_endereco" class="form-control" placeholder="Rua das Flores">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Número</label>
                            <input type="text" name="numero" id="cli_numero" class="form-control" placeholder="123">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Bairro</label>
                            <input type="text" name="bairro" id="cli_bairro" class="form-control" placeholder="Centro">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Cidade</label>
                            <input type="text" name="cidade" id="cli_cidade" class="form-control" placeholder="Belém">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estado (UF)</label>
                            <input type="text" name="estado" id="cli_estado" class="form-control text-uppercase" maxlength="2" placeholder="PA">
                        </div>
                        <div class="col-md-8" id="campoSenhaContainer">
                            <label class="form-label small fw-bold" id="labelSenha">Senha de Acesso (Mínimo 6 dígitos)</label>
                            <input type="password" name="password" id="cli_password" class="form-control" placeholder="******">
                            <small class="text-secondary" id="ajudaSenha" style="display:none;">Deixe em branco para manter a senha atual.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold"><i class="fas fa-save me-1"></i> Gravar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL REDEFINIR SENHA -->
<div class="modal fade" id="modalSenha" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="fas fa-key text-warning me-2"></i> Redefinir Senha do Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRedefinirSenha" class="ajax-form" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">Defina a nova senha para o cliente <strong id="nomeClienteSenha" class="text-body fw-bold"></strong>:</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nova Senha *</label>
                        <input type="password" name="nova_senha" id="nova_senha" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="fas fa-check me-1"></i> Salvar Nova Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function abrirModalCliente() {
        $('#cliente_id').val('');
        $('#formCliente')[0].reset();
        $('#cli_status').val('ativo');
        $('#cli_password').prop('required', true);
        $('#ajudaSenha').hide();
        $('#modalClienteTitulo').text('Cadastrar Novo Cliente');
        new bootstrap.Modal(document.getElementById('modalCliente')).show();
    }

    function editarCliente(id) {
        $.getJSON('/admin/clientes/obter/' + id, function(res) {
            if (res.success && res.cliente) {
                var c = res.cliente;
                $('#cliente_id').val(c.id);
                $('#cli_name').val(c.name);
                $('#cli_email').val(c.email);
                $('#cli_telefone').val(c.telefone);
                $('#cli_cpf_cnpj').val(c.cpf_cnpj);
                $('#cli_status').val(c.status);
                $('#cli_cep').val(c.cep);
                $('#cli_endereco').val(c.endereco);
                $('#cli_numero').val(c.numero);
                $('#cli_bairro').val(c.bairro);
                $('#cli_cidade').val(c.cidade);
                $('#cli_estado').val(c.estado);
                $('#cli_password').val('').prop('required', false);
                $('#ajudaSenha').show();

                $('#modalClienteTitulo').text('Editar Cliente: ' + c.name);
                new bootstrap.Modal(document.getElementById('modalCliente')).show();
            }
        });
    }

    function abrirModalSenha(id, nome) {
        $('#nomeClienteSenha').text(nome);
        $('#nova_senha').val('');
        $('#formRedefinirSenha').attr('action', '/admin/clientes/senha/' + id);
        new bootstrap.Modal(document.getElementById('modalSenha')).show();
    }

    function excluirCliente(id) {
        window.foco7Confirm('Excluir Cliente?', 'Deseja realmente remover este cliente do sistema? Esta ação não poderá ser desfeita.', function() {
            $.ajax({
                url: '/admin/clientes/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.success) {
                        window.foco7Notify.success(res.message);
                        setTimeout(function() { location.reload(); }, 600);
                    }
                }
            });
        });
    }
</script>
@endsection
