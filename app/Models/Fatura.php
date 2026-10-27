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
        return 'R$ '.number_format($this->valor, 2, ',', '.');
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
        return 'R$ '.number_format($this->valor_total_com_encargos, 2, ',', '.');
    }

    public function getMultaFormatadaAttribute(): string
    {
        return 'R$ '.number_format($this->valor_multa ?? 0, 2, ',', '.');
    }

    public function getJurosFormatadoAttribute(): string
    {
        return 'R$ '.number_format($this->valor_juros ?? 0, 2, ',', '.');
    }

    /**
     * Retorna a quantidade de dias de atraso caso a fatura esteja vencida
     */
    public function getDiasAtrasoAttribute(): int
    {
        if (! $this->data_vencimento) {
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

        return in_array($st, ['pago', 'paga', 'quitada', 'liquidada']) || ! empty($this->data_pagamento);
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

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->isVencida() && $this->status !== 'cancelado') {
            return 'status-badge-vencida';
        }

        return match ($this->status) {
            'pago' => 'status-badge-pago',
            'cancelado' => 'status-badge-cancelado',
            'estornado' => 'status-badge-estornado',
            default => 'status-badge-pendente',
        };
    }

    public function getStatusBadgeIconAttribute(): string
    {
        if ($this->isVencida() && $this->status !== 'cancelado') {
            return 'fa-exclamation-triangle';
        }

        return match ($this->status) {
            'pago' => 'fa-check-circle',
            'cancelado' => 'fa-ban',
            'estornado' => 'fa-undo',
            default => 'fa-clock',
        };
    }

    public function getStatusBadgeLabelAttribute(): string
    {
        if ($this->isVencida() && $this->status !== 'cancelado') {
            return 'Vencida ('.$this->dias_atraso.'d)';
        }

        return match ($this->status) {
            'pago' => 'Pago',
            'cancelado' => 'Cancelado',
            'estornado' => 'Estornado',
            default => 'Aguardando Pagamento',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return '<span class="status-badge-pill '.$this->status_badge_class.'"><span class="status-dot"></span><i class="fas '.$this->status_badge_icon.'"></i><span>'.$this->status_badge_label.'</span></span>';
    }

    public function isCarne(): bool
    {
        return ! empty($this->codigo_carne)
            || $this->metodo_pagamento === 'carne'
            || str_starts_with($this->codigo_fatura, 'CARNE-')
            || ($this->total_parcelas !== null && $this->total_parcelas > 1);
    }

    /**
     * Retorna a query de todas as parcelas pertencentes ao mesmo carnê
     */
    public function getParcelasCarneQuery()
    {
        if (! empty($this->codigo_carne)) {
            return self::where('codigo_carne', $this->codigo_carne);
        }

        if (! empty($this->efi_carnet_id)) {
            return self::where('efi_carnet_id', $this->efi_carnet_id);
        }

        if ($this->total_parcelas > 1 && str_contains($this->codigo_fatura, '-')) {
            $prefixo = substr($this->codigo_fatura, 0, (int) strrpos($this->codigo_fatura, '-'));

            return self::where('user_id', $this->user_id)
                ->where('codigo_fatura', 'like', $prefixo.'-%');
        }

        return self::where('id', $this->id);
    }

    /**
     * Retorna informações completas do carnê (pagas, pendentes, sequência a partir desta parcela)
     */
    public function getInfoCarne(): array
    {
        if (! $this->isCarne()) {
            return [
                'is_carne' => false,
                'codigo_carne' => null,
                'numero_parcela' => 1,
                'total_parcelas' => 1,
                'total_geral' => 1,
                'tem_pagas' => false,
                'total_pagas' => 0,
                'total_canceladas' => 0,
                'total_pendentes' => $this->status === 'pendente' ? 1 : 0,
                'total_sequencia' => 1,
            ];
        }

        $parcelas = $this->getParcelasCarneQuery()->orderBy('numero_parcela')->orderBy('id')->get();
        $totalGeral = $parcelas->count();

        if ($totalGeral <= 1 && ($this->total_parcelas ?? 1) <= 1) {
            return [
                'is_carne' => false,
                'codigo_carne' => $this->codigo_carne,
                'numero_parcela' => $this->numero_parcela ?? 1,
                'total_parcelas' => 1,
                'total_geral' => 1,
                'tem_pagas' => $this->isPaga(),
                'total_pagas' => $this->isPaga() ? 1 : 0,
                'total_canceladas' => $this->status === 'cancelado' ? 1 : 0,
                'total_pendentes' => $this->status === 'pendente' ? 1 : 0,
                'total_sequencia' => 1,
            ];
        }

        $pagas = $parcelas->filter(fn ($p) => $p->isPaga());
        $temPagas = $pagas->isNotEmpty();
        $totalPagas = $pagas->count();

        $canceladas = $parcelas->filter(fn ($p) => $p->status === 'cancelado');
        $pendentes = $parcelas->filter(fn ($p) => ! $p->isPaga() && $p->status !== 'cancelado');

        $numAtual = $this->numero_parcela ?: 1;
        $sequenciaPendente = $parcelas->filter(fn ($p) => ($p->numero_parcela >= $numAtual) && ! $p->isPaga() && $p->status !== 'cancelado'
        );

        return [
            'is_carne' => true,
            'codigo_carne' => $this->codigo_carne ?: ($this->codigo_fatura ? preg_replace('/-\\d+$/', '', $this->codigo_fatura) : 'CARNE'),
            'numero_parcela' => $numAtual,
            'total_parcelas' => $this->total_parcelas ?: $totalGeral,
            'total_geral' => $totalGeral,
            'tem_pagas' => $temPagas,
            'total_pagas' => $totalPagas,
            'total_canceladas' => $canceladas->count(),
            'total_pendentes' => $pendentes->count(),
            'total_sequencia' => $sequenciaPendente->count(),
            'sequencia_numeros' => $sequenciaPendente->pluck('numero_parcela')->values()->all(),
            'pagas_numeros' => $pagas->pluck('numero_parcela')->values()->all(),
        ];
    }

    public function getTaxaRepassadaFormatadaAttribute(): string
    {
        return 'R$ '.number_format($this->taxa_repassada ?? 0, 2, ',', '.');
    }

    public function getValorOriginalFormatadoAttribute(): string
    {
        $val = $this->valor_original ?? ($this->valor - ($this->taxa_repassada ?? 0));

        return 'R$ '.number_format($val, 2, ',', '.');
    }
}
