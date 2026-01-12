<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Storage;

use App\Models\TransportationAddress;
use DateTime;
use Illuminate\Database\ConnectionInterface;
use Ramsey\Uuid\Uuid;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Storage\TransportationAddressStorageInterface;

class TransportationAddressStorage implements TransportationAddressStorageInterface
{
    protected string $tableName;

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
        $this->tableName = TransportationAddress::getTableName();
    }

    public function createTransportationAddress(
        string $alias,
        string $clientId,
        string $type,
        string $contact,
        int $cityId,
        string $addressLine1,
        string $phoneNumber,
        int $countryId,
        string $latitude,
        string $longitude,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $comment,
    ): TransportationAddressId {
        $uuid = Uuid::uuid7()->toString();

        $this->connection
            ->table($this->tableName)
            ->insert([
                'id' => $uuid,
                'alias' => $alias,
                'client_id' => $clientId,
                'type' => $type,
                'contact' => $contact,

                'city_id' => $cityId,
                'address_line_1' => $addressLine1,
                'address_line_2' => $addressLine2,
                'address_line_3' => $addressLine3,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'phone_number' => $phoneNumber,
                'comment' => $comment,
                'country_id' => $countryId,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
            ]);

        return new TransportationAddressId($uuid);
    }

    public function updateTransportationAddress(
        TransportationAddressId $id,
        ?string $alias,
        ?string $contact,
        ?string $addressLine1,
        ?string $phoneNumber,
        ?string $latitude,
        ?string $longitude,
        ?string $addressLine2,
        ?string $addressLine3,
        ?string $comment
    ): void {
        $data = [
            'alias' => $alias,
            'contact' => $contact,
            'address_line_1' => $addressLine1,
            'address_line_2' => $addressLine2,
            'address_line_3' => $addressLine3,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'phone_number' => $phoneNumber,
            'comment' => $comment,

            'updated_at' => new DateTime,
        ];

        $this->connection
            ->table($this->tableName)
            ->where('id', $id->value())
            ->update(array_filter($data));
    }
}
