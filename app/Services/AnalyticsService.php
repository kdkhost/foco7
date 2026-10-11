<?php

namespace App\Services;

use App\Models\Visita;
use App\Models\Notificacao;
use App\Models\Agendamento;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    /**
     * Registra uma visita ao site com detecção inteligente de dispositivo e navegador
     */
    public static function registrarVisita(Request $request): void
    {
        try {
            // Ignora rotas de administração, webhooks, assets e chamadas AJAX de polling
            $uri = $request->path();
            if (
                str_starts_with($uri, 'admin') ||
                str_starts_with($uri, 'webhook') ||
                str_starts_with($uri, 'api') ||
                str_starts_with($uri, '_') ||
                $request->ajax() ||
                $request->isMethod('POST') ||
                $request->isMethod('DELETE') ||
                $request->isMethod('PUT')
            ) {
                return;
            }

            $ua = $request->userAgent() ?: '';
            
            // Ignora bots e crawlers conhecidos
            if (preg_match('/bot|crawl|spider|slurp|facebookexternalhit|whatsapp/i', $ua)) {
                return;
            }

            // Hash seguro do IP (LGPD compliant)
            $ip = $request->ip() ?: '127.0.0.1';
            $ipHash = hash('sha256', $ip . config('app.key', 'foco7_salt'));

            // Detecção do dispositivo
            $dispositivo = 'desktop';
            if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
                $dispositivo = 'tablet';
            } elseif (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $ua)) {
                $dispositivo = 'mobile';
            }

            // Detecção do navegador
            $navegador = 'Outro';
            if (preg_match('/edg/i', $ua)) {
                $navegador = 'Edge';
            } elseif (preg_match('/chrome|crios/i', $ua)) {
                $navegador = 'Chrome';
            } elseif (preg_match('/firefox|fxios/i', $ua)) {
                $navegador = 'Firefox';
            } elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
                $navegador = 'Safari';
            } elseif (preg_match('/opera|opr/i', $ua)) {
                $navegador = 'Opera';
            }

            // Detecção de Sistema Operacional
            $so = 'Outro';
            if (preg_match('/windows/i', $ua)) {
                $so = 'Windows';
            } elseif (preg_match('/android/i', $ua)) {
                $so = 'Android';
            } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
                $so = 'iOS';
            } elseif (preg_match('/mac os|macintosh/i', $ua)) {
                $so = 'macOS';
            } elseif (preg_match('/linux/i', $ua)) {
                $so = 'Linux';
            }

            $origem = $request->headers->get('referer');
            if ($origem && strlen($origem) > 250) {
                $origem = substr($origem, 0, 250);
            }

            $rota = $request->route() ? $request->route()->getName() : null;

            Visita::create([
                'ip_hash' => $ipHash,
                'session_id' => $request->session()->getId(),
                'url' => substr($request->fullUrl(), 0, 250),
                'rota' => $rota ?: ($uri === '/' || empty($uri) ? 'site.home' : $uri),
                'dispositivo' => $dispositivo,
                'navegador' => $navegador,
                'sistema_operacional' => $so,
                'origem' => $origem,
                'data_visita' => now()->toDateString(),
            ]);
        } catch (\Throwable $e) {
            Log::debug('AnalyticsService registro silenciado: ' . $e->getMessage());
        }
    }

    /**
     * Retorna o resumo completo de métricas e analytics para o Dashboard
     */
    public static function obterMetricas(): array
    {
        $hoje = now()->toDateString();
        $mesAtual = now()->month;
        $anoAtual = now()->year;

        // Métricas de Visitas Hoje
        $visitasHoje = Visita::where('data_visita', $hoje)->count();
        $unicosHoje = Visita::where('data_visita', $hoje)->distinct('ip_hash')->count('ip_hash');

        // Métricas de Visitas no Mês
        $visitasMes = Visita::whereYear('data_visita', $anoAtual)->whereMonth('data_visita', $mesAtual)->count();
        $unicosMes = Visita::whereYear('data_visita', $anoAtual)->whereMonth('data_visita', $mesAtual)->distinct('ip_hash')->count('ip_hash');

        $totalHistorico = Visita::count();

        // Conversões (Contatos + Agendamentos no mês)
        $totalContatosMes = Notificacao::where('tipo', 'contato')
            ->whereYear('created_at', $anoAtual)
            ->whereMonth('created_at', $mesAtual)
            ->count();

        $totalAgendamentosMes = Agendamento::whereYear('created_at', $anoAtual)
            ->whereMonth('created_at', $mesAtual)
            ->count();

        $totalConversoesMes = $totalContatosMes + $totalAgendamentosMes;
        $taxaConversao = $unicosMes > 0 
            ? round(($totalConversoesMes / $unicosMes) * 100, 1) 
            : 0.0;

        // Gráfico de Visitas dos Últimos 7 Dias
        $diasLabels = [];
        $visitasDiasData = [];
        $unicosDiasData = [];

        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i)->toDateString();
            $label = now()->subDays($i)->format('d/m');
            $diasLabels[] = $label;

            $totalDia = Visita::where('data_visita', $data)->count();
            $unicosDia = Visita::where('data_visita', $data)->distinct('ip_hash')->count('ip_hash');

            $visitasDiasData[] = $totalDia;
            $unicosDiasData[] = $unicosDia;
        }

        // Distribuição por Dispositivo
        $dispositivosRaw = Visita::select('dispositivo', DB::raw('count(*) as total'))
            ->whereYear('data_visita', $anoAtual)
            ->whereMonth('data_visita', $mesAtual)
            ->groupBy('dispositivo')
            ->pluck('total', 'dispositivo')
            ->toArray();

        $mobile = $dispositivosRaw['mobile'] ?? 0;
        $desktop = $dispositivosRaw['desktop'] ?? 0;
        $tablet = $dispositivosRaw['tablet'] ?? 0;
        $totalDisp = $mobile + $desktop + $tablet;

        $mobilePct = $totalDisp > 0 ? round(($mobile / $totalDisp) * 100) : 55;
        $desktopPct = $totalDisp > 0 ? round(($desktop / $totalDisp) * 100) : 40;
        $tabletPct = $totalDisp > 0 ? round(($tablet / $totalDisp) * 100) : 5;

        // Páginas mais acessadas
        $paginasPopulares = Visita::select('rota', DB::raw('count(*) as total'))
            ->whereYear('data_visita', $anoAtual)
            ->groupBy('rota')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Gráfico Financeiro dos Últimos 6 Meses
        $mesesFinanceiroLabels = [];
        $receitaMesesData = [];

        for ($m = 5; $m >= 0; $m--) {
            $mesData = now()->subMonths($m);
            $mesNome = $mesData->translatedFormat('M/Y');
            $mesesFinanceiroLabels[] = ucfirst($mesNome);

            $receitaMes = Fatura::where('status', 'pago')
                ->whereYear('data_pagamento', $mesData->year)
                ->whereMonth('data_pagamento', $mesData->month)
                ->sum('valor');

            $receitaMesesData[] = round((float) $receitaMes, 2);
        }

        return [
            'visitas_hoje' => $visitasHoje,
            'unicos_hoje' => $unicosHoje,
            'visitas_mes' => $visitasMes,
            'unicos_mes' => $unicosMes,
            'total_historico' => $totalHistorico,
            'taxa_conversao' => $taxaConversao,
            'total_conversoes_mes' => $totalConversoesMes,
            'dias_labels' => $diasLabels,
            'visitas_dias_data' => $visitasDiasData,
            'unicos_dias_data' => $unicosDiasData,
            'mobile_pct' => $mobilePct,
            'desktop_pct' => $desktopPct,
            'tablet_pct' => $tabletPct,
            'mobile_count' => $mobile,
            'desktop_count' => $desktop,
            'tablet_count' => $tablet,
            'paginas_populares' => $paginasPopulares,
            'meses_financeiro_labels' => $mesesFinanceiroLabels,
            'receita_meses_data' => $receitaMesesData,
        ];
    }
}
