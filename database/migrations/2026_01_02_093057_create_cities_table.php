<?php

declare(strict_types=1);

use App\Models\City;
use Database\Seeders\CitySeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $sql = file_get_contents(database_path('seeders/Data/create_cities_table.sql'));
        DB::unprepared($sql);
        $dataToInsert = [];
        $seeder = new CitySeeder;

        foreach ($seeder->getDataSet() as $cityData) {
            $dataToInsert[] = $cityData;

            if (count($dataToInsert) >= 200) {
                City::query()->insert($dataToInsert);
                Log::debug('City batch inserted successfully. Memory usage: ' . memory_get_usage(true) / 1024 / 1024 . 'Mb');
                $dataToInsert = [];
            }
        }

        // Insert remaining records if any
        if (! empty($dataToInsert)) {
            City::query()->insert($dataToInsert);
            Log::debug('Final city batch inserted successfully. Memory usage: ' . memory_get_usage(true) / 1024 / 1024 . 'Mb');
        }
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS cities');
    }
};
