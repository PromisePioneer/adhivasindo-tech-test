<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\StudentSearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $r) => $r->user());
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::apiResource('users', UserController::class)->only(['index', 'store', 'show']);

    Route::get('/search/{field}', [StudentSearchController::class, 'search'])
        ->where('field', 'nama|nim|ymd');
});
