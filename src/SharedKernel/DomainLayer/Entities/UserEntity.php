<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Entities;

final readonly class UserEntity
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public bool $isShipper,
        public bool $isCarrier,
    ) {}
}
