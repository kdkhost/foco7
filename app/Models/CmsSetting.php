<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
     * Recupera o valor de uma configuração com cache inteligente
     */
    public static function get(string $chave, mixed $default = null): mixed
    {
        try {
            $settings = Cache::rememberForever('cms_settings_all', function () {
                return self::all()->pluck('valor', 'chave')->toArray();
            });

            return $settings[$chave] ?? $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Salva ou atualiza uma configuração e limpa o cache
     */
    public static function set(string $chave, mixed $valor, string $grupo = 'geral', string $tipo = 'text', ?string $descricao = null): self
    {
        $setting = self::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => is_array($valor) ? json_encode($valor) : $valor,
                'grupo' => $grupo,
                'tipo' => $tipo,
                'descricao' => $descricao,
            ]
        );

        Cache::forget('cms_settings_all');

        return $setting;
    }

    /**
     * Retorna todas as configurações de um grupo
     */
    public static function porGrupo(string $grupo): array
    {
        try {
            return self::where('grupo', $grupo)->pluck('valor', 'chave')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
