<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class CreateSchemaV1Controller extends Controller
{
    public function __invoke(string $stepId)
    {
        $filePath = match (true) {
            $stepId === 'transportationStep' => 'Schema/Step01.json',
            $stepId === 'pickupAddressStep' => 'Schema/Step02.json',
            $stepId === 'deliveryAddressStep' => 'Schema/Step03.json',
            $stepId === 'cargoStep' => 'Schema/Step04.json',
            $stepId === 'confirmationStep' => 'Schema/Step05.json',
        };

        $filePath = resource_path($filePath);
        if (! file_exists($filePath)) {
            return response()->json(['error' => 'Step not found'], 404);
        }
        $data = json_decode(file_get_contents($filePath), true);

        return response()->json($data);
    }
}
