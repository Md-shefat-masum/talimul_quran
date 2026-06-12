<?php

namespace App\Services\FileManager;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FileManagerService
{
    private const DISK = 'ftp';

    /**
     * @return array<string, mixed>
     */
    public function list(string $path = ''): array
    {
        $path = $this->normalizePath($path);
        $disk = Storage::disk(self::DISK);

        $directories = collect($disk->directories($path))
            ->map(fn (string $directory): array => $this->buildDirectoryItem($directory))
            ->values();

        $files = collect($disk->files($path))
            ->map(fn (string $file): array => $this->buildFileItem($file))
            ->values();

        return [
            'path' => $path,
            'breadcrumbs' => $this->breadcrumbs($path),
            'items' => $directories->merge($files)->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function uploadPhoto(UploadedFile $file, string $path = '', ?string $name = null): array
    {
        $path = $this->normalizePath($path);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $baseName = $name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName) ?: 'photo';
        $fileName = $this->uniqueFileName($path, $safeName, $extension);

        $stored = $file->storeAs($path, $fileName, self::DISK);

        if (! is_string($stored) || $stored === '') {
            throw new RuntimeException('Could not upload the selected photo.');
        }

        return $this->buildFileItem($stored);
    }

    /**
     * @return array<string, mixed>
     */
    public function createFolder(string $path, string $name): array
    {
        $path = $this->normalizePath($path);
        $folderName = Str::slug($name) ?: 'new-folder';
        $folderPath = trim($path.'/'.$folderName, '/');

        Storage::disk(self::DISK)->makeDirectory($folderPath);

        return $this->buildDirectoryItem($folderPath);
    }

    public function delete(string $path, string $type): void
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            throw new RuntimeException('Root directory cannot be deleted.');
        }

        $disk = Storage::disk(self::DISK);

        if ($type === 'directory') {
            $disk->deleteDirectory($path);
            return;
        }

        $disk->delete($path);
    }

    /**
     * @return array{contents: string, mime: string, name: string}
     */
    public function readForPreview(string $path): array
    {
        $path = $this->normalizePath($path);
        $disk = Storage::disk(self::DISK);

        if ($path === '' || ! $disk->exists($path)) {
            throw new RuntimeException('File was not found.');
        }

        return [
            'contents' => $disk->get($path),
            'mime' => $disk->mimeType($path) ?: 'application/octet-stream',
            'name' => basename($path),
        ];
    }

    private function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $segments = collect(explode('/', $path))
            ->filter(fn (string $segment): bool => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values();

        return $segments->implode('/');
    }

    /**
     * @return array<int, array{label: string, path: string}>
     */
    private function breadcrumbs(string $path): array
    {
        $crumbs = [['label' => 'Home', 'path' => '']];
        $current = '';

        foreach (array_filter(explode('/', $path)) as $segment) {
            $current = trim($current.'/'.$segment, '/');
            $crumbs[] = [
                'label' => Str::headline($segment),
                'path' => $current,
            ];
        }

        return $crumbs;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDirectoryItem(string $path): array
    {
        return [
            'name' => basename($path),
            'path' => trim($path, '/'),
            'type' => 'directory',
            'extension' => null,
            'mime' => null,
            'size' => null,
            'size_label' => 'Folder',
            'url' => null,
            'preview_url' => null,
            'modified_at' => null,
            'is_image' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFileItem(string $path): array
    {
        $disk = Storage::disk(self::DISK);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $this->safeValue(fn (): ?string => $disk->mimeType($path));
        $size = $this->safeValue(fn (): ?int => $disk->size($path));
        $modified = $this->safeValue(fn (): ?int => $disk->lastModified($path));
        $isImage = str_starts_with((string) $mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
        $publicUrl = $this->publicUrl($path);

        return [
            'name' => basename($path),
            'path' => trim($path, '/'),
            'type' => 'file',
            'extension' => $extension,
            'mime' => $mime,
            'size' => $size,
            'size_label' => $this->formatBytes($size),
            'url' => $publicUrl,
            'preview_url' => $isImage ? ($publicUrl ?: route('backend.file-manager.preview', ['path' => trim($path, '/')])) : null,
            'modified_at' => $modified ? date('Y-m-d H:i:s', $modified) : null,
            'is_image' => $isImage,
        ];
    }

    private function uniqueFileName(string $path, string $baseName, string $extension): string
    {
        $disk = Storage::disk(self::DISK);
        $fileName = "{$baseName}.{$extension}";
        $counter = 2;

        while ($disk->exists(trim($path.'/'.$fileName, '/'))) {
            $fileName = "{$baseName}-{$counter}.{$extension}";
            $counter++;
        }

        return $fileName;
    }

    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $value = max($bytes, 0);
        $unitIndex = 0;

        while ($value >= 1024 && $unitIndex < count($units) - 1) {
            $value /= 1024;
            $unitIndex++;
        }

        return round($value, $unitIndex === 0 ? 0 : 1).' '.$units[$unitIndex];
    }

    private function publicUrl(string $path): ?string
    {
        $baseUrl = rtrim((string) config('filesystems.disks.'.self::DISK.'.url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $encodedPath = collect(explode('/', trim($path, '/')))
            ->map(fn (string $segment): string => rawurlencode($segment))
            ->implode('/');

        return $baseUrl.'/'.$encodedPath;
    }

    /**
     * @template T
     * @param callable(): T $callback
     * @return T|null
     */
    private function safeValue(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Throwable) {
            return null;
        }
    }
}
