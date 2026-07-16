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
        Schema::create('weather_cache', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Data Cuaca
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('rainfall', 8, 2)->nullable();
            $table->decimal('wind_speed', 8, 2)->nullable();
            $table->decimal('storm_probability', 5, 2)->nullable();

            // Weather Risk (0-100)
            $table->decimal('weather_risk_score', 5, 2)->default(0);

            // Waktu data diambil dari API
            $table->timestamp('weather_time')->nullable();

            // Cache Expired
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('country_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_cache');
    }
};