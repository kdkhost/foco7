<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     */
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50)->default('geral'); // contato, agendamento, depoimento, sistema
            $table->string('titulo');
            $table->text('mensagem');
            $table->string('icone', 60)->default('fas fa-bell');
            $table->string('cor', 30)->default('danger');
            $table->string('link_acao')->nullable();
            $table->json('dados')->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->boolean('notificado_email')->default(false);
            $table->boolean('notificado_push')->default(false);
            $table->timestamps();

            $table->index(['tipo', 'lida', 'created_at']);
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
