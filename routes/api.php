<?php

use App\Http\Controllers\Api\V1\AdminApiController;
use App\Http\Controllers\Api\V1\ApiCheckoutController;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Foco 7 Rastreamento Veicular
|--------------------------------------------------------------------------
|
| Rotas RESTful versionadas para integrações externas, aplicativos móveis
| e microsserviços parceiros com suporte a Checkout Transparente.
|
*/

Route::prefix('v1')->group(function () {
    // Endpoints Públicos de Consulta
    Route::get('/ping', [ApiController::class, 'ping'])->name('api.v1.ping');
    Route::get('/planos', [ApiController::class, 'planos'])->name('api.v1.planos');
    Route::get('/filiais', [ApiController::class, 'filiais'])->name('api.v1.filiais');
    Route::get('/faturas/{codigo}', [ApiController::class, 'consultarFatura'])->name('api.v1.faturas.consultar');

    // Checkout Transparente Unificado (Mercado Pago e Efí PRO - Sem Redirecionamento)
    Route::post('/checkout/calcular-taxas', [ApiCheckoutController::class, 'calcularTaxas'])->name('api.v1.checkout.calcular_taxas');
    Route::post('/checkout/gerar', [ApiCheckoutController::class, 'checkout'])->name('api.v1.checkout.gerar');

    // Endpoints Administrativos e de Gestão (Protegidos por Chave de API / Bearer Token)
    Route::middleware(['api.key'])->prefix('admin')->group(function () {
        Route::get('/metricas', [AdminApiController::class, 'metricas'])->name('api.v1.admin.metricas');
        Route::get('/clientes', [AdminApiController::class, 'clientes'])->name('api.v1.admin.clientes');
        Route::post('/carnes/emitir', [AdminApiController::class, 'emitirCarne'])->name('api.v1.admin.carnes.emitir');
        Route::post('/faturas/{id}/estornar', [AdminApiController::class, 'estornarFatura'])->name('api.v1.admin.faturas.estornar');
    });
});
