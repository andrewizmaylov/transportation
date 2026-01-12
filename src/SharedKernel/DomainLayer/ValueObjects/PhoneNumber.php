<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\ValueObjects;

use Brick\PhoneNumber\PhoneNumber as BrickPhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use DomainException;

readonly class PhoneNumber
{
    public string $number;

    /**
     * @throws PhoneNumberParseException
     */
    public function __construct(
        public string $numberInput,
    ) {
        $number = BrickPhoneNumber::parse($this->numberInput);
        if (! $number->isValidNumber()) {
            throw new DomainException('Invalid phone number entered');
        }

        $this->number = $number->format(PhoneNumberFormat::INTERNATIONAL);
    }
}
