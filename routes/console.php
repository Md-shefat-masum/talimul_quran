<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\MediaImport;
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

Artisan::command('file-manager:prune-imports {--days= : Delete imports older than this many days} {--keep= : Always keep this many newest rows} {--dry-run : Show what would be deleted without deleting}', function () {
    $days = $this->option('days') !== null
        ? max(0, (int) $this->option('days'))
        : max(0, (int) config('file_manager.import_retention_days', 90));
    $keep = $this->option('keep') !== null
        ? max(0, (int) $this->option('keep'))
        : max(0, (int) config('file_manager.import_retention_keep', 100));
    $cutoff = now()->subDays($days);
    $keepIds = MediaImport::query()
        ->latest()
        ->limit($keep)
        ->pluck('id')
        ->all();
    $query = MediaImport::query()
        ->where('created_at', '<', $cutoff)
        ->when($keepIds !== [], fn ($builder) => $builder->whereNotIn('id', $keepIds));
    $count = (clone $query)->count();

    if ((bool) $this->option('dry-run')) {
        $this->info("{$count} media import row(s) would be pruned.");
        $this->line('Cutoff: '.$cutoff->toDateTimeString());
        $this->line('Keep newest: '.$keep);

        return self::SUCCESS;
    }

    $deleted = $query->delete();

    $this->info("Pruned {$deleted} media import row(s).");
    $this->line('Cutoff: '.$cutoff->toDateTimeString());
    $this->line('Kept newest: '.$keep);

    return self::SUCCESS;
})->purpose('Prune old file-manager import audit rows with a retention policy');

Artisan::command('file-manager:doctor {--ping : Attempt a live storage disk read}', function () {
    $disk = (string) config('file_manager.storage_disk', 'ftp');
    $diskConfig = config("filesystems.disks.{$disk}", []);
    $requiredTables = [
        'media_folders',
        'media',
        'media_in_uses',
        'media_imports',
    ];
    $checks = [];
    $ok = true;

    $addCheck = function (string $label, bool $passed, string $detail = '') use (&$checks, &$ok): void {
        $checks[] = [$label, $passed ? 'OK' : 'FAIL', $detail];
        $ok = $ok && $passed;
    };

    $addCheck('File manager mode', config('file_manager.mode') === 'database', (string) config('file_manager.mode'));
    $addCheck('Storage disk configured', is_array($diskConfig) && $diskConfig !== [], $disk);
    $addCheck('Storage disk driver', (string) ($diskConfig['driver'] ?? '') !== '', (string) ($diskConfig['driver'] ?? 'missing'));
    $addCheck('Public file URL', trim((string) ($diskConfig['url'] ?? '')) !== '', (string) ($diskConfig['url'] ?? 'missing'));
    $addCheck('Guest access locked', config('file_manager.allow_guest') === false, config('file_manager.allow_guest') ? 'enabled' : 'disabled');

    foreach ($requiredTables as $table) {
        $addCheck("Table {$table}", Schema::hasTable($table), Schema::hasTable($table) ? 'present' : 'missing');
    }

    if (($diskConfig['driver'] ?? null) === 'ftp') {
        foreach (['host', 'port', 'username', 'password'] as $key) {
            $value = $diskConfig[$key] ?? null;
            $detail = $key === 'password' && $value ? 'set' : (string) ($value ?: 'missing');

            $addCheck("FTP {$key}", filled($value), $detail);
        }
    }

    if ((bool) $this->option('ping')) {
        try {
            Storage::disk($disk)->files('');
            $addCheck('Storage live read', true, 'root listed');
        } catch (Throwable $exception) {
            $addCheck('Storage live read', false, $exception->getMessage());
        }
    }

    $this->table(['Check', 'Status', 'Detail'], $checks);

    if (! $ok) {
        $this->error('File manager readiness checks failed.');

        return self::FAILURE;
    }

    $this->info('File manager readiness checks passed.');

    return self::SUCCESS;
})->purpose('Check file-manager database/config readiness without exposing secrets');

if ((bool) config('file_manager.import_retention_schedule.enabled', false)) {
    Schedule::command('file-manager:prune-imports')
        ->dailyAt((string) config('file_manager.import_retention_schedule.time', '02:30'))
        ->withoutOverlapping()
        ->runInBackground();
}
