<?php

declare(strict_types=1);

namespace Tests\Feature\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Models\Transportation;
use App\Models\TransportationAddress;
use App\Models\User;
use Brick\PhoneNumber\PhoneNumberParseException;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\patch;

use Ramsey\Uuid\Uuid;
use Src\SharedKernel\DomainLayer\Repository\TransportationAddressRepositoryInterface;
use Src\SharedKernel\DomainLayer\Storage\TransportationAddressStorageInterface;
use Src\SharedKernel\DomainLayer\ValueObjects\PhoneNumber;
use Src\SharedKernel\InfrastructureLayer\Repository\TransportationAddressRepository;
use Src\SharedKernel\InfrastructureLayer\Storage\TransportationAddressStorage;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()->bind(TransportationAddressStorageInterface::class, TransportationAddressStorage::class);
    app()->bind(TransportationAddressRepositoryInterface::class, TransportationAddressRepository::class);
    app()->bind(ConnectionInterface::class, fn () => DB::connection());
});

test(
    /**
     * @throws PhoneNumberParseException
     * @throws \JsonException
     */
    /**
     * @throws PhoneNumberParseException
     * @throws \JsonException
     */
    'it can update 9 fields',
    function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        $address = TransportationAddress::factory()->create(['client_id' => $user->id]);

        $transportation = Transportation::factory()->create([
            'client_id' => $user->id,
            'delivery_address_id' => $address->id,
        ]);

        expect($address)
            ->alias->toBe('Initial alias')
            ->contact->toBe('Initial contact')
            ->address_line_1->toBe('Initial address 1')
            ->address_line_2->toBe('Initial address 2')
            ->address_line_3->toBe('Initial address 3')
            ->phone_number->toBe('+7 999 987-89-82')
            ->comment->toBe('Initial comment');

        $dataForUpdate = [
            'alias' => 'new alias',
            'contact' => 'new contact',
            'addressLine1' => 'new addressLine1',
            'addressLine2' => 'new addressLine2',
            'addressLine3' => 'new addressLine3',
            'phoneNumber' => '+79122345678',
            'comment' => 'new comment',
        ];

        $updatedAddress = patch(route('transportation.update-address', [
            'transportation_id' => $transportation->id,
            'address_id' => $address->id,
        ]), $dataForUpdate);

        $phoneNumber = new PhoneNumber($dataForUpdate['phoneNumber']);

        expect($updatedAddress->getContent())->json()
            ->id->toBeString()->toBe($address->id)
            ->attributes->alias->toBeString()->toBe($dataForUpdate['alias'])
            ->attributes->contact->toBeString()->toBe($dataForUpdate['contact'])
            ->attributes->addressLine1->toBeString()->toBe($dataForUpdate['addressLine1'])
            ->attributes->addressLine2->toBeString()->toBe($dataForUpdate['addressLine2'])
            ->attributes->addressLine3->toBeString()->toBe($dataForUpdate['addressLine3'])
            ->attributes->phoneNumber->toBeString()->toBe($phoneNumber->number)
            ->attributes->comment->toBeString()->toBe($dataForUpdate['comment']);
    }
);

test('it returns 401 when user is not authenticated', function () {
    $user = User::factory()->create();
    $address = TransportationAddress::factory()->create(['client_id' => $user->id]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'alias' => 'new alias',
    ]);

    $response->assertUnauthorized();
});

test('it fails when address id is missing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    patch(route('transportation.update-address', [
        'transportation_id' => Uuid::uuid7()->toString(),
        'address_id' => '',
    ]), [
        'alias' => 'new alias',
    ]);
})->throws(UrlGenerationException::class);

test('it fails when transportation id is missing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    patch(route('transportation.update-address', [
        'transportation_id' => '',
        'address_id' => Uuid::uuid7()->toString(),
    ]), [
        'alias' => 'new alias',
    ]);
})->throws(UrlGenerationException::class);

test('it fails when address id is invalid format', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => 'invalid-id-format',
        'address_id' => 'invalid-id-format',
    ]), [
        'alias' => 'new alias',
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('address_id')
        ->and($errorPointers)->toContain('transportation_id');
});

test('it fails when address and transportation does not exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $nonExistentId = Uuid::uuid7()->toString();

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $nonExistentId,
        'address_id' => Uuid::uuid7()->toString(),
    ]), [
        'alias' => 'new alias',
    ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('address_id')
        ->and($errorPointers)->toContain('transportation_id');
});

test('it can update only one field', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create(['client_id' => $user->id]);
    $originalAlias = $address->alias;

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'alias' => 'updated alias only',
    ]);

    expect($response->getContent())->json()
        ->id->toBeUuid()->toBe($address->id)
        ->attributes->alias->toBeString('updated alias only')
        ->attributes->alias->toBeString()->not($originalAlias)
        ->attributes->contact->toBeString()->toBe($address->contact)
        ->attributes->addressLine1->toBeString()->toBe($address->address_line_1);
});

test('it can update with empty optional fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create([
        'client_id' => $user->id,
        'address_line_2' => 'original line 2',
        'address_line_3' => 'original line 3',
        'comment' => 'original comment',
    ]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'alias' => 'updated alias',
        'addressLine2' => '',
        'addressLine3' => '',
        'comment' => '',
    ]);

    expect($response->getContent())->json()
        ->id->toBeUuid()->toBe($address->id)
        ->attributes->alias->toBeString()->toBe('updated alias')
        ->attributes->addressLine2->toBeString()->toBe($address->address_line_2)
        ->attributes->addressLine3->toBeString()->toBe($address->address_line_3)
        ->attributes->comment->toBeString()->toBe($address->comment);
});

test('it fails when phone number is invalid', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create(['client_id' => $user->id]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'phoneNumber' => 'invalid-phone-number',
    ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it can handle very long string values', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create(['client_id' => $user->id]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $longString = str_repeat('a', 1000);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'alias' => $longString,
        'comment' => $longString,
    ]);

    $response->assertStatus(422);
    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('alias');
});

test('it can update with null optional fields only provided fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create([
        'client_id' => $user->id,
        'address_line_2' => 'original line 2',
    ]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $response = patch(route('transportation.update-address', [
        'transportation_id' => $transportation->id,
        'address_id' => $address->id,
    ]), [
        'alias' => 'updated alias',
        'addressLine2' => null,
    ]);

    expect($response->getContent())->json()
        ->id->toBeString()->toBe($address->id)
        ->attributes->alias->toBeString()->toBe('updated alias')
        ->attributes->comment->toBeString()->toBe($address->comment)
        ->attributes->addressLine3->toBeString()->toBe($address->address_line_3)
        ->attributes->addressLine2->toBeString()->toBe($address->address_line_2);
});

test('it can update phone number with different formats', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $address = TransportationAddress::factory()->create(['client_id' => $user->id]);

    $transportation = Transportation::factory()->create([
        'client_id' => $user->id,
        'delivery_address_id' => $address->id,
    ]);

    $phoneFormats = [
        '+79991234567',
        '+7 999 123-45-67',
        '+7 (999) 123-45-67',
    ];

    foreach ($phoneFormats as $phoneFormat) {
        $response = patch(route('transportation.update-address', [
            'transportation_id' => $transportation->id,
            'address_id' => $address->id,
        ]), [
            'phoneNumber' => $phoneFormat,
        ]);

        expect($response->status())->toBe(200);
        expect($response->getContent())->json()
            ->attributes->phoneNumber->toBeString();
    }
});
