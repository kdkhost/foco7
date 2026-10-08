<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerSlide extends Model
{
    use HasFactory;

    protected $table = 'banners_slides';

    protected $fillable = [
        'tipo',
        'badge',
        'titulo',
        'subtitulo',
        'texto_botao_1',
        'link_botao_1',
        'texto_botao_2',
        'link_botao_2',
        'imagem',
        'imagem_mobile',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true)->orderBy('ordem', 'asc');
    }

    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
