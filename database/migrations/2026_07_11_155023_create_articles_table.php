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
        Schema::create('articles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->string('title');

            $table->string('slug')->unique();

            $table->text('excerpt')->nullable();

            $table->longText('content');

            $table->string('meta_description')->nullable();

            $table->enum('status', [
                'draft',
                'published'
            ])->default('draft');

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};