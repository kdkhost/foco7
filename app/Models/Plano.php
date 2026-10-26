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
        'sob_consulta',
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
        'sob_consulta' => 'boolean',
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

    public function getIsSobConsultaAttribute(): bool
    {
        return (bool) ($this->sob_consulta ?? false);
    }

    public function getPrecoFormatadoAttribute(): string
    {
        if ($this->is_sob_consulta) {
            return 'CONSULTAR';
        }

        return 'R$ ' . number_format($this->preco_mensal, 2, ',', '.');
    }

    public function getTaxaAdesaoFormatadaAttribute(): string
    {
        return 'R$ ' . number_format($this->taxa_adesao, 2, ',', '.');
    }

    /**
     * Renderiza o ícone do plano de forma inteligente e universal (FontAwesome, Flaticon, Bootstrap Icons, SVG ou Imagem)
     */
    public function getIconeHtmlAttribute(): string
    {
        $icone = trim($this->icone ?? 'fa-shield-alt');

        // 1. Se for SVG inline
        if (str_starts_with($icone, '<svg')) {
            return $icone;
        }

        // 2. Se o usuário digitou uma tag <i> completa (ex: <i class="..."></i>)
        if (str_starts_with($icone, '<i ') && str_ends_with($icone, '</i>')) {
            return $icone;
        }

        // 3. Se for arquivo de imagem
        if (preg_match('/\.(png|svg|webp|jpg|jpeg)$/i', $icone)) {
            return '<img src="' . asset($icone) . '" alt="' . e($this->nome) . '" style="max-height: 40px; width: auto; object-fit: contain;">';
        }

        // 4. Se for Flaticon (fi fi-rr-..., fi fi-sr-..., flaticon-...)
        if (str_starts_with($icone, 'fi ') || str_starts_with($icone, 'fi-') || str_starts_with($icone, 'flaticon')) {
            return '<i class="' . e($icone) . '"></i>';
        }

        // 5. Se for Bootstrap Icons (bi bi-..., bi-...)
        if (str_starts_with($icone, 'bi ') || str_starts_with($icone, 'bi-')) {
            $class = str_starts_with($icone, 'bi ') ? $icone : 'bi ' . $icone;
            return '<i class="' . e($class) . '"></i>';
        }

        // 6. Se for FontAwesome completo (já contém fa-solid, fa-regular, fa-brands, fas, far, fab, etc.)
        if (preg_match('/^(fa[srbld]?\s+|fa-(solid|regular|brands|light|thin|duotone)\s+)/', $icone)) {
            return '<i class="' . e($icone) . '"></i>';
        }

        // 7. Se for FontAwesome parcial (ex: "fa-car" ou "car")
        if (!str_starts_with($icone, 'fa-')) {
            $icone = 'fa-' . $icone;
        }

        return '<i class="fas ' . e($icone) . '"></i>';
    }
}
