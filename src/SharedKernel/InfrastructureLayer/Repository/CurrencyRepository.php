<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Repository;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Src\SharedKernel\DomainLayer\Repository\CurrencyRepositoryInterface;

readonly class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function getAllCurrencies(): Collection
    {
        return Cache::rememberForever(
            'currencies.all',
            fn () => $this->connection
                ->table('currencies')
                ->get()
        );
    }
}
