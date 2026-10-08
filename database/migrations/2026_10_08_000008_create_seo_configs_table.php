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
        Schema::create('seo_configs', function (Blueprint $table) {
            $table->id();
            $table->string('pagina', 50)->unique(); // home, sobre, planos, contato, etc.
            $table->string('meta_title', 255);
            $table->text('meta_description');
            $table->text('meta_keywords')->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 255)->nullable();
            $table->string('canonical_url', 255)->nullable();
            $table->string('robots', 50)->default('index, follow');
            $table->integer('seo_score')->default(85);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_configs');
    }
};
