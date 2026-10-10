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
            if (!Schema::hasColumn('faturas', 'codigo_carne')) {
                $table->string('codigo_carne', 60)->nullable()->index()->after('codigo_fatura');
            }
            if (!Schema::hasColumn('faturas', 'numero_parcela')) {
                $table->integer('numero_parcela')->nullable()->after('codigo_carne');
            }
            if (!Schema::hasColumn('faturas', 'total_parcelas')) {
                $table->integer('total_parcelas')->nullable()->after('numero_parcela');
            }
            if (!Schema::hasColumn('faturas', 'taxa_repassada')) {
                $table->decimal('taxa_repassada', 10, 2)->default(0.00)->after('valor');
            }
            if (!Schema::hasColumn('faturas', 'valor_original')) {
                $table->decimal('valor_original', 10, 2)->nullable()->after('taxa_repassada');
            }
            if (!Schema::hasColumn('faturas', 'link_pdf_carne')) {
                $table->text('link_pdf_carne')->nullable()->after('url_boleto');
            }
            if (!Schema::hasColumn('faturas', 'efi_carnet_id')) {
                $table->string('efi_carnet_id', 100)->nullable()->index()->after('gateway_transaction_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faturas', function (Blueprint $table) {
            $cols = ['codigo_carne', 'numero_parcela', 'total_parcelas', 'taxa_repassada', 'valor_original', 'link_pdf_carne', 'efi_carnet_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('faturas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
