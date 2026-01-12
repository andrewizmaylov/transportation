<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use DomainException;
use Src\SharedKernel\DomainLayer\Repository\CurrencyRepositoryInterface;

readonly class Currency
{
    public function __construct(
        private string $currency,
    ) {
        $currencyRepository = app(CurrencyRepositoryInterface::class);
        $currencies = array_column($currencyRepository->getAllCurrencies()->toArray(), 'code');

        if (! in_array($currency, $currencies)) {
            throw new DomainException('Invalid currency ' . $this->currency);
        }
    }

    public function value(): string
    {
        return $this->currency;
    }
}
