<?php

declare(strict_types=1);

use App\Models\Cargo;
use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

test('cargo can be updated with all fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
        'transportation_id' => $transportation->id,
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->id->toBe($cargo->id)
        ->attributes->name->toBe($data['name'])
        ->attributes->client_id->toBe($user->id)
        ->attributes->length->toBe($data['length'])
        ->attributes->width->toBe($data['width'])
        ->attributes->height->toBe($data['height'])
        ->attributes->weight->toBe($data['weight'])
        ->attributes->price->toBe($data['price'])
        ->attributes->currency->toBe($data['currency'])
        ->attributes->transportation_id->toBe($data['transportation_id']);
});

test('it is not accessible when unauthorized', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(401);
});

test('it fails with validation error when cargo_id is invalid uuid', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => 'invalid-uuid',
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('cargo_id');
});

test('it fails with validation error when transportation_id is invalid uuid', function () {
    $user = User::factory()->create();
    $cargo = Cargo::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => 'invalid-uuid',
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('transportation_id');
});

test('it fails when cargo does not exist', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $nonExistentCargoId = Str::uuid()->toString();

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $nonExistentCargoId,
    ]), $data);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray()
        ->and($responseData['errors'][0])->toHaveKey('title')
        ->and($responseData['errors'][0]['title'])->toBe('Server error.');
});

test('it fails with validation error when length is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 0,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('length');
});

test('it fails with validation error when length is negative', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => -100,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('length');
});

test('it fails with validation error when width is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'width' => 0,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('width');
});

test('it fails with validation error when height is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'height' => 0,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('height');
});

test('it fails with validation error when weight is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'weight' => 0,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('weight');
});

test('it fails with validation error when weight is negative', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'weight' => -100,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('weight');
});

test('it fails with validation error when price is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'price' => 0,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('price');
});

test('it fails with validation error when price is negative', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'price' => -100,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('price');
});

test('it fails with validation error when currency is invalid', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'currency' => 'INVALID',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('currency');
});

test('it fails when name is too long', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => Str::random(256), // 256 characters - exceeds 255 limit
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it returns correct response structure after update', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertOk();

    $responseData = $response->json();
    expect($responseData)
        ->toHaveKey('id')
        ->toHaveKey('type')
        ->toHaveKey('attributes')
        ->and($responseData['attributes'])->toHaveKey('name')
        ->and($responseData['attributes'])->toHaveKey('length')
        ->and($responseData['attributes'])->toHaveKey('width')
        ->and($responseData['attributes'])->toHaveKey('height')
        ->and($responseData['attributes'])->toHaveKey('weight')
        ->and($responseData['attributes'])->toHaveKey('price')
        ->and($responseData['attributes'])->toHaveKey('currency')
        ->and($responseData['attributes'])->toHaveKey('transportation_id')
        ->and($responseData['attributes'])->toHaveKey('client_id');
});

test('it can update cargo from different transportation', function () {
    $user = User::factory()->create();
    $transportation1 = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $transportation2 = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation1->id,
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
    ];

    // Try to update cargo using wrong transportation_id in route
    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation2->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    expect($response->status())->toBe(500);
});

test('it doesn\'t fails when trying to update already deleted cargo', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);
    $cargo = Cargo::factory()->create([
        'transportation_id' => $transportation->id,
        'client_id' => $user->id,
    ]);

    // Soft delete the cargo first
    $cargo->delete();

    $this->actingAs($user);

    $data = [
        'name' => 'Updated Cargo name',
        'length' => 700,
        'width' => 700,
        'height' => 700,
        'weight' => 5000,
        'price' => 15000,
        'currency' => 'USD',
    ];

    $response = patch(route('transportation.update-cargo', [
        'transportation_id' => $transportation->id,
        'cargo_id' => $cargo->id,
    ]), $data);

    $response->assertOk();
});
