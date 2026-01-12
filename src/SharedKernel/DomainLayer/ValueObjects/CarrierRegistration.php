<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use Src\SharedKernel\DomainLayer\Entities\Ids\CarrierRegistrationId;

readonly class CarrierRegistration
{
    public function __construct(
        public CarrierRegistrationId $id,
    ) {}
}
