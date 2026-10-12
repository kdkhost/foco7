<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * Exibe o formulário para solicitação de recuperação de senha
     */
    public function esqueciSenhaForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envia o link seguro de recuperação de senha para o e-mail do usuário
     */
    public function enviarLinkRecuperacao(Request $request)
    {
        if (!empty($request->input('_hp_security_check'))) {
            return response()->json(['success' => false, 'message' => 'Requisição bloqueada por segurança.'], 403);
        }

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Por favor, informe seu endereço de e-mail.',
            'email.email' => 'Informe um formato de e-mail válido.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $throttleKey = 'password-reset|' . Str::transliterate($email . '|' . $request->ip());

        // Proteção contra brute-force e flooding de e-mails (máximo 3 tentativas em 5 minutos)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Muitas solicitações recentes. Por motivos de segurança, aguarde {$seconds} segundos antes de tentar novamente.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }

            return back()->withErrors(['email' => $msg]);
        }

        RateLimiter::hit($throttleKey, 300);

        $user = User::where('email', $email)->first();

        // Sempre retorna mensagem amigável para evitar enumeração de usuários
        $msgSucesso = 'Se o e-mail informado estiver cadastrado em nosso sistema, um link seguro para redefinição de senha foi enviado com sucesso!';

        if ($user) {
            $plainToken = Str::random(64);

            // Grava o token seguro na tabela nativa do Laravel
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($plainToken),
                    'created_at' => Carbon::now(),
                ]
            );

            $linkRedefinir = route('password.reset', [
                'token' => $plainToken,
                'email' => $user->email,
            ]);

            try {
                EmailTemplateService::enviar(
                    'recuperacao_senha',
                    $user->email,
                    $user->name,
                    [
                        'nome' => $user->name,
                        'email' => $user->email,
                        'link_redefinir' => $linkRedefinir,
                        'expiracao' => '60 minutos',
                        'ip_solicitante' => $request->ip(),
                    ]
                );
            } catch (\Throwable $e) {
                \Log::error('Erro ao enviar e-mail de recuperação de senha: ' . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msgSucesso,
            ]);
        }

        return back()->with('status', $msgSucesso);
    }

    /**
     * Exibe o formulário de redefinição de senha a partir do token
     */
    public function redefinirSenhaForm(Request $request, string $token)
    {
        $email = $request->query('email', '');

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Processa a redefinição e atualização da nova senha
     */
    public function atualizarSenha(Request $request)
    {
        if (!empty($request->input('_hp_security_check'))) {
            return response()->json(['success' => false, 'message' => 'Requisição bloqueada por segurança.'], 403);
        }

        $throttleTokenKey = 'pwd-update|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleTokenKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleTokenKey);
            $msg = "Muitas tentativas de redefinição de senha a partir deste IP. Aguarde {$seconds} segundos por segurança.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'Token de redefinição inválido ou ausente.',
            'email.required' => 'Informe o endereço de e-mail.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não coincide com a nova senha digitada.',
        ]);

        RateLimiter::hit($throttleTokenKey, 60);

        $email = strtolower(trim($request->input('email')));
        $token = $request->input('token');

        $registro = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$registro) {
            $msgErro = 'Solicitação de redefinição não encontrada ou já utilizada. Solicite um novo link.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msgErro], 422);
            }
            return back()->withErrors(['email' => $msgErro]);
        }

        // Verifica se o token expirou (mais de 60 minutos)
        if (Carbon::parse($registro->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            $msgErro = 'Este link de redefinição expirou por motivos de segurança. Solicite um novo link.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msgErro], 422);
            }
            return back()->withErrors(['email' => $msgErro]);
        }

        // Validação criptográfica do token
        if (!Hash::check($token, $registro->token)) {
            $msgErro = 'Token de validação inválido. Verifique o link recebido ou solicite novamente.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msgErro], 422);
            }
            return back()->withErrors(['email' => $msgErro]);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            $msgErro = 'Usuário não localizado no sistema.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msgErro], 404);
            }
            return back()->withErrors(['email' => $msgErro]);
        }

        // Atualiza a nova senha com hash Bcrypt
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Invalida o token utilizado
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $msgSucesso = 'Sua senha foi redefinida com sucesso! Você já pode acessar sua conta.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msgSucesso,
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', $msgSucesso);
    }
}
