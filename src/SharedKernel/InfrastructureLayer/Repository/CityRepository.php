<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Repository;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Src\SharedKernel\DomainLayer\Actions\GetTranslatedRegionNameByLocale;
use Src\SharedKernel\DomainLayer\Entities\City;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;

readonly class CityRepository implements CityRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function getAllCities(): Collection
    {
        return Cache::rememberForever('allCities', function () {
            return $this->connection->table('cities')
                ->select(['id', 'name', 'native', 'translations'])
                ->orderBy('name')
                ->get()
                ->each(static function ($city) {
                    $city->name = GetTranslatedRegionNameByLocale::translate($city->translations);
                })
                ->keyBy('id');
        });
    }

    public function findById(int $id): ?City
    {
        $city = $this->connection->table('cities')
            ->select(['id', 'name', 'native', 'translations'])
            ->where('id', $id)
            ->first();

        if ($city === null) {
            return null;
        }

        return $this->composeCityEntity($city);
    }

    public function findByName(string $name): ?City
    {
        $city = $this->connection->table('cities')
            ->select(['id', 'name', 'native', 'translations'])
            ->where('name', $name)
            ->first();

        if ($city === null) {
            return null;
        }

        return $this->composeCityEntity($city);
    }

    public function findByNative(string $native): ?City
    {
        $city = $this->connection->table('cities')
            ->select(['id', 'name', 'native', 'translations'])
            ->where('native', $native)
            ->first();

        if ($city === null) {
            return null;
        }

        return $this->composeCityEntity($city);
    }

    public function composeCityEntity(object $city): City
    {
        return new City(
            $city->id,
            GetTranslatedRegionNameByLocale::translate($city->translations)
        );
    }

    public function getAllCitiesByCountry(int $countryId): Collection
    {
        return Cache::rememberForever('allCities_' . $countryId, function () use ($countryId) {
            return $this->connection->table('cities')
                ->select(['id', 'name', 'native', 'translations'])
                ->orderBy('name')
                ->where('country_id', $countryId)
                ->get()
                ->each(static function ($city) {
                    $city->name = GetTranslatedRegionNameByLocale::translate($city->translations);
                })
                ->keyBy('id');
        });
    }
}
