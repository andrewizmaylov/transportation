<?php

declare(strict_types=1);

use App\Models\Transportation;
use App\Models\TransportationAddress;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\post;

use Ramsey\Uuid\Uuid;
use Src\SharedKernel\DomainLayer\Enum\TransportationAddressTypesEnum;
use Src\SharedKernel\DomainLayer\Repository\CityRepositoryInterface;
use Src\SharedKernel\DomainLayer\Repository\CountryRepositoryInterface;
use Src\SharedKernel\InfrastructureLayer\Repository\CityRepository;
use Src\SharedKernel\InfrastructureLayer\Repository\CountryRepository;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()->bind(CityRepositoryInterface::class, CityRepository::class);
    app()->bind(CountryRepositoryInterface::class, CountryRepository::class);
    app()->bind(ConnectionInterface::class, fn () => DB::connection());
});

it('throw 422 when no data provided', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), []);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);

    expect($errorPointers)->toContain('alias')
        ->and($errorPointers)->toContain('type')
        ->and($errorPointers)->toContain('contact')
        ->and($errorPointers)->toContain('city')
        ->and($errorPointers)->toContain('addressLine1');
});

test('it can create transportation address', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', [
        'transportation_id' => $transportation->id,
    ]), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'addressLine2' => 'корпус 2',
        'addressLine3' => 'квартира 16',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
        'comment' => 'Test comment',
    ]);

    $response->assertStatus(200);

    $address = TransportationAddress::query()->first();

    $response->assertJson([
        'id' => $address->id,
        'type' => 'TransportationAddress',
        'attributes' => [
            'id' => $address->id,
            'alias' => $address->alias,
            'clientId' => $address->client_id,
            'type' => $address->type,
            'contact' => $address->contact,
            'city' => [
                'id' => $address->city_id,
            ],
            'country' => [
                'id' => $address->country_id,
            ],
            'addressLine1' => $address->address_line_1,
            'addressLine2' => $address->address_line_2,
            'addressLine3' => $address->address_line_3,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
            'phoneNumber' => $address->phone_number,
            'comment' => $address->comment,
        ],
    ]);
});

test('it can create adress and update pickup and delivery_address_id', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $deliveryAddress = post(route('transportation.add-new-address', [
        'transportation_id' => $transportation->id,
    ]), [
        'alias' => 'Test Delivery Address',
        'type' => TransportationAddressTypesEnum::DELIVERY->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'addressLine2' => 'корпус 2',
        'addressLine3' => 'квартира 16',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
        'comment' => 'Test comment',
    ]);

    $pickupAddress = post(route('transportation.add-new-address', [
        'transportation_id' => $transportation->id,
    ]), [
        'alias' => 'Test Pickup Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 2',
        'addressLine2' => 'корпус 6',
        'addressLine3' => 'квартира 164',
        'phoneNumber' => '+79997654321',
        'country' => 'RU',
        'comment' => 'Test comment',
    ]);

    $pickupAddressId = json_decode($pickupAddress->getContent(), true)['id'];
    $deliveryAddressId = json_decode($deliveryAddress->getContent(), true)['id'];

    $transportationRecord = Transportation::query()->find($transportation->id);
    expect($transportationRecord)
        ->pickup_address_id->toBe($pickupAddressId)
        ->delivery_address_id->toBe($deliveryAddressId);
});

test('it returns 401 when user is not authenticated', function () {
    $user = User::factory()->create();
    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertUnauthorized();
});

test('it fails when transportation id is missing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    post(route('transportation.add-new-address', [
        'transportation_id' => '',
    ]), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);
})->throws(UrlGenerationException::class);

test('it fails when transportation id is invalid format', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = post(route('transportation.add-new-address', [
        'transportation_id' => 'invalid-id-format',
    ]), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('transportation_id');
});

test('it fails when transportation does not exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $nonExistentId = Uuid::uuid7()->toString();

    $response = post(route('transportation.add-new-address', $nonExistentId), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('transportation_id');
});

test('it fails when address type is invalid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => 'invalid_type',
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('type');
});

test('it fails when city is not found', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'NonExistentCity12345',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when country is not found', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'XX',
    ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when phone number is invalid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => 'invalid-phone-number',
        'country' => 'RU',
    ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when required fields are missing individually', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $baseData = [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ];

    $requiredFields = ['alias', 'type', 'contact', 'city', 'addressLine1', 'phoneNumber'];

    foreach ($requiredFields as $field) {
        $data = $baseData;
        unset($data[$field]);

        $response = post(route('transportation.add-new-address', $transportation->id), $data);

        $response->assertStatus(422);

        $responseData = $response->json();
        expect($responseData)->toHaveKey('errors')
            ->and($responseData['errors'])->toBeArray();

        $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
        expect($errorPointers)->toContain($field);
    }
});

test('it can create address with only required fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
    ]);

    $response->assertStatus(200);

    $address = TransportationAddress::query()->latest()->first();
    expect($address)->not->toBeNull()
        ->and($address->alias)->toBe('Test Address')
        ->and($address->address_line_2)->toBeNull()
        ->and($address->address_line_3)->toBeNull()
        ->and($address->comment)->toBeNull();
});

test('it can handle very long string values', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $longString = str_repeat('a', 1000);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => $longString,
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => $longString,
        'city' => 'Екатеринбург',
        'addressLine1' => $longString,
        'addressLine2' => $longString,
        'addressLine3' => $longString,
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
        'comment' => $longString,
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('alias');
});

test('it can create address with empty optional fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
    ]);

    $response = post(route('transportation.add-new-address', $transportation->id), [
        'alias' => 'Test Address',
        'client_id' => $user->id,
        'type' => TransportationAddressTypesEnum::PICKUP->value,
        'contact' => 'Some person',
        'city' => 'Екатеринбург',
        'addressLine1' => 'Ленина 132',
        'addressLine2' => '',
        'addressLine3' => '',
        'phoneNumber' => '+79991234567',
        'country' => 'RU',
        'comment' => '',
    ]);

    $response->assertStatus(200);

    $address = TransportationAddress::query()->latest()->first();
    expect($address)->not->toBeNull()
        ->and($address->address_line_2)->toBeNull()
        ->and($address->address_line_3)->toBeNull()
        ->and($address->comment)->toBeNull();
});
