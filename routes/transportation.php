<?php

declare(strict_types=1);

use App\Http\Controllers\CreateSchemaV1Controller;
use App\Http\Controllers\CreateTransportationFormController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\SaveTransportationDraftController;
use Symfony\Component\HttpFoundation\Response;

Route::prefix('api/v1/transportation')->group(function () {
    Route::get('create', CreateTransportationFormController::class);

    Route::get('create-transportation-schema/{stepId}', CreateSchemaV1Controller::class);

    Route::put('save-draft/{draftId}', SaveTransportationDraftController::class);

    Route::get('draft/{draftId}', function ($draftId) {
        $userID = auth()->id();
        $draft = Cache::get("{$userID}_transportation_draft_{$draftId}");
        if (! $draft) {
            return response()->json(status: Response::HTTP_NO_CONTENT);
        }

        return response()->json(['success' => true, 'data' => $draft]);
    });
})->middleware(['api-web', 'auth:sanctum']);
