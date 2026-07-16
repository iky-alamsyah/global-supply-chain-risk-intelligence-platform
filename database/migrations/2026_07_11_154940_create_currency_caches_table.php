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
        Schema::create('currency_cache', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Mata Uang
            $table->string('base_currency', 10);
            $table->string('target_currency', 10)->default('USD');

            // Nilai Tukar
            $table->decimal('exchange_rate', 18, 6);

            // Persentase perubahan
            $table->decimal('change_percentage', 8, 2)
                ->default(0);

            // Currency Risk
            $table->decimal('currency_risk_score', 5, 2)
                ->default(0);

            // Waktu data dari API
            $table->timestamp('rate_time')->nullable();

            // Cache Expired
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('country_id');
            $table->index('base_currency');
            $table->index('target_currency');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_cache');
    }
};