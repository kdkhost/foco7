<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fatura;
use App\Models\Agendamento;
use App\Models\Plano;
use App\Models\Notificacao;
use App\Models\CmsSetting;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Exibe o painel de controle principal (Dashboard) reformulado com KPIs animados e Analítico Completo
     */
    public function index()
    {
        // Estatísticas Rápidas de Alta Performance
        $totalClientes = User::where('role', 'cliente')->count();
        $totalPlanos = Plano::where('ativo', true)->count();
        $agendamentosPendentes = Agendamento::where('status', 'pendente')->count();

        $faturasPagasMes = (float) Fatura::where('status', 'pago')
            ->whereMonth('data_pagamento', date('m'))
            ->whereYear('data_pagamento', date('Y'))
            ->sum('valor');

        $faturasPendentes = Fatura::where('status', 'pendente')->count();
        $notificacoesPendentes = Notificacao::naoLidas()->count();

        // Próximos agendamentos com Eager Loading
        $proximosAgendamentos = Agendamento::with(['user', 'servicoEstetica'])
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '>=', now()->startOfDay())
            ->orderBy('data_hora_inicio', 'asc')
            ->limit(6)
            ->get();

        // Últimas faturas com Eager Loading
        $ultimasFaturas = Fatura::with(['user', 'plano'])
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $modoManutencao = CmsSetting::get('manutencao_ativa', '0') == '1';

        // Métricas e Analítico Completo do Site
        $analytics = AnalyticsService::obterMetricas();

        return view('admin.dashboard', compact(
            'totalClientes',
            'totalPlanos',
            'agendamentosPendentes',
            'faturasPagasMes',
            'faturasPendentes',
            'notificacoesPendentes',
            'proximosAgendamentos',
            'ultimasFaturas',
            'modoManutencao',
            'analytics'
        ));
    }
}
