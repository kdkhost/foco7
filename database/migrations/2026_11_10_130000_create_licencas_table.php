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
        if (!Schema::hasTable('licencas')) {
            Schema::create('licencas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('fatura_id')->nullable()->constrained('faturas')->nullOnDelete();
                $table->string('chave_licenca', 64)->unique()->index();
                $table->string('tipo_licenca', 40)->default('vitalicia'); // 'vitalicia', 'anual', 'mensal', 'teste'
                $table->string('nome_empresa', 150)->nullable();
                $table->string('dominio_autorizado', 180)->nullable()->index(); // ex: rastreamento.meudominio.com.br ou '*'
                $table->string('ip_autorizado', 45)->nullable();
                $table->string('status', 30)->default('pendente')->index(); // 'pendente', 'ativa', 'suspensa', 'bloqueada', 'expirada'
                $table->string('versao_liberada', 20)->default('v3.8.0');
                $table->integer('limite_veiculos')->nullable(); // null = ilimitado
                $table->integer('limite_clientes')->nullable(); // null = ilimitado
                $table->integer('total_downloads')->default(0);
                $table->timestamp('ultimo_download_em')->nullable();
                $table->timestamp('ultimo_check_in')->nullable();
                $table->timestamp('data_ativacao')->nullable();
                $table->timestamp('data_expiracao')->nullable();
                $table->json('dados_servidor')->nullable(); // SO, PHP, Servidor Web, etc.
                $table->text('observacoes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencas');
    }
};

