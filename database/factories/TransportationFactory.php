<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Transportation;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class TransportationFactory extends Factory
{
    protected $model = Transportation::class;

    /**
     * @throws \DateMalformedStringException
     */
    public function definition(): array
    {
        $daysToSubtract = rand(2, 7);

        $pickupDate = new DateTimeImmutable(timezone: new DateTimeZone('Asia/Yekaterinburg'))->setTime(9, 0);

        return [
            'id' => Uuid::uuid7()->toString(),
            'client_id' => null,
            'name' => $this->faker->word(),
            'pickup_from' => $pickupDate->format('Y-m-d H:i:s'),
            'pickup_to' => $pickupDate->modify('+4 hours')->format('Y-m-d H:i:s'),
            'pickup_address_id' => null,
            'delivery_address_id' => null,
            'created_at' => new DateTime()->modify("-$daysToSubtract day"),
            'updated_at' => new DateTime()->modify("-$daysToSubtract day"),
            'deleted_at' => null,
        ];
    }
}
