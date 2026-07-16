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
        Schema::table('favorites', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['user_id', 'country_id']);

            // Modify country_id to be nullable
            $table->foreignId('country_id')
                ->nullable()
                ->change();

            // Add port_id and news_cache_id columns
            $table->foreignId('port_id')
                ->nullable()
                ->constrained('ports')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('news_cache_id')
                ->nullable()
                ->constrained('news_cache')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Add new unique constraints
            // We use raw queries or unique definitions.
            $table->unique(['user_id', 'country_id'], 'fav_user_country_unique');
            $table->unique(['user_id', 'port_id'], 'fav_user_port_unique');
            $table->unique(['user_id', 'news_cache_id'], 'fav_user_news_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('fav_user_news_unique');
            $table->dropUnique('fav_user_port_unique');
            $table->dropUnique('fav_user_country_unique');

            $table->dropForeign(['news_cache_id']);
            $table->dropColumn('news_cache_id');

            $table->dropForeign(['port_id']);
            $table->dropColumn('port_id');

            $table->foreignId('country_id')
                ->nullable(false)
                ->change();

            $table->unique(['user_id', 'country_id']);
        });
    }
};
