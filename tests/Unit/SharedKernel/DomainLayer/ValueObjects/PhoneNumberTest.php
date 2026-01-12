<?php

declare(strict_types=1);

use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;

test('it can return formatted number', function () {
    $number = new PhoneNumber('+79997654321');

    expect($number)->toBeInstanceOf(PhoneNumber::class)
        ->number->toBe('+7 999 765-43-21')
        ->number->toBeString('+7 999 765-43-21');
});
