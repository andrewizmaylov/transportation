<?php

declare(strict_types=1);

namespace Src\Transportation\ApplicationLayer\Processes;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Transportation\DomainLayer\Entities\Transportation;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;
use Src\Transportation\DomainLayer\Storage\TransportationStorageInterface;

readonly class UpdateTransportationProcess
{
    public function __construct(
        private TransportationRepositoryInterface $transportationRepository,
        private TransportationStorageInterface $transportationStorage,
    ) {}

    public function execute(
        TransportationId $transportationId,
        DateTimeInterval $pickupDateInterval,
        ?string $name,
    ): Transportation {
        $this->transportationStorage->updateTransportation(
            $transportationId,
            $name,
            $pickupDateInterval->from,
            $pickupDateInterval->to,
        );

        return $this->transportationRepository->findById($transportationId);
    }
}
