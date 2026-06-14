<?php

use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')
    ->name('backend.')
    ->middleware(['dashboard.auth'])
    ->group(function (): void {
        Route::get('permissions', [PermissionController::class, 'index'])
            ->middleware('can:permissions.view')
            ->name('permissions.index');

        Route::get('roles', [RoleController::class, 'index'])
            ->middleware('can:roles.view')
            ->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])
            ->middleware('can:roles.create')
            ->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])
            ->middleware('can:roles.create')
            ->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('can:roles.update')
            ->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])
            ->middleware('can:roles.update')
            ->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('can:roles.delete')
            ->name('roles.destroy');
    });
