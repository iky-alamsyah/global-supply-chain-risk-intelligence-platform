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
        Schema::create('risk_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Komponen Risk
            $table->decimal('weather_risk', 5, 2)->default(0);
            $table->decimal('inflation_risk', 5, 2)->default(0);
            $table->decimal('news_risk', 5, 2)->default(0);
            $table->decimal('currency_risk', 5, 2)->default(0);

            // Total Risk
            $table->decimal('total_risk_score', 5, 2)->default(0);

            // Level Risk
            $table->enum('risk_level', [
                'Low',
                'Medium',
                'High'
            ])->default('Low');

            // Waktu Perhitungan
            $table->timestamp('calculated_at');

            $table->timestamps();

            // Index
            $table->index('country_id');
            $table->index('calculated_at');
            $table->index('risk_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_histories');
    }
};