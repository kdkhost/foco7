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
        Schema::create('banners_slides', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50)->default('hero_slide')->index(); // hero_slide, notification_banner, partner_logo
            $table->string('badge', 100)->nullable();
            $table->string('titulo', 255)->nullable();
            $table->text('subtitulo')->nullable();
            $table->string('texto_botao_1', 100)->nullable();
            $table->string('link_botao_1', 255)->nullable();
            $table->string('texto_botao_2', 100)->nullable();
            $table->string('link_botao_2', 255)->nullable();
            $table->string('imagem', 255)->nullable();
            $table->string('imagem_mobile', 255)->nullable();
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
        Schema::dropIfExists('banners_slides');
    }
};
