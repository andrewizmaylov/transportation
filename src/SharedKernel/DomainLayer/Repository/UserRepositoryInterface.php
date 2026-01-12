<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Repository;

use Src\SharedKernel\DomainLayer\Entities\UserEntity;

interface UserRepositoryInterface
{
    public function getCurrentUser(): ?UserEntity;
}
