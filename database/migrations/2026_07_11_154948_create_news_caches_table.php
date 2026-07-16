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
        Schema::create('news_cache', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Informasi Berita
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();

            // Sumber Berita
            $table->string('source')->nullable();
            $table->string('api_source')->default('GNews');
            $table->string('author')->nullable();

            // URL
            $table->string('url')->unique();
            $table->string('image_url')->nullable();

            // Kategori
            $table->enum('category', [
                'economy',
                'trade',
                'shipping',
                'logistics'
            ]);

            // Tanggal Publikasi
            $table->timestamp('published_at')->nullable();

            // Lexicon Sentiment
            $table->integer('positive_score')->default(0);
            $table->integer('negative_score')->default(0);

            $table->enum('sentiment', [
                'positive',
                'neutral',
                'negative'
            ])->default('neutral');

            // Risk News
            $table->decimal('news_risk_score', 5, 2)
                ->default(0);

            // Cache Expired
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Index
            $table->index('country_id');
            $table->index('category');
            $table->index('sentiment');
            $table->index('published_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_cache');
    }
};