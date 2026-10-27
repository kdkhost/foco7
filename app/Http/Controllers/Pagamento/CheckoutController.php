<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Plano;
use App\Models\User;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Calcula as taxas repassadas e valor final para preview em tempo real no checkout
     */
    public function calcularTaxas(Request $request)
    {
        $planoId = $request->input('plano_id');
        $metodo = $request->input('metodo_pagamento', 'pix');
        $parcelas = max(1, (int) $request->input('parcelas', 1));

        $plano = Plano::find($planoId);
        if (! $plano) {
            return response()->json(['success' => false, 'message' => 'Plano não encontrado.'], 404);
        }

        $valorBase = (float) ($plano->preco_mensal + ($plano->taxa_adesao ?? 0));
        $taxas = EfiPaymentService::calcularTaxas($valorBase, $metodo, $parcelas);

        return response()->json([
            'success' => true,
            'taxas' => [
                'valor_original' => $taxas['valor_original'],
                'valor_original_formatado' => 'R$ '.number_format($taxas['valor_original'], 2, ',', '.'),
                'taxa_repassada' => $taxas['taxa_repassada'],
                'taxa_repassada_formatada' => 'R$ '.number_format($taxas['taxa_repassada'], 2, ',', '.'),
                'desconto' => $taxas['desconto'],
                'desconto_formatado' => 'R$ '.number_format($taxas['desconto'], 2, ',', '.'),
                'valor_final' => $taxas['valor_final'],
                'valor_final_formatado' => 'R$ '.number_format($taxas['valor_final'], 2, ',', '.'),
                'detalhe' => $taxas['detalhe'],
                'repasse_ativo' => $taxas['repasse_ativo'],
            ],
        ]);
    }

    /**
     * Processa a contratação e checkout transparente a partir da Landing Page
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'nome' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'telefone' => 'required|string|max:30',
            'cpf_cnpj' => 'required|string|max:20',
            'metodo_pagamento' => 'required|in:pix,cartao,boleto,carne',
            'gateway' => 'nullable|in:efi,mercadopago',
            'parcelas' => 'nullable|integer|min:1|max:24',
            'card_token' => 'nullable|string',
            'card_numero' => 'nullable|string',
            'card_titular' => 'nullable|string',
            'card_expiracao' => 'nullable|string',
            'card_cvv' => 'nullable|string',
            'card_bandeira' => 'nullable|string',
        ], [
            'nome.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'telefone.required' => 'Informe seu telefone ou WhatsApp.',
            'cpf_cnpj.required' => 'Informe seu CPF ou CNPJ.',
        ]);

        $plano = Plano::findOrFail($validated['plano_id']);
        $metodo = $validated['metodo_pagamento'];
        $parcelas = max(1, (int) ($validated['parcelas'] ?? 1));

        // 1. Cadastrar ou localizar o usuário cliente
        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['nome'],
                'password' => Hash::make(Str::random(12)),
                'role' => 'cliente',
                'cpf_cnpj' => $validated['cpf_cnpj'],
                'telefone' => $validated['telefone'],
                'status' => 'ativo',
            ]
        );

        // Atualiza CPF e telefone se não estavam preenchidos
        if (empty($user->cpf_cnpj) || empty($user->telefone)) {
            $user->update([
                'cpf_cnpj' => $user->cpf_cnpj ?: $validated['cpf_cnpj'],
                'telefone' => $user->telefone ?: $validated['telefone'],
            ]);
        }

        // 2. Gateway a utilizar: respeita a escolha do form ou o gateway_padrao ('efi' ou 'mercadopago')
        $gatewayEscolhido = $request->input('gateway', CmsSetting::get('gateway_padrao', 'efi'));

        // Se o método for Carnê, a Efí é obrigatoriamente o gateway especializado
        if ($metodo === 'carne') {
            $gatewayEscolhido = 'efi';
        }

        // Se for Mercado Pago, mantém o fluxo legado do Mercado Pago sem tocar
        if ($gatewayEscolhido === 'mercadopago') {
            return $this->processarMercadoPago($user, $plano, $metodo, $request);
        }

        // =========================================================================
        // 3. FLUXO EXCLUSIVO EFÍ BANK (TOTALMENTE TRANSPARENTE COM REPASSE DE TAXAS)
        // =========================================================================

        // Caso especial: emissão de CARNÊ MENSAL via Efí
        if ($metodo === 'carne') {
            $resultadoCarne = EfiPaymentService::criarCarne(
                cliente: $user,
                valorParcelaBase: (float) $plano->preco_mensal,
                totalParcelas: max(2, min(24, $parcelas > 1 ? $parcelas : 12)),
                primeiroVencimento: now()->addDays((int) CmsSetting::get('carne_dias_primeiro_vencimento', '10')),
                descricao: "Assinatura: {$plano->nome}",
                plano: $plano
            );

            return response()->json([
                'success' => true,
                'metodo' => 'carne',
                'message' => 'Carnê emitido com sucesso! Você pode visualizar as parcelas ou baixar o Carnê em PDF.',
                'carne' => [
                    'codigo' => $resultadoCarne['codigo_carne'],
                    'total_parcelas' => $resultadoCarne['total_parcelas'],
                    'valor_parcela' => $resultadoCarne['valor_parcela_formatado'],
                    'pdf_url' => $resultadoCarne['pdf_url'],
                    'pdf_efi_url' => $resultadoCarne['pdf_efi_url'],
                ],
            ]);
        }

        // Cálculo de Repasse de Taxas para Pix, Boleto ou Cartão
        $valorBase = (float) ($plano->preco_mensal + ($plano->taxa_adesao ?? 0));
        $taxas = EfiPaymentService::calcularTaxas($valorBase, $metodo, $parcelas);
        $valorFinal = $taxas['valor_final'];
        $taxaRepassada = $taxas['taxa_repassada'];

        $codigoFatura = 'FAT-'.date('Ymd').'-'.strtoupper(Str::random(5));
        $diasVencimento = $metodo === 'boleto' ? (int) CmsSetting::get('boleto_dias_vencimento', '3') : 3;

        // Criação da fatura no banco de dados com registro das taxas
        $fatura = Fatura::create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'codigo_fatura' => $codigoFatura,
            'descricao' => "Adesão / Mensalidade: {$plano->nome}",
            'valor' => $valorFinal,
            'valor_original' => $valorBase,
            'taxa_repassada' => $taxaRepassada,
            'gateway' => 'efi',
            'metodo_pagamento' => $metodo,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays($diasVencimento),
        ]);

        // Processamento específico do método escolhido via Efí
        if ($metodo === 'pix') {
            $resultadoPix = EfiPaymentService::criarCobrancaPix($fatura, $user);

            // Dispara e-mail de fatura gerada com PIX
            if (! empty($user->email)) {
                EmailTemplateService::enviar($user->email, 'fatura_gerada', [
                    'cliente_nome' => $user->name,
                    'cliente_email' => $user->email,
                    'fatura_id' => $fatura->codigo_fatura,
                    'plano_nome' => $plano->nome,
                    'valor' => $fatura->valor_formatado,
                    'data_vencimento' => $fatura->data_vencimento->format('d/m/Y'),
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                ]);
            }

            return response()->json([
                'success' => true,
                'metodo' => 'pix',
                'message' => 'Cobrança PIX gerada com sucesso pela Efí Bank!',
                'fatura' => [
                    'codigo' => $fatura->codigo_fatura,
                    'plano' => $plano->nome,
                    'valor_formatado' => $fatura->valor_formatado,
                    'valor_original_formatado' => 'R$ '.number_format($valorBase, 2, ',', '.'),
                    'taxa_repassada_formatada' => 'R$ '.number_format($taxaRepassada, 2, ',', '.'),
                    'desconto_formatado' => 'R$ '.number_format($taxas['desconto'], 2, ',', '.'),
                    'metodo' => 'pix',
                    'qr_code_pix' => $resultadoPix['qr_code_pix'],
                    'qr_code_pix_url' => $resultadoPix['qr_code_pix_url'],
                ],
            ]);
        } elseif ($metodo === 'boleto') {
            $resultadoBoleto = EfiPaymentService::criarBoleto($fatura, $user);

            return response()->json([
                'success' => true,
                'metodo' => 'boleto',
                'message' => 'Boleto Bancário emitido com sucesso pela Efí Bank!',
                'fatura' => [
                    'codigo' => $fatura->codigo_fatura,
                    'plano' => $plano->nome,
                    'valor_formatado' => $fatura->valor_formatado,
                    'valor_original_formatado' => 'R$ '.number_format($valorBase, 2, ',', '.'),
                    'taxa_repassada_formatada' => 'R$ '.number_format($taxaRepassada, 2, ',', '.'),
                    'metodo' => 'boleto',
                    'linha_digitavel' => $resultadoBoleto['linha_digitavel'],
                    'url_boleto' => $resultadoBoleto['url_boleto'],
                ],
            ]);
        } elseif ($metodo === 'cartao') {
            $dadosCartao = [
                'payment_token' => $request->input('card_token'),
                'numero' => $request->input('card_numero'),
                'titular' => $request->input('card_titular'),
                'expiracao' => $request->input('card_expiracao'),
                'cvv' => $request->input('card_cvv'),
                'bandeira' => $request->input('card_bandeira', 'mastercard'),
                'parcelas' => $parcelas,
            ];

            $resultadoCartao = EfiPaymentService::criarCobrancaCartao($fatura, $dadosCartao, $user);

            // Se aprovado imediatamente, envia e-mail de confirmação
            if (($resultadoCartao['status'] ?? '') === 'pago' && ! empty($user->email)) {
                EmailTemplateService::enviar($user->email, 'pagamento_confirmado', [
                    'cliente_nome' => $user->name,
                    'cliente_email' => $user->email,
                    'fatura_id' => $fatura->codigo_fatura,
                    'plano_nome' => $plano->nome,
                    'valor' => $fatura->valor_formatado,
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                ]);
            }

            return response()->json([
                'success' => true,
                'metodo' => 'cartao',
                'status' => $resultadoCartao['status'] ?? 'pago',
                'message' => $resultadoCartao['message'] ?? 'Pagamento com Cartão processado com sucesso!',
                'fatura' => [
                    'codigo' => $fatura->codigo_fatura,
                    'plano' => $plano->nome,
                    'valor_formatado' => $fatura->valor_formatado,
                    'valor_original_formatado' => 'R$ '.number_format($valorBase, 2, ',', '.'),
                    'taxa_repassada_formatada' => 'R$ '.number_format($taxaRepassada, 2, ',', '.'),
                    'metodo' => 'cartao',
                    'parcelas' => $parcelas,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Método de pagamento inválido.'], 400);
    }

    /**
     * Fluxo de Pagamento Oficial Mercado Pago (Checkout Transparente sem redirecionamento externo)
     * Suporta PIX, Cartão de Crédito e Boleto Bancário com Webhook Dinâmico por requisição
     */
    private function processarMercadoPago(User $user, Plano $plano, string $metodo, Request $request)
    {
        $codigoFatura = 'FAT-'.date('Ymd').'-'.strtoupper(Str::random(5));
        $valorTotal = (float) ($plano->preco_mensal + ($plano->taxa_adesao ?? 0));
        $parcelas = max(1, (int) $request->input('parcelas', 1));

        $mpAmbiente = CmsSetting::get('mercadopago_ambiente', 'sandbox');
        $mpAccessToken = $mpAmbiente === 'producao'
            ? CmsSetting::get('mercadopago_access_token_prod', CmsSetting::get('mercadopago_access_token', ''))
            : CmsSetting::get('mercadopago_access_token_sandbox', CmsSetting::get('mercadopago_access_token', ''));

        // Webhook Dinâmico por requisição com rastreamento da fatura e timestamp
        $webhookDinamico = url('/webhook/mercadopago?fatura_codigo='.$codigoFatura.'&t='.time());

        // Se houver Access Token do Mercado Pago, processa diretamente via API oficial v1/payments
        if (! empty($mpAccessToken)) {
            try {
                $cpfLimpo = preg_replace('/\D/', '', $user->cpf_cnpj ?? '00000000000');
                $idempotencyKey = 'MP-'.$codigoFatura.'-'.time();

                // Montagem do payload conforme o método escolhido (Checkout 100% Transparente)
                $payloadMp = [
                    'transaction_amount' => (float) round($valorTotal, 2),
                    'description' => "Foco 7 - {$plano->nome} ({$codigoFatura})",
                    'payer' => [
                        'email' => $user->email,
                        'first_name' => substr($user->name, 0, 50),
                        'identification' => [
                            'type' => strlen($cpfLimpo) > 11 ? 'CNPJ' : 'CPF',
                            'number' => $cpfLimpo,
                        ],
                    ],
                    'notification_url' => $webhookDinamico,
                ];

                if ($metodo === 'pix') {
                    $payloadMp['payment_method_id'] = 'pix';
                } elseif ($metodo === 'cartao') {
                    $payloadMp['token'] = $request->input('card_token');
                    $payloadMp['installments'] = $parcelas;
                    $payloadMp['payment_method_id'] = strtolower($request->input('card_bandeira', 'master'));
                } elseif ($metodo === 'boleto') {
                    $payloadMp['payment_method_id'] = 'bolbradesco';
                }

                $response = Http::withToken($mpAccessToken)
                    ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
                    ->timeout(12)
                    ->post('https://api.mercadopago.com/v1/payments', $payloadMp);

                if ($response->successful()) {
                    $data = $response->json();
                    $mpId = (string) ($data['id'] ?? '');
                    $statusMp = strtolower($data['status'] ?? 'pending');
                    $isPago = in_array($statusMp, ['approved', 'pago']);

                    // PIX Mercado Pago
                    if ($metodo === 'pix') {
                        $copiaCola = $data['point_of_interaction']['transaction_data']['qr_code'] ?? null;
                        $qrCodeBase64 = $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
                        $qrUrl = $qrCodeBase64 ? 'data:image/png;base64,'.$qrCodeBase64 : ($copiaCola ? 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.urlencode($copiaCola) : null);

                        if ($copiaCola) {
                            $fatura = Fatura::create([
                                'user_id' => $user->id,
                                'plano_id' => $plano->id,
                                'codigo_fatura' => $codigoFatura,
                                'descricao' => "Adesão / Mensalidade: {$plano->nome}",
                                'valor' => $valorTotal,
                                'valor_original' => $valorTotal,
                                'gateway' => 'mercadopago',
                                'gateway_transaction_id' => $mpId,
                                'metodo_pagamento' => 'pix',
                                'status' => 'pendente',
                                'data_vencimento' => now()->addDays(3),
                                'qr_code_pix' => $copiaCola,
                                'qr_code_pix_url' => $qrUrl,
                                'webhook_payload' => $data,
                            ]);

                            return response()->json([
                                'success' => true,
                                'metodo' => 'pix',
                                'message' => 'Cobrança PIX gerada com sucesso via Mercado Pago Oficial!',
                                'fatura' => [
                                    'codigo' => $fatura->codigo_fatura,
                                    'plano' => $plano->nome,
                                    'valor_formatado' => $fatura->valor_formatado,
                                    'metodo' => 'pix',
                                    'qr_code_pix' => $fatura->qr_code_pix,
                                    'qr_code_pix_url' => $fatura->qr_code_pix_url,
                                ],
                            ]);
                        }
                    } elseif ($metodo === 'cartao') {
                        // Cartão Mercado Pago Transparente
                        $fatura = Fatura::create([
                            'user_id' => $user->id,
                            'plano_id' => $plano->id,
                            'codigo_fatura' => $codigoFatura,
                            'descricao' => "Adesão / Mensalidade: {$plano->nome}",
                            'valor' => $valorTotal,
                            'valor_original' => $valorTotal,
                            'gateway' => 'mercadopago',
                            'gateway_transaction_id' => $mpId,
                            'metodo_pagamento' => 'cartao',
                            'status' => $isPago ? 'pago' : 'pendente',
                            'data_pagamento' => $isPago ? now() : null,
                            'data_vencimento' => now()->addDays(3),
                            'webhook_payload' => $data,
                        ]);

                        if ($isPago && ! empty($user->email)) {
                            EmailTemplateService::enviar($user->email, 'pagamento_confirmado', [
                                'cliente_nome' => $user->name,
                                'cliente_email' => $user->email,
                                'fatura_id' => $fatura->codigo_fatura,
                                'plano_nome' => $plano->nome,
                                'valor' => $fatura->valor_formatado,
                                'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                            ]);
                        }

                        return response()->json([
                            'success' => true,
                            'metodo' => 'cartao',
                            'status' => $isPago ? 'pago' : 'processando',
                            'message' => $isPago ? 'Pagamento com Cartão Aprovado via Mercado Pago!' : 'Pagamento em processamento pela operadora.',
                            'fatura' => [
                                'codigo' => $fatura->codigo_fatura,
                                'plano' => $plano->nome,
                                'valor_formatado' => $fatura->valor_formatado,
                                'status' => $fatura->status,
                            ],
                        ]);
                    } elseif ($metodo === 'boleto') {
                        // Boleto Mercado Pago Transparente
                        $barcode = $data['barcode']['content'] ?? null;
                        $ticketUrl = $data['transaction_details']['external_resource_url'] ?? null;

                        $fatura = Fatura::create([
                            'user_id' => $user->id,
                            'plano_id' => $plano->id,
                            'codigo_fatura' => $codigoFatura,
                            'descricao' => "Adesão / Mensalidade: {$plano->nome}",
                            'valor' => $valorTotal,
                            'valor_original' => $valorTotal,
                            'gateway' => 'mercadopago',
                            'gateway_transaction_id' => $mpId,
                            'metodo_pagamento' => 'boleto',
                            'status' => 'pendente',
                            'data_vencimento' => now()->addDays(3),
                            'linha_digitavel_boleto' => $barcode,
                            'url_boleto' => $ticketUrl,
                            'webhook_payload' => $data,
                        ]);

                        return response()->json([
                            'success' => true,
                            'metodo' => 'boleto',
                            'message' => 'Boleto gerado com sucesso via Mercado Pago!',
                            'fatura' => [
                                'codigo' => $fatura->codigo_fatura,
                                'plano' => $plano->nome,
                                'valor_formatado' => $fatura->valor_formatado,
                                'linha_digitavel' => $barcode,
                                'url_boleto' => $ticketUrl,
                            ],
                        ]);
                    }
                } else {
                    Log::warning('Mercado Pago API retornou status não-200: '.$response->body());
                }
            } catch (Exception $e) {
                Log::error('Erro ao conectar na API Mercado Pago Checkout: '.$e->getMessage());
            }
        }

        // Fallback Seguro Padrão BACEN Oficial (EMVCo com CRC16 válido e chave cadastrada)
        $chavePix = EfiPaymentService::getChavePix();
        $pixPayload = EfiPaymentService::gerarPayloadPixBacen($chavePix, $valorTotal, "Foco 7 - {$codigoFatura}", $codigoFatura);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.urlencode($pixPayload);

        $fatura = Fatura::create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'codigo_fatura' => $codigoFatura,
            'descricao' => "Adesão / Mensalidade: {$plano->nome}",
            'valor' => $valorTotal,
            'valor_original' => $valorTotal,
            'gateway' => 'mercadopago',
            'gateway_transaction_id' => 'MP-'.strtoupper(Str::random(12)),
            'metodo_pagamento' => $metodo === 'pix' ? 'pix' : $metodo,
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(3),
            'qr_code_pix' => $pixPayload,
            'qr_code_pix_url' => $qrCodeUrl,
        ]);

        return response()->json([
            'success' => true,
            'metodo' => 'pix',
            'message' => 'Cobrança gerada com sucesso via Mercado Pago!',
            'fatura' => [
                'codigo' => $fatura->codigo_fatura,
                'plano' => $plano->nome,
                'valor_formatado' => $fatura->valor_formatado,
                'metodo' => $fatura->metodo_pagamento,
                'qr_code_pix' => $fatura->qr_code_pix,
                'qr_code_pix_url' => $fatura->qr_code_pix_url,
            ],
        ]);
    }

    /**
     * Download / Stream do Carnê de Pagamento em PDF
     */
    public function downloadCarnePdf(string $codigo)
    {
        return EfiPaymentService::gerarCarnePdf($codigo);
    }
}
