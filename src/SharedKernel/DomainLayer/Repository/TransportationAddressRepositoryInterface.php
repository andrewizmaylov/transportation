<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Repository;

use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\TransportationAddress as TransportationAddressEntity;

interface TransportationAddressRepositoryInterface
{
    public function findById(TransportationAddressId $transportationId): ?TransportationAddressEntity;
}
