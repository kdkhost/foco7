<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Aplica cabeçalhos rigorosos de segurança HTTP para proteção contra ataques
     * de injeção, XSS, clickjacking, MIME-sniffing e sequestro de conteúdo.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Previne que a aplicação seja exibida dentro de frames ou iframes externos (Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Previne que o navegador tente adivinhar o MIME-type (MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Ativa o filtro XSS nativo dos navegadores legados
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controla o envio de dados do referenciador para requisições externas
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restringe acesso a recursos sensíveis de hardware que não são utilizados
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), payment=(), usb=()');

        return $response;
    }
}
