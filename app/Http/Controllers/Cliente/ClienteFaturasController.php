<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fatura;

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

        // Se a fatura ainda não tem dados PIX, gerar dados demonstrativos / gateway
        if (empty($fatura->qr_code_pix)) {
            $fatura->qr_code_pix = "00020126580014br.gov.bcb.pix0136123e4567-e89b-12d3-a456-4266141740005204000053039865405" . number_format($fatura->valor, 2, '.', '') . "5802BR5915FOCO 7 SISTEMAS6009SAO PAULO62070503***6304ABCD";
            $fatura->qr_code_pix_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($fatura->qr_code_pix);
            $fatura->save();
        }

        return response()->json([
            'success' => true,
            'fatura' => [
                'codigo' => $fatura->codigo_fatura,
                'descricao' => $fatura->descricao,
                'valor_formatado' => $fatura->valor_formatado,
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
            ],
        ]);
    }
}
