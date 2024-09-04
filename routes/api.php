<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Protected Routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('users', UserController::class);
});
