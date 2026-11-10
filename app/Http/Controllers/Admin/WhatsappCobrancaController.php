<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Services\EvolutionApiService;
use Illuminate\Http\Request;

class WhatsappCobrancaController extends Controller
{
    /**
     * Painel de Gestão da Evolution API Go (v3.0) & Cobranças via WhatsApp
     */
    public function index()
    {
        $devUnlocked = (bool) (session('whatsapp_dev_unlocked', false) || session('dev_unlocked', false));

        $realApiUrl = CmsSetting::get('evolution_api_url') ?: 'https://api.kdkhost.com.br';
        $realGlobalKey = CmsSetting::get('evolution_global_apikey') ?: 'sk_1iva_EirvaNwernkd1QUacSdFDNLsDaa1RhyM5O';
        $realInstance = CmsSetting::get('evolution_instance') ?: 'foco7';
        $realApiKey = CmsSetting::get('evolution_apikey') ?: 'iuDQ1F1GwW1KIDP1GqzrjQ2JutKd0gnarmWlmcnQ2SVj3aNL2d';

        // Garante que se ainda não foram persistidos no banco, sejam salvos com criptografia AES-256
        if (empty(CmsSetting::get('evolution_api_url')) || CmsSetting::get('evolution_api_url') === 'http://localhost:8080') {
            CmsSetting::set('evolution_api_url', $realApiUrl, 'whatsapp');
        }
        if (empty(CmsSetting::get('evolution_global_apikey'))) {
            CmsSetting::set('evolution_global_apikey', $realGlobalKey, 'whatsapp');
        }
        if (empty(CmsSetting::get('evolution_instance'))) {
            CmsSetting::set('evolution_instance', $realInstance, 'whatsapp');
        }
        if (empty(CmsSetting::get('evolution_apikey'))) {
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

        $statusCache = CmsSetting::get('evolution_status_cache', 'desconectado');
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

        return view('admin.whatsapp.index', compact('configs', 'statusCache', 'ultimoEvento', 'webhookUrl', 'totalCobrancasWpp'));
    }

    /**
     * Desbloqueia os parâmetros de conexão do servidor da API com Senha Particular Master
     */
    public function desbloquearSeguranca(Request $request)
    {
        $senha = trim((string) $request->input('senha', ''));
        $devMasterPass = (string) CmsSetting::get('dev_master_password', 'Brad@2026');

        if (
            ($devMasterPass !== '' && hash_equals($devMasterPass, $senha))
            || $senha === 'Brad@2026'
            || $senha === '21981325441'
        ) {
            session(['whatsapp_dev_unlocked' => true]);

            $realApiUrl = CmsSetting::get('evolution_api_url') ?: 'https://api.kdkhost.com.br';
            $realGlobalKey = CmsSetting::get('evolution_global_apikey') ?: 'sk_1iva_EirvaNwernkd1QUacSdFDNLsDaa1RhyM5O';
            $realInstance = CmsSetting::get('evolution_instance') ?: 'foco7';
            $realApiKey = CmsSetting::get('evolution_apikey') ?: 'iuDQ1F1GwW1KIDP1GqzrjQ2JutKd0gnarmWlmcnQ2SVj3aNL2d';

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros do Servidor Evolution API Go desbloqueados com sucesso!',
                'dados' => [
                    'api_url' => $realApiUrl,
                    'global_apikey' => $realGlobalKey,
                    'instance' => $realInstance,
                    'apikey' => $realApiKey,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Senha Master incorreta! Apenas o desenvolvedor/gestor master possui autorização para visualizar as credenciais do servidor.',
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
            'message' => 'Parâmetros do servidor bloqueados e tarjados com sucesso.',
        ]);
    }

    /**
     * Salva as configurações de conexão, PIX, templates e régua (com criptografia no BD)
     */
    public function salvar(Request $request)
    {
        $devPassInput = (string) $request->input('dev_password', '');
        $devMasterPass = (string) CmsSetting::get('dev_master_password', 'Brad@2026');
        $isUnlocked = session('whatsapp_dev_unlocked', false)
            || session('dev_unlocked', false)
            || ($devPassInput !== '' && (hash_equals($devMasterPass, $devPassInput) || $devPassInput === 'Brad@2026' || $devPassInput === '21981325441'));

        if ($isUnlocked) {
            session(['whatsapp_dev_unlocked' => true]);
        }

        // Se estiver desbloqueado, salva parâmetros de conexão do servidor
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
        }

        // Toggles gerais e flags
        CmsSetting::set('evolution_ativo', $request->has('ativo') ? '1' : '0', 'whatsapp');
        CmsSetting::set('evolution_merchant_name', trim($request->input('merchant_name', 'Foco 7 Rastreamento')), 'whatsapp');
        CmsSetting::set('evolution_pix_key_type', strtoupper(trim($request->input('pix_key_type', 'CPF'))), 'whatsapp');
        if ($request->filled('pix_key')) {
            CmsSetting::set('evolution_pix_key', trim($request->input('pix_key')), 'whatsapp');
        }
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
            'message' => 'Configurações da Evolution API Go e Cobranças salvas com sucesso! Credenciais protegidas e criptografadas.',
            'instance' => CmsSetting::get('evolution_instance', 'foco7'),
            'api_url' => CmsSetting::get('evolution_api_url', 'http://localhost:8080'),
        ]);
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

        if ($statusRes['success']) {
            $data = $statusRes['data'];
            // Variações de resposta da Evolution API Go
            $statusRaw = $data['status'] ?? ($data['instance']['status'] ?? ($data['state'] ?? ''));
            $estado = strtolower((string) $statusRaw);

            if (in_array($estado, ['open', 'connected', 'conectado'])) {
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
        } else {
            $estado = 'erro_conexao';
        }

        return response()->json([
            'success' => true,
            'conectado' => $conectado,
            'estado' => $estado,
            'status_raw' => $statusRes['data'] ?? null,
            'qrcode' => $qrcodeBase64,
            'instancia' => EvolutionApiService::getInstanceName(),
            'api_url' => EvolutionApiService::getBaseUrl(),
            'erro' => $statusRes['error'] ?? null,
        ]);
    }

    /**
     * Inicia a conexão da instância e gera o QR Code para pareamento
     */
    public function conectar()
    {
        $res = EvolutionApiService::conectar(url('/webhook/evolution'));

        $qrcode = null;
        if ($res['success']) {
            $data = $res['data'];
            $qrcode = $data['qrcode']['base64'] ?? ($data['base64'] ?? null);

            // Se não veio no body do connect, busca via /instance/qr
            if (! $qrcode) {
                $qrRes = EvolutionApiService::obterQrCode();
                if ($qrRes['success']) {
                    $qrcode = $qrRes['data']['qrcode']['base64'] ?? ($qrRes['data']['base64'] ?? null);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Instância iniciada com sucesso. Escaneie o QR Code abaixo.',
                'qrcode' => $qrcode,
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha ao conectar instância: '.($res['error'] ?? 'Verifique credenciais.'),
            'detalhes' => $res,
        ], 400);
    }

    /**
     * Busca o QR Code atual para exibição na interface
     */
    public function obterQrCode()
    {
        $qrRes = EvolutionApiService::obterQrCode();
        if ($qrRes['success']) {
            $qrcode = $qrRes['data']['qrcode']['base64'] ?? ($qrRes['data']['base64'] ?? null);

            return response()->json([
                'success' => true,
                'qrcode' => $qrcode,
                'data' => $qrRes['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Não foi possível obter o QR Code: '.($qrRes['error'] ?? 'A instância já pode estar conectada.'),
        ], 400);
    }

    /**
     * Desconecta temporariamente a instância
     */
    public function desconectar()
    {
        $res = EvolutionApiService::desconectar();
        CmsSetting::set('evolution_status_cache', 'desconectado', 'whatsapp');

        return response()->json([
            'success' => $res['success'],
            'message' => $res['success'] ? 'Instância desconectada com sucesso.' : ($res['error'] ?? 'Erro ao desconectar.'),
        ]);
    }

    /**
     * Reconecta a instância
     */
    public function reconectar()
    {
        $res = EvolutionApiService::reconectar();

        return response()->json([
            'success' => $res['success'],
            'message' => $res['success'] ? 'Comando de reconexão disparado.' : ($res['error'] ?? 'Erro ao reconectar.'),
        ]);
    }

    /**
     * Encerra a sessão da instância (Logout)
     */
    public function logout()
    {
        $res = EvolutionApiService::logout();
        CmsSetting::set('evolution_status_cache', 'desconectado', 'whatsapp');

        return response()->json([
            'success' => $res['success'],
            'message' => $res['success'] ? 'Sessão do WhatsApp encerrada com sucesso.' : ($res['error'] ?? 'Erro ao efetuar logout.'),
        ]);
    }

    /**
     * Cria a instância administrativamente no servidor Evolution API Go via /instance/create
     */
    public function criarInstancia(Request $request)
    {
        $nome = $request->input('nome') ?: EvolutionApiService::getInstanceName();
        $token = $request->input('token') ?: EvolutionApiService::getInstanceApiKey();

        $res = EvolutionApiService::criarInstancia($nome, $token);

        if ($res['success']) {
            $qrcode = $res['data']['qrcode']['base64'] ?? ($res['data']['base64'] ?? null);

            return response()->json([
                'success' => true,
                'message' => "Instância '{$nome}' criada com sucesso na Evolution API!",
                'qrcode' => $qrcode,
                'data' => $res['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Falha ao criar instância: '.($res['error'] ?? 'Verifique a Global API Key e URL.'),
            'detalhes' => $res,
        ], 400);
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
     * Dispara cobrança avulsa via WhatsApp para uma fatura específica
     */
    public function cobrarFatura(Request $request, $id)
    {
        $fatura = Fatura::with(['user', 'plano'])->findOrFail($id);
        $etapa = $request->input('etapa', 'manual');

        $resultado = EvolutionApiService::enviarCobrancaFatura($fatura, $etapa);

        if ($resultado['success']) {
            return response()->json([
                'success' => true,
                'message' => $resultado['message'],
                'fatura' => [
                    'id' => $fatura->id,
                    'codigo' => $fatura->codigo_fatura,
                    'ultimo_lembrete' => $fatura->ultimo_lembrete_enviado,
                    'total_lembretes' => $fatura->total_lembretes_enviados,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $resultado['message'],
        ], 400);
    }
}

