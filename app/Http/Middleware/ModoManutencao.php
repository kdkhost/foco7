<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CmsSetting;

class ModoManutencao
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ignorar rotas do painel, login, instalador, webhooks e bypass
        if (
            $request->is('admin*') ||
            $request->is('login*') ||
            $request->is('logout*') ||
            $request->is('instalar*') ||
            $request->is('webhook*') ||
            $request->is('manutencao/bypass*') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // 2. Verificar se o modo de manutenção está ativado no CMS
        $manutencaoAtiva = CmsSetting::get('manutencao_ativa', '0');

        if ($manutencaoAtiva !== '1' && $manutencaoAtiva !== true) {
            return $next($request);
        }

        // 3. Verificar liberação por Cookie / Identificação de Dispositivo
        $bypassToken = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        if ($request->cookie('foco7_maintenance_bypass') === $bypassToken) {
            return $next($request);
        }

        // 4. Verificar liberação por IP
        $ipsLiberadosStr = CmsSetting::get('manutencao_ips_liberados', '');
        $ipsLiberados = array_filter(array_map('trim', explode(',', $ipsLiberadosStr)));
        $clientIp = $request->ip();

        if (in_array($clientIp, $ipsLiberados, true)) {
            return $next($request);
        }

        // 5. Se o usuário estiver autenticado como administrador, permitir acesso
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // 6. Exibir página de manutenção com HTTP 503 Service Unavailable
        $dadosManutencao = [
            'titulo' => CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA'),
            'mensagem' => CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores.'),
            'previsao' => CmsSetting::get('manutencao_previsao', date('Y-m-d H:i:s', strtotime('+24 hours'))),
            'mostrar_redes' => CmsSetting::get('manutencao_mostrar_redes', '1') == '1',
            'mostrar_whatsapp' => CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1',
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => CmsSetting::get('site_logo', '/img/logo-foco7.png'),
            'client_ip' => $clientIp,
        ];

        return response()->view('site.manutencao', $dadosManutencao, 503);
    }
}
