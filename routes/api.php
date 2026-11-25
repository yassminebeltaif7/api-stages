<?php

use App\Http\Controllers\Api\DomaineController;
use App\Http\Controllers\Api\OffreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::apiResource('offres', OffreController::class)->only(['index', 'show']);
    Route::apiResource('domaines', DomaineController::class)->only(['index', 'show']);
});
