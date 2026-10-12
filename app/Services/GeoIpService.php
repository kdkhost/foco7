<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CmsSetting;

class GeoIpService
{
    /**
     * Países oficiais de todo o continente americano (América do Sul, Norte, Central e Caribe)
     */
    public const PAISES_AMERICAS = [
        // América do Sul
        'BR' => 'Brasil',
        'AR' => 'Argentina',
        'CL' => 'Chile',
        'CO' => 'Colômbia',
        'PE' => 'Peru',
        'UY' => 'Uruguai',
        'PY' => 'Paraguai',
        'BO' => 'Bolívia',
        'EC' => 'Equador',
        'VE' => 'Venezuela',
        'GY' => 'Guiana',
        'SR' => 'Suriname',
        'GF' => 'Guiana Francesa',

        // América do Norte
        'US' => 'Estados Unidos',
        'CA' => 'Canadá',
        'MX' => 'México',
        'GL' => 'Groenlândia',
        'BM' => 'Bermudas',
        'PM' => 'Saint Pierre e Miquelon',

        // América Central
        'CR' => 'Costa Rica',
        'PA' => 'Panamá',
        'GT' => 'Guatemala',
        'HN' => 'Honduras',
        'SV' => 'El Salvador',
        'NI' => 'Nicarágua',
        'BZ' => 'Belize',

        // Caribe e Antilhas
        'CU' => 'Cuba',
        'DO' => 'República Dominicana',
        'HT' => 'Haiti',
        'JM' => 'Jamaica',
        'PR' => 'Porto Rico',
        'TT' => 'Trinidad e Tobago',
        'BB' => 'Barbados',
        'BS' => 'Bahamas',
        'LC' => 'Santa Lúcia',
        'CW' => 'Curaçao',
        'AW' => 'Aruba',
        'VI' => 'Ilhas Virgens Americanas',
        'VG' => 'Ilhas Virgens Britânicas',
        'KY' => 'Ilhas Cayman',
        'AG' => 'Antígua e Barbuda',
        'DM' => 'Dominica',
        'GD' => 'Granada',
        'KN' => 'São Cristóvão e Névis',
        'VC' => 'São Vicente e Granadinas',
        'SX' => 'Sint Maarten',
        'MQ' => 'Martinica',
        'GP' => 'Guadalupe',
    ];

    /**
     * Exemplos de países europeus comuns para demonstração e bloqueio de testes
     */
    public const PAISES_EUROPA = [
        'DE' => 'Alemanha',
        'FR' => 'França',
        'GB' => 'Reino Unido',
        'IT' => 'Itália',
        'ES' => 'Espanha',
        'RU' => 'Rússia',
        'NL' => 'Holanda',
        'PL' => 'Polônia',
        'UA' => 'Ucrânia',
        'PT' => 'Portugal',
        'SE' => 'Suécia',
        'CH' => 'Suíça',
        'RO' => 'Romênia',
        'CZ' => 'República Tcheca',
    ];

    /**
     * Detecta o país e continente do IP
     */
    public static function detectar(?string $ip = null, ?Request $request = null): array
    {
        $ip = $ip ?: ($request ? $request->ip() : request()->ip());
        $ip = trim((string)$ip);

        // 1. IP Local / Rede Privada
        if (self::isIpLocal($ip)) {
            return [
                'ip' => $ip,
                'pais_codigo' => 'BR',
                'pais_nome' => 'Brasil (Ambiente Local / Rede Segura)',
                'continente_codigo' => 'SA',
                'continente_nome' => 'América do Sul',
                'is_americas' => true,
                'metodo' => 'rede_local',
            ];
        }

        // 2. Cabeçalhos diretos de proxy reverso (Cloudflare / LiteSpeed / cPanel)
        if ($request) {
            $headerCountry = strtoupper((string)(
                $request->header('CF-IPCountry') ?:
                $request->header('X-Country-Code') ?:
                $request->server('GEOIP_COUNTRY_CODE') ?:
                $request->server('HTTP_CF_IPCOUNTRY')
            ));

            if (!empty($headerCountry) && strlen($headerCountry) === 2 && $headerCountry !== 'XX' && $headerCountry !== 'T1') {
                $isAmericas = isset(self::PAISES_AMERICAS[$headerCountry]);
                return [
                    'ip' => $ip,
                    'pais_codigo' => $headerCountry,
                    'pais_nome' => self::PAISES_AMERICAS[$headerCountry] ?? (self::PAISES_EUROPA[$headerCountry] ?? $headerCountry),
                    'continente_codigo' => $isAmericas ? 'AM' : 'EU/OUTRO',
                    'continente_nome' => $isAmericas ? 'Américas' : 'Fora das Américas',
                    'is_americas' => $isAmericas,
                    'metodo' => 'header_proxy',
                ];
            }
        }

        // 3. Cache de 24 horas por IP
        return Cache::remember("geoip_detalhes_{$ip}", 86400, function () use ($ip) {
            return self::consultarApiExterna($ip);
        });
    }

