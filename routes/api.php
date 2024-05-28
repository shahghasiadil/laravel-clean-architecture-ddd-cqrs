<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Presentation\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function () {
    Route::post('users/create', 'store');
    Route::put('users/{id}/update', 'update');
});
