<?php

declare(strict_types=1);

use Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers\CheckAuthenticatedUserController;
use Src\SharedKernel\PresentationLayer\HTTP\V1\Controllers\GetApiTokenForRegisteredUserController;

Route::prefix('public/api/v1/users')
    ->group(function () {
        Route::post('get-token', GetApiTokenForRegisteredUserController::class)->name('users.get-token');
        Route::get('check-auth', CheckAuthenticatedUserController::class)->name('users.check-auth')->middleware('auth:sanctum');
    });
