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
        // 1. Tabela de Serviços de Estética Automotiva
        Schema::create('servicos_estetica', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('slug', 160)->unique();
            $table->string('subtitulo', 255)->nullable();
            $table->text('descricao')->nullable();
            $table->string('icone', 60)->default('fa-car-side');
            $table->string('imagem')->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->string('tempo_estimado', 100)->nullable();
            $table->json('recursos')->nullable();
            $table->boolean('destaque')->default(false);
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // 2. Colunas de Categoria e Estética em Agendamentos
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('categoria', 40)->default('rastreamento')->after('tipo_servico');
            $table->foreignId('servico_estetica_id')->nullable()->after('categoria')->constrained('servicos_estetica')->nullOnDelete();
            $table->decimal('valor', 10, 2)->nullable()->after('servico_estetica_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropForeign(['servico_estetica_id']);
            $table->dropColumn(['categoria', 'servico_estetica_id', 'valor']);
        });

        Schema::dropIfExists('servicos_estetica');
    }
};
