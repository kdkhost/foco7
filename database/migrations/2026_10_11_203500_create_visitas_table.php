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
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->string('ip_hash', 64)->index();
            $table->string('session_id', 80)->nullable()->index();
            $table->string('url', 255)->nullable();
            $table->string('rota', 100)->nullable()->index();
            $table->string('dispositivo', 20)->default('desktop')->index(); // mobile, tablet, desktop
            $table->string('navegador', 50)->nullable(); // Chrome, Safari, etc.
            $table->string('sistema_operacional', 50)->nullable(); // Android, iOS, Windows, etc.
            $table->string('origem', 255)->nullable();
            $table->date('data_visita')->index();
            $table->timestamps();

            $table->index(['data_visita', 'ip_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
