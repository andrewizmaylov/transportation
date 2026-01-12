<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Transportation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class CargoFactory extends Factory
{
    protected $model = Cargo::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'id' => Uuid::uuid7()->toString(),
            'transportation_id' => Transportation::factory()->create([
                'client_id' => $user->id,
            ])->id,
            'client_id' => $user->id,
            'name' => $this->faker->name,
            'length' => rand(10, 1000),
            'width' => rand(10, 1000),
            'height' => rand(10, 1000),
            'weight' => rand(10, 1000),
            'price' => rand(10, 1000),
            'currency' => 'RUB',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
