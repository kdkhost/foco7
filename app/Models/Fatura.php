<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fatura extends Model
{
    use HasFactory;

    protected $table = 'faturas';

    protected $fillable = [
        'user_id',
        'plano_id',
        'codigo_fatura',
        'descricao',
        'valor',
        'gateway',
        'metodo_pagamento',
        'gateway_transaction_id',
        'qr_code_pix',
        'qr_code_pix_url',
        'linha_digitavel_boleto',
        'url_boleto',
        'status',
        'data_vencimento',
        'data_pagamento',
        'webhook_payload',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'webhook_payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pago' => '<span class="badge bg-success">Pago</span>',
            'cancelado' => '<span class="badge bg-danger">Cancelado</span>',
            'estornado' => '<span class="badge bg-secondary">Estornado</span>',
            default => '<span class="badge bg-warning text-dark">Aguardando Pagamento</span>',
        };
    }
}
