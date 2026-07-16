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
        Schema::create('country_statistics', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Tahun Data Statistik
            $table->year('year');

            $table->date('record_date')->nullable();
            
            // Data Ekonomi
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('inflation', 8, 2)->nullable();

            // Perdagangan
            $table->decimal('export_value', 20, 2)->nullable();
            $table->decimal('import_value', 20, 2)->nullable();

            // Demografi
            $table->bigInteger('population')->nullable();

            // Metadata
            $table->string('data_source')->default('World Bank');

            $table->timestamps();

            // Index
            $table->unique(['country_id', 'year']);

            $table->index('year');
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_statistics');
    }
};