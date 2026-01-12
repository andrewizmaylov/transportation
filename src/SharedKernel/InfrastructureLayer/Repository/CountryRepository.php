<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Repository;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Src\SharedKernel\DomainLayer\Actions\GetTranslatedRegionNameByLocale;
use Src\SharedKernel\DomainLayer\Entities\Country;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;

readonly class CountryRepository implements CountryRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function getAllCountries(): Collection
    {
        return Cache::rememberForever('allCountries', function () {
            return $this->connection->table('countries')
                ->select(['id', 'name', 'iso2', 'currency_symbol', 'native', 'translations'])
                ->orderBy('name')
                ->get()
                ->each(static function ($country) {
                    $country->name = GetTranslatedRegionNameByLocale::translate($country->translations);
                })
                ->keyBy('iso2');
        });
    }

    public function findByIso2(string $iso2): ?Country
    {
        $record = $this->connection->table('countries')
            ->select(['id', 'name', 'iso2', 'currency_symbol', 'native', 'translations'])
            ->where('iso2', $iso2)
            ->first();

        if (! $record) {
            return null;
        }

        return $this->composeCountryEntity($record);
    }

    public function findById(int $id): ?Country
    {
        $record = $this->connection->table('countries')
            ->select(['id', 'name', 'iso2', 'currency_symbol', 'native', 'translations'])
            ->where('id', $id)
            ->first();

        if (! $record) {
            return null;
        }

        return $this->composeCountryEntity($record);
    }

    public function findByName(string $name): ?Country
    {
        $record = $this->connection->table('countries')
            ->select(['id', 'name', 'iso2', 'currency_symbol', 'native', 'translations'])
            ->where('name', $name)
            ->first();

        if (! $record) {
            return null;
        }

        return $this->composeCountryEntity($record);
    }

    public function findByNative(string $native): ?Country
    {
        $record = $this->connection->table('countries')
            ->select(['id', 'name', 'iso2', 'currency_symbol', 'native', 'translations'])
            ->where('native', $native)
            ->first();

        if (! $record) {
            return null;
        }

        return $this->composeCountryEntity($record);
    }

    public function composeCountryEntity(object $record): Country
    {
        return new Country(
            $record->id,
            $record->iso2,
            GetTranslatedRegionNameByLocale::translate($record->translations)
        );
    }
}
