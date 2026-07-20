<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('risk_scores', 'recorded_date')) {
                $table->date('recorded_date')->nullable()->after('risk_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            if (Schema::hasColumn('risk_scores', 'recorded_date')) {
                $table->dropColumn('recorded_date');
            }
        });
    }
};
