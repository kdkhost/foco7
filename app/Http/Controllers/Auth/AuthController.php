<?php

namespace App\Http\Controllers\Auth;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Informe seu endereço de e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'Informe sua senha de acesso.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Muitas tentativas de login incorretas. Tente novamente em {$seconds} segundos.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }

            return back()->withErrors(['email' => $msg]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status !== 'ativo') {
                Auth::logout();
                $msg = 'Sua conta está inativa ou bloqueada. Contate a administração.';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }

                return back()->withErrors(['email' => $msg]);
            }

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

        RateLimiter::hit($throttleKey);

        $msg = 'E-mail ou senha incorretos.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        return back()->withErrors(['email' => $msg])->withInput($request->only('email'));
    }

    /**
     * Encerra a sessão do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sessão encerrada com sucesso.');
    }
}
