<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    /* Role Route Start */
    Route::get('role', [RoleController::class, 'index']);
    Route::post('role', [RoleController::class, 'store'])->name('role.store'); //create role and assign permission
    Route::get('role/{id}', [RoleController::class, 'show']);
    Route::put('role/{id}', [RoleController::class, 'update'])->name('role.update'); //update role and assign permission
    Route::delete('role/{id}', [RoleController::class, 'destroy']); //delete role and remove assign permission
    Route::post('role/assign-user', [RoleController::class, 'assignUserRole'])->name('role.assign-user-role'); //assign user role
    Route::post('role/remove-user', [RoleController::class, 'removeUserRole'])->name('role.remove-user-role'); //remove user role
    Route::post('role/assign-permission', [RoleController::class, 'assignRolePermission'])->name('role.assign-role-permission'); //assign role permission
    Route::post('role/remove-permission', [RoleController::class, 'removeRolePermission'])->name('role.remove-role-permission'); //remove role permission
    /* Role route end*/

    /* Permission Route Start */
    Route::get('permission', [PermissionController::class, 'index']);
    Route::post('permission', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('permission/{id}', [PermissionController::class, 'show']);
    Route::put('permission/{id}', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('permission/{id}', [PermissionController::class, 'destroy']);
    Route::post('permission/assign-user', [PermissionController::class, 'userPermissionAssign'])->name('permission.user-permission-assign'); // user extra permission assign
    Route::post('permission/remove-user', [PermissionController::class, 'userPermissionRemove'])->name('permission.user-permission-remove'); // user extra permission remove
    /* Permission route end*/
});
