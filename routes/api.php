<?php

use App\Http\Controllers\Api\CakeController;
use App\Http\Controllers\Api\CakeEmailListController;
use App\Http\Controllers\Api\CakeSubscriberController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('cakes', CakeController::class);
    Route::apiResource('cakes.subscribers', CakeSubscriberController::class)->only('index', 'store', 'destroy');
    Route::apiResource('cakes.email-lists', CakeEmailListController::class)->only('index');
});
