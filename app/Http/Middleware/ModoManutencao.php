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

        // 4. Verificar liberação por Cookie / Identificação de Dispositivo
        $bypassToken = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        if ($request->cookie('foco7_maintenance_bypass') === $bypassToken) {
            return $next($request);
        }

        // 5. Verificar liberação por IP
        $ipsLiberadosStr = CmsSetting::get('manutencao_ips_liberados', '');
        $ipsLiberados = array_filter(array_map('trim', explode(',', $ipsLiberadosStr)));
        $clientIp = $request->ip();

        if (in_array($clientIp, $ipsLiberados, true)) {
            return $next($request);
        }

        // 6. Se o usuário estiver autenticado como administrador, permitir acesso
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // 7. Exibir página de manutenção com HTTP 503 Service Unavailable
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
