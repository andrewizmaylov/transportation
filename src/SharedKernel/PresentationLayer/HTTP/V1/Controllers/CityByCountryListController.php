<?php

declare(strict_types=1);

namespace Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers;

use Illuminate\Http\Request;
use Src\SharedKernel\InfrastructureLayer\Repository\CityRepository;

class CityByCountryListController
{
    const int RUSSIA_CODE = 182;

    public function __invoke(Request $request): array
    {
        $countryId = $request->pickupCountryId ?? $request->deliveryCountryId ?? self::RUSSIA_CODE;

        $data = app(CityRepository::class)->getAllCitiesByCountry(countryId: (int) $countryId);

        return array_values($data->toArray());
    }
}
