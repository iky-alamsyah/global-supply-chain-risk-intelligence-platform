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
        Schema::create('ports', function (Blueprint $table) {

            $table->id();

            // Relasi ke countries
            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Informasi Pelabuhan
            $table->string('port_name');
            $table->string('port_code')->nullable();
            $table->string('city')->nullable();

            // Lokasi
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Informasi tambahan
            $table->string('timezone')->nullable();
            $table->string('status')->default('active');

            $table->text('description')->nullable();

            $table->timestamps();

            // Index
            $table->index('country_id');
            $table->index('port_name');
            $table->index('city');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};