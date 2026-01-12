<?php

declare(strict_types=1);

namespace Src\Cargo\ApplicationLayer;

use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\Cargo\DomainLayer\Repository\CargoRepositoryInterface;
use Src\Cargo\DomainLayer\Storage\CargoStorageInterface;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

readonly class AddCargoToTransportationProcess
{
    public function __construct(
        private CargoStorageInterface $storage,
        private CargoRepositoryInterface $repository,
    ) {}

    public function execute(
        TransportationId $transportationId,
        CargoCharacteristics $cargoCharacteristics
    ): CargoEntity {
        $cargoId = $this->storage->addNewCargo($transportationId, $cargoCharacteristics);

        return $this->repository->findById($cargoId);
    }
}
