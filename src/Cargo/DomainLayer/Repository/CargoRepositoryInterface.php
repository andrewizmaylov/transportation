<?php

declare(strict_types=1);

namespace Src\Cargo\DomainLayer\Repository;

use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;

interface CargoRepositoryInterface
{
    public function findById(CargoId $cargoId): ?CargoEntity;
}
