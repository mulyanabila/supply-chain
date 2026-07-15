<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {

            $table->foreignId('country_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('port_name')->after('country_id');

            $table->string('city')->nullable()->after('port_name');

            $table->double('latitude')->after('city');

            $table->double('longitude')->after('latitude');

            $table->string('type')->default('Seaport')->after('longitude');

            $table->string('status')->default('Normal')->after('type');

        });
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {

            $table->dropForeign(['country_id']);

            $table->dropColumn([
                'country_id',
                'port_name',
                'city',
                'latitude',
                'longitude',
                'type',
                'status'
            ]);

        });
    }
};