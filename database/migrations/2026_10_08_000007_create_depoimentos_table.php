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
        Schema::create('depoimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_cliente', 150);
            $table->string('cidade_uf', 100)->nullable();
            $table->string('veiculo', 100)->nullable();
            $table->string('foto', 255)->nullable();
            $table->text('texto');
            $table->integer('estrelas')->default(5);
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
        Schema::dropIfExists('depoimentos');
    }
};
