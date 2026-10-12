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
        Schema::table('faturas', function (Blueprint $table) {
            if (!Schema::hasColumn('faturas', 'ultimo_lembrete_enviado')) {
                $table->string('ultimo_lembrete_enviado', 50)->nullable()->index()->after('status');
            }
            if (!Schema::hasColumn('faturas', 'data_ultimo_lembrete')) {
                $table->dateTime('data_ultimo_lembrete')->nullable()->index()->after('ultimo_lembrete_enviado');
            }
            if (!Schema::hasColumn('faturas', 'valor_multa')) {
                $table->decimal('valor_multa', 10, 2)->default(0.00)->after('valor');
            }
            if (!Schema::hasColumn('faturas', 'valor_juros')) {
                $table->decimal('valor_juros', 10, 2)->default(0.00)->after('valor_multa');
            }
            if (!Schema::hasColumn('faturas', 'total_lembretes_enviados')) {
                $table->integer('total_lembretes_enviados')->default(0)->after('data_ultimo_lembrete');
            }
            if (!Schema::hasColumn('faturas', 'historico_cobranca')) {
                $table->json('historico_cobranca')->nullable()->after('webhook_payload');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            $cols = [
                'ultimo_lembrete_enviado',
                'data_ultimo_lembrete',
                'valor_multa',
                'valor_juros',
                'total_lembretes_enviados',
                'historico_cobranca',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('faturas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
