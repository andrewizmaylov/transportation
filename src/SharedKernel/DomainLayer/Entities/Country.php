<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Entities;

readonly class Country
{
    public function __construct(
        public int $id,
        public string $iso2,
        public string $name,
    ) {}
}
