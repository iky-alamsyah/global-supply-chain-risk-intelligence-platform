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
        Schema::create('watchlists', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Target Risk yang ingin dipantau user
            $table->decimal('target_risk_score', 5, 2)
                ->default(50);

            // Aktif / Tidak
            $table->boolean('notification_enabled')
                ->default(true);

            // Catatan User
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'country_id']);

            $table->index('user_id');
            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};