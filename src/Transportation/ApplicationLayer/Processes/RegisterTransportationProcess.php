<?php

declare(strict_types=1);

namespace Src\Transportation\ApplicationLayer\Processes;

use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Transportation\DomainLayer\Entities\Transportation;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;
use Src\Transportation\DomainLayer\Storage\TransportationStorageInterface;

readonly class RegisterTransportationProcess
{
    public function __construct(
        private TransportationStorageInterface $transportationStorage,
        private TransportationRepositoryInterface $transportationRepository,
    ) {}

    public function execute(
        string $name,
        DateTimeInterval $pickupDateInterval,
    ): Transportation {
        $id = $this->transportationStorage->createTransportation(
            name: $name,
            pickupDateInterval: $pickupDateInterval,
        );

        $transportation = $this->transportationRepository->findById($id);

        if ($transportation === null) {
            throw new \RuntimeException('Transportation was not found after creation');
        }

        return $transportation;
    }
}
