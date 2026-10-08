<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsSetting;

class GatewaysController extends Controller
{
    /**
     * Exibe o formulário de configurações dos Gateways de Pagamento
     */
    public function index()
    {
        $configs = [
            'mp_ativo' => CmsSetting::get('mercadopago_ativo', '1') == '1',
            'mp_public_key' => CmsSetting::get('mercadopago_public_key', env('MERCADO_PAGO_PUBLIC_KEY', '')),
            'mp_access_token' => CmsSetting::get('mercadopago_access_token', env('MERCADO_PAGO_ACCESS_TOKEN', '')),
            'mp_sandbox' => CmsSetting::get('mercadopago_sandbox', '1') == '1',

            'efi_ativo' => CmsSetting::get('efi_ativo', '1') == '1',
            'efi_client_id' => CmsSetting::get('efi_client_id', env('EFI_CLIENT_ID', '')),
            'efi_client_secret' => CmsSetting::get('efi_client_secret', env('EFI_CLIENT_SECRET', '')),
            'efi_sandbox' => CmsSetting::get('efi_sandbox', '1') == '1',
        ];

        $webhookUrlMp = url('/webhook/mercadopago');
        $webhookUrlEfi = url('/webhook/efi');

        return view('admin.gateways.index', compact('configs', 'webhookUrlMp', 'webhookUrlEfi'));
    }

    /**
     * Salva as configurações dos gateways via AJAX
     */
    public function salvar(Request $request)
    {
        CmsSetting::set('mercadopago_ativo', $request->boolean('mp_ativo') ? '1' : '0', 'gateways');
        CmsSetting::set('mercadopago_public_key', $request->input('mp_public_key', ''), 'gateways');
        CmsSetting::set('mercadopago_access_token', $request->input('mp_access_token', ''), 'gateways');
        CmsSetting::set('mercadopago_sandbox', $request->boolean('mp_sandbox') ? '1' : '0', 'gateways');

        CmsSetting::set('efi_ativo', $request->boolean('efi_ativo') ? '1' : '0', 'gateways');
        CmsSetting::set('efi_client_id', $request->input('efi_client_id', ''), 'gateways');
        CmsSetting::set('efi_client_secret', $request->input('efi_client_secret', ''), 'gateways');
        CmsSetting::set('efi_sandbox', $request->boolean('efi_sandbox') ? '1' : '0', 'gateways');

        return response()->json([
            'success' => true,
            'message' => 'Credenciais dos Gateways salvas com sucesso!',
        ]);
    }
}
