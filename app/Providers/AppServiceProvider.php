<?php

namespace App\Providers;

use App\Models\CmsSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        View::composer(['admin.*', 'cliente.*', 'site.*', 'errors.*'], function ($view) {
            $corPrimaria = CmsSetting::get('cor_primaria', '#d90000');
            $corSecundaria = CmsSetting::get('cor_secundaria', '#11141c');
            $corDestaque = CmsSetting::get('cor_destaque', '#b80000');

            $hex = ltrim($corPrimaria, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            $r = hexdec(substr($hex, 0, 2) ?: 'd9');
            $g = hexdec(substr($hex, 2, 2) ?: '00');
            $b = hexdec(substr($hex, 4, 2) ?: '00');

            $view->with([
                'cmsCorPrimaria' => $corPrimaria,
                'cmsCorSecundaria' => $corSecundaria,
                'cmsCorDestaque' => $corDestaque,
                'cmsCorPrimariaRgb' => "{$r}, {$g}, {$b}",
            ]);
        });
    }
}
