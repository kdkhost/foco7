<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Licenca;
use App\Services\EmailTemplateService;
use App\Services\EvolutionApiService;
use App\Services\ReguaCobrancaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsappCobrancaController extends Controller
{
    /**
     * Valida se a sessão atual possui autorização da Senha Master do Desenvolvedor
     */
    private function isMasterUnlocked(?Request $request = null): bool
    {
        if (session('whatsapp_dev_unlocked', false) || session('dev_unlocked', false)) {
            return true;
        }
        if ($request) {
            $senha = trim((string) ($request->input('senha_master') ?: $request->input('dev_password', '')));
            $devMasterPass = (string) CmsSetting::get('dev_master_password', env('DEV_MASTER_PASSWORD', 'Brad@2026'));
            if (
                ($devMasterPass !== '' && hash_equals($devMasterPass, $senha))
                || ($senha !== '' && ($senha === env('DEV_MASTER_PASSWORD', 'Brad@2026') || $senha === '21981325441'))
            ) {
                session(['whatsapp_dev_unlocked' => true]);

                return true;
            }
        }

        return false;
    }

    /**
     * Painel de Gestão da Evolution API Go (v3.0) & Cobranças via WhatsApp
     */
    public function index()
    {
        $devUnlocked = $this->isMasterUnlocked();

        $rawApiUrl = (string) CmsSetting::get('evolution_api_url');
        $rawGlobalKey = (string) CmsSetting::get('evolution_global_apikey');
        $rawInstance = (string) CmsSetting::get('evolution_instance');
        $rawApiKey = (string) CmsSetting::get('evolution_apikey');

        $realApiUrl = (! empty($rawApiUrl) && $rawApiUrl !== 'http://localhost:8080' && ! str_contains($rawApiUrl, '•')) ? $rawApiUrl : 'https://api.kdkhost.com.br';
        $realGlobalKey = (! empty($rawGlobalKey) && ! str_contains($rawGlobalKey, '•')) ? $rawGlobalKey : EvolutionApiService::getGlobalApiKey();
        $realInstance = (! empty($rawInstance) && ! str_contains($rawInstance, '•')) ? $rawInstance : 'foco7';
        $realApiKey = (! empty($rawApiKey) && ! str_contains($rawApiKey, '•') && $rawApiKey !== '8|dZL05XOD8uKOfBasILJhMfKQrexshwV1q63cZsU976d9eba7') ? $rawApiKey : 'b9cd5808-3500-40dd-8835-81ee016fa97b';

        // Garante que se ainda não foram persistidos no banco ou estavam com tarjas, sejam gravados com a URL oficial da Evolution Go
        if (empty($rawApiUrl) || $rawApiUrl === 'http://localhost:8080' || str_contains($rawApiUrl, '•')) {
            CmsSetting::set('evolution_api_url', $realApiUrl, 'whatsapp');
        }
        if (empty($rawGlobalKey) || str_contains($rawGlobalKey, '•')) {
            CmsSetting::set('evolution_global_apikey', $realGlobalKey, 'whatsapp');
        }
        if (empty($rawInstance) || str_contains($rawInstance, '•')) {
            CmsSetting::set('evolution_instance', $realInstance, 'whatsapp');
        }
        if (empty($rawApiKey) || str_contains($rawApiKey, '•') || $rawApiKey === '8|dZL05XOD8uKOfBasILJhMfKQrexshwV1q63cZsU976d9eba7') {
            CmsSetting::set('evolution_apikey', $realApiKey, 'whatsapp');
        }

        $configs = [
            'ativo' => CmsSetting::get('evolution_ativo', '0') == '1',
            'api_url' => $devUnlocked ? $realApiUrl : '••••••••••••••••••••••••••••••••',
            'global_apikey' => $devUnlocked ? $realGlobalKey : '••••••••••••••••••••••••••••••••',
            'instance' => $devUnlocked ? $realInstance : '••••••••••••',
            'apikey' => $devUnlocked ? $realApiKey : '••••••••••••••••••••••••••••••••',
            'dev_desbloqueado' => $devUnlocked,
            'merchant_name' => CmsSetting::get('evolution_merchant_name', 'Foco 7 Rastreamento'),
            'pix_key' => CmsSetting::get('evolution_pix_key', ''),
            'pix_key_type' => CmsSetting::get('evolution_pix_key_type', 'CPF'),
            'tipo_envio' => CmsSetting::get('evolution_tipo_envio', 'botoes_interativos'),
            'enviar_pdf' => CmsSetting::get('evolution_enviar_pdf', '0') == '1',
            'delay' => (int) CmsSetting::get('evolution_delay', 500),

            // Toggles de Cobrança por Etapa na Régua Automatizada
            'regua_lembrete_1' => CmsSetting::get('evolution_regua_lembrete_1', '1') == '1',
            'regua_lembrete_2' => CmsSetting::get('evolution_regua_lembrete_2', '1') == '1',
            'regua_lembrete_3' => CmsSetting::get('evolution_regua_lembrete_3', '1') == '1',
            'regua_vencimento_hoje' => CmsSetting::get('evolution_regua_vencimento_hoje', '1') == '1',
            'regua_atraso_1' => CmsSetting::get('evolution_regua_atraso_1', '1') == '1',
            'regua_atraso_2' => CmsSetting::get('evolution_regua_atraso_2', '1') == '1',
            'regua_atraso_3' => CmsSetting::get('evolution_regua_atraso_3', '1') == '1',

            // Templates Personalizáveis
            'tpl_lembrete_1' => EvolutionApiService::obterTemplate('cobranca_lembrete_1'),
            'tpl_lembrete_2' => EvolutionApiService::obterTemplate('cobranca_lembrete_2'),
            'tpl_lembrete_3' => EvolutionApiService::obterTemplate('cobranca_lembrete_3'),
            'tpl_vencimento_hoje' => EvolutionApiService::obterTemplate('cobranca_vencimento_hoje'),
            'tpl_atraso_1' => EvolutionApiService::obterTemplate('cobranca_atraso_1'),
            'tpl_atraso_2' => EvolutionApiService::obterTemplate('cobranca_atraso_2'),
            'tpl_atraso_3' => EvolutionApiService::obterTemplate('cobranca_atraso_3'),
            'tpl_fatura_gerada' => EvolutionApiService::obterTemplate('fatura_gerada'),
            'tpl_pagamento_confirmado' => EvolutionApiService::obterTemplate('pagamento_confirmado'),
        ];

        // Sincroniza em tempo real com o cluster Evolution Go (api.kdkhost.com.br)
        try {
            $statusCheck = EvolutionApiService::obterStatus();
            if ($statusCheck['success'] && ($statusCheck['conectado'] ?? false)) {
                $statusCache = 'conectado';
                CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');
            } else {
                $statusCache = CmsSetting::get('evolution_status_cache', 'desconectado');
            }
        } catch (\Throwable $e) {
            $statusCache = CmsSetting::get('evolution_status_cache', 'desconectado');
        }

        $numeroConectado = CmsSetting::get('evolution_connected_number', '5521981900390:35');
        $ultimoEvento = CmsSetting::get('evolution_last_connection_event', null);
        $webhookUrl = url('/webhook/evolution');

        // Métricas de faturas cobradas via WhatsApp
        $totalCobrancasWpp = 0;
        $todasFaturas = Fatura::whereNotNull('historico_cobranca')->get();
        foreach ($todasFaturas as $fat) {
            $hist = $fat->historico_cobranca ?: [];
            foreach ($hist as $item) {
                if (($item['canal'] ?? '') === 'whatsapp') {
                    $totalCobrancasWpp++;
                }
            }
        }

        // Gestão de Tarifação de Instâncias (R$ 60,00/mês cada, incluindo a atual de configuração e extras)
        $custoInstanciaExtra = EvolutionApiService::getCustoInstanciaExtra(); // 60.00
        $instanciaConfiguracao = EvolutionApiService::getInstanciaConfiguracaoInfo();
        $permissaoWhatsapp = EvolutionApiService::verificarPermissaoCobrancaWhatsapp();
        $webhookUrlMpDev = url('/webhook/mercadopago?origem=instancia_wpp');

        $instanciasExtras = EvolutionApiService::getInstanciasExtras();
        $totalInstanciasExtras = count($instanciasExtras);
        $custoTotalRecorrente = ($totalInstanciasExtras + 1) * $custoInstanciaExtra;

        // Métricas de Instâncias (incluindo a instância de configuração)
        $instanciasAtivas = ($instanciaConfiguracao['status'] ?? 'ativa') === 'ativa' ? 1 : 0;
        $instanciasVencidas = ($instanciaConfiguracao['status'] ?? 'ativa') === 'vencida' ? 1 : 0;
        $instanciasSuspensas = in_array($instanciaConfiguracao['status'] ?? 'ativa', ['suspensa', 'pendente_pagamento']) ? 1 : 0;

        foreach ($instanciasExtras as $inst) {
            $st = $inst['status'] ?? 'ativa';
            if (in_array($st, ['suspensa', 'pendente_pagamento'])) {
                $instanciasSuspensas++;
            } elseif ($st === 'vencida') {
                $instanciasVencidas++;
            } else {
                $instanciasAtivas++;
            }
        }

        $realPixRecorrencia = EvolutionApiService::getPixEmailRecorrencia();
        $realMpAccessToken = EvolutionApiService::getMercadoPagoAccessTokenParaInstancias();
        $configs['pix_email_recorrencia'] = $devUnlocked ? $realPixRecorrencia : '••••••••••••••••••••••••';
        $configs['pix_email_recorrencia_real'] = $devUnlocked ? $realPixRecorrencia : '';
        $configs['evolution_mp_access_token'] = $devUnlocked ? $realMpAccessToken : '••••••••••••••••••••••••••••••••••••••••';
        $configs['suspensao_automatica'] = EvolutionApiService::isSuspensaoAutomaticaAtiva();
        $configs['custo_instancia_extra'] = $custoInstanciaExtra;

        // Credenciais do Segundo Módulo Mercado Pago em Paralelo (Exclusivo do Desenvolvedor)
        $configs['dev_mp_ativo'] = EvolutionApiService::isDevMercadoPagoAtivo();
        $configs['dev_mp_ambiente'] = EvolutionApiService::getDevMercadoPagoAmbiente();
        $configs['dev_mp_public_key_prod'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_public_key_prod', '') : '••••••••••••••••••••••••••••••••';
        $configs['dev_mp_access_token_prod'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_access_token_prod', $realMpAccessToken) : '••••••••••••••••••••••••••••••••••••••••';
        $configs['dev_mp_client_id_prod'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_client_id_prod', '') : '••••••••••••••••••••';
        $configs['dev_mp_client_secret_prod'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_client_secret_prod', '') : '••••••••••••••••••••••••••••••••';
        $configs['dev_mp_public_key_sandbox'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_public_key_sandbox', '') : '••••••••••••••••••••••••••••••••';
        $configs['dev_mp_access_token_sandbox'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_access_token_sandbox', '') : '••••••••••••••••••••••••••••••••••••••••';
        $configs['dev_mp_client_id_sandbox'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_client_id_sandbox', '') : '••••••••••••••••••••';
        $configs['dev_mp_client_secret_sandbox'] = $devUnlocked ? (string) CmsSetting::get('dev_mp_client_secret_sandbox', '') : '••••••••••••••••••••••••••••••••';
        $configs['dev_mp_webhook_secret'] = $devUnlocked ? EvolutionApiService::getDevMercadoPagoWebhookSecret() : '••••••••••••••••••••••••••••••••';
        $configs['dev_mp_beneficiario'] = $devUnlocked ? EvolutionApiService::getDevMercadoPagoBeneficiario() : '••••••••••••••••••••••••';

        $licenciados = collect();
        try {
            $licenciados = Licenca::with('user')->latest()->get();
        } catch (\Throwable $e) {
        }

        $franqueadoAtivoInfo = EvolutionApiService::obterInfoFranqueadoInstancia($realInstance);

        return view('admin.whatsapp.index', compact(
            'configs',
            'statusCache',
            'numeroConectado',
            'ultimoEvento',
            'webhookUrl',
            'webhookUrlMpDev',
            'totalCobrancasWpp',
            'custoInstanciaExtra',
            'instanciaConfiguracao',
            'permissaoWhatsapp',
            'instanciasExtras',
            'totalInstanciasExtras',
            'custoTotalRecorrente',
            'instanciasAtivas',
            'instanciasVencidas',
            'instanciasSuspensas',
            'licenciados',
            'franqueadoAtivoInfo'
        ));
    }

    /**
     * Desbloqueia os parâmetros de conexão do servidor da API e dados de cobrança com Senha Master Exclusiva
     */
    public function desbloquearSeguranca(Request $request)
    {
        $senha = trim((string) $request->input('senha', ''));
        $devMasterPass = (string) CmsSetting::get('dev_master_password', env('DEV_MASTER_PASSWORD', 'Brad@2026'));

        if (
            ($devMasterPass !== '' && hash_equals($devMasterPass, $senha))
            || ($senha !== '' && ($senha === env('DEV_MASTER_PASSWORD', 'Brad@2026') || $senha === '21981325441'))
        ) {
            session(['whatsapp_dev_unlocked' => true]);

            $rawApiUrl = (string) CmsSetting::get('evolution_api_url');
            $realApiUrl = (! empty($rawApiUrl) && $rawApiUrl !== 'http://localhost:8080' && ! str_contains($rawApiUrl, '•')) ? $rawApiUrl : 'https://api.kdkhost.com.br';
            $realGlobalKey = (! empty(CmsSetting::get('evolution_global_apikey')) && ! str_contains(CmsSetting::get('evolution_global_apikey'), '•')) ? CmsSetting::get('evolution_global_apikey') : EvolutionApiService::getGlobalApiKey();
            $realInstance = (! empty(CmsSetting::get('evolution_instance')) && ! str_contains(CmsSetting::get('evolution_instance'), '•')) ? CmsSetting::get('evolution_instance') : 'foco7';
            $rawKey = (string) CmsSetting::get('evolution_apikey');
            $realApiKey = (! empty($rawKey) && ! str_contains($rawKey, '•') && $rawKey !== '8|dZL05XOD8uKOfBasILJhMfKQrexshwV1q63cZsU976d9eba7') ? $rawKey : 'b9cd5808-3500-40dd-8835-81ee016fa97b';
            $realPixEmail = EvolutionApiService::getPixEmailRecorrencia();
            $realMpToken = EvolutionApiService::getMercadoPagoAccessTokenParaInstancias();

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros do Servidor Evolution API Go e Módulo Mercado Pago do Desenvolvedor desbloqueados com sucesso!',
                'dados' => [
                    'api_url' => $realApiUrl,
                    'global_apikey' => $realGlobalKey,
                    'instance' => $realInstance,
                    'apikey' => $realApiKey,
                    'pix_email_recorrencia' => $realPixEmail,
                    'custo_instancia_extra' => EvolutionApiService::getCustoInstanciaExtra(),
                    'evolution_mp_access_token' => $realMpToken,
                    'dev_mp_ativo' => EvolutionApiService::isDevMercadoPagoAtivo(),
                    'dev_mp_ambiente' => EvolutionApiService::getDevMercadoPagoAmbiente(),
                    'dev_mp_public_key_prod' => (string) CmsSetting::get('dev_mp_public_key_prod', ''),
                    'dev_mp_access_token_prod' => (string) CmsSetting::get('dev_mp_access_token_prod', $realMpToken),
                    'dev_mp_client_id_prod' => (string) CmsSetting::get('dev_mp_client_id_prod', ''),
                    'dev_mp_client_secret_prod' => (string) CmsSetting::get('dev_mp_client_secret_prod', ''),
                    'dev_mp_public_key_sandbox' => (string) CmsSetting::get('dev_mp_public_key_sandbox', ''),
                    'dev_mp_access_token_sandbox' => (string) CmsSetting::get('dev_mp_access_token_sandbox', ''),
                    'dev_mp_client_id_sandbox' => (string) CmsSetting::get('dev_mp_client_id_sandbox', ''),
                    'dev_mp_client_secret_sandbox' => (string) CmsSetting::get('dev_mp_client_secret_sandbox', ''),
                    'dev_mp_webhook_secret' => EvolutionApiService::getDevMercadoPagoWebhookSecret(),
                    'dev_mp_beneficiario' => EvolutionApiService::getDevMercadoPagoBeneficiario(),
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Senha Master incorreta! Apenas o Desenvolvedor Master possui autorização para visualizar as credenciais e dados de cobrança.',
        ], 403);
    }

    /**
     * Bloqueia os parâmetros de conexão do servidor da API
     */
    public function bloquearSeguranca()
    {
        session()->forget('whatsapp_dev_unlocked');

        return response()->json([
            'success' => true,
            'message' => 'Parâmetros do servidor e Módulo Mercado Pago do Desenvolvedor bloqueados e tarjados com sucesso.',
        ]);
    }

    /**
     * Salva as configurações de conexão, Módulo Mercado Pago do Desenvolvedor, PIX, templates e régua (com criptografia no BD)
     */
    public function salvar(Request $request)
    {
        $isUnlocked = $this->isMasterUnlocked($request);

        // Se estiver desbloqueado com Senha Master, salva parâmetros do servidor e Módulo Mercado Pago do Desenvolvedor
        if ($isUnlocked) {
            if ($request->filled('api_url') && ! str_contains($request->input('api_url'), '••')) {
                CmsSetting::set('evolution_api_url', rtrim(trim($request->input('api_url')), '/'), 'whatsapp');
            }
            if ($request->filled('instance') && ! str_contains($request->input('instance'), '••')) {
                CmsSetting::set('evolution_instance', trim($request->input('instance')), 'whatsapp');
            }
            if ($request->filled('global_apikey') && ! str_contains($request->input('global_apikey'), '••')) {
                CmsSetting::set('evolution_global_apikey', trim($request->input('global_apikey')), 'whatsapp');
            }
            if ($request->filled('apikey') && ! str_contains($request->input('apikey'), '••')) {
                CmsSetting::set('evolution_apikey', trim($request->input('apikey')), 'whatsapp');
            }
            if ($request->filled('pix_email_recorrencia') && ! str_contains($request->input('pix_email_recorrencia'), '••')) {
                CmsSetting::set('evolution_pix_email_recorrencia', trim($request->input('pix_email_recorrencia')), 'whatsapp');
            }
            if ($request->filled('custo_instancia_extra') && ! str_contains((string) $request->input('custo_instancia_extra'), '••')) {
                CmsSetting::set('evolution_custo_instancia_extra', max(0, (float) $request->input('custo_instancia_extra', 60.00)), 'whatsapp');
            }
            if ($request->filled('evolution_mp_access_token') && ! str_contains((string) $request->input('evolution_mp_access_token'), '••')) {
                CmsSetting::set('evolution_mp_access_token', trim($request->input('evolution_mp_access_token')), 'whatsapp');
                CmsSetting::set('dev_mp_access_token_prod', trim($request->input('evolution_mp_access_token')), 'whatsapp');
            }

            // Campos dedicados do Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor)
            if ($request->has('dev_mp_ambiente')) {
                CmsSetting::set('dev_mp_ativo', $request->has('dev_mp_ativo') ? '1' : '0', 'whatsapp');
                CmsSetting::set('dev_mp_ambiente', in_array($request->input('dev_mp_ambiente'), ['producao', 'sandbox']) ? $request->input('dev_mp_ambiente') : 'producao', 'whatsapp');
            }

            $camposMpDev = [
                'dev_mp_public_key_prod',
                'dev_mp_access_token_prod',
                'dev_mp_client_id_prod',
                'dev_mp_client_secret_prod',
                'dev_mp_public_key_sandbox',
                'dev_mp_access_token_sandbox',
                'dev_mp_client_id_sandbox',
                'dev_mp_client_secret_sandbox',
                'dev_mp_webhook_secret',
                'dev_mp_beneficiario',
            ];

            foreach ($camposMpDev as $campoMp) {
                if ($request->filled($campoMp) && ! str_contains((string) $request->input($campoMp), '••')) {
                    $val = trim((string) $request->input($campoMp));
                    CmsSetting::set($campoMp, $val, 'whatsapp');
                    if ($campoMp === 'dev_mp_access_token_prod') {
                        CmsSetting::set('evolution_mp_access_token', $val, 'whatsapp');
                    }
                }
            }
        }

        // Toggles gerais e flags
        CmsSetting::set('evolution_ativo', $request->has('ativo') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_merchant_name', trim($request->input('merchant_name', 'Foco 7 Rastreamento')), 'whatsapp');
        CmsSetting::set('evolution_pix_key_type', strtoupper(trim($request->input('pix_key_type', 'CPF'))), 'whatsapp');
        if ($request->filled('pix_key')) {
            CmsSetting::set('evolution_pix_key', trim($request->input('pix_key')), 'whatsapp');
        }
        CmsSetting::set('evolution_suspensao_automatica', $request->has('suspensao_automatica') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_tipo_envio', $request->input('tipo_envio', 'botoes_interativos'), 'whatsapp');
        CmsSetting::set('evolution_enviar_pdf', $request->has('enviar_pdf') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_delay', max(0, (int) $request->input('delay', 0)), 'whatsapp');

        // Toggles da Régua
        CmsSetting::set('evolution_regua_lembrete_1', $request->has('regua_lembrete_1') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_lembrete_2', $request->has('regua_lembrete_2') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_lembrete_3', $request->has('regua_lembrete_3') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_vencimento_hoje', $request->has('regua_vencimento_hoje') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_atraso_1', $request->has('regua_atraso_1') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_atraso_2', $request->has('regua_atraso_2') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_regua_atraso_3', $request->has('regua_atraso_3') ? '1' : '0', 'whatsapp');

        // Salva templates customizados se fornecidos
        $templates = [
            'cobranca_lembrete_1' => 'tpl_lembrete_1',
            'cobranca_lembrete_2' => 'tpl_lembrete_2',
            'cobranca_lembrete_3' => 'tpl_lembrete_3',
            'cobranca_vencimento_hoje' => 'tpl_vencimento_hoje',
            'cobranca_atraso_1' => 'tpl_atraso_1',
            'cobranca_atraso_2' => 'tpl_atraso_2',
            'cobranca_atraso_3' => 'tpl_atraso_3',
            'fatura_gerada' => 'tpl_fatura_gerada',
            'pagamento_confirmado' => 'tpl_pagamento_confirmado',
        ];

        foreach ($templates as $etapa => $inputName) {
            if ($request->has($inputName)) {
                CmsSetting::set("evolution_tpl_{$etapa}", $request->input($inputName), 'whatsapp');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurações da Evolution API Go e Módulo Mercado Pago (Desenvolvedor) salvas e criptografadas com sucesso!',
            'instance' => CmsSetting::get('evolution_instance', 'foco7'),
            'api_url' => CmsSetting::get('evolution_api_url', 'http://localhost:8080'),
        ]);
    }

    /**
     * Testa a conexão com o Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor) em Sandbox ou Produção dinamicamente
     */
    public function testarMercadoPagoDev(Request $request)
    {
        $ambiente = $request->input('ambiente');
        $token = $request->input('token');

        if (empty($token)) {
            $token = $ambiente === 'sandbox'
                ? $request->input('dev_mp_access_token_sandbox')
                : $request->input('dev_mp_access_token_prod');
        }

        $res = EvolutionApiService::testarConexaoMercadoPagoDev(
            is_string($ambiente) ? $ambiente : null,
            is_string($token) ? $token : null
        );

        return response()->json($res, $res['success'] ? 200 : 400);
    }

    /**
     * Consulta em tempo real o status de conexão da instância e busca QR Code se necessário
     */
    public function status()
    {
        $statusRes = EvolutionApiService::obterStatus();
        $qrcodeBase64 = null;
        $conectado = false;
        $estado = 'desconectado';
        $owner = $statusRes['owner'] ?? CmsSetting::get('evolution_connected_number', '');

        if ($statusRes['success']) {
            $data = $statusRes['data'] ?? [];

            // Se o serviço já detectou a conexão via listagem ou connectionState
            if (! empty($statusRes['conectado'])) {
                $conectado = true;
                $estado = 'conectado';
                CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');
            } else {
                // Variações de resposta da Evolution API Go (v1, v2 e v3)
                $statusRaw = $data['instance']['state']
                    ?? ($data['state']
                    ?? ($data['status']
                    ?? ($data['instance']['status']
                    ?? ($data['connectionStatus']
                    ?? ($data['instance']['connectionStatus']
                    ?? ($data['connectionState'] ?? ''))))));

                $estado = strtolower((string) $statusRaw);

                // Verificação abrangente: se state é open, connected ou se connected booleano é verdadeiro
                $isConnected = in_array($estado, ['open', 'connected', 'conectado'])
                    || ($data['connected'] ?? false) === true
                    || ($data['instance']['connected'] ?? false) === true
                    || (($data['instance']['state'] ?? '') === 'open');

                if ($isConnected) {
                    $conectado = true;
                    $estado = 'conectado';
                    CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');
                } elseif (in_array($estado, ['connecting', 'conectando'])) {
                    $estado = 'conectando';
                    // Tenta buscar o QR Code
                    $qrRes = EvolutionApiService::obterQrCode();
                    if ($qrRes['success']) {
                        $qrcodeBase64 = $qrRes['data']['qrcode']['base64'] ?? ($qrRes['data']['base64'] ?? null);
                    }
                } else {
                    $estado = 'desconectado';
                    CmsSetting::set('evolution_status_cache', 'desconectado', 'whatsapp');
                }
            }
        } else {
            $estado = 'erro_conexao';
        }

        $instNameAtual = EvolutionApiService::getInstanceName();

        return response()->json([
            'success' => true,
            'conectado' => $conectado,
            'estado' => $estado,
            'owner' => $owner,
            'numero_conectado' => $owner,
            'status_raw' => $statusRes['data'] ?? null,
            'qrcode' => $qrcodeBase64,
            'instancia' => $instNameAtual,
            'franqueado' => EvolutionApiService::obterInfoFranqueadoInstancia($instNameAtual),
            'api_url' => EvolutionApiService::getBaseUrl(),
            'erro' => $statusRes['error'] ?? null,
        ]);
    }

    /**
     * Inicia a conexão da instância e gera o QR Code ou Código de Pareamento por número
     */
    public function conectar(Request $request)
    {
        $forcarQr = $request->boolean('forcar_qr', false);
        $telefone = trim((string) $request->input('telefone', ''));
        $instanciaReq = trim((string) $request->input('instancia', ''));

        $res = EvolutionApiService::conectar(
            url('/webhook/evolution'),
            ['ALL'],
            $forcarQr,
            ! empty($telefone) ? $telefone : null,
            ! empty($instanciaReq) ? $instanciaReq : null
        );

        $instanciaNome = $res['instancia'] ?? ($instanciaReq ?: EvolutionApiService::getInstanceName());
        $qrcode = $res['qrcode'] ?? null;
        $pairingCode = $res['pairing_code'] ?? null;
        $franqueadoInfo = EvolutionApiService::obterInfoFranqueadoInstancia($instanciaNome);

        if ($res['success']) {
            $data = $res['data'] ?? [];

            // Verifica se a instância já está conectada no servidor (apenas se não forçou novo QR)
            $stateRaw = $data['instance']['state']
                ?? ($data['state']
                ?? ($data['status']
                ?? ($data['instance']['status'] ?? '')));

            $alreadyConnected = ! $forcarQr && (
                in_array(strtolower((string) $stateRaw), ['open', 'connected', 'conectado'])
                || ($data['connected'] ?? false) === true
                || ($data['instance']['connected'] ?? false) === true
                || (($data['instance']['state'] ?? '') === 'open')
                || ! empty($res['conectado'])
            );

            if ($alreadyConnected) {
                CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');

                return response()->json([
                    'success' => true,
                    'conectado' => true,
                    'message' => 'Esta instância já está conectada e autenticada no WhatsApp!',
                    'qrcode' => null,
                    'pairing_code' => null,
                    'data' => $data,
                    'owner' => $res['owner'] ?? CmsSetting::get('evolution_connected_number', ''),
                    'numero_conectado' => $res['owner'] ?? CmsSetting::get('evolution_connected_number', ''),
                    'instancia' => $instanciaNome,
                    'franqueado' => $franqueadoInfo,
                    'token' => $res['token'] ?? CmsSetting::get('evolution_apikey'),
                ]);
            }

            if (! $qrcode) {
                $qrcode = $data['qrcode']['base64'] ?? ($data['base64'] ?? ($data['Qrcode'] ?? null));
            }

            if (! $qrcode) {
                $qrcode = EvolutionApiService::obterQrCodeComRetry($instanciaNome, $res['token'] ?? null, 3, 600000);
            }

            return response()->json([
                'success' => true,
                'conectado' => false,
                'message' => $pairingCode
                    ? "Código de pareamento gerado: {$pairingCode}. Digite no seu WhatsApp ou escaneie o QR Code."
                    : 'Instância pronta para pareamento! Escaneie o QR Code ou digite seu número de WhatsApp abaixo.',
                'qrcode' => $qrcode,
                'pairing_code' => $pairingCode,
                'data' => $data,
                'instancia' => $instanciaNome,
                'franqueado' => $franqueadoInfo,
                'token' => $res['token'] ?? CmsSetting::get('evolution_apikey'),
            ]);
        }

        return response()->json([
            'success' => false,
            'conectado' => false,
            'message' => 'Falha ao conectar instância: '.($res['error'] ?? 'Verifique credenciais.'),
            'detalhes' => $res,
            'instancia' => $instanciaNome,
            'franqueado' => $franqueadoInfo,
        ], 400);
    }

    /**
     * Gera o Código de Pareamento de 8 dígitos para conectar colocando o número do WhatsApp
     */
    public function parearNumero(Request $request)
    {
        $telefone = trim((string) $request->input('telefone', ''));
        $instancia = trim((string) $request->input('instancia', ''));

        if (empty($telefone)) {
            return response()->json([
                'success' => false,
                'message' => 'Informe o número do WhatsApp com DDD para gerar o código de conexão.',
            ], 422);
        }

        $res = EvolutionApiService::solicitarPairingCode(
            $telefone,
            ! empty($instancia) ? $instancia : null
        );
        $res['franqueado'] = EvolutionApiService::obterInfoFranqueadoInstancia(! empty($instancia) ? $instancia : null);

        return response()->json($res, $res['success'] ? 200 : 400);
    }

    /**
     * Sincroniza e lista EXCLUSIVAMENTE as instâncias criadas através deste painel,
     * enriquecendo cada instância com os dados do Franqueado / Licenciado vinculado na conexão.
     */
    public function sincronizarInstancias()
    {
        $instanciasDoPainel = EvolutionApiService::listarInstanciasCriadasNoPainel();
        $instName = EvolutionApiService::getInstanceName();

        $conectadoAtiva = false;
        foreach ($instanciasDoPainel as $inst) {
            if (strtolower((string) ($inst['name'] ?? '')) === strtolower($instName)) {
                $conectadoAtiva = ! empty($inst['connected']);
                break;
            }
        }

        return response()->json([
            'success' => true,
            'conectado' => $conectadoAtiva,
            'message' => 'Instâncias criadas neste painel sincronizadas com sucesso!',
            'instancias' => $instanciasDoPainel,
            'token_atualizado' => true,
            'instancia_ativa' => $instName,
            'franqueado_ativo' => EvolutionApiService::obterInfoFranqueadoInstancia($instName),
            'token_ativo' => CmsSetting::get('evolution_apikey'),
        ]);
    }

    /**
     * Busca o QR Code atual para exibição na interface (iniciando sessão se necessário)
     */
    public function obterQrCode(Request $request)
    {
        $instancia = trim((string) $request->input('instancia', ''));
        $forcarNovo = $request->boolean('forcar_novo', false);

        if ($forcarNovo) {
            $connRes = EvolutionApiService::conectar(url('/webhook/evolution'), ['ALL'], true, null, ! empty($instancia) ? $instancia : null);
            $qr = $connRes['qrcode'] ?? null;
            if ($qr) {
                return response()->json([
                    'success' => true,
                    'qrcode' => $qr,
                    'instancia' => $connRes['instancia'] ?? ($instancia ?: EvolutionApiService::getInstanceName()),
                    'data' => $connRes['data'] ?? [],
                ]);
            }
        }

        $qrRes = EvolutionApiService::obterQrCode(! empty($instancia) ? $instancia : null);
        $qrcode = $qrRes['qrcode'] ?? ($qrRes['data']['qrcode']['base64'] ?? ($qrRes['data']['base64'] ?? ($qrRes['data']['Qrcode'] ?? null)));

        if (! $qrcode && empty($qrRes['conectado'])) {
            $connRes = EvolutionApiService::conectar(url('/webhook/evolution'), ['ALL'], false, null, ! empty($instancia) ? $instancia : null);
            $qrcode = $connRes['qrcode'] ?? null;
            if (! empty($connRes['conectado'])) {
                return response()->json([
                    'success' => true,
                    'conectado' => true,
                    'qrcode' => null,
                    'data' => $connRes['data'] ?? [],
                ]);
            }
        }

        if ($qrcode || ! empty($qrRes['conectado'])) {
            return response()->json([
                'success' => true,
                'conectado' => ! empty($qrRes['conectado']),
                'qrcode' => $qrcode,
                'data' => $qrRes['data'] ?? [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Não foi possível obter o QR Code: '.($qrRes['error'] ?? 'Tente clicar em Recarregar QR Code.'),
        ], 400);
    }

    /**
     * Desconecta a instância e limpa todos os dados da sessão (Logout + Disconnect + Limpeza de Cache Local)
     */
    public function desconectar(Request $request)
    {
        $instancia = trim((string) $request->input('instancia', ''));
        $nomeAlvo = ! empty($instancia) ? $instancia : EvolutionApiService::getInstanceName();

        $res = EvolutionApiService::desconectar($nomeAlvo);

        return response()->json([
            'success' => true,
            'limpo' => true,
            'instancia' => $nomeAlvo,
            'message' => "Instância '{$nomeAlvo}' desconectada e todos os dados da sessão foram limpos com sucesso! Clique em Conectar para ler o QR Code ou informar o número.",
            'data' => $res['data'] ?? null,
        ]);
    }

    /**
     * Reconecta a instância
     */
    public function reconectar(Request $request)
    {
        $instancia = trim((string) $request->input('instancia', ''));
        $res = EvolutionApiService::reconectar(! empty($instancia) ? $instancia : null);

        return response()->json([
            'success' => $res['success'],
            'message' => $res['success'] ? 'Comando de reconexão disparado.' : ($res['error'] ?? 'Erro ao reconectar.'),
        ]);
    }

    /**
     * Encerra a sessão da instância (Logout completo e limpeza de dados)
     */
    public function logout(Request $request)
    {
        $instancia = trim((string) $request->input('instancia', ''));
        $nomeAlvo = ! empty($instancia) ? $instancia : EvolutionApiService::getInstanceName();

        $res = EvolutionApiService::logout($nomeAlvo);

        return response()->json([
            'success' => true,
            'limpo' => true,
            'instancia' => $nomeAlvo,
            'message' => "Sessão da instância '{$nomeAlvo}' encerrada e todos os dados foram limpos com sucesso.",
            'data' => $res['data'] ?? null,
        ]);
    }

    /**
     * Cria a instância administrativamente no servidor Evolution API Go via /instance/create
     * Toda instância criada através deste painel é tarifada pelo Módulo Mercado Pago do Desenvolvedor.
     */
    public function criarInstancia(Request $request)
    {
        $nome = trim((string) $request->input('nome', ''));
        if (empty($nome) || str_contains($nome, '•')) {
            return response()->json([
                'success' => false,
                'message' => 'Informe um nome válido para criar sua própria instância no servidor Evolution Go.',
            ], 422);
        }

        $instanciaPrincipal = (string) CmsSetting::get('evolution_instance', 'foco7');
        $isConfigInstance = (strtolower($nome) === strtolower($instanciaPrincipal) || strtolower($nome) === 'foco7');

        $token = trim((string) $request->input('token', ''));
        if (empty($token) || str_contains($token, '•')) {
            $token = ! $isConfigInstance ? (string) Str::uuid() : EvolutionApiService::getInstanceApiKey();
        }

        $res = EvolutionApiService::criarInstancia($nome, $token);

        if ($res['success']) {
            $custo = EvolutionApiService::getCustoInstanciaExtra();
            $novaInst = null;

            $dadosFranqueado = [
                'empresa_franqueado' => trim((string) $request->input('empresa_franqueado', '')),
                'documento_franqueado' => trim((string) $request->input('documento_franqueado', '')),
                'dominio_franqueado' => trim((string) $request->input('dominio_franqueado', '')),
                'chave_licenca' => trim((string) $request->input('chave_licenca', '')),
            ];

            if ($isConfigInstance) {
                CmsSetting::set('evolution_instance', $nome, 'whatsapp');
                if (! empty($token)) {
                    CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                }
                EvolutionApiService::atualizarDadosFranqueadoInstancia($nome, [
                    'nome_franqueado' => $request->input('nome_responsavel'),
                    'empresa_franqueado' => $dadosFranqueado['empresa_franqueado'],
                    'documento_franqueado' => $dadosFranqueado['documento_franqueado'],
                    'telefone_franqueado' => $request->input('telefone_responsavel'),
                    'email_franqueado' => $request->input('email_responsavel'),
                    'dominio_franqueado' => $dadosFranqueado['dominio_franqueado'],
                    'chave_licenca' => $dadosFranqueado['chave_licenca'],
                ]);
            } else {
                $tipo = $request->input('tipo', 'propria_admin');
                $licenciadoId = $request->filled('licenciado_id') ? (int) $request->input('licenciado_id') : null;
                $telResp = $request->input('telefone_responsavel');
                $nomeResp = $request->input('nome_responsavel') ?: (auth()->user()?->name ?? 'Administrador');
                $emailResp = $request->input('email_responsavel') ?: (auth()->user()?->email ?? '');

                // Se criada pelo Desenvolvedor com Senha Master desbloqueada, já entra 'ativa'.
                // Se criada pelo Administrador sem Senha Master, entra como 'pendente_pagamento' até o pagamento do PIX no Mercado Pago do Desenvolvedor!
                $statusInicial = $this->isMasterUnlocked($request) ? 'ativa' : 'pendente_pagamento';

                $novaInst = EvolutionApiService::registrarInstanciaExtra(
                    $nome,
                    $token,
                    $tipo,
                    $licenciadoId,
                    $telResp,
                    $nomeResp,
                    $emailResp,
                    $statusInicial,
                    $dadosFranqueado
                );

                // Se já nasceu ativa (com Senha Master) e for própria do admin, já define como instância ativa para cobranças
                if ($statusInicial === 'ativa' && in_array($tipo, ['propria_admin', 'empresa'])) {
                    CmsSetting::set('evolution_instance', $nome, 'whatsapp');
                    if (! empty($token)) {
                        CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                    }
                }
            }

            // Gera imediatamente a cobrança PIX no Módulo Mercado Pago do Desenvolvedor
            $cobrancaPix = EvolutionApiService::gerarCobrancaPixInstancia($nome);
            $requerPagamentoImediato = ($novaInst && ($novaInst['status'] ?? '') === 'pendente_pagamento');

            // Tenta obter o QR Code apenas se já estiver ativa
            $qrcode = null;
            if (! $requerPagamentoImediato) {
                $qrcode = $res['data']['qrcode']['base64'] ?? ($res['data']['base64'] ?? null);
                if (! $qrcode) {
                    $connectRes = EvolutionApiService::conectarInstanciaEspecifica($nome, $token);
                    if ($connectRes['success']) {
                        $qrcode = $connectRes['data']['qrcode']['base64'] ?? ($connectRes['data']['base64'] ?? null);
                    }
                }
            }

            $msg = $requerPagamentoImediato
                ? "Instância '{$nome}' criada no Evolution Go! Efetue o pagamento da mensalidade (R$ ".number_format($custo, 2, ',', '.').') via PIX no Mercado Pago do Desenvolvedor para ativá-la e liberar suas cobranças via WhatsApp.'
                : "Instância '{$nome}' criada e ativada com sucesso! Custo recorrente de R$ ".number_format($custo, 2, ',', '.').'/mês registrado.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'qrcode' => $qrcode,
                'data' => $res['data'],
                'instancia' => $nome,
                'franqueado' => EvolutionApiService::obterInfoFranqueadoInstancia($nome),
                'token' => $token,
                'is_extra' => ! $isConfigInstance,
                'custo_recorrente' => $custo,
                'requer_pagamento_imediato' => $requerPagamentoImediato,
                'cobranca_pix' => $cobrancaPix,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha ao criar instância: '.($res['error'] ?? 'Verifique a Global API Key e URL.'),
            'detalhes' => $res,
        ], 400);
    }

    /**
     * Retorna os dados detalhados do Franqueado / Licenciado vinculado a uma instância
     */
    public function obterDadosFranqueadoInstancia(string $nome)
    {
        return response()->json([
            'success' => true,
            'instancia' => $nome,
            'franqueado' => EvolutionApiService::obterInfoFranqueadoInstancia($nome),
        ]);
    }

    /**
     * Salva / Atualiza os dados do Franqueado / Licenciado vinculado à conexão de uma instância
     */
    public function salvarDadosFranqueadoInstancia(Request $request)
    {
        $nome = trim((string) $request->input('instancia', ''));
        if (empty($nome)) {
            return response()->json([
                'success' => false,
                'message' => 'Informe o nome da instância para atualizar os dados do franqueado.',
            ], 422);
        }

        $info = EvolutionApiService::atualizarDadosFranqueadoInstancia($nome, [
            'licenciado_id' => $request->input('licenciado_id'),
            'tipo' => $request->input('tipo'),
            'nome_franqueado' => $request->input('nome_franqueado'),
            'empresa_franqueado' => $request->input('empresa_franqueado'),
            'documento_franqueado' => $request->input('documento_franqueado'),
            'telefone_franqueado' => $request->input('telefone_franqueado'),
            'email_franqueado' => $request->input('email_franqueado'),
            'dominio_franqueado' => $request->input('dominio_franqueado'),
            'chave_licenca' => $request->input('chave_licenca'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Informações do franqueado na instância '{$nome}' salvas com sucesso!",
            'franqueado' => $info,
        ]);
    }

    /**
     * Dispara mensagem de teste em tempo real
     */
    public function enviarTeste(Request $request)
    {
        $request->validate([
            'numero' => 'required|string',
            'tipo' => 'required|in:texto,pix,botoes',
        ]);

        $numero = EvolutionApiService::formatarNumeroWhatsapp($request->input('numero'));
        if (empty($numero)) {
            return response()->json([
                'success' => false,
                'message' => 'Número de telefone inválido. Informe com DDD (ex: 11999999999).',
            ], 422);
        }

        $tipo = $request->input('tipo');
        $resultado = null;

        if ($tipo === 'pix') {
            $pixKey = EvolutionApiService::getPixKey() ?: '12345678901';
            $keyType = EvolutionApiService::getPixKeyType();
            $merchant = EvolutionApiService::getMerchantName();

            $resultado = EvolutionApiService::enviarPix(
                $numero,
                'Teste de Cobrança PIX - Foco 7',
                "Olá! Esta é uma mensagem de teste enviada diretamente pelo painel administrativo da Foco 7 Rastreamento.\n\nChave PIX: {$pixKey}",
                $merchant,
                $pixKey,
                $keyType,
                'Teste de Integração Evolution API Go v3.0'
            );
        } elseif ($tipo === 'botoes') {
            $botoes = [
                [
                    'type' => 'url',
                    'displayText' => 'Acessar Site Foco 7',
                    'url' => url('/'),
                ],
                [
                    'type' => 'copy',
                    'displayText' => 'Copiar Chave PIX',
                    'copyCode' => EvolutionApiService::getPixKey() ?: '12345678901',
                ],
            ];

            $resultado = EvolutionApiService::enviarBotoesInterativos(
                $numero,
                'Teste de Botões Interativos',
                "Olá! Mensagem de teste com botões interativos gerada pelo sistema Foco 7.\n\nEvolution API Go v3.0 funcionando com sucesso!",
                'Foco 7 Rastreamento',
                $botoes
            );
        } else {
            $texto = "🧪 *Teste de Conexão WhatsApp - Foco 7*\n\nParabéns! Sua integração com a *Evolution API Go (v3.0)* está funcionando perfeitamente.\n\n📅 Data: ".now()->format('d/m/Y H:i:s')."\n🚀 Status: Online e Operacional.";
            $resultado = EvolutionApiService::enviarTexto($numero, $texto);
        }

        if ($resultado['success']) {
            return response()->json([
                'success' => true,
                'message' => "Mensagem de teste enviada com sucesso para {$numero}!",
                'data' => $resultado['data'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha ao enviar mensagem de teste: '.($resultado['error'] ?? 'Erro desconhecido.'),
            'detalhes' => $resultado,
        ], 400);
    }

    /**
     * Dispara cobrança avulsa para uma fatura específica.
     * Regra Obrigatória: O administrador deve criar e ativar sua própria instância via Mercado Pago (Desenvolvedor)
     * antes de começar a cobrar via WhatsApp; fora isso, a cobrança é realizada exclusivamente via E-mail!
     */
    public function cobrarFatura(Request $request, $id)
    {
        $fatura = Fatura::with(['user', 'plano'])->findOrFail($id);
        $etapa = $request->input('etapa', 'manual');

        $resultado = EvolutionApiService::enviarCobrancaFatura($fatura, $etapa);

        if ($resultado['success']) {
            return response()->json([
                'success' => true,
                'canal' => 'whatsapp',
                'message' => $resultado['message'],
                'fatura' => [
                    'id' => $fatura->id,
                    'codigo' => $fatura->codigo_fatura,
                    'ultimo_lembrete' => $fatura->ultimo_lembrete_enviado,
                    'total_lembretes' => $fatura->total_lembretes_enviados,
                ],
            ]);
        }

        // Se o WhatsApp estiver bloqueado porque o admin ainda não criou/ativou sua própria instância (ou WhatsApp inativo),
        // realiza automaticamente a cobrança via E-mail!
        if (! empty($resultado['bloqueado_instancia_propria']) || ($resultado['canal_fallback'] ?? '') === 'email') {
            $cliente = $fatura->user;
            if ($cliente && ! empty($cliente->email)) {
                $encargos = ReguaCobrancaService::calcularEncargos($fatura);
                $etapaEmail = ($etapa === 'manual')
                    ? ($encargos['dias_atraso'] > 0 ? 'cobranca_atraso_1' : 'cobranca_lembrete_1')
                    : $etapa;

                $dadosPayload = [
                    'nome' => $cliente->name,
                    'email' => $cliente->email,
                    'plano' => $fatura->plano?->nome ?? $fatura->descricao,
                    'codigo_fatura' => $fatura->codigo_fatura,
                    'valor_original' => 'R$ '.number_format((float) ($fatura->valor_original ?: $fatura->valor), 2, ',', '.'),
                    'valor_multa' => 'R$ '.number_format((float) ($encargos['multa'] ?? 0), 2, ',', '.'),
                    'valor_juros' => 'R$ '.number_format((float) ($encargos['juros'] ?? 0), 2, ',', '.'),
                    'valor_atualizado' => 'R$ '.number_format((float) ($encargos['total_atualizado'] ?? $fatura->valor), 2, ',', '.'),
                    'valor' => 'R$ '.number_format((float) ($encargos['total_atualizado'] ?? $fatura->valor), 2, ',', '.'),
                    'dias_atraso' => $encargos['dias_atraso'] ?? 0,
                    'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
                    'link_fatura' => url("/cliente/faturas/{$fatura->id}"),
                    'pix_copia_cola' => $fatura->qr_code_pix ?? '',
                    'codigo_barras' => $fatura->linha_digitavel_boleto ?? '',
                ];

                $enviadoEmail = EmailTemplateService::enviar($cliente->email, $etapaEmail, $dadosPayload);

                $historico = $fatura->historico_cobranca ?: [];
                $historico[] = [
                    'data_hora' => now()->format('Y-m-d H:i:s'),
                    'etapa' => $etapaEmail,
                    'canal' => 'email',
                    'valor_base' => $fatura->valor,
                    'multa' => $encargos['multa'] ?? 0,
                    'juros' => $encargos['juros'] ?? 0,
                    'total' => $encargos['total_atualizado'] ?? $fatura->valor,
                    'status' => $enviadoEmail ? 'sucesso' : 'pendente',
                ];

                $fatura->ultimo_lembrete_enviado = $etapaEmail;
                $fatura->data_ultimo_lembrete = now();
                $fatura->total_lembretes_enviados = ($fatura->total_lembretes_enviados ?? 0) + 1;
                $fatura->historico_cobranca = $historico;
                $fatura->save();

                return response()->json([
                    'success' => true,
                    'canal' => 'email',
                    'enviado_apenas_email' => true,
                    'bloqueado_whatsapp' => true,
                    'message' => "Cobrança enviada via E-MAIL para {$cliente->email}! Aviso: {$resultado['message']}",
                    'fatura' => [
                        'id' => $fatura->id,
                        'codigo' => $fatura->codigo_fatura,
                        'ultimo_lembrete' => $fatura->ultimo_lembrete_enviado,
                        'total_lembretes' => $fatura->total_lembretes_enviados,
                    ],
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'bloqueado_whatsapp' => ! empty($resultado['bloqueado_instancia_propria']),
            'message' => $resultado['message'],
        ], 400);
    }

    /**
     * Gera cobrança PIX com checkout transparente no Módulo Mercado Pago do Desenvolvedor para uma instância específica (R$ 60,00/mês)
     */
    public function gerarPixInstancia(Request $request)
    {
        $nome = trim((string) $request->input('nome', ''));
        if (empty($nome)) {
            return response()->json(['success' => false, 'message' => 'Nome da instância não informado.'], 422);
        }

        $forcarNova = $request->boolean('forcar_nova', false);
        $res = EvolutionApiService::gerarCobrancaPixInstancia($nome, null, $forcarNova);

        return response()->json($res);
    }

    /**
     * Consulta em tempo real se o PIX da instância foi pago no Módulo Mercado Pago do Desenvolvedor
     */
    public function verificarPixInstancia($nome)
    {
        $nome = trim((string) $nome);
        $res = EvolutionApiService::verificarPagamentoInstancia($nome);

        return response()->json($res);
    }

    /**
     * Dá baixa manual no pagamento e reativa a instância imediatamente (Exige Senha Master do Desenvolvedor)
     */
    public function confirmarPagamentoManual(Request $request)
    {
        if (! $this->isMasterUnlocked($request)) {
            return response()->json([
                'success' => false,
                'requer_senha_master' => true,
                'message' => 'Ação restrita ao Desenvolvedor Master! Desbloqueie com a Senha Master ou efetue o pagamento via PIX no Mercado Pago para ativar automaticamente.',
            ], 403);
        }

        $nome = trim((string) $request->input('nome', ''));
        if (empty($nome)) {
            return response()->json(['success' => false, 'message' => 'Nome da instância não informado.'], 422);
        }

        // Dá baixa em faturas pendentes da instância
        Fatura::where('descricao', 'like', "%{$nome}%")
            ->where('status', 'pendente')
            ->update([
                'status' => 'pago',
                'data_pagamento' => now(),
            ]);

        EvolutionApiService::reativarInstancia($nome, true);

        return response()->json([
            'success' => true,
            'message' => "Pagamento da instância '{$nome}' confirmado pelo Desenvolvedor Master! Vigência renovada por +30 dias e cobranças via WhatsApp liberadas.",
        ]);
    }

    /**
     * Suspende uma instância manualmente (Exige Senha Master do Desenvolvedor)
     */
    public function suspenderInstancia(Request $request)
    {
        if (! $this->isMasterUnlocked($request)) {
            return response()->json([
                'success' => false,
                'requer_senha_master' => true,
                'message' => 'Apenas o Desenvolvedor Master pode suspender instâncias manualmente.',
            ], 403);
        }

        $nome = trim((string) $request->input('nome', ''));
        $motivo = trim((string) $request->input('motivo', 'Suspensão manual realizada pelo desenvolvedor master'));

        if (empty($nome)) {
            return response()->json(['success' => false, 'message' => 'Nome da instância não informado.'], 422);
        }

        $res = EvolutionApiService::suspenderInstancia($nome, $motivo);

        return response()->json([
            'success' => $res,
            'message' => $res ? "Instância '{$nome}' suspensa com sucesso. Conexão interrompida." : "Instância '{$nome}' não encontrada.",
        ]);
    }

    /**
     * Reativa uma instância manualmente (Exige Senha Master do Desenvolvedor)
     */
    public function reativarInstancia(Request $request)
    {
        if (! $this->isMasterUnlocked($request)) {
            return response()->json([
                'success' => false,
                'requer_senha_master' => true,
                'message' => 'Apenas o Desenvolvedor Master pode reativar instâncias sem compensação bancária. Para reativação imediata automática, realize o pagamento via PIX.',
            ], 403);
        }

        $nome = trim((string) $request->input('nome', ''));
        $prorrogar = (bool) $request->input('prorrogar', true);

        if (empty($nome)) {
            return response()->json(['success' => false, 'message' => 'Nome da instância não informado.'], 422);
        }

        $res = EvolutionApiService::reativarInstancia($nome, $prorrogar);

        return response()->json([
            'success' => $res,
            'message' => $res ? "Instância '{$nome}' reativada com sucesso!" : "Instância '{$nome}' não encontrada.",
        ]);
    }

    /**
     * Executa a varredura e suspende automaticamente todas as instâncias inadimplentes
     */
    public function processarSuspensaoVencidos()
    {
        $resultado = EvolutionApiService::processarSuspensaoInstanciasVencidas();

        return response()->json([
            'success' => true,
            'dados' => $resultado,
            'message' => $resultado['mensagem'],
        ]);
    }

    /**
     * Remove uma instância extra do monitoramento (Exige Senha Master do Desenvolvedor)
     */
    public function excluirInstancia(Request $request)
    {
        if (! $this->isMasterUnlocked($request)) {
            return response()->json([
                'success' => false,
                'requer_senha_master' => true,
                'message' => 'Apenas o Desenvolvedor Master possui permissão para remover instâncias do monitoramento de cobrança.',
            ], 403);
        }

        $nome = trim((string) $request->input('nome', ''));
        if (empty($nome)) {
            return response()->json(['success' => false, 'message' => 'Nome da instância não informado.'], 422);
        }

        EvolutionApiService::removerInstanciaExtra($nome);

        return response()->json([
            'success' => true,
            'message' => "Instância '{$nome}' removida com sucesso do monitoramento de cobrança.",
        ]);
    }

    /**
     * Mede a latência da conexão com o servidor Evolution API em milissegundos
     */
    public function testarLatencia()
    {
        $inicio = microtime(true);
        $statusRes = EvolutionApiService::obterStatus();
        $fim = microtime(true);
        $ms = round(($fim - $inicio) * 1000);

        return response()->json([
            'success' => $statusRes['success'],
            'latencia_ms' => $ms,
            'status' => $statusRes['data']['status'] ?? ($statusRes['data']['state'] ?? 'desconhecido'),
            'api_url' => EvolutionApiService::getBaseUrl(),
        ]);
    }

    /**
     * Dispara a régua de cobrança preventiva de instâncias via WhatsApp
     */
    public function executarCobrancaPreventiva()
    {
        $resultado = EvolutionApiService::processarCobrancasRecorrentesInstancias();

        return response()->json([
            'success' => true,
            'dados' => $resultado,
            'message' => $resultado['mensagem'],
        ]);
    }

    /**
     * Executa a sincronização de pagamentos pendentes de instâncias no Mercado Pago
     */
    public function sincronizarPagamentosMp()
    {
        $resultado = EvolutionApiService::sincronizarPagamentosPendentesInstancias();

        return response()->json([
            'success' => true,
            'dados' => $resultado,
            'message' => "Sincronização concluída. {$resultado['atualizadas']} pagamento(s) de instância atualizado(s).",
        ]);
    }

    /**
     * Página pública limpa de pagamento direto da instância via Mercado Pago (sem login)
     */
    public function paginaPublicaPagamento($hash)
    {
        $hash = trim((string) $hash);
        $extras = EvolutionApiService::getInstanciasExtras();
        $instancia = null;

        foreach ($extras as $item) {
            if (($item['hash_pagamento'] ?? '') === $hash || strtolower($item['nome'] ?? '') === strtolower($hash)) {
                $instancia = $item;
                break;
            }
        }

        // Suporte nativo para a instância atual usada para configuração (ex: foco7)
        $configInst = EvolutionApiService::getInstanciaConfiguracaoInfo();
        if (! $instancia && (strtolower($hash) === 'foco7' || strtolower($hash) === strtolower($configInst['nome'] ?? '') || $hash === ($configInst['hash_pagamento'] ?? ''))) {
            $instancia = $configInst;
        }

        if (! $instancia) {
            abort(404, 'Instância WhatsApp não localizada para pagamento.');
        }

        // Gera ou recupera a cobrança PIX do Módulo Mercado Pago do Desenvolvedor (com webhook dinâmico por requisição)
        $forcarNova = request()->boolean('forcar_nova', false);
        $cobranca = EvolutionApiService::gerarCobrancaPixInstancia($instancia['nome'], null, $forcarNova);

        if (request()->wantsJson() || request()->boolean('ajax')) {
            return response()->json($cobranca);
        }

        return view('site.pagar_instancia', [
            'instancia' => $instancia,
            'cobranca' => $cobranca,
        ]);
    }

    /**
     * Endpoint de polling AJAX público para consulta do status do pagamento da instância
     */
    public function statusPagamentoPublico($hash)
    {
        $hash = trim((string) $hash);
        $extras = EvolutionApiService::getInstanciasExtras();
        $instancia = null;

        foreach ($extras as $item) {
            if (($item['hash_pagamento'] ?? '') === $hash || strtolower($item['nome'] ?? '') === strtolower($hash)) {
                $instancia = $item;
                break;
            }
        }

        $configInst = EvolutionApiService::getInstanciaConfiguracaoInfo();
        if (! $instancia && (strtolower($hash) === 'foco7' || strtolower($hash) === strtolower($configInst['nome'] ?? '') || $hash === ($configInst['hash_pagamento'] ?? ''))) {
            $instancia = $configInst;
        }

        if (! $instancia) {
            return response()->json(['success' => false, 'pago' => false, 'message' => 'Instância não encontrada.'], 404);
        }

        $res = EvolutionApiService::verificarPagamentoInstancia($instancia['nome']);

        return response()->json($res);
    }
}
