<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Admin\GatewaysController;
use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    /**
     * Métricas financeiras e resumo do painel
     */
    public function metricas(): JsonResponse
    {
        $totalFaturas = Fatura::count();
        $totalPendentes = Fatura::where('status', 'pendente')->count();
        $totalPagas = Fatura::where('status', 'pago')->count();
        $valorRecebidoMes = (float) Fatura::where('status', 'pago')
            ->whereMonth('updated_at', now()->month)
            ->sum('valor');

        return response()->json([
            'success' => true,
            'data' => [
                'total_faturas' => $totalFaturas,
                'total_pendentes' => $totalPendentes,
                'total_pagas' => $totalPagas,
                'receita_mes_corrente' => $valorRecebidoMes,
                'receita_mes_formatada' => 'R$ '.number_format($valorRecebidoMes, 2, ',', '.'),
            ],
        ], 200);
    }

    /**
     * Listagem paginada de clientes
     */
    public function clientes(Request $request): JsonResponse
    {
        $query = User::where('role', 'cliente');

        if ($request->filled('busca')) {
            $busca = $request->input('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$busca}%")
                    ->orWhere('telefone', 'like', "%{$busca}%");
            });
        }

        $clientes = $query->orderBy('name', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $clientes->items(),
            'meta' => [
                'current_page' => $clientes->currentPage(),
                'total' => $clientes->total(),
                'per_page' => $clientes->perPage(),
                'last_page' => $clientes->lastPage(),
            ],
        ], 200);
    }

    /**
     * Emissão programática de Carnê de Mensalidades (Próprio vs Efí PRO)
     */
    public function emitirCarne(Request $request, GatewaysController $gatewaysController): JsonResponse
    {
        return $gatewaysController->gerarCarne($request);
    }

    /**
     * Estorno de fatura com justificativa contábil via API REST
     */
    public function estornarFatura(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'motivo' => 'required|string|min:3|max:255',
        ]);

        $fatura = Fatura::findOrFail($id);

        if ($fatura->status !== 'pago') {
            return response()->json([
                'success' => false,
                'message' => 'Somente faturas pagas/quitadas podem ser estornadas.',
            ], 422);
        }

        $fatura->update([
            'status' => 'estornado',
            'webhook_payload' => array_merge($fatura->webhook_payload ?? [], [
                'estorno_em' => now()->toIso8601String(),
                'estorno_motivo' => $request->input('motivo'),
                'estorno_origem' => 'api_rest_v1',
            ]),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Fatura {$fatura->codigo_fatura} estornada com sucesso via API.",
            'fatura' => [
                'id' => $fatura->id,
                'codigo_fatura' => $fatura->codigo_fatura,
                'status' => 'estornado',
                'motivo' => $request->input('motivo'),
            ],
        ], 200);
    }
}
