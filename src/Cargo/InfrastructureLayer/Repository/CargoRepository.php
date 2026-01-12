<?php

declare(strict_types=1);

namespace Src\Cargo\InfrastructureLayer\Repository;

use App\Models\Cargo;
use Illuminate\Database\ConnectionInterface;
use Src\Cargo\DomainLayer\Entities\CargoEntity;
use Src\Cargo\DomainLayer\Repository\CargoRepositoryInterface;
use Src\Cargo\PresentationLayer\HTTP\V1\Responder\CargoResponder;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;

readonly class CargoRepository implements CargoRepositoryInterface
{
    protected string $tableName;

    public function __construct(
        private ConnectionInterface $connection,
        private CargoResponder $cargoResponder,
    ) {
        $this->tableName = Cargo::getTableName();
    }

    public function findById(CargoId $cargoId): ?CargoEntity
    {
        $cargo = $this->connection
            ->table($this->tableName)
            ->find($cargoId->value());

        if (! $cargo) {
            return null;
        }

        return $this->cargoResponder->composeFromModel($cargo);
    }
}
