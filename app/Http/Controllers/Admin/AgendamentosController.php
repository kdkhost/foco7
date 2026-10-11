<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\ServicoEstetica;

class AgendamentosController extends Controller
{
    /**
     * Exibe o calendário interativo do FullCalendar 6
     */
    public function index()
    {
        $servicosEstetica = ServicoEstetica::where('ativo', true)->orderBy('ordem')->get();
        return view('admin.agendamentos.index', compact('servicosEstetica'));
    }

    /**
     * Retorna dados de um agendamento específico para edição
     */
    public function obter($id)
    {
        $agendamento = Agendamento::with('servicoEstetica')->findOrFail($id);

        return response()->json([
            'success' => true,
            'agendamento' => $agendamento,
            'inicio_iso' => $agendamento->data_hora_inicio ? $agendamento->data_hora_inicio->format('Y-m-d\TH:i') : '',
            'fim_iso' => $agendamento->data_hora_fim ? $agendamento->data_hora_fim->format('Y-m-d\TH:i') : '',
        ]);
    }

    /**
     * Feed JSON formatado para o FullCalendar 6
     */
    public function apiEventos(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        // O calendário é exclusivo para agendamento de serviços (instalações, revisões e estética)
        $query = Agendamento::with('servicoEstetica')
            ->whereIn('categoria', ['rastreamento', 'estetica'])
            ->where('tipo_servico', 'not like', '%Contato%')
            ->where('tipo_servico', 'not like', '%Dúvida%');

        if ($start && $end) {
            $query->whereBetween('data_hora_inicio', [
                date('Y-m-d 00:00:00', strtotime($start)),
                date('Y-m-d 23:59:59', strtotime($end)),
            ]);
        }

        $agendamentos = $query->get();

        $eventos = $agendamentos->map(function ($ag) {
            $isEstetica = $ag->categoria === 'estetica';

            $corPrimaria = \App\Models\CmsSetting::get('cor_primaria', '#d90000');
            $cor = match ($ag->status) {
                'confirmado' => $isEstetica ? '#1e293b' : '#28a745',
                'concluido' => $corPrimaria,
                'cancelado' => '#dc3545',
                default => '#ffc107',
            };

            $prefixo = $isEstetica ? '[Estética] ' : '[Rastreador] ';

            return [
                'id' => $ag->id,
                'title' => "{$prefixo}{$ag->tipo_servico}: {$ag->cliente_nome} ({$ag->veiculo_modelo})",
                'start' => $ag->data_hora_inicio ? $ag->data_hora_inicio->format('Y-m-d\TH:i:s') : now()->format('Y-m-d\TH:i:s'),
                'end' => $ag->data_hora_fim ? $ag->data_hora_fim->format('Y-m-d\TH:i:s') : null,
                'backgroundColor' => $cor,
                'borderColor' => $cor,
                'textColor' => $ag->status === 'pendente' ? '#000000' : '#ffffff',
                'extendedProps' => [
                    'categoria' => $ag->categoria,
                    'servico_estetica_id' => $ag->servico_estetica_id,
                    'valor' => $ag->valor,
                    'cliente_nome' => $ag->cliente_nome,
                    'cliente_telefone' => $ag->cliente_telefone,
                    'tipo_servico' => $ag->tipo_servico,
                    'veiculo_modelo' => $ag->veiculo_modelo,
                    'veiculo_placa' => $ag->veiculo_placa,
                    'endereco' => $ag->endereco_atendimento,
                    'cidade' => $ag->cidade,
                    'observacoes' => $ag->observacoes,
                    'status' => $ag->status,
                    'tipo_pagamento' => $ag->tipo_pagamento ?? 'online',
                ],
            ];
        });

        return response()->json($eventos);
    }

    /**
     * Cria ou atualiza agendamento via modal AJAX
     */
    public function salvar(Request $request)
    {
        $validated = $request->validate([
            'cliente_nome' => 'required|string|max:150',
            'cliente_telefone' => 'required|string|max:30',
            'cliente_email' => 'nullable|email|max:150',
            'categoria' => 'nullable|string|in:rastreamento,estetica',
            'servico_estetica_id' => 'nullable|exists:servicos_estetica,id',
            'tipo_servico' => 'required|string',
            'valor' => 'nullable',
            'tipo_pagamento' => 'nullable|string|in:online,local',
            'veiculo_modelo' => 'nullable|string|max:100',
            'veiculo_placa' => 'nullable|string|max:20',
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'nullable|date',
            'endereco_atendimento' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $id = $request->input('agendamento_id');

        // Tratamento seguro do valor financeiro
        if (array_key_exists('valor', $validated)) {
            if ($validated['valor'] === '' || $validated['valor'] === null) {
                $validated['valor'] = null;
            } else {
                $vLimpo = str_replace(['R$', ' ', '.'], '', (string) $validated['valor']);
                $vLimpo = str_replace(',', '.', $vLimpo);
                $validated['valor'] = is_numeric($vLimpo) ? (float) $vLimpo : null;
            }
        }

        if (empty($validated['categoria'])) {
            $validated['categoria'] = $request->filled('servico_estetica_id') ? 'estetica' : 'rastreamento';
        }

        if ($id) {
            $agendamento = Agendamento::findOrFail($id);
            $agendamento->update($validated);
        } else {
            Agendamento::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Agendamento gravado com sucesso no calendário!',
        ]);
    }

    /**
     * Atualização rápida de horário via Drag & Drop do FullCalendar
     */
    public function atualizarHorario(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:agendamentos,id',
            'start' => 'required|date',
            'end' => 'nullable|date',
        ]);

        $agendamento = Agendamento::findOrFail($request->input('id'));
        $agendamento->data_hora_inicio = $request->input('start');
        if ($request->filled('end')) {
            $agendamento->data_hora_fim = $request->input('end');
        }
        $agendamento->save();

        return response()->json([
            'success' => true,
            'message' => 'Horário do agendamento remarcado com sucesso!',
        ]);
    }

    /**
     * Cancela um agendamento com registro de motivo e liberacao de vaga
     */
    public function cancelar(Request $request, $id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $motivo = $request->input('motivo', 'Cancelado pelo administrador');

        $agendamento->status = 'cancelado';
        $logCancelamento = "[Cancelado em " . date('d/m/Y H:i') . "]: " . $motivo;
        $agendamento->observacoes = trim(($agendamento->observacoes ? $agendamento->observacoes . "\n" : '') . $logCancelamento);
        $agendamento->save();

        return response()->json([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso e vaga liberada no calendário!',
            'agendamento' => $agendamento,
        ]);
    }

    /**
     * Exclui um agendamento
     */
    public function excluir($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agendamento removido com sucesso!',
        ]);
    }
}
