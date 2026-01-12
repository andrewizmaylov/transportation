<?php

declare(strict_types=1);

use App\Models\Cargo;
use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('it is not accessible when unauthorized', function () {
    $response = post(route('transportation.add-cargo', ['transportation_id' => 1]), []);

    $response->assertStatus(401);
});

test('it fails with validation error when no data provided', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), []);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);

    expect($errorPointers)
        ->toHaveCount(6)
        ->toContain('name')
        ->toContain('length')
        ->toContain('width')
        ->toContain('height')
        ->toContain('weight')
        ->toContain('price');
});

test('it have correct response when all data provided', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertOk();

    $cargo = Cargo::query()->first();

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

test('it fails with validation error when transportation_id is invalid uuid', function () {
    $user = User::factory()->create();
    Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => 'invalid-uuid',
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => 'invalid-uuid']), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('transportation_id');
});

test('it fails with validation error when length is zero', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 0,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => -100,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 0,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 0,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 0,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => -100,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 0,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => -100,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'INVALID',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

    $this->actingAs($user);

    $data = [
        'name' => Str::random(256), // 256 characters - exceeds 255 limit
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it fails when name is empty', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => '',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it fails when transportation does not exist', function () {
    $user = User::factory()->create();
    $nonExistentTransportationId = Str::uuid()->toString();

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $nonExistentTransportationId,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $nonExistentTransportationId]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('transportation_id');
});

test('it accepts all valid currency codes', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $currencies = ['EUR', 'USD', 'RUB'];

    foreach ($currencies as $currency) {
        $data = [
            'name' => "Cargo name {$currency}",
            'length' => 600,
            'width' => 600,
            'height' => 600,
            'weight' => 4000,
            'price' => 12000,
            'currency' => $currency,
            'transportation_id' => $transportation->id,
        ];

        $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

        $response->assertOk();

        $responseData = $response->json();
        expect($responseData['attributes']['currency'])->toBe($currency);
    }
});

test('it uses default currency RUB when currency is not provided', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'transportation_id' => $transportation->id,
        // currency is missing
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertOk();

    expect($response->getContent())->json()
        ->attributes->currency->toBe('RUB');
});

test('it returns correct response structure after creation', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

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

test('it creates cargo with correct client_id from authenticated user', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => 600,
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertOk();

    $cargo = Cargo::query()->first();

    expect($cargo->client_id)->toBe($user->id)
        ->and($cargo->transportation_id)->toBe($transportation->id);
});

test('it fails when dimensions are provided as strings instead of integers', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $this->actingAs($user);

    $data = [
        'name' => 'Cargo name',
        'length' => '6ee', // string instead of int
        'width' => 600,
        'height' => 600,
        'weight' => 4000,
        'price' => 12000,
        'currency' => 'EUR',
        'transportation_id' => $transportation->id,
    ];

    $response = post(route('transportation.add-cargo', ['transportation_id' => $transportation->id]), $data);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('length');
});
