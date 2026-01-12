<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo\AddCargoToTransportationController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo\DeleteCargoController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Cargo\UpdateCargoController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\AddNewTransportationAddressController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\GetShipperTransportationByIdController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\GetShipperTransportationsListController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\RegisterTransportationController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\UpdateTransportationAddressController;
use Src\Shipper\PresentationLayer\HTTP\V1\Controllers\Transportation\UpdateTransportationController;

Route::prefix('public/api/v1/shipper')
    ->group(function () {
        Route::get('transportation-list', GetShipperTransportationsListController::class)->name('transportation.shipper-list');
        Route::get('transportation/{transportation_id}', GetShipperTransportationByIdController::class)->name('transportation.shipper-card');
        Route::post('register-transportation', RegisterTransportationController::class)->name('transportation.register');
        Route::patch('{transportation_id}/update-transportation', UpdateTransportationController::class)->name('transportation.update');
        Route::post('{transportation_id}/add-transportation-address', AddNewTransportationAddressController::class)->name('transportation.add-new-address');
        Route::patch('{transportation_id}/{address_id}/update-transportation-address', UpdateTransportationAddressController::class)->name('transportation.update-address');
        Route::post('{transportation_id}/add-cargo', AddCargoToTransportationController::class)->name('transportation.add-cargo');
        Route::patch('{transportation_id}/{cargo_id}/update-cargo', UpdateCargoController::class)->name('transportation.update-cargo');
        Route::delete('{transportation_id}/{cargo_id}/delete-cargo', DeleteCargoController::class)->name('transportation.delete-cargo');
    });
