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
            $table->string('title')->after('id');
            $table->text('content')->after('title');
            $table->string('author')->after('content');
            $table->string('category')->nullable()->after('author');
            $table->timestamp('published_at')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'author', 'category', 'published_at']);
        });
    }
};
