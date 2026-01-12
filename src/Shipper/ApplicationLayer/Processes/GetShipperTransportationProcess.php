<?php

declare(strict_types=1);

namespace Src\Shipper\ApplicationLayer\Processes;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\Transportation\DomainLayer\Entities\Transportation;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;

readonly class GetShipperTransportationProcess
{
    public function __construct(
        private TransportationRepositoryInterface $transportationRepository,
    ) {}

    public function execute(TransportationId $transportationId): ?Transportation
    {
        return $this->transportationRepository->findById($transportationId) ?? null;
    }
}
