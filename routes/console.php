<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\FileManager\MediaImportService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('file-manager:import-media {path=uploads} {--no-recursive} {--dry-run} {--limit=}', function () {
    $summary = app(MediaImportService::class)->import(
        (string) $this->argument('path'),
        ! (bool) $this->option('no-recursive'),
        (bool) $this->option('dry-run'),
        $this->option('limit') !== null ? (int) $this->option('limit') : null,
    );

    $this->info('Media import completed.');
    $this->line('Disk: '.$summary['disk']);
    $this->line('Root: '.$summary['root']);
    $this->line('Scanned: '.$summary['scanned']);
    $this->line('Created: '.$summary['created']);
    $this->line('Updated: '.$summary['updated']);
    $this->line('Skipped: '.$summary['skipped']);
    $this->line('Failed: '.$summary['failed']);

    if (! empty($summary['errors'])) {
        $this->warn('Errors:');
        foreach ($summary['errors'] as $error) {
            $this->line('- '.$error['path'].': '.$error['message']);
        }
    }
})->purpose('Import existing storage files into DB media rows without changing storage paths');
