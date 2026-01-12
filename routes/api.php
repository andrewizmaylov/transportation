<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\SharedKernel\InfrastructureLayer\Repository\CityRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CountryRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CurrencyRepository;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers\CityByCountryListController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('countries', function () {
    $data = app(CountryRepository::class)->getAllCountries();

    return array_values($data->toArray());
});

Route::get('cities', CityByCountryListController::class);

Route::get('currencies', function () {
    return app(CurrencyRepository::class)->getAllCurrencies();
});
