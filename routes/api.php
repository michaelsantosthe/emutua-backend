<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth.token'])->group(function () {
    Route::resource('/products', \App\Http\Controllers\Api\ProductController::class);
});

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);