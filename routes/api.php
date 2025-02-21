<?php

use App\Http\Controllers\Api\CakeController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('cakes', CakeController::class)->only('store', 'show');
});
