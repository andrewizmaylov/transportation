<?php

declare(strict_types=1);

use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('it not accessible without auth', function () {
    $response = get(route('transportation.shipper-card', ['transportation_id' => 3]));
    $response->assertStatus(401);
});

test('it doesnt show results from other user', function () {
    $user1 = User::factory()->create();
    $transportation1 = Transportation::factory()->create([
        'client_id' => $user1->id,
    ]);

    $user2 = User::factory()->create([
        'email' => 'my@email.com',
    ]);
    Transportation::factory()->create([
        'client_id' => $user2->id,
    ]);

    expect(Transportation::all()->count())->toBe(2);

    $this->actingAs($user2);
    $response = get(route('transportation.shipper-card', ['transportation_id' => $transportation1->id]));

    $response->assertStatus(403);

    $errors = (json_decode($response->getContent(), true))['errors'];
    expect($errors[0])
        ->title->toBe('Access denied.')
        ->detail->toBe('[GetShipperTransportationByIdController] An unexpected error occurred while searching for transportation. Transportation does not belong to client');
});

test('it shows owned by user results', function () {
    $user1 = User::factory()->create();
    Transportation::factory()->create([
        'client_id' => $user1->id,
    ]);

    $user2 = User::factory()->create([
        'email' => 'my@email.com',
    ]);
    $transportation2 = Transportation::factory()->create([
        'client_id' => $user2->id,
    ]);

    expect(Transportation::all()->count())->toBe(2);

    $this->actingAs($user2);
    $response = get(route('transportation.shipper-card', ['transportation_id' => $transportation2->id]));

    $response->assertOk();

    expect($response->getContent())->json()
        ->id->toBe($transportation2->id)
        ->attributes->toBeArray()
        ->attributes->clientId->toBe($user2->id)
        ->attributes->clientId->not->toBe($user1->id);
});
