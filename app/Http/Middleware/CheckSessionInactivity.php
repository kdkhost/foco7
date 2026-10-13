<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CmsSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionInactivity
{
    /**
     * Monitora e desconecta usuários por tempo sem interação para prevenir sequestro de sessão.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Ignora rotas de logout e autenticação
            if ($request->is('logout') || $request->is('login') || $request->is('auth/2fa*')) {
                return $next($request);
            }

            $timeoutMinutos = (int) CmsSetting::get('sessao_inatividade_minutos', 30);

            if ($timeoutMinutos > 0) {
                $agora = now()->timestamp;
                $ultimaAtividade = $request->session()->get('foco7_ultima_atividade');

                if ($ultimaAtividade && ($agora - $ultimaAtividade) > ($timeoutMinutos * 60)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $mensagem = "Sua sessão foi desconectada após {$timeoutMinutos} minutos de inatividade para proteger seus dados contra roubo de sessão.";

                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'session_expired' => true,
                            'message' => $mensagem,
                            'redirect' => route('login', ['motivo' => 'inatividade']),
                        ], 401);
                    }

                    return redirect()->route('login', ['motivo' => 'inatividade'])
                        ->with('warning', $mensagem);
                }

                $request->session()->put('foco7_ultima_atividade', $agora);
            }
        }

        return $next($request);
    }
}
