<?php

declare(strict_types=1);

namespace Src\Cargo\InfrastructureLayer\Storage;

use App\Models\Cargo;
use DateTime;
use Illuminate\Database\ConnectionInterface;
use Ramsey\Uuid\Uuid;
use Src\Cargo\DomainLayer\Storage\CargoStorageInterface;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

class CargoStorage implements CargoStorageInterface
{
    protected string $tableName;

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
        $this->tableName = Cargo::getTableName();
    }

    public function addNewCargo(TransportationId $transportationId, CargoCharacteristics $cargoCharacteristics): CargoId
    {
        $uuid = Uuid::uuid7()->toString();
        $this->connection->table($this->tableName)->insert([
            'id' => $uuid,
            'transportation_id' => $transportationId->value(),
            'client_id' => auth()->id(),
            'name' => $cargoCharacteristics->name,
            'length' => $cargoCharacteristics->length,
            'width' => $cargoCharacteristics->width,
            'height' => $cargoCharacteristics->height,
            'weight' => $cargoCharacteristics->weight,
            'price' => $cargoCharacteristics->price->amount,
            'currency' => $cargoCharacteristics->price->currency->value(),
        ]);

        return new CargoId($uuid);
    }

    public function updateCargo(CargoId $cargoId, CargoCharacteristics $cargoCharacteristics): void
    {
        $this->connection->table($this->tableName)
            ->where('id', $cargoId->value())
            ->update([
                'name' => $cargoCharacteristics->name,
                'length' => $cargoCharacteristics->length,
                'width' => $cargoCharacteristics->width,
                'height' => $cargoCharacteristics->height,
                'weight' => $cargoCharacteristics->weight,
                'price' => $cargoCharacteristics->price->amount,
                'currency' => $cargoCharacteristics->price->currency->value(),
            ]);
    }

    public function deleteCargo(CargoId $cargoId): void
    {
        $this->connection->table($this->tableName)
            ->where('id', $cargoId->value())
            ->update([
                'deleted_at' => new DateTime,
                'transportation_id' => null,
            ]);
    }
}
