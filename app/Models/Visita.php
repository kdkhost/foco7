<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $fillable = [
        'ip_hash',
        'session_id',
        'url',
        'rota',
        'dispositivo',
        'navegador',
        'sistema_operacional',
        'origem',
        'data_visita',
    ];

    protected $casts = [
        'data_visita' => 'date',
    ];
}