    /**
     * Consulta API leve de GeoIP com timeout curto e fallback resiliente
     */
    protected static function consultarApiExterna(string $ip): array
    {
        try {
            $response = Http::timeout(1.8)->get("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,continent,continentCode");

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'success') {
                    $code = strtoupper((string)($data['countryCode'] ?? ''));
                    $contCode = strtoupper((string)($data['continentCode'] ?? ''));

                    $isAmericas = ($contCode === 'SA' || $contCode === 'NA' || isset(self::PAISES_AMERICAS[$code]));

                    return [
                        'ip' => $ip,
                        'pais_codigo' => $code,
                        'pais_nome' => $data['country'] ?? (self::PAISES_AMERICAS[$code] ?? $code),
                        'continente_codigo' => $contCode,
                        'continente_nome' => $data['continent'] ?? ($isAmericas ? 'Américas' : 'Outro'),
                        'is_americas' => $isAmericas,
                        'metodo' => 'api_externa',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("[GeoIpService] Falha na consulta de GeoIP para {$ip}: " . $e->getMessage());
        }

        // Fallback resiliente: não bloqueia em caso de falha de conexão da API
        return [
            'ip' => $ip,
            'pais_codigo' => 'BR',
            'pais_nome' => 'Indeterminado (Acesso Liberado por Resiliência)',
            'continente_codigo' => 'SA',
            'continente_nome' => 'Américas',
            'is_americas' => true,
            'metodo' => 'fallback_seguro',
        ];
    }

    /**
     * Verifica se o IP está em lista branca configurada no CMS
     */
    public static function ipEstaLiberado(string $ip): bool
    {
        if (self::isIpLocal($ip)) {
            return true;
        }

        $ipsConfig = (string) CmsSetting::get('geoblock_ips_liberados', '');
        if (empty($ipsConfig)) {
            return false;
        }

        $listaIps = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $ipsConfig)));

        foreach ($listaIps as $ipLiberado) {
            if ($ipLiberado === $ip) {
                return true;
            }

            // Suporte a curinga (ex: 177.192.*)
            if (str_contains($ipLiberado, '*')) {
                $padrao = str_replace(['.', '*'], ['\.', '.*'], $ipLiberado);
                if (preg_match("/^{$padrao}$/", $ip)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Avalia se a requisição deve ser bloqueada
     */
    public static function deveBloquear(Request $request): array
    {
        // 1. Checa se o bloqueio está ativo
        $ativo = CmsSetting::get('geoblock_ativo', '1') == '1';
        if (!$ativo) {
            return ['bloquear' => false, 'motivo' => 'modulo_desativado'];
        }

        // 2. Rotas isentas (Webhooks de Gateways, instalador e bypass com token)
        if ($request->is('webhook/*') || $request->is('instalar*') || $request->is('manutencao/bypass/*')) {
            return ['bloquear' => false, 'motivo' => 'rota_isenta'];
        }

        // Bypass por token de emergência na URL se configurado
        $tokenBypass = CmsSetting::get('manutencao_token', 'foco7_vip_bypass_2026');
        if ($request->has('geo_bypass') && $request->input('geo_bypass') === $tokenBypass) {
            return ['bloquear' => false, 'motivo' => 'token_bypass'];
        }

        $ip = $request->ip();

        // 3. IP na lista branca
        if (self::ipEstaLiberado($ip)) {
            return ['bloquear' => false, 'motivo' => 'ip_whitelist', 'ip' => $ip];
        }

        // 4. Detecção de localização
        $geo = self::detectar($ip, $request);

        if (!$geo['is_americas']) {
            return [
                'bloquear' => true,
                'motivo' => 'regiao_nao_autorizada',
                'dados' => $geo,
            ];
        }

        return ['bloquear' => false, 'motivo' => 'autorizado_americas', 'dados' => $geo];
    }

    /**
     * Simula localização para testes no painel administrativo
     */
    public static function simular(string $termo): array
    {
        $termo = trim(strtoupper($termo));

        // Se for um código de país de 2 letras
        if (strlen($termo) === 2) {
            $isAmericas = isset(self::PAISES_AMERICAS[$termo]);
            return [
                'tipo' => 'pais',
                'codigo' => $termo,
                'nome' => self::PAISES_AMERICAS[$termo] ?? (self::PAISES_EUROPA[$termo] ?? $termo),
                'is_americas' => $isAmericas,
                'resultado' => $isAmericas ? 'LIBERADO (Pertence às Américas)' : 'BLOQUEADO (Fora das Américas / Ameaça Regional)',
                'cor' => $isAmericas ? 'success' : 'danger',
            ];
        }

        // Se for um IP
        $geo = self::detectar($termo);
        return [
            'tipo' => 'ip',
            'ip' => $termo,
            'pais' => $geo['pais_nome'],
            'codigo' => $geo['pais_codigo'],
            'continente' => $geo['continente_nome'],
            'is_americas' => $geo['is_americas'],
            'resultado' => $geo['is_americas'] ? 'LIBERADO (Pertence às Américas)' : 'BLOQUEADO (Origem fora das Américas)',
            'cor' => $geo['is_americas'] ? 'success' : 'danger',
        ];
    }

    /**
     * Verifica se é IP local ou privado
     */
    public static function isIpLocal(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
