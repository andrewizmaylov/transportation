<?php

declare(strict_types=1);

namespace Tests\Transportation\PresentationLayer\HTTP\V1\Controllers;

use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('no auth user get 401', function () {
    get(route('transportation.get-by-id', ['transportation_id' => 1]))->assertUnauthorized();
});

test('auth user can rich the route', function () {
    $user = User::factory()->create();

    $transportationEntity = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('transportation.get-by-id', ['transportation_id' => $transportationEntity->id]))
        ->assertOk();
});

test('auth user can get formatted response for transportation', function () {
    $user = User::factory()->create();

    $transportationEntity = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('transportation.get-by-id', [
            'transportation_id' => $transportationEntity->id,
        ]));

    $response->assertJson([
        'id' => $transportationEntity->id,
        'type' => 'Transportation',
        'attributes' => [
            'id' => $transportationEntity->id,
        ],
    ]);
});
