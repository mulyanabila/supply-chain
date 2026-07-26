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
            $table->string('thumbnail')->nullable()->after('category');
            $table->string('country')->nullable()->after('thumbnail');
            $table->string('source')->nullable()->after('country');
            $table->string('sentiment')->nullable()->after('source');
            $table->string('risk_level')->nullable()->after('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'country', 'source', 'sentiment', 'risk_level']);
        });
    }
};
