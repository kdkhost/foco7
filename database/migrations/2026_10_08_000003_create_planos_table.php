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
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('slug', 100)->unique();
            $table->string('categoria', 50)->default('geral')->index(); // carro, moto, frota, carga
            $table->string('subtitulo', 255)->nullable();
            $table->decimal('preco_mensal', 10, 2);
            $table->decimal('taxa_adesao', 10, 2)->default(0.00);
            $table->json('recursos')->nullable(); // Lista de itens/benefícios
            $table->string('icone', 100)->nullable(); // Classe de ícone FontAwesome / Bootstrap
            $table->boolean('destaque')->default(false)->index();
            $table->string('tag_destaque', 50)->nullable(); // Ex: "MAIS POPULAR", "MELHOR CUSTO-BENEFÍCIO"
            $table->integer('ordem')->default(0)->index();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
