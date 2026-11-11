<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Licenca extends Model
{
    use HasFactory;

    protected $table = 'licencas';

    protected $fillable = [
        'user_id',
        'fatura_id',
        'chave_licenca',
        'tipo_licenca',
        'nome_empresa',
        'dominio_autorizado',
        'ip_autorizado',
        'status',
        'modo_ativacao',
        'valor_adesao',
        'status_adesao',
        'valor_mensalidade',
        'status_mensalidade',
        'dia_vencimento',
        'proximo_vencimento',
        'versao_liberada',
        'limite_veiculos',
        'limite_clientes',
        'total_downloads',
        'ultimo_download_em',
        'ultimo_check_in',
        'data_ativacao',
        'data_expiracao',
        'dados_servidor',
        'observacoes',
    ];

    protected $casts = [
        'dados_servidor' => 'array',
        'total_downloads' => 'integer',
        'limite_veiculos' => 'integer',
        'limite_clientes' => 'integer',
        'valor_adesao' => 'decimal:2',
        'valor_mensalidade' => 'decimal:2',
        'dia_vencimento' => 'integer',
        'proximo_vencimento' => 'date',
        'ultimo_download_em' => 'datetime',
        'ultimo_check_in' => 'datetime',
        'data_ativacao' => 'datetime',
        'data_expiracao' => 'datetime',
    ];

    /**
     * Relacionamento com o Licenciado (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a Fatura que originou a licença
     */
    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }

    /**
     * Verifica se a licença está formalmente ativa e dentro da vigência
     */
    public function isAtiva(): bool
    {
        if ($this->status !== 'ativa') {
            return false;
        }

        if ($this->data_expiracao && $this->data_expiracao->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se a licença está pendente de pagamento
     */
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    /**
     * Gera uma chave de licença única e segura no formato FOCO7-XXXX-XXXX-XXXX-XXXX
     */
    public static function gerarChaveUnica(): string
    {
        do {
            $p1 = strtoupper(Str::random(4));
            $p2 = strtoupper(Str::random(4));
            $p3 = strtoupper(Str::random(4));
            $p4 = strtoupper(Str::random(4));
            $chave = "FOCO7-{$p1}-{$p2}-{$p3}-{$p4}";
        } while (self::where('chave_licenca', $chave)->exists());

        return $chave;
    }

    /**
     * Retorna badge HTML estilizado para o status da licença
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'ativa' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i> Ativa</span>',
            'pendente' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i> Aguardando Pagamento</span>',
            'suspensa' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1"><i class="fas fa-pause-circle me-1"></i> Suspensa</span>',
            'bloqueada' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1"><i class="fas fa-ban me-1"></i> Bloqueada</span>',
            'expirada' => '<span class="badge bg-dark-subtle text-dark border border-dark-subtle rounded-pill px-3 py-1"><i class="fas fa-calendar-xmark me-1"></i> Expirada</span>',
            default => '<span class="badge bg-secondary rounded-pill px-3 py-1">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Modo de Ativação: 'automatica' (ativa ao pagar) ou 'manual' (requer aprovação do admin)
     */
    public function getModoAtivacaoAttribute($value): string
    {
        if (! empty($value)) {
            return $value;
        }

        return $this->dados_servidor['financeiro']['modo_ativacao'] ?? 'automatica';
    }

    public function setModoAtivacaoAttribute($value): void
    {
        $this->attributes['modo_ativacao'] = $value ?: 'automatica';
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['modo_ativacao'] = $value ?: 'automatica';
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    public function getModoAtivacaoBadgeAttribute(): string
    {
        return $this->modo_ativacao === 'automatica'
            ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1"><i class="fas fa-bolt me-1"></i> Automática</span>'
            : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="fas fa-hand-holding me-1"></i> Manual</span>';
    }

    /**
     * Taxa de Adesão / Implantação
     */
    public function getValorAdesaoAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) ($this->dados_servidor['financeiro']['valor_adesao'] ?? (float) \App\Models\CmsSetting::get('licenciamento_preco_adesao', 1497.00));
    }

    public function setValorAdesaoAttribute($value): void
    {
        $v = (float) $value;
        $this->attributes['valor_adesao'] = $v;
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['valor_adesao'] = $v;
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    public function getValorAdesaoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_adesao, 2, ',', '.');
    }

    public function getStatusAdesaoAttribute($value): string
    {
        if (! empty($value)) {
            return $value;
        }

        if (isset($this->dados_servidor['financeiro']['status_adesao'])) {
            return $this->dados_servidor['financeiro']['status_adesao'];
        }

        return $this->status === 'ativa' ? 'pago' : 'pendente';
    }

    public function setStatusAdesaoAttribute($value): void
    {
        $this->attributes['status_adesao'] = $value;
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['status_adesao'] = $value;
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    public function getStatusAdesaoBadgeAttribute(): string
    {
        return match ($this->status_adesao) {
            'pago' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="fas fa-check me-1"></i> Pago</span>',
            'isento' => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1"><i class="fas fa-gift me-1"></i> Isento</span>',
            default => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="fas fa-clock me-1"></i> Pendente</span>',
        };
    }

    /**
     * Mensalidade da Licença (Suporte / Atualizações)
     */
    public function getValorMensalidadeAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return (float) ($this->dados_servidor['financeiro']['valor_mensalidade'] ?? (float) \App\Models\CmsSetting::get('licenciamento_preco_mensal', 0.00));
    }

    public function setValorMensalidadeAttribute($value): void
    {
        $v = (float) $value;
        $this->attributes['valor_mensalidade'] = $v;
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['valor_mensalidade'] = $v;
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    public function getValorMensalidadeFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_mensalidade, 2, ',', '.');
    }

    public function getStatusMensalidadeAttribute($value): string
    {
        if (! empty($value)) {
            return $value;
        }

        if (isset($this->dados_servidor['financeiro']['status_mensalidade'])) {
            return $this->dados_servidor['financeiro']['status_mensalidade'];
        }

        if ($this->valor_mensalidade <= 0) {
            return 'isento';
        }

        return $this->status === 'ativa' ? 'em_dia' : 'pendente';
    }

    public function setStatusMensalidadeAttribute($value): void
    {
        $this->attributes['status_mensalidade'] = $value;
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['status_mensalidade'] = $value;
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    public function getStatusMensalidadeBadgeAttribute(): string
    {
        if ($this->valor_mensalidade <= 0) {
            return '<span class="badge bg-light text-secondary border rounded-pill px-2 py-1">Sem Mensalidade</span>';
        }

        return match ($this->status_mensalidade) {
            'em_dia' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="fas fa-check-double me-1"></i> Em Dia</span>',
            'vencida' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i> Vencida</span>',
            'isento' => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1"><i class="fas fa-shield me-1"></i> Isento</span>',
            default => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="fas fa-hourglass-half me-1"></i> Pendente</span>',
        };
    }

    /**
     * Vencimento da Mensalidade
     */
    public function getProximoVencimentoAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return $this->dados_servidor['financeiro']['proximo_vencimento'] ?? null;
    }

    public function setProximoVencimentoAttribute($value): void
    {
        $this->attributes['proximo_vencimento'] = $value;
        $dados = $this->dados_servidor ?? [];
        $dados['financeiro']['proximo_vencimento'] = $value ? (is_string($value) ? $value : $value->format('Y-m-d')) : null;
        $this->attributes['dados_servidor'] = json_encode($dados);
    }

    /**
     * Helpers de Verificação
     */
    public function isModoAutomatico(): bool
    {
        return $this->modo_ativacao === 'automatica';
    }

    public function isModoManual(): bool
    {
        return $this->modo_ativacao === 'manual';
    }

    public function isAdesaoPaga(): bool
    {
        return in_array($this->status_adesao, ['pago', 'isento']);
    }

    public function isMensalidadeEmDia(): bool
    {
        return $this->valor_mensalidade <= 0 || in_array($this->status_mensalidade, ['em_dia', 'isento']);
    }

    /**
     * Ativa formalmente a licença
     */
    public function ativar(?string $motivo = null): bool
    {
        $this->status = 'ativa';
        if (! $this->data_ativacao) {
            $this->data_ativacao = now();
        }
        if ($this->valor_mensalidade > 0 && (! $this->proximo_vencimento || $this->proximo_vencimento < now())) {
            $this->proximo_vencimento = now()->addDays(30)->format('Y-m-d');
            $this->status_mensalidade = 'em_dia';
        }
        if ($motivo) {
            $this->observacoes = trim(($this->observacoes ? $this->observacoes . "\n" : '') . '[' . now()->format('d/m/Y H:i') . '] Ativada: ' . $motivo);
        }

        return $this->save();
    }

    /**
     * Desativa / Suspende a licença
     */
    public function desativar(string $status = 'suspensa', ?string $motivo = null): bool
    {
        $this->status = in_array($status, ['suspensa', 'bloqueada']) ? $status : 'suspensa';
        if ($motivo) {
            $this->observacoes = trim(($this->observacoes ? $this->observacoes . "\n" : '') . '[' . now()->format('d/m/Y H:i') . '] ' . ucfirst($this->status) . ': ' . $motivo);
        }

        return $this->save();
    }

    /**
     * Escopo para licenças ativas
     */
    public function scopeAtiva($query)
    {
        return $query->where('status', 'ativa')
            ->where(function ($q) {
                $q->whereNull('data_expiracao')->orWhere('data_expiracao', '>', now());
            });
    }

    /**
     * Escopo para licenças pendentes
     */
    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }
}

