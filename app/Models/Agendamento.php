<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'user_id',
        'cliente_nome',
        'cliente_telefone',
        'cliente_email',
        'tipo_servico',
        'categoria',
        'servico_estetica_id',
        'valor',
        'veiculo_modelo',
        'veiculo_placa',
        'data_hora_inicio',
        'data_hora_fim',
        'endereco_atendimento',
        'cidade',
        'observacoes',
        'status',
        'cor_calendario',
    ];

    protected $casts = [
        'data_hora_inicio' => 'datetime',
        'data_hora_fim' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicoEstetica(): BelongsTo
    {
        return $this->belongsTo(ServicoEstetica::class, 'servico_estetica_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmado' => '<span class="badge bg-success">Confirmado</span>',
            'concluido' => '<span class="badge bg-primary">Concluído</span>',
            'cancelado' => '<span class="badge bg-danger">Cancelado</span>',
            default => '<span class="badge bg-warning text-dark">Pendente</span>',
        };
    }
}
