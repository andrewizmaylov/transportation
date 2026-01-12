<?php

declare(strict_types=1);

namespace Src\FinancialInformation\DomainLayer\Entities;

use Src\SharedKernel\DomainLayer\Entities\Ids\FinancialInformationId;

readonly class FinancialInformation
{
    public function __construct(
        public FinancialInformationId $id,
    ) {}
}
