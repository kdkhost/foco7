<?php

use App\Models\CmsSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migração e criptografa todos os registros do desenvolvedor no banco de dados.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cms_settings')) {
            return;
        }

        $campos = [
            'desenvolvedor_nome' => 'Marcelo Brad',
            'desenvolvedor_whatsapp' => '5521981325441',
            'desenvolvedor_telegram' => 'marcelobrad',
            'desenvolvedor_link' => 'https://wa.me/5521981325441',
            'desenvolvedor_avatar' => '/img/desenvolvedor-avatar.png',
            'dev_master_password' => 'Brad@2026',
        ];

        foreach ($campos as $chave => $default) {
            $registro = DB::table('cms_settings')->where('chave', $chave)->first();
            $valorAtual = $registro ? (string) $registro->valor : '';

            if ($valorAtual === '' || str_contains($valorAtual, '31970219') || str_contains($valorAtual, 'Carlos')) {
                $valorLimpo = $default;
            } else {
                try {
                    $decrypted = Crypt::decryptString($valorAtual);
                    $data = json_decode($decrypted, true);
                    $valorLimpo = is_array($data) && isset($data['v']) ? $data['v'] : $decrypted;
                } catch (Throwable $e) {
                    $valorLimpo = $valorAtual;
                }
            }

            CmsSetting::set($chave, $valorLimpo, 'desenvolvedor', 'text', 'Dado protegido do desenvolvedor');
        }
    }

    /**
     * Reverte a migração.
     */
    public function down(): void
    {
        //
    }
};
