<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depoimento extends Model
{
    use HasFactory;

    protected $table = 'depoimentos';

    protected $fillable = [
        'nome_cliente',
        'cidade_uf',
        'veiculo',
        'foto',
        'texto',
        'estrelas',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'estrelas' => 'integer',
        'ordem' => 'integer',
        'ativo' => 'boolean',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true)->orderBy('ordem', 'asc');
    }
}
