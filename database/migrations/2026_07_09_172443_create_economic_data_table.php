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
        Schema::create('economic_data', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->onDelete('cascade');
        $table->decimal('gdp',18,2)->nullable();
        $table->decimal('inflation',8,2)->nullable();
        $table->decimal('exports',18,2)->nullable();
        $table->decimal('imports',18,2)->nullable();
        $table->year('year');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_data');
    }
};
