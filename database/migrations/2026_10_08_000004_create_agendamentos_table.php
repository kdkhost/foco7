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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('cliente_nome', 150);
            $table->string('cliente_telefone', 30);
            $table->string('cliente_email', 150)->nullable();
            $table->string('tipo_servico', 50)->default('Instalação')->index(); // Instalação, Manutenção, Vistoria, Suporte
            $table->string('veiculo_modelo', 100)->nullable();
            $table->string('veiculo_placa', 20)->nullable()->index();
            $table->dateTime('data_hora_inicio')->index();
            $table->dateTime('data_hora_fim')->nullable();
            $table->string('endereco_atendimento', 255)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->text('observacoes')->nullable();
            $table->string('status', 30)->default('pendente')->index(); // pendente, confirmado, concluido, cancelado
            $table->string('cor_calendario', 20)->default('#d90000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
