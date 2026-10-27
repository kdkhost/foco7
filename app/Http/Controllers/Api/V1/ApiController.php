<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Models\Filial;
use App\Models\Plano;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Health check / Ping do sistema e versão da API REST
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'API REST Foco 7 Rastreamento operacional',
            'version' => 'v1.0.0',
            'timestamp' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
            'env' => config('app.env'),
            'status' => 'healthy',
        ], 200);
    }

    /**
     * Catálogo público de planos ativos para apps e integrações
     */
    public function planos(): JsonResponse
    {
        $planos = Plano::where('ativo', true)
            ->orderBy('preco_mensal', 'asc')
            ->get()
            ->map(function ($plano) {
                return [
                    'id' => $plano->id,
                    'nome' => $plano->nome,
                    'slug' => $plano->slug,
                    'descricao' => $plano->descricao,
                    'preco_mensal' => (float) $plano->preco_mensal,
                    'preco_formatado' => $plano->preco_formatado,
                    'taxa_adesao' => (float) ($plano->taxa_adesao ?? 0),
                    'taxa_adesao_formatada' => 'R$ '.number_format($plano->taxa_adesao ?? 0, 2, ',', '.'),
                    'destaque' => (bool) $plano->destaque,
                    'beneficios' => is_array($plano->beneficios) ? $plano->beneficios : json_decode($plano->beneficios ?? '[]', true),
                ];
            });

        return response()->json([
            'success' => true,
            'total' => $planos->count(),
            'data' => $planos,
        ], 200);
    }

    /**
     * Lista de unidades e filiais para mapa e localização
     */
    public function filiais(): JsonResponse
    {
        $filiais = Filial::where('ativo', true)
            ->orderBy('cidade', 'asc')
            ->get()
            ->map(function ($filial) {
                return [
                    'id' => $filial->id,
                    'nome' => $filial->nome,
                    'cidade' => $filial->cidade,
                    'estado' => $filial->estado,
                    'bairro' => $filial->bairro,
                    'endereco' => $filial->endereco,
                    'telefone' => $filial->telefone,
                    'whatsapp' => $filial->whatsapp,
                    'latitude' => $filial->latitude ? (float) $filial->latitude : null,
                    'longitude' => $filial->longitude ? (float) $filial->longitude : null,
                ];
            });

        return response()->json([
            'success' => true,
            'total' => $filiais->count(),
            'data' => $filiais,
        ], 200);
    }

    /**
     * Consulta status detalhado de uma fatura por código ou ID
     */
    public function consultarFatura(string $identificador): JsonResponse
    {
        $fatura = Fatura::with(['user:id,name,email,cpf_cnpj,telefone', 'plano:id,nome,preco_mensal'])
            ->where('codigo_fatura', $identificador)
            ->orWhere('id', $identificador)
            ->first();

        if (! $fatura) {
            return response()->json([
                'success' => false,
                'message' => "Fatura '{$identificador}' não localizada.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $fatura->id,
                'codigo_fatura' => $fatura->codigo_fatura,
                'descricao' => $fatura->descricao,
                'valor' => (float) $fatura->valor,
                'valor_formatado' => $fatura->valor_formatado,
                'valor_original' => (float) ($fatura->valor_original ?? $fatura->valor),
                'taxa_repassada' => (float) ($fatura->taxa_repassada ?? 0),
                'status' => $fatura->status,
                'gateway' => $fatura->gateway,
                'metodo_pagamento' => $fatura->metodo_pagamento,
                'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('Y-m-d') : null,
                'data_pagamento' => $fatura->data_pagamento ? $fatura->data_pagamento->toIso8601String() : null,
                'qr_code_pix' => $fatura->qr_code_pix,
                'qr_code_pix_url' => $fatura->qr_code_pix_url,
                'linha_digitavel_boleto' => $fatura->linha_digitavel_boleto,
                'url_boleto' => $fatura->url_boleto,
                'codigo_carne' => $fatura->codigo_carne,
                'numero_parcela' => $fatura->numero_parcela,
                'total_parcelas' => $fatura->total_parcelas,
                'link_pdf_carne' => $fatura->codigo_carne ? route('faturas.carne.pdf', $fatura->codigo_carne) : null,
                'cliente' => $fatura->user ? [
                    'id' => $fatura->user->id,
                    'nome' => $fatura->user->name,
                    'email' => $fatura->user->email,
                    'cpf_cnpj' => $fatura->user->cpf_cnpj,
                    'telefone' => $fatura->user->telefone,
                ] : null,
                'plano' => $fatura->plano ? [
                    'id' => $fatura->plano->id,
                    'nome' => $fatura->plano->nome,
                ] : null,
            ],
        ], 200);
    }
}
