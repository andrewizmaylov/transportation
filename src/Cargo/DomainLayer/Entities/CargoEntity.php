<?php

declare(strict_types=1);

namespace Src\Cargo\DomainLayer\Entities;

use DateTime;
use Src\Cargo\DomainLayer\ValueObjects\CargoCharacteristics;
use Src\SharedKernel\DomainLayer\Entities\Ids\CargoId;
use Src\SharedKernel\DomainLayer\Entities\Ids\TransportationId;

final readonly class CargoEntity
{
    public function __construct(
        public CargoId $id,
        public ?TransportationId $transportationId,
        public string $clientId,
        public CargoCharacteristics $cargoCharacteristics,
        public ?DateTime $deletedAt,
    ) {}
}
