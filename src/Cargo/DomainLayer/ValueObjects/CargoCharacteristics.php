<?php

declare(strict_types=1);

namespace Src\Cargo\DomainLayer\ValueObjects;

use DomainException;
use Src\SharedKernel\DomainLayer\ValueObjects\Money;

readonly class CargoCharacteristics
{
    public function __construct(
        public string $name,
        public int $length,
        public int $width,
        public int $height,
        public int $weight,
        public Money $price,
    ) {
        if ($this->length < 1 || $this->width < 1 || $this->height < 1) {
            throw new DomainException('Cargo spatial characteristics must be specified');
        }
        if ($this->weight < 1) {
            throw new DomainException('Cargo weight must be specified');
        }
        if (strlen($this->name) > 255) {
            throw new DomainException('Cargo name cannot exceed 255 characters');
        }
    }
}
