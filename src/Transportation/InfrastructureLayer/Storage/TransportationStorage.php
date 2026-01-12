<?php

declare(strict_types=1);

namespace Src\Transportation\InfrastructureLayer\Storage;

use App\Models\Transportation;
use DateTime;
use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Ramsey\Uuid\Uuid;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;
use Src\SharedKernel\DomainLayer\ValueObjects\DateTimeInterval;
use Src\Transportation\DomainLayer\Enum\TransportationStatus;
use Src\Transportation\DomainLayer\Storage\TransportationStorageInterface;

readonly class TransportationStorage implements TransportationStorageInterface
{
    public function __construct(
        private ConnectionInterface $connection,
    ) {}

    public function createTransportation(string $name, DateTimeInterval $pickupDateInterval): TransportationId
    {
        $id = Uuid::uuid7()->toString();

        $this->connection
            ->table(Transportation::getTableName())
            ->insert([
                'id' => $id,
                'name' => $name,
                'client_id' => auth()->id(),
                'pickup_from' => $pickupDateInterval->from,
                'pickup_to' => $pickupDateInterval->to,
                'transportation_status' => TransportationStatus::NEW->value,
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ]);

        return new TransportationId($id);
    }

    public function setTransportationAddress(
        TransportationId $transportationId,
        TransportationAddressId $id,
        TransportationAddressTypesEnum $type
    ): void {
        $field = match (true) {
            $type === TransportationAddressTypesEnum::PICKUP => 'pickup_address_id',
            $type === TransportationAddressTypesEnum::DELIVERY => 'delivery_address_id',
        };

        $this->connection
            ->table(Transportation::getTableName())
            ->where(
                'id',
                $transportationId->value(),
            )
            ->update([
                $field => $id->value(),
                'updated_at' => new DateTime,
            ]);
    }

    public function updateTransportation(
        TransportationId $transportationId,
        ?string $name,
        DateTimeInterface $pickupFrom,
        DateTimeInterface $pickupTo
    ): void {
        $dataForUpdate = [
            'name' => $name ?? null,
            'pickup_from' => $pickupFrom,
            'pickup_to' => $pickupTo,
            'updated_at' => new DateTime,
        ];

        $this->connection
            ->table(Transportation::getTableName())
            ->where(
                'id',
                $transportationId->value(),
            )
            ->update($dataForUpdate);
    }
}
