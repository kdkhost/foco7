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
        'valor_multa',
        'valor_juros',
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
        'ultimo_lembrete_enviado',
        'data_ultimo_lembrete',
        'total_lembretes_enviados',
        'data_vencimento',
        'data_pagamento',
        'webhook_payload',
        'historico_cobranca',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'taxa_repassada' => 'decimal:2',
        'valor_original' => 'decimal:2',
        'valor_multa' => 'decimal:2',
        'valor_juros' => 'decimal:2',
        'numero_parcela' => 'integer',
        'total_parcelas' => 'integer',
        'total_lembretes_enviados' => 'integer',
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'data_ultimo_lembrete' => 'datetime',
        'webhook_payload' => 'array',
        'historico_cobranca' => 'array',
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

    /**
     * Retorna o valor total com eventuais encargos de multa e juros
     */
    public function getValorTotalComEncargosAttribute(): float
    {
        $base = (float) $this->valor;
        $multa = (float) ($this->valor_multa ?? 0);
        $juros = (float) ($this->valor_juros ?? 0);
        return round($base + $multa + $juros, 2);
    }

    public function getValorTotalComEncargosFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total_com_encargos, 2, ',', '.');
    }

    public function getMultaFormatadaAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_multa ?? 0, 2, ',', '.');
    }

    public function getJurosFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_juros ?? 0, 2, ',', '.');
    }

    /**
     * Retorna a quantidade de dias de atraso caso a fatura esteja vencida
     */
    public function getDiasAtrasoAttribute(): int
    {
        if (!$this->data_vencimento) {
            return 0;
        }

        $hoje = now()->startOfDay();
        $venc = $this->data_vencimento->startOfDay();

        if ($hoje->greaterThan($venc)) {
            return (int) $venc->diffInDays($hoje);
        }

        return 0;
    }

    /**
     * Verifica se a fatura já está paga/quitada
     */
    public function isPaga(): bool
    {
        $st = strtolower((string) $this->status);
        return in_array($st, ['pago', 'paga', 'quitada', 'liquidada']) || !empty($this->data_pagamento);
    }

    /**
     * Alias semântico para isPaga()
     */
    public function isQuitada(): bool
    {
        return $this->isPaga();
    }

    /**
     * Verifica se a fatura está encerrada (paga, estornada ou cancelada)
     */
    public function isLiquidadaOuCancelada(): bool
    {
        $st = strtolower((string) $this->status);
        return $this->isPaga() || in_array($st, ['cancelado', 'cancelada', 'estornado', 'estornada']);
    }

    public function isVencida(): bool
    {
        if ($this->isLiquidadaOuCancelada()) {
            return false;
        }

        return $this->status === 'vencida' || ($this->data_vencimento && $this->data_vencimento->isPast());
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->isVencida() && $this->status !== 'cancelado') {
            return '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Vencida (' . $this->dias_atraso . 'd)</span>';
        }

        return match ($this->status) {
            'pago' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Pago</span>',
            'cancelado' => '<span class="badge bg-secondary"><i class="fas fa-ban me-1"></i> Cancelado</span>',
            'estornado' => '<span class="badge bg-dark text-white"><i class="fas fa-undo me-1"></i> Estornado</span>',
            default => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Aguardando Pagamento</span>',
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
