<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
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
     * Atualiza os dados cadastrais e endereço completo do perfil via AJAX
     */
    public function salvarDados(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:30',
            'cpf_cnpj' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário.',
        ]);

        $user->update([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'telefone' => $request->telefone,
            'cpf_cnpj' => $request->cpf_cnpj,
            'data_nascimento' => $request->data_nascimento,
            'genero' => $request->genero,
            'cargo' => $request->cargo,
            'bio' => $request->bio,
            'cep' => $request->cep,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seus dados pessoais e endereço foram atualizados com sucesso!',
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

    /**
     * Inicia o fluxo de ativação do 2FA gerando chave secreta e QR Code (AJAX)
     */
    public function iniciar2fa(Request $request)
    {
        $user = auth()->user();

        $secret = TwoFactorAuthService::generateSecretKey(16);
        $otpAuthUrl = TwoFactorAuthService::getOtpAuthUrl($user->email, $secret, 'Foco 7');
        $qrCodeUrl = TwoFactorAuthService::getQrCodeImageUrl($otpAuthUrl, 240);

        // Armazena provisoriamente na sessão para confirmação
        session(['temp_2fa_secret' => $secret]);

        return response()->json([
            'success' => true,
            'secret' => $secret,
            'secret_formatted' => chunk_split($secret, 4, ' '),
            'otpauth_url' => $otpAuthUrl,
            'qrcode_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirma o código do aplicativo autenticador e ativa o 2FA (AJAX)
     */
    public function confirmar2fa(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'code' => 'required|string|min:6|max:8',
        ], [
            'code.required' => 'Digite o código de 6 dígitos gerado no aplicativo.',
            'code.min' => 'O código de autenticação possui 6 dígitos.',
        ]);

        $secret = session('temp_2fa_secret') ?? $request->secret;

        if (empty($secret)) {
            return response()->json([
                'success' => false,
                'message' => 'A sessão de configuração expirou. Inicie a configuração novamente.',
            ], 422);
        }

        if (!TwoFactorAuthService::verifyCode($secret, $request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido ou expirado. Verifique o relógio do seu celular e tente novamente.',
            ], 422);
        }

        // Gera 8 códigos de recuperação
        $recoveryCodes = TwoFactorAuthService::generateRecoveryCodes(8);

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
            'two_factor_confirmed_at' => now(),
            'two_factor_enabled' => true,
        ]);

        session()->forget('temp_2fa_secret');

        return response()->json([
            'success' => true,
            'message' => 'Autenticação em Duas Etapas (2FA) ativada com sucesso!',
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    /**
     * Desativa o 2FA mediante confirmação de senha ou código (AJAX)
     */
    public function desativar2fa(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'senha_ou_codigo' => 'required|string',
        ], [
            'senha_ou_codigo.required' => 'Informe sua senha atual ou o código do autenticador para confirmar.',
        ]);

        $input = trim($request->senha_ou_codigo);

        $validoPorSenha = Hash::check($input, $user->password);
        $validoPorTotp = !empty($user->two_factor_secret) && TwoFactorAuthService::verifyCode($user->two_factor_secret, $input);

        if (!$validoPorSenha && !$validoPorTotp) {
            return response()->json([
                'success' => false,
                'message' => 'Senha ou código incorreto. A desativação não foi autorizada.',
            ], 422);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_enabled' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'A Autenticação em Duas Etapas (2FA) foi desativada.',
        ]);
    }

    /**
     * Visualiza os códigos de recuperação ativos (AJAX)
     */
    public function visualizarRecoveryCodes(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Informe sua senha atual para visualizar os códigos de recuperação.',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.',
            ], 422);
        }

        $rawCodes = json_decode($user->two_factor_recovery_codes ?? '[]', true);
        $codes = [];

        if (is_array($rawCodes)) {
            foreach ($rawCodes as $item) {
                if (is_array($item)) {
                    $codes[] = [
                        'code' => $item['code'] ?? '',
                        'used' => $item['used'] ?? false,
                    ];
                } else {
                    $codes[] = [
                        'code' => $item,
                        'used' => false,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'codes' => $codes,
        ]);
    }

    /**
     * Regenera os 8 códigos de recuperação (AJAX)
     */
    public function regenerarRecoveryCodes(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Informe sua senha atual para regenerar os códigos.',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha incorreta.',
            ], 422);
        }

        $newCodes = TwoFactorAuthService::generateRecoveryCodes(8);

        $user->update([
            'two_factor_recovery_codes' => json_encode($newCodes),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Novos códigos de recuperação gerados com sucesso!',
            'codes' => $newCodes,
        ]);
    }
}
