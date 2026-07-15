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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/countries', [CountryController::class, 'index'])->name('countries');
    Route::get('/countries/sync', [CountryController::class, 'sync'])->name('countries.sync');
    Route::get('/countries/{country_name}', [CountryController::class, 'show'])->name('countries.show');

    Route::get('/economic-data', [EconomicDataController::class, 'index'])
    ->name('economic.index');

    Route::get('/economic-data/sync', [EconomicDataController::class, 'sync'])
    ->name('economic.sync');

    Route::get('/country/{id}', [DashboardController::class, 'countryDetail'])
    ->name('country.detail');

    Route::get('/weather/{lat}/{lon}',[WeatherController::class,'getWeather']);

    Route::get('/worldbank/{countryCode}',[WorldBankController::class,'getEconomicData'])
    ->middleware('auth')
    ->name('worldbank');

    Route::get('/news/{country}',[NewsController::class,'getNews'])
    ->name('news');

    Route::get('/currency/{code}',[CurrencyController::class,'getCurrency'])
    ->middleware('auth')
    ->name('currency');
});

require __DIR__.'/auth.php';
