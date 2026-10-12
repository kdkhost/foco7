<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TwoFactorAuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TwoFactorAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_geracao_de_chave_secreta_base32(): void
    {
        $secret = TwoFactorAuthService::generateSecretKey(16);
        $this->assertEquals(16, strlen($secret));
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
    }

    public function test_calculo_e_validacao_de_codigo_totp(): void
    {
        $secret = TwoFactorAuthService::generateSecretKey(16);
        $code = TwoFactorAuthService::calculateCode($secret);

        $this->assertEquals(6, strlen($code));
        $this->assertTrue(ctype_digit($code));

        $valido = TwoFactorAuthService::verifyCode($secret, $code);
        $this->assertTrue($valido);

        $invalido = TwoFactorAuthService::verifyCode($secret, '000000');
        // Se por coincidência improvável calculateCode der '000000', testamos com inverso
        if ($code === '000000') {
            $invalido = TwoFactorAuthService::verifyCode($secret, '999999');
        }
        $this->assertFalse($invalido);
    }

    public function test_url_otpauth_e_qrcode(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $url = TwoFactorAuthService::getOtpAuthUrl('teste@foco7.com.br', $secret, 'Foco 7');

        $this->assertStringContainsString('otpauth://totp/', $url);
        $this->assertStringContainsString('secret=JBSWY3DPEHPK3PXP', $url);
        $this->assertStringContainsString('issuer=Foco%207', $url);

        $qrUrl = TwoFactorAuthService::getQrCodeImageUrl($url);
        $this->assertStringContainsString('create-qr-code', $qrUrl);
    }

    public function test_geracao_e_consumo_de_recovery_codes(): void
    {
        $codes = TwoFactorAuthService::generateRecoveryCodes(8);
        $this->assertCount(8, $codes);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}-[A-Z0-9]{4}$/', $codes[0]);

        $user = User::factory()->create([
            'two_factor_recovery_codes' => json_encode($codes),
            'two_factor_enabled' => true,
        ]);

        $primeiroCodigo = $codes[0];
        $sucesso = TwoFactorAuthService::verifyAndConsumeRecoveryCode($user, $primeiroCodigo);
        $this->assertTrue($sucesso);

        // Não pode consumir o mesmo código duas vezes
        $segundoUso = TwoFactorAuthService::verifyAndConsumeRecoveryCode($user->fresh(), $primeiroCodigo);
        $this->assertFalse($segundoUso);
    }
}
