<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\TwoFactorAuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Exibe a tela de login unificada (Admin e Cliente)
     */
    public function loginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('cliente.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Processa a autenticação do usuário com proteção contra brute-force
     */
    public function login(Request $request)
    {
        // Proteção Honeypot contra robôs de força bruta
        if (!empty($request->input('_hp_security_check'))) {
            return response()->json(['success' => false, 'message' => 'Requisição bloqueada por segurança.'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Informe seu endereço de e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'Informe sua senha de acesso.',
        ]);

        $email = Str::lower(trim($request->input('email')));
        $throttleKey = 'login|' . Str::transliterate($email . '|' . $request->ip());
        $ipThrottleKey = 'login-ip|' . $request->ip();

        // Camada 1: Bloqueio por e-mail + IP (máx 5 tentativas)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Muitas tentativas de login incorretas. Tente novamente em {$seconds} segundos.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }

            return back()->withErrors(['email' => $msg]);
        }

        // Camada 2: Bloqueio por IP global contra varredura de múltiplos e-mails (máx 10 tentativas)
        if (RateLimiter::tooManyAttempts($ipThrottleKey, 10)) {
            $seconds = RateLimiter::availableIn($ipThrottleKey);
            $msg = "Muitas tentativas a partir deste IP. Aguarde {$seconds} segundos por segurança.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }

            return back()->withErrors(['email' => $msg]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            RateLimiter::clear($throttleKey);
            RateLimiter::clear($ipThrottleKey);

            $user = Auth::user();

            if ($user->status !== 'ativo') {
                Auth::logout();
                $msg = 'Sua conta está inativa ou bloqueada. Contate a administração.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }

                return back()->withErrors(['email' => $msg]);
            }

            // Se o usuário possui 2FA ativo, exige o desafio do autenticador
            if ($user->hasTwoFactor()) {
                Auth::logout();
                $request->session()->put('login.2fa.user_id', $user->id);
                $request->session()->put('login.2fa.remember', $remember);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'requires_2fa' => true,
                        'message' => 'Autenticação em duas etapas requerida.',
                        'redirect' => route('login.2fa'),
                    ]);
                }

                return redirect()->route('login.2fa');
            }

            $request->session()->regenerate();

            $redirectUrl = $user->isAdmin()
                ? route('admin.dashboard')
                : route('cliente.dashboard');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login realizado com sucesso! Redirecionando...',
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->intended($redirectUrl);
        }

        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($ipThrottleKey, 60);

        $msg = 'E-mail ou senha incorretos.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        return back()->withErrors(['email' => $msg])->withInput($request->only('email'));
    }

    /**
     * Exibe a tela de cadastro público de novos clientes
     */
    public function cadastroForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('cliente.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Processa o cadastro público com proteção anti-bruteforce e honeypot
     */
    public function cadastro(Request $request)
    {
        if (!empty($request->input('_hp_security_check'))) {
            return response()->json(['success' => false, 'message' => 'Requisição bloqueada por segurança.'], 403);
        }

        $ipThrottleKey = 'register-ip|' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($ipThrottleKey);
            $msg = "Muitas tentativas de cadastro recentes. Aguarde {$seconds} segundos por segurança.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:120|unique:users,email',
            'telefone' => 'required|string|max:30',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema. Faça login para continuar.',
            'telefone.required' => 'Informe seu número de WhatsApp/Telefone.',
            'password.required' => 'Crie uma senha de acesso.',
            'password.min' => 'A senha deve conter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        RateLimiter::hit($ipThrottleKey, 300);

        $user = \App\Models\User::create([
            'name' => trim($request->input('name')),
            'email' => strtolower(trim($request->input('email'))),
            'telefone' => trim($request->input('telefone')),
            'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
            'role' => 'cliente',
            'status' => 'ativo',
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cadastro realizado com sucesso! Bem-vindo à Foco 7.',
                'redirect' => route('cliente.dashboard'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->route('cliente.dashboard')->with('success', 'Cadastro realizado com sucesso!');
    }

    /**
     * Encerra a sessão do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $motivo = $request->input('motivo', $request->query('motivo'));
        $msg = ($motivo === 'inatividade')
            ? 'Sua sessão foi encerrada por inatividade para sua segurança. Faça login novamente.'
            : 'Sessão encerrada com sucesso.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'redirect' => route('login', $motivo ? ['motivo' => $motivo] : []),
            ]);
        }

        return redirect()->route('login', $motivo ? ['motivo' => $motivo] : [])
            ->with($motivo === 'inatividade' ? 'warning' : 'success', $msg);
    }

    /**
     * Atualiza a atividade da sessão ativa (Ping de Heartbeat)
     */
    public function pingSessao(Request $request)
    {
        if (Auth::check()) {
            $request->session()->put('foco7_ultima_atividade', now()->timestamp);
            return response()->json([
                'success' => true,
                'authenticated' => true,
                'timestamp' => now()->timestamp,
            ]);
        }

        return response()->json([
            'success' => false,
            'authenticated' => false,
            'redirect' => route('login', ['motivo' => 'inatividade']),
        ], 401);
    }

    /**
     * Retorna o status atual de autenticação e dados básicos do usuário
     */
    public function statusAuth(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                    'role' => $user->role,
                ],
            ]);
        }

        return response()->json(['authenticated' => false]);
    }

    /**
     * Login rápido via AJAX para o fluxo de agendamento do site
     */
    public function loginRapidoAjax(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Informe seu e-mail cadastrado.',
            'email.email' => 'Informe um formato válido de e-mail.',
            'password.required' => 'Informe sua senha de acesso.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Muitas tentativas de login incorretas. Tente novamente em {$seconds} segundos.",
            ], 429);
        }

        if (Auth::attempt($request->only('email', 'password'), true)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status !== 'ativo') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Sua conta está desativada ou bloqueada.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Autenticado com sucesso!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telefone' => $user->telefone,
                ],
            ]);
        }

        RateLimiter::hit($throttleKey);

        return response()->json([
            'success' => false,
            'message' => 'E-mail ou senha incorretos.',
        ], 422);
    }

    /**
     * Cadastro rápido de cliente via AJAX para o fluxo de agendamento do site
     */
    public function cadastroRapidoAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:120|unique:users,email',
            'telefone' => 'required|string|max:30',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado em nosso sistema. Faça login para continuar.',
            'telefone.required' => 'Informe seu número de WhatsApp/Telefone.',
            'password.required' => 'Crie uma senha de acesso.',
            'password.min' => 'A senha deve conter no mínimo 6 caracteres.',
        ]);

        $user = \App\Models\User::create([
            'name' => trim($request->input('name')),
            'email' => strtolower(trim($request->input('email'))),
            'telefone' => trim($request->input('telefone')),
            'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
            'role' => 'cliente',
            'status' => 'ativo',
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Cadastro realizado com sucesso! Você já está conectado.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telefone' => $user->telefone,
            ],
        ]);
    }

    /**
     * Exibe a tela de desafio da autenticação em duas etapas (2FA)
     */
    public function twoFactorChallengeForm(Request $request)
    {
        if (!session()->has('login.2fa.user_id')) {
            return redirect()->route('login');
        }

        $userId = session('login.2fa.user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->forget(['login.2fa.user_id', 'login.2fa.remember']);
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', compact('user'));
    }

    /**
     * Processa a verificação do código 2FA ou código de recuperação
     */
    public function twoFactorChallengeVerify(Request $request)
    {
        if (!session()->has('login.2fa.user_id')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.', 'redirect' => route('login')], 401);
            }
            return redirect()->route('login');
        }

        $userId = session('login.2fa.user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->forget(['login.2fa.user_id', 'login.2fa.remember']);
            return redirect()->route('login');
        }

        $throttleKey = '2fa|' . $user->id . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Muitas tentativas de verificação. Aguarde {$seconds} segundos.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['code' => $msg]);
        }

        $isRecovery = $request->boolean('usar_recuperacao') || $request->filled('recovery_code');
        $valido = false;

        if ($isRecovery) {
            $request->validate([
                'recovery_code' => 'required|string',
            ], [
                'recovery_code.required' => 'Informe o código de recuperação de emergência.',
            ]);

            $valido = TwoFactorAuthService::verifyAndConsumeRecoveryCode($user, $request->recovery_code);
        } else {
            $request->validate([
                'code' => 'required|string|min:6|max:8',
            ], [
                'code.required' => 'Digite o código de 6 dígitos gerado no seu aplicativo autenticador.',
            ]);

            $valido = TwoFactorAuthService::verifyCode($user->two_factor_secret, $request->code);
        }

        if (!$valido) {
            RateLimiter::hit($throttleKey, 60);
            $erroMsg = $isRecovery ? 'Código de recuperação inválido ou já utilizado.' : 'Código de 6 dígitos incorreto ou expirado.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $erroMsg], 422);
            }

            return back()->withErrors(['code' => $erroMsg]);
        }

        RateLimiter::clear($throttleKey);

        $remember = session('login.2fa.remember', false);
        Auth::login($user, $remember);
        session()->forget(['login.2fa.user_id', 'login.2fa.remember']);
        $request->session()->regenerate();

        $redirectUrl = $user->isAdmin() ? route('admin.dashboard') : route('cliente.dashboard');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Autenticação de 2 fatores confirmada com sucesso!',
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended($redirectUrl);
    }

    /**
     * Cancela o fluxo de 2FA e volta para a tela de login
     */
    public function cancelTwoFactor(Request $request)
    {
        session()->forget(['login.2fa.user_id', 'login.2fa.remember']);
        return redirect()->route('login');
    }
}
