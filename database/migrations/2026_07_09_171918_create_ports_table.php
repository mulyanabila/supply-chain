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
    Schema::create('ports', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('port_name');

        $table->string('port_code')->nullable();

        $table->string('city')->nullable();

        $table->decimal('latitude',10,7)->nullable();

        $table->decimal('longitude',10,7)->nullable();

        $table->string('type')->nullable();

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
