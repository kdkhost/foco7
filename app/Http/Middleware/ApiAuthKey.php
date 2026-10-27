<?php

namespace App\Http\Middleware;

use App\Models\CmsSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken()
            ?: $request->header('X-API-Key')
            ?: $request->header('X-Api-Token')
            ?: $request->input('api_key');

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado. Cabeçalho Authorization (Bearer) ou X-API-Key ausente.',
                'code' => 401,
            ], 401);
        }

        // 1. Chave mestre de API configurada no sistema (CMS Setting)
        $masterApiKey = CmsSetting::get('api_rest_secret_token');
        if (empty($masterApiKey)) {
            // Gera e persiste automaticamente uma chave de alta entropia caso ainda não configurada
            $masterApiKey = hash('sha256', config('app.key').'foco7_api_secret_v1');
            CmsSetting::set('api_rest_secret_token', $masterApiKey, 'api');
        }

        if (hash_equals($masterApiKey, (string) $token)) {
            return $next($request);
        }

        // 2. Chave de API individual de usuário administrador ou parceiro
        $user = User::where('status', 'ativo')
            ->where(function ($query) use ($token) {
                $query->where('api_token', $token)
                    ->orWhere('remember_token', $token);
            })
            ->first();

        if ($user) {
            $request->setUserResolver(fn () => $user);

            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Chave de API inválida ou revogada.',
            'code' => 401,
        ], 401);
    }
}
