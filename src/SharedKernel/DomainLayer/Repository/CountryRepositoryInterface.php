<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Repository;

use Illuminate\Support\Collection;
use Src\SharedKernel\DomainLayer\Entities\Country;

interface CountryRepositoryInterface
{
    public function getAllCountries(): Collection;

    public function findByIso2(string $iso2): ?Country;

    public function findById(int $id): ?Country;

    public function findByName(string $name): ?Country;

    public function findByNative(string $native): ?Country;
}
