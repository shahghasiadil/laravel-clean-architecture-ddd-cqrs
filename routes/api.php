<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Presentation\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('users', UserController::class);
