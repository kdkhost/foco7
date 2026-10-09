<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoEstetica extends Model
{
    use HasFactory;

    protected $table = 'servicos_estetica';

    protected $fillable = [
        'nome',
        'slug',
        'subtitulo',
        'descricao',
        'icone',
        'imagem',
        'preco',
        'tempo_estimado',
        'recursos',
        'destaque',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'recursos' => 'array',
        'destaque' => 'boolean',
        'ativo' => 'boolean',
        'preco' => 'decimal:2',
    ];

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'servico_estetica_id');
    }

    public function getPrecoFormatadoAttribute(): string
    {
        if ($this->preco === null || $this->preco <= 0) {
            return 'Sob Consulta';
        }

        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }

    public function getImagemUrlAttribute(): string
    {
        if (!empty($this->imagem) && file_exists(public_path($this->imagem))) {
            return asset($this->imagem);
        }

        return asset('img/estetica-padrao.jpg');
    }
}
