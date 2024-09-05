<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api','RPManagement']], function () {
    /* Role Route Start */
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store'); //create roles and assign permissions
    Route::get('roles/{id}', [RoleController::class, 'show']);
    Route::put('roles/{id}', [RoleController::class, 'update'])->name('roles.update'); //update roles and assign permissions
    Route::delete('roles/{id}', [RoleController::class, 'destroy']); //delete roles and remove assign permissions
    Route::post('roles/assign-user', [RoleController::class, 'assignUserRole'])->name('roles.assign-user-roles'); //assign user roles
    Route::post('roles/remove-user', [RoleController::class, 'removeUserRole'])->name('roles.remove-user-roles'); //remove user roles
    Route::post('roles/assign-permissions', [RoleController::class, 'assignRolePermission'])->name('roles.assign-roles-permissions'); //assign roles permissions
    Route::post('roles/remove-permissions', [RoleController::class, 'removeRolePermission'])->name('roles.remove-roles-permissions'); //remove roles permissions
    /* Role route end*/

    /* Permission Route Start */
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{id}', [PermissionController::class, 'show']);
    Route::put('permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('permissions/{id}', [PermissionController::class, 'destroy']);
    Route::post('permissions/assign-user', [PermissionController::class, 'userPermissionAssign'])->name('permissions.user-permissions-assign'); // user extra permissions assign
    Route::post('permissions/remove-user', [PermissionController::class, 'userPermissionRemove'])->name('permissions.user-permissions-remove'); // user extra permissions remove
    /* Permission route end*/
});
