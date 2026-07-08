<?php

use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('{vertical}')->middleware('api.key')->group(function () {
    Route::post('/disponibilite', [BookingController::class, 'verifierDisponibilite']);
    Route::post('/reservation',    [BookingController::class, 'creerReservation']);
    Route::get('{vertical}/reservations', [BookingController::class, 'index']);
    Route::patch('{vertical}/reservations/{id}', [BookingController::class, 'update']);
});