<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EconomicDataController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WorldBankController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\GDPController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/shipment', [DashboardController::class, 'shipment'])
    ->middleware(['auth'])
    ->name('shipment');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth'])->group(function () {

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');

Route::get('/admin/users', [AdminDashboardController::class, 'users'])
    ->name('admin.users');

Route::get('/admin/ports', [AdminDashboardController::class, 'ports'])
    ->name('admin.ports');

Route::get('/admin/articles', [AdminDashboardController::class, 'articles'])
    ->name('admin.articles');

    Route::get('/countries', [CountryController::class, 'index'])->name('countries');
    Route::get('/countries/sync', [CountryController::class, 'sync'])->name('countries.sync');
    Route::get('/countries/{country_name}', [CountryController::class, 'show'])->name('countries.show');

    Route::get('/economic-data', [EconomicDataController::class, 'index'])
    ->name('economic.index');

    Route::get('/economic-data/sync', [EconomicDataController::class, 'sync'])
    ->name('economic.sync');

    Route::get('/country/{id}', [DashboardController::class, 'countryDetail'])
    ->name('country.detail');
    
    Route::get('/country/{country}/gdp', [GDPController::class, 'index'])
    ->name('country.gdp');

    Route::get('/weather/{lat}/{lon}',[WeatherController::class,'getWeather'])
    ->where(['lat' => '.*', 'lon' => '.*']);

    Route::get('/weather-monitoring/{country_name?}', [WeatherController::class, 'index'])->name('weather.monitoring');

    Route::get('/worldbank/{countryCode}',[WorldBankController::class,'getEconomicData'])
    ->middleware('auth')
    ->name('worldbank');

    Route::get('/news-intelligence/{country_name?}', [NewsController::class, 'index'])->name('news.index');
    Route::get('/api/news/{country}', [NewsController::class, 'getAdvancedNews']);

    Route::get('/news/{country}',[NewsController::class,'getNews'])
    ->name('news');

    Route::get('/currency/{code}',[CurrencyController::class,'getCurrency'])
    ->middleware('auth')
    ->name('currency');

    Route::get('/ports',[PortController::class,'index'])
    ->name('ports');

    Route::get('/ports/sync', [PortController::class,'sync'])
    ->name('ports.sync');

    Route::get('/watchlist', [WatchlistController::class, 'index'])
    ->name('watchlist.index');

    Route::post('/watchlist', [WatchlistController::class, 'store'])
        ->name('watchlist.store');

    Route::delete('/watchlist/{watchlist}', [WatchlistController::class, 'destroy'])
        ->name('watchlist.destroy');

    Route::get('/comparison', [ComparisonController::class, 'index'])
    ->name('comparison.index');

    Route::post('/comparison', [ComparisonController::class, 'compare'])
    ->name('comparison.compare');
});

require __DIR__.'/auth.php';
