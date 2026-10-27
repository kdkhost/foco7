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
        'tipo_pagamento',
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
            'confirmado' => '<span class="status-badge-pill status-badge-pago"><span class="status-dot"></span><i class="fas fa-check-circle"></i><span>Confirmado</span></span>',
            'concluido' => '<span class="status-badge-pill status-badge-concluido"><span class="status-dot"></span><i class="fas fa-flag-checkered"></i><span>Concluído</span></span>',
            'cancelado' => '<span class="status-badge-pill status-badge-cancelado"><span class="status-dot"></span><i class="fas fa-ban"></i><span>Cancelado</span></span>',
            default => '<span class="status-badge-pill status-badge-pendente"><span class="status-dot"></span><i class="fas fa-clock"></i><span>Pendente</span></span>',
        };
    }

    public function getTipoPagamentoBadgeAttribute(): string
    {
        return match ($this->tipo_pagamento) {
            'local' => '<span class="badge bg-secondary"><i class="fas fa-hand-holding-dollar me-1"></i> No Local</span>',
            default => '<span class="badge bg-success"><i class="fas fa-bolt me-1"></i> Online</span>',
        };
    }
}
