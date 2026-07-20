<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\RiskScore;
use App\Models\ExchangeRate;
use App\Models\Watchlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some random countries from the database to attach data to
        $countries = Country::limit(5)->get();

        if ($countries->isEmpty()) {
            $this->command->warn('Tabel Country kosong! Harap isi data negara terlebih dahulu sebelum menjalankan seeder ini.');
            return;
        }

        /* 
        |--------------------------------------------------------------------------
        | 1. Seeding Data Historis Risk Score (Untuk Grafik Trend)
        |--------------------------------------------------------------------------
        */
        $this->command->info('Menyiapkan data historis grafik Trend Risk Score...');
        
        // Asumsikan negara pertama adalah patokan untuk global average trend atau kita insert acak
        $trendData = [45, 52, 48, 55, 60, 58, 62];
        $months = [
            Carbon::now()->subMonths(6),
            Carbon::now()->subMonths(5),
            Carbon::now()->subMonths(4),
            Carbon::now()->subMonths(3),
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonths(1),
            Carbon::now(),
        ];

        foreach ($countries as $country) {
            foreach ($trendData as $index => $score) {
                // Berikan sedikit variasi skor antar negara agar tidak sama persis
                $variation = rand(-5, 5); 
                RiskScore::create([
                    'country_id' => $country->id,
                    'total_score' => $score + $variation,
                    'recorded_date' => $months[$index]->format('Y-m-d'),
                    'risk_level' => ($score + $variation) >= 70 ? 'High Risk' : 'Medium Risk'
                ]);
            }
        }

        /* 
        |--------------------------------------------------------------------------
        | 2. Seeding Data Currency Impact (Untuk Tabel Kurs)
        |--------------------------------------------------------------------------
        */
        $this->command->info('Menyiapkan data tabel Exchange Rates...');
        
        $currencies = [
            ['code' => 'EUR', 'rate' => 1.0932, 'change' => 0.25],
            ['code' => 'JPY', 'rate' => 0.0064, 'change' => -0.18],
            ['code' => 'CNY', 'rate' => 0.1382, 'change' => 0.12],
            ['code' => 'IDR', 'rate' => 15923, 'change' => 0.35],
            ['code' => 'GBP', 'rate' => 1.2541, 'change' => -0.05],
        ];

        foreach ($countries as $index => $country) {
            if (isset($currencies[$index])) {
                ExchangeRate::create([
                    'country_id' => $country->id,
                    'currency_code' => $currencies[$index]['code'],
                    'exchange_rate_to_usd' => $currencies[$index]['rate'],
                    'trend' => $currencies[$index]['change'] >= 0 ? 'Up' : 'Down',
                    'change_24h' => $currencies[$index]['change']
                ]);
            }
        }

        /* 
        |--------------------------------------------------------------------------
        | 3. Seeding Data Watchlist
        |--------------------------------------------------------------------------
        */
        $this->command->info('Menyiapkan data tabel Watchlist...');

        // User pertama
        $user = DB::table('users')->first();
        $userId = $user ? $user->id : null;

        if ($userId) {
            foreach ($countries->take(3) as $country) {
                Watchlist::create([
                    'user_id' => $userId,
                    'country_id' => $country->id,
                    'notes' => 'Di-bookmark otomatis oleh sistem untuk pemantauan.'
                ]);
            }
        } else {
            $this->command->warn('Tidak ada User di database, melewati pengisian Watchlist...');
        }

        $this->command->info('Seeding data dummy untuk dashboard selesai!');
    }
}
