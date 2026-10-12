<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'permissoes',
        'plano_id',
        'cpf_cnpj',
        'telefone',
        'data_nascimento',
        'genero',
        'cargo',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'bio',
        'status',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'data_nascimento' => 'date',
            'password' => 'hashed',
            'permissoes' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function hasTwoFactor(): bool
    {
        return (bool) $this->two_factor_enabled && !empty($this->two_factor_secret);
    }

    public function temPermissao(string $permissao): bool
    {
        if ($this->isAdmin() || $this->role === 'admin') {
            return true;
        }

        if (empty($this->permissoes) || !is_array($this->permissoes)) {
            return false;
        }

        return in_array($permissao, $this->permissoes);
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset(ltrim($this->avatar, '/'));
        }

        return '';
    }

    public function getEnderecoCompletoAttribute(): string
    {
        $partes = [];
        if (!empty($this->endereco)) {
            $end = $this->endereco;
            if (!empty($this->numero)) $end .= ', ' . $this->numero;
            if (!empty($this->complemento)) $end .= ' - ' . $this->complemento;
            $partes[] = $end;
        }
        if (!empty($this->bairro)) $partes[] = $this->bairro;
        $cidadeUf = trim("{$this->cidade} - {$this->estado}");
        if (!empty($cidadeUf)) $partes[] = $cidadeUf;
        if (!empty($this->cep)) $partes[] = "CEP: {$this->cep}";

        return implode(', ', $partes);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(Plano::class);
    }

    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
