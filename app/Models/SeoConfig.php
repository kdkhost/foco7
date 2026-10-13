<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoConfig extends Model
{
    use HasFactory;

    protected $table = 'seo_configs';

    protected $fillable = [
        'pagina',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'seo_score',
    ];

    protected $casts = [
        'seo_score' => 'integer',
    ];

    public static function paraPagina(string $pagina = 'home'): self
    {
        $seo = self::where('pagina', $pagina)->first();
        if ($seo) {
            return $seo;
        }

        $padrao = self::obterPadraoParaPagina($pagina);
        return self::firstOrCreate(['pagina' => $pagina], $padrao);
    }

    /**
     * Fornece metadados padrão ideais para cada página do sistema
     */
    public static function obterPadraoParaPagina(string $pagina): array
    {
        $padroes = [
            'home' => [
                'meta_title' => 'Foco 7 | Rastreamento Veicular 24 Horas, Bloqueio e Ronda Tática',
                'meta_description' => 'Proteja seu carro, moto ou frota com o rastreador mais confiável do Norte e Nordeste. Bloqueio imediato pelo celular, ronda tática ágil e planos a partir de R$ 49,90/mês.',
                'meta_keywords' => 'rastreador veicular, rastreamento de carros, rastreamento de motos, ronda tática, bloqueio veicular, foco 7',
                'og_title' => 'Foco 7 Rastreamento Veicular - Proteger Hoje para Viver em Paz',
                'og_description' => 'Tecnologia de rastreamento de alta precisão com suporte de ronda tática presencial.',
                'og_image' => '/img/og-foco7.jpg',
                'canonical_url' => 'https://foco7.com.br',
                'robots' => 'index, follow',
                'seo_score' => 95,
            ],
            'login' => [
                'meta_title' => 'Login & Acesso Seguro ao Sistema | Foco 7 Rastreamento',
                'meta_description' => 'Acesse o Painel Foco 7 com segurança criptografada. Monitore seus veículos em tempo real, emita 2ª via de faturas e acione comandos de bloqueio remoto.',
                'meta_keywords' => 'login foco 7, acesso painel foco 7, central do assinante foco 7, rastreamento veicular login, entrar foco 7',
                'og_title' => 'Login do Cliente & Central de Monitoramento | Foco 7',
                'og_description' => 'Área restrita de alta performance para clientes e frotistas da Foco 7 Rastreamento.',
                'og_image' => '/img/og-foco7.jpg',
                'canonical_url' => 'https://foco7.com.br/login',
                'robots' => 'index, follow',
                'seo_score' => 90,
            ],
            'cadastro' => [
                'meta_title' => 'Criar Conta & Cadastro de Assinante | Foco 7 Rastreamento',
                'meta_description' => 'Cadastre-se na Foco 7 e blinde seu veículo com a melhor tecnologia de rastreamento 24h, bloqueio via app e equipe de ronda tática pronta para agir.',
                'meta_keywords' => 'cadastro foco 7, criar conta rastreador, assinar plano foco 7, rastreamento veicular cadastro, proteger veículo hoje',
                'og_title' => 'Cadastre-se na Foco 7 | Rastreamento Veicular & Ronda Tática',
                'og_description' => 'Junte-se a milhares de motoristas protegidos pela Foco 7. Ativação imediata e suporte 24h.',
                'og_image' => '/img/og-foco7.jpg',
                'canonical_url' => 'https://foco7.com.br/register',
                'robots' => 'index, follow',
                'seo_score' => 90,
            ],
            'reset' => [
                'meta_title' => 'Recuperação de Senha & Acesso Seguro | Foco 7',
                'meta_description' => 'Esqueceu sua senha de acesso? Solicite a redefinição segura de suas credenciais para recuperar o acesso à Central do Assinante Foco 7.',
                'meta_keywords' => 'recuperar senha foco 7, esqueci minha senha rastreador, redefinir acesso foco 7, suporte login foco 7',
                'og_title' => 'Recuperação de Acesso | Foco 7 Rastreamento',
                'og_description' => 'Processo criptografado para redefinição de senha da Central Foco 7.',
                'og_image' => '/img/og-foco7.jpg',
                'canonical_url' => 'https://foco7.com.br/password/reset',
                'robots' => 'noindex, follow',
                'seo_score' => 88,
            ],
        ];

        return $padroes[$pagina] ?? $padroes['home'];
    }
}
