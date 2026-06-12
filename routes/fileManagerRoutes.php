<?php

use App\Http\Controllers\Backend\FileManager\FileManagerController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard/file-manager')
    ->name('backend.file-manager.')
    ->middleware(['file-manager.auth'])
    ->group(function (): void {
        Route::get('/', [FileManagerController::class, 'index'])->name('index');
        Route::get('preview', [FileManagerController::class, 'preview'])->name('preview');
        Route::get('thumbnail', [FileManagerController::class, 'thumbnail'])->name('thumbnail');
        Route::get('maintenance/thumbnail-cache', [FileManagerController::class, 'thumbnailCache'])->name('thumbnail-cache');
        Route::delete('maintenance/thumbnail-cache', [FileManagerController::class, 'clearThumbnailCache'])->name('thumbnail-cache.clear');
        Route::post('maintenance/import', [FileManagerController::class, 'importMedia'])->name('import');
        Route::get('maintenance/imports', [FileManagerController::class, 'importHistory'])->name('imports');
        Route::patch('folder/permissions', [FileManagerController::class, 'updateFolderPermissions'])->name('folder.permissions');
        Route::get('usage', [FileManagerController::class, 'usage'])->name('usage');
        Route::post('photo', [FileManagerController::class, 'uploadPhoto'])->name('photo.upload');
        Route::post('folder', [FileManagerController::class, 'folder'])->name('folder');
        Route::post('usage', [FileManagerController::class, 'trackUsage'])->name('usage.track');
        Route::delete('usage', [FileManagerController::class, 'forgetUsage'])->name('usage.forget');
        Route::patch('item/rename', [FileManagerController::class, 'rename'])->name('rename');
        Route::patch('item/move', [FileManagerController::class, 'move'])->name('move');
        Route::delete('item', [FileManagerController::class, 'destroy'])->name('destroy');
    });
