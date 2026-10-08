<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook de retorno do Mercado Pago
     */
    public function mercadopago(Request $request)
    {
        Log::info('Webhook Mercado Pago recebido:', $request->all());

        $type = $request->input('type');
        $dataId = $request->input('data.id');

        if ($type === 'payment' && $dataId) {
            // Localiza a fatura vinculada ao transaction id
            $fatura = Fatura::where('gateway_transaction_id', $dataId)->first();

            if ($fatura && $fatura->status !== 'pago') {
                $fatura->update([
                    'status' => 'pago',
                    'data_pagamento' => now(),
                    'webhook_payload' => $request->all(),
                ]);

                Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Mercado Pago Webhook.");
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Webhook de retorno da Efí PRO (Gerencianet)
     */
    public function efi(Request $request)
    {
        Log::info('Webhook Efí PRO recebido:', $request->all());

        $pixList = $request->input('pix', []);

        foreach ($pixList as $pixItem) {
            $txid = $pixItem['txid'] ?? null;
            if ($txid) {
                $fatura = Fatura::where('gateway_transaction_id', $txid)
                    ->orWhere('codigo_fatura', 'like', "%{$txid}%")
                    ->first();

                if ($fatura && $fatura->status !== 'pago') {
                    $fatura->update([
                        'status' => 'pago',
                        'data_pagamento' => now(),
                        'webhook_payload' => $pixItem,
                    ]);

                    Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Efí PRO Webhook.");
                }
            }
        }

        return response()->json(['status' => 'received'], 200);
    }
}
