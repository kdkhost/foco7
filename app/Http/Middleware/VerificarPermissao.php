<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarPermissao
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permissao): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
            }
            return redirect()->route('login');
        }

        // Se o usuário tem a permissão necessária
        if ($user->temPermissao($permissao)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado: Você não possui permissão para acessar esta seção ou executar esta ação.',
            ], 403);
        }

        return redirect()->route('admin.dashboard')->with('error', 'Acesso negado: Você não possui permissão para acessar este módulo.');
    }
}
