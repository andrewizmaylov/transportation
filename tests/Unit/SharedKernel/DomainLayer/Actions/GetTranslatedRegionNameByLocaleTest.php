<?php

declare(strict_types=1);

namespace Tests\Unit\SharedKernel\DomainLayer\Actions;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Src\SharedKernel\DomainLayer\Actions\GetTranslatedRegionNameByLocale;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;
use Src\SharedKernel\InfrastructureLayer\Repository\CityRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CountryRepository;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    app()->bind(CityRepositoryInterface::class, CityRepository::class);
    app()->bind(CountryRepositoryInterface::class, CountryRepository::class);
    app()->bind(ConnectionInterface::class, fn () => DB::connection());
});

test('it can work with translation string from city', function () {
    $cityRepository = app()->make(CityRepositoryInterface::class);
    $city = $cityRepository->getAllCities()->where('name', 'Екатеринбург')->first();

    expect(GetTranslatedRegionNameByLocale::translate($city->translations, 'EN'))
        ->toBe('Yekaterinburg')
        ->and(GetTranslatedRegionNameByLocale::translate($city->translations, 'RU'))
        ->toBe('Екатеринбург');
});

test('it can work with translation string from country', function () {
    $countryRepository = app()->make(CountryRepositoryInterface::class);
    $country = $countryRepository->getAllCountries()->where('iso2', 'RU')->first();

    expect(GetTranslatedRegionNameByLocale::translate($country->translations, 'EN'))
        ->toBe('Rusia')
        ->and(GetTranslatedRegionNameByLocale::translate($country->translations, 'RU'))
        ->toBe('Россия');
});
