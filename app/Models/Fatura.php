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
        'codigo_carne',
        'numero_parcela',
        'total_parcelas',
        'descricao',
        'valor',
        'taxa_repassada',
        'valor_original',
        'gateway',
        'metodo_pagamento',
        'gateway_transaction_id',
        'efi_carnet_id',
        'qr_code_pix',
        'qr_code_pix_url',
        'linha_digitavel_boleto',
        'url_boleto',
        'link_pdf_carne',
        'status',
        'data_vencimento',
        'data_pagamento',
        'webhook_payload',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'taxa_repassada' => 'decimal:2',
        'valor_original' => 'decimal:2',
        'numero_parcela' => 'integer',
        'total_parcelas' => 'integer',
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

    public function isCarne(): bool
    {
        return !empty($this->codigo_carne) || $this->metodo_pagamento === 'carne' || str_starts_with($this->codigo_fatura, 'CARNE-');
    }

    public function getTaxaRepassadaFormatadaAttribute(): string
    {
        return 'R$ ' . number_format($this->taxa_repassada ?? 0, 2, ',', '.');
    }

    public function getValorOriginalFormatadoAttribute(): string
    {
        $val = $this->valor_original ?? ($this->valor - ($this->taxa_repassada ?? 0));
        return 'R$ ' . number_format($val, 2, ',', '.');
    }
}
