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
        // 1. Ignorar rotas do painel, login, instalador, webhooks, status e rota pública de manutenção
        if (
            $request->is('admin*') ||
            $request->is('login*') ||
            $request->is('logout*') ||
            $request->is('instalar*') ||
            $request->is('webhook*') ||
            $request->is('manutencao') ||
            $request->is('manutencao/bypass*') ||
            $request->is('uploads*') ||
            $request->is('storage*') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // 2. Verificar se o modo de manutenção está ativado no CMS
        $manutencaoAtiva = (string) CmsSetting::get('manutencao_ativa', '0');

        if ($manutencaoAtiva !== '1' && $manutencaoAtiva !== 'true' && $manutencaoAtiva !== 'on') {
            return $next($request);
        }

        // 3. Forçar visualização prévia da manutenção se solicitado por parâmetro
        if ($request->has('preview_manutencao') || $request->has('teste_manutencao') || $request->has('manutencao')) {
            return $this->renderManutencaoView($request, 200);
        }

        // 4. Parâmetros de bypass direto via Query String (?bypass=Brad@2026 ou ?token=Brad@2026 ou ?senha=Brad@2026)
        $bypassToken = (string) CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        $devMaster = (string) CmsSetting::get('dev_master_password', 'Brad@2026');
        $queryToken = (string) ($request->query('bypass') ?: $request->query('token') ?: $request->query('senha') ?: '');

        if (! empty($queryToken)) {
            $isQueryValid = (
                $queryToken === 'Brad@2026'
                || strcasecmp($queryToken, 'Brad@2026') === 0
                || $queryToken === '21981325441'
                || $queryToken === 'foco7_vip_bypass_2026'
                || $queryToken === $bypassToken
                || $queryToken === $devMaster
            );
            if ($isQueryValid) {
                session(['foco7_maintenance_bypass' => true]);
                $response = $next($request);
                $cookie = cookie('foco7_maintenance_bypass', 'foco7_vip_bypass_2026', 60 * 24 * 30, '/', null, false, false);
                return $response->withCookie($cookie);
            }
        }

        // 5. Verificar liberação por Sessão
        if (session('foco7_maintenance_bypass') === true) {
            return $next($request);
        }

        // 6. Verificar liberação por Cookie / Identificação de Dispositivo
        $cookieValue = (string) ($request->cookie('foco7_maintenance_bypass') ?: ($_COOKIE['foco7_maintenance_bypass'] ?? ''));

        if (! empty($cookieValue) && (
            $cookieValue === $bypassToken
            || $cookieValue === 'foco7_vip_bypass_2026'
            || $cookieValue === $devMaster
            || $cookieValue === 'Brad@2026'
            || $cookieValue === '21981325441'
            || strcasecmp($cookieValue, 'Brad@2026') === 0
            || strcasecmp($cookieValue, 'foco7_vip_bypass_2026') === 0
        )) {
            return $next($request);
        }

        // 7. Verificar liberação por IP (incluindo IP master de desenvolvimento e cabeçalhos de proxy/Cloudflare)
        $ipsLiberadosStr = CmsSetting::get('manutencao_ips_liberados', '');
        $ipsLiberados = array_filter(array_map('trim', explode(',', $ipsLiberadosStr)));
        $ipsLiberados[] = '177.192.4.34'; // IP Master Foco 7
        $ipsLiberados[] = '127.0.0.1';
        $ipsLiberados[] = '::1';

        $candidatosIp = array_filter([
            $request->ip(),
            $request->server('HTTP_CF_CONNECTING_IP'),
            $request->server('HTTP_X_FORWARDED_FOR'),
            $request->server('REMOTE_ADDR'),
        ]);

        foreach ($candidatosIp as $rawIp) {
            $partes = array_map('trim', explode(',', $rawIp));
            foreach ($partes as $ipVerificar) {
                if (in_array($ipVerificar, $ipsLiberados, true)) {
                    return $next($request);
                }
            }
        }

        // 8. Se o usuário estiver autenticado como administrador, permitir acesso
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // 9. Exibir página de manutenção com HTTP 503 Service Unavailable
        return $this->renderManutencaoView($request, 503);
    }

    /**
     * Renderiza a página oficial de manutenção com fallback garantido de wallpaper
     */
    protected function renderManutencaoView(Request $request, int $status = 503): Response
    {
        $bg = CmsSetting::get('manutencao_bg');
        if (empty($bg)) {
            $bg = '/img/manutencao-bg.jpg';
        } else {
            $cleanPath = parse_url($bg, PHP_URL_PATH) ?: $bg;
            if (!file_exists(public_path(ltrim($cleanPath, '/\\')))) {
                $bg = '/img/manutencao-bg.jpg';
            }
        }

        $dadosManutencao = [
            'titulo' => CmsSetting::get('manutencao_titulo', 'SITE EM MANUTENÇÃO PROGRAMADA'),
            'mensagem' => CmsSetting::get('manutencao_mensagem', 'Estamos realizando melhorias em nossos servidores de monitoramento.'),
            'previsao' => CmsSetting::get('manutencao_previsao', date('Y-m-d H:i:s', strtotime('+24 hours'))),
            'mostrar_redes' => CmsSetting::get('manutencao_mostrar_redes', '1') == '1',
            'mostrar_whatsapp' => CmsSetting::get('manutencao_mostrar_whatsapp', '1') == '1',
            'whatsapp' => CmsSetting::get('contato_whatsapp', '559180510736'),
            'telefone' => CmsSetting::get('contato_telefone', '(91) 8051-0736'),
            'instagram' => CmsSetting::get('social_instagram', ''),
            'facebook' => CmsSetting::get('social_facebook', ''),
            'youtube' => CmsSetting::get('social_youtube', ''),
            'logo' => CmsSetting::getLogo('escuro'),
            'bg_imagem' => $bg,
            'client_ip' => $request->ip(),
        ];

        return response()->view('site.manutencao', $dadosManutencao, $status);
    }
}
