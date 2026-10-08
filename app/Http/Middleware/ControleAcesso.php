<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ControleAcesso
{
    /**
     * Handle an incoming request.
     *
     * @param string $roleExpected 'admin' ou 'cliente'
     */
    public function handle(Request $request, Closure $next, string $roleExpected = 'admin'): Response
    {
        if (!auth()->check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sessão expirada. Por favor, realize o login novamente.',
                    'redirect' => route('login'),
                ], 401);
            }

            return redirect()->route('login')->with('warning', 'Acesso restrito. Faça login para continuar.');
        }

        $user = auth()->user();

        // Verificar status do usuário
        if ($user->status !== 'ativo') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Sua conta está inativa ou bloqueada. Contate o suporte.');
        }

        // Se a rota exige 'admin' mas o usuário é 'cliente'
        if ($roleExpected === 'admin' && !$user->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado. Esta área é restrita a administradores.',
                ], 403);
            }

            return redirect()->route('cliente.dashboard')->with('error', 'Acesso não autorizado para perfil de cliente.');
        }

        // Se a rota exige 'cliente' e o usuário é 'admin', permitir (admin tem visão global)
        return $next($request);
    }
}
