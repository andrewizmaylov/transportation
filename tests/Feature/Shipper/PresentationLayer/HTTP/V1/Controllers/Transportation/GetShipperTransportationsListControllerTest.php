<?php

declare(strict_types=1);

use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('it not accessible without auth', function () {
    $response = get(route('transportation.shipper-list'));
    $response->assertStatus(401);
});

test('it shows only results for auth user', function () {
    $user1 = User::factory()->create();
    $count1 = 6;
    Transportation::factory()->count($count1)->create([
        'client_id' => $user1->id,
    ]);

    $user2 = User::factory()->create([
        'email' => 'my@email.com',
    ]);
    $count2 = 2;
    Transportation::factory()->count($count2)->create([
        'client_id' => $user2->id,
    ]);

    expect(Transportation::all()->count())->toBe($count1 + $count2);

    $this->actingAs($user2);
    $response = get(route('transportation.shipper-list'));

    $response->assertStatus(200);

    expect($response->getContent())->json()
        ->totalRecords->toBe(2)
        ->items->each(function ($item) use ($user2) {
            $item->clientId->toBe($user2->id);
        });
});
