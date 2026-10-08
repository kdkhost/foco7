<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use App\Models\Agendamento;

class ClienteDashboardController extends Controller
{
    /**
     * Exibe o painel exclusivo da Área do Cliente
     */
    public function index()
    {
        $user = auth()->user();

        // Faturas do cliente com Eager Loading
        $faturas = Fatura::with('plano')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $faturasPendentes = $faturas->where('status', 'pendente');
        $faturasPagas = $faturas->where('status', 'pago');

        // Agendamentos do cliente
        $agendamentos = Agendamento::where('user_id', $user->id)
            ->orWhere('cliente_telefone', $user->telefone)
            ->orderBy('data_hora_inicio', 'desc')
            ->get();

        return view('cliente.dashboard', compact('user', 'faturas', 'faturasPendentes', 'faturasPagas', 'agendamentos'));
    }
}
