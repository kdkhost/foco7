<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
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
        $dataId = $request->input('data.id') ?: $request->input('id');
        $faturaCodigo = $request->query('fatura_codigo') ?: $request->input('fatura_codigo');

        if (($type === 'payment' || $request->input('action') === 'payment.created' || $request->input('action') === 'payment.updated' || ! empty($dataId)) && ($dataId || $faturaCodigo)) {
            // Localiza a fatura vinculada ao transaction id ou código dinâmico
            $fatura = Fatura::where(function ($q) use ($dataId, $faturaCodigo) {
                if (! empty($dataId)) {
                    $q->where('gateway_transaction_id', (string) $dataId);
                }
                if (! empty($faturaCodigo)) {
                    $q->orWhere('codigo_fatura', (string) $faturaCodigo);
                }
            })->first();

            if ($fatura && $fatura->status !== 'pago') {
                $fatura->update([
                    'status' => 'pago',
                    'data_pagamento' => now(),
                    'webhook_payload' => $request->all(),
                ]);

                Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Mercado Pago Webhook Dinâmico.");

                // Dispara confirmação imediata por e-mail ao cliente
                if ($fatura->user && ! empty($fatura->user->email)) {
                    EmailTemplateService::enviar($fatura->user->email, 'pagamento_confirmado', [
                        'cliente_nome' => $fatura->user->name,
                        'cliente_email' => $fatura->user->email,
                        'fatura_id' => $fatura->codigo_fatura,
                        'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                        'valor' => $fatura->valor_formatado,
                        'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                    ]);
                }
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

        // 1. Tratamento de Notificações Pix da Efí
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

                    Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Efí PRO Pix Webhook.");

                    if ($fatura->user && ! empty($fatura->user->email)) {
                        EmailTemplateService::enviar($fatura->user->email, 'pagamento_confirmado', [
                            'cliente_nome' => $fatura->user->name,
                            'cliente_email' => $fatura->user->email,
                            'fatura_id' => $fatura->codigo_fatura,
                            'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                            'valor' => $fatura->valor_formatado,
                            'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                        ]);
                    }
                }
            }
        }

        // 2. Tratamento de Notificações de Carnê e Boleto v1 da Efí
        $notificationToken = $request->input('notification');
        if (! empty($notificationToken)) {
            $dadosNotif = EfiPaymentService::consultarNotificacaoV1($notificationToken);
            if (! empty($dadosNotif) && is_array($dadosNotif)) {
                foreach ($dadosNotif as $evento) {
                    $chargeId = $evento['identifiers']['charge_id'] ?? null;
                    $status = $evento['status']['current'] ?? null;

                    if ($chargeId && in_array($status, ['paid', 'settled'])) {
                        $fatura = Fatura::where('gateway_transaction_id', (string) $chargeId)
                            ->orWhere('codigo_fatura', 'like', "%{$chargeId}%")
                            ->first();

                        if ($fatura && $fatura->status !== 'pago') {
                            $fatura->update([
                                'status' => 'pago',
                                'data_pagamento' => now(),
                                'webhook_payload' => $evento,
                            ]);

                            Log::info("Fatura {$fatura->codigo_fatura} (Carnê/Boleto Efí) baixada automaticamente via Notificação v1.");

                            if ($fatura->user && ! empty($fatura->user->email)) {
                                EmailTemplateService::enviar($fatura->user->email, 'pagamento_confirmado', [
                                    'cliente_nome' => $fatura->user->name,
                                    'cliente_email' => $fatura->user->email,
                                    'fatura_id' => $fatura->codigo_fatura,
                                    'plano_nome' => $fatura->plano->nome ?? $fatura->descricao,
                                    'valor' => $fatura->valor_formatado,
                                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'received'], 200);
    }
}
