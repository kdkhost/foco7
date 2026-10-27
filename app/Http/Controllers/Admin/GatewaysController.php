<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Plano;
use App\Models\User;
use App\Services\EfiPaymentService;
use App\Services\EmailTemplateService;
use App\Services\ReguaCobrancaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GatewaysController extends Controller
{
    /**
     * Exibe o Portal de Gateways organizado por guias com alternador dinâmico de ambiente
     */
    public function index()
    {
        $configs = [
            // Gateway Padrão Ativo
            'gateway_padrao' => CmsSetting::get('gateway_padrao', 'efi'),

            // ==========================================
            // 1. MERCADO PAGO
            // ==========================================
            'mp_ativo' => CmsSetting::get('mercadopago_ativo', '1') == '1',
            'mp_ambiente' => CmsSetting::get('mercadopago_ambiente', 'sandbox'), // 'sandbox' ou 'producao'

            // Credenciais de Sandbox (Homologação)
            'mp_public_key_sandbox' => CmsSetting::get('mercadopago_public_key_sandbox', CmsSetting::get('mercadopago_public_key', '')),
            'mp_access_token_sandbox' => CmsSetting::get('mercadopago_access_token_sandbox', CmsSetting::get('mercadopago_access_token', '')),
            'mp_client_id_sandbox' => CmsSetting::get('mercadopago_client_id_sandbox', CmsSetting::get('mercadopago_client_id', '')),
            'mp_client_secret_sandbox' => CmsSetting::get('mercadopago_client_secret_sandbox', CmsSetting::get('mercadopago_client_secret', '')),

            // Credenciais de Produção Real
            'mp_public_key_prod' => CmsSetting::get('mercadopago_public_key_prod', ''),
            'mp_access_token_prod' => CmsSetting::get('mercadopago_access_token_prod', ''),
            'mp_client_id_prod' => CmsSetting::get('mercadopago_client_id_prod', ''),
            'mp_client_secret_prod' => CmsSetting::get('mercadopago_client_secret_prod', ''),

            'mp_webhook_secret' => CmsSetting::get('mercadopago_webhook_secret', ''),
            'mp_aceita_pix' => CmsSetting::get('mp_aceita_pix', '1') == '1',
            'mp_aceita_cartao' => CmsSetting::get('mp_aceita_cartao', '1') == '1',
            'mp_aceita_boleto' => CmsSetting::get('mp_aceita_boleto', '1') == '1',

            // ==========================================
            // 2. EFÍ PRO (GERENCIANET)
            // ==========================================
            'efi_ativo' => CmsSetting::get('efi_ativo', '1') == '1',
            'efi_ambiente' => CmsSetting::get('efi_ambiente', 'sandbox'), // 'sandbox' ou 'producao'

            // Credenciais de Sandbox (Homologação)
            'efi_client_id_sandbox' => CmsSetting::get('efi_client_id_sandbox', CmsSetting::get('efi_client_id', '')),
            'efi_client_secret_sandbox' => CmsSetting::get('efi_client_secret_sandbox', CmsSetting::get('efi_client_secret', '')),
            'efi_chave_pix_sandbox' => CmsSetting::get('efi_chave_pix_sandbox', CmsSetting::get('efi_chave_pix', '')),
            'efi_certificado_pem_sandbox' => CmsSetting::get('efi_certificado_pem_sandbox', CmsSetting::get('efi_certificado_pem', '')),

            // Credenciais de Produção Real
            'efi_client_id_prod' => CmsSetting::get('efi_client_id_prod', ''),
            'efi_client_secret_prod' => CmsSetting::get('efi_client_secret_prod', ''),
            'efi_chave_pix_prod' => CmsSetting::get('efi_chave_pix_prod', ''),
            'efi_certificado_pem_prod' => CmsSetting::get('efi_certificado_pem_prod', ''),

            'efi_aceita_pix' => CmsSetting::get('efi_aceita_pix', '1') == '1',
            'efi_aceita_boleto' => CmsSetting::get('efi_aceita_boleto', '1') == '1',
            'efi_aceita_cartao' => CmsSetting::get('efi_aceita_cartao', '1') == '1',
            'efi_aceita_carne' => CmsSetting::get('efi_aceita_carne', '1') == '1',

            // Regras Comerciais & Financeiras Gerais
            'pix_desconto' => CmsSetting::get('pix_desconto', '5'),
            'pix_expiracao_minutos' => CmsSetting::get('pix_expiracao_minutos', '30'),
            'cartao_max_parcelas' => CmsSetting::get('cartao_max_parcelas', '12'),
            'cartao_sem_juros' => CmsSetting::get('cartao_sem_juros', '3'),
            'cartao_juros_mensal' => CmsSetting::get('cartao_juros_mensal', '1.99'),
            'boleto_dias_vencimento' => CmsSetting::get('boleto_dias_vencimento', '3'),
            'multa_atraso' => CmsSetting::get('multa_atraso', '2.0'),
            'juros_diario' => CmsSetting::get('juros_diario', '0.033'),
            'instrucoes_cobranca' => CmsSetting::get('instrucoes_cobranca', 'Não receber após 30 dias do vencimento. Sujeito a inclusão nos órgãos de proteção ao crédito.'),

            // Parâmetros de Repasse de Taxas ao Cliente
            'repassar_taxas_cliente' => CmsSetting::get('repassar_taxas_cliente', '0') == '1',
            'taxa_pix_tipo' => CmsSetting::get('taxa_pix_tipo', 'percentual'),
            'taxa_pix_valor' => CmsSetting::get('taxa_pix_valor', '0.00'),
            'taxa_boleto_fixo' => CmsSetting::get('taxa_boleto_fixo', '2.99'),
            'taxa_carne_parcela_fixo' => CmsSetting::get('taxa_carne_parcela_fixo', '2.99'),
            'taxa_cartao_valor' => CmsSetting::get('taxa_cartao_valor', '3.49'),
            'taxa_cartao_fixo' => CmsSetting::get('taxa_cartao_fixo', '0.49'),

            // ==========================================
            // 3. COBRANÇA POR CARNÊ (EFÍ PRO)
            // ==========================================
            'carne_ativo' => CmsSetting::get('carne_ativo', '1') == '1',
            'carne_max_parcelas' => CmsSetting::get('carne_max_parcelas', '12'),
            'carne_dias_primeiro_vencimento' => CmsSetting::get('carne_dias_primeiro_vencimento', '10'),
            'carne_intervalo_dias' => CmsSetting::get('carne_intervalo_dias', '30'),
            'carne_instrucoes' => CmsSetting::get('carne_instrucoes', 'Cobrança bancária emitida via Efí PRO para clientes Foco 7. Em caso de dúvidas, consulte nossa central pelo WhatsApp.'),
            'carne_envio_email' => CmsSetting::get('carne_envio_email', '1') == '1',
            'carne_envio_whatsapp' => CmsSetting::get('carne_envio_whatsapp', '1') == '1',

            // ==========================================
            // 4. RÉGUA DE COBRANÇA AUTOMATIZADA
            // ==========================================
            'regua_cobranca_ativa' => CmsSetting::get('regua_cobranca_ativa', '1') == '1',
            'gerar_mensalidade_dias_antes' => CmsSetting::get('gerar_mensalidade_dias_antes', '15'),
            'regua_lembrete_1_ativo' => CmsSetting::get('regua_lembrete_1_ativo', '1') == '1',
            'regua_lembrete_1_dias' => CmsSetting::get('regua_lembrete_1_dias', '10'),
            'regua_lembrete_2_ativo' => CmsSetting::get('regua_lembrete_2_ativo', '1') == '1',
            'regua_lembrete_2_dias' => CmsSetting::get('regua_lembrete_2_dias', '5'),
            'regua_lembrete_3_ativo' => CmsSetting::get('regua_lembrete_3_ativo', '1') == '1',
            'regua_lembrete_3_dias' => CmsSetting::get('regua_lembrete_3_dias', '1'),
            'regua_vencimento_hoje_ativo' => CmsSetting::get('regua_vencimento_hoje_ativo', '1') == '1',
            'regua_atraso_1_ativo' => CmsSetting::get('regua_atraso_1_ativo', '1') == '1',
            'regua_atraso_1_dias' => CmsSetting::get('regua_atraso_1_dias', '1'),
            'regua_atraso_2_ativo' => CmsSetting::get('regua_atraso_2_ativo', '1') == '1',
            'regua_atraso_2_dias' => CmsSetting::get('regua_atraso_2_dias', '5'),
            'regua_atraso_3_ativo' => CmsSetting::get('regua_atraso_3_ativo', '1') == '1',
            'regua_atraso_3_dias' => CmsSetting::get('regua_atraso_3_dias', '10'),
            'regua_multa_percentual' => CmsSetting::get('regua_multa_percentual', '2.0'),
            'regua_juros_mensal_percentual' => CmsSetting::get('regua_juros_mensal_percentual', '1.0'),
            'regua_juros_diario_percentual' => CmsSetting::get('regua_juros_diario_percentual', '0.033'),
            'regua_dias_tolerancia' => CmsSetting::get('regua_dias_tolerancia', '0'),
            'regua_enviar_email' => CmsSetting::get('regua_enviar_email', '1') == '1',
            'regua_enviar_whatsapp' => CmsSetting::get('regua_enviar_whatsapp', '0') == '1',
        ];

        // URLs Oficiais de Webhook
        $webhookUrlMp = url('/webhook/mercadopago');
        $webhookUrlEfi = url('/webhook/efi');

        // Clientes e Planos para a emissão de carnê sob demanda
        $clientes = User::where('role', 'cliente')->where('status', 'ativo')->orderBy('name')->get();
        $planos = Plano::where('ativo', true)->orderBy('nome')->get();

        // Templates de E-mails Próprios
        $templatesEmail = EmailTemplateService::getTemplates();
        foreach ($templatesEmail as $chave => &$tpl) {
            $tpl['assunto_atual'] = EmailTemplateService::getAssunto($chave);
            $tpl['corpo_atual'] = EmailTemplateService::getCorpo($chave);
        }

        // Histórico de Execução de Tarefas Cron
        $cronStatus = [
            'comando_cpanel' => '* * * * * cd '.base_path().' && php artisan schedule:run >> /dev/null 2>&1',
            'rotinas' => [
                'regua_cobranca' => [
                    'nome' => 'Régua de Cobrança & Mensalidades Recorrentes',
                    'comando' => 'faturas:processar-regua',
                    'frequencia' => 'Diário às 07:00 e 13:00',
                    'descricao' => 'Gera mensalidades com antecedência, dispara 3 lembretes preventivos, cobrança no vencimento e 3 avisos de atraso com multa e juros.',
                    'ultima_execucao' => CmsSetting::get('cron_last_run_regua_cobranca', 'Aguardando agendador'),
                ],
                'verificar_vencidas' => [
                    'nome' => 'Verificação de Faturas Vencidas',
                    'comando' => 'faturas:verificar-vencidas',
                    'frequencia' => 'Diário às 01:00',
                    'descricao' => 'Atualiza status de faturas pendentes expiradas para vencida e dispara e-mail de alerta ao cliente.',
                    'ultima_execucao' => CmsSetting::get('cron_last_run_verificar_vencidas', 'Aguardando agendador'),
                ],
                'enviar_lembretes' => [
                    'nome' => 'Envio de Lembretes Preventivos',
                    'comando' => 'faturas:enviar-lembretes',
                    'frequencia' => 'Diário às 08:30',
                    'descricao' => 'Localiza faturas com vencimento nos próximos 3 dias e dispara lembrete amigável.',
                    'ultima_execucao' => CmsSetting::get('cron_last_run_enviar_lembretes', 'Aguardando agendador'),
                ],
                'sincronizar_pagamentos' => [
                    'nome' => 'Sincronização de Pagamentos em Gateways',
                    'comando' => 'gateways:sincronizar-pagamentos',
                    'frequencia' => 'A cada 15 minutos',
                    'descricao' => 'Consulta status na API do Mercado Pago e Efí PRO para compensar pagamentos que não receberam webhook.',
                    'ultima_execucao' => CmsSetting::get('cron_last_run_sincronizar_pagamentos', 'Aguardando agendador'),
                ],
                'limpar_cache' => [
                    'nome' => 'Otimização e Limpeza de Cache',
                    'comando' => 'cache:clear && view:clear',
                    'frequencia' => 'Semanal (ou sob demanda)',
                    'descricao' => 'Descarrega views e caches para manter o painel e site rápidos em cPanel/LiteSpeed.',
                    'ultima_execucao' => CmsSetting::get('cron_last_run_limpar_cache', 'Aguardando agendador'),
                ],
            ],
        ];

        return view('admin.gateways.index', compact(
            'configs', 'webhookUrlMp', 'webhookUrlEfi', 'clientes', 'planos', 'templatesEmail', 'cronStatus'
        ));
    }

    /**
     * Salva as configurações de Mercado Pago, Efí PRO e Carnê
     */
    public function salvar(Request $request)
    {
        // 1. MERCADO PAGO
        CmsSetting::set('mercadopago_ativo', $request->boolean('mp_ativo') ? '1' : '0', 'gateways');
        $mpAmbiente = $request->input('mp_ambiente', $request->boolean('mp_sandbox', true) ? 'sandbox' : 'producao');
        CmsSetting::set('mercadopago_ambiente', $mpAmbiente, 'gateways');
        CmsSetting::set('mercadopago_sandbox', $mpAmbiente === 'sandbox' ? '1' : '0', 'gateways');

        $mpPubSandbox = $request->filled('mp_public_key_sandbox') ? $request->input('mp_public_key_sandbox') : $request->input('mp_public_key', '');
        $mpTokenSandbox = $request->filled('mp_access_token_sandbox') ? $request->input('mp_access_token_sandbox') : $request->input('mp_access_token', '');
        $mpClientSandbox = $request->filled('mp_client_id_sandbox') ? $request->input('mp_client_id_sandbox') : $request->input('mp_client_id', '');
        $mpSecretSandbox = $request->filled('mp_client_secret_sandbox') ? $request->input('mp_client_secret_sandbox') : $request->input('mp_client_secret', '');

        $mpPubProd = $request->input('mp_public_key_prod', '');
        $mpTokenProd = $request->input('mp_access_token_prod', '');
        $mpClientProd = $request->input('mp_client_id_prod', '');
        $mpSecretProd = $request->input('mp_client_secret_prod', '');

        // Salva Sandbox Mercado Pago
        if ($mpPubSandbox !== '') {
            CmsSetting::set('mercadopago_public_key_sandbox', $mpPubSandbox, 'gateways');
        }
        if ($mpTokenSandbox !== '') {
            CmsSetting::set('mercadopago_access_token_sandbox', $mpTokenSandbox, 'gateways');
        }
        if ($mpClientSandbox !== '') {
            CmsSetting::set('mercadopago_client_id_sandbox', $mpClientSandbox, 'gateways');
        }
        if ($mpSecretSandbox !== '') {
            CmsSetting::set('mercadopago_client_secret_sandbox', $mpSecretSandbox, 'gateways');
        }

        // Salva Produção Mercado Pago
        if ($mpPubProd !== '') {
            CmsSetting::set('mercadopago_public_key_prod', $mpPubProd, 'gateways');
        }
        if ($mpTokenProd !== '') {
            CmsSetting::set('mercadopago_access_token_prod', $mpTokenProd, 'gateways');
        }
        if ($mpClientProd !== '') {
            CmsSetting::set('mercadopago_client_id_prod', $mpClientProd, 'gateways');
        }
        if ($mpSecretProd !== '') {
            CmsSetting::set('mercadopago_client_secret_prod', $mpSecretProd, 'gateways');
        }

        // Mantém retrocompatibilidade com chaves legadas baseando-se no ambiente ativo
        if ($mpAmbiente === 'sandbox') {
            CmsSetting::set('mercadopago_public_key', $mpPubSandbox ?: CmsSetting::get('mercadopago_public_key_sandbox', ''), 'gateways');
            if ($mpTokenSandbox !== '') {
                CmsSetting::set('mercadopago_access_token', $mpTokenSandbox, 'gateways');
            }
            if ($mpClientSandbox !== '') {
                CmsSetting::set('mercadopago_client_id', $mpClientSandbox, 'gateways');
            }
            if ($mpSecretSandbox !== '') {
                CmsSetting::set('mercadopago_client_secret', $mpSecretSandbox, 'gateways');
            }
        } else {
            CmsSetting::set('mercadopago_public_key', $mpPubProd ?: CmsSetting::get('mercadopago_public_key_prod', ''), 'gateways');
            if ($mpTokenProd !== '') {
                CmsSetting::set('mercadopago_access_token', $mpTokenProd, 'gateways');
            }
            if ($mpClientProd !== '') {
                CmsSetting::set('mercadopago_client_id', $mpClientProd, 'gateways');
            }
            if ($mpSecretProd !== '') {
                CmsSetting::set('mercadopago_client_secret', $mpSecretProd, 'gateways');
            }
        }

        if ($request->filled('mp_webhook_secret')) {
            CmsSetting::set('mercadopago_webhook_secret', $request->input('mp_webhook_secret'), 'gateways');
        }
        CmsSetting::set('mp_aceita_pix', $request->boolean('mp_aceita_pix') ? '1' : '0', 'gateways');
        CmsSetting::set('mp_aceita_cartao', $request->boolean('mp_aceita_cartao') ? '1' : '0', 'gateways');
        CmsSetting::set('mp_aceita_boleto', $request->boolean('mp_aceita_boleto') ? '1' : '0', 'gateways');

        // 2. EFÍ PRO (GERENCIANET)
        CmsSetting::set('efi_ativo', $request->boolean('efi_ativo') ? '1' : '0', 'gateways');
        $efiAmbiente = $request->input('efi_ambiente', $request->boolean('efi_sandbox', true) ? 'sandbox' : 'producao');
        CmsSetting::set('efi_ambiente', $efiAmbiente, 'gateways');
        CmsSetting::set('efi_sandbox', $efiAmbiente === 'sandbox' ? '1' : '0', 'gateways');

        $efiClientSandbox = $request->filled('efi_client_id_sandbox') ? $request->input('efi_client_id_sandbox') : $request->input('efi_client_id', '');
        $efiSecretSandbox = $request->filled('efi_client_secret_sandbox') ? $request->input('efi_client_secret_sandbox') : $request->input('efi_client_secret', '');
        $efiPixSandbox = $request->filled('efi_chave_pix_sandbox') ? $request->input('efi_chave_pix_sandbox') : $request->input('efi_chave_pix', '');

        $efiClientProd = $request->input('efi_client_id_prod', '');
        $efiSecretProd = $request->input('efi_client_secret_prod', '');
        $efiPixProd = $request->input('efi_chave_pix_prod', '');

        // Sandbox Efí
        if ($efiClientSandbox !== '') {
            CmsSetting::set('efi_client_id_sandbox', $efiClientSandbox, 'gateways');
        }
        if ($efiSecretSandbox !== '') {
            CmsSetting::set('efi_client_secret_sandbox', $efiSecretSandbox, 'gateways');
        }
        if ($efiPixSandbox !== '') {
            CmsSetting::set('efi_chave_pix_sandbox', $efiPixSandbox, 'gateways');
        }

        // Produção Efí
        if ($efiClientProd !== '') {
            CmsSetting::set('efi_client_id_prod', $efiClientProd, 'gateways');
        }
        if ($efiSecretProd !== '') {
            CmsSetting::set('efi_client_secret_prod', $efiSecretProd, 'gateways');
        }
        if ($efiPixProd !== '') {
            CmsSetting::set('efi_chave_pix_prod', $efiPixProd, 'gateways');
        }

        // Retrocompatibilidade Efí
        if ($efiAmbiente === 'sandbox') {
            CmsSetting::set('efi_client_id', $efiClientSandbox ?: CmsSetting::get('efi_client_id_sandbox', ''), 'gateways');
            if ($efiSecretSandbox !== '') {
                CmsSetting::set('efi_client_secret', $efiSecretSandbox, 'gateways');
            }
            CmsSetting::set('efi_chave_pix', $efiPixSandbox ?: CmsSetting::get('efi_chave_pix_sandbox', ''), 'gateways');
        } else {
            CmsSetting::set('efi_client_id', $efiClientProd ?: CmsSetting::get('efi_client_id_prod', ''), 'gateways');
            if ($efiSecretProd !== '') {
                CmsSetting::set('efi_client_secret', $efiSecretProd, 'gateways');
            }
            CmsSetting::set('efi_chave_pix', $efiPixProd ?: CmsSetting::get('efi_chave_pix_prod', ''), 'gateways');
        }

        // Upload Certificados .PEM Sandbox e Produção
        $certsPath = storage_path('certs');
        if (! File::isDirectory($certsPath)) {
            File::makeDirectory($certsPath, 0700, true, true);
        }

        // Certificado Sandbox
        if ($request->boolean('remover_efi_cert_sandbox') || $request->boolean('remover_efi_certificado_sandbox')) {
            CmsSetting::set('efi_certificado_pem_sandbox', '', 'gateways');
            if ($efiAmbiente === 'sandbox') {
                CmsSetting::set('efi_certificado_pem', '', 'gateways');
            }
        }
        if ($request->hasFile('efi_certificado_sandbox')) {
            $file = $request->file('efi_certificado_sandbox');
            if (strtolower($file->getClientOriginalExtension()) === 'pem') {
                $nome = 'efi_cert_sandbox_'.time().'.pem';
                $file->move($certsPath, $nome);
                CmsSetting::set('efi_certificado_pem_sandbox', $nome, 'gateways');
                if ($efiAmbiente === 'sandbox') {
                    CmsSetting::set('efi_certificado_pem', $nome, 'gateways');
                }
            }
        }

        // Certificado Produção
        if ($request->boolean('remover_efi_cert_prod') || $request->boolean('remover_efi_certificado_prod')) {
            CmsSetting::set('efi_certificado_pem_prod', '', 'gateways');
            if ($efiAmbiente === 'producao') {
                CmsSetting::set('efi_certificado_pem', '', 'gateways');
            }
        }
        if ($request->hasFile('efi_certificado_prod')) {
            $file = $request->file('efi_certificado_prod');
            if (strtolower($file->getClientOriginalExtension()) === 'pem') {
                $nome = 'efi_cert_prod_'.time().'.pem';
                $file->move($certsPath, $nome);
                CmsSetting::set('efi_certificado_pem_prod', $nome, 'gateways');
                if ($efiAmbiente === 'producao') {
                    CmsSetting::set('efi_certificado_pem', $nome, 'gateways');
                }
            }
        }

        CmsSetting::set('efi_aceita_pix', $request->boolean('efi_aceita_pix') ? '1' : '0', 'gateways');
        CmsSetting::set('efi_aceita_boleto', $request->boolean('efi_aceita_boleto') ? '1' : '0', 'gateways');
        CmsSetting::set('efi_aceita_cartao', $request->boolean('efi_aceita_cartao') ? '1' : '0', 'gateways');
        CmsSetting::set('efi_aceita_carne', $request->boolean('efi_aceita_carne') ? '1' : '0', 'gateways');

        // 3. REGRAS FINANCEIRAS & REPASSE DE TAXAS
        if ($request->has('gateway_padrao')) {
            CmsSetting::set('gateway_padrao', $request->input('gateway_padrao', 'efi'), 'gateways');
        }

        CmsSetting::set('repassar_taxas_cliente', $request->boolean('repassar_taxas_cliente') ? '1' : '0', 'financeiro');
        CmsSetting::set('taxa_pix_tipo', $request->input('taxa_pix_tipo', 'percentual'), 'financeiro');
        CmsSetting::set('taxa_pix_valor', $request->input('taxa_pix_valor', '0.00'), 'financeiro');
        CmsSetting::set('taxa_boleto_fixo', $request->input('taxa_boleto_fixo', '2.99'), 'financeiro');
        CmsSetting::set('taxa_carne_parcela_fixo', $request->input('taxa_carne_parcela_fixo', '2.99'), 'financeiro');
        CmsSetting::set('taxa_cartao_valor', $request->input('taxa_cartao_valor', '3.49'), 'financeiro');
        CmsSetting::set('taxa_cartao_fixo', $request->input('taxa_cartao_fixo', '0.49'), 'financeiro');

        CmsSetting::set('pix_desconto', $request->input('pix_desconto', '0'), 'financeiro');
        CmsSetting::set('pix_expiracao_minutos', $request->input('pix_expiracao_minutos', '30'), 'financeiro');
        CmsSetting::set('cartao_max_parcelas', $request->input('cartao_max_parcelas', '12'), 'financeiro');
        CmsSetting::set('cartao_sem_juros', $request->input('cartao_sem_juros', '1'), 'financeiro');
        CmsSetting::set('cartao_juros_mensal', $request->input('cartao_juros_mensal', '0'), 'financeiro');
        CmsSetting::set('boleto_dias_vencimento', $request->input('boleto_dias_vencimento', '3'), 'financeiro');
        CmsSetting::set('multa_atraso', $request->input('multa_atraso', '2.0'), 'financeiro');
        CmsSetting::set('juros_diario', $request->input('juros_diario', '0.033'), 'financeiro');
        CmsSetting::set('instrucoes_cobranca', $request->input('instrucoes_cobranca', ''), 'financeiro');

        // 4. CARNÊ EFÍ PRO
        CmsSetting::set('carne_ativo', $request->boolean('carne_ativo') ? '1' : '0', 'gateways');
        CmsSetting::set('carne_max_parcelas', $request->input('carne_max_parcelas', '12'), 'gateways');
        CmsSetting::set('carne_dias_primeiro_vencimento', $request->input('carne_dias_primeiro_vencimento', '10'), 'gateways');
        CmsSetting::set('carne_intervalo_dias', $request->input('carne_intervalo_dias', '30'), 'gateways');
        CmsSetting::set('carne_instrucoes', $request->input('carne_instrucoes', ''), 'gateways');
        CmsSetting::set('carne_envio_email', $request->boolean('carne_envio_email') ? '1' : '0', 'gateways');
        CmsSetting::set('carne_envio_whatsapp', $request->boolean('carne_envio_whatsapp') ? '1' : '0', 'gateways');

        // 5. RÉGUA DE COBRANÇA AUTOMATIZADA
        $reguaAtiva = ($request->boolean('cobranca_regua_ativa') || $request->boolean('regua_cobranca_ativa')) ? '1' : '0';
        CmsSetting::set('regua_cobranca_ativa', $reguaAtiva, 'financeiro');
        CmsSetting::set('cobranca_regua_ativa', $reguaAtiva, 'gateways');

        $diasAntecedencia = $request->input('cobranca_dias_antecedencia_fatura', $request->input('gerar_mensalidade_dias_antes', '10'));
        CmsSetting::set('gerar_mensalidade_dias_antes', $diasAntecedencia, 'financeiro');
        CmsSetting::set('cobranca_dias_antecedencia_fatura', $diasAntecedencia, 'gateways');

        $lembrete1 = $request->input('cobranca_dias_lembrete_1', $request->input('regua_lembrete_1_dias', '10'));
        CmsSetting::set('regua_lembrete_1_ativo', ($request->boolean('cobranca_lembrete_1_ativo') || $request->boolean('regua_lembrete_1_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_lembrete_1_dias', $lembrete1, 'financeiro');
        CmsSetting::set('cobranca_dias_lembrete_1', $lembrete1, 'gateways');

        $lembrete2 = $request->input('cobranca_dias_lembrete_2', $request->input('regua_lembrete_2_dias', '5'));
        CmsSetting::set('regua_lembrete_2_ativo', ($request->boolean('cobranca_lembrete_2_ativo') || $request->boolean('regua_lembrete_2_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_lembrete_2_dias', $lembrete2, 'financeiro');
        CmsSetting::set('cobranca_dias_lembrete_2', $lembrete2, 'gateways');

        $lembrete3 = $request->input('cobranca_dias_lembrete_3', $request->input('regua_lembrete_3_dias', '1'));
        CmsSetting::set('regua_lembrete_3_ativo', ($request->boolean('cobranca_lembrete_3_ativo') || $request->boolean('regua_lembrete_3_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_lembrete_3_dias', $lembrete3, 'financeiro');
        CmsSetting::set('cobranca_dias_lembrete_3', $lembrete3, 'gateways');

        CmsSetting::set('regua_vencimento_hoje_ativo', ($request->boolean('cobranca_vencimento_hoje_ativo') || $request->boolean('regua_vencimento_hoje_ativo', true)) ? '1' : '0', 'financeiro');

        $atraso1 = $request->input('cobranca_dias_atraso_1', $request->input('regua_atraso_1_dias', '1'));
        CmsSetting::set('regua_atraso_1_ativo', ($request->boolean('cobranca_atraso_1_ativo') || $request->boolean('regua_atraso_1_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_atraso_1_dias', $atraso1, 'financeiro');
        CmsSetting::set('cobranca_dias_atraso_1', $atraso1, 'gateways');

        $atraso2 = $request->input('cobranca_dias_atraso_2', $request->input('regua_atraso_2_dias', '5'));
        CmsSetting::set('regua_atraso_2_ativo', ($request->boolean('cobranca_atraso_2_ativo') || $request->boolean('regua_atraso_2_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_atraso_2_dias', $atraso2, 'financeiro');
        CmsSetting::set('cobranca_dias_atraso_2', $atraso2, 'gateways');

        $atraso3 = $request->input('cobranca_dias_atraso_3', $request->input('regua_atraso_3_dias', '10'));
        CmsSetting::set('regua_atraso_3_ativo', ($request->boolean('cobranca_atraso_3_ativo') || $request->boolean('regua_atraso_3_ativo', true)) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_atraso_3_dias', $atraso3, 'financeiro');
        CmsSetting::set('cobranca_dias_atraso_3', $atraso3, 'gateways');

        $multa = $request->input('cobranca_multa_percentual', $request->input('regua_multa_percentual', '2.0'));
        CmsSetting::set('regua_multa_percentual', $multa, 'financeiro');
        CmsSetting::set('cobranca_multa_percentual', $multa, 'gateways');

        $juros = $request->input('cobranca_juros_mensal_percentual', $request->input('regua_juros_mensal_percentual', '1.0'));
        CmsSetting::set('regua_juros_mensal_percentual', $juros, 'financeiro');
        CmsSetting::set('cobranca_juros_mensal_percentual', $juros, 'gateways');

        if ($request->filled('cobranca_bloquear_rastreador_apos_dias')) {
            CmsSetting::set('cobranca_bloquear_rastreador_apos_dias', $request->input('cobranca_bloquear_rastreador_apos_dias'), 'gateways');
        }

        CmsSetting::set('regua_juros_diario_percentual', $request->input('regua_juros_diario_percentual', round(((float) $juros) / 30, 4) ?: '0.033'), 'financeiro');
        CmsSetting::set('regua_dias_tolerancia', $request->input('regua_dias_tolerancia', '0'), 'financeiro');
        CmsSetting::set('regua_enviar_email', $request->boolean('regua_enviar_email', true) ? '1' : '0', 'financeiro');
        CmsSetting::set('regua_enviar_whatsapp', $request->boolean('regua_enviar_whatsapp') ? '1' : '0', 'financeiro');

        return response()->json([
            'success' => true,
            'message' => 'Configurações de Pagamento, Carnês e Régua de Cobrança salvas com sucesso!',
        ]);
    }

    /**
     * Executa a régua de cobrança imediatamente sob demanda via AJAX
     */
    public function executarRegua(Request $request)
    {
        $resultado = ReguaCobrancaService::processarFila();

        return response()->json([
            'success' => $resultado['sucesso'] ?? true,
            'message' => $resultado['mensagem'] ?? 'Régua de cobrança processada com sucesso.',
            'detalhes' => [
                'geradas' => $resultado['mensalidades_geradas'] ?? 0,
                'fila' => [
                    'lembrete_1' => $resultado['lembrete_1'] ?? 0,
                    'lembrete_2' => $resultado['lembrete_2'] ?? 0,
                    'lembrete_3' => $resultado['lembrete_3'] ?? 0,
                    'vencimento_hoje' => $resultado['vencimento_hoje'] ?? 0,
                    'atraso_1' => $resultado['atraso_1'] ?? 0,
                    'atraso_2' => $resultado['atraso_2'] ?? 0,
                    'atraso_3' => $resultado['atraso_3'] ?? 0,
                    'total_processados' => $resultado['total_processados'] ?? 0,
                ],
            ],
            'metricas' => $resultado['metricas'] ?? [],
        ]);
    }

    /**
     * Salva as alterações de um template de e-mail específico
     */
    public function salvarTemplateEmail(Request $request)
    {
        $request->validate([
            'template_chave' => 'required|string',
            'assunto' => 'required|string|max:255',
            'corpo' => 'required|string',
        ]);

        $chave = $request->input('template_chave');
        CmsSetting::set("email_assunto_{$chave}", $request->input('assunto'), 'emails');
        CmsSetting::set("email_corpo_{$chave}", $request->input('corpo'), 'emails');

        return response()->json([
            'success' => true,
            'message' => 'Template de e-mail atualizado com sucesso!',
        ]);
    }

    /**
     * Cria um novo template de e-mail personalizado com suporte a preview em tempo real
     */
    public function criarEmailTemplate(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'chave' => 'nullable|string|max:50',
            'descricao' => 'nullable|string|max:255',
            'assunto' => 'required|string|max:200',
            'corpo' => 'required|string',
        ]);

        $nome = $request->input('nome');
        $chave = $request->input('chave') ?: Str::slug($nome, '_');
        $descricao = $request->input('descricao') ?: 'Template de e-mail personalizado';
        $assunto = $request->input('assunto');
        $corpo = $request->input('corpo');

        EmailTemplateService::salvarTemplateCustomizado($chave, $nome, $descricao, $assunto, $corpo);

        return response()->json([
            'success' => true,
            'message' => "Template '{$nome}' criado com sucesso!",
            'chave' => $chave,
        ]);
    }

    /**
     * Exclui um template customizado
     */
    public function excluirEmailTemplate(string $chave)
    {
        $removido = EmailTemplateService::excluirTemplateCustomizado($chave);

        if ($removido) {
            return response()->json([
                'success' => true,
                'message' => 'Template personalizado removido com sucesso!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Templates nativos do sistema não podem ser removidos.',
        ], 422);
    }

    /**
     * Retorna a pré-visualização completa em tempo real no layout responsivo oficial
     */
    public function previewEmail(Request $request)
    {
        $chave = $request->input('template_chave', 'fatura_gerada');
        $assunto = $request->input('assunto');
        $corpo = $request->input('corpo');

        $html = EmailTemplateService::renderPreview($chave, $corpo, $assunto);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Dispara um e-mail de teste para validar a formatação do template
     */
    public function enviarEmailTeste(Request $request)
    {
        $request->validate([
            'template_chave' => 'required|string',
            'email_destinatario' => 'required|email',
        ]);

        $chave = $request->input('template_chave');
        $email = $request->input('email_destinatario');
        $corpo = $request->input('corpo');
        $assunto = $request->input('assunto');

        $resultado = EmailTemplateService::enviarTeste($email, $chave, $corpo, $assunto);

        return response()->json($resultado);
    }

    /**
     * Emite um Carnê de Pagamento parcelado para um cliente existente
     */
    public function gerarCarne(Request $request)
    {
        if (! $request->filled('data_primeiro_vencimento') && $request->filled('primeiro_vencimento')) {
            $request->merge(['data_primeiro_vencimento' => $request->input('primeiro_vencimento')]);
        } elseif (! $request->filled('data_primeiro_vencimento')) {
            $request->merge(['data_primeiro_vencimento' => now()->addDays(5)->format('Y-m-d')]);
        }

        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'plano_id' => 'nullable|exists:planos,id',
            'descricao' => 'required|string|max:150',
            'valor_parcela' => 'required',
            'total_parcelas' => 'required|integer|min:2|max:24',
            'data_primeiro_vencimento' => 'required|date',
            'tipo_carne' => 'nullable|in:proprio,efi',
        ]);

        $cliente = User::findOrFail($request->input('cliente_id'));
        $planoId = $request->input('plano_id');
        $descricao = $request->input('descricao');
        $numParcelas = (int) $request->input('total_parcelas');
        $primeiroVenc = Carbon::parse($request->input('data_primeiro_vencimento'));
        $tipoCarne = $request->input('tipo_carne', 'proprio');

        // Normalização do valor (aceita tanto formato BR '89,90' / '1.250,50' quanto float '89.90')
        $valorInput = trim((string) $request->input('valor_parcela'));
        $valorInput = str_ireplace(['R$', ' '], '', $valorInput);
        if (str_contains($valorInput, ',') && str_contains($valorInput, '.')) {
            $valorInput = str_replace('.', '', $valorInput);
            $valorInput = str_replace(',', '.', $valorInput);
        } elseif (str_contains($valorInput, ',')) {
            $valorInput = str_replace(',', '.', $valorInput);
        }
        $valorParcela = (float) $valorInput;

        if ($valorParcela <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'O valor de cada parcela deve ser maior que zero.',
            ], 422);
        }

        $plano = $planoId ? Plano::find($planoId) : null;

        $resultadoCarne = EfiPaymentService::criarCarne(
            cliente: $cliente,
            valorParcelaBase: $valorParcela,
            totalParcelas: $numParcelas,
            primeiroVencimento: $primeiroVenc,
            descricao: $descricao,
            plano: $plano,
            tipoCarne: $tipoCarne
        );

        $tipoRotulo = $resultadoCarne['tipo_carne_rotulo'];
        $msg = "{$tipoRotulo} ({$resultadoCarne['codigo_carne']}) com {$numParcelas} parcelas de {$resultadoCarne['valor_parcela_formatado']} gerado com sucesso para {$cliente->name}!";
        if ($resultadoCarne['repasse_ativo'] && $resultadoCarne['taxa_repassada_por_parcela'] > 0) {
            $msg .= ' (Taxa bancária de R$ '.number_format($resultadoCarne['taxa_repassada_por_parcela'], 2, ',', '.').' repassada por parcela).';
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'tipo_carne' => $resultadoCarne['tipo_carne'],
            'tipo_carne_rotulo' => $tipoRotulo,
            'codigo_carne' => $resultadoCarne['codigo_carne'],
            'total_parcelas' => $numParcelas,
            'valor_parcela' => $resultadoCarne['valor_parcela_formatado'],
            'pdf_url' => $resultadoCarne['pdf_url'],
            'pdf_efi_url' => $resultadoCarne['pdf_efi_url'],
        ]);
    }

    /**
     * Executa tarefas agendadas (Cron) sob demanda via AJAX com feedback
     */
    public function executarCron(Request $request)
    {
        $tarefa = $request->input('tarefa');
        $output = '';

        try {
            switch ($tarefa) {
                case 'verificar_vencidas':
                    Artisan::call('faturas:verificar-vencidas');
                    $output = Artisan::output();
                    CmsSetting::set('cron_last_run_verificar_vencidas', now()->format('d/m/Y H:i:s'), 'cron');
                    $msg = 'Verificação de faturas vencidas executada com sucesso!';
                    break;

                case 'enviar_lembretes':
                    Artisan::call('faturas:enviar-lembretes');
                    $output = Artisan::output();
                    CmsSetting::set('cron_last_run_enviar_lembretes', now()->format('d/m/Y H:i:s'), 'cron');
                    $msg = 'Disparo preventivo de lembretes concluído!';
                    break;

                case 'sincronizar_pagamentos':
                    Artisan::call('gateways:sincronizar-pagamentos');
                    $output = Artisan::output();
                    CmsSetting::set('cron_last_run_sincronizar_pagamentos', now()->format('d/m/Y H:i:s'), 'cron');
                    $msg = 'Sincronização com Mercado Pago e Efí PRO finalizada!';
                    break;

                case 'limpar_cache':
                    Artisan::call('cache:clear');
                    Artisan::call('view:clear');
                    $output = 'Cache da aplicação e views compiladas limpos com sucesso.';
                    CmsSetting::set('cron_last_run_limpar_cache', now()->format('d/m/Y H:i:s'), 'cron');
                    $msg = 'Otimização e limpeza de cache executadas!';
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Rotina não reconhecida.',
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'output' => trim($output),
                'data_hora' => now()->format('d/m/Y H:i:s'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao executar tarefa: '.$e->getMessage(),
            ], 500);
        }
    }
}
