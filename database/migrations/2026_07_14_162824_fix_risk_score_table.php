<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {

            if (!Schema::hasColumn('risk_scores', 'country_id')) {
                $table->foreignId('country_id')
                      ->nullable()
                      ->after('id');
            }

            if (!Schema::hasColumn('risk_scores', 'weather_score')) {
                $table->double('weather_score')->default(0);
            }

            if (!Schema::hasColumn('risk_scores', 'inflation_score')) {
                $table->double('inflation_score')->default(0);
            }

            if (!Schema::hasColumn('risk_scores', 'news_score')) {
                $table->double('news_score')->default(0);
            }

            if (!Schema::hasColumn('risk_scores', 'currency_score')) {
                $table->double('currency_score')->default(0);
            }

            if (!Schema::hasColumn('risk_scores', 'total_score')) {
                $table->double('total_score')->default(0);
            }

            if (!Schema::hasColumn('risk_scores', 'risk_level')) {
                $table->string('risk_level')->default('Low Risk');
            }

        });
    }

    public function down(): void
    {
        //
    }
};