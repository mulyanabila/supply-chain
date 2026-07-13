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
    Schema::create('risk_scores', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
              ->constrained()
              ->onDelete('cascade');

        $table->decimal('economic_score',5,2)->default(0);
        $table->decimal('news_score',5,2)->default(0);
        $table->decimal('weather_score',5,2)->default(0);
        $table->decimal('final_score',5,2)->default(0);

        $table->enum('risk_level',[
            'Low',
            'Medium',
            'High'
        ])->default('Low');

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
