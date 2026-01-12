<?php

declare(strict_types=1);

namespace App\Services;

use Src\SharedKernel\DomainLayer\Entities\City;
use Src\SharedKernel\DomainLayer\Entities\Country;

class GoogleGeocodingAPIService
{
    public function getCoordinatesFromAddress(
        Country $country,
        City $city,
        string $addressLine1,
        ?string $addressLine2
    ): array {
        $latitude = '56.766214,60.669111';
        $longitude = '56.768912,60.671809';

        return [
            $latitude,
            $longitude,
        ];
    }
}
