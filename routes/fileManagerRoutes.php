<?php

use App\Http\Controllers\Backend\FileManager\FileManagerController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard/file-manager')
    ->name('backend.file-manager.')
    // ->middleware(['auth'])
    ->group(function (): void {
        Route::get('/', [FileManagerController::class, 'index'])->name('index');
        Route::get('preview', [FileManagerController::class, 'preview'])->name('preview');
        Route::post('photo', [FileManagerController::class, 'uploadPhoto'])->name('photo.upload');
        Route::post('folder', [FileManagerController::class, 'folder'])->name('folder');
        Route::delete('item', [FileManagerController::class, 'destroy'])->name('destroy');
    });
