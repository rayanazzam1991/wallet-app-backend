<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'transactions', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [TransactionsController::class, 'index']);
    Route::post('/', [TransactionsController::class, 'create']);
});

Route::middleware(['throttle:api'])->prefix('auth/')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['middleware' => 'auth:sanctum'])->prefix('auth/')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});
