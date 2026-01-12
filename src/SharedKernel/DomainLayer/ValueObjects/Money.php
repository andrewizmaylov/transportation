<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Src\SharedKernel\DomainLayer\Repository\CurrencyRepositoryInterface;

final class Money
{
    private Collection $currenciesCollection;

    public function __construct(
        public int $amount,
        public ?Currency $currency = null,
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        $this->currency = $this->currency ?? new Currency('RUB');

        $currencyRepository = app(CurrencyRepositoryInterface::class);
        $this->currenciesCollection = $currencyRepository->getAllCurrencies();
        $currencyCodes = array_column($this->currenciesCollection->toArray(), 'code');
        if (! in_array($this->currency->value(), $currencyCodes)) {
            throw new InvalidArgumentException('Invalid currency: ' . $this->currency->value());
        }
    }

    public function add(self $other): self
    {
        if ($this->currency->value() !== $other->currency->value()) {
            throw new InvalidArgumentException('Cannot add different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    private function withCurrency(string $currency): self
    {
        return new self(
            $this->amount,
            new Currency($currency)
        );
    }

    public function getPriceString(): string
    {
        $currencySymbol = $this->currenciesCollection->where('code', $this->currency->value())->first()->symbol;

        return number_format($this->amount / 100, 2, ',', ' ') . ' ' . $currencySymbol;
    }
}
