<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TransportationAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;

class TransportationAddressFactory extends Factory
{
    protected $model = TransportationAddress::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid7()->toString(),
            'alias' => 'Initial alias',
            'type' => TransportationAddressTypesEnum::DELIVERY->value,
            'client_id' => User::factory()->create()->id,
            'contact' => 'Initial contact',
            'city_id' => 102527,
            'address_line_1' => 'Initial address 1',
            'address_line_2' => 'Initial address 2',
            'address_line_3' => 'Initial address 3',
            'latitude' => 'Initial latitude',
            'longitude' => 'Initial longitude',
            'phone_number' => '+7 999 987-89-82',
            'comment' => 'Initial comment',
            'country_id' => 182,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
