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
        Schema::create('weather_alerts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
                
            $table->string('severity', 20); // CRITICAL, HIGH, MEDIUM, LOW
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->string('weather_condition')->nullable();
            
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            
            $table->index('country_id');
            $table->index('severity');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_alerts');
    }
};
