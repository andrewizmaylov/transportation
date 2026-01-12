<?php

declare(strict_types=1);

namespace Src\Cargo\ApplicationLayer;

use App\Exceptions\BusinessException;
use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\Cargo\DomainLayer\Repository\CargoRepositoryInterface;
use Src\Cargo\DomainLayer\Storage\CargoStorageInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

readonly class DeleteCargoProcess
{
    public function __construct(
        private CargoStorageInterface $storage,
        private CargoRepositoryInterface $repository,
    ) {}

    /**
     * @throws BusinessException
     */
    public function execute(
        TransportationId $transportationId,
        CargoId $cargoId,
    ): CargoEntity {
        $cargo = $this->repository->findById($cargoId);
        if (! $cargo) {
            throw new BusinessException('Cargo not found');
        }

        if ($cargo->clientId !== auth()->id()) {
            throw new BusinessException('Could\'t delete other user cargo');
        }

        if ($cargo->transportationId->value() !== $transportationId->value()) {
            throw new BusinessException('Could\'t delete cargo from different Transportation');
        }

        $this->storage->deleteCargo($cargoId);

        return $this->repository->findById($cargoId);
    }
}
