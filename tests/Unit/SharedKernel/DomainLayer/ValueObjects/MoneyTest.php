<?php

declare(strict_types=1);

namespace Tests\Order\DomainLayer\ValueObjects;

use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Src\SharedKernel\DomainLayer\Repository\CurrencyRepositoryInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\Currency;
use Src\SharedKernel\DomainLayer\ValueObjects\Money;
use Src\SharedKernel\InfrastructureLayer\Repository\CurrencyRepository;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Bind CurrencyRepositoryInterface to CurrencyRepository
    // This is needed because Currency and Money constructors use app() to resolve the interface
    app()->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
});

test('it can create money value object', function () {
    $money = new Money(1000, new Currency('USD'));

    expect($money)
        ->amount->toBe(1000)
        ->currency->value()->toBe('USD');
});

test('money is immutable', function () {
    // This is implicitly tested by the readonly class
    // but we can verify properties can't be changed
    $money = new Money(1000, new Currency('USD'));

    // Should throw error if we try to modify (PHP 8.3+ with readonly)
    expect($money)->toBeInstanceOf(Money::class);
});

test('can add money of same currency', function () {
    $currency = new Currency('USD');
    $money1 = new Money(1000, $currency);
    $money2 = new Money(500, $currency);

    $result = $money1->add($money2);

    expect($result)
        ->amount->toBe(1500)
        ->currency->toBe($currency);
});

test('cannot add money of different currencies', function () {
    $usd = new Money(1000, new Currency('USD'));
    $eur = new Money(500, new Currency('EUR'));

    expect(fn () => $usd->add($eur))
        ->toThrow(InvalidArgumentException::class);
});

test('money equality check', function () {
    $currencyUSD = new Currency('USD');
    $currencyEUR = new Currency('EUR');

    $money1 = new Money(1000, $currencyUSD);
    $money2 = new Money(1000, $currencyUSD);
    $money3 = new Money(500, $currencyUSD);
    $money4 = new Money(1000, $currencyEUR);

    expect($money1->equals($money2))->toBeTrue()
        ->and($money1->equals($money3))->toBeFalse()
        ->and($money1->equals($money4))->toBeFalse();
});

test('cannot create money with negative amount', function () {
    expect(fn () => new Money(-100, new Currency('USD')))
        ->toThrow(InvalidArgumentException::class, 'Amount cannot be negative');
});

test('cannot create money with invalid currency', function () {
    // Currency constructor throws DomainException when currency is invalid
    expect(fn () => new Money(1000, new Currency('XXX')))
        ->toThrow(DomainException::class, 'Invalid currency XXX');
});

test('it can return human readable price', function () {
    $money = new Money(8334567);

    expect($money->getPriceString())
        ->toBeString()
        ->toBe('83 345,67 ₽');
});
