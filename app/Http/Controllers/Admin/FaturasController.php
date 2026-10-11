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
     * Realiza a antecipação de uma parcela com cálculo de desconto opcional e baixa imediata
     */
    public function anteciparParcela(Request $request, $id)
    {
        $fatura = Fatura::with('user')->findOrFail($id);

        if ($fatura->status === 'pago') {
            return response()->json([
                'success' => false,
                'message' => 'Esta fatura já está marcada como paga!',
            ], 422);
        }

        $desconto = (float) str_replace(',', '.', str_replace(['R$', ' '], '', (string) $request->input('desconto', '0')));
        $metodo = $request->input('metodo_pagamento', 'pix');
        $observacao = $request->input('observacao', '');

        $valorOriginal = (float) $fatura->valor;
        $valorComDesconto = max(0, round($valorOriginal - $desconto, 2));

        $fatura->status = 'pago';
        $fatura->data_pagamento = now();
        $fatura->metodo_pagamento = $metodo;
        $fatura->valor = $valorComDesconto;
        
        $obsAntecipacao = " [Antecipada em " . date('d/m/Y H:i');
        if ($desconto > 0) {
            $obsAntecipacao .= " com desconto de R$ " . number_format($desconto, 2, ',', '.') . " (original R$ " . number_format($valorOriginal, 2, ',', '.') . ")";
        }
        if ($observacao) {
            $obsAntecipacao .= " - Obs: {$observacao}";
        }
        $obsAntecipacao .= "]";

        $fatura->descricao = trim($fatura->descricao . $obsAntecipacao);
        $fatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Parcela antecipada e quitada com sucesso!',
            'fatura' => $fatura,
            'valor_original' => number_format($valorOriginal, 2, ',', '.'),
            'desconto' => number_format($desconto, 2, ',', '.'),
            'valor_final' => number_format($valorComDesconto, 2, ',', '.'),
        ]);
    }

    /**
     * Realiza a antecipação e quitação de múltiplas parcelas em lote (ex: quitação integral de carnê)
     */
    public function anteciparLote(Request $request)
    {
        $request->validate([
            'fatura_ids' => 'required|array|min:1',
            'fatura_ids.*' => 'exists:faturas,id',
        ]);

        $ids = $request->input('fatura_ids');
        $descontoTotal = (float) str_replace(',', '.', str_replace(['R$', ' '], '', (string) $request->input('desconto_total', '0')));
        $metodo = $request->input('metodo_pagamento', 'pix');

        $faturas = Fatura::whereIn('id', $ids)->where('status', '!=', 'pago')->get();

        if ($faturas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma parcela pendente encontrada para antecipação.',
            ], 422);
        }

        $descontoPorParcela = $faturas->count() > 0 ? round($descontoTotal / $faturas->count(), 2) : 0;
        $totalQuitado = 0;

        foreach ($faturas as $fat) {
            $valorOriginalFat = (float) $fat->valor;
            $valorComDescFat = max(0, round($valorOriginalFat - $descontoPorParcela, 2));

            $fat->status = 'pago';
            $fat->data_pagamento = now();
            $fat->metodo_pagamento = $metodo;
            $fat->valor = $valorComDescFat;
            $fat->descricao = trim($fat->descricao . " [Antecipação em lote em " . date('d/m/Y H:i') . ($descontoPorParcela > 0 ? " com desc. de R$ " . number_format($descontoPorParcela, 2, ',', '.') : "") . "]");
            $fat->save();
            $totalQuitado += $valorComDescFat;
        }

        return response()->json([
            'success' => true,
            'message' => "{$faturas->count()} parcelas foram antecipadas e quitadas com sucesso!",
            'total_parcelas' => $faturas->count(),
            'valor_total' => number_format($totalQuitado, 2, ',', '.'),
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
