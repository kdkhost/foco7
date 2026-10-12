<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\GeoIpService;

class GeoRestricao
{
    /**
     * Intercepta a requisição e valida a origem geográfica (Apenas Américas)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se estiver em ambiente de testes e não houver simulação explícita de país, permite prosseguir
        if (app()->environment('testing') && !$request->hasHeader('CF-IPCountry') && !$request->has('simular_ip_estrangeiro')) {
            return $next($request);
        }

        $analise = GeoIpService::deveBloquear($request);

        if ($analise['bloquear']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso Restrito por Localização: Plataforma autorizada exclusivamente para as Américas.',
                    'codigo' => 403,
                ], 403);
            }

            return response()->view('errors.geoblock', [
                'geo' => $analise['dados'] ?? [],
            ], 403);
        }

        return $next($request);
    }
}
