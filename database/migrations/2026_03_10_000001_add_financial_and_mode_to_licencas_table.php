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
        if (Schema::hasTable('licencas')) {
            Schema::table('licencas', function (Blueprint $table) {
                if (! Schema::hasColumn('licencas', 'modo_ativacao')) {
                    $table->string('modo_ativacao', 20)->default('automatica')->after('status')->comment('automatica ou manual');
                }
                if (! Schema::hasColumn('licencas', 'valor_adesao')) {
                    $table->decimal('valor_adesao', 10, 2)->nullable()->default(1497.00)->after('modo_ativacao');
                }
                if (! Schema::hasColumn('licencas', 'status_adesao')) {
                    $table->string('status_adesao', 20)->default('pago')->after('valor_adesao')->comment('pago, pendente, isento');
                }
                if (! Schema::hasColumn('licencas', 'valor_mensalidade')) {
                    $table->decimal('valor_mensalidade', 10, 2)->nullable()->default(0.00)->after('status_adesao');
                }
                if (! Schema::hasColumn('licencas', 'status_mensalidade')) {
                    $table->string('status_mensalidade', 20)->default('em_dia')->after('valor_mensalidade')->comment('em_dia, pendente, vencida, isento');
                }
                if (! Schema::hasColumn('licencas', 'dia_vencimento')) {
                    $table->unsignedTinyInteger('dia_vencimento')->nullable()->default(10)->after('status_mensalidade');
                }
                if (! Schema::hasColumn('licencas', 'proximo_vencimento')) {
                    $table->date('proximo_vencimento')->nullable()->after('dia_vencimento');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('licencas')) {
            Schema::table('licencas', function (Blueprint $table) {
                $cols = ['modo_ativacao', 'valor_adesao', 'status_adesao', 'valor_mensalidade', 'status_mensalidade', 'dia_vencimento', 'proximo_vencimento'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('licencas', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

