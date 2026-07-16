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
        Schema::table('currency_cache', function (Blueprint $table) {
            $table->decimal('previous_exchange_rate', 18, 6)->nullable()->after('exchange_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currency_cache', function (Blueprint $table) {
            $table->dropColumn('previous_exchange_rate');
        });
    }
};
