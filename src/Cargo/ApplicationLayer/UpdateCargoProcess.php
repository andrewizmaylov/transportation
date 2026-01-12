<?php

declare(strict_types=1);

namespace Src\Cargo\ApplicationLayer;

use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\Cargo\DomainLayer\Repository\CargoRepositoryInterface;
use Src\Cargo\DomainLayer\Storage\CargoStorageInterface;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;

readonly class UpdateCargoProcess
{
    public function __construct(
        private CargoStorageInterface $storage,
        private CargoRepositoryInterface $repository,
    ) {}

    public function execute(
        CargoId $cargoId,
        CargoCharacteristics $cargoCharacteristics
    ): CargoEntity {
        $this->storage->updateCargo($cargoId, $cargoCharacteristics);

        return $this->repository->findById($cargoId);
    }
}
