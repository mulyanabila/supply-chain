<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            if (!Schema::hasColumn('exchange_rates', 'country_id')) {
                $table->foreignId('country_id')->nullable()->constrained()->cascadeOnDelete();
                $table->string('currency_code', 10)->nullable();
                $table->decimal('exchange_rate_to_usd', 15, 4)->nullable();
                $table->string('trend', 20)->nullable(); // e.g., 'Up', 'Down', 'Stable'
                $table->decimal('change_24h', 8, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'currency_code', 'exchange_rate_to_usd', 'trend', 'change_24h']);
        });
    }
};
