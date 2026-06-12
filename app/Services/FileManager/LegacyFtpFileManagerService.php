<?php

namespace App\Services\FileManager;

use App\Exceptions\FileManager\DuplicateFileException;
use App\Exceptions\FileManager\FileInUseException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class LegacyFtpFileManagerService
{
    private const DISK = 'ftp';
    private const THUMBNAIL_CACHE_DIR = 'file-manager-thumbnails';

    public function __construct(
        private readonly FileManagerUsageService $usageService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function list(string $path = '', int $page = 1, int $perPage = 60, string $query = ''): array
    {
        $path = $this->normalizePath($path);
        $disk = Storage::disk(self::DISK);
        $page = max(1, $page);
        $perPage = max(1, min(120, $perPage));
        $query = trim(Str::lower($query));

        $directories = collect($disk->directories($path))
            ->filter(fn (string $directory): bool => $this->matchesQuery($directory, $query))
            ->sortBy(fn (string $directory): string => Str::lower(basename($directory)))
            ->values();

        $files = collect($disk->files($path))
            ->filter(fn (string $file): bool => $this->matchesQuery($file, $query))
            ->sortBy(fn (string $file): string => Str::lower(basename($file)))
            ->values();
        $paths = $directories->merge($files)->values();
        $total = $paths->count();
        $pagePaths = $paths
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();
        $items = $pagePaths->map(function (string $itemPath) use ($directories): array {
            return $directories->contains($itemPath)
                ? $this->buildDirectoryItem($itemPath)
                : $this->buildFileItem($itemPath);
        })->values();

        return [
            'path' => $path,
            'breadcrumbs' => $this->breadcrumbs($path),
            'items' => $items->all(),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'shown' => min($total, $page * $perPage),
                'has_more' => ($page * $perPage) < $total,
                'next_page' => ($page * $perPage) < $total ? $page + 1 : null,
                'query' => $query,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function uploadPhoto(UploadedFile $file, string $path = '', ?string $name = null, string $conflictStrategy = 'rename'): array
    {
        $path = $this->normalizePath($path);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $baseName = $name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName) ?: 'photo';
        $fileName = "{$safeName}.{$extension}";
        $targetPath = trim($path.'/'.$fileName, '/');

        if ($this->pathExists($targetPath)) {
            if ($conflictStrategy === 'error') {
                $suggestedFileName = $this->uniqueFileName($path, $safeName, $extension);

                throw new DuplicateFileException([
                    'path' => $targetPath,
                    'name' => $fileName,
                    'suggested_name' => pathinfo($suggestedFileName, PATHINFO_FILENAME),
                    'suggested_file_name' => $suggestedFileName,
                    'strategy' => $conflictStrategy,
                ]);
            }

            if ($conflictStrategy !== 'replace') {
                $fileName = $this->uniqueFileName($path, $safeName, $extension);
            }
        }

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

    public function delete(string $path, string $type, bool $force = false): void
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            throw new RuntimeException('Root directory cannot be deleted.');
        }

        $usageSummary = $this->usageService->summary($path, $type);

        if (! $force && (int) $usageSummary['count'] > 0) {
            throw new FileInUseException($usageSummary);
        }

        $disk = Storage::disk(self::DISK);

        if ($type === 'directory') {
            $disk->deleteDirectory($path);
            return;
        }

        $disk->delete($path);
    }

    /**
     * @return array<string, mixed>
     */
    public function rename(string $path, string $type, string $name): array
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            throw new RuntimeException('Root directory cannot be renamed.');
        }

        $disk = Storage::disk(self::DISK);
        $targetPath = trim($this->parentPath($path).'/'.$this->safeItemName($name, $type, $path), '/');

        if ($targetPath === $path) {
            return $type === 'directory'
                ? $this->buildDirectoryItem($path)
                : $this->buildFileItem($path);
        }

        if ($this->pathExists($targetPath)) {
            throw new RuntimeException('An item with this name already exists.');
        }

        if (! $disk->move($path, $targetPath)) {
            throw new RuntimeException('Could not rename the selected item.');
        }

        $this->usageService->remapPath($path, $targetPath, $type);

        return $type === 'directory'
            ? $this->buildDirectoryItem($targetPath)
            : $this->buildFileItem($targetPath);
    }

    /**
     * @return array<string, mixed>
     */
    public function move(string $path, string $type, string $destination): array
    {
        $path = $this->normalizePath($path);
        $destination = $this->normalizePath($destination);

        if ($path === '') {
            throw new RuntimeException('Root directory cannot be moved.');
        }

        if ($type === 'directory' && ($destination === $path || str_starts_with($destination, $path.'/'))) {
            throw new RuntimeException('A folder cannot be moved into itself.');
        }

        $disk = Storage::disk(self::DISK);
        $targetPath = trim($destination.'/'.basename($path), '/');

        if ($targetPath === $path) {
            return $type === 'directory'
                ? $this->buildDirectoryItem($path)
                : $this->buildFileItem($path);
        }

        if ($this->pathExists($targetPath)) {
            throw new RuntimeException('An item already exists in the destination folder.');
        }

        if (! $disk->move($path, $targetPath)) {
            throw new RuntimeException('Could not move the selected item.');
        }

        $this->usageService->remapPath($path, $targetPath, $type);

        return $type === 'directory'
            ? $this->buildDirectoryItem($targetPath)
            : $this->buildFileItem($targetPath);
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

    /**
     * @return array{contents: string, mime: string, name: string}
     */
    public function readThumbnail(string $path, int $width = 360, int $height = 270): array
    {
        $path = $this->normalizePath($path);
        $width = max(80, min(1200, $width));
        $height = max(80, min(1200, $height));
        $disk = Storage::disk(self::DISK);

        if ($path === '' || ! $disk->exists($path)) {
            throw new RuntimeException('File was not found.');
        }

        $mime = $this->safeValue(fn (): ?string => $disk->mimeType($path)) ?: 'application/octet-stream';

        if (! str_starts_with($mime, 'image/')) {
            return $this->readForPreview($path);
        }

        $cachePath = $this->thumbnailCachePath($path, $width, $height);

        if (is_file($cachePath)) {
            return [
                'contents' => (string) file_get_contents($cachePath),
                'mime' => 'image/jpeg',
                'name' => pathinfo($path, PATHINFO_FILENAME).'-thumb.jpg',
            ];
        }

        $contents = $disk->get($path);
        $thumbnail = $this->makeThumbnail($contents, $width, $height);

        if ($thumbnail === null) {
            return [
                'contents' => $contents,
                'mime' => $mime,
                'name' => basename($path),
            ];
        }

        $directory = dirname($cachePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($cachePath, $thumbnail);

        return [
            'contents' => $thumbnail,
            'mime' => 'image/jpeg',
            'name' => pathinfo($path, PATHINFO_FILENAME).'-thumb.jpg',
        ];
    }

    /**
     * @return array{files: int, bytes: int, bytes_label: string, path: string}
     */
    public function thumbnailCacheStats(): array
    {
        $directory = $this->thumbnailCacheDirectory();
        $files = is_dir($directory) ? File::allFiles($directory) : [];
        $bytes = collect($files)->sum(fn (\SplFileInfo $file): int => $file->getSize());

        return [
            'files' => count($files),
            'bytes' => $bytes,
            'bytes_label' => $this->formatBytes($bytes),
            'path' => $directory,
        ];
    }

    /**
     * @return array{files: int, bytes: int, bytes_label: string, path: string}
     */
    public function clearThumbnailCache(): array
    {
        $stats = $this->thumbnailCacheStats();
        $directory = $this->thumbnailCacheDirectory();

        if (is_dir($directory)) {
            File::deleteDirectory($directory);
        }

        return $stats;
    }

    private function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $segments = collect(explode('/', $path))
            ->filter(fn (string $segment): bool => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values();

        return $segments->implode('/');
    }

    private function parentPath(string $path): string
    {
        $parent = dirname($path);

        return $parent === '.' ? '' : trim($parent, '/');
    }

    private function matchesQuery(string $path, string $query): bool
    {
        return $query === '' || str_contains(Str::lower(basename($path)), $query);
    }

    private function safeItemName(string $name, string $type, string $currentPath): string
    {
        $name = trim(str_replace('\\', '/', $name), '/');
        $extension = pathinfo($currentPath, PATHINFO_EXTENSION);

        if ($type === 'file') {
            $newExtension = pathinfo($name, PATHINFO_EXTENSION);
            $baseName = $newExtension !== '' ? pathinfo($name, PATHINFO_FILENAME) : $name;
            $safeBaseName = Str::slug($baseName) ?: 'file';

            return $safeBaseName.'.'.strtolower($newExtension ?: $extension ?: 'file');
        }

        return Str::slug($name) ?: 'new-folder';
    }

    private function pathExists(string $path): bool
    {
        $disk = Storage::disk(self::DISK);

        if ($disk->exists($path)) {
            return true;
        }

        return method_exists($disk, 'directoryExists') && $disk->directoryExists($path);
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
            'thumbnail_url' => null,
            'modified_at' => null,
            'is_image' => false,
            'usage' => $this->usageService->summary(trim($path, '/'), 'directory', 3),
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
            'thumbnail_url' => $isImage ? route('backend.file-manager.thumbnail', ['path' => trim($path, '/')]) : null,
            'modified_at' => $modified ? date('Y-m-d H:i:s', $modified) : null,
            'is_image' => $isImage,
            'usage' => $this->usageService->summary(trim($path, '/'), 'file', 3),
        ];
    }

    private function thumbnailCachePath(string $path, int $width, int $height): string
    {
        $disk = Storage::disk(self::DISK);
        $modified = $this->safeValue(fn (): ?int => $disk->lastModified($path)) ?: 0;
        $size = $this->safeValue(fn (): ?int => $disk->size($path)) ?: 0;
        $key = sha1($path.'|'.$modified.'|'.$size.'|'.$width.'x'.$height);

        return $this->thumbnailCacheDirectory().'/'.$key.'.jpg';
    }

    private function thumbnailCacheDirectory(): string
    {
        return storage_path('app/private/'.self::THUMBNAIL_CACHE_DIR);
    }

    private function makeThumbnail(string $contents, int $width, int $height): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $source = @imagecreatefromstring($contents);

        if (! $source) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth < 1 || $sourceHeight < 1) {
            imagedestroy($source);
            return null;
        }

        $scale = min($width / $sourceWidth, $height / $sourceHeight, 1);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        ob_start();
        imagejpeg($target, null, 82);
        $thumbnail = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return is_string($thumbnail) && $thumbnail !== '' ? $thumbnail : null;
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
