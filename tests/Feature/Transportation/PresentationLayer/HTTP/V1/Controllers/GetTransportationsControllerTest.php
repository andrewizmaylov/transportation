<?php

declare(strict_types=1);

namespace Tests\Feature\Transportation\PresentationLayer\HTTP\V1\Controllers;

use App\Models\Transportation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

use Src\SharedKernel\DomainLayer\ValueObjects\PaginatedResult;

uses(RefreshDatabase::class);

test('no auth user get 401', function () {
    get(route('transportation.get-all'))->assertUnauthorized();
});

test('auth user can rich transportations', function () {
    $user = User::factory()->create();

    $activeTransportations = Transportation::factory()->count(110)->create([
        'client_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('transportation.get-all'));

    $response->assertOk();

    $response->assertJsonStructure([
        'items',
        'currentPage',
        'lastPage',
        'perPage',
        'totalRecords',
    ]);

    expect($response->getContent())
        ->json()
        ->currentPage->toBe(PaginatedResult::CURRENT_PAGE)
        ->items->toHaveCount(PaginatedResult::PER_PAGE)
        ->totalRecords->toBe($activeTransportations->count())
        ->perPage->toBe(PaginatedResult::PER_PAGE)
        ->lastPage->toBe((int) ceil($activeTransportations->count() / PaginatedResult::PER_PAGE));
});
