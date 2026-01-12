<?php

declare(strict_types=1);

namespace Src\Transportation\DomainLayer\Repository;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;
use Src\Transportation\DomainLayer\Entities\Transportation;

interface TransportationRepositoryInterface
{
    public function findById(TransportationId $id): ?Transportation;

    public function getAllWithPagination(int $page, int $perPage, ?array $filter = null): PaginatedResult;
}
