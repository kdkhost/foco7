<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class PerfilController extends Controller
{
    /**
     * Exibe a página do perfil do usuário autenticado
     */
    public function index()
    {
        $user = auth()->user();
        return view('admin.perfil.index', compact('user'));
    }

    /**
     * Atualiza os dados cadastrais do perfil via AJAX
     */
    public function salvarDados(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:20',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cpf_cnpj' => $request->cpf_cnpj,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seus dados foram atualizados com sucesso!',
        ]);
    }

    /**
     * Atualiza o avatar do usuário com arrasta e solta via AJAX
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

        // Remove avatar anterior se existir
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

    /**
     * Altera a senha do usuário com confirmação via AJAX
     */
    public function alterarSenha(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:8|confirmed',
        ], [
            'senha_atual.required' => 'Digite sua senha atual.',
            'nova_senha.required' => 'Digite a nova senha.',
            'nova_senha.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
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
}
