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
require __DIR__ . '/fileManagerRoutes.php';

// Legacy FTP smoke route intentionally disabled. Use artisan/tinker for
// storage diagnostics so production routes do not write files by accident.
// Route::get('/test', function () {
//     $path = \Illuminate\Support\Facades\Storage::disk('ftp')->put('text', new \Illuminate\Http\File(public_path('robots.txt')));
//     return $path;
// });
