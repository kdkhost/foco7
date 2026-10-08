<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;
use Illuminate\Support\Facades\File;

class GatewaysController extends Controller
{
    /**
     * Exibe o formulário de configurações dos Gateways de Pagamento
     */
    public function index()
    {
        $configs = [
            // Mercado Pago
            'mp_ativo' => CmsSetting::get('mercadopago_ativo', '1') == '1',
            'mp_public_key' => CmsSetting::get('mercadopago_public_key', ''),
            'mp_access_token' => CmsSetting::get('mercadopago_access_token', ''),
            'mp_client_id' => CmsSetting::get('mercadopago_client_id', ''),
            'mp_client_secret' => CmsSetting::get('mercadopago_client_secret', ''),
            'mp_webhook_secret' => CmsSetting::get('mercadopago_webhook_secret', ''),
            'mp_sandbox' => CmsSetting::get('mercadopago_sandbox', '1') == '1',

            // Efí PRO (Gerencianet)
            'efi_ativo' => CmsSetting::get('efi_ativo', '1') == '1',
            'efi_client_id' => CmsSetting::get('efi_client_id', ''),
            'efi_client_secret' => CmsSetting::get('efi_client_secret', ''),
            'efi_chave_pix' => CmsSetting::get('efi_chave_pix', ''),
            'efi_certificado_pem' => CmsSetting::get('efi_certificado_pem', ''),
            'efi_sandbox' => CmsSetting::get('efi_sandbox', '1') == '1',

            // Regras Comerciais & Financeiras
            'pix_desconto' => CmsSetting::get('pix_desconto', '5'),
            'pix_expiracao_minutos' => CmsSetting::get('pix_expiracao_minutos', '30'),
            'cartao_max_parcelas' => CmsSetting::get('cartao_max_parcelas', '12'),
            'cartao_sem_juros' => CmsSetting::get('cartao_sem_juros', '3'),
            'cartao_juros_mensal' => CmsSetting::get('cartao_juros_mensal', '1.99'),
            'boleto_dias_vencimento' => CmsSetting::get('boleto_dias_vencimento', '3'),
            'multa_atraso' => CmsSetting::get('multa_atraso', '2.0'),
            'juros_diario' => CmsSetting::get('juros_diario', '0.033'),
            'instrucoes_cobranca' => CmsSetting::get('instrucoes_cobranca', 'Não receber após 30 dias do vencimento. Sujeito a inclusão nos órgãos de proteção ao crédito.'),
        ];

        $webhookUrlMp = url('/webhook/mercadopago');
        $webhookUrlEfi = url('/webhook/efi');

        return view('admin.gateways.index', compact('configs', 'webhookUrlMp', 'webhookUrlEfi'));
    }

    /**
     * Salva todas as configurações dos gateways e regras financeiras via AJAX
     */
    public function salvar(Request $request)
    {
        // 1. Mercado Pago
        CmsSetting::set('mercadopago_ativo', $request->boolean('mp_ativo') ? '1' : '0', 'gateways');
        CmsSetting::set('mercadopago_public_key', $request->input('mp_public_key', ''), 'gateways');
        if ($request->filled('mp_access_token')) {
            CmsSetting::set('mercadopago_access_token', $request->input('mp_access_token'), 'gateways');
        }
        CmsSetting::set('mercadopago_client_id', $request->input('mp_client_id', ''), 'gateways');
        if ($request->filled('mp_client_secret')) {
            CmsSetting::set('mercadopago_client_secret', $request->input('mp_client_secret'), 'gateways');
        }
        if ($request->filled('mp_webhook_secret')) {
            CmsSetting::set('mercadopago_webhook_secret', $request->input('mp_webhook_secret'), 'gateways');
        }
        CmsSetting::set('mercadopago_sandbox', $request->boolean('mp_sandbox') ? '1' : '0', 'gateways');

        // 2. Efí PRO (Gerencianet)
        CmsSetting::set('efi_ativo', $request->boolean('efi_ativo') ? '1' : '0', 'gateways');
        CmsSetting::set('efi_client_id', $request->input('efi_client_id', ''), 'gateways');
        if ($request->filled('efi_client_secret')) {
            CmsSetting::set('efi_client_secret', $request->input('efi_client_secret'), 'gateways');
        }
        CmsSetting::set('efi_chave_pix', $request->input('efi_chave_pix', ''), 'gateways');
        CmsSetting::set('efi_sandbox', $request->boolean('efi_sandbox') ? '1' : '0', 'gateways');

        // Upload do Certificado .pem via Drag & Drop
        if ($request->has('remover_efi_certificado') && $request->remover_efi_certificado == '1') {
            $antigo = CmsSetting::get('efi_certificado_pem', '');
            if ($antigo && file_exists(storage_path('certs/' . $antigo))) {
                @unlink(storage_path('certs/' . $antigo));
            }
            CmsSetting::set('efi_certificado_pem', '', 'gateways');
        }

        if ($request->hasFile('efi_certificado')) {
            $file = $request->file('efi_certificado');
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext !== 'pem') {
                return response()->json([
                    'success' => false,
                    'message' => 'O certificado deve ser obrigatoriamente um arquivo com extensão .pem gerado na Efí PRO.',
                ], 422);
            }

            $certsPath = storage_path('certs');
            if (!File::isDirectory($certsPath)) {
                File::makeDirectory($certsPath, 0700, true, true);
            }

            $nomeCertificado = 'efi_cert_' . time() . '.pem';
            $file->move($certsPath, $nomeCertificado);
            CmsSetting::set('efi_certificado_pem', $nomeCertificado, 'gateways');
        }

        // 3. Regras Comerciais & Financeiras
        CmsSetting::set('pix_desconto', $request->input('pix_desconto', '0'), 'financeiro');
        CmsSetting::set('pix_expiracao_minutos', $request->input('pix_expiracao_minutos', '30'), 'financeiro');
        CmsSetting::set('cartao_max_parcelas', $request->input('cartao_max_parcelas', '12'), 'financeiro');
        CmsSetting::set('cartao_sem_juros', $request->input('cartao_sem_juros', '1'), 'financeiro');
        CmsSetting::set('cartao_juros_mensal', $request->input('cartao_juros_mensal', '0'), 'financeiro');
        CmsSetting::set('boleto_dias_vencimento', $request->input('boleto_dias_vencimento', '3'), 'financeiro');
        CmsSetting::set('multa_atraso', $request->input('multa_atraso', '2.0'), 'financeiro');
        CmsSetting::set('juros_diario', $request->input('juros_diario', '0.033'), 'financeiro');
        CmsSetting::set('instrucoes_cobranca', $request->input('instrucoes_cobranca', ''), 'financeiro');

        return response()->json([
            'success' => true,
            'message' => 'Configurações do Portal de Pagamentos salvas com sucesso!',
        ]);
    }
}
