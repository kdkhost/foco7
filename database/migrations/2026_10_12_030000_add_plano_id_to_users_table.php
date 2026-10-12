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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'plano_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('plano_id')->nullable()->after('role')->constrained('planos')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'plano_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['plano_id']);
                $table->dropColumn('plano_id');
            });
        }
    }
};
