<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filial extends Model
{
    use HasFactory;

    protected $table = 'filiais';

    protected $fillable = [
        'nome',
        'cidade',
        'estado',
        'endereco',
        'bairro',
        'cep',
        'telefone',
        'whatsapp',
        'email',
        'horario_atendimento',
        'latitude',
        'longitude',
        'link_rota',
        'foto',
        'destaque',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'destaque' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'ordem' => 'integer',
    ];

    protected $appends = [
        'foto_url',
        'rota_url',
        'whatsapp_url',
        'endereco_completo',
    ];

    /**
     * Scope para unidades ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para ordenação de exibição
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('destaque', 'desc')->orderBy('ordem', 'asc')->orderBy('nome', 'asc');
    }

    /**
     * URL completa da foto/fachada
     */
    public function getFotoUrlAttribute()
    {
        if (empty($this->foto)) {
            return asset('img/logo-foco7.png');
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        return asset(ltrim($this->foto, '/'));
    }

    /**
     * URL direta para traçar rota no OpenStreetMap (100% gratuito e sem custos)
     */
    public function getRotaOsmUrlAttribute()
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            return "https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=%2C{$this->latitude}%2C{$this->longitude}#map=17/{$this->latitude}/{$this->longitude}";
        }

        $destinoFormatado = urlencode("{$this->endereco}, {$this->bairro}, {$this->cidade} - {$this->estado}, Brasil");
        return "https://www.openstreetmap.org/search?query={$destinoFormatado}";
    }

    /**
     * URL direta para traçar rota no Google Maps com endereço completo e CEP exatos
     */
    public function getRotaGoogleMapsUrlAttribute()
    {
        $destino = trim("{$this->endereco}, " . ($this->bairro ? "{$this->bairro}, " : "") . "{$this->cidade} - {$this->estado}" . ($this->cep ? ", {$this->cep}" : ""));
        return "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($destino);
    }

    /**
     * URL direta para traçar rota (OpenStreetMap por padrão, ou link_rota se customizado)
     */
    public function getRotaUrlAttribute()
    {
        if (!empty($this->link_rota) && !str_contains($this->link_rota, '-1.4557549')) {
            return $this->link_rota;
        }

        return $this->rota_osm_url;
    }

    /**
     * URL do WhatsApp da unidade
     */
    public function getWhatsappUrlAttribute()
    {
        if (empty($this->whatsapp)) {
            return 'https://wa.me/559180510736';
        }

        $numeroLimpo = preg_replace('/\D/', '', $this->whatsapp);
        if (strlen($numeroLimpo) <= 11 && !str_starts_with($numeroLimpo, '55')) {
            $numeroLimpo = '55' . $numeroLimpo;
        }

        $msg = urlencode("Olá! Gostaria de atendimento na unidade Foco 7 de {$this->cidade}/{$this->estado}.");
        return "https://wa.me/{$numeroLimpo}?text={$msg}";
    }

    /**
     * Endereço formatado e completo
     */
    public function getEnderecoCompletoAttribute()
    {
        $partes = [];
        if (!empty($this->endereco)) $partes[] = $this->endereco;
        if (!empty($this->bairro)) $partes[] = $this->bairro;
        $cidadeUf = trim("{$this->cidade} - {$this->estado}");
        if (!empty($cidadeUf)) $partes[] = $cidadeUf;
        if (!empty($this->cep)) $partes[] = "CEP: {$this->cep}";

        return implode(', ', $partes);
    }
}
