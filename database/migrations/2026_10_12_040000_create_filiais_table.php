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
        Schema::create('filiais', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('cidade', 100);
            $table->string('estado', 2)->default('PA');
            $table->string('endereco', 255);
            $table->string('bairro', 100)->nullable();
            $table->string('cep', 20)->nullable();
            $table->string('telefone', 50)->nullable();
            $table->string('whatsapp', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('horario_atendimento', 150)->nullable()->default('Seg a Sex: 08:00 às 18:00 | Sáb: 08:00 às 12:00');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('link_rota')->nullable();
            $table->string('foto', 255)->nullable();
            $table->boolean('destaque')->default(false);
            $table->boolean('ativo')->default(true)->index();
            $table->integer('ordem')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiais');
    }
};
