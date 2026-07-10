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
       Schema::create('weather', function (Blueprint $table) {
        $table->id();
        $table->foreignId('country_id')->constrained()->onDelete('cascade');
        $table->decimal('temperature',5,2)->nullable();
        $table->decimal('rainfall',5,2)->nullable();
        $table->decimal('wind_speed',5,2)->nullable();
        $table->string('storm_risk')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather');
    }
};
