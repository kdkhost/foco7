<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PermissaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Lista todos os usuários com filtros, paginação e métricas
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro por termo de busca
        if ($request->filled('busca')) {
            $busca = trim($request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%");
            });
        }

        // Filtro por papel / perfil
        if ($request->filled('role') && $request->role !== 'todos') {
            $query->where('role', $request->role);
        }

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtro por 2FA
        if ($request->filled('two_factor')) {
            if ($request->two_factor === 'ativo') {
                $query->where('two_factor_enabled', true);
            } elseif ($request->two_factor === 'inativo') {
                $query->where(function ($q) {
                    $q->where('two_factor_enabled', false)
                      ->orWhereNull('two_factor_enabled');
                });
            }
        }

        $usuarios = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // Métricas Small Boxes
        $metricas = [
            'total' => User::count(),
            'admins' => User::whereIn('role', ['admin', 'gerente'])->count(),
            'equipe' => User::whereIn('role', ['atendente', 'financeiro'])->count(),
            'clientes' => User::where('role', 'cliente')->count(),
            'com_2fa' => User::where('two_factor_enabled', true)->count(),
        ];

        $modulosPermissoes = PermissaoService::MODULOS;
        $papeis = PermissaoService::PAPEIS;

        return view('admin.usuarios.index', compact('usuarios', 'metricas', 'modulosPermissoes', 'papeis'));
    }

    /**
     * Retorna os dados completos do usuário para o modal de edição (AJAX)
     */
    public function show($id)
    {
        $usuario = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $usuario->role,
                'status' => $usuario->status,
                'cpf_cnpj' => $usuario->cpf_cnpj,
                'telefone' => $usuario->telefone,
                'data_nascimento' => $usuario->data_nascimento ? $usuario->data_nascimento->format('Y-m-d') : null,
                'genero' => $usuario->genero,
                'cargo' => $usuario->cargo,
                'bio' => $usuario->bio,
                'cep' => $usuario->cep,
                'endereco' => $usuario->endereco,
                'numero' => $usuario->numero,
                'complemento' => $usuario->complemento,
                'bairro' => $usuario->bairro,
                'cidade' => $usuario->cidade,
                'estado' => $usuario->estado,
                'avatar_url' => $usuario->avatar ? asset(ltrim($usuario->avatar, '/')) : null,
                'two_factor_enabled' => (bool)$usuario->two_factor_enabled,
                'permissoes' => $usuario->permissoes ?? [],
            ],
        ]);
    }

    /**
     * Cadastra um novo usuário no sistema (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,gerente,atendente,financeiro,cliente',
            'status' => 'required|string|in:ativo,inativo',
            'telefone' => 'nullable|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'bio' => 'nullable|string|max:1000',
            'permissoes' => 'nullable|array',
            'avatar_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'role.required' => 'Selecione o papel/função do usuário.',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar_upload')) {
            $dest = public_path('uploads/avatares');
            if (!File::isDirectory($dest)) {
                File::makeDirectory($dest, 0755, true, true);
            }
            $file = $request->file('avatar_upload');
            $fileName = 'avatar_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($dest, $fileName);
            $avatarPath = '/uploads/avatares/' . $fileName;
        }

        // Se for admin, concede todas as permissões se não foram explicitadas
        $permissoes = $request->permissoes ?? [];
        if ($request->role === 'admin' && empty($permissoes)) {
            $permissoes = PermissaoService::todasPermissoes();
        }

        $usuario = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'telefone' => $request->telefone,
            'cpf_cnpj' => $request->cpf_cnpj,
            'data_nascimento' => $request->data_nascimento,
            'genero' => $request->genero,
            'cargo' => $request->cargo,
            'cep' => $request->cep,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'bio' => $request->bio,
            'avatar' => $avatarPath,
            'permissoes' => $permissoes,
            'two_factor_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'usuario_id' => $usuario->id,
        ]);
    }

    /**
     * Atualiza um usuário existente (AJAX)
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'role' => 'required|string|in:admin,gerente,atendente,financeiro,cliente',
            'status' => 'required|string|in:ativo,inativo',
            'password' => 'nullable|string|min:8',
            'telefone' => 'nullable|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'bio' => 'nullable|string|max:1000',
            'permissoes' => 'nullable|array',
            'avatar_upload' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
        ]);

        // Previne que o próprio usuário desative sua conta ou retire seu próprio perfil de admin
        if ($usuario->id === auth()->id()) {
            if ($request->status === 'inativo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode desativar sua própria conta de usuário conectado.',
                ], 422);
            }
            if ($usuario->role === 'admin' && $request->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode revogar o seu próprio perfil de Administrador.',
                ], 422);
            }
        }

        $dados = [
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'role' => $request->role,
            'status' => $request->status,
            'telefone' => $request->telefone,
            'cpf_cnpj' => $request->cpf_cnpj,
            'data_nascimento' => $request->data_nascimento,
            'genero' => $request->genero,
            'cargo' => $request->cargo,
            'cep' => $request->cep,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'bio' => $request->bio,
            'permissoes' => $request->permissoes ?? [],
        ];

        if ($request->filled('password')) {
            $dados['password'] = Hash::make($request->password);
        }

        // Remoção de avatar
        if ($request->input('remover_avatar') == '1') {
            if ($usuario->avatar && file_exists(public_path($usuario->avatar))) {
                @unlink(public_path($usuario->avatar));
            }
            $dados['avatar'] = null;
        }

        // Upload de novo avatar
        if ($request->hasFile('avatar_upload')) {
            $dest = public_path('uploads/avatares');
            if (!File::isDirectory($dest)) {
                File::makeDirectory($dest, 0755, true, true);
            }
            if ($usuario->avatar && file_exists(public_path($usuario->avatar))) {
                @unlink(public_path($usuario->avatar));
            }
            $file = $request->file('avatar_upload');
            $fileName = 'avatar_' . $usuario->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dest, $fileName);
            $dados['avatar'] = '/uploads/avatares/' . $fileName;
        }

        $usuario->update($dados);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
        ]);
    }

    /**
     * Alterna o status ativo/inativo do usuário (Toggle AJAX)
     */
    public function toggleStatus($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode alternar o status da sua própria conta.',
            ], 422);
        }

        $novoStatus = $usuario->status === 'ativo' ? 'inativo' : 'ativo';
        $usuario->update(['status' => $novoStatus]);

        return response()->json([
            'success' => true,
            'novo_status' => $novoStatus,
            'message' => "Usuário '{$usuario->name}' agora está " . ($novoStatus === 'ativo' ? 'Ativo' : 'Inativo') . '.',
        ]);
    }

    /**
     * Exclui um usuário do sistema (AJAX com SweetAlert2)
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ação bloqueada: Você não pode excluir sua própria conta conectada.',
            ], 422);
        }

        // Se for o único admin do sistema, bloqueia exclusão
        if ($usuario->role === 'admin') {
            $totalAdmins = User::where('role', 'admin')->where('status', 'ativo')->count();
            if ($totalAdmins <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é permitido excluir o único administrador ativo do sistema.',
                ], 422);
            }
        }

        if ($usuario->avatar && file_exists(public_path($usuario->avatar))) {
            @unlink(public_path($usuario->avatar));
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso.',
        ]);
    }

    /**
     * Redefine a senha do usuário diretamente pelo administrador (AJAX)
     */
    public function resetPassword(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'nova_senha' => 'required|string|min:8|confirmed',
        ], [
            'nova_senha.required' => 'Digite a nova senha.',
            'nova_senha.min' => 'A senha deve possuir no mínimo 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        $usuario->update([
            'password' => Hash::make($request->nova_senha),
        ]);

        return response()->json([
            'success' => true,
            'message' => "A senha de {$usuario->name} foi alterada com sucesso!",
        ]);
    }
}
