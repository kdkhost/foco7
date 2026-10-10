<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    use HasFactory;

    protected $table = 'cron_jobs';

    protected $fillable = [
        'nome',
        'descricao',
        'comando',
        'tipo',
        'expressao_cron',
        'frequencia_descricao',
        'ativo',
        'sistema',
        'ultima_execucao',
        'ultimo_status',
        'ultimo_output',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'sistema' => 'boolean',
        'ultima_execucao' => 'datetime',
    ];

    /**
     * Retorna a data/hora formatada da última execução
     */
    public function getUltimaExecucaoFormatadaAttribute(): string
    {
        return $this->ultima_execucao ? $this->ultima_execucao->format('d/m/Y H:i:s') : 'Nunca executado';
    }

    /**
     * Retorna o badge com status da última execução
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->ultima_execucao) {
            return '<span class="text-muted"><i class="fas fa-hourglass-start me-1"></i> Aguardando</span>';
        }

        if ($this->ultimo_status === 'erro') {
            return '<span class="badge bg-danger-subtle text-danger"><i class="fas fa-times-circle me-1"></i> Falha</span>';
        }

        return '<span class="badge bg-success-subtle text-success"><i class="fas fa-check-circle me-1"></i> Sucesso</span>';
    }
}
