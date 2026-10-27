<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pagamento\CheckoutController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiCheckoutController extends Controller
{
    public function __construct(
        protected CheckoutController $checkoutController
    ) {}

    /**
     * Simula e calcula valores com repasse de taxas e descontos em tempo real
     */
    public function calcularTaxas(Request $request): JsonResponse
    {
        return $this->checkoutController->calcularTaxas($request);
    }

    /**
     * Executa Checkout Transparente via API REST (100% sem redirecionamento externo)
     */
    public function checkout(Request $request): JsonResponse
    {
        return $this->checkoutController->checkout($request);
    }
}
