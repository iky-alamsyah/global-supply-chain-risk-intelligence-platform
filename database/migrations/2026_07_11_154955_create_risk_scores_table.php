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
        Schema::create('risk_scores', function (Blueprint $table) {

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

            // Kategori Risk
            $table->enum('risk_level', [
                'Low',
                'Medium',
                'High'
            ])->default('Low');

            // Warna Indikator Dashboard
            $table->string('indicator_color', 20)
                ->default('success');

            // Waktu Perhitungan
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();

            // Satu negara hanya memiliki satu skor terbaru
            $table->unique('country_id');

            // Index
            $table->index('risk_level');
            $table->index('total_risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};