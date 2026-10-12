<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Gera uma nova chave secreta aleatória Base32 para TOTP (16 caracteres)
     */
    public static function generateSecretKey(int $length = 16): string
    {
        $alphabet = self::BASE32_ALPHABET;
        $secret = '';
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $max)];
        }

        return $secret;
    }

    /**
     * Gera a URL compatível com Google Authenticator, Microsoft Authenticator e Authy
     */
    public static function getOtpAuthUrl(string $userEmail, string $secret, string $appName = 'Foco 7'): string
    {
        $label = rawurlencode($appName) . ':' . rawurlencode($userEmail);
        $issuer = rawurlencode($appName);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Retorna a URL da imagem de QR Code para visualização
     */
    public static function getQrCodeImageUrl(string $otpAuthUrl, int $size = 240): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($otpAuthUrl);
    }

    /**
     * Decodifica uma string Base32 em string binária
     */
    public static function base32Decode(string $base32): string
    {
        $base32 = strtoupper(trim($base32));
        $alphabet = self::BASE32_ALPHABET;
        $binary = '';

        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($base32); $i++) {
            $char = $base32[$i];
            if ($char === '=') {
                break;
            }

            $val = strpos($alphabet, $char);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $binary;
    }

    /**
     * Calcula o código TOTP de 6 dígitos para uma chave e um timestamp específico
     */
    public static function calculateCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = floor($timestamp / 30);

        $binarySecret = self::base32Decode($secret);

        // Converte o time slice para formato binário de 8 bytes em Big-Endian (64-bit integer)
        $binaryTime = pack('N*', 0) . pack('N*', $timeSlice);

        // Gera o HMAC-SHA1
        $hash = hash_hmac('sha1', $binaryTime, $binarySecret, true);

        // Pega o offset usando o último nibble
        $offset = ord(substr($hash, -1)) & 0x0F;

        // Extrai os 4 bytes correspondentes
        $part = substr($hash, $offset, 4);
        $value = unpack('N', $part)[1] & 0x7FFFFFFF;

        $pin = $value % 1000000;

        return str_pad((string)$pin, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Valida um código TOTP com tolerância de drift de relógio (janela de tempo de +/- 1 ciclo)
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $code = trim(preg_replace('/\s+/', '', $code));

        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::calculateCode($secret, $currentTime + ($i * 30));
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gera 8 códigos de recuperação de emergência (formato: XXXX-XXXX)
     * @return array<string>
     */
    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $p1 = strtoupper(Str::random(4));
            $p2 = strtoupper(Str::random(4));
            $codes[] = "{$p1}-{$p2}";
        }

        return $codes;
    }

    /**
     * Verifica e consome um código de recuperação de emergência do usuário
     */
    public static function verifyAndConsumeRecoveryCode(User $user, string $code): bool
    {
        $codeLimpo = strtoupper(trim(preg_replace('/[^a-zA-Z0-9]/', '', $code)));

        if (empty($codeLimpo) || empty($user->two_factor_recovery_codes)) {
            return false;
        }

        $codes = json_decode($user->two_factor_recovery_codes, true);
        if (!is_array($codes)) {
            return false;
        }

        foreach ($codes as $index => $item) {
            // Cada item pode ser uma string 'XXXX-XXXX' ou array ['code' => '...', 'used' => false]
            $storedCode = is_array($item) ? ($item['code'] ?? '') : $item;
            $storedLimpo = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $storedCode));
            $alreadyUsed = is_array($item) ? ($item['used'] ?? false) : false;

            if (!$alreadyUsed && hash_equals($storedLimpo, $codeLimpo)) {
                // Marca como usado
                if (is_array($item)) {
                    $codes[$index]['used'] = true;
                    $codes[$index]['used_at'] = now()->toDateTimeString();
                } else {
                    unset($codes[$index]);
                    $codes = array_values($codes);
                }

                $user->update([
                    'two_factor_recovery_codes' => json_encode($codes),
                ]);

                return true;
            }
        }

        return false;
    }
}
