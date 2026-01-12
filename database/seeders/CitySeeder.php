<?php

declare(strict_types=1);

namespace Database\Seeders;

class CitySeeder
{
    /**
     * @throws \JsonException
     */
    public function getDataSet(): \Generator
    {
        $countryCodes = ['RU', 'KZ'];
        $filePath = env('APP_ENV') === 'testing'
            ? 'seeders/Data/cities_test.jsonl'
            : 'seeders/Data/cities.jsonl';

        $handle = fopen(database_path($filePath), 'rb');

        try {
            while ($line = fgets($handle)) {
                if ($line === "[\n" || $line === ']') {
                    continue;
                }

                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                if (! in_array($data['country_code'], $countryCodes)) {
                    continue;
                }
                yield [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'country_id' => $data['country_id'],
                    'country_code' => $data['country_code'],
                    'country_name' => $data['country_name'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'level' => $data['level'],
                    'native' => $data['native'],
                    'parent_id' => $data['parent_id'],
                    'population' => $data['population'],
                    'state_id' => $data['state_id'],
                    'state_code' => $data['state_code'],
                    'state_name' => $data['state_name'],
                    'timezone' => $data['timezone'],
                    'translations' => json_encode($data['translations']),
                    'type' => $data['type'],
                    'wikiDataId' => $data['wikiDataId'],
                ];
            }
        } finally {
            fclose($handle);
        }
    }
}
