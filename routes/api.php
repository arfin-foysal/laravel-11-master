<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Open Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected Routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('logout', [AuthController::class, 'logout']);

    /* Role Route Start */
    Route::get('role', [RoleController::class, 'index']);
    Route::post('role', [RoleController::class, 'store'])->name('role.store');
    Route::get('role/{id}', [RoleController::class, 'show']);
    Route::put('role/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('role/{id}', [RoleController::class, 'destroy']);
    Route::post('role/assign-user', [RoleController::class, 'assignUserRole'])->name('role.assign-user-role');
    /* Role route end*/

    /* Permission Route Start */
    // Route::get('permission', [PermissionController::class, 'index']);
    // Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');
    // Route::get('/permission/{id}', [PermissionController::class, 'show']);
    // Route::put('/permission/{id}', [PermissionController::class, 'update'])->name('permission.update');
    // Route::delete('/permission/{id}', [PermissionController::class, 'destroy']);
    // Route::post('/permission/assign-user', [PermissionController::class, 'userPermissionAssign'])->name('permission.user-permission-assign');
    /* Permission route end*/
});
