<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Transportation\PresentationLayer\HTTP\V1\Controllers\GetTransportationController;
use Src\Transportation\PresentationLayer\HTTP\V1\Controllers\GetTransportationsController;

Route::prefix('public/api/v1/transportation')
    ->group(function () {
        Route::get('/', GetTransportationsController::class)->name('transportation.get-all');
        Route::get('{transportation_id}', GetTransportationController::class)->name('transportation.get-by-id');
    });
