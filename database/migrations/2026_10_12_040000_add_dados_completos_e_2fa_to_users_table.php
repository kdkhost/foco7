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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'data_nascimento')) {
                $table->date('data_nascimento')->nullable()->after('cpf_cnpj');
            }
            if (!Schema::hasColumn('users', 'genero')) {
                $table->string('genero', 20)->nullable()->after('data_nascimento');
            }
            if (!Schema::hasColumn('users', 'cargo')) {
                $table->string('cargo', 100)->nullable()->after('genero');
            }
            if (!Schema::hasColumn('users', 'complemento')) {
                $table->string('complemento', 100)->nullable()->after('numero');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('complemento');
            }
            if (!Schema::hasColumn('users', 'permissoes')) {
                $table->json('permissoes')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('two_factor_confirmed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $colunas = [
                'data_nascimento',
                'genero',
                'cargo',
                'complemento',
                'bio',
                'permissoes',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'two_factor_enabled',
            ];

            foreach ($colunas as $coluna) {
                if (Schema::hasColumn('users', $coluna)) {
                    $table->dropColumn($coluna);
                }
            }
        });
    }
};
