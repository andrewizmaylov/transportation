<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Repository;

use Illuminate\Support\Collection;
use Src\SharedKernel\DomainLayer\Entities\City;

interface CityRepositoryInterface
{
    public function getAllCities(): Collection;

    public function getAllCitiesByCountry(int $countryId): Collection;

    public function findById(int $id): ?City;

    public function findByName(string $name): ?City;

    public function findByNative(string $native): ?City;
}
