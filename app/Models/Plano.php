<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plano extends Model
{
    use HasFactory;

    protected $table = 'planos';

    protected $fillable = [
        'nome',
        'slug',
        'categoria',
        'subtitulo',
        'preco_mensal',
        'taxa_adesao',
        'recursos',
        'icone',
        'texto_botao',
        'destaque',
        'tag_destaque',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'recursos' => 'array',
        'destaque' => 'boolean',
        'ativo' => 'boolean',
        'preco_mensal' => 'decimal:2',
        'taxa_adesao' => 'decimal:2',
        'ordem' => 'integer',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true)->orderBy('ordem', 'asc');
    }

    public function scopeDestaques($query)
    {
        return $query->where('ativo', true)->where('destaque', true)->orderBy('ordem', 'asc');
    }

    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }

    public function getPrecoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->preco_mensal, 2, ',', '.');
    }

    public function getTaxaAdesaoFormatadaAttribute(): string
    {
        return 'R$ ' . number_format($this->taxa_adesao, 2, ',', '.');
    }
}
