<?php

declare(strict_types=1);

namespace Src\Cargo\DomainLayer\Storage;

use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

interface CargoStorageInterface
{
    public function addNewCargo(TransportationId $transportationId, CargoCharacteristics $cargoCharacteristics): CargoId;

    public function updateCargo(CargoId $cargoId, CargoCharacteristics $cargoCharacteristics): void;

    public function deleteCargo(CargoId $cargoId): void;
}
