<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
| Keep this file focused on the User domain only.
| The auth middleware is the production baseline for admin modules.
*/
Route::prefix('dashboard')
    ->name('backend.')
    ->middleware(['dashboard.auth'])
    ->group(function (): void {
        Route::get('users/data', [UserController::class, 'data'])
            ->middleware('can:users.view')
            ->name('users.data');
        Route::get('users/options/user-types', [UserController::class, 'userTypeOptions'])
            ->middleware('can:users.view')
            ->name('users.options.user-types');
        Route::get('users/export/csv', [UserController::class, 'exportCsv'])
            ->middleware('can:users.export')
            ->name('users.export.csv');
        Route::get('users', [UserController::class, 'index'])
            ->middleware('can:users.view')
            ->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])
            ->middleware('can:users.create')
            ->name('users.create');
        Route::post('users', [UserController::class, 'store'])
            ->middleware('can:users.create')
            ->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])
            ->middleware('can:users.view')
            ->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('can:users.update')
            ->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])
            ->middleware('can:users.update')
            ->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->middleware('can:users.delete')
            ->name('users.destroy');
    });
