<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fatura;
use App\Models\Agendamento;
use App\Models\Plano;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Exibe o painel de controle principal (Dashboard) do AdminLTE 4
     */
    public function index()
    {
        // Estatísticas rápidas de alto desempenho
        $totalClientes = User::where('role', 'cliente')->count();
        $totalPlanos = Plano::where('ativo', true)->count();
        $agendamentosPendentes = Agendamento::where('status', 'pendente')->count();

        $faturasPagasMes = Fatura::where('status', 'pago')
            ->whereMonth('data_pagamento', date('m'))
            ->whereYear('data_pagamento', date('Y'))
            ->sum('valor');

        $faturasPendentes = Fatura::where('status', 'pendente')->count();

        // Próximos agendamentos com Eager Loading
        $proximosAgendamentos = Agendamento::with('user')
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '>=', now()->startOfDay())
            ->orderBy('data_hora_inicio', 'asc')
            ->limit(5)
            ->get();

        // Últimas faturas com Eager Loading
        $ultimasFaturas = Fatura::with(['user', 'plano'])
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        $modoManutencao = CmsSetting::get('manutencao_ativa', '0') == '1';

        return view('admin.dashboard', compact(
            'totalClientes',
            'totalPlanos',
            'agendamentosPendentes',
            'faturasPagasMes',
            'faturasPendentes',
            'proximosAgendamentos',
            'ultimasFaturas',
            'modoManutencao'
        ));
    }
}
