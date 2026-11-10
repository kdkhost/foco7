<?php

namespace App\Services;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    /**
     * Retorna a URL base limpa da Evolution API Go (v3.0)
     * Documentação Swagger: https://whatsapp.km.site.nom.br/api/docs
     */
    public static function getBaseUrl(): string
    {
        $url = trim((string) CmsSetting::get('evolution_api_url', ''));
        if (empty($url) || $url === 'http://localhost:8080' || str_contains($url, 'kdkhost') || str_contains($url, '•')) {
            return 'https://whatsapp.km.site.nom.br';
        }

        return rtrim($url, '/');
    }

    /**
     * Custo mensal recorrente de cada instância adicional do WhatsApp (R$ 60,00/mês)
     */
    public static function getCustoInstanciaExtra(): float
    {
        return 60.00;
    }

    /**
     * Retorna a lista de instâncias extras cadastradas com cobrança recorrente (R$ 60,00/mês)
     */
    public static function getInstanciasExtras(): array
    {
        $data = CmsSetting::get('evolution_instancias_extras', []);
        if (is_string($data)) {
            $decoded = json_decode($data, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Registra ou atualiza uma instância com tarifação recorrente de R$ 60,00/mês
     */
    public static function registrarInstanciaExtra(string $nome, ?string $token = null, string $tipo = 'empresa', ?int $licenciadoId = null): array
    {
        $extras = self::getInstanciasExtras();
        $custo = self::getCustoInstanciaExtra();

        $nova = [
            'nome' => $nome,
            'token' => $token,
            'tipo' => $tipo, // 'empresa' ou 'licenciado'
            'licenciado_id' => $licenciadoId,
            'valor_recorrente' => $custo,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'status' => 'ativa',
        ];

        $encontrou = false;
        foreach ($extras as &$item) {
            if (($item['nome'] ?? '') === $nome) {
                $item = array_merge($item, $nova);
                $encontrou = true;
                break;
            }
        }
        if (! $encontrou) {
            $extras[] = $nova;
        }

        CmsSetting::set('evolution_instancias_extras', $extras, 'whatsapp', 'json');

        return $nova;
    }

    /**
     * Remove uma instância extra do registro de cobrança
     */
    public static function removerInstanciaExtra(string $nome): bool
    {
        $extras = self::getInstanciasExtras();
        $filtradas = array_values(array_filter($extras, fn ($i) => ($i['nome'] ?? '') !== $nome));
        CmsSetting::set('evolution_instancias_extras', $filtradas, 'whatsapp', 'json');

        return true;
    }

    /**
     * Obtém o QR Code para uma instância específica (ex: do licenciado)
     */
    public static function obterQrCodeParaInstancia(string $instName, ?string $token = null): array
    {
        // 1. Tenta GET /instance/connect/{instance} (padrão v3 Go no endpoint https://whatsapp.km.site.nom.br/api/docs)
        $res = self::request('GET', "instance/connect/{$instName}", [], false, 15, $token);
        if ($res['success'] && (! empty($res['data']['qrcode']['base64']) || ! empty($res['data']['base64']))) {
            return $res;
        }

        // 2. Tenta GET /instance/qr/{instance}
        return self::request('GET', "instance/qr/{$instName}", [], false, 15, $token);
    }

    /**
     * Obtém o status de conexão de uma instância específica
     */
    public static function obterStatusInstancia(string $instName, ?string $token = null): array
    {
        // 1. Tenta GET /instance/connectionState/{instance} (Swagger /api/docs)
        $res = self::request('GET', "instance/connectionState/{$instName}", [], false, 10, $token);
        if ($res['success']) {
            return $res;
        }

        // 2. Tenta GET /instance/status/{instance}
        return self::request('GET', "instance/status/{$instName}", [], false, 10, $token);
    }

    /**
     * Desconecta uma instância específica
     */
    public static function desconectarInstanciaEspecifica(string $instName, ?string $token = null): array
    {
        $res = self::request('POST', "instance/disconnect/{$instName}", [], false, 10, $token);
        if (! $res['success']) {
            return self::request('POST', 'instance/disconnect', ['instance' => $instName], false, 10, $token);
        }

        return $res;
    }

    /**
     * Retorna a Global API Key (Autenticação Administrativa)
     */
    public static function getGlobalApiKey(): string
    {
        $key = trim((string) CmsSetting::get('evolution_global_apikey', ''));
        if (empty($key) || str_contains($key, '•')) {
            return self::getInstanceApiKey();
        }

        return $key;
    }

    /**
     * Retorna o nome da instância configurada
     */
    public static function getInstanceName(): string
    {
        $name = trim((string) CmsSetting::get('evolution_instance', ''));
        if (empty($name) || str_contains($name, '•')) {
            return 'foco7';
        }

        return $name;
    }

    /**
     * Retorna o Token / API Key específico da instância (suporte nativo a Laravel Sanctum e tokens padrão)
     */
    public static function getInstanceApiKey(): string
    {
        $key = trim((string) CmsSetting::get('evolution_apikey', ''));
        if (empty($key) || str_contains($key, '•')) {
            return '8|dZL05XOD8uKOfBasILJhMfKQrexshwV1q63cZsU976d9eba7';
        }

        return $key;
    }

    /**
     * Verifica se a integração com a Evolution API está ativada no sistema
     */
    public static function isAtivo(): bool
    {
        return CmsSetting::get('evolution_ativo', '0') == '1';
    }

    /**
     * Nome do Comerciante para cobranças PIX
     */
    public static function getMerchantName(): string
    {
        return trim((string) CmsSetting::get('evolution_merchant_name', 'Foco 7 Rastreamento'));
    }

    /**
     * Chave PIX cadastrada para cobrança
     */
    public static function getPixKey(): string
    {
        return trim((string) CmsSetting::get('evolution_pix_key', ''));
    }

    /**
     * Tipo da Chave PIX: CPF | CNPJ | EMAIL | PHONE | EVP
     */
    public static function getPixKeyType(): string
    {
        return strtoupper(trim((string) CmsSetting::get('evolution_pix_key_type', 'CPF')));
    }

    /**
     * Formato de envio da cobrança: botoes_interativos | pix_nativo | review_and_pay | texto
     */
    public static function getTipoEnvio(): string
    {
        return (string) CmsSetting::get('evolution_tipo_envio', 'botoes_interativos');
    }

    /**
     * Monta os headers da requisição HTTP com isolamento estrito entre chamadas globais e de instância
     */
    protected static function getHeaders(bool $isGlobal = false, ?string $overrideKey = null): array
    {
        $key = $overrideKey ?: ($isGlobal ? self::getGlobalApiKey() : self::getInstanceApiKey());

        $headers = [
            'apikey' => $key,
            'Authorization' => 'Bearer '.$key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($isGlobal) {
            // Em chamadas administrativas globais (ex: /instance/create, /instance/all):
            // Envia todos os cabeçalhos reconhecidos pelas variações da Evolution API Go e Node.js
            $headers['Global-Api-Key'] = $key;
            $headers['globalapikey'] = $key;
            $headers['adminkey'] = $key;
            // ATENÇÃO CRÍTICA: NUNCA injetar 'instance' ou 'Instance-Name' em chamadas globais,
            // pois o middleware de roteamento do servidor intercepta e rejeita a chave mestra com 401 not authorized!
        } else {
            // Em chamadas direcionadas a uma instância específica (ex: envio de mensagens, status):
            $instName = self::getInstanceName();
            if (! empty($instName)) {
                $headers['instance'] = $instName;
                $headers['Instance-Name'] = $instName;
            }
        }

        return $headers;
    }

    /**
     * Executa requisição HTTP resiliente à Evolution API Go v3.0 com resolução automática de prefixo /api
     */
    protected static function request(string $method, string $path, array $data = [], bool $isGlobal = false, int $timeout = 25, ?string $overrideKey = null): array
    {
        $baseUrl = self::getBaseUrl();
        if (empty($baseUrl)) {
            return [
                'success' => false,
                'status' => 0,
                'error' => 'URL da Evolution API não está configurada no painel administrativo.',
                'data' => null,
            ];
        }

        $headers = self::getHeaders($isGlobal, $overrideKey);
        $key = $overrideKey ?: ($isGlobal ? self::getGlobalApiKey() : self::getInstanceApiKey());

        $cleanPath = ltrim($path, '/');

        // Candidatos de rota: testa sob /api/ (padrão Swagger /api/docs) e na raiz
        if (str_contains($baseUrl, 'km.site.nom.br') || str_contains($baseUrl, 'whatsapp.')) {
            // No servidor oficial https://whatsapp.km.site.nom.br as rotas da API ficam sob /api/
            $candidatos = ! str_starts_with($cleanPath, 'api/')
                ? ['api/'.$cleanPath, $cleanPath]
                : [$cleanPath, substr($cleanPath, 4)];
        } else {
            $candidatos = ! str_starts_with($cleanPath, 'api/')
                ? [$cleanPath, 'api/'.$cleanPath]
                : [$cleanPath, substr($cleanPath, 4)];
        }

        $lastResult = null;
        foreach ($candidatos as $caminho) {
            $url = $baseUrl.'/'.ltrim($caminho, '/');
            if (! empty($key)) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator.'apikey='.urlencode($key);
            }

            $res = self::executeSingleCall($method, $url, $data, $headers, $timeout);
            if ($res['success']) {
                return $res;
            }

            // Se o erro foi 404 ou 405 ("The POST method is not supported for route..."), testa o candidato alternativo
            $errMsg = strtolower((string) ($res['error'] ?? ''));
            $isRouteMismatch = ($res['status'] === 404 || $res['status'] === 405)
                || str_contains($errMsg, 'not supported for route')
                || str_contains($errMsg, 'route not found')
                || str_contains($errMsg, 'cannot post')
                || str_contains($errMsg, 'cannot get');

            if (! $isRouteMismatch) {
                return $res;
            }

            $lastResult = $res;
        }

        return $lastResult ?: [
            'success' => false,
            'status' => 404,
            'error' => 'Rota não encontrada no servidor Evolution API.',
            'data' => null,
        ];
    }

    /**
     * Executa a chamada HTTP bruta isolada
     */
    protected static function executeSingleCall(string $method, string $fullUrl, array $data, array $headers, int $timeout): array
    {
        try {
            $http = Http::timeout($timeout)->withoutVerifying()->withHeaders($headers);

            $response = match (strtoupper($method)) {
                'GET' => $http->get($fullUrl, $data),
                'POST' => $http->post($fullUrl, $data),
                'PUT' => $http->put($fullUrl, $data),
                'DELETE' => $http->delete($fullUrl, $data),
                default => throw new Exception("Método HTTP {$method} não suportado."),
            };

            $json = $response->json();
            $statusCode = $response->status();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'data' => $json,
                    'error' => null,
                ];
            }

            $errorMsg = $json['message'] ?? ($json['error'] ?? 'Erro retornado pela Evolution API (HTTP '.$statusCode.')');
            if (is_array($errorMsg)) {
                $errorMsg = json_encode($errorMsg);
            }

            return [
                'success' => false,
                'status' => $statusCode,
                'error' => $errorMsg,
                'data' => $json,
            ];
        } catch (\Throwable $e) {
            Log::warning('Evolution API Connection Error ['.$method.' '.$fullUrl.']: '.$e->getMessage());

            return [
                'success' => false,
                'status' => 0,
                'error' => 'Falha de conexão com Evolution API: '.$e->getMessage(),
                'data' => null,
            ];
        }
    }

    // =========================================================================
    // 1. ADMIN OPERATIONS (Autenticação com globalApikey)
    // =========================================================================

    /**
     * Cria uma nova instância na Evolution API Go v3.0
     * Endpoint: POST /instance/create
     */
    public static function criarInstancia(?string $nome = null, ?string $token = null, array $advancedSettings = []): array
    {
        $instance = $nome ?: self::getInstanceName();
        $apiKey = $token ?: self::getInstanceApiKey();

        $defaultAdvanced = [
            'rejectCalls' => false,
            'groupsIgnore' => true,
            'alwaysOnline' => true,
            'readMessages' => true,
            'readStatus' => true,
            'syncFullHistory' => false,
        ];

        $payload = [
            'name' => $instance,
            'instanceName' => $instance,
            'token' => $apiKey,
            'number' => '',
            'qrcode' => true,
            'integration' => 'WHATSAPP-BAILEYS',
            'advancedSettings' => array_merge($defaultAdvanced, $advancedSettings),
            'proxy' => [
                'enabled' => false,
                'host' => '',
                'port' => '',
                'username' => '',
                'password' => '',
            ],
        ];

        $res = self::request('POST', 'instance/create', $payload, true);

        // Se a instância já existir no servidor, trata graciosamente como sucesso
        if (! $res['success']) {
            $err = strtolower($res['error'] ?? '');
            if (str_contains($err, 'already exists') || str_contains($err, 'já existe') || str_contains($err, 'in use')) {
                Log::info("Evolution API: Instância '{$instance}' já existia no servidor.");

                return [
                    'success' => true,
                    'status' => 200,
                    'data' => [
                        'name' => $instance,
                        'instance' => ['name' => $instance],
                        'already_existed' => true,
                    ],
                    'error' => null,
                ];
            }
        }

        return $res;
    }

    /**
     * Busca todas as instâncias existentes no servidor
     * Endpoint: GET /instance/all com fallback para GET /instance/fetchInstances
     */
    public static function listarInstancias(): array
    {
        $res = self::request('GET', 'instance/all', [], true);
        if (! $res['success'] && ($res['status'] === 404 || $res['status'] === 405)) {
            $alt = self::request('GET', 'instance/fetchInstances', [], true);
            if ($alt['success']) {
                return $alt;
            }
        }

        return $res;
    }

    /**
     * Obtém informações detalhadas de uma instância
     * Endpoint: GET /instance/info/{instance} com fallback para GET /instance/fetchInstances?instanceName={instance}
     */
    public static function obterInfoInstancia(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();

        $res = self::request('GET', "instance/info/{$inst}", [], true);
        if (! $res['success'] && ($res['status'] === 404 || $res['status'] === 405)) {
            $alt = self::request('GET', "instance/fetchInstances?instanceName={$inst}", [], true);
            if ($alt['success']) {
                return $alt;
            }
        }

        return $res;
    }

    /**
     * Deleta permanentemente uma instância da Evolution API
     * Endpoint: DELETE /instance/delete/{instance}
     */
    public static function deletarInstancia(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();

        return self::request('DELETE', "instance/delete/{$inst}", [], true);
    }

    /**
     * Força a reconexão da instância com um número específico
     * Endpoint: POST /instance/forcereconnect/{instance}
     */
    public static function forcarReconexao(?string $numero = null, ?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $payload = [
            'number' => $numero ? self::formatarNumeroWhatsapp($numero) : '',
        ];

        return self::request('POST', "instance/forcereconnect/{$inst}", $payload, true);
    }

    // =========================================================================
    // 2. INSTANCE OPERATIONS (Autenticação com apikey / Token da Instância)
    // =========================================================================

    /**
     * Sincroniza e recupera automaticamente o token correto da instância diretamente do servidor Evolution API via Global API Key
     */
    public static function sincronizarTokenInstancia(?string $instanceName = null): ?string
    {
        $instName = $instanceName ?: self::getInstanceName();
        $globalKey = self::getGlobalApiKey();

        if (empty($globalKey) || empty($instName)) {
            return null;
        }

        // 1. Tenta obter info da instância específica via GET /instance/info/{instance}
        $infoRes = self::obterInfoInstancia($instName);
        if ($infoRes['success'] && is_array($infoRes['data'])) {
            $d = $infoRes['data'];
            $token = $d['token'] ?? ($d['instance']['token'] ?? ($d['apikey'] ?? null));
            if (! empty($token)) {
                CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                Log::info("Evolution API: Token sincronizado via /instance/info para '{$instName}'.");

                return $token;
            }
        }

        // 2. Tenta listar todas as instâncias via GET /instance/all
        $allRes = self::listarInstancias();
        if ($allRes['success'] && is_array($allRes['data'])) {
            $list = $allRes['data'];
            $itens = isset($list['instances']) ? $list['instances'] : $list;
            if (is_array($itens)) {
                foreach ($itens as $item) {
                    $nome = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? null));
                    if ($nome === $instName) {
                        $token = $item['token'] ?? ($item['instance']['token'] ?? ($item['apikey'] ?? null));
                        if (! empty($token)) {
                            CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                            Log::info("Evolution API: Token sincronizado via /instance/all para '{$instName}'.");

                            return $token;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Inicia a conexão da instância e configura o webhook se fornecido (com Auto-Recuperação e Criação Inteligente)
     * Endpoint padrão: GET /instance/connect/{instance} ou POST /instance/connect
     */
    public static function conectar(?string $webhookUrl = null, array $subscribe = ['ALL']): array
    {
        $instName = self::getInstanceName();
        $payload = [
            'number' => '',
            'instance' => $instName,
        ];

        $webhook = $webhookUrl ?: url('/webhook/evolution');
        if (! empty($webhook)) {
            $payload['webhookUrl'] = $webhook;
            $payload['subscribe'] = ! empty($subscribe) ? $subscribe : ['MESSAGE', 'SEND_MESSAGE', 'CONNECTION'];
        }

        // 1. Tenta endpoint padrão Evolution API Go / v2: GET /instance/connect/{instance}
        $res = self::request('GET', "instance/connect/{$instName}", [], false, 25);
        if ($res['success']) {
            return $res;
        }

        // 2. Tenta POST /instance/connect/{instance}
        $res2 = self::request('POST', "instance/connect/{$instName}", $payload, false, 25);
        if ($res2['success']) {
            return $res2;
        }

        // 3. Tenta POST /instance/connect com payload
        $res3 = self::request('POST', 'instance/connect', $payload, false, 25);
        if ($res3['success']) {
            return $res3;
        }

        // 4. Se falhou por autorização (401 / "not authorized") ou token incorreto
        $isAuthError = ($res['status'] === 401 || $res2['status'] === 401 || $res3['status'] === 401)
            || str_contains(strtolower($res['error'] ?? ''), 'authorized')
            || str_contains(strtolower($res['error'] ?? ''), 'unauthorized')
            || str_contains(strtolower($res['error'] ?? ''), 'token');

        if ($isAuthError && ! empty(self::getGlobalApiKey())) {
            Log::info("Evolution API: Conexão não autorizada para '{$instName}'. Recuperando com Global API Key...");

            // 4.1 Tenta recuperar o token real da instância no servidor Evolution
            $tokenReal = self::sincronizarTokenInstancia($instName);
            if (! empty($tokenReal)) {
                $retryRes = self::request('GET', "instance/connect/{$instName}", [], false, 25, $tokenReal);
                if ($retryRes['success']) {
                    return $retryRes;
                }
                $retryResPost = self::request('POST', "instance/connect/{$instName}", $payload, false, 25, $tokenReal);
                if ($retryResPost['success']) {
                    return $retryResPost;
                }
            }

            // 4.2 Se a instância não existe no servidor, cria-a via POST /instance/create
            Log::info("Evolution API: Tentando criar/recriar instância '{$instName}' no servidor...");
            $createRes = self::criarInstancia($instName, self::getInstanceApiKey());

            if ($createRes['success']) {
                $createData = $createRes['data'];
                $novoToken = $createData['token'] ?? ($createData['instance']['token'] ?? null);
                if (! empty($novoToken)) {
                    CmsSetting::set('evolution_apikey', $novoToken, 'whatsapp');
                }

                // Se a criação já devolveu o QR Code direto, retorna imediatamente
                if (! empty($createData['qrcode']['base64']) || ! empty($createData['base64'])) {
                    return $createRes;
                }

                // Tenta conectar agora que a instância foi criada
                $retryAfterCreate = self::request('GET', "instance/connect/{$instName}", [], false, 25, $novoToken ?: self::getInstanceApiKey());
                if ($retryAfterCreate['success']) {
                    return $retryAfterCreate;
                }
            }

            // 4.3 Tenta conectar usando a Global API Key diretamente (suportado por várias distribuições Evolution API)
            $globalRes = self::request('GET', "instance/connect/{$instName}", [], true, 25);
            if ($globalRes['success']) {
                return $globalRes;
            }
        }

        return $res;
    }

    /**
     * Retorna o status de conexão da instância (open, connected, connecting, close, etc.)
     * Endpoint: GET /instance/connectionState/{instance} ou GET /instance/status
     */
    public static function obterStatus(): array
    {
        $instName = self::getInstanceName();

        // 1. Tenta GET /instance/connectionState/{instance} (padrão v2 / v3)
        $res = self::request('GET', "instance/connectionState/{$instName}", [], false, 10);
        if ($res['success']) {
            return $res;
        }

        // 2. Tenta GET /instance/status
        $res2 = self::request('GET', 'instance/status', [], false, 10);
        if ($res2['success']) {
            return $res2;
        }

        // 3. Tenta GET /instance/status/{instance}
        $res3 = self::request('GET', "instance/status/{$instName}", [], false, 10);
        if ($res3['success']) {
            return $res3;
        }

        // Se falhou por autenticação, tenta sincronizar token com a chave global
        if ($res['status'] === 401 || str_contains(strtolower($res['error'] ?? ''), 'authorized')) {
            $tokenReal = self::sincronizarTokenInstancia($instName);
            if ($tokenReal) {
                return self::request('GET', "instance/connectionState/{$instName}", [], false, 10, $tokenReal);
            }
        }

        return $res;
    }

    /**
     * Retorna o QR Code atual para autenticação
     * Endpoint: GET /instance/connect/{instance} ou GET /instance/qr
     */
    public static function obterQrCode(): array
    {
        $instName = self::getInstanceName();

        // 1. Tenta GET /instance/connect/{instance}
        $res = self::request('GET', "instance/connect/{$instName}", [], false, 15);
        if ($res['success'] && (! empty($res['data']['qrcode']['base64']) || ! empty($res['data']['base64']))) {
            return $res;
        }

        // 2. Tenta GET /instance/qr
        $res2 = self::request('GET', 'instance/qr', [], false, 15);
        if ($res2['success']) {
            return $res2;
        }

        // 3. Tenta GET /instance/qr/{instance}
        $res3 = self::request('GET', "instance/qr/{$instName}", [], false, 15);
        if ($res3['success']) {
            return $res3;
        }

        return $res;
    }

    /**
     * Desconecta a instância temporariamente
     * Suporta DELETE /instance/logout/{instance}, GET /instance/disconnect/{instance} e fallback
     */
    public static function desconectar(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();

        // 1. Tenta DELETE /instance/logout/{instance}
        $res = self::request('DELETE', "instance/logout/{$inst}", [], false);
        if ($res['success']) {
            return $res;
        }

        // 2. Tenta GET /instance/disconnect/{instance} (método suportado pelo servidor Evolution Go)
        $res2 = self::request('GET', "instance/disconnect/{$inst}", [], false);
        if ($res2['success']) {
            return $res2;
        }

        // 3. Tenta GET /instance/disconnect
        $res3 = self::request('GET', 'instance/disconnect', [], false);
        if ($res3['success']) {
            return $res3;
        }

        // 4. Fallback POST /instance/logout/{instance}
        return self::request('POST', "instance/logout/{$inst}", [], false);
    }

    /**
     * Reconecta a instância ao WhatsApp
     * Endpoint: POST /instance/reconnect/{instance} com fallback para POST /instance/reconnect
     */
    public static function reconectar(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $res = self::request('POST', "instance/reconnect/{$inst}", [], false);
        if ($res['success']) {
            return $res;
        }

        return self::request('POST', 'instance/reconnect', [], false);
    }

    /**
     * Faz logout da instância (remove a sessão do WhatsApp)
     * Endpoint: DELETE /instance/logout/{instance} com fallback para GET disconnect
     */
    public static function logout(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();

        $res = self::request('DELETE', "instance/logout/{$inst}", [], false);
        if ($res['success']) {
            return $res;
        }

        $res2 = self::request('GET', "instance/disconnect/{$inst}", [], false);
        if ($res2['success']) {
            return $res2;
        }

        return self::request('POST', "instance/logout/{$inst}", [], false);
    }

    // =========================================================================
    // 3. SEND MESSAGE OPERATIONS (Autenticação com apikey / Token da Instância)
    // =========================================================================

    /**
     * Envia mensagem de texto simples
     * Endpoint padrão Swagger (https://whatsapp.km.site.nom.br/api/docs): POST /message/sendText/{instance} com fallback para POST /send/text
     */
    public static function enviarTexto(string $numero, string $texto, int $delay = 0): array
    {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $inst = self::getInstanceName();

        // 1. Tenta endpoint padrão Evolution API Go v3 (Swagger /api/docs): POST /message/sendText/{instance}
        $payloadV3 = [
            'number' => $num,
            'text' => $texto,
            'textMessage' => [
                'text' => $texto,
            ],
        ];
        if ($delay > 0) {
            $payloadV3['options'] = ['delay' => $delay];
            $payloadV3['delay'] = $delay;
        }

        $res = self::request('POST', "message/sendText/{$inst}", $payloadV3, false);
        if ($res['success']) {
            return $res;
        }

        // 2. Fallback para rota compacta POST /send/text
        $payloadCompact = [
            'number' => $num,
            'text' => $texto,
        ];
        if ($delay > 0) {
            $payloadCompact['delay'] = $delay;
        }

        $res2 = self::request('POST', 'send/text', $payloadCompact, false);
        if ($res2['success']) {
            return $res2;
        }

        return $res;
    }

    /**
     * Envia mensagem com botão nativo de pagamento PIX
     * Endpoint: POST /send/pix
     */
    public static function enviarPix(
        string $numero,
        string $headerTitle,
        string $bodyText,
        string $merchantName,
        string $pixKey,
        string $keyType,
        ?string $footerText = null,
        int $delay = 0
    ): array {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $payload = [
            'number' => $num,
            'headerTitle' => $headerTitle,
            'bodyText' => $bodyText,
            'merchantName' => $merchantName ?: self::getMerchantName(),
            'pixKey' => $pixKey ?: self::getPixKey(),
            'keyType' => strtoupper($keyType ?: self::getPixKeyType()),
            'footerText' => $footerText ?: 'Pagamento seguro via PIX - Foco 7',
        ];

        if ($delay > 0) {
            $payload['delay'] = $delay;
        }

        return self::request('POST', 'send/pix', $payload, false);
    }

    /**
     * Envia mensagem interativa com botão Review and Pay (revisão de pedido e PIX)
     * Endpoint: POST /send/button
     */
    public static function enviarBotaoReviewAndPay(
        string $numero,
        string $titulo,
        string $merchantName,
        string $pixKey,
        string $keyType,
        array $itens,
        ?string $descricao = null,
        ?string $footer = null,
        int $delay = 0
    ): array {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $btn = [
            'type' => 'review_and_pay',
            'displayText' => 'Revisar e Pagar',
            'currency' => 'BRL',
            'name' => $merchantName ?: self::getMerchantName(),
            'keyType' => strtoupper($keyType ?: self::getPixKeyType()),
            'key' => $pixKey ?: self::getPixKey(),
            'items' => $itens,
        ];

        $payload = [
            'number' => $num,
            'title' => $titulo,
            'buttons' => [$btn],
        ];

        if (! empty($descricao)) {
            $payload['description'] = $descricao;
        }
        if (! empty($footer)) {
            $payload['footer'] = $footer;
        }
        if ($delay > 0) {
            $payload['delay'] = $delay;
        }

        return self::request('POST', 'send/button', $payload, false);
    }

    /**
     * Envia mensagem interativa com botões de ação (URL, Copiar Código PIX, Ligar)
     * Endpoint: POST /send/button
     */
    public static function enviarBotoesInterativos(
        string $numero,
        string $titulo,
        string $descricao,
        string $footer,
        array $botoes,
        int $delay = 0
    ): array {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $payload = [
            'number' => $num,
            'title' => $titulo,
            'description' => $descricao,
            'footer' => $footer,
            'buttons' => $botoes,
        ];

        if ($delay > 0) {
            $payload['delay'] = $delay;
        }

        return self::request('POST', 'send/button', $payload, false);
    }

    /**
     * Envia documento ou mídia via URL (Ex: PDF de boleto ou carne)
     * Endpoint: POST /send/media
     */
    public static function enviarMedia(
        string $numero,
        string $url,
        string $tipo = 'document',
        ?string $caption = null,
        ?string $filename = null
    ): array {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $payload = [
            'number' => $num,
            'url' => $url,
            'type' => $tipo,
            'caption' => $caption ?: '',
            'filename' => $filename ?: basename(parse_url($url, PHP_URL_PATH) ?: 'fatura.pdf'),
        ];

        return self::request('POST', 'send/media', $payload, false);
    }

    // =========================================================================
    // 4. MÉTODOS DE COBRANÇA DE CLIENTES & DISPARO INTELIGENTE
    // =========================================================================

    /**
     * Sanitiza e formata o telefone para o padrão internacional do WhatsApp (Ex: 5591981117777)
     */
    public static function formatarNumeroWhatsapp(?string $telefone): string
    {
        if (empty($telefone)) {
            return '';
        }

        // Remove tudo que não for dígito
        $limpo = preg_replace('/\D/', '', (string) $telefone);

        if (empty($limpo)) {
            return '';
        }

        // Remove zero inicial se houver (Ex: 011988887777 -> 11988887777)
        if (str_starts_with($limpo, '0')) {
            $limpo = ltrim($limpo, '0');
        }

        // Se o número tiver 10 ou 11 dígitos (DDD + Número), adiciona o DDI 55 do Brasil
        if (strlen($limpo) === 10 || strlen($limpo) === 11) {
            return '55'.$limpo;
        }

        // Se já tiver DDI e for maior ou igual a 12 dígitos (ex: 5511988887777)
        if (strlen($limpo) >= 12 && str_starts_with($limpo, '55')) {
            return $limpo;
        }

        // Caso seja internacional ou já formatado
        return $limpo;
    }

    /**
     * Retorna os templates padrão de mensagens de cobrança para cada etapa
     */
    public static function getTemplatePadrao(string $etapa): string
    {
        return match ($etapa) {
            'cobranca_lembrete_1' => "Olá, *{cliente_nome}*! 👋\n\nSua fatura da *{empresa_nome}* já está disponível.\n\n📄 *Fatura:* #{fatura_id}\n📦 *Plano:* {plano_nome}\n💰 *Valor:* {valor}\n📅 *Vencimento:* {data_vencimento}\n\nVocê pode pagar com agilidade via PIX acessando o link:\n🔗 {link_fatura}\n\nObrigado por confiar na {empresa_nome}!",

            'cobranca_lembrete_2' => "Olá, *{cliente_nome}*! ⏰\n\nLembramos que sua fatura #{fatura_id} vence em breve no dia *{data_vencimento}*.\n\n💰 *Valor:* {valor}\n\nPara efetuar o pagamento instantâneo:\n🔗 {link_fatura}\n\nQualquer dúvida, estamos à sua total disposição!",

            'cobranca_lembrete_3' => "Atenção, *{cliente_nome}*! ⏳\n\nSua fatura da *{empresa_nome}* vence **amanhã** ({data_vencimento}).\n\n📄 *Fatura:* #{fatura_id}\n💰 *Valor:* {valor}\n\nEvite encargos de mora realizando o pagamento pelo link:\n🔗 {link_fatura}",

            'cobranca_vencimento_hoje' => "Olá, *{cliente_nome}*! ⚠️\n\nSua fatura #{fatura_id} da *{empresa_nome}* vence **HOJE** ({data_vencimento}).\n\n💰 *Valor:* {valor}\n\nPague via PIX agora mesmo para manter seu rastreamento sempre ativo:\n🔗 {link_fatura}",

            'cobranca_atraso_1' => "Olá, *{cliente_nome}*.\n\nConstatamos que a fatura #{fatura_id} venceu ontem ({data_vencimento}).\n\n💰 *Valor com Encargos:* {valor_atualizado} (Multa: {multa} | Juros: {juros})\n\nRegularize seu pagamento hoje mesmo e evite restrições:\n🔗 {link_fatura}",

            'cobranca_atraso_2' => "Prezado(a) *{cliente_nome}*,\n\nSua fatura #{fatura_id} está com *{dias_atraso} dias de atraso*.\n\n📅 *Vencimento Original:* {data_vencimento}\n💰 *Total Atualizado:* {valor_atualizado}\n\nSolicitamos a quitação para continuidade dos serviços de rastreamento e segurança:\n🔗 {link_fatura}",

            'cobranca_atraso_3' => "🚨 *AVISO URGENTE:* {cliente_nome},\n\nIdentificamos {dias_atraso} dias de inadimplência na fatura #{fatura_id}.\n\n⚠️ O sinal de rastreamento do seu veículo está sujeito a suspensão preventiva imediata.\n\n💰 *Valor Atualizado:* {valor_atualizado}\n\nPague agora via PIX para manter sua proteção ativa:\n🔗 {link_fatura}",

            'fatura_gerada' => "Olá, *{cliente_nome}*!\n\nUma nova fatura da *{empresa_nome}* foi gerada para você.\n\n📄 *Fatura:* #{fatura_id}\n📦 *Plano:* {plano_nome}\n💰 *Valor:* {valor}\n📅 *Vencimento:* {data_vencimento}\n\nLink para pagamento seguro:\n🔗 {link_fatura}",

            'pagamento_confirmado' => "Pagamento confirmado com sucesso! 🎉\n\nOlá, *{cliente_nome}*, recebemos o pagamento da sua fatura #{fatura_id} no valor de {valor}.\n\nSeus serviços de rastreamento continuam 100% ativos e monitorados. Obrigado pela parceria com a *{empresa_nome}*!",

            default => "Olá, *{cliente_nome}*! Segue a fatura #{fatura_id} no valor de {valor} com vencimento em {data_vencimento}.\n\nLink de pagamento: {link_fatura}",
        };
    }

    /**
     * Retorna o template personalizado configurado ou o padrão oficial
     */
    public static function obterTemplate(string $etapa): string
    {
        $custom = CmsSetting::get("evolution_tpl_{$etapa}");
        if (! empty($custom)) {
            return (string) $custom;
        }

        return self::getTemplatePadrao($etapa);
    }

    /**
     * Substitui todas as variáveis dinâmicas no template da mensagem
     */
    public static function formatarMensagem(string $template, Fatura $fatura, array $encargos = []): string
    {
        $cliente = $fatura->user;
        $nomeCliente = $cliente ? $cliente->name : 'Cliente';
        $planoNome = $fatura->plano ? $fatura->plano->nome : ($fatura->descricao ?: 'Mensalidade Rastreamento');

        $base = (float) ($fatura->valor_original ?: $fatura->valor);
        $multa = $encargos['multa'] ?? (float) ($fatura->valor_multa ?? 0);
        $juros = $encargos['juros'] ?? (float) ($fatura->valor_juros ?? 0);
        $total = $encargos['total_atualizado'] ?? round($base + $multa + $juros, 2);
        $diasAtraso = $encargos['dias_atraso'] ?? 0;

        if ($diasAtraso <= 0 && $fatura->data_vencimento && now()->startOfDay()->greaterThan($fatura->data_vencimento->copy()->startOfDay())) {
            $diasAtraso = (int) $fatura->data_vencimento->diffInDays(now()->startOfDay());
        }

        $empresaNome = (string) CmsSetting::get('site_nome', 'Foco 7 Rastreamento e Segurança');
        $telefoneSuporte = (string) CmsSetting::get('site_whatsapp', CmsSetting::get('site_telefone', '0800 000 0000'));
        $linkFatura = url("/cliente/faturas/{$fatura->id}");

        $map = [
            '{cliente_nome}' => $nomeCliente,
            '{cliente_email}' => $cliente?->email ?? '',
            '{fatura_id}' => $fatura->codigo_fatura,
            '{plano_nome}' => $planoNome,
            '{valor}' => 'R$ '.number_format($base, 2, ',', '.'),
            '{valor_original}' => 'R$ '.number_format($base, 2, ',', '.'),
            '{valor_atualizado}' => 'R$ '.number_format($total, 2, ',', '.'),
            '{multa}' => 'R$ '.number_format($multa, 2, ',', '.'),
            '{juros}' => 'R$ '.number_format($juros, 2, ',', '.'),
            '{dias_atraso}' => (string) $diasAtraso,
            '{data_vencimento}' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : '',
            '{link_fatura}' => $linkFatura,
            '{pix_copia_cola}' => $fatura->qr_code_pix ?: self::getPixKey(),
            '{codigo_barras}' => $fatura->linha_digitavel_boleto ?: '',
            '{chave_pix}' => self::getPixKey(),
            '{empresa_nome}' => $empresaNome,
            '{telefone_suporte}' => $telefoneSuporte,
        ];

        return str_replace(array_keys($map), array_values($map), $template);
    }

    /**
     * Dispara a cobrança de uma fatura para o WhatsApp do cliente
     */
    public static function enviarCobrancaFatura(Fatura $fatura, string $etapa = 'manual', array $encargosExtras = []): array
    {
        // 1. Verificações de segurança
        if (! self::isAtivo()) {
            return [
                'success' => false,
                'message' => 'A integração de cobranças via WhatsApp (Evolution API) está desativada no painel.',
            ];
        }

        if ($fatura->isLiquidadaOuCancelada() || ! empty($fatura->data_pagamento)) {
            return [
                'success' => false,
                'message' => "A fatura #{$fatura->codigo_fatura} já se encontra quitada ou cancelada.",
            ];
        }

        $cliente = $fatura->user;
        if (! $cliente) {
            return [
                'success' => false,
                'message' => 'A fatura não possui cliente associado.',
            ];
        }

        $numeroWhatsapp = self::formatarNumeroWhatsapp($cliente->telefone);
        if (empty($numeroWhatsapp)) {
            return [
                'success' => false,
                'message' => "O cliente {$cliente->name} não possui número de telefone/WhatsApp válido cadastrado.",
            ];
        }

        // 2. Calcula encargos se houver
        $encargos = ! empty($encargosExtras) ? $encargosExtras : ReguaCobrancaService::calcularEncargos($fatura);
        $totalAtualizado = $encargos['total_atualizado'] ?? (float) $fatura->valor;

        // 3. Monta a mensagem pelo template
        $template = self::obterTemplate($etapa);
        $textoMensagem = self::formatarMensagem($template, $fatura, $encargos);

        $tipoEnvio = self::getTipoEnvio();
        $pixKey = self::getPixKey();
        $keyType = self::getPixKeyType();
        $merchantName = self::getMerchantName();
        $linkFatura = url("/cliente/faturas/{$fatura->id}");

        $resultadoEnvio = null;

        // 4. Executa o disparo de acordo com o tipo de envio configurado
        switch ($tipoEnvio) {
            case 'pix_nativo':
                if (! empty($pixKey)) {
                    $headerTitle = 'Fatura #'.$fatura->codigo_fatura;
                    $resultadoEnvio = self::enviarPix(
                        $numeroWhatsapp,
                        $headerTitle,
                        $textoMensagem,
                        $merchantName,
                        $pixKey,
                        $keyType,
                        'Pagamento instantâneo via PIX'
                    );
                } else {
                    $resultadoEnvio = self::enviarTexto($numeroWhatsapp, $textoMensagem);
                }
                break;

            case 'review_and_pay':
                if (! empty($pixKey)) {
                    $valorCentavos = (int) round($totalAtualizado * 100);
                    $nomeItem = $fatura->plano ? $fatura->plano->nome : ($fatura->descricao ?: 'Mensalidade Rastreamento');

                    $itens = [
                        [
                            'name' => mb_substr($nomeItem, 0, 60),
                            'amount' => $valorCentavos,
                            'quantity' => 1,
                            'retailerId' => $fatura->codigo_fatura,
                        ],
                    ];

                    $resultadoEnvio = self::enviarBotaoReviewAndPay(
                        $numeroWhatsapp,
                        'Fatura #'.$fatura->codigo_fatura,
                        $merchantName,
                        $pixKey,
                        $keyType,
                        $itens,
                        $textoMensagem,
                        'Foco 7 Rastreamento e Segurança'
                    );
                } else {
                    $resultadoEnvio = self::enviarTexto($numeroWhatsapp, $textoMensagem);
                }
                break;

            case 'botoes_interativos':
                $botoes = [
                    [
                        'type' => 'url',
                        'displayText' => 'Pagar Fatura Online',
                        'url' => $linkFatura,
                    ],
                ];

                // Adiciona botão de copiar chave PIX se configurada ou PIX copia-e-cola
                $codigoCopia = ! empty($fatura->qr_code_pix) ? $fatura->qr_code_pix : $pixKey;
                if (! empty($codigoCopia)) {
                    $botoes[] = [
                        'type' => 'copy',
                        'displayText' => 'Copiar Código PIX',
                        'copyCode' => $codigoCopia,
                    ];
                }

                $resultadoEnvio = self::enviarBotoesInterativos(
                    $numeroWhatsapp,
                    'Fatura #'.$fatura->codigo_fatura,
                    $textoMensagem,
                    'Foco 7 Rastreamento',
                    $botoes
                );

                // Fallback automático para texto se o endpoint de botões retornar erro ou não for suportado no client
                if (! $resultadoEnvio['success']) {
                    $resultadoEnvio = self::enviarTexto($numeroWhatsapp, $textoMensagem);
                }
                break;

            case 'texto':
            default:
                // Se houver código copia e cola da fatura, anexa ao texto
                if (! empty($fatura->qr_code_pix)) {
                    $textoMensagem .= "\n\n📋 *PIX Copia e Cola:*\n```".$fatura->qr_code_pix.'```';
                }
                $resultadoEnvio = self::enviarTexto($numeroWhatsapp, $textoMensagem);
                break;
        }

        // 5. Envio opcional do PDF da fatura/carnê como mídia em anexo
        if (CmsSetting::get('evolution_enviar_pdf', '0') == '1' && $resultadoEnvio['success']) {
            $pdfUrl = null;
            if (! empty($fatura->link_pdf_carne)) {
                $pdfUrl = $fatura->link_pdf_carne;
            } elseif (! empty($fatura->url_boleto)) {
                $pdfUrl = $fatura->url_boleto;
            } elseif (! empty($fatura->codigo_carne)) {
                $pdfUrl = route('faturas.carne.pdf', ['codigo' => $fatura->codigo_carne]);
            }

            if (! empty($pdfUrl)) {
                try {
                    self::enviarMedia(
                        $numeroWhatsapp,
                        $pdfUrl,
                        'document',
                        "Segue em anexo a fatura #{$fatura->codigo_fatura}.",
                        "Fatura-{$fatura->codigo_fatura}.pdf"
                    );
                } catch (\Throwable $e) {
                    Log::warning("Falha ao anexar PDF de fatura via WhatsApp para {$fatura->codigo_fatura}: ".$e->getMessage());
                }
            }
        }

        // 6. Grava histórico na fatura
        $sucesso = $resultadoEnvio['success'] ?? false;
        $historico = $fatura->historico_cobranca ?: [];
        $historico[] = [
            'data_hora' => now()->format('Y-m-d H:i:s'),
            'etapa' => $etapa,
            'canal' => 'whatsapp',
            'telefone' => $numeroWhatsapp,
            'valor_base' => $fatura->valor,
            'multa' => $encargos['multa'] ?? 0,
            'juros' => $encargos['juros'] ?? 0,
            'total' => $totalAtualizado,
            'status' => $sucesso ? 'sucesso' : 'falha',
            'resposta_api' => $resultadoEnvio['data'] ?? ($resultadoEnvio['error'] ?? null),
        ];

        $fatura->ultimo_lembrete_enviado = $etapa;
        $fatura->data_ultimo_lembrete = now();
        $fatura->total_lembretes_enviados = ($fatura->total_lembretes_enviados ?? 0) + 1;
        $fatura->historico_cobranca = $historico;
        $fatura->save();

        if ($sucesso) {
            return [
                'success' => true,
                'message' => "Cobrança enviada com sucesso via WhatsApp para {$cliente->name} ({$numeroWhatsapp}).",
                'detalhes' => $resultadoEnvio,
            ];
        }

        return [
            'success' => false,
            'message' => 'Falha ao enviar via WhatsApp: '.($resultadoEnvio['error'] ?? 'Erro desconhecido'),
            'detalhes' => $resultadoEnvio,
        ];
    }

    /**
     * Envia confirmação de pagamento recebido via WhatsApp
     */
    public static function enviarConfirmacaoPagamento(Fatura $fatura): array
    {
        if (! self::isAtivo()) {
            return ['success' => false, 'message' => 'WhatsApp desativado.'];
        }

        $cliente = $fatura->user;
        if (! $cliente || empty($cliente->telefone)) {
            return ['success' => false, 'message' => 'Cliente sem telefone cadastrado.'];
        }

        $num = self::formatarNumeroWhatsapp($cliente->telefone);
        if (empty($num)) {
            return ['success' => false, 'message' => 'Telefone inválido.'];
        }

        $template = self::obterTemplate('pagamento_confirmado');
        $texto = self::formatarMensagem($template, $fatura);

        return self::enviarTexto($num, $texto);
    }
}

