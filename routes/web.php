<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Frontend\PagesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => ''], function () {
    Route::get('/', [PagesController::class, 'home'])->name('home');
});

Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
});

/* Domain route files are loaded here. Keep business logic out of web.php. */
require __DIR__ . '/userRoutes.php';
