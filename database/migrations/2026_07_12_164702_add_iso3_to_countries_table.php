<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {

            $table->string('iso3',3)
                  ->nullable()
                  ->after('country_code');

        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {

            $table->dropColumn('iso3');

        });
    }
};