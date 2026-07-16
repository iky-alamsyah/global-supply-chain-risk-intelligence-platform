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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name');
            $table->string('official_name')->nullable();
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique();
            $table->string('numeric_code',3)->nullable();

            // Region Information
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->string('capital')->nullable();

            // Geographic
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Country Details
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol', 10)->nullable();

            $table->string('flag')->nullable();

$table->string('flag_png')->nullable();

$table->string('flag_svg')->nullable();

$table->json('languages')->nullable();

            $table->bigInteger('population')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index
            $table->index('name');
            $table->index('region');
            $table->index('currency_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};