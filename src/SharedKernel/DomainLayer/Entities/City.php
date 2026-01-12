<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Entities;

readonly class City
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
