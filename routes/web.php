<?php

use Illuminate\Support\Facades\Route;
use Presentation\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('users', UserController::class);
