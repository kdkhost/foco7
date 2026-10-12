<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;
use App\Services\EfiPaymentService;

class ClienteFaturasController extends Controller
{
    /**
     * Lista todas as faturas do cliente autenticado
     */
    public function index()
    {
        $user = auth()->user();

        $faturas = Fatura::with('plano')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('cliente.faturas', compact('faturas'));
    }

    /**
     * Retorna detalhes da fatura e QR Code PIX para modal de pagamento
     */
    public function detalhes($codigo)
    {
        $fatura = Fatura::with('plano')
            ->where('user_id', auth()->id())
            ->where('codigo_fatura', $codigo)
            ->firstOrFail();

        // Se a fatura ainda não tem dados PIX, gerar via BACEN Oficial padrão com chave real
        if (empty($fatura->qr_code_pix)) {
            $chavePix = EfiPaymentService::getChavePix();
            $valorCobranca = $fatura->valor_total_com_encargos > 0 ? (float) $fatura->valor_total_com_encargos : (float) $fatura->valor;
            $fatura->qr_code_pix = EfiPaymentService::gerarPayloadPixBacen($chavePix, $valorCobranca, "Foco 7 - {$fatura->codigo_fatura}", $fatura->codigo_fatura);
            $fatura->qr_code_pix_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($fatura->qr_code_pix);
            $fatura->save();
        }

        return response()->json([
            'success' => true,
            'fatura' => [
                'codigo' => $fatura->codigo_fatura,
                'descricao' => $fatura->descricao,
                'valor' => (float) $fatura->valor,
                'valor_formatado' => $fatura->valor_formatado,
                'valor_total_com_encargos' => (float) $fatura->valor_total_com_encargos,
                'valor_total_formatado' => 'R$ ' . number_format($fatura->valor_total_com_encargos, 2, ',', '.'),
                'valor_multa' => (float) ($fatura->valor_multa ?? 0),
                'multa_formatada' => $fatura->multa_formatada,
                'valor_juros' => (float) ($fatura->valor_juros ?? 0),
                'juros_formatado' => $fatura->juros_formatado,
                'dias_atraso' => $fatura->dias_atraso,
                'is_vencida' => $fatura->isVencida(),
                'status' => $fatura->status,
                'vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : 'A definir',
                'qr_code_pix' => $fatura->qr_code_pix,
                'qr_code_pix_url' => $fatura->qr_code_pix_url,
                'linha_digitavel' => $fatura->linha_digitavel_boleto,
                'url_boleto' => $fatura->url_boleto,
                'is_carne' => $fatura->isCarne(),
                'codigo_carne' => $fatura->codigo_carne,
                'numero_parcela' => $fatura->numero_parcela,
                'total_parcelas' => $fatura->total_parcelas,
                'pdf_carne_url' => $fatura->isCarne() ? route('faturas.carne.pdf', $fatura->codigo_carne) : null,
                'taxa_repassada' => (float) $fatura->taxa_repassada,
                'taxa_repassada_formatada' => $fatura->taxa_repassada_formatada,
                'valor_original_formatado' => $fatura->valor_original_formatada,
                'ultimo_lembrete_enviado' => $fatura->ultimo_lembrete_enviado,
            ],
        ]);
    }
}
