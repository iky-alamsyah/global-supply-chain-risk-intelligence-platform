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
        Schema::table('articles', function (Blueprint $table) {
            // Drop foreign keys if they exist
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add author_id
            $table->foreignId('author_id')
                ->after('id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Rename excerpt to summary
            $table->dropColumn('excerpt');
            $table->text('summary')->nullable()->after('slug');

            // Change status to support archived
            $table->string('status')->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->dropColumn('summary');
            $table->text('excerpt')->nullable();
        });
    }
};
