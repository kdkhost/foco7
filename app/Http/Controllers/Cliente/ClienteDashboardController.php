<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use App\Models\Agendamento;
use App\Models\ServicoEstetica;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ClienteDashboardController extends Controller
{
    /**
     * Exibe o painel exclusivo da Área do Cliente
     */
    public function index()
    {
        $user = auth()->user();

        // Faturas do cliente com Eager Loading
        $faturas = Fatura::with('plano')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $faturasPendentes = $faturas->where('status', 'pendente');
        $faturasPagas = $faturas->where('status', 'pago');

        // Agendamentos do cliente (rastreamento e estética)
        $agendamentos = Agendamento::with('servicoEstetica')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('cliente_telefone', $user->telefone)
                  ->orWhere('cliente_email', $user->email);
            })
            ->orderBy('data_hora_inicio', 'desc')
            ->get();

        $servicosEstetica = ServicoEstetica::where('ativo', true)->orderBy('ordem')->get();

        return view('cliente.dashboard', compact('user', 'faturas', 'faturasPendentes', 'faturasPagas', 'agendamentos', 'servicosEstetica'));
    }

    /**
     * Permite que o cliente solicite agendamento (estética ou suporte/instalação)
     */
    public function solicitarAgendamento(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'categoria' => 'required|string|in:rastreamento,estetica',
            'servico_estetica_id' => 'nullable|exists:servicos_estetica,id',
            'tipo_servico' => 'nullable|string|max:100',
            'veiculo_modelo' => 'required|string|max:100',
            'veiculo_placa' => 'nullable|string|max:20',
            'data_hora_inicio' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $categoria = $validated['categoria'];
        $tipoServico = $validated['tipo_servico'] ?? ($categoria === 'estetica' ? 'Estética Automotiva' : 'Suporte Técnico');
        $valor = null;

        if ($categoria === 'estetica' && !empty($validated['servico_estetica_id'])) {
            $servico = ServicoEstetica::find($validated['servico_estetica_id']);
            if ($servico) {
                $tipoServico = $servico->nome;
                $valor = $servico->preco;
            }
        }

        $agendamento = Agendamento::create([
            'user_id' => $user->id,
            'categoria' => $categoria,
            'servico_estetica_id' => $validated['servico_estetica_id'] ?? null,
            'cliente_nome' => $user->name,
            'cliente_telefone' => $user->telefone ?? '',
            'cliente_email' => $user->email,
            'tipo_servico' => $tipoServico,
            'valor' => $valor,
            'veiculo_modelo' => $validated['veiculo_modelo'],
            'veiculo_placa' => $validated['veiculo_placa'] ?? null,
            'data_hora_inicio' => $validated['data_hora_inicio'],
            'observacoes' => $validated['observacoes'] ?? 'Solicitado via Painel do Cliente.',
            'status' => 'pendente',
            'cor_calendario' => $categoria === 'estetica' ? '#1e293b' : '#28a745',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agendamento solicitado com sucesso! Entraremos em contato para confirmar.',
            'agendamento_id' => $agendamento->id,
        ]);
    }

    /**
     * Permite que o cliente cancele seu agendamento pendente ou confirmado
     */
    public function cancelarAgendamento(Request $request, $id)
    {
        $user = auth()->user();

        $agendamento = Agendamento::where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('cliente_telefone', $user->telefone)
                  ->orWhere('cliente_email', $user->email);
            })
            ->firstOrFail();

        if (in_array($agendamento->status, ['cancelado', 'concluido'])) {
            return response()->json([
                'success' => false,
                'message' => 'Este agendamento já se encontra finalizado ou cancelado.',
            ], 422);
        }

        $agendamento->status = 'cancelado';
        $agendamento->save();

        // Notificação automática para a administração
        Notificacao::create([
            'titulo' => 'Agendamento Cancelado pelo Cliente',
            'mensagem' => "O cliente {$user->name} cancelou o agendamento de '{$agendamento->tipo_servico}' agendado para " . $agendamento->data_hora_inicio->format('d/m/Y \à\s H:i') . ".",
            'tipo' => 'agendamento',
            'icone' => 'fa-calendar-times',
            'cor' => 'danger',
            'url_destino' => route('admin.agendamentos.index'),
            'lida' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso. O horário foi liberado.',
        ]);
    }

    /**
     * Exibe a tela de Perfil do Cliente
     */
    public function perfil()
    {
        $user = auth()->user();
        return view('cliente.perfil', compact('user'));
    }

    /**
     * Atualiza os dados cadastrais do cliente via AJAX
     */
    public function salvarDados(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'telefone' => 'required|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:30',
            'cep' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:10',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'telefone.required' => 'O telefone/WhatsApp é obrigatório.',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Seus dados cadastrais foram atualizados com sucesso!',
        ]);
    }

    /**
     * Atualiza a senha do cliente de forma segura via AJAX
     */
    public function alterarSenha(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:6|confirmed',
        ], [
            'senha_atual.required' => 'Informe a sua senha atual.',
            'nova_senha.required' => 'Informe a nova senha.',
            'nova_senha.min' => 'A nova senha deve possuir pelo menos 6 caracteres.',
            'nova_senha.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        if (!Hash::check($request->senha_atual, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'A senha atual informada está incorreta.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->nova_senha),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sua senha foi alterada com sucesso!',
        ]);
    }

    /**
     * Atualiza ou remove o avatar do cliente via AJAX
     */
    public function salvarAvatar(Request $request)
    {
        $user = auth()->user();

        if ($request->has('remover_avatar') && $request->remover_avatar == '1') {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar removido com sucesso!',
                'avatar_url' => null,
            ]);
        }

        $request->validate([
            'avatar_upload' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'avatar_upload.required' => 'Selecione ou solte uma imagem para o avatar.',
            'avatar_upload.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'avatar_upload.mimes' => 'Formatos permitidos: JPG, PNG ou WEBP.',
            'avatar_upload.max' => 'O tamanho máximo da imagem é de 5MB.',
        ]);

        $destinationPath = public_path('uploads/avatares');
        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        if ($user->avatar && file_exists(public_path($user->avatar))) {
            @unlink(public_path($user->avatar));
        }

        $file = $request->file('avatar_upload');
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);

        $avatarPath = '/uploads/avatares/' . $fileName;
        $user->update(['avatar' => $avatarPath]);

        return response()->json([
            'success' => true,
            'message' => 'Foto de perfil atualizada com sucesso!',
            'avatar_url' => asset($avatarPath),
        ]);
    }
}
