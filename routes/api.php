<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SubMenuController;
use Illuminate\Support\Facades\Route;


// Protected Routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('sub-menus', SubMenuController::class);
});
