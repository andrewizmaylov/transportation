<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Repository;

use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Src\SharedKernel\DomainLayer\Entities\UserEntity;
use Src\SharedKernel\DomainLayer\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected string $tableName;

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
        $this->tableName = User::getTableName();
    }

    public function getCurrentUser(): ?UserEntity
    {
        $userId = auth()->id();
        if (is_null($userId)) {
            return null;
        }

        $user = $this->connection
            ->table($this->tableName)
            ->find($userId);

        return new UserEntity(
            userId: $user->id,
            name: $user->name,
            email: $user->email,
            isShipper: true,
            isCarrier: false,
        );
    }
}
