<?php

namespace App\Services\FileManager;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\MediaImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaImportService
{
    /**
     * @return array<string, mixed>
     */
    public function import(string $root = 'uploads', bool $recursive = true, bool $dryRun = false, ?int $limit = null): array
    {
        $diskName = $this->diskName();
        $disk = Storage::disk($diskName);
        $root = $this->normalizePath($root ?: 'uploads');
        $summary = [
            'disk' => $diskName,
            'root' => $root,
            'recursive' => $recursive,
            'dry_run' => $dryRun,
            'limit' => $limit,
            'scanned' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'items' => [],
            'errors' => [],
            'started_at' => now(),
        ];

        try {
            $paths = collect($recursive ? $disk->allFiles($root) : $disk->files($root))
                ->map(fn (string $path): string => $this->normalizePath($path))
                ->filter()
                ->values();
        } catch (\Throwable $exception) {
            $summary['failed'] = 1;
            $summary['errors'][] = [
                'path' => $root,
                'message' => $exception->getMessage(),
            ];

            return $this->recordImport($summary);
        }

        if ($limit !== null && $limit > 0) {
            $paths = $paths->take($limit)->values();
        }

        $summary['scanned'] = $paths->count();

        foreach ($paths as $path) {
            try {
                $result = $this->importPath($diskName, $path, $dryRun);
                $summary[$result['status']]++;
                $summary['items'][] = $result;
            } catch (\Throwable $exception) {
                $summary['failed']++;
                $summary['errors'][] = [
                    'path' => $path,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return $this->recordImport($summary);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recentImports(int $limit = 5): array
    {
        return MediaImport::query()
            ->latest()
            ->limit(max(1, min($limit, 20)))
            ->get()
            ->map(fn (MediaImport $import): array => $import->toFileManagerArray())
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function importPath(string $diskName, string $path, bool $dryRun): array
    {
        $existing = Media::query()
            ->where('disk', $diskName)
            ->where('path', $path)
            ->first();
        $folder = $this->ensureFolderPath(dirname($path), $dryRun);
        $payload = [
            'filename' => basename($path),
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            'mime_type' => $this->safeDiskValue(fn () => Storage::disk($diskName)->mimeType($path)),
            'size' => $this->safeDiskValue(fn () => Storage::disk($diskName)->size($path)),
            'media_folder_id' => $folder?->id,
            'folders' => $folder ? $this->folderTrail($folder) : [],
            'slug' => Str::slug(pathinfo($path, PATHINFO_FILENAME)),
            'status' => 1,
        ];

        if ($dryRun) {
            return [
                'status' => $existing ? 'skipped' : 'created',
                'path' => $path,
                'media_id' => $existing?->id,
                'folder_id' => $folder?->id,
                'dry_run' => true,
            ];
        }

        if ($existing) {
            $existing->fill($payload);
            $existing->save();

            return [
                'status' => 'updated',
                'path' => $path,
                'media_id' => $existing->id,
                'folder_id' => $existing->media_folder_id,
            ];
        }

        $media = Media::query()->create(array_merge($payload, [
            'disk' => $diskName,
            'path' => $path,
            'creator' => auth()->id(),
        ]));

        return [
            'status' => 'created',
            'path' => $path,
            'media_id' => $media->id,
            'folder_id' => $media->media_folder_id,
        ];
    }

    private function ensureFolderPath(string $path, bool $dryRun): ?MediaFolder
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            return null;
        }

        $parent = null;

        foreach (explode('/', $path) as $segment) {
            $folder = MediaFolder::query()
                ->active()
                ->where('parent_id', $parent?->id ?? 0)
                ->where('saved_name_into_storage', $segment)
                ->first();

            if (! $folder && $dryRun) {
                return $parent;
            }

            if (! $folder) {
                $folder = MediaFolder::query()->create([
                    'name' => Str::headline($segment),
                    'saved_name_into_storage' => $segment,
                    'parent_id' => $parent?->id ?? 0,
                    'is_default' => 0,
                    'creator' => auth()->id(),
                    'slug' => Str::slug($segment),
                    'status' => 1,
                ]);
            }

            $parent = $folder;
        }

        return $parent;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function folderTrail(MediaFolder $folder): array
    {
        $items = collect();
        $current = $folder;

        while ($current) {
            $items->prepend($current);
            $current = $current->parent_id ? MediaFolder::query()->find($current->parent_id) : null;
        }

        return $items
            ->map(fn (MediaFolder $item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'path' => $items->takeUntil(fn (MediaFolder $candidate): bool => $candidate->id === $item->id)
                    ->push($item)
                    ->pluck('saved_name_into_storage')
                    ->implode('/'),
            ])
            ->values()
            ->all();
    }

    private function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $segments = collect(explode('/', $path))
            ->filter(fn (string $segment): bool => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values();

        return $segments->implode('/');
    }

    private function diskName(): string
    {
        return (string) config('file_manager.storage_disk', 'ftp');
    }

    /**
     * @param array<string, mixed> $summary
     * @return array<string, mixed>
     */
    private function recordImport(array $summary): array
    {
        $status = $summary['failed'] > 0
            ? ($summary['created'] > 0 || $summary['updated'] > 0 || $summary['skipped'] > 0 ? 'completed_with_errors' : 'failed')
            : ($summary['dry_run'] ? 'dry_run' : 'completed');
        $finishedAt = now();

        $import = MediaImport::query()->create([
            'disk' => $summary['disk'],
            'root' => $summary['root'],
            'recursive' => $summary['recursive'],
            'dry_run' => $summary['dry_run'],
            'limit' => $summary['limit'],
            'status' => $status,
            'scanned' => $summary['scanned'],
            'created' => $summary['created'],
            'updated' => $summary['updated'],
            'skipped' => $summary['skipped'],
            'failed' => $summary['failed'],
            'items' => array_slice($summary['items'], 0, 200),
            'errors' => $summary['errors'],
            'creator' => auth()->id(),
            'started_at' => $summary['started_at'],
            'finished_at' => $finishedAt,
        ]);

        return array_merge($summary, [
            'import_id' => $import->id,
            'status' => $status,
            'started_at' => $summary['started_at']->toDateTimeString(),
            'finished_at' => $finishedAt->toDateTimeString(),
        ]);
    }

    private function safeDiskValue(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable) {
            return null;
        }
    }
}
