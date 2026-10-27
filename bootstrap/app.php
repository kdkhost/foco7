<?php

use App\Http\Middleware\ApiAuthKey;
use App\Http\Middleware\CheckSessionInactivity;
use App\Http\Middleware\ControleAcesso;
use App\Http\Middleware\GeoRestricao;
use App\Http\Middleware\ModoManutencao;
use App\Http\Middleware\RegistrarVisita;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\VerificarPermissao;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SecurityHeaders::class,
            GeoRestricao::class,
            ModoManutencao::class,
            RegistrarVisita::class,
            CheckSessionInactivity::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/*',
            'api/*',
        ]);

        $middleware->alias([
            'role' => ControleAcesso::class,
            'permissao' => VerificarPermissao::class,
            'manutencao' => ModoManutencao::class,
            'api.key' => ApiAuthKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
