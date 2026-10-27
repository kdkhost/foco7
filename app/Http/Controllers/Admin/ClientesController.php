<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientesController extends Controller
{
    /**
     * Listagem de Clientes com busca e métricas
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'cliente')->withCount(['faturas', 'agendamentos']);

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%")
                    ->orWhere('telefone', 'like', "%{$busca}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $clientes = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalClientes = User::where('role', 'cliente')->count();
        $clientesAtivos = User::where('role', 'cliente')->where('status', 'ativo')->count();
        $clientesBloqueados = User::where('role', 'cliente')->where('status', 'inativo')->count();

        return view('admin.clientes.index', compact('clientes', 'totalClientes', 'clientesAtivos', 'clientesBloqueados'));
    }

    /**
     * Retorna dados do cliente em JSON para edição
     */
    public function obter($id)
    {
        $cliente = User::where('role', 'cliente')->findOrFail($id);

        return response()->json([
            'success' => true,
            'cliente' => $cliente,
        ]);
    }

    /**
     * Cria ou edita os dados cadastrais do cliente
     */
    public function salvar(Request $request)
    {
        $id = $request->input('cliente_id');

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'telefone' => 'required|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:30',
            'cep' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
            'status' => 'required|in:ativo,inativo',
            'password' => $id ? 'nullable|min:6' : 'required|min:6',
        ]);

        if ($id) {
            $cliente = User::where('role', 'cliente')->findOrFail($id);
            $cliente->name = $validated['name'];
            $cliente->email = $validated['email'];
            $cliente->telefone = $validated['telefone'];
            $cliente->cpf_cnpj = $validated['cpf_cnpj'];
            $cliente->cep = $validated['cep'];
            $cliente->endereco = $validated['endereco'];
            $cliente->numero = $validated['numero'];
            $cliente->bairro = $validated['bairro'];
            $cliente->cidade = $validated['cidade'];
            $cliente->estado = $validated['estado'];
            $cliente->status = $validated['status'];

            if (! empty($validated['password'])) {
                $cliente->password = Hash::make($validated['password']);
            }

            $cliente->save();
            $mensagem = 'Dados do cliente atualizados com sucesso!';
        } else {
            $dados = $validated;
            $dados['role'] = 'cliente';
            $dados['password'] = Hash::make($validated['password']);
            User::create($dados);
            $mensagem = 'Novo cliente cadastrado com sucesso!';
        }

        return response()->json([
            'success' => true,
            'message' => $mensagem,
        ]);
    }

    /**
     * Redefinição rápida de senha
     */
    public function alterarSenha(Request $request, $id)
    {
        $request->validate([
            'nova_senha' => 'required|string|min:6',
        ]);

        $cliente = User::where('role', 'cliente')->findOrFail($id);
        $cliente->password = Hash::make($request->input('nova_senha'));
        $cliente->save();

        return response()->json([
            'success' => true,
            'message' => 'Senha do cliente redefinida com sucesso!',
        ]);
    }

    /**
     * Exclui um cliente
     */
    public function excluir($id)
    {
        $cliente = User::where('role', 'cliente')->findOrFail($id);
        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente removido com sucesso!',
        ]);
    }

    /**
     * Inicia o Acesso Supervisionado ao painel do cliente
     */
    public function supervisionar(Request $request, $id)
    {
        $admin = Auth::user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $cliente = User::where('role', 'cliente')->findOrFail($id);

        if ($cliente->status !== 'ativo') {
            return redirect()->back()->with('error', 'Não é possível supervisionar uma conta inativa ou bloqueada.');
        }

        // Salva a URL de retorno (a página exata onde o administrador estava)
        $retorno = $request->input('retorno') ?: url()->previous() ?: route('admin.clientes.index');
        if (str_contains($retorno, 'supervisionar')) {
            $retorno = route('admin.clientes.index');
        }

        session([
            'impersonator_id' => $admin->id,
            'impersonator_nome' => $admin->name,
            'impersonator_email' => $admin->email,
            'impersonator_previous_url' => $retorno,
            'impersonator_target_id' => $cliente->id,
            'impersonator_target_nome' => $cliente->name,
            'impersonator_started_at' => now()->toIso8601String(),
        ]);

        Auth::login($cliente);

        return redirect()->route('cliente.dashboard')
            ->with('info', "Acesso supervisionado ativo na conta de {$cliente->name}. Para retornar ao painel administrativo exatamente onde estava, utilize o botão na barra superior.");
    }

    /**
     * Encerra o Acesso Supervisionado e retorna o administrador à página anterior exata
     */
    public function voltarSupervisao(Request $request)
    {
        $impersonatorId = session('impersonator_id');
        $previousUrl = session('impersonator_previous_url', route('admin.clientes.index'));

        if (! $impersonatorId) {
            return redirect()->route('admin.dashboard');
        }

        $admin = User::where('role', 'admin')->find($impersonatorId);

        if ($admin) {
            Auth::login($admin);
        }

        session()->forget([
            'impersonator_id',
            'impersonator_nome',
            'impersonator_email',
            'impersonator_previous_url',
            'impersonator_target_id',
            'impersonator_target_nome',
            'impersonator_started_at',
        ]);

        return redirect($previousUrl)
            ->with('success', 'Acesso supervisionado encerrado com sucesso! Você retornou ao painel administrativo exatamente onde estava.');
    }
}
