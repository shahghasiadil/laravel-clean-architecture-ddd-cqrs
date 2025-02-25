<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Presentation\UserManagement\Controllers\UserController;

Route::get('/user', fn(Request $request) => $request->user())->middleware('auth:sanctum');

Route::controller(UserController::class)->group(function (): void {
    Route::post('users', 'store');
    Route::patch('users/{id}', 'update');
    Route::get('users/{id}', 'show');
    Route::get('users', 'index');
});
