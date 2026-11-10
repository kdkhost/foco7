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

