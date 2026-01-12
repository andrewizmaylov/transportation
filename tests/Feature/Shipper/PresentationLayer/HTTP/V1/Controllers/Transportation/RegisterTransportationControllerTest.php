<?php

declare(strict_types=1);

namespace Tests\Feature\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation;

use App\Models\User;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

use Src\Transportation\DomainLayer\Enum\TransportationStatus;

uses(RefreshDatabase::class);

$dateTime = new DateTimeImmutable()->setTime(8, 30);
$pickupFrom = $dateTime->format('Y-m-d H:i:s');
$pickupTo = $dateTime->modify('+6 hours')->format('Y-m-d H:i:s');
$name = 'Test Transportation';

test('no auth user get 401', function () {
    post(route('transportation.register'))->assertUnauthorized();
});

test('auth user can reach the route', function () use ($pickupFrom, $pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('transportation.register', [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]));

    $response->assertOk();

    $response->assertJsonStructure([
        'id',
        'type',
        'attributes',
    ]);

    expect($response->getContent())
        ->json()
        ->type->toBe('Transportation')
        ->attributes->toBeArray()
        ->attributes->pickupFrom->toBe($pickupFrom)
        ->attributes->pickupTo->toBe($pickupTo)
        ->attributes->clientId->toBe($user->id)
        ->attributes->transportationStatus->toBe(TransportationStatus::NEW->value);
});

test('check validation rules were applied', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'));

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupFrom')
        ->and($errorPointers)->toContain('pickupTo');
});

test('it fails when name is empty string', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => '',
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it fails when name is missing', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it fails when pickupFrom is missing', function () use ($pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupFrom');
});

test('it fails when pickupTo is missing', function () use ($pickupFrom, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupTo');
});

test('it fails when pickupFrom is invalid date format', function () use ($pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => 'not-a-date',
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when pickupTo is invalid date format', function () use ($pickupFrom, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => 'invalid-date-format',
        ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when pickupFrom has invalid date values', function () use ($pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => '2024-13-45 25:99:99',
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it fails when pickupTo is before pickupFrom', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30);
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->modify('-6 hours')->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(500);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors');
});

test('it handles pickupTo equals pickupFrom', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30);
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->attributes->pickupFrom->toBe($pickupFrom)
        ->attributes->pickupTo->toBe($pickupTo);
});

test('it fails when pickupFrom is empty string', function () use ($pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => '',
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupFrom');
});

test('it fails when pickupTo is empty string', function () use ($pickupFrom, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => '',
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupTo');
});

test('it handles name with special characters', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => 'Transportation !@#$%^&*()_+-=[]{}|;:,.<>?',
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    // Note: strip_tags() removes < and > characters as they're HTML tag delimiters
    // This is expected security behavior
    expect($response->getContent())
        ->json()
        ->attributes->name->toBe('Transportation !@#$%^&*()_+-=[]{}|;:,.?');
});

test('it handles name with unicode characters', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => 'Перевозка 🚚 运输',
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->attributes->name->toBe('Перевозка 🚚 运输');
});

test('it handles name at maximum length', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();
    $longName = str_repeat('A', 255); // VARCHAR(255) is the default Laravel string limit

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $longName,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->attributes->name->toBe($longName);
});

test('it fails when name exceeds database limit', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();
    $tooLongName = str_repeat('A', 256); // Exceeds VARCHAR(255) limit

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $tooLongName,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it handles name with SQL injection attempt', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => "'; DROP TABLE transportations; --",
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->attributes->name->toBe("'; DROP TABLE transportations; --");
});

test('it sanitizes name with XSS attempt', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => '<script>alert("XSS")</script>',
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    // XSS attempt should be sanitized - HTML tags are stripped for security
    expect($response->getContent())
        ->json()
        ->attributes->name->toBe('alert("XSS")');
});

test('it handles name with newlines and tabs', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => "Transportation\nWith\tTabs",
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();

    expect($response->getContent())
        ->json()
        ->attributes->name->toBe("Transportation\nWith\tTabs");
});

test('it handles pickupFrom with different date format', function () use ($pickupTo, $name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30);
    $pickupFrom = $dateTime->format('Y-m-d\TH:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it handles pickupTo with different date format', function () use ($pickupFrom, $name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30)->modify('+6 hours');
    $pickupTo = $dateTime->format('Y-m-d\TH:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it handles very far future dates', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable('2099-12-31 23:59:59');
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->modify('+1 day')->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it handles past dates', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable('2020-01-01 08:00:00');
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->modify('+6 hours')->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it handles very short time interval', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30);
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->modify('+1 second')->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it handles very long time interval', function () use ($name) {
    $user = User::factory()->create();
    $dateTime = new DateTimeImmutable()->setTime(8, 30);
    $pickupFrom = $dateTime->format('Y-m-d H:i:s');
    $pickupTo = $dateTime->modify('+365 days')->format('Y-m-d H:i:s');

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertOk();
});

test('it fails when name is null', function () use ($pickupFrom, $pickupTo) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => null,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('name');
});

test('it fails when pickupFrom is null', function () use ($pickupTo, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => null,
            'pickupTo' => $pickupTo,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupFrom');
});

test('it fails when pickupTo is null', function () use ($pickupFrom, $name) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('transportation.register'), [
            'name' => $name,
            'pickupFrom' => $pickupFrom,
            'pickupTo' => null,
        ]);

    $response->assertStatus(422);

    $responseData = $response->json();
    expect($responseData)->toHaveKey('errors')
        ->and($responseData['errors'])->toBeArray();

    $errorPointers = array_map(fn ($error) => $error['source']['pointer'] ?? null, $responseData['errors']);
    expect($errorPointers)->toContain('pickupTo');
});
