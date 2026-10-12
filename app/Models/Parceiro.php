<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parceiro extends Model
{
    use HasFactory;

    protected $table = 'parceiros';

    protected $fillable = [
        'nome',
        'logo',
        'link',
        'descricao',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    protected $appends = [
        'logo_url',
        'categoria',
    ];

    /**
     * Scope para parceiros ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para ordenar pela coluna ordem e nome
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Retorna a URL completa da logo
     */
    public function getLogoUrlAttribute(): string
    {
        if (empty($this->logo)) {
            return asset('img/favicon.png');
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return asset(ltrim($this->logo, '/'));
    }

    /**
     * Alias para categoria / descrição
     */
    public function getCategoriaAttribute(): ?string
    {
        return $this->descricao;
    }

    public function setCategoriaAttribute($value): void
    {
        $this->descricao = $value;
    }
}
