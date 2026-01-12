<?php

declare(strict_types=1);

namespace Src\Shipper\ApplicationLayer\Processes;

use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\Transportation\DomainLayer\Repository\TransportationRepositoryInterface;

readonly class GetShipperTransportationListProcess
{
    public function __construct(
        private TransportationRepositoryInterface $transportationRepository,
    ) {}

    public function execute(
        int $page,
        int $perPage,
        array $filter
    ): PaginatedResult {
        return $this->transportationRepository->getAllWithPagination(page: $page, perPage: $perPage, filter: $filter);
    }
}
