<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('comando'); // artisan command ou url
            $table->enum('tipo', ['artisan', 'url'])->default('artisan');
            $table->string('expressao_cron')->default('* * * * *');
            $table->string('frequencia_descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->boolean('sistema')->default(false); // se true, é nativo do sistema
            $table->timestamp('ultima_execucao')->nullable();
            $table->string('ultimo_status')->nullable(); // sucesso, erro
            $table->text('ultimo_output')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_jobs');
    }
};
