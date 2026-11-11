<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Licenca;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use App\Services\EvolutionApiService;
use App\Services\LicenciamentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Webhook de retorno do Mercado Pago (Suporta Webhook Dinâmico por requisição e Módulos Paralelos Sandbox/Produção)
     */
    public function mercadopago(Request $request)
    {
        Log::info('Webhook Mercado Pago recebido:', [
            'query' => $request->query(),
            'body' => $request->all(),
        ]);

        $type = $request->input('type') ?: $request->query('topic');
        $dataId = $request->input('data.id') ?: ($request->input('id') ?: $request->query('data.id') ?: $request->query('id'));
        $faturaId = $request->query('fatura_id') ?: $request->input('fatura_id');
        $faturaCodigo = $request->query('fatura_codigo') ?: $request->input('fatura_codigo');
        $instanciaQuery = $request->query('wpp_instancia') ?: $request->input('wpp_instancia');
        $ambienteWebhook = $request->query('ambiente') ?: $request->input('ambiente');
        $reqToken = $request->query('req_token') ?: $request->input('req_token');
        $origem = (string) ($request->query('origem') ?: $request->input('origem', ''));
        $modulo = (string) ($request->query('modulo') ?: $request->input('modulo', ''));

        if (($type === 'payment' || $request->input('action') === 'payment.created' || $request->input('action') === 'payment.updated' || ! empty($dataId)) && ($dataId || $faturaCodigo || $faturaId)) {
            $baixou = false;

            // 1. Consulta o pagamento na API do Mercado Pago (Suporta os 2 módulos em paralelo: Desenvolvedor [Sandbox/Produção] e Empresa)
            if (! empty($dataId)) {
                $tokenDevAmbienteReq = in_array($ambienteWebhook, ['producao', 'sandbox'], true)
                    ? EvolutionApiService::getMercadoPagoAccessTokenParaInstancias($ambienteWebhook)
                    : '';
                $tokenDevAtivo = EvolutionApiService::getMercadoPagoAccessTokenParaInstancias();
                $tokenDevProd = EvolutionApiService::getMercadoPagoAccessTokenParaInstancias('producao');
                $tokenDevSandbox = EvolutionApiService::getMercadoPagoAccessTokenParaInstancias('sandbox');

                $mpAmbEmpresa = CmsSetting::get('mercadopago_ambiente', 'sandbox');
                $tokenEmpresa = $mpAmbEmpresa === 'producao'
                    ? (string) CmsSetting::get('mercadopago_access_token_prod', CmsSetting::get('mercadopago_access_token', ''))
                    : (string) CmsSetting::get('mercadopago_access_token_sandbox', CmsSetting::get('mercadopago_access_token', ''));

                // Prioriza os tokens do Módulo Paralelo do Desenvolvedor quando a requisição veio do webhook dinâmico de instância WhatsApp
                $isWebhookInstanciaDev = ($origem === 'instancia_wpp' || $modulo === 'mp_dev_paralelo' || ! empty($instanciaQuery));

                $tokensParaTestar = $isWebhookInstanciaDev
                    ? array_filter([$tokenDevAmbienteReq, $tokenDevAtivo, $tokenDevProd, $tokenDevSandbox, $tokenEmpresa])
                    : array_filter([$tokenEmpresa, $tokenDevAtivo, $tokenDevProd, $tokenDevSandbox]);

                foreach (array_unique($tokensParaTestar) as $tokenMp) {
                    try {
                        $resMp = Http::withToken($tokenMp)->timeout(8)->get("https://api.mercadopago.com/v1/payments/{$dataId}");
                        if ($resMp->successful()) {
                            $mpData = $resMp->json();
                            $mpStatus = strtolower((string) ($mpData['status'] ?? ''));
                            $extRef = $mpData['external_reference'] ?? null;

                            if (in_array($mpStatus, ['approved', 'pago'])) {
                                $fatura = Fatura::where('gateway_transaction_id', (string) $dataId)
                                    ->orWhere(function ($q) use ($faturaId, $extRef, $faturaCodigo, $instanciaQuery) {
                                        if (! empty($faturaId)) {
                                            $q->where('id', (int) $faturaId);
                                        }
                                        if (! empty($extRef)) {
                                            $q->orWhere('codigo_fatura', (string) $extRef);
                                        }
                                        if (! empty($faturaCodigo)) {
                                            $q->orWhere('codigo_fatura', (string) $faturaCodigo);
                                        }
                                        if (! empty($instanciaQuery)) {
                                            $q->orWhere('descricao', 'like', "%{$instanciaQuery}%");
                                        }
                                    })->first();

                                if ($fatura && $fatura->status !== 'pago') {
                                    $payloadAnterior = is_array($fatura->webhook_payload) ? $fatura->webhook_payload : [];
                                    $fatura->update([
                                        'status' => 'pago',
                                        'metodo_pagamento' => 'pix',
                                        'data_pagamento' => now(),
                                        'gateway_transaction_id' => (string) $dataId,
                                        'webhook_payload' => array_merge($payloadAnterior, $mpData, [
                                            'webhook_dinamico_recebido_em' => now()->format('Y-m-d H:i:s'),
                                            'ambiente_webhook' => $ambienteWebhook,
                                            'req_token' => $reqToken,
                                        ]),
                                    ]);

                                    Log::info("Fatura {$fatura->codigo_fatura} baixada com sucesso via Mercado Pago Webhook Dinâmico (Approved).");
                                    $this->processarPosPagamentoFatura($fatura);
                                    $baixou = true;
                                }
                            }
                            break;
                        }
                    } catch (\Throwable $e) {
                        Log::warning("Aviso ao validar Mercado Pago via API no webhook para {$dataId}: ".$e->getMessage());
                    }
                }
            }

            // 2. Fallback caso não tenha consultado a API mas exista correspondência direta no banco
            if (! $baixou) {
                $fatura = Fatura::where(function ($q) use ($dataId, $faturaId, $faturaCodigo, $instanciaQuery) {
                    if (! empty($dataId)) {
                        $q->where('gateway_transaction_id', (string) $dataId);
                    }
                    if (! empty($faturaId)) {
                        $q->orWhere('id', (int) $faturaId);
                    }
                    if (! empty($faturaCodigo)) {
                        $q->orWhere('codigo_fatura', (string) $faturaCodigo);
                    }
                    if (! empty($instanciaQuery)) {
                        $q->orWhere('descricao', 'like', "%{$instanciaQuery}%");
                    }
                })->first();

                if ($fatura && $fatura->status !== 'pago') {
                    $payloadAnterior = is_array($fatura->webhook_payload) ? $fatura->webhook_payload : [];
                    $fatura->update([
                        'status' => 'pago',
                        'metodo_pagamento' => 'pix',
                        'data_pagamento' => now(),
                        'webhook_payload' => array_merge($payloadAnterior, $request->all(), [
                            'webhook_dinamico_recebido_em' => now()->format('Y-m-d H:i:s'),
                            'ambiente_webhook' => $ambienteWebhook,
                            'req_token' => $reqToken,
                        ]),
                    ]);

                    Log::info("Fatura {$fatura->codigo_fatura} baixada automaticamente via Mercado Pago Webhook Dinâmico.");
                    $this->processarPosPagamentoFatura($fatura);
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

                    $this->processarPosPagamentoFatura($fatura);
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

                            $this->processarPosPagamentoFatura($fatura);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Webhook de retorno da Evolution API Go (v3.0)
     * Recebe eventos de status de conexão, mensagens, QR code, etc.
     */
    public function evolution(Request $request)
    {
        $payload = $request->all();
        $event = $request->input('event') ?? $request->input('type') ?? 'UNKNOWN';

        Log::info("Webhook Evolution API Go [{$event}]:", $payload);

        // Tratamento de evento de CONEXÃO (CONNECTION / CONNECTION_UPDATE)
        if (str_contains(strtoupper($event), 'CONNECTION') || isset($payload['state']) || isset($payload['connected']) || isset($payload['Connected']) || isset($payload['data']['Connected'])) {
            $state = $payload['state'] ?? ($payload['data']['state'] ?? ($payload['status'] ?? ($payload['data']['status'] ?? null)));
            $boolConn = $payload['connected'] ?? ($payload['Connected'] ?? ($payload['data']['Connected'] ?? ($payload['data']['connected'] ?? null)));

            if ($state === null && is_bool($boolConn)) {
                $state = $boolConn ? 'open' : 'close';
            }

            if ($state) {
                $statusMapeado = match (strtolower((string) $state)) {
                    'open', 'connected', 'conectado', '1', 'true' => 'conectado',
                    'connecting', 'conectando' => 'conectando',
                    'close', 'disconnected', 'desconectado', '0', 'false' => 'desconectado',
                    'refused' => 'recusado',
                    default => strtolower((string) $state),
                };

                CmsSetting::set('evolution_status_cache', $statusMapeado, 'whatsapp');
                CmsSetting::set('evolution_last_connection_event', now()->format('Y-m-d H:i:s'), 'whatsapp');

                $jid = $payload['jid'] ?? ($payload['data']['jid'] ?? ($payload['owner'] ?? null));
                if (! empty($jid)) {
                    CmsSetting::set('evolution_connected_number', str_replace(['@s.whatsapp.net', '@c.us'], '', (string) $jid), 'whatsapp');
                }

                Log::info("Status da Evolution API Go atualizado para: {$statusMapeado}");
            }
        }

        // Tratamento de evento de QRCODE
        if (str_contains(strtoupper($event), 'QRCODE') || isset($payload['qrcode'])) {
            $qrCode = $payload['qrcode']['base64'] ?? ($payload['data']['qrcode']['base64'] ?? null);
            if ($qrCode) {
                CmsSetting::set('evolution_last_qrcode', $qrCode, 'whatsapp');
            }
        }

        return response()->json(['status' => 'success', 'event' => $event], 200);
    }

    /**
     * Processa notificações e ativação pós-pagamento de fatura
     */
    protected function processarPosPagamentoFatura(Fatura $fatura): void
    {
        // 1. Dispara confirmação imediata por e-mail ao cliente
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

        // 2. Dispara confirmação imediata via WhatsApp
        if ($fatura->user && ! empty($fatura->user->telefone) && EvolutionApiService::isAtivo()) {
            try {
                EvolutionApiService::enviarConfirmacaoPagamento($fatura);
            } catch (\Throwable $e) {
                Log::warning('Falha ao enviar confirmação WhatsApp: '.$e->getMessage());
            }
        }

        // 3. Ativação automática de Licença de Software FOCO 7
        if (str_contains(strtolower($fatura->descricao ?? ''), 'licen')
            || Licenca::where('fatura_id', $fatura->id)->exists()
            || Licenca::where('user_id', $fatura->user_id)->where('status', 'pendente')->exists()) {
            try {
                LicenciamentoService::ativarLicencaPosPagamento($fatura);
            } catch (\Throwable $e) {
                Log::warning('Falha ao ativar licença no webhook: '.$e->getMessage());
            }
        }

        // 4. Ativação automática de Instância de WhatsApp (Evolution API Go) ao pagar a fatura
        if (str_contains(strtolower($fatura->descricao ?? ''), 'whatsapp')
            || str_contains(strtolower($fatura->descricao ?? ''), 'instancia')
            || str_starts_with($fatura->codigo_fatura ?? '', 'FAT-WPP-')) {
            try {
                EvolutionApiService::processarPagamentoFaturaInstancia($fatura);
            } catch (\Throwable $e) {
                Log::warning('Falha ao reativar instância WhatsApp no webhook: '.$e->getMessage());
            }
        }
    }
}
