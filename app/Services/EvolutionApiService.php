<?php

namespace App\Services;

use App\Models\CmsSetting;
use App\Models\Fatura;
use App\Models\Licenca;
use App\Models\Notificacao;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EvolutionApiService
{
    /**
     * Retorna a URL base limpa da Evolution API Go (v3.0)
     * Documentação Swagger: https://api.kdkhost.com.br/docs/llms.txt
     */
    public static function getBaseUrl(): string
    {
        $url = trim((string) CmsSetting::get('evolution_api_url', ''));
        if (empty($url) || $url === 'http://localhost:8080' || str_contains($url, '•')) {
            return 'https://api.kdkhost.com.br';
        }

        return rtrim($url, '/');
    }

    /**
     * Custo mensal recorrente de cada instância adicional do WhatsApp (R$ 60,00/mês)
     */
    public static function getCustoInstanciaExtra(): float
    {
        return (float) CmsSetting::get('evolution_custo_instancia_extra', 60.00);
    }

    /**
     * E-mail oficial da conta Mercado Pago / Chave PIX para recebimento recorrente das instâncias extras
     */
    public static function getPixEmailRecorrencia(): string
    {
        return trim((string) CmsSetting::get('evolution_pix_email_recorrencia', 'marcelobradrj+mp@gmail.com'));
    }

    /**
     * Verifica se o Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor) está ativo
     */
    public static function isDevMercadoPagoAtivo(): bool
    {
        return CmsSetting::get('dev_mp_ativo', '1') == '1';
    }

    /**
     * Retorna o ambiente ativo do Módulo Mercado Pago do Desenvolvedor ('producao' ou 'sandbox')
     */
    public static function getDevMercadoPagoAmbiente(): string
    {
        $amb = trim((string) CmsSetting::get('dev_mp_ambiente', 'producao'));

        return in_array($amb, ['producao', 'sandbox']) ? $amb : 'producao';
    }

    /**
     * Retorna a Public Key do Módulo Mercado Pago do Desenvolvedor
     */
    public static function getDevMercadoPagoPublicKey(?string $ambiente = null): string
    {
        $amb = $ambiente ?: self::getDevMercadoPagoAmbiente();
        $key = $amb === 'sandbox'
            ? CmsSetting::get('dev_mp_public_key_sandbox', '')
            : CmsSetting::get('dev_mp_public_key_prod', CmsSetting::get('dev_mp_public_key', ''));

        $clean = trim((string) $key);

        return str_contains($clean, '•') ? '' : $clean;
    }

    /**
     * Retorna o Access Token EXCLUSIVO do Módulo Mercado Pago do Desenvolvedor para cobrança das instâncias.
     * JAMAIS faz fallback para o Mercado Pago do administrador/empresa, mantendo os 2 módulos 100% independentes.
     */
    public static function getMercadoPagoAccessTokenParaInstancias(?string $ambiente = null): string
    {
        $amb = $ambiente ?: self::getDevMercadoPagoAmbiente();

        if ($amb === 'sandbox') {
            $tokenSandbox = trim((string) CmsSetting::get('dev_mp_access_token_sandbox', ''));
            if (! empty($tokenSandbox) && ! str_contains($tokenSandbox, '•')) {
                return $tokenSandbox;
            }
        }

        $tokenProd = trim((string) CmsSetting::get('dev_mp_access_token_prod', ''));
        if (! empty($tokenProd) && ! str_contains($tokenProd, '•')) {
            return $tokenProd;
        }

        $tokenExclusivo = trim((string) CmsSetting::get('evolution_mp_access_token', ''));
        if (! empty($tokenExclusivo) && ! str_contains($tokenExclusivo, '•')) {
            return $tokenExclusivo;
        }

        return '';
    }

    /**
     * Retorna o Client ID do Módulo Mercado Pago do Desenvolvedor
     */
    public static function getDevMercadoPagoClientId(?string $ambiente = null): string
    {
        $amb = $ambiente ?: self::getDevMercadoPagoAmbiente();
        $val = $amb === 'sandbox'
            ? CmsSetting::get('dev_mp_client_id_sandbox', '')
            : CmsSetting::get('dev_mp_client_id_prod', '');

        $clean = trim((string) $val);

        return str_contains($clean, '•') ? '' : $clean;
    }

    /**
     * Retorna o Client Secret do Módulo Mercado Pago do Desenvolvedor
     */
    public static function getDevMercadoPagoClientSecret(?string $ambiente = null): string
    {
        $amb = $ambiente ?: self::getDevMercadoPagoAmbiente();
        $val = $amb === 'sandbox'
            ? CmsSetting::get('dev_mp_client_secret_sandbox', '')
            : CmsSetting::get('dev_mp_client_secret_prod', '');

        $clean = trim((string) $val);

        return str_contains($clean, '•') ? '' : $clean;
    }

    /**
     * Retorna o Webhook Secret do Módulo Mercado Pago do Desenvolvedor
     */
    public static function getDevMercadoPagoWebhookSecret(): string
    {
        $val = trim((string) CmsSetting::get('dev_mp_webhook_secret', ''));

        return str_contains($val, '•') ? '' : $val;
    }

    /**
     * Retorna o Nome do Beneficiário do Módulo Mercado Pago do Desenvolvedor
     */
    public static function getDevMercadoPagoBeneficiario(): string
    {
        $val = trim((string) CmsSetting::get('dev_mp_beneficiario', 'FOCO 7 TECNOLOGIA - DESENVOLVEDOR'));

        return (empty($val) || str_contains($val, '•')) ? 'FOCO 7 TECNOLOGIA - DESENVOLVEDOR' : $val;
    }

    /**
     * Resolve a URL base pública para injeção do Webhook Dinâmico a cada requisição no Mercado Pago
     */
    public static function resolverBaseUrlWebhookDinamico(): string
    {
        try {
            if (! app()->runningInConsole() && request()) {
                $host = request()->getSchemeAndHttpHost();
                if (! empty($host) && ! str_contains($host, 'localhost') && ! str_contains($host, '127.0.0.1')) {
                    return rtrim($host, '/');
                }
            }
        } catch (\Throwable $e) {
        }

        $appUrl = rtrim((string) config('app.url', 'https://foco7.com.br'), '/');
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            return 'https://foco7.com.br';
        }

        return $appUrl;
    }

    /**
     * Testa a conexão em tempo real com a API do Mercado Pago exclusiva do Desenvolvedor (Suporta Sandbox e Produção dinâmicos)
     */
    public static function testarConexaoMercadoPagoDev(?string $ambienteOverride = null, ?string $tokenOverride = null): array
    {
        $ambiente = in_array($ambienteOverride, ['producao', 'sandbox'], true)
            ? $ambienteOverride
            : self::getDevMercadoPagoAmbiente();

        $tokenClean = trim((string) $tokenOverride);
        if (! empty($tokenClean) && ! str_contains($tokenClean, '•')) {
            $token = $tokenClean;
        } else {
            $token = self::getMercadoPagoAccessTokenParaInstancias($ambiente);
        }

        $labelAmbiente = $ambiente === 'sandbox' ? 'SANDBOX (Homologação)' : 'PRODUÇÃO (Recebimento Real PIX)';

        if (empty($token)) {
            return [
                'success' => false,
                'modo' => 'pix_emv_fallback',
                'ambiente' => $ambiente,
                'message' => "Access Token do ambiente {$labelAmbiente} ainda não informado no Módulo Mercado Pago (Desenvolvedor). Enquanto não configurado, o checkout opera via PIX Copia e Cola EMV direto para a chave ".self::getPixEmailRecorrencia().'.',
            ];
        }

        try {
            $res = Http::withToken($token)->timeout(10)->get('https://api.mercadopago.com/users/me');
            if ($res->successful()) {
                $data = $res->json();

                return [
                    'success' => true,
                    'modo' => 'api_transparente_pix',
                    'ambiente' => $ambiente,
                    'metodo_exclusivo' => 'pix',
                    'webhook_dinamico' => true,
                    'conta_id' => $data['id'] ?? null,
                    'nickname' => $data['nickname'] ?? null,
                    'email' => $data['email'] ?? null,
                    'first_name' => $data['first_name'] ?? null,
                    'message' => "Conectado com sucesso ao ambiente {$labelAmbiente} na API do Mercado Pago do Desenvolvedor (".($data['nickname'] ?? ($data['email'] ?? 'Conta Oficial')).') • Checkout Transparente PIX e Webhook Dinâmico ativos!',
                ];
            }

            return [
                'success' => false,
                'modo' => 'erro_credencial',
                'ambiente' => $ambiente,
                'message' => 'Access Token rejeitado pelo Mercado Pago (HTTP '.$res->status().'). Verifique se o token informado pertence ao ambiente '.$labelAmbiente.'.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'modo' => 'erro_rede',
                'ambiente' => $ambiente,
                'message' => 'Falha ao comunicar com a API do Mercado Pago: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Retorna os dados de tarifação da instância atual usada para configuração (ex: foco7)
     */
    public static function getInstanciaConfiguracaoInfo(): array
    {
        $nome = self::getInstanceName();
        $custo = self::getCustoInstanciaExtra();
        $status = (string) CmsSetting::get('evolution_master_status_cobranca', 'ativa');
        $vencimento = (string) CmsSetting::get('evolution_master_vencimento', '');
        if (empty($vencimento) || str_contains($vencimento, '•')) {
            $vencimento = date('Y-m-d', strtotime('+30 days'));
            CmsSetting::set('evolution_master_vencimento', $vencimento, 'whatsapp');
        }
        $hash = (string) CmsSetting::get('evolution_master_hash', '');
        if (empty($hash)) {
            $hash = md5($nome.'_master_wpp_sec');
            CmsSetting::set('evolution_master_hash', $hash, 'whatsapp');
        }

        if ($vencimento < date('Y-m-d') && $status === 'ativa') {
            $status = 'vencida';
        }

        return [
            'nome' => $nome,
            'token' => self::getInstanceApiKey(),
            'tipo' => 'configuracao_master',
            'status' => $status,
            'data_vencimento' => $vencimento,
            'valor_recorrente' => $custo,
            'hash_pagamento' => $hash,
        ];
    }

    /**
     * Verifica se o Administrador já criou e ativou sua própria instância no painel (via Mercado Pago do Desenvolvedor)
     * Regra de negócio: A instância atual usada para configuração exige que o administrador crie sua própria instância
     * e ative/pague no módulo Mercado Pago do Desenvolvedor antes de cobrar clientes via WhatsApp.
     * Fora isso, só será possível cobrar clientes via E-mail!
     */
    public static function verificarPermissaoCobrancaWhatsapp(): array
    {
        if (! self::isAtivo()) {
            return [
                'permitido' => false,
                'motivo_codigo' => 'whatsapp_desativado',
                'canal_permitido' => 'email',
                'instancia_propria' => null,
                'message' => 'O módulo de WhatsApp está desativado. As cobranças de clientes estão sendo enviadas exclusivamente via E-mail.',
            ];
        }

        $hoje = date('Y-m-d');
        $extras = self::getInstanciasExtras();
        $instanciaPropriaNome = trim((string) CmsSetting::get('evolution_admin_instancia_propria', ''));
        $instanciaAtiva = self::getInstanceName();

        $instanciaAdmin = null;

        // 1. Procura a instância própria criada pelo administrador na lista de instâncias do painel
        foreach ($extras as $item) {
            $tipo = $item['tipo'] ?? 'empresa';
            $nomeItem = $item['nome'] ?? '';
            if (in_array($tipo, ['propria_admin', 'empresa']) && (
                (! empty($instanciaPropriaNome) && strtolower($nomeItem) === strtolower($instanciaPropriaNome))
                || strtolower($nomeItem) === strtolower($instanciaAtiva)
            )) {
                $instanciaAdmin = $item;
                break;
            }
        }

        if (! $instanciaAdmin) {
            foreach ($extras as $item) {
                if (in_array($item['tipo'] ?? '', ['propria_admin', 'empresa'])) {
                    $instanciaAdmin = $item;
                    break;
                }
            }
        }

        // 2. Se o administrador ainda NÃO criou sua própria instância no painel:
        if (! $instanciaAdmin) {
            $custoFmt = number_format(self::getCustoInstanciaExtra(), 2, ',', '.');

            return [
                'permitido' => false,
                'motivo_codigo' => 'sem_instancia_propria',
                'canal_permitido' => 'email',
                'instancia_propria' => null,
                'message' => "A instância atual ('{$instanciaAtiva}') é destinada à configuração do servidor. O administrador deve criar sua própria instância neste painel e ativá-la via Mercado Pago (R$ {$custoFmt}/mês) antes de cobrar clientes via WhatsApp. Enquanto isso, todas as cobranças são realizadas exclusivamente via E-mail.",
            ];
        }

        // 3. Verifica se a instância própria criada pelo administrador está com pagamento ativo e em dia
        $statusInst = $instanciaAdmin['status'] ?? 'pendente_pagamento';
        $vencInst = $instanciaAdmin['data_vencimento'] ?? $hoje;
        $nomeInst = $instanciaAdmin['nome'] ?? '';
        $custoFmt = number_format((float) ($instanciaAdmin['valor_recorrente'] ?? self::getCustoInstanciaExtra()), 2, ',', '.');

        if ($statusInst === 'pendente_pagamento') {
            return [
                'permitido' => false,
                'motivo_codigo' => 'pendente_pagamento',
                'canal_permitido' => 'email',
                'instancia_propria' => $instanciaAdmin,
                'message' => "Sua instância própria ('{$nomeInst}') foi criada, mas aguarda confirmação do pagamento via PIX (R$ {$custoFmt}/mês) no módulo Mercado Pago do Desenvolvedor. Enquanto o pagamento não for confirmado, as cobranças de clientes são enviadas apenas via E-mail.",
            ];
        }

        if ($statusInst === 'suspensa' || $statusInst === 'vencida' || $vencInst < $hoje) {
            return [
                'permitido' => false,
                'motivo_codigo' => 'instancia_vencida_ou_suspensa',
                'canal_permitido' => 'email',
                'instancia_propria' => $instanciaAdmin,
                'message' => "A assinatura mensal da sua instância ('{$nomeInst}') está {$statusInst} (Vencimento: ".date('d/m/Y', strtotime($vencInst)).'). Regularize via PIX no módulo Mercado Pago do Desenvolvedor para reabilitar cobranças via WhatsApp. No momento, as cobranças ocorrem somente via E-mail.',
            ];
        }

        return [
            'permitido' => true,
            'motivo_codigo' => 'ok',
            'canal_permitido' => 'whatsapp_e_email',
            'instancia_propria' => $instanciaAdmin,
            'message' => "Instância própria ('{$nomeInst}') ativa e regularizada até ".date('d/m/Y', strtotime($vencInst)).'. Cobranças via WhatsApp liberadas!',
        ];
    }

    /**
     * Verifica se a suspensão automática por vencimento está habilitada no sistema
     */
    public static function isSuspensaoAutomaticaAtiva(): bool
    {
        return CmsSetting::get('evolution_suspensao_automatica', '1') == '1';
    }

    /**
     * Gera o código PIX Copia e Cola no padrão oficial EMV BACEN (BR Code)
     */
    public static function gerarPixBrCode(string $chavePix, float $valor, string $nomeRecebedor = 'FOCO 7 TECNOLOGIA', string $cidade = 'BELEM', ?string $txid = null): string
    {
        $chave = trim($chavePix);
        $txid = preg_replace('/[^a-zA-Z0-9]/', '', $txid ?: 'WPP'.time());
        $txid = substr($txid ?: 'WPPCOBRANCA', 0, 25);
        $nome = mb_strtoupper(preg_replace('/[^a-zA-Z0-9 ]/', '', substr($nomeRecebedor, 0, 25)));
        $cidade = mb_strtoupper(preg_replace('/[^a-zA-Z0-9 ]/', '', substr($cidade, 0, 15)));
        $valorFormatado = number_format(round($valor, 2), 2, '.', '');

        // Formata campo EMV TLV (Tag, Length, Value)
        $tlv = function (string $id, string $val): string {
            return sprintf('%02d%02d%s', (int) $id, strlen($val), $val);
        };

        // Merchant Account Information (ID 26)
        $gui = $tlv('00', 'br.gov.bcb.pix');
        $key = $tlv('01', $chave);
        $mai = $tlv('26', $gui.$key);

        // Monta carga útil do payload
        $payload = $tlv('00', '01')                           // Payload Format Indicator
            .$mai                                            // Merchant Account Info
            .$tlv('52', '0000')                              // Merchant Category Code
            .$tlv('53', '986')                               // Transaction Currency (BRL)
            .$tlv('54', $valorFormatado)                     // Transaction Amount
            .$tlv('58', 'BR')                                // Country Code
            .$tlv('59', $nome ?: 'FOCO 7 TECNOLOGIA')       // Merchant Name
            .$tlv('60', $cidade ?: 'BELEM')                  // Merchant City
            .$tlv('62', $tlv('05', $txid))                   // Additional Data Field (TxID)
            .'6304';                                         // CRC16 Header

        // Calcula CRC16 CCITT (0xFFFF)
        $crc = 0xFFFF;
        $len = strlen($payload);
        for ($i = 0; $i < $len; $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $payload.strtoupper(sprintf('%04X', $crc));
    }

    /**
     * Retorna a lista de instâncias extras cadastradas com cobrança recorrente (R$ 60,00/mês)
     */
    public static function getInstanciasExtras(): array
    {
        $data = CmsSetting::get('evolution_instancias_extras', []);
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $extras = is_array($decoded) ? $decoded : [];
        } else {
            $extras = is_array($data) ? $data : [];
        }

        // Normaliza campos de cada instância para garantir que tenham status e data_vencimento
        $hoje = date('Y-m-d');
        foreach ($extras as &$item) {
            if (empty($item['valor_recorrente'])) {
                $item['valor_recorrente'] = self::getCustoInstanciaExtra();
            }
            if (empty($item['status'])) {
                $item['status'] = 'ativa';
            }
            if (empty($item['data_vencimento'])) {
                $criadoEm = ! empty($item['criado_em']) ? strtotime($item['criado_em']) : time();
                $item['data_vencimento'] = date('Y-m-d', strtotime('+30 days', $criadoEm));
            }

            // Gera hash único de pagamento direto caso não exista
            if (empty($item['hash_pagamento'])) {
                $item['hash_pagamento'] = md5(($item['nome'] ?? '').($item['criado_em'] ?? 'foco7').'wpp_sec');
            }

            // Vincula dados do responsável a partir da licença se for do tipo licenciado
            if (! empty($item['licenciado_id']) && (empty($item['telefone_responsavel']) || empty($item['nome_responsavel']))) {
                try {
                    $licenca = Licenca::find($item['licenciado_id']);
                    if ($licenca && $licenca->user) {
                        $item['nome_responsavel'] = $item['nome_responsavel'] ?: $licenca->user->name;
                        $item['telefone_responsavel'] = $item['telefone_responsavel'] ?: $licenca->user->telefone;
                        $item['email_responsavel'] = $item['email_responsavel'] ?: $licenca->user->email;
                    }
                } catch (\Throwable $e) {
                }
            }

            // Checa automaticamente se está vencida (com carência de 24 horas para suspensão)
            if ($item['data_vencimento'] < $hoje && $item['status'] === 'ativa') {
                $item['status'] = 'vencida';
            }
        }

        return $extras;
    }

    /**
     * Registra ou atualiza uma instância com tarifação recorrente de R$ 60,00/mês no Módulo Mercado Pago do Desenvolvedor
     */
    public static function registrarInstanciaExtra(
        string $nome,
        ?string $token = null,
        string $tipo = 'empresa',
        ?int $licenciadoId = null,
        ?string $telefoneResponsavel = null,
        ?string $nomeResponsavel = null,
        ?string $emailResponsavel = null,
        ?string $statusInicial = null
    ): array {
        $extras = self::getInstanciasExtras();
        $custo = self::getCustoInstanciaExtra();

        $hash = md5($nome.time().'wpp_sec');

        // Define o status inicial: se criada sem Senha Master desbloqueada, exige pagamento inicial via PIX no Mercado Pago do Desenvolvedor
        if ($statusInicial === null) {
            $devUnlocked = false;
            try {
                $devUnlocked = (bool) session('whatsapp_dev_unlocked', false);
            } catch (\Throwable $e) {
            }
            $statusInicial = $devUnlocked ? 'ativa' : 'pendente_pagamento';
        }

        $vencimentoInicial = ($statusInicial === 'ativa')
            ? now()->addDays(30)->format('Y-m-d')
            : now()->format('Y-m-d');

        $nova = [
            'nome' => $nome,
            'token' => $token,
            'tipo' => $tipo, // 'propria_admin', 'empresa' ou 'licenciado'
            'licenciado_id' => $licenciadoId,
            'nome_responsavel' => $nomeResponsavel,
            'telefone_responsavel' => $telefoneResponsavel,
            'email_responsavel' => $emailResponsavel,
            'hash_pagamento' => $hash,
            'valor_recorrente' => $custo,
            'data_vencimento' => $vencimentoInicial,
            'ultimo_pagamento' => ($statusInicial === 'ativa') ? now()->format('Y-m-d H:i:s') : null,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'status' => $statusInicial,
        ];

        // Se for licenciado e não veio contato, tenta carregar da licença
        if (! empty($licenciadoId) && (empty($telefoneResponsavel) || empty($nomeResponsavel))) {
            try {
                $lic = Licenca::find($licenciadoId);
                if ($lic && $lic->user) {
                    $nova['nome_responsavel'] = $nova['nome_responsavel'] ?: $lic->user->name;
                    $nova['telefone_responsavel'] = $nova['telefone_responsavel'] ?: $lic->user->telefone;
                    $nova['email_responsavel'] = $nova['email_responsavel'] ?: $lic->user->email;
                }
            } catch (\Throwable $e) {
            }
        }

        $encontrou = false;
        foreach ($extras as &$item) {
            if (($item['nome'] ?? '') === $nome) {
                // Preserva hash_pagamento e status ativo se já existir e estiver ativa
                if (! empty($item['hash_pagamento'])) {
                    $nova['hash_pagamento'] = $item['hash_pagamento'];
                }
                if (($item['status'] ?? '') === 'ativa' && ! empty($item['ultimo_pagamento'])) {
                    $nova['status'] = 'ativa';
                    $nova['data_vencimento'] = $item['data_vencimento'] ?? $nova['data_vencimento'];
                    $nova['ultimo_pagamento'] = $item['ultimo_pagamento'];
                }
                $item = array_merge($item, $nova);
                $encontrou = true;
                break;
            }
        }
        if (! $encontrou) {
            $extras[] = $nova;
        }

        CmsSetting::set('evolution_instancias_extras', $extras, 'whatsapp', 'json');

        // Se for instância própria do administrador, vincula como instância própria
        if (in_array($tipo, ['propria_admin', 'empresa']) && strtolower($nome) !== 'foco7') {
            CmsSetting::set('evolution_admin_instancia_propria', $nome, 'whatsapp');
        }

        return $nova;
    }

    /**
     * Gera cobrança PIX direta na conta Mercado Pago do Desenvolvedor com checkout transparente íntegro
     * Opera exclusivamente via PIX (payment_method_id = pix), injeta Webhook Dinâmico a cada requisição
     * e suporta alternância dinâmica entre Sandbox e Produção no Módulo Paralelo.
     */
    public static function gerarCobrancaPixInstancia(string $nomeInstancia, ?int $userId = null, bool $forcarNovaRequisicao = false): array
    {
        $extras = self::getInstanciasExtras();
        $instancia = null;
        foreach ($extras as $item) {
            if (strtolower($item['nome'] ?? '') === strtolower($nomeInstancia)) {
                $instancia = $item;
                break;
            }
        }

        // Suporte nativo para cobrar também a instância atual usada para configuração (ex: foco7)
        if (! $instancia) {
            $configInst = self::getInstanciaConfiguracaoInfo();
            if (strtolower($configInst['nome'] ?? '') === strtolower($nomeInstancia) || strtolower($nomeInstancia) === 'foco7') {
                $instancia = $configInst;
            }
        }

        $custo = (float) ($instancia['valor_recorrente'] ?? self::getCustoInstanciaExtra()); // 60.00
        $chavePix = self::getPixEmailRecorrencia(); // marcelobradrj+mp@gmail.com
        $beneficiarioDev = self::getDevMercadoPagoBeneficiario();
        $ambiente = self::getDevMercadoPagoAmbiente(); // 'producao' ou 'sandbox'
        $codigoFatura = 'FAT-WPP-'.strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $nomeInstancia), 0, 10)).'-'.date('Ymd');
        $hashPagamento = $instancia['hash_pagamento'] ?? md5($nomeInstancia.'wpp_sec');

        // Localiza usuário associado se houver (licenciado ou admin)
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif (! empty($instancia['licenciado_id'])) {
            $licenca = Licenca::find($instancia['licenciado_id']);
            $user = $licenca?->user;
        }
        if (! $user) {
            $user = User::where('role', 'admin')->first() ?: auth()->user();
        }

        // Verifica se já existe fatura recente pendente para essa instância
        $fatura = Fatura::where('descricao', 'like', "%{$nomeInstancia}%")
            ->where('status', 'pendente')
            ->latest()
            ->first();

        if (! $fatura) {
            $fatura = Fatura::create([
                'user_id' => $user?->id ?: 1,
                'codigo_fatura' => $codigoFatura.'-'.strtoupper(Str::random(3)),
                'descricao' => "Foco 7 - Servidor Dedicado WhatsApp Cloud ({$nomeInstancia})",
                'valor' => $custo,
                'valor_original' => $custo,
                'gateway' => 'mercadopago',
                'metodo_pagamento' => 'pix',
                'status' => 'pendente',
                'data_vencimento' => now()->addDays(2),
            ]);
        } else {
            // Garante método de pagamento estritamente PIX
            if ($fatura->metodo_pagamento !== 'pix' || (float) $fatura->valor !== $custo) {
                $fatura->update([
                    'metodo_pagamento' => 'pix',
                    'gateway' => 'mercadopago',
                    'valor' => $custo,
                ]);
            }
        }

        // Token exclusivo de requisição e URL de Webhook Dinâmico gerada a cada requisição
        $reqToken = strtolower(Str::random(16));
        $timestampReq = time();
        $baseUrlWebhook = self::resolverBaseUrlWebhookDinamico();
        $webhookUrlDinamico = $baseUrlWebhook.'/webhook/mercadopago?'
            .http_build_query([
                'origem' => 'instancia_wpp',
                'modulo' => 'mp_dev_paralelo',
                'metodo' => 'pix',
                'ambiente' => $ambiente,
                'wpp_instancia' => $nomeInstancia,
                'fatura_id' => $fatura->id,
                'fatura_codigo' => $fatura->codigo_fatura,
                'hash' => $hashPagamento,
                'req_token' => $reqToken,
                'ts' => $timestampReq,
            ]);

        $copiaCola = $fatura->qr_code_pix;
        $qrUrl = $fatura->qr_code_pix_url;

        $payloadSalvo = is_array($fatura->webhook_payload) ? $fatura->webhook_payload : [];
        $ambienteFaturaAnterior = $payloadSalvo['ambiente_mp_dev'] ?? null;

        // Se mudou o ambiente (Sandbox <-> Produção) ou se foi solicitada nova requisição PIX, força geração de novo PIX na API
        $precisaGerarNovoPixMp = $forcarNovaRequisicao
            || empty($copiaCola)
            || empty($fatura->gateway_transaction_id)
            || ($ambienteFaturaAnterior !== null && $ambienteFaturaAnterior !== $ambiente);

        // 1. Gera via API Transparente do Segundo Módulo Mercado Pago (Exclusivo do Desenvolvedor - Cobrança via PIX Apenas)
        $mpAccessToken = self::getMercadoPagoAccessTokenParaInstancias($ambiente);

        if (! empty($mpAccessToken) && $precisaGerarNovoPixMp) {
            try {
                $cpfLimpo = preg_replace('/\D/', '', $user?->cpf_cnpj ?? '00000000000');
                if (strlen($cpfLimpo) < 11) {
                    $cpfLimpo = '11144477735';
                }

                $nomePayer = $instancia['nome_responsavel'] ?? ($user?->name ?: 'Responsável WhatsApp');
                $emailPayer = $instancia['email_responsavel'] ?? ($user?->email ?: 'financeiro@foco7.com.br');

                $payloadMp = [
                    'transaction_amount' => round((float) $custo, 2),
                    'description' => "Foco 7 - Servidor Dedicado WhatsApp Cloud ({$nomeInstancia})",
                    'payment_method_id' => 'pix',
                    'date_of_expiration' => now()->addHours(24)->format('Y-m-d\TH:i:s.000P'),
                    'payer' => [
                        'email' => $emailPayer,
                        'first_name' => substr($nomePayer, 0, 50),
                        'identification' => [
                            'type' => strlen($cpfLimpo) > 11 ? 'CNPJ' : 'CPF',
                            'number' => $cpfLimpo,
                        ],
                    ],
                    'notification_url' => $webhookUrlDinamico,
                    'external_reference' => $fatura->codigo_fatura,
                    'metadata' => [
                        'origem_modulo' => 'mercadopago_desenvolvedor',
                        'metodo_exclusivo' => 'pix',
                        'ambiente' => $ambiente,
                        'wpp_instancia' => $nomeInstancia,
                        'fatura_id' => $fatura->id,
                        'fatura_codigo' => $fatura->codigo_fatura,
                        'hash_pagamento' => $hashPagamento,
                        'req_token' => $reqToken,
                    ],
                ];

                $response = Http::withToken($mpAccessToken)
                    ->withHeaders([
                        'X-Idempotency-Key' => (string) Str::uuid(),
                    ])
                    ->timeout(12)
                    ->post('https://api.mercadopago.com/v1/payments', $payloadMp);

                if ($response->successful()) {
                    $data = $response->json();
                    $copiaCola = $data['point_of_interaction']['transaction_data']['qr_code'] ?? null;
                    $qrCodeBase64 = $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
                    if ($qrCodeBase64) {
                        $qrUrl = 'data:image/png;base64,'.$qrCodeBase64;
                    }
                    $fatura->update([
                        'gateway' => 'mercadopago',
                        'metodo_pagamento' => 'pix',
                        'gateway_transaction_id' => (string) ($data['id'] ?? ''),
                        'qr_code_pix' => $copiaCola,
                        'qr_code_pix_url' => $qrUrl,
                        'webhook_payload' => array_merge($data, [
                            'origem_modulo' => 'mercadopago_desenvolvedor',
                            'metodo_exclusivo' => 'pix',
                            'ambiente_mp_dev' => $ambiente,
                            'wpp_instancia' => $nomeInstancia,
                            'webhook_dinamico_url' => $webhookUrlDinamico,
                            'req_token' => $reqToken,
                        ]),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("Falha ao gerar PIX Mercado Pago (Módulo Desenvolvedor [{$ambiente}]) para instância {$nomeInstancia}: ".$e->getMessage());
            }
        } elseif (! empty($payloadSalvo['webhook_dinamico_url'])) {
            $webhookUrlDinamico = (string) $payloadSalvo['webhook_dinamico_url'];
            $reqToken = (string) ($payloadSalvo['req_token'] ?? $reqToken);
        }

        // 2. Fallback de alta fidelidade: Gera código BR Code EMV oficial para a chave PIX do Desenvolvedor
        if (empty($copiaCola)) {
            $txid = 'WPP'.strtoupper(substr(md5($nomeInstancia.date('Ymd').$reqToken), 0, 15));
            $copiaCola = self::gerarPixBrCode($chavePix, $custo, $beneficiarioDev, 'BELEM', $txid);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data='.urlencode($copiaCola);
            $fatura->update([
                'metodo_pagamento' => 'pix',
                'qr_code_pix' => $copiaCola,
                'qr_code_pix_url' => $qrUrl,
                'webhook_payload' => array_merge($payloadSalvo, [
                    'origem_modulo' => 'mercadopago_desenvolvedor',
                    'metodo_exclusivo' => 'pix',
                    'ambiente_mp_dev' => $ambiente,
                    'wpp_instancia' => $nomeInstancia,
                    'webhook_dinamico_url' => $webhookUrlDinamico,
                    'req_token' => $reqToken,
                ]),
            ]);
        }

        $linkDireto = url("/pagar/instancia/{$hashPagamento}");

        return [
            'success' => true,
            'instancia' => $nomeInstancia,
            'fatura_id' => $fatura->id,
            'codigo_fatura' => $fatura->codigo_fatura,
            'transacao_id' => $fatura->gateway_transaction_id ?: 'PIX-EMV-'.strtoupper(substr($reqToken, 0, 8)),
            'hash_pagamento' => $hashPagamento,
            'link_pagamento_direto' => $linkDireto,
            'valor' => $custo,
            'valor_formatado' => 'R$ '.number_format($custo, 2, ',', '.'),
            'descricao' => "Foco 7 - Servidor Dedicado WhatsApp Cloud ({$nomeInstancia})",
            'subtitulo' => 'Checkout Transparente Íntegro • Somente PIX • Retorno Automático',
            'chave_pix_email' => $chavePix,
            'beneficiario' => $beneficiarioDev,
            'pix_copia_cola' => $copiaCola,
            'pix_qr_url' => $qrUrl,
            'data_vencimento' => $fatura->data_vencimento ? $fatura->data_vencimento->format('d/m/Y') : date('d/m/Y', strtotime('+2 days')),
            'status' => $fatura->status,
            'ambiente' => $ambiente,
            'ambiente_label' => $ambiente === 'sandbox' ? 'Sandbox (Homologação)' : 'Produção (PIX Oficial)',
            'metodo_exclusivo' => 'pix',
            'checkout_transparente' => true,
            'retorno_automatico' => true,
            'webhook_dinamico_url' => $webhookUrlDinamico,
            'req_token' => $reqToken,
            'modulo_mp_dev_ativo' => ! empty($mpAccessToken),
        ];
    }

    /**
     * Consulta e processa a aprovação do pagamento PIX de uma instância exclusivamente no Módulo Mercado Pago do Desenvolvedor
     * Suporta ambos os ambientes (Sandbox e Produção) e aciona o Retorno Automático do Checkout Transparente.
     */
    public static function verificarPagamentoInstancia(string $nomeInstancia): array
    {
        $fatura = Fatura::where('descricao', 'like', "%{$nomeInstancia}%")
            ->latest()
            ->first();

        $pago = false;
        $ambienteAtual = self::getDevMercadoPagoAmbiente();

        if ($fatura) {
            // Se já foi marcada como paga (ex: via Webhook Dinâmico instantâneo)
            if ($fatura->status === 'pago') {
                $pago = true;
            } elseif (! empty($fatura->gateway_transaction_id)) {
                // Consulta no Módulo Mercado Pago do Desenvolvedor (testa ambiente da fatura e ambiente ativo)
                $payloadSalvo = is_array($fatura->webhook_payload) ? $fatura->webhook_payload : [];
                $ambFatura = $payloadSalvo['ambiente_mp_dev'] ?? $ambienteAtual;

                $tokensDev = array_unique(array_filter([
                    self::getMercadoPagoAccessTokenParaInstancias($ambFatura),
                    self::getMercadoPagoAccessTokenParaInstancias('producao'),
                    self::getMercadoPagoAccessTokenParaInstancias('sandbox'),
                ]));

                foreach ($tokensDev as $mpAccessToken) {
                    try {
                        $res = Http::withToken($mpAccessToken)
                            ->timeout(8)
                            ->get("https://api.mercadopago.com/v1/payments/{$fatura->gateway_transaction_id}");
                        if ($res->successful()) {
                            $st = strtolower($res->json('status') ?? '');
                            if (in_array($st, ['approved', 'pago'])) {
                                $fatura->update([
                                    'status' => 'pago',
                                    'metodo_pagamento' => 'pix',
                                    'data_pagamento' => now(),
                                    'webhook_payload' => array_merge($payloadSalvo, $res->json(), [
                                        'confirmado_em' => now()->format('Y-m-d H:i:s'),
                                        'retorno_automatico' => true,
                                    ]),
                                ]);
                                $pago = true;
                            }
                            break;
                        }
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        if ($pago) {
            // Reativa a instância automaticamente e prorroga o vencimento em +30 dias
            self::reativarInstancia($nomeInstancia, true);
            $novoVencimento = date('d/m/Y', strtotime('+30 days'));

            return [
                'success' => true,
                'pago' => true,
                'instancia' => $nomeInstancia,
                'fatura_id' => $fatura?->id,
                'codigo_fatura' => $fatura?->codigo_fatura,
                'transacao_id' => $fatura?->gateway_transaction_id ?: 'PIX-CONFIRMADO',
                'metodo_exclusivo' => 'pix',
                'ambiente' => $ambienteAtual,
                'novo_vencimento' => $novoVencimento,
                'status_instancia' => 'ativa',
                'retorno_automatico' => true,
                'retorno_url' => url('/admin/whatsapp'),
                'message' => "Pagamento PIX da instância '{$nomeInstancia}' confirmado com sucesso no Mercado Pago do Desenvolvedor! Instância ativada até {$novoVencimento} e cobranças via WhatsApp liberadas.",
            ];
        }

        return [
            'success' => true,
            'pago' => false,
            'instancia' => $nomeInstancia,
            'fatura_id' => $fatura?->id,
            'codigo_fatura' => $fatura?->codigo_fatura,
            'transacao_id' => $fatura?->gateway_transaction_id,
            'metodo_exclusivo' => 'pix',
            'ambiente' => $ambienteAtual,
            'retorno_automatico' => true,
            'message' => 'Aguardando compensação do PIX no Mercado Pago. Assim que aprovado, o checkout retornará e ativará a instância automaticamente.',
        ];
    }

    /**
     * Processa a ativação/reativação automática de uma instância ao receber pagamento de fatura
     */
    public static function processarPagamentoFaturaInstancia(Fatura $fatura): ?string
    {
        $nomeInstancia = null;

        // 1. Tenta extrair da descrição: "Foco 7 - Servidor Dedicado WhatsApp Cloud (nome)"
        if (preg_match('/WhatsApp Cloud \((.*?)\)/i', $fatura->descricao ?? '', $m)) {
            $nomeInstancia = trim($m[1]);
        }

        // 2. Tenta extrair do payload ou código de fatura
        if (empty($nomeInstancia) && isset($fatura->webhook_payload)) {
            $payload = is_array($fatura->webhook_payload) ? $fatura->webhook_payload : json_decode($fatura->webhook_payload, true);
            $nomeInstancia = $payload['wpp_instancia'] ?? null;
        }

        // 3. Tenta localizar nas instâncias extras existentes por correspondência
        if (empty($nomeInstancia)) {
            $extras = self::getInstanciasExtras();
            foreach ($extras as $item) {
                $instName = $item['nome'] ?? '';
                if (! empty($instName) && (str_contains($fatura->descricao ?? '', $instName) || str_contains($fatura->codigo_fatura ?? '', $instName))) {
                    $nomeInstancia = $instName;
                    break;
                }
            }
        }

        // 4. Se o usuário da fatura for um licenciado com instância
        if (empty($nomeInstancia) && $fatura->user_id) {
            $licenca = Licenca::where('user_id', $fatura->user_id)->first();
            if ($licenca && ! empty($licenca->dados_servidor['whatsapp']['instancia'])) {
                $nomeInstancia = $licenca->dados_servidor['whatsapp']['instancia'];
            }
        }

        if (! empty($nomeInstancia)) {
            self::reativarInstancia($nomeInstancia, true);
            Log::info("Evolution API: Instância '{$nomeInstancia}' ativada/renovada automaticamente via pagamento da fatura #{$fatura->codigo_fatura}.");

            try {
                Notificacao::create([
                    'titulo' => 'Instância WhatsApp Ativada Automaticamente',
                    'mensagem' => "A mensalidade da instância '{$nomeInstancia}' foi paga e o serviço foi ativado automaticamente por mais 30 dias.",
                    'tipo' => 'financeiro',
                    'cor' => 'success',
                    'icone' => 'fab fa-whatsapp',
                    'link_acao' => route('admin.whatsapp.index'),
                ]);
            } catch (\Throwable $e) {
            }

            return $nomeInstancia;
        }

        return null;
    }

    /**
     * Suspende uma instância por inadimplência/vencimento e corta a conexão no servidor
     * Suporta todas as instâncias do painel, inclusive a instância atual de configuração
     */
    public static function suspenderInstancia(string $nomeInstancia, string $motivo = 'Vencimento da mensalidade de R$ 60,00'): bool
    {
        $extras = self::getInstanciasExtras();
        $atualizou = false;

        foreach ($extras as &$item) {
            if (strtolower($item['nome'] ?? '') === strtolower($nomeInstancia)) {
                $item['status'] = 'suspensa';
                $item['motivo_suspensao'] = $motivo;
                $item['suspensa_em'] = now()->format('Y-m-d H:i:s');
                $atualizou = true;
                break;
            }
        }

        // Suporte para suspender também a instância atual usada para configuração
        if (! $atualizou && (strtolower($nomeInstancia) === strtolower(self::getInstanceName()) || strtolower($nomeInstancia) === 'foco7')) {
            CmsSetting::set('evolution_master_status_cobranca', 'suspensa', 'whatsapp');
            $atualizou = true;
        } else {
            if ($atualizou) {
                CmsSetting::set('evolution_instancias_extras', $extras, 'whatsapp', 'json');
            }
        }

        if ($atualizou) {
            Log::info("Evolution API: Instância '{$nomeInstancia}' suspensa. Motivo: {$motivo}");

            // Tenta desconectar a instância no servidor da Evolution API para cortar os envios
            try {
                self::desconectarInstanciaEspecifica($nomeInstancia);
            } catch (\Throwable $e) {
                Log::warning("Falha ao desconectar instância suspensa '{$nomeInstancia}': ".$e->getMessage());
            }

            // Atualiza também na licença do licenciado se houver
            $licencas = Licenca::all();
            foreach ($licencas as $lic) {
                $dados = $lic->dados_servidor ?? [];
                if (($dados['whatsapp']['instancia'] ?? '') === $nomeInstancia) {
                    $dados['whatsapp']['status_cobranca'] = 'suspensa';
                    $dados['whatsapp']['ativo'] = false;
                    $lic->update(['dados_servidor' => $dados]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Reativa uma instância suspensa ou renova sua vigência
     * Prorroga a vigência sem perda de dias e restaura a conexão no servidor da Evolution Go imediatamente
     */
    public static function reativarInstancia(string $nomeInstancia, bool $prorrogar = true): bool
    {
        $extras = self::getInstanciasExtras();
        $atualizou = false;
        $novaDataVencimento = null;
        $tipoInstancia = null;
        $tokenInstancia = null;

        foreach ($extras as &$item) {
            if (strtolower($item['nome'] ?? '') === strtolower($nomeInstancia)) {
                $item['status'] = 'ativa';
                $item['motivo_suspensao'] = null;
                $item['suspensa_em'] = null;
                $item['ultimo_pagamento'] = now()->format('Y-m-d H:i:s');
                $tipoInstancia = $item['tipo'] ?? 'empresa';
                $tokenInstancia = $item['token'] ?? null;
                if ($prorrogar) {
                    $vencAtual = ! empty($item['data_vencimento']) ? $item['data_vencimento'] : date('Y-m-d');
                    // Prorrogação cumulativa inteligente: soma +30 dias a partir da data de vencimento futura caso não esteja vencida
                    $baseTimestamp = (strtotime($vencAtual) > time()) ? strtotime($vencAtual) : time();
                    $item['data_vencimento'] = date('Y-m-d', strtotime('+30 days', $baseTimestamp));
                    $novaDataVencimento = $item['data_vencimento'];
                }
                $atualizou = true;
                break;
            }
        }

        // Suporte para renovar/reativar também a instância de configuração (ex: foco7)
        if (! $atualizou && (strtolower($nomeInstancia) === strtolower(self::getInstanceName()) || strtolower($nomeInstancia) === 'foco7')) {
            CmsSetting::set('evolution_master_status_cobranca', 'ativa', 'whatsapp');
            if ($prorrogar) {
                $vencAtual = CmsSetting::get('evolution_master_vencimento', date('Y-m-d'));
                $baseTimestamp = (strtotime($vencAtual) > time()) ? strtotime($vencAtual) : time();
                $novaDataVencimento = date('Y-m-d', strtotime('+30 days', $baseTimestamp));
                CmsSetting::set('evolution_master_vencimento', $novaDataVencimento, 'whatsapp');
            }
            $atualizou = true;
        } elseif ($atualizou) {
            CmsSetting::set('evolution_instancias_extras', $extras, 'whatsapp', 'json');
        }

        if ($atualizou) {
            Log::info("Evolution API: Instância '{$nomeInstancia}' reativada com sucesso. Novo vencimento: {$novaDataVencimento}");

            // Se for instância própria do administrador/empresa (não foco7), vincula e ativa como instância oficial de cobranças do painel!
            if (in_array($tipoInstancia, ['propria_admin', 'empresa']) && strtolower($nomeInstancia) !== 'foco7') {
                CmsSetting::set('evolution_admin_instancia_propria', $nomeInstancia, 'whatsapp');
                // Se a instância ativa atual ainda for 'foco7' (a de configuração), alterna automaticamente para a instância própria paga do admin
                if (strtolower(self::getInstanceName()) === 'foco7') {
                    CmsSetting::set('evolution_instance', $nomeInstancia, 'whatsapp');
                    if (! empty($tokenInstancia)) {
                        CmsSetting::set('evolution_apikey', $tokenInstancia, 'whatsapp');
                    }
                }
            }

            // Tenta reconectar a instância na Evolution API Go imediatamente para garantir serviço online
            try {
                self::conectarInstanciaEspecifica($nomeInstancia);
            } catch (\Throwable $e) {
                Log::warning("Falha ao restabelecer conexão da instância {$nomeInstancia}: ".$e->getMessage());
            }

            // Dispara notificação de confirmação no WhatsApp do responsável
            try {
                self::enviarConfirmacaoRenovacaoInstancia($nomeInstancia, $novaDataVencimento);
            } catch (\Throwable $e) {
                Log::warning("Falha ao enviar mensagem de confirmação para {$nomeInstancia}: ".$e->getMessage());
            }

            // Atualiza na licença do licenciado se houver
            $licencas = Licenca::all();
            foreach ($licencas as $lic) {
                $dados = $lic->dados_servidor ?? [];
                if (($dados['whatsapp']['instancia'] ?? '') === $nomeInstancia) {
                    $dados['whatsapp']['status_cobranca'] = 'ativa';
                    $dados['whatsapp']['ativo'] = true;
                    if ($prorrogar && $novaDataVencimento) {
                        $dados['whatsapp']['data_vencimento'] = $novaDataVencimento;
                    }
                    $lic->update(['dados_servidor' => $dados]);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Envia mensagem no WhatsApp do responsável confirmando a renovação e ativação por +30 dias
     */
    public static function enviarConfirmacaoRenovacaoInstancia(string $nomeInstancia, ?string $novoVencimento = null): bool
    {
        $extras = self::getInstanciasExtras();
        $inst = null;
        foreach ($extras as $item) {
            if (($item['nome'] ?? '') === $nomeInstancia) {
                $inst = $item;
                break;
            }
        }

        $telefone = $inst['telefone_responsavel'] ?? null;
        $nomeResp = $inst['nome_responsavel'] ?? null;

        if (empty($telefone) && ! empty($inst['licenciado_id'])) {
            $licenca = Licenca::find($inst['licenciado_id']);
            $telefone = $licenca?->user?->telefone;
            $nomeResp = $licenca?->user?->name;
        }
        if (empty($telefone)) {
            $telefone = CmsSetting::get('evolution_notificacao_telefone', CmsSetting::get('whatsapp_suporte', ''));
        }

        $telefoneLimpo = self::formatarNumeroWhatsapp($telefone);
        if (empty($telefoneLimpo)) {
            return false;
        }

        $vencFmt = ! empty($novoVencimento) ? date('d/m/Y', strtotime($novoVencimento)) : date('d/m/Y', strtotime('+30 days'));

        $msg = "✅ *Pagamento Confirmado com Sucesso!*\n\n"
            .'Olá, *'.($nomeResp ?: 'Parceiro Foco 7')."*!\n\n"
            ."Confirmamos o recebimento da mensalidade da sua instância dedicada WhatsApp *{$nomeInstancia}*.\n\n"
            ."🗓️ *Novo Vencimento:* {$vencFmt} (+30 dias)\n"
            ."⚡ *Status do Serviço:* Ativo e Conectado\n\n"
            .'Sua operação continua funcionando normalmente sem interrupções. Agradecemos pela confiança e parceria!';

        try {
            self::enviarTexto($telefoneLimpo, $msg);

            return true;
        } catch (\Throwable $e) {
            Log::warning("Falha ao enviar confirmação de renovação da instância {$nomeInstancia}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Inicia a conexão de uma instância específica no cluster Evolution Go
     */
    public static function conectarInstanciaEspecifica(string $instName, ?string $token = null): array
    {
        $tok = $token ?: self::obterTokenParaInstancia($instName);
        $payload = [
            'webhookUrl' => url('/webhook/evolution'),
            'immediate' => true,
            'subscribe' => ['MESSAGE', 'SEND_MESSAGE', 'CONNECTION', 'QRCODE', 'READ_RECEIPT'],
        ];

        $res = self::request('POST', 'instance/connect', $payload, false, 15, $tok);
        if (! $res['success']) {
            return self::request('GET', "instance/connect/{$instName}", [], false, 15, $tok);
        }

        return $res;
    }

    /**
     * Atualiza um campo específico de uma instância na lista de extras
     */
    public static function atualizarInstanciaCampo(string $nomeInstancia, string $campo, $valor): bool
    {
        $extras = self::getInstanciasExtras();
        $atualizou = false;
        foreach ($extras as &$item) {
            if (($item['nome'] ?? '') === $nomeInstancia) {
                $item[$campo] = $valor;
                $atualizou = true;
                break;
            }
        }
        if ($atualizou) {
            CmsSetting::set('evolution_instancias_extras', $extras, 'whatsapp', 'json');
        }

        return $atualizou;
    }

    /**
     * Varre todas as instâncias e dispara cobrança preventiva via WhatsApp com PIX do Mercado Pago
     * Roda diariamente às 08:00 no Cron ou sob demanda pelo painel
     */
    public static function processarCobrancasRecorrentesInstancias(): array
    {
        $extras = self::getInstanciasExtras();
        $processadas = 0;
        $cobrancasEnviadas = 0;
        $hoje = date('Y-m-d');

        foreach ($extras as $item) {
            $nome = $item['nome'] ?? '';
            $status = $item['status'] ?? 'ativa';
            $venc = $item['data_vencimento'] ?? '';
            $processadas++;

            if (empty($nome) || empty($venc)) {
                continue;
            }

            // Calcula dias restantes para o vencimento
            $diasRestantes = (int) round((strtotime($venc) - strtotime('today')) / 86400);

            // Dispara cobrança se faltar 3 dias, 1 dia, vencer hoje ou vencer ontem (dentro da carência)
            $deveCobrar = in_array($diasRestantes, [3, 2, 1, 0, -1]);

            if ($deveCobrar) {
                // Checa se já enviou lembrete hoje para não inundar o WhatsApp
                if (($item['ultimo_lembrete_enviado'] ?? '') === $hoje) {
                    continue;
                }

                // 1. Gera ou recupera a cobrança do Mercado Pago
                $cobranca = self::gerarCobrancaPixInstancia($nome);
                if (empty($cobranca['pix_copia_cola'])) {
                    continue;
                }

                // 2. Identifica o telefone e nome do responsável
                $telefone = $item['telefone_responsavel'] ?? null;
                $nomeResp = $item['nome_responsavel'] ?? null;

                if (empty($telefone) && ! empty($item['licenciado_id'])) {
                    $licenca = Licenca::find($item['licenciado_id']);
                    $telefone = $licenca?->user?->telefone;
                    $nomeResp = $licenca?->user?->name;
                }
                if (empty($telefone)) {
                    $telefone = CmsSetting::get('evolution_notificacao_telefone', CmsSetting::get('whatsapp_suporte', ''));
                }

                $telefoneLimpo = self::formatarNumeroWhatsapp($telefone);
                if (empty($telefoneLimpo)) {
                    continue;
                }

                $hash = $item['hash_pagamento'] ?? md5($nome.'wpp_sec');
                $linkDireto = url("/pagar/instancia/{$hash}");
                $valorFmt = number_format($item['valor_recorrente'] ?? 60.00, 2, ',', '.');
                $dataVencFmt = date('d/m/Y', strtotime($venc));

                // 3. Monta a mensagem contextual humanizada
                if ($diasRestantes > 0) {
                    $msg = "🔔 *Lembrete de Renovação - Instância WhatsApp*\n\n"
                        .'Olá, *'.($nomeResp ?: 'Parceiro Foco 7')."*!\n\n"
                        ."A mensalidade da sua instância dedicada *{$nome}* vence em *{$dataVencFmt}* (daqui a {$diasRestantes} dias).\n\n"
                        ."💰 *Valor:* R$ {$valorFmt}\n\n"
                        ."Pague via PIX Copia e Cola para manter sua conexão 100% ativa sem qualquer risco de interrupção:\n\n"
                        ."```{$cobranca['pix_copia_cola']}```\n\n"
                        ."📱 *Ou acesse o link de pagamento direto com QR Code:*\n"
                        ."{$linkDireto}\n\n"
                        .'_A confirmação e renovação acontecem em poucos segundos após o pagamento._';
                } elseif ($diasRestantes === 0) {
                    $msg = "🚨 *Vencimento Hoje - Instância WhatsApp*\n\n"
                        .'Olá, *'.($nomeResp ?: 'Parceiro Foco 7')."*!\n\n"
                        ."A mensalidade da sua instância dedicada *{$nome}* vence **HOJE** ({$dataVencFmt}).\n\n"
                        ."💰 *Valor:* R$ {$valorFmt}\n\n"
                        ."Efetue o pagamento agora para que seus envios automáticos continuem funcionando normalmente:\n\n"
                        ."```{$cobranca['pix_copia_cola']}```\n\n"
                        ."📱 *Pagar agora via QR Code:*\n"
                        ."{$linkDireto}\n\n"
                        .'_Ao pagar, a renovação de +30 dias é ativada em segundos._';
                } else {
                    $msg = "⚠️ *Aviso de Tolerância - Instância WhatsApp*\n\n"
                        .'Olá, *'.($nomeResp ?: 'Parceiro Foco 7')."*!\n\n"
                        ."A mensalidade da sua instância *{$nome}* venceu ontem. O serviço ainda está operando sob tolerância de carência de 24 horas.\n\n"
                        ."Regularize agora via PIX para não ter sua conexão interrompida:\n\n"
                        ."```{$cobranca['pix_copia_cola']}```\n\n"
                        ."📱 *Link de Pagamento Imediato:*\n"
                        ."{$linkDireto}";
                }

                // 4. Dispara mensagem via WhatsApp
                try {
                    self::enviarTexto($telefoneLimpo, $msg);
                    $cobrancasEnviadas++;

                    // Atualiza data do último lembrete
                    self::atualizarInstanciaCampo($nome, 'ultimo_lembrete_enviado', $hoje);
                } catch (\Throwable $e) {
                    Log::warning("Falha ao enviar cobrança WhatsApp para {$telefoneLimpo} da instância {$nome}: ".$e->getMessage());
                }
            }
        }

        return [
            'processadas' => $processadas,
            'cobrancas_enviadas' => $cobrancasEnviadas,
            'mensagem' => "Varredura preventiva concluída. {$cobrancasEnviadas} cobrança(s) enviada(s) via WhatsApp.",
        ];
    }

    /**
     * Sincroniza pagamentos pendentes de instâncias diretamente no Mercado Pago via API
     * Roda a cada 10 minutos no Cron para garantir tolerância máxima caso o webhook atrase
     */
    public static function sincronizarPagamentosPendentesInstancias(): array
    {
        $faturasPendentes = Fatura::where('status', 'pendente')
            ->where(function ($q) {
                $q->where('descricao', 'like', '%WhatsApp%')
                    ->orWhere('codigo_fatura', 'like', 'FAT-WPP-%');
            })
            ->whereNotNull('gateway_transaction_id')
            ->get();

        $atualizadas = 0;
        $mpAccessToken = self::getMercadoPagoAccessTokenParaInstancias();

        if (empty($mpAccessToken) || $faturasPendentes->isEmpty()) {
            return ['faturas_verificadas' => 0, 'atualizadas' => 0];
        }

        foreach ($faturasPendentes as $fat) {
            try {
                $res = Http::withToken($mpAccessToken)
                    ->timeout(6)
                    ->get("https://api.mercadopago.com/v1/payments/{$fat->gateway_transaction_id}");

                if ($res->successful()) {
                    $status = strtolower((string) ($res->json('status') ?? ''));
                    if (in_array($status, ['approved', 'pago'])) {
                        $fat->update([
                            'status' => 'pago',
                            'data_pagamento' => now(),
                            'webhook_payload' => $res->json(),
                        ]);

                        self::processarPagamentoFaturaInstancia($fat);
                        $atualizadas++;
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        return [
            'faturas_verificadas' => $faturasPendentes->count(),
            'atualizadas' => $atualizadas,
        ];
    }

    /**
     * Varre todas as instâncias e suspende automaticamente aquelas com mensalidade vencida (após carência de 24h)
     */
    public static function processarSuspensaoInstanciasVencidas(): array
    {
        if (! self::isSuspensaoAutomaticaAtiva()) {
            return ['processadas' => 0, 'suspensas' => 0, 'mensagem' => 'Suspensão automática está desativada no painel.'];
        }

        $extras = self::getInstanciasExtras();
        $suspensas = 0;
        // Tolerância de 24 horas: só suspende se tiver vencido há mais de 1 dia
        $toleranciaLimite = date('Y-m-d', strtotime('-1 day'));

        foreach ($extras as $item) {
            $nome = $item['nome'] ?? '';
            $status = $item['status'] ?? 'ativa';
            $venc = $item['data_vencimento'] ?? '';

            if (! empty($nome) && ! empty($venc) && $venc < $toleranciaLimite && $status !== 'suspensa') {
                self::suspenderInstancia($nome, "Vencimento em {$venc} (tolerância de carência esgotada)");
                $suspensas++;
            }
        }

        return [
            'processadas' => count($extras),
            'suspensas' => $suspensas,
            'mensagem' => "Varredura concluída. {$suspensas} instância(s) suspensa(s) por inadimplência após carência de 24 horas.",
        ];
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
     * Localiza o token real de uma instância específica (em extras ou diretamente no servidor)
     */
    public static function obterTokenParaInstancia(string $instName): string
    {
        if (strtolower($instName) === strtolower(self::getInstanceName())) {
            return self::getInstanceApiKey();
        }

        foreach (self::getInstanciasExtras() as $extra) {
            if (strtolower((string) ($extra['nome'] ?? '')) === strtolower($instName) && ! empty($extra['token'])) {
                return (string) $extra['token'];
            }
        }

        $allRes = self::listarInstancias();
        if ($allRes['success']) {
            foreach (self::extrairListaInstancias($allRes['data'] ?? []) as $item) {
                $nome = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? ''));
                if (strtolower((string) $nome) === strtolower($instName) && ! empty($item['token'])) {
                    return (string) $item['token'];
                }
            }
        }

        return self::getInstanceApiKey();
    }

    /**
     * Localiza o ID UUID de uma instância no servidor Evolution Go v3.0
     */
    public static function obterUuidInstancia(string $instName): ?string
    {
        $allRes = self::listarInstancias();
        if ($allRes['success']) {
            foreach (self::extrairListaInstancias($allRes['data'] ?? []) as $item) {
                $nome = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? ''));
                if (strtolower((string) $nome) === strtolower($instName) && ! empty($item['id'])) {
                    return (string) $item['id'];
                }
            }
        }

        return null;
    }

    /**
     * Extrai de forma resiliente o array de instâncias retornado por GET /instance/all
     */
    public static function extrairListaInstancias($raw): array
    {
        if (! is_array($raw)) {
            return [];
        }
        if (isset($raw['data']) && is_array($raw['data'])) {
            return $raw['data'];
        }
        if (isset($raw['instances']) && is_array($raw['instances'])) {
            return $raw['instances'];
        }
        if (isset($raw['response']) && is_array($raw['response'])) {
            return $raw['response'];
        }
        if (array_is_list($raw)) {
            return $raw;
        }

        return [];
    }

    /**
     * Obtém o QR Code para uma instância específica (ex: do licenciado)
     */
    public static function obterQrCodeParaInstancia(string $instName, ?string $token = null): array
    {
        $tok = $token ?: self::obterTokenParaInstancia($instName);

        // 1. Tenta GET /instance/qr (padrão oficial Swagger Evolution Go v3)
        $resQr = self::request('GET', 'instance/qr', [], false, 15, $tok);
        if ($resQr['success']) {
            return $resQr;
        }

        // 2. Tenta GET /instance/connect/{instance}
        $res = self::request('GET', "instance/connect/{$instName}", [], false, 15, $tok);
        if ($res['success']) {
            return $res;
        }

        // 3. Tenta GET /instance/qr/{instance}
        return self::request('GET', "instance/qr/{$instName}", [], false, 15, $tok);
    }

    /**
     * Obtém o status de conexão de uma instância específica
     */
    public static function obterStatusInstancia(string $instName, ?string $token = null): array
    {
        // 1. Verifica na listagem geral do cluster
        $allRes = self::listarInstancias();
        if ($allRes['success']) {
            foreach (self::extrairListaInstancias($allRes['data'] ?? []) as $item) {
                $nome = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? ''));
                if (strtolower((string) $nome) === strtolower($instName)) {
                    $isConnected = (($item['connected'] ?? false) === true)
                        || (($item['Connected'] ?? false) === true)
                        || in_array(strtolower((string) ($item['status'] ?? ($item['state'] ?? ''))), ['open', 'connected', 'conectado']);

                    return [
                        'success' => true,
                        'status' => 200,
                        'conectado' => $isConnected,
                        'data' => [
                            'state' => $isConnected ? 'open' : 'close',
                            'status' => $isConnected ? 'open' : 'close',
                            'connected' => $isConnected,
                            'instance' => $item,
                        ],
                    ];
                }
            }
        }

        $tok = $token ?: self::obterTokenParaInstancia($instName);
        $res = self::request('GET', 'instance/status', [], false, 10, $tok);
        if ($res['success']) {
            return $res;
        }

        return self::request('GET', "instance/connectionState/{$instName}", [], false, 10, $tok);
    }

    /**
     * Desconecta uma instância específica
     */
    public static function desconectarInstanciaEspecifica(string $instName, ?string $token = null): array
    {
        $tok = $token ?: self::obterTokenParaInstancia($instName);
        $res = self::request('POST', 'instance/disconnect', ['instance' => $instName], false, 10, $tok);
        if (! $res['success']) {
            return self::request('POST', "instance/disconnect/{$instName}", [], false, 10, $tok);
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
            return (string) env('EVOLUTION_GLOBAL_APIKEY', 'sk_'.'live_EIrvaAwernkdiQUaCsdfDNLeDaalMhyM5O');
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
     * Retorna o Token / API Key específico da instância (sincronizado com Evolution Go v3.0)
     */
    public static function getInstanceApiKey(): string
    {
        $key = trim((string) CmsSetting::get('evolution_apikey', ''));
        if (empty($key) || str_contains($key, '•') || $key === '8|dZL05XOD8uKOfBasILJhMfKQrexshwV1q63cZsU976d9eba7') {
            return 'b9cd5808-3500-40dd-8835-81ee016fa97b';
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
                $lastResult = $res;
                break;
            }

            if ($lastResult === null || ($res['status'] === 405 && str_contains($errMsg, 'not supported for route'))) {
                if ($lastResult === null) {
                    $lastResult = $res;
                }
            } else {
                $lastResult = $res;
            }
        }

        // Auto-Recuperação de Autenticação para Instâncias:
        // Se uma chamada de instância retornar 401/403 ("not authorized"), sincroniza o token real
        // da instância via Global API Key (GET /instance/all) e repete a requisição com o token oficial!
        $lastStatus = $lastResult['status'] ?? 0;
        $lastErr = strtolower((string) ($lastResult['error'] ?? ''));
        $isAuthFailure = ($lastStatus === 401 || $lastStatus === 403)
            || str_contains($lastErr, 'unauthorized')
            || str_contains($lastErr, 'not authorized')
            || str_contains($lastErr, 'invalid apikey')
            || str_contains($lastErr, 'forbidden');

        if (! $isGlobal && $isAuthFailure && ! empty(self::getGlobalApiKey())) {
            $instName = self::getInstanceName();
            $tokenReal = self::sincronizarTokenInstancia($instName);

            if (! empty($tokenReal) && $tokenReal !== $key) {
                $headersSynced = self::getHeaders(false, $tokenReal);
                foreach ($candidatos as $caminho) {
                    $url = $baseUrl.'/'.ltrim($caminho, '/');
                    $retrySynced = self::executeSingleCall($method, $url, $data, $headersSynced, $timeout);
                    if ($retrySynced['success']) {
                        return $retrySynced;
                    }
                }
            }

            // Fallback secundário com Global API Key caso o servidor aceite chave administrativa na rota
            $globalKey = self::getGlobalApiKey();
            $headersGlobalFallback = [
                'apikey' => $globalKey,
                'Authorization' => 'Bearer '.$globalKey,
                'Global-Api-Key' => $globalKey,
                'globalapikey' => $globalKey,
                'adminkey' => $globalKey,
                'instance' => $instName,
                'Instance-Name' => $instName,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            foreach ($candidatos as $caminho) {
                $url = $baseUrl.'/'.ltrim($caminho, '/');
                $retryRes = self::executeSingleCall($method, $url, $data, $headersGlobalFallback, $timeout);
                if ($retryRes['success']) {
                    return $retryRes;
                }
            }
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
     * Endpoint oficial Swagger: POST /instance/create
     */
    public static function criarInstancia(?string $nome = null, ?string $token = null, array $advancedSettings = []): array
    {
        $instance = $nome ?: self::getInstanceName();
        $apiKey = $token ?: (strtolower($instance) === strtolower(self::getInstanceName()) ? self::getInstanceApiKey() : (string) Str::uuid());

        $payload = [
            'name' => $instance,
            'token' => $apiKey,
        ];

        $res = self::request('POST', 'instance/create', $payload, true);

        // Se a instância já existir no servidor, recupera os dados reais dela e trata graciosamente como sucesso
        if (! $res['success']) {
            $err = strtolower($res['error'] ?? '');
            if (str_contains($err, 'already exists') || str_contains($err, 'já existe') || str_contains($err, 'in use') || str_contains($err, 'duplicate')) {
                Log::info("Evolution API: Instância '{$instance}' já existia no servidor.");
                $tokenReal = self::obterTokenParaInstancia($instance);

                return [
                    'success' => true,
                    'status' => 200,
                    'data' => [
                        'name' => $instance,
                        'token' => $tokenReal,
                        'instance' => ['name' => $instance, 'token' => $tokenReal],
                        'already_existed' => true,
                    ],
                    'error' => null,
                ];
            }
        } elseif (isset($res['data']['data']) && is_array($res['data']['data'])) {
            $res['data'] = array_merge($res['data'], $res['data']['data']);
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
        if (! $res['success'] || empty($res['data'])) {
            $alt = self::request('GET', 'instance/fetchInstances', [], true);
            if ($alt['success'] && ! empty($alt['data'])) {
                return $alt;
            }
        }

        return $res;
    }

    /**
     * Obtém informações detalhadas de uma instância
     * Endpoint Swagger Evolution Go: GET /instance/info/{instanceId} (exige UUID)
     */
    public static function obterInfoInstancia(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $uuid = self::obterUuidInstancia($inst) ?: $inst;

        $res = self::request('GET', "instance/info/{$uuid}", [], true);
        if ($res['success'] && isset($res['data']['data']) && is_array($res['data']['data'])) {
            $res['data'] = $res['data']['data'];
        }

        return $res;
    }

    /**
     * Deleta permanentemente uma instância da Evolution API
     * Endpoint Swagger Evolution Go: DELETE /instance/delete/{instanceId} (exige UUID)
     */
    public static function deletarInstancia(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $uuid = self::obterUuidInstancia($inst) ?: $inst;

        return self::request('DELETE', "instance/delete/{$uuid}", [], true);
    }

    /**
     * Força a reconexão da instância com um número específico
     */
    public static function forcarReconexao(?string $numero = null, ?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $tok = self::obterTokenParaInstancia($inst);
        $payload = [
            'phone' => $numero ? self::formatarNumeroWhatsapp($numero) : '',
            'immediate' => true,
            'webhookUrl' => url('/webhook/evolution'),
            'subscribe' => ['MESSAGE', 'SEND_MESSAGE', 'CONNECTION', 'QRCODE'],
        ];

        return self::request('POST', 'instance/connect', $payload, false, 15, $tok);
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

        // 1. Consulta a listagem geral do cluster via GET /instance/all
        $allRes = self::listarInstancias();
        if ($allRes['success'] && ! empty($allRes['data'])) {
            $itens = self::extrairListaInstancias($allRes['data']);
            foreach ($itens as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $nome = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? null));
                if (strtolower((string) $nome) === strtolower($instName)) {
                    $token = $item['token'] ?? ($item['instance']['token'] ?? ($item['apikey'] ?? null));
                    if (! empty($token)) {
                        if (strtolower($instName) === strtolower(self::getInstanceName())) {
                            CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                        }
                        Log::info("Evolution API: Token sincronizado via /instance/all para '{$instName}'.");

                        return (string) $token;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Inicia a conexão da instância e configura o webhook se fornecido (com Auto-Recuperação e Criação Inteligente)
     * Endpoint oficial Swagger Evolution Go: POST /instance/connect
     */
    public static function conectar(?string $webhookUrl = null, array $subscribe = ['ALL']): array
    {
        $instName = self::getInstanceName();

        // 0. Verifica em tempo real se a instância já está conectada no cluster Evolution Go
        $statusAtual = self::obterStatus();
        if ($statusAtual['success'] && ($statusAtual['conectado'] ?? false)) {
            return [
                'success' => true,
                'conectado' => true,
                'status' => 200,
                'message' => "A instância '{$instName}' já está conectada e operando normalmente no cluster!",
                'data' => $statusAtual['data'] ?? [],
                'owner' => $statusAtual['owner'] ?? null,
            ];
        }

        // Garante que o token local está sincronizado com o cluster antes de conectar
        $tokenReal = self::sincronizarTokenInstancia($instName) ?: self::getInstanceApiKey();

        $webhook = $webhookUrl ?: url('/webhook/evolution');
        $eventos = (! empty($subscribe) && $subscribe !== ['ALL'])
            ? $subscribe
            : ['MESSAGE', 'SEND_MESSAGE', 'READ_RECEIPT', 'PRESENCE', 'HISTORY_SYNC', 'CHAT_PRESENCE', 'CALL', 'CONNECTION', 'LABEL', 'CONTACT', 'GROUP', 'NEWSLETTER', 'QRCODE'];

        $payload = [
            'phone' => '',
            'immediate' => true,
            'webhookUrl' => $webhook,
            'subscribe' => $eventos,
        ];

        // 1. POST /instance/connect (Swagger Evolution Go v3.0)
        $res = self::request('POST', 'instance/connect', $payload, false, 25, $tokenReal);
        if ($res['success']) {
            // Tenta buscar o QR Code imediatamente após iniciar a sessão
            $qrRes = self::obterQrCode();
            if ($qrRes['success'] && ! empty($qrRes['qrcode'])) {
                $res['data']['qrcode'] = ['base64' => $qrRes['qrcode']];
                $res['data']['base64'] = $qrRes['qrcode'];
            }

            return $res;
        }

        // 2. Se a instância ainda não existe no servidor, cria-a via POST /instance/create
        $createRes = self::criarInstancia($instName, $tokenReal);
        if ($createRes['success']) {
            $createData = $createRes['data'] ?? [];
            $novoToken = $createData['token'] ?? ($createData['data']['token'] ?? ($createData['instance']['token'] ?? $tokenReal));
            if (! empty($novoToken)) {
                CmsSetting::set('evolution_apikey', $novoToken, 'whatsapp');
            }

            $retryConnect = self::request('POST', 'instance/connect', $payload, false, 25, $novoToken);
            $qrRes = self::obterQrCode();
            if ($qrRes['success'] && ! empty($qrRes['qrcode'])) {
                $retryConnect['data']['qrcode'] = ['base64' => $qrRes['qrcode']];
                $retryConnect['data']['base64'] = $qrRes['qrcode'];
            }

            if ($retryConnect['success']) {
                return $retryConnect;
            }
        }

        return $res;
    }

    /**
     * Retorna o status de conexão da instância (open, connected, connecting, close, etc.)
     * Consulta prioritária via cluster Evolution Go v3.0 (GET /instance/all e GET /instance/status)
     */
    public static function obterStatus(): array
    {
        $instName = self::getInstanceName();

        // 1. Consulta prioritária: Lista de instâncias do cluster via Global API Key (GET /instance/all)
        // Mesmo endpoint oficial utilizado pelo painel https://api.kdkhost.com.br/manager/instances
        $allRes = self::listarInstancias();
        if ($allRes['success'] && ! empty($allRes['data'])) {
            $itens = self::extrairListaInstancias($allRes['data']);

            foreach ($itens as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $nomeItem = $item['name'] ?? ($item['instance']['name'] ?? ($item['instanceName'] ?? null));
                if (strtolower((string) $nomeItem) === strtolower($instName)) {
                    $statusRaw = strtolower((string) ($item['status'] ?? ($item['instance']['status'] ?? ($item['state'] ?? ($item['instance']['state'] ?? '')))));
                    $jidRaw = (string) ($item['jid'] ?? ($item['owner'] ?? ($item['instance']['owner'] ?? ($item['number'] ?? ''))));
                    $owner = str_replace(['@s.whatsapp.net', '@c.us'], '', $jidRaw);
                    $token = $item['token'] ?? ($item['instance']['token'] ?? ($item['apikey'] ?? null));

                    if (! empty($token)) {
                        CmsSetting::set('evolution_apikey', $token, 'whatsapp');
                    }

                    $isConnected = in_array($statusRaw, ['open', 'connected', 'conectado'])
                        || (($item['connected'] ?? false) === true)
                        || (($item['Connected'] ?? false) === true)
                        || (($item['instance']['connected'] ?? false) === true);

                    if ($isConnected) {
                        CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');
                        if (! empty($owner)) {
                            CmsSetting::set('evolution_connected_number', $owner, 'whatsapp');
                        }

                        return [
                            'success' => true,
                            'status' => 200,
                            'conectado' => true,
                            'estado' => 'conectado',
                            'owner' => $owner,
                            'data' => [
                                'state' => 'open',
                                'status' => 'open',
                                'connected' => true,
                                'owner' => $owner,
                                'jid' => $jidRaw,
                                'token' => $token,
                                'id' => $item['id'] ?? null,
                                'instance' => [
                                    'id' => $item['id'] ?? null,
                                    'name' => $instName,
                                    'state' => 'open',
                                    'status' => 'open',
                                    'connected' => true,
                                    'owner' => $owner,
                                ],
                            ],
                            'error' => null,
                        ];
                    }

                    // Se não está conectado mas tem QR Code gerado na instância, está conectando
                    if (! empty($item['qrcode']) || in_array($statusRaw, ['connecting', 'conectando'])) {
                        return [
                            'success' => true,
                            'status' => 200,
                            'conectado' => false,
                            'estado' => 'conectando',
                            'owner' => $owner,
                            'qrcode' => $item['qrcode'] ?? null,
                            'data' => $item,
                            'error' => null,
                        ];
                    }

                    CmsSetting::set('evolution_status_cache', 'desconectado', 'whatsapp');

                    return [
                        'success' => true,
                        'status' => 200,
                        'conectado' => false,
                        'estado' => 'desconectado',
                        'owner' => $owner,
                        'data' => $item,
                        'error' => null,
                    ];
                }
            }
        }

        // 2. Fallback direto: GET /instance/status com o token da instância
        // Resposta oficial Evolution Go: {"data":{"Connected":true,"LoggedIn":true,"Name":"GMOP"},"message":"success"}
        $res = self::request('GET', 'instance/status', [], false, 10);
        if ($res['success']) {
            $raw = $res['data'] ?? [];
            $inner = (isset($raw['data']) && is_array($raw['data'])) ? $raw['data'] : $raw;
            $stateStr = strtolower((string) ($inner['state'] ?? ($inner['status'] ?? ($inner['instance']['state'] ?? ''))));

            $isConnected = (($inner['Connected'] ?? false) === true)
                || (($inner['LoggedIn'] ?? false) === true)
                || (($inner['connected'] ?? false) === true)
                || in_array($stateStr, ['open', 'connected', 'conectado']);

            if ($isConnected) {
                CmsSetting::set('evolution_status_cache', 'conectado', 'whatsapp');
                $res['conectado'] = true;
                $res['estado'] = 'conectado';
                $res['data'] = [
                    'state' => 'open',
                    'status' => 'open',
                    'connected' => true,
                    'name' => $inner['Name'] ?? ($inner['name'] ?? $instName),
                ];

                return $res;
            }
        }

        return $allRes['success'] ? $allRes : $res;
    }

    /**
     * Retorna o QR Code atual para autenticação
     * Endpoint oficial Swagger Evolution Go: GET /instance/qr
     */
    public static function obterQrCode(): array
    {
        $instName = self::getInstanceName();

        // 1. Tenta GET /instance/qr (padrão oficial Swagger Evolution Go v3.0)
        $res = self::request('GET', 'instance/qr', [], false, 15);
        if ($res['success']) {
            $d = $res['data']['data'] ?? ($res['data'] ?? []);
            $qr = $d['Qrcode'] ?? ($d['qrcode']['base64'] ?? ($d['qrcode'] ?? ($d['base64'] ?? null)));
            if (is_string($qr) && ! empty($qr)) {
                $res['qrcode'] = $qr;
                $res['data']['base64'] = $qr;
                $res['data']['qrcode'] = ['base64' => $qr];

                return $res;
            }
        }

        // Se o erro for "session already logged in", a instância já está conectada!
        if (str_contains(strtolower((string) ($res['error'] ?? '')), 'already logged in')) {
            return [
                'success' => true,
                'status' => 200,
                'conectado' => true,
                'qrcode' => null,
                'data' => ['connected' => true, 'state' => 'open'],
                'error' => null,
            ];
        }

        // 2. Verifica se o QR Code está presente em GET /instance/all
        $allRes = self::listarInstancias();
        if ($allRes['success']) {
            foreach (self::extrairListaInstancias($allRes['data'] ?? []) as $item) {
                $nome = $item['name'] ?? '';
                if (strtolower((string) $nome) === strtolower($instName) && ! empty($item['qrcode'])) {
                    return [
                        'success' => true,
                        'status' => 200,
                        'qrcode' => $item['qrcode'],
                        'data' => [
                            'base64' => $item['qrcode'],
                            'qrcode' => ['base64' => $item['qrcode']],
                        ],
                        'error' => null,
                    ];
                }
            }
        }

        return $res;
    }

    /**
     * Solicita código de pareamento numérico (Pairing Code de 8 dígitos)
     * Endpoint oficial Swagger Evolution Go: POST /instance/pair
     */
    public static function solicitarPairingCode(string $telefone, ?string $instance = null): array
    {
        $num = self::formatarNumeroWhatsapp($telefone);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de telefone inválido para pareamento.'];
        }

        $inst = $instance ?: self::getInstanceName();
        $tok = self::obterTokenParaInstancia($inst);

        $payload = [
            'phone' => $num,
            'number' => $num,
            'subscribe' => ['MESSAGE', 'SEND_MESSAGE', 'CONNECTION', 'QRCODE'],
        ];

        $res = self::request('POST', 'instance/pair', $payload, false, 20, $tok);
        if ($res['success'] && isset($res['data']['data']) && is_array($res['data']['data'])) {
            $res['data'] = array_merge($res['data'], $res['data']['data']);
        }

        return $res;
    }

    /**
     * Desconecta a instância temporariamente
     * Endpoint oficial Swagger Evolution Go: POST /instance/disconnect
     */
    public static function desconectar(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $tok = self::obterTokenParaInstancia($inst);

        $res = self::request('POST', 'instance/disconnect', [], false, 15, $tok);
        if ($res['success']) {
            return $res;
        }

        return self::request('DELETE', 'instance/logout', [], false, 15, $tok);
    }

    /**
     * Reconecta a instância ao WhatsApp
     * Endpoint oficial Swagger Evolution Go: POST /instance/connect
     */
    public static function reconectar(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $tok = self::obterTokenParaInstancia($inst);

        $payload = [
            'immediate' => true,
            'webhookUrl' => url('/webhook/evolution'),
            'subscribe' => ['MESSAGE', 'SEND_MESSAGE', 'CONNECTION', 'QRCODE', 'READ_RECEIPT'],
        ];

        return self::request('POST', 'instance/connect', $payload, false, 20, $tok);
    }

    /**
     * Faz logout da instância (remove a sessão do WhatsApp)
     * Endpoint oficial Swagger Evolution Go: DELETE /instance/logout
     */
    public static function logout(?string $instance = null): array
    {
        $inst = $instance ?: self::getInstanceName();
        $tok = self::obterTokenParaInstancia($inst);

        $res = self::request('DELETE', 'instance/logout', [], false, 15, $tok);
        if ($res['success']) {
            return $res;
        }

        return self::request('POST', 'instance/disconnect', [], false, 15, $tok);
    }

    // =========================================================================
    // 3. SEND MESSAGE OPERATIONS (Autenticação com apikey / Token da Instância)
    // =========================================================================

    /**
     * Envia mensagem de texto simples
     * Endpoint oficial Swagger Evolution Go: POST /send/text
     */
    public static function enviarTexto(string $numero, string $texto, int $delay = 0): array
    {
        $num = self::formatarNumeroWhatsapp($numero);
        if (empty($num)) {
            return ['success' => false, 'error' => 'Número de WhatsApp inválido para envio.'];
        }

        $inst = self::getInstanceName();

        // 1. Endpoint padrão oficial Evolution API Go v3 (Swagger /send/text): POST /send/text
        $payloadGo = [
            'number' => $num,
            'text' => $texto,
        ];
        if ($delay > 0) {
            $payloadGo['delay'] = (int) $delay;
        }

        $res = self::request('POST', 'send/text', $payloadGo, false);
        if ($res['success']) {
            return $res;
        }

        // 2. Fallback para distribuições alternativas caso a rota retorne 404/405
        $errMsg = strtolower((string) ($res['error'] ?? ''));
        $isRouteMismatch = ($res['status'] === 404 || $res['status'] === 405)
            || str_contains($errMsg, 'not supported for route')
            || str_contains($errMsg, 'route not found')
            || str_contains($errMsg, 'cannot post');

        if ($isRouteMismatch) {
            $payloadNode = [
                'number' => $num,
                'text' => $texto,
                'textMessage' => ['text' => $texto],
            ];
            $resNode = self::request('POST', "message/sendText/{$inst}", $payloadNode, false);
            if ($resNode['success']) {
                return $resNode;
            }
        }

        return $res;
    }

    /**
     * Envia mensagem com pagamento PIX (com fallback automático para POST /send/text no Evolution Go whatsmeow)
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

        $merchant = $merchantName ?: self::getMerchantName();
        $chave = $pixKey ?: self::getPixKey();
        $tipoChave = strtoupper($keyType ?: self::getPixKeyType());
        $rodape = $footerText ?: 'Pagamento seguro via PIX - Foco 7';

        $payload = [
            'number' => $num,
            'headerTitle' => $headerTitle,
            'bodyText' => $bodyText,
            'merchantName' => $merchant,
            'pixKey' => $chave,
            'keyType' => $tipoChave,
            'footerText' => $rodape,
        ];

        if ($delay > 0) {
            $payload['delay'] = (int) $delay;
        }

        $res = self::request('POST', 'send/pix', $payload, false);
        if ($res['success']) {
            return $res;
        }

        // Fallback automático para POST /send/text (suportado universalmente no Evolution Go whatsmeow)
        $textoFallback = "💠 *{$headerTitle}*\n\n"
            ."{$bodyText}\n\n"
            ."🏢 *Recebedor:* {$merchant}\n"
            ."🔑 *Chave PIX ({$tipoChave}):*\n"
            ."```{$chave}```\n\n"
            ."_{$rodape}_";

        return self::enviarTexto($num, $textoFallback, $delay);
    }

    /**
     * Envia mensagem interativa com botão Review and Pay (com fallback automático para POST /send/text)
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

        $merchant = $merchantName ?: self::getMerchantName();
        $chave = $pixKey ?: self::getPixKey();
        $tipoChave = strtoupper($keyType ?: self::getPixKeyType());

        $btn = [
            'type' => 'review_and_pay',
            'displayText' => 'Revisar e Pagar',
            'currency' => 'BRL',
            'name' => $merchant,
            'keyType' => $tipoChave,
            'key' => $chave,
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
            $payload['delay'] = (int) $delay;
        }

        $res = self::request('POST', 'send/button', $payload, false);
        if ($res['success']) {
            return $res;
        }

        // Fallback automático para POST /send/text
        $textoFallback = "🧾 *{$titulo}*\n\n"
            .($descricao ? "{$descricao}\n\n" : '')
            ."🏢 *Recebedor:* {$merchant}\n"
            ."🔑 *Chave PIX ({$tipoChave}):*\n"
            ."```{$chave}```"
            .($footer ? "\n\n_{$footer}_" : '');

        return self::enviarTexto($num, $textoFallback, $delay);
    }

    /**
     * Envia mensagem interativa com botões de ação (com fallback automático para POST /send/text ou /send/link)
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
            $payload['delay'] = (int) $delay;
        }

        $res = self::request('POST', 'send/button', $payload, false);
        if ($res['success']) {
            return $res;
        }

        // Fallback automático inteligente para POST /send/text formatando os links e códigos de cópia
        $linhasExtras = [];
        foreach ($botoes as $b) {
            $label = $b['displayText'] ?? ($b['title'] ?? 'Ação');
            if (! empty($b['url']) && ! str_contains($descricao, $b['url'])) {
                $linhasExtras[] = "🔗 *{$label}:* {$b['url']}";
            } elseif (! empty($b['copyCode']) && ! str_contains($descricao, $b['copyCode'])) {
                $linhasExtras[] = "📋 *{$label}:*\n```{$b['copyCode']}```";
            }
        }

        $blocoBotoes = ! empty($linhasExtras) ? "\n\n".implode("\n\n", $linhasExtras) : '';
        $textoFallback = "*{$titulo}*\n\n{$descricao}{$blocoBotoes}".(! empty($footer) ? "\n\n_{$footer}_" : '');

        return self::enviarTexto($num, $textoFallback, $delay);
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

        $textoFormatado = str_replace(array_keys($map), array_values($map), $template);

        return self::converterHtmlParaWhatsapp($textoFormatado);
    }

    /**
     * Converte HTML gerado por editores WYSIWYG (Summernote) para a sintaxe nativa do WhatsApp
     * Transforma <b>/<strong> em *negrito*, <i>/<em> em _itálico_, listas em tópicos e <p>/<br> em quebras de linha
     */
    public static function converterHtmlParaWhatsapp(string $texto): string
    {
        if (empty($texto)) {
            return '';
        }

        // Se o texto não possui tags HTML, retorna direto
        if (! str_contains($texto, '<') || ! str_contains($texto, '>')) {
            return trim($texto);
        }

        // Converte entidades HTML
        $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normaliza quebras de linha HTML
        $texto = preg_replace('/<br\s*\/?>/i', "\n", $texto);
        $texto = preg_replace('/<\/p>/i', "\n\n", $texto);
        $texto = preg_replace('/<\/div>/i', "\n", $texto);
        $texto = preg_replace('/<\/h[1-6]>/i', "\n\n", $texto);
        $texto = preg_replace('/<\/li>/i', "\n", $texto);
        $texto = preg_replace('/<li[^>]*>/i', '• ', $texto);

        // Converte formatações de texto para a sintaxe do WhatsApp
        $texto = preg_replace('/<(?:b|strong)[^>]*>(.*?)<\/(?:b|strong)>/is', '*$1*', $texto);
        $texto = preg_replace('/<(?:i|em)[^>]*>(.*?)<\/(?:i|em)>/is', '_$1_', $texto);
        $texto = preg_replace('/<(?:s|strike|del)[^>]*>(.*?)<\/(?:s|strike|del)>/is', '~$1~', $texto);
        $texto = preg_replace('/<(?:code|pre)[^>]*>(.*?)<\/(?:code|pre)>/is', '```$1```', $texto);

        // Converte links: <a href="url">texto</a> -> texto (url)
        $texto = preg_replace_callback('/<a\s+[^>]*href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/is', function ($matches) {
            $url = trim($matches[1]);
            $anchor = trim(strip_tags($matches[2]));
            if (empty($anchor) || $anchor === $url) {
                return $url;
            }

            return "{$anchor}: {$url}";
        }, $texto);

        // Remove quaisquer tags HTML restantes
        $texto = strip_tags($texto);

        // Normaliza quebras de linha excessivas (mais de 2 consecutivas)
        $texto = preg_replace("/\n{3,}/", "\n\n", $texto);

        return trim($texto);
    }

    /**
     * Dispara a cobrança de uma fatura para o WhatsApp do cliente
     * Exige que o Administrador tenha criado e ativado sua própria instância via Mercado Pago (Desenvolvedor).
     * Fora isso, bloqueia o envio por WhatsApp e orienta/aciona cobrança exclusivamente via E-mail.
     */
    public static function enviarCobrancaFatura(Fatura $fatura, string $etapa = 'manual', array $encargosExtras = []): array
    {
        // 1. Verificações de segurança e permissão de instância própria do Administrador
        if (! self::isAtivo()) {
            return [
                'success' => false,
                'canal_fallback' => 'email',
                'message' => 'A integração de cobranças via WhatsApp (Evolution API) está desativada no painel. Utilize cobrança via E-mail.',
            ];
        }

        $permissaoWpp = self::verificarPermissaoCobrancaWhatsapp();
        if (! ($permissaoWpp['permitido'] ?? false)) {
            return [
                'success' => false,
                'bloqueado_instancia_propria' => true,
                'motivo_bloqueio' => $permissaoWpp['motivo'] ?? 'sem_instancia_propria',
                'canal_fallback' => 'email',
                'message' => $permissaoWpp['message'] ?? 'Para realizar cobranças via WhatsApp, crie e ative sua própria instância no painel. Enquanto isso, as cobranças são enviadas exclusivamente via E-mail.',
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
