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
        Schema::create('country_risk_scores', function (Blueprint $table) {

    $table->id();

    $table->foreignId('country_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->decimal('gdp_score',5,2)->default(0);

    $table->decimal('inflation_score',5,2)->default(0);

    $table->decimal('weather_score',5,2)->default(0);

    $table->decimal('currency_score',5,2)->default(0);

    $table->decimal('news_score',5,2)->default(0);

    $table->decimal('risk_score',5,2)->default(0);

    $table->enum('risk_level',[
        'LOW',
        'MEDIUM',
        'HIGH'
    ]);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_risk_scores');
    }
};
