<?php

namespace App\Http\Controllers\Pagamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Fatura;
use App\Models\User;
use App\Models\CmsSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
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
            'metodo_pagamento' => 'required|in:pix,cartao,boleto',
        ], [
            'nome.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'telefone.required' => 'Informe seu telefone ou WhatsApp.',
            'cpf_cnpj.required' => 'Informe seu CPF ou CNPJ.',
        ]);

        $plano = Plano::findOrFail($validated['plano_id']);

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

        $codigoFatura = 'FAT-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $valorTotal = $plano->preco_mensal + $plano->taxa_adesao;

        // Gerar QR Code PIX Copia e Cola dinâmico
        $pixPayload = "00020126580014br.gov.bcb.pix0136123e4567-e89b-12d3-a456-4266141740005204000053039865405"
            . number_format($valorTotal, 2, '.', '')
            . "5802BR5915FOCO 7 SISTEMAS6009SAO PAULO62070503"
            . substr($codigoFatura, 0, 10) . "6304ABCD";

        $fatura = Fatura::create([
            'user_id' => $user->id,
            'plano_id' => $plano->id,
            'codigo_fatura' => $codigoFatura,
            'descricao' => "Adesão / Mensalidade: {$plano->nome}",
            'valor' => $valorTotal,
            'gateway' => 'mercadopago',
            'metodo_pagamento' => $validated['metodo_pagamento'],
            'status' => 'pendente',
            'data_vencimento' => now()->addDays(3),
            'qr_code_pix' => $pixPayload,
            'qr_code_pix_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($pixPayload),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cobrança gerada com sucesso! Conclua o pagamento para ativação.',
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
}
