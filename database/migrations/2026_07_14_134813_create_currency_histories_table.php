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
        Schema::create('currency_histories', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
                
            $table->string('base_currency', 10);
            $table->string('target_currency', 10)->default('USD');
            $table->decimal('old_rate', 18, 6)->nullable();
            $table->decimal('new_rate', 18, 6);
            $table->decimal('change_percentage', 8, 2)->nullable();
            
            $table->timestamps();
            
            $table->index('country_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_histories');
    }
};
