<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plano_id')->nullable()->constrained('planos')->onDelete('set null');
            $table->string('codigo_fatura', 50)->unique();
            $table->string('descricao', 255);
            $table->decimal('valor', 10, 2);
            $table->string('gateway', 30)->nullable()->index(); // mercadopago, efi
            $table->string('metodo_pagamento', 30)->nullable()->index(); // pix, cartao, boleto
            $table->string('gateway_transaction_id', 150)->nullable()->index();
            $table->longText('qr_code_pix')->nullable(); // Copia e Cola
            $table->text('qr_code_pix_url')->nullable(); // URL da imagem do QR Code
            $table->string('linha_digitavel_boleto', 100)->nullable();
            $table->text('url_boleto')->nullable();
            $table->string('status', 30)->default('pendente')->index(); // pendente, pago, cancelado, estornado
            $table->date('data_vencimento')->index();
            $table->dateTime('data_pagamento')->nullable()->index();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};
