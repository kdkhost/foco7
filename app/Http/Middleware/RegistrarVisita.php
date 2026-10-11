<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AnalyticsService;

class RegistrarVisita
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Registra a visita apenas se a resposta for bem-sucedida (código 200)
        if ($response->getStatusCode() === 200) {
            AnalyticsService::registrarVisita($request);
        }

        return $response;
    }
}
