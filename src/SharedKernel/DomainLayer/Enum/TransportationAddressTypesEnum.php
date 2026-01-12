<?php

declare(strict_types=1);

namespace Src\SharedKernel\DomainLayer\Enum;

enum TransportationAddressTypesEnum: string
{
    case PICKUP = 'pickup';
    case DELIVERY = 'delivery';
}
