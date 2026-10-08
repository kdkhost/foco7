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

    public static function paraPagina(string $pagina = 'home'): ?self
    {
        return self::where('pagina', $pagina)->first() ?? self::first();
    }
}
