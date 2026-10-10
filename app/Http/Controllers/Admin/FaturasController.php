<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use App\Models\User;
use App\Models\Plano;
use Illuminate\Support\Str;

class FaturasController extends Controller
{
    /**
     * Listagem completa de Faturas e Mensalidades
     */
    public function index(Request $request)
    {
        $query = Fatura::with(['user', 'plano']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('codigo_fatura', 'like', "%{$busca}%")
                  ->orWhere('descricao', 'like', "%{$busca}%")
                  ->orWhereHas('user', function ($uq) use ($busca) {
                      $uq->where('name', 'like', "%{$busca}%")
                         ->orWhere('email', 'like', "%{$busca}%");
                  });
            });
        }

        $faturas = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalFaturas = Fatura::count();
        $totalPendentes = Fatura::where('status', 'pendente')->count();
        $totalPagas = Fatura::where('status', 'pago')->count();
        $valorRecebidoMes = Fatura::where('status', 'pago')->whereMonth('updated_at', now()->month)->sum('valor');

        $clientes = User::where('role', 'cliente')->where('status', 'ativo')->orderBy('name')->get();
        $planos = Plano::where('ativo', true)->orderBy('nome')->get();

        return view('admin.faturas.index', compact(
            'faturas', 'totalFaturas', 'totalPendentes', 'totalPagas', 'valorRecebidoMes', 'clientes', 'planos'
        ));
    }

    /**
     * Retorna dados da fatura para edição no modal
     */
    public function obter($id)
    {
        $fatura = Fatura::with(['user', 'plano'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'fatura' => $fatura,
            'valor_formatado' => number_format($fatura->valor, 2, ',', '.'),
            'vencimento_iso' => $fatura->data_vencimento ? $fatura->data_vencimento->format('Y-m-d') : '',
        ]);
    }

    /**
     * Cria ou edita uma fatura
     */
    public function salvar(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plano_id' => 'nullable|exists:planos,id',
            'descricao' => 'required|string|max:255',
            'valor' => 'required',
            'data_vencimento' => 'required|date',
            'status' => 'required|in:pendente,pago,cancelado',
        ]);

        $id = $request->input('fatura_id');
        $fatura = $id ? Fatura::findOrFail($id) : new Fatura();

        // Tratamento do Valor Monetário
        $valorRaw = $request->input('valor');
        $valorLimpo = str_replace(['R$', ' ', '.'], '', $valorRaw);
        $valorLimpo = str_replace(',', '.', $valorLimpo);
        $valorFinal = is_numeric($valorLimpo) ? (float) $valorLimpo : 0.00;

        $fatura->user_id = $request->input('user_id');
        $fatura->plano_id = $request->input('plano_id');
        $fatura->descricao = $request->input('descricao');
        $fatura->valor = $valorFinal;
        $fatura->data_vencimento = $request->input('data_vencimento');
        $fatura->status = $request->input('status');
        if ($fatura->status === 'pago' && empty($fatura->data_pagamento)) {
            $fatura->data_pagamento = now();
        } elseif ($fatura->status !== 'pago') {
            $fatura->data_pagamento = null;
        }

        if (!$id) {
            $fatura->codigo_fatura = 'FAT-' . date('Y') . '-' . strtoupper(Str::random(6));
            $fatura->gateway = 'mercadopago';
            $fatura->metodo_pagamento = 'pix';
        }

        $fatura->save();

        return response()->json([
            'success' => true,
            'message' => $id ? 'Fatura atualizada com sucesso!' : 'Fatura lançada com sucesso para o cliente!',
        ]);
    }

    /**
     * Alteração rápida de status da fatura
     */
    public function alterarStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,pago,cancelado',
        ]);

        $fatura = Fatura::findOrFail($id);
        $novoStatus = $request->input('status');
        $fatura->status = $novoStatus;

        if ($novoStatus === 'pago' && empty($fatura->data_pagamento)) {
            $fatura->data_pagamento = now();
        } elseif ($novoStatus !== 'pago') {
            $fatura->data_pagamento = null;
        }

        $fatura->save();

        return response()->json([
            'success' => true,
            'status' => $fatura->status,
            'message' => 'Status da fatura alterado para ' . ucfirst($fatura->status) . '!',
        ]);
    }

    /**
     * Exclui uma fatura
     */
    public function excluir($id)
    {
        $fatura = Fatura::findOrFail($id);
        $fatura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fatura removida com sucesso!',
        ]);
    }
}
