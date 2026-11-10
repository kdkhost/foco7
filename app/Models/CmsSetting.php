<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class CmsSetting extends Model
{
    use HasFactory;

    protected $table = 'cms_settings';

    protected $fillable = [
        'chave',
        'valor',
        'grupo',
        'tipo',
        'descricao',
    ];

    /**
     * Chaves exclusivas do desenvolvedor com assinatura criptográfica anti-adulteração
     */
    protected static array $devProtectedKeys = [
        'desenvolvedor_nome',
        'desenvolvedor_whatsapp',
        'desenvolvedor_telegram',
        'desenvolvedor_link',
        'desenvolvedor_avatar',
        'dev_master_password',
    ];

    /**
     * Chaves protegidas que DEVEM ser criptografadas no banco de dados (AES-256)
     * para impedir qualquer alteração direta ou vazamento via phpMyAdmin / dump SQL.
     */
    protected static array $encryptedKeys = [
        'desenvolvedor_nome',
        'desenvolvedor_whatsapp',
        'desenvolvedor_telegram',
        'desenvolvedor_link',
        'desenvolvedor_avatar',
        'dev_master_password',
        // Credenciais Evolution API Go v3.0 (Criptografia total no banco de dados)
        'evolution_api_url',
        'evolution_global_apikey',
        'evolution_apikey',
        'evolution_instance',
        'evolution_pix_key',
        'evolution_webhook_secret',
    ];

    /**
     * Padrões oficiais imutáveis do desenvolvedor caso haja adulteração ou ausência no banco
     */
    protected static array $devDefaults = [
        'desenvolvedor_nome' => 'Marcelo Brad',
        'desenvolvedor_whatsapp' => '5521981325441',
        'desenvolvedor_telegram' => 'marcelobrad',
        'desenvolvedor_link' => 'https://wa.me/5521981325441',
        'desenvolvedor_avatar' => '/img/desenvolvedor-avatar.png',
        'dev_master_password' => 'Brad@2026',
    ];

    /**
     * Verifica se uma chave requer criptografia no banco de dados
     */
    public static function isEncryptedKey(string $chave): bool
    {
        return in_array($chave, self::$encryptedKeys, true);
    }

    /**
     * Verifica se uma chave é estritamente protegida com assinatura do desenvolvedor
     */
    public static function isDevProtectedKey(string $chave): bool
    {
        return in_array($chave, self::$devProtectedKeys, true);
    }

    /**
     * Gera assinatura de integridade do desenvolvedor
     */
    protected static function gerarAssinaturaDev(string $chave, string $valor): string
    {
        $appKey = (string) config('app.key', 'base64:foco7devmastersecretkey');

        return hash_hmac('sha256', $chave.'|'.$valor, $appKey.'#BradDevFoco7#2026');
    }

    /**
     * Criptografa o valor com payload assinado para proteção anti-adulteração
     */
    public static function encryptDevValue(string $chave, string $valor): string
    {
        $payload = json_encode([
            'v' => $valor,
            'k' => $chave,
            'ts' => time(),
            'sig' => self::gerarAssinaturaDev($chave, $valor),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return Crypt::encryptString($payload);
    }

    /**
     * Descriptografa e valida integridade.
     * Se houver tentativa de troca direta no banco (texto plano ou hash inválido),
     * a adulteração é descartada e o valor oficial do desenvolvedor Marcelo Brad é retornado.
     */
    public static function decryptDevValue(string $chave, mixed $rawValor, mixed $default = null): mixed
    {
        if ($rawValor === null || $rawValor === '') {
            return $default ?? (self::$devDefaults[$chave] ?? null);
        }

        try {
            $decrypted = Crypt::decryptString((string) $rawValor);
            $data = json_decode($decrypted, true);

            if (is_array($data) && isset($data['v'], $data['sig'])) {
                $expectedSig = self::gerarAssinaturaDev($chave, (string) $data['v']);
                if (hash_equals($expectedSig, (string) $data['sig'])) {
                    return $data['v'];
                }

                // Adulteração na assinatura detectada: rejeita e devolve padrão oficial
                return self::$devDefaults[$chave] ?? $default;
            }

            // String criptografada legada válida
            return $decrypted;
        } catch (\Throwable $e) {
            // Tentativa de troca direta no banco por texto puro ou chave adulterada:
            // Rejeita a adulteração e mantém Marcelo Brad
            return self::$devDefaults[$chave] ?? $default;
        }
    }

    /**
     * Recupera o valor de uma configuração com cache inteligente e descriptografia automática
     */
    public static function get(string $chave, mixed $default = null): mixed
    {
        try {
            $settings = Cache::remember('cms_settings_all', 60, function () {
                return self::all()->pluck('valor', 'chave')->toArray();
            });

            $valor = $settings[$chave] ?? null;

            if (self::isDevProtectedKey($chave)) {
                return self::decryptDevValue($chave, $valor, $default);
            }

            if (self::isEncryptedKey($chave)) {
                if ($valor === null || $valor === '') {
                    return $default;
                }
                try {
                    return Crypt::decryptString((string) $valor);
                } catch (\Throwable $e) {
                    // Fallback se o valor estiver em texto puro (ex: recém migrado ou configuração padrão)
                    return $valor;
                }
            }

            return ($valor === null || $valor === '') ? $default : $valor;
        } catch (\Throwable $e) {
            if (self::isDevProtectedKey($chave)) {
                return self::$devDefaults[$chave] ?? $default;
            }

            return $default;
        }
    }

    /**
     * Salva ou atualiza uma configuração com criptografia automática para campos protegidos
     */
    public static function set(string $chave, mixed $valor, string $grupo = 'geral', string $tipo = 'text', ?string $descricao = null): self
    {
        $valorFinal = is_array($valor) ? json_encode($valor) : (string) $valor;

        if (self::isDevProtectedKey($chave)) {
            $valorFinal = self::encryptDevValue($chave, $valorFinal);
            $grupo = 'desenvolvedor';
        } elseif (self::isEncryptedKey($chave)) {
            if ($valorFinal !== '') {
                $valorFinal = Crypt::encryptString($valorFinal);
            }
        }

        $setting = self::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => $valorFinal,
                'grupo' => $grupo,
                'tipo' => $tipo,
                'descricao' => $descricao,
            ]
        );

        Cache::forget('cms_settings_all');

        return $setting;
    }

    /**
     * Retorna todas as configurações de um grupo com descriptografia transparente
     */
    public static function porGrupo(string $grupo): array
    {
        try {
            $itens = self::where('grupo', $grupo)->pluck('valor', 'chave')->toArray();
            $resultado = [];
            foreach ($itens as $chave => $valor) {
                if (self::isDevProtectedKey($chave)) {
                    $resultado[$chave] = self::decryptDevValue($chave, $valor);
                } elseif (self::isEncryptedKey($chave)) {
                    if ($valor === null || $valor === '') {
                        $resultado[$chave] = '';
                    } else {
                        try {
                            $resultado[$chave] = Crypt::decryptString((string) $valor);
                        } catch (\Throwable $e) {
                            $resultado[$chave] = $valor;
                        }
                    }
                } else {
                    $resultado[$chave] = $valor;
                }
            }

            return $resultado;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Retorna a URL do logotipo oficial baseada no fundo (claro/branco ou escuro/preto)
     *
     * @param  string  $fundo  'escuro' | 'claro' | 'branco' | 'preto'
     */
    public static function getLogo(string $fundo = 'escuro'): string
    {
        if (in_array(strtolower($fundo), ['claro', 'branco', 'light', 'white'])) {
            $logoClaro = self::get('site_logo_clara', self::get('site_logo_light', '/img/logo-foco7-dark.png'));
            $cleanPath = parse_url($logoClaro, PHP_URL_PATH) ?: $logoClaro;
            if (empty($logoClaro) || ! file_exists(public_path(ltrim($cleanPath, '/\\')))) {
                return '/img/logo-foco7-dark.png';
            }

            return $logoClaro;
        }

        $logoEscuro = self::get('site_logo', '/img/logo-foco7.png');
        $cleanPath = parse_url($logoEscuro, PHP_URL_PATH) ?: $logoEscuro;
        if (empty($logoEscuro) || ! file_exists(public_path(ltrim($cleanPath, '/\\')))) {
            return '/img/logo-foco7.png';
        }

        return $logoEscuro;
    }
}
