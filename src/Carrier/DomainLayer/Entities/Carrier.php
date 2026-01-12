<?php

declare(strict_types=1);

namespace Src\Carrier\DomainLayer\Entities;

use Src\SharedKernel\DomainLayer\Entities\Ids\CarrierId;

readonly class Carrier
{
    public function __construct(
        public CarrierId $id,
    ) {}
}
