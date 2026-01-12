<?php

declare(strict_types=1);

namespace Src\SharedKernel\InfrastructureLayer\Repository;

use App\Models\TransportationAddress;
use Illuminate\Database\ConnectionInterface;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationAddressId;
use Src\SharedKernel\DomainLayer\Entities\TransportationAddress as TransportationAddressEntity;
use Src\SharedKernel\DomainLayer\Repository\TransportationAddressRepositoryInterface;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Responder\TransportationAddressResponder;

class TransportationAddressRepository implements TransportationAddressRepositoryInterface
{
    protected string $tableName;

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly TransportationAddressResponder $responder,
    ) {
        $this->tableName = TransportationAddress::getTableName();
    }

    public function findById(TransportationAddressId $transportationId): ?TransportationAddressEntity
    {
        $transportationAddress = $this->connection->table($this->tableName)
            ->where('id', $transportationId->value())
            ->first();

        if (! $transportationAddress) {
            return null;
        }

        return $this->responder->composeFromModel($transportationAddress);
    }
}
