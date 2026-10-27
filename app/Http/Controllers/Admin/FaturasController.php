<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Notificacao;
use App\Models\Plano;
use App\Models\User;
use App\Services\EfiPaymentService;
use Illuminate\Http\Request;
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
     * Retorna detalhes completos do carnê para os diálogos interativos de cancelamento e exclusão
     */
    public function obterInfoCarne($id)
    {
        $fatura = Fatura::with(['user', 'plano'])->findOrFail($id);
        $info = $fatura->getInfoCarne();

        return response()->json([
            'success' => true,
            'fatura' => [
                'id' => $fatura->id,
                'codigo_fatura' => $fatura->codigo_fatura,
                'descricao' => $fatura->descricao,
                'cliente_nome' => $fatura->user?->name ?? 'Cliente',
                'valor_formatado' => $fatura->valor_formatado,
                'status' => $fatura->status,
                'is_paga' => $fatura->isPaga(),
                'numero_parcela' => $fatura->numero_parcela ?: 1,
                'total_parcelas' => $fatura->total_parcelas ?: 1,
            ],
            'carne' => $info,
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
        $fatura = $id ? Fatura::findOrFail($id) : new Fatura;

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

        if (! $id) {
            $fatura->codigo_fatura = 'FAT-'.date('Y').'-'.strtoupper(Str::random(6));
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
     * Alteração rápida de status da fatura (com suporte a escopo de carnê se status for cancelado)
     */
    public function alterarStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,pago,cancelado',
        ]);

        $fatura = Fatura::findOrFail($id);

        // Bloqueio contábil: fatura já quitada não pode ter status alterado diretamente
        if ($fatura->status === 'pago') {
            return response()->json([
                'success' => false,
                'message' => 'Faturas já quitadas não podem ter o status alterado manualmente. Para cancelar e anular a receita, utilize a função formal de "Estornar Fatura".',
            ], 422);
        }

        $novoStatus = $request->input('status');

        if ($novoStatus === 'cancelado' && $request->filled('escopo')) {
            return $this->cancelarCarne($request, $id);
        }

        $fatura->status = $novoStatus;

        if ($novoStatus === 'pago' && empty($fatura->data_pagamento)) {
            $fatura->data_pagamento = now();
        } elseif ($novoStatus !== 'pago') {
            $fatura->data_pagamento = null;
        }

        $fatura->save();

        if ($novoStatus === 'cancelado' && ! empty($fatura->efi_carnet_id) && $fatura->numero_parcela) {
            EfiPaymentService::cancelarParcelaCarneEfi($fatura->efi_carnet_id, (int) $fatura->numero_parcela);
        }

        return response()->json([
            'success' => true,
            'status' => $fatura->status,
            'message' => 'Status da fatura alterado para '.ucfirst($fatura->status).'!',
        ]);
    }

    /**
     * Cancela parcela individual, sequência pendente ou todas as parcelas do carnê
     */
    public function cancelarCarne(Request $request, $id)
    {
        $fatura = Fatura::with('user')->findOrFail($id);
        $escopo = $request->input('escopo', 'somente_esta');
        $clienteNome = $fatura->user?->name ?? 'Cliente';
        $canceladasCount = 0;

        if ($fatura->isPaga() && $escopo === 'somente_esta') {
            return response()->json([
                'success' => false,
                'message' => 'Faturas já quitadas não podem ser canceladas diretamente. Para anular a receita, utilize a função de "Estorno".',
            ], 422);
        }

        if ($escopo === 'somente_esta') {
            if ($fatura->status === 'pago') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta fatura já está marcada como paga e não pode ser cancelada diretamente.',
                ], 422);
            }

            $fatura->status = 'cancelado';
            $fatura->data_pagamento = null;
            $fatura->save();
            $canceladasCount = 1;

            if (! empty($fatura->efi_carnet_id) && $fatura->numero_parcela) {
                EfiPaymentService::cancelarParcelaCarneEfi($fatura->efi_carnet_id, (int) $fatura->numero_parcela);
            }

            $numInfo = $fatura->numero_parcela ? "Parcela #{$fatura->numero_parcela}" : "Fatura #{$fatura->codigo_fatura}";
            $mensagem = "{$numInfo} cancelada com sucesso!";
        } elseif ($escopo === 'sequencia_pendente') {
            $numAtual = $fatura->numero_parcela ?: 1;
            $parcelas = $fatura->getParcelasCarneQuery()
                ->where('numero_parcela', '>=', $numAtual)
                ->whereNotIn('status', ['pago', 'quitada', 'liquidada'])
                ->get();

            foreach ($parcelas as $p) {
                $p->status = 'cancelado';
                $p->data_pagamento = null;
                $p->save();
                $canceladasCount++;

                if (! empty($p->efi_carnet_id) && $p->numero_parcela) {
                    EfiPaymentService::cancelarParcelaCarneEfi($p->efi_carnet_id, (int) $p->numero_parcela);
                }
            }

            $mensagem = "A parcela #{$numAtual} e a sequência pendente ({$canceladasCount} parcelas no total) foram canceladas com sucesso!";
        } elseif ($escopo === 'todas_parcelas') {
            $parcelas = $fatura->getParcelasCarneQuery()
                ->whereNotIn('status', ['pago', 'quitada', 'liquidada'])
                ->get();

            foreach ($parcelas as $p) {
                $p->status = 'cancelado';
                $p->data_pagamento = null;
                $p->save();
                $canceladasCount++;
            }

            if (! empty($fatura->efi_carnet_id)) {
                EfiPaymentService::cancelarCarneEfi($fatura->efi_carnet_id);
            }

            $mensagem = "Todas as {$canceladasCount} parcelas pendentes do carnê foram canceladas com sucesso!";
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Escopo de cancelamento informado é inválido.',
            ], 422);
        }

        try {
            Notificacao::create([
                'tipo' => 'financeiro',
                'titulo' => 'Cancelamento de Carnê/Parcela',
                'mensagem' => "{$mensagem} Cliente: {$clienteNome}. Realizado por: ".(auth()->user()->name ?? 'Administrador'),
                'link' => route('admin.faturas.index'),
                'lida' => false,
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => $mensagem,
            'total_afetadas' => $canceladasCount,
            'status' => 'cancelado',
        ]);
    }

    /**
     * Realiza o estorno manual de uma fatura paga, registrando motivo e recalculando a receita contábil
     */
    public function estornar(Request $request, $id)
    {
        $request->validate([
            'motivo' => 'required|string|min:3|max:255',
        ], [
            'motivo.required' => 'Informe a justificativa do estorno.',
            'motivo.min' => 'A justificativa deve conter no mínimo 3 caracteres.',
        ]);

        $fatura = Fatura::with('user')->findOrFail($id);

        if ($fatura->status !== 'pago') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas faturas com status "Pago" podem ser estornadas.',
            ], 422);
        }

        $motivo = trim($request->input('motivo'));
        $valorEstornado = (float) $fatura->valor;

        $fatura->status = 'estornado';
        $fatura->descricao = trim($fatura->descricao.' [Estornado em '.date('d/m/Y H:i').' por '.(auth()->user()->name ?? 'Administrador')." - Motivo: {$motivo}]");
        $fatura->save();

        // Registra alerta na central de notificações para auditoria contábil
        try {
            Notificacao::create([
                'tipo' => 'financeiro',
                'titulo' => 'Fatura Estornada: '.$fatura->codigo_fatura,
                'mensagem' => "A fatura #{$fatura->codigo_fatura} (R$ ".number_format($valorEstornado, 2, ',', '.').") do cliente {$fatura->user?->name} foi estornada manualmente. Motivo: {$motivo}",
                'link' => route('admin.faturas.index'),
                'lida' => false,
            ]);
        } catch (\Throwable $e) {
            // Log sem interromper o fluxo
            \Log::error('Falha ao gerar notificação de estorno: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Fatura estornada com sucesso! A receita referente à fatura foi recalculada na contabilidade.',
            'fatura' => $fatura,
            'status' => 'estornado',
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

        $obsAntecipacao = ' [Antecipada em '.date('d/m/Y H:i');
        if ($desconto > 0) {
            $obsAntecipacao .= ' com desconto de R$ '.number_format($desconto, 2, ',', '.').' (original R$ '.number_format($valorOriginal, 2, ',', '.').')';
        }
        if ($observacao) {
            $obsAntecipacao .= " - Obs: {$observacao}";
        }
        $obsAntecipacao .= ']';

        $fatura->descricao = trim($fatura->descricao.$obsAntecipacao);
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
            $fat->descricao = trim($fat->descricao.' [Antecipação em lote em '.date('d/m/Y H:i').($descontoPorParcela > 0 ? ' com desc. de R$ '.number_format($descontoPorParcela, 2, ',', '.') : '').']');
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
     * Exclui parcela individual, sequência pendente ou todas as parcelas do carnê de uma única vez
     */
    public function excluirCarne(Request $request, $id)
    {
        $fatura = Fatura::with('user')->findOrFail($id);
        $escopo = $request->input('escopo', 'somente_esta');
        $clienteNome = $fatura->user?->name ?? 'Cliente';
        $excluidasCount = 0;

        if ($escopo === 'somente_esta') {
            if ($fatura->isPaga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faturas quitadas não podem ser excluídas diretamente sem estorno prévio para fins contábeis.',
                ], 422);
            }

            $codigo = $fatura->codigo_fatura;
            $fatura->delete();
            $excluidasCount = 1;
            $mensagem = "Fatura #{$codigo} removida com sucesso!";
        } elseif ($escopo === 'sequencia_pendente') {
            $numAtual = $fatura->numero_parcela ?: 1;
            $parcelas = $fatura->getParcelasCarneQuery()
                ->where('numero_parcela', '>=', $numAtual)
                ->whereNotIn('status', ['pago', 'quitada', 'liquidada'])
                ->get();

            $excluidasCount = $parcelas->count();
            foreach ($parcelas as $p) {
                $p->delete();
            }

            $mensagem = "A parcela #{$numAtual} e a sequência pendente ({$excluidasCount} parcelas) foram excluídas com sucesso!";
        } elseif ($escopo === 'todas_parcelas') {
            // DELETAR TODAS AS PARCELAS DO CARNÊ DE UMA ÚNICA VEZ
            $todas = $fatura->getParcelasCarneQuery()->get();
            $pagas = $todas->filter(fn ($p) => $p->isPaga());

            if ($pagas->isNotEmpty()) {
                // Se houver alguma paga, remove todas as pendentes e avisa sobre a preservação contábil das quitadas
                $pendentes = $todas->filter(fn ($p) => ! $p->isPaga());
                $excluidasCount = $pendentes->count();
                foreach ($pendentes as $p) {
                    $p->delete();
                }
                $mensagem = "{$excluidasCount} parcelas pendentes do carnê foram excluídas de uma única vez! ({$pagas->count()} parcelas já quitadas foram preservadas por segurança contábil).";
            } else {
                // Nenhuma paga: deleta 100% das parcelas do carnê de uma única vez
                $excluidasCount = $todas->count();
                if (! empty($fatura->efi_carnet_id)) {
                    EfiPaymentService::cancelarCarneEfi($fatura->efi_carnet_id);
                }
                foreach ($todas as $p) {
                    $p->delete();
                }
                $mensagem = "Todas as {$excluidasCount} parcelas do carnê foram excluídas de uma única vez com sucesso!";
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Escopo de exclusão informado é inválido.',
            ], 422);
        }

        try {
            Notificacao::create([
                'tipo' => 'financeiro',
                'titulo' => 'Exclusão de Faturas/Carnê',
                'mensagem' => "{$mensagem} Cliente: {$clienteNome}. Realizado por: ".(auth()->user()->name ?? 'Administrador'),
                'link' => route('admin.faturas.index'),
                'lida' => false,
            ]);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => $mensagem,
            'total_excluidas' => $excluidasCount,
        ]);
    }

    /**
     * Exclui uma fatura (com suporte a escopo para carnês)
     */
    public function excluir(Request $request, $id)
    {
        if ($request->filled('escopo') && $request->input('escopo') !== 'somente_esta') {
            return $this->excluirCarne($request, $id);
        }

        $fatura = Fatura::findOrFail($id);

        if ($fatura->isPaga()) {
            return response()->json([
                'success' => false,
                'message' => 'Faturas já quitadas não podem ser excluídas diretamente sem estorno prévio.',
            ], 422);
        }

        $fatura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fatura removida com sucesso!',
        ]);
    }
}
