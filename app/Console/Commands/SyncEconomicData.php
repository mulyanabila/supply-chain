<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\EconomicData;
use Illuminate\Support\Facades\Http;

class SyncEconomicData extends Command
{
    /**
     * Nama command
     */
    protected $signature = 'economic:sync';

    /**
     * Deskripsi command
     */
    protected $description = 'Sinkronisasi Economic Data dari World Bank';

    /**
     * Jalankan command
     */
    public function handle()
    {
        $this->info('');
        $this->info('========================================');
        $this->info(' Global Supply Chain');
        $this->info(' Economic Data Synchronization');
        $this->info('========================================');
        $this->info('');

        $countries = Country::whereNotNull('iso3')
            ->orderBy('country_name')
            ->get();

        $this->info('Total Negara : '.$countries->count());
        $this->newLine();

        $bar = $this->output->createProgressBar($countries->count());

        $bar->start();

        foreach ($countries as $country) {

            $this->syncCountry($country);

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);

        $this->info('========================================');
        $this->info(' Sinkronisasi Economic Data Selesai');
        $this->info('========================================');

        return self::SUCCESS;
    }

    /**
     * Sinkronisasi seluruh indikator
     */
private function syncCountry($country)
{
    $gdp = $this->getIndicator(
        $country->iso3,
        'NY.GDP.MKTP.CD'
    );

    $inflation = $this->getIndicator(
        $country->iso3,
        'FP.CPI.TOTL.ZG'
    );

    $exports = $this->getIndicator(
        $country->iso3,
        'NE.EXP.GNFS.CD'
    );

    $imports = $this->getIndicator(
        $country->iso3,
        'NE.IMP.GNFS.CD'
    );

    // Ambil semua tahun yang ada
    $years = array_unique(array_merge(
        array_keys($gdp),
        array_keys($inflation),
        array_keys($exports),
        array_keys($imports)
    ));

    rsort($years);

    // Simpan hanya 5 tahun terakhir
    $years = array_slice($years, 0, 5);

    foreach ($years as $year) {

        EconomicData::updateOrCreate(

            [
                'country_id' => $country->id,
                'year' => $year
            ],

            [
                'gdp' => $gdp[$year] ?? null,
                'inflation' => $inflation[$year] ?? null,
                'exports' => $exports[$year] ?? null,
                'imports' => $imports[$year] ?? null,
            ]

        );
    }
}

private function getIndicator($iso3, $indicator)
{
    $url = "https://api.worldbank.org/v2/country/{$iso3}/indicator/{$indicator}";

    try {

        $response = Http::withoutVerifying()
            ->timeout(60)
            ->get($url, [
                'format' => 'json',
                'per_page' => 5
            ]);

        if (!$response->successful()) {
            return [];
        }

        $json = $response->json();

        if (!isset($json[1])) {
            return [];
        }

        $data = [];

        foreach ($json[1] as $row) {

            if (
                !empty($row['date']) &&
                $row['value'] !== null
            ) {

                $data[$row['date']] = $row['value'];

            }

        }

        return $data;

    } catch (\Exception $e) {

        return [];

    }
}

}