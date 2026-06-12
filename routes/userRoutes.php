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
        Route::get('users/data', [UserController::class, 'data'])->name('users.data');
        Route::get('users/options/user-types', [UserController::class, 'userTypeOptions'])->name('users.options.user-types');
        Route::get('users/export/csv', [UserController::class, 'exportCsv'])->name('users.export.csv');
        Route::resource('users', UserController::class);
    });
