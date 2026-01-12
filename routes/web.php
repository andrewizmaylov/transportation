<?php

declare(strict_types=1);

use App\Models\Transportation;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

require __DIR__ . '/transportation.php';

use App\Http\Controllers\CreateTransportationFormController;

Route::get('transportations/create-form', CreateTransportationFormController::class)
    ->name('transportations.create-form')
    ->middleware(['auth', 'verified']);

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('sup', function () {
    $ts = Transportation::all();

    return Inertia::render('Sup', [
        'ts' => $ts,
    ]);
})->middleware(['auth', 'verified'])->name('sup');

Route::get('/test-auth', function () {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
        'guard' => auth()->getDefaultDriver(),
    ];
})->middleware('auth:sanctum');

require __DIR__ . '/settings.php';
