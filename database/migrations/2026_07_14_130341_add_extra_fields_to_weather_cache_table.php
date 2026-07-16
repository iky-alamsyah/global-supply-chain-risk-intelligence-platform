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
        Schema::table('weather_cache', function (Blueprint $table) {
            $table->integer('humidity')->nullable()->after('temperature');
            $table->decimal('pressure', 8, 2)->nullable()->after('humidity');
            $table->string('weather_main')->nullable()->after('pressure');
            $table->string('weather_description')->nullable()->after('weather_main');
            $table->integer('cloud')->nullable()->after('weather_description');
            $table->integer('weather_code')->nullable()->after('cloud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weather_cache', function (Blueprint $table) {
            $table->dropColumn([
                'humidity',
                'pressure',
                'weather_main',
                'weather_description',
                'cloud',
                'weather_code',
            ]);
        });
    }
};
