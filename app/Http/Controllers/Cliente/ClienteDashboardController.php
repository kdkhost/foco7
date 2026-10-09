<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use App\Models\Agendamento;
use App\Models\ServicoEstetica;

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

        // Agendamentos do cliente (rastreamento e estética)
        $agendamentos = Agendamento::with('servicoEstetica')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('cliente_telefone', $user->telefone)
                  ->orWhere('cliente_email', $user->email);
            })
            ->orderBy('data_hora_inicio', 'desc')
            ->get();

        $servicosEstetica = ServicoEstetica::where('ativo', true)->orderBy('ordem')->get();

        return view('cliente.dashboard', compact('user', 'faturas', 'faturasPendentes', 'faturasPagas', 'agendamentos', 'servicosEstetica'));
    }

    /**
     * Permite que o cliente solicite agendamento (estética ou suporte/instalação)
     */
    public function solicitarAgendamento(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'categoria' => 'required|string|in:rastreamento,estetica',
            'servico_estetica_id' => 'nullable|exists:servicos_estetica,id',
            'tipo_servico' => 'nullable|string|max:100',
            'veiculo_modelo' => 'required|string|max:100',
            'veiculo_placa' => 'nullable|string|max:20',
            'data_hora_inicio' => 'required|date',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $categoria = $validated['categoria'];
        $tipoServico = $validated['tipo_servico'] ?? ($categoria === 'estetica' ? 'Estética Automotiva' : 'Suporte Técnico');
        $valor = null;

        if ($categoria === 'estetica' && !empty($validated['servico_estetica_id'])) {
            $servico = ServicoEstetica::find($validated['servico_estetica_id']);
            if ($servico) {
                $tipoServico = $servico->nome;
                $valor = $servico->preco;
            }
        }

        $agendamento = Agendamento::create([
            'user_id' => $user->id,
            'categoria' => $categoria,
            'servico_estetica_id' => $validated['servico_estetica_id'] ?? null,
            'cliente_nome' => $user->name,
            'cliente_telefone' => $user->telefone ?? '',
            'cliente_email' => $user->email,
            'tipo_servico' => $tipoServico,
            'valor' => $valor,
            'veiculo_modelo' => $validated['veiculo_modelo'],
            'veiculo_placa' => $validated['veiculo_placa'] ?? null,
            'data_hora_inicio' => $validated['data_hora_inicio'],
            'observacoes' => $validated['observacoes'] ?? 'Solicitado via Painel do Cliente.',
            'status' => 'pendente',
            'cor_calendario' => $categoria === 'estetica' ? '#1e293b' : '#28a745',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agendamento solicitado com sucesso! Entraremos em contato para confirmar.',
            'agendamento_id' => $agendamento->id,
        ]);
    }
}

