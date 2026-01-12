<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Repository;

use Illuminate\Support\Collection;

interface CurrencyRepositoryInterface
{
    public function getAllCurrencies(): Collection;
}
