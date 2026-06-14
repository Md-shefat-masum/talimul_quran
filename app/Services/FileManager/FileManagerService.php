<?php

namespace App\Services\FileManager;

use App\Exceptions\FileManager\DuplicateFileException;
use App\Exceptions\FileManager\FileInUseException;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FileManagerService
{
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
        $folder = $this->folderFromPath($path);
        $folderId = $folder?->id;
        $page = max(1, $page);
        $perPage = max(1, min(120, $perPage));
        $query = trim($query);
        $offset = ($page - 1) * $perPage;

        $folderQuery = MediaFolder::query()
            ->active()
            ->where('parent_id', $folderId ?: 0)
            ->when($query !== '', fn ($builder) => $builder->where('name', 'like', '%'.$query.'%'));

        $mediaQuery = Media::query()
            ->active()
            ->where('media_folder_id', $folderId)
            ->when($query !== '', fn ($builder) => $builder->where('filename', 'like', '%'.$query.'%'));

        $folderTotal = (clone $folderQuery)->count();
        $mediaTotal = (clone $mediaQuery)->count();
        $total = $folderTotal + $mediaTotal;
        $folders = collect();
        $media = collect();

        if ($offset < $folderTotal) {
            $folders = (clone $folderQuery)
                ->orderBy('name')
                ->offset($offset)
                ->limit($perPage)
                ->get();
        }

        $remaining = $perPage - $folders->count();

        if ($remaining > 0) {
            $mediaOffset = max(0, $offset - $folderTotal);
            $media = (clone $mediaQuery)
                ->with('folder')
                ->orderBy('filename')
                ->offset($mediaOffset)
                ->limit($remaining)
                ->get();
        }

        $pagedItems = $folders
            ->map(fn (MediaFolder $mediaFolder): array => $this->buildDirectoryItem($mediaFolder))
            ->merge($media->map(fn (Media $item): array => $this->buildFileItem($item)))
            ->values();

        return [
            'path' => $this->folderPath($folder),
            'folder_id' => $folderId,
            'breadcrumbs' => $this->breadcrumbs($folder),
            'items' => $pagedItems->all(),
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
     * @return array<int, array<string, mixed>>
     */
    public function tree(string $parent = ''): array
    {
        $folder = $this->folderFromPath($parent);
        $folders = MediaFolder::query()
            ->active()
            ->where('parent_id', $folder?->id ?? 0)
            ->withCount([
                'children as children_count' => fn ($builder) => $builder->active(),
            ])
            ->orderBy('name')
            ->get();

        return $folders
            ->map(fn (MediaFolder $mediaFolder): array => $this->buildTreeDirectoryItem($mediaFolder))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function uploadPhoto(UploadedFile $file, string $path = '', ?string $name = null, string $conflictStrategy = 'rename'): array
    {
        $folder = $this->ensureFolderPath($path);
        $diskName = $this->diskName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $baseName = $name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName) ?: 'photo';
        $fileName = "{$safeName}.{$extension}";
        $folderPath = $this->folderPath($folder);
        $targetPath = trim($folderPath.'/'.$fileName, '/');

        if ($this->mediaPathExists($targetPath, $diskName)) {
            if ($conflictStrategy === 'error') {
                $suggestedFileName = $this->uniqueFileName($folder, $safeName, $extension, $diskName);

                throw new DuplicateFileException([
                    'path' => $targetPath,
                    'name' => $fileName,
                    'suggested_name' => pathinfo($suggestedFileName, PATHINFO_FILENAME),
                    'suggested_file_name' => $suggestedFileName,
                    'strategy' => $conflictStrategy,
                ]);
            }

            if ($conflictStrategy !== 'replace') {
                $fileName = $this->uniqueFileName($folder, $safeName, $extension, $diskName);
                $targetPath = trim($folderPath.'/'.$fileName, '/');
            }
        }

        $stored = $file->storeAs($folderPath, $fileName, $diskName);

        if (! is_string($stored) || $stored === '') {
            throw new RuntimeException('Could not upload the selected photo.');
        }

        $media = Media::query()->updateOrCreate(
            [
                'disk' => $diskName,
                'path' => $stored,
            ],
            [
                'filename' => basename($stored),
                'extension' => strtolower(pathinfo($stored, PATHINFO_EXTENSION)),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'media_folder_id' => $folder->id,
                'folders' => $this->folderTrail($folder),
                'creator' => auth()->id(),
                'slug' => Str::slug(pathinfo($stored, PATHINFO_FILENAME)),
                'status' => 1,
            ],
        );

        return $this->buildFileItem($media->refresh());
    }

    /**
     * @return array<string, mixed>
     */
    public function createFolder(string $path, string $name): array
    {
        $parent = $this->folderFromPath($path);
        $parentId = $parent?->id ?? 0;
        $folderName = trim($name) ?: 'New folder';
        $savedName = $this->uniqueFolderStorageName($parent, Str::slug($folderName) ?: 'new-folder');

        $folder = MediaFolder::query()->create([
            'name' => $folderName,
            'saved_name_into_storage' => $savedName,
            'parent_id' => $parentId,
            'is_default' => 0,
            'creator' => auth()->id(),
            'slug' => Str::slug($folderName),
            'status' => 1,
        ]);

        return $this->buildDirectoryItem($folder);
    }

    public function delete(string $path, string $type, bool $force = false): void
    {
        if ($type === 'directory') {
            $folder = $this->folderFromPath($path);

            if (! $folder) {
                throw new RuntimeException('Folder was not found.');
            }

            $usageSummary = $this->usageService->summary((string) $folder->id, 'directory');

            if (! $force && (int) $usageSummary['count'] > 0) {
                throw new FileInUseException($usageSummary);
            }

            $folder->status = 0;
            $folder->save();

            return;
        }

        $media = $this->mediaFromPath($path);

        if (! $media) {
            throw new RuntimeException('File was not found.');
        }

        $usageSummary = $this->usageService->summary((string) $media->path, 'file');

        if (! $force && (int) $usageSummary['count'] > 0) {
            throw new FileInUseException($usageSummary);
        }

        $media->status = 0;
        $media->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function rename(string $path, string $type, string $name): array
    {
        if ($type === 'directory') {
            $folder = $this->folderFromPath($path);

            if (! $folder) {
                throw new RuntimeException('Folder was not found.');
            }

            $folder->name = trim($name) ?: $folder->name;
            $folder->slug = Str::slug($folder->name);
            $folder->save();

            return $this->buildDirectoryItem($folder->refresh());
        }

        $media = $this->mediaFromPath($path);

        if (! $media) {
            throw new RuntimeException('File was not found.');
        }

        $extension = $media->extension ?: pathinfo((string) $media->path, PATHINFO_EXTENSION);
        $baseName = pathinfo($name, PATHINFO_FILENAME) ?: $name;
        $safeBaseName = Str::slug($baseName) ?: 'file';
        $fileName = $safeBaseName.'.'.strtolower($extension ?: 'file');
        if ($this->mediaDisplayNameExists($media->media_folder_id ? (int) $media->media_folder_id : null, $fileName, $media->id)) {
            throw new RuntimeException('An item with this name already exists.');
        }

        $media->filename = $fileName;
        $media->slug = $safeBaseName;
        $media->save();

        return $this->buildFileItem($media->refresh());
    }

    /**
     * @return array<string, mixed>
     */
    public function move(string $path, string $type, string $destination): array
    {
        $destinationFolder = $this->folderFromPath($destination);

        if ($type === 'directory') {
            $folder = $this->folderFromPath($path);

            if (! $folder) {
                throw new RuntimeException('Folder was not found.');
            }

            if ($destinationFolder && $this->folderPath($destinationFolder) === $this->folderPath($folder)) {
                return $this->buildDirectoryItem($folder);
            }

            $folder->parent_id = $destinationFolder?->id ?? 0;
            $folder->save();

            return $this->buildDirectoryItem($folder->refresh());
        }

        $media = $this->mediaFromPath($path);

        if (! $media) {
            throw new RuntimeException('File was not found.');
        }

        if ($this->mediaDisplayNameExists($destinationFolder?->id, (string) $media->filename, $media->id)) {
            throw new RuntimeException('An item already exists in the destination folder.');
        }

        $media->media_folder_id = $destinationFolder?->id;
        $media->folders = $this->folderTrail($destinationFolder);
        $media->save();

        return $this->buildFileItem($media->refresh());
    }

    /**
     * @return array{contents: string, mime: string, name: string}
     */
    public function readForPreview(string $path): array
    {
        $media = $this->mediaFromPath($path);

        if (! $media || ! Storage::disk((string) $media->disk)->exists((string) $media->path)) {
            throw new RuntimeException('File was not found.');
        }

        return [
            'contents' => Storage::disk((string) $media->disk)->get((string) $media->path),
            'mime' => $media->mime_type ?: 'application/octet-stream',
            'name' => $media->filename ?: basename((string) $media->path),
        ];
    }

    /**
     * @return array{contents: string, mime: string, name: string}
     */
    public function readThumbnail(string $path, int $width = 360, int $height = 270): array
    {
        $media = $this->mediaFromPath($path);

        if (! $media) {
            throw new RuntimeException('File was not found.');
        }

        $mime = $media->mime_type ?: 'application/octet-stream';

        if (! str_starts_with($mime, 'image/')) {
            return $this->readForPreview((string) $media->path);
        }

        $cachePath = $this->thumbnailCachePath((string) $media->path, $width, $height);

        if (is_file($cachePath)) {
            return [
                'contents' => (string) file_get_contents($cachePath),
                'mime' => 'image/jpeg',
                'name' => pathinfo((string) $media->path, PATHINFO_FILENAME).'-thumb.jpg',
            ];
        }

        $source = $this->readForPreview((string) $media->path);
        $thumbnail = $this->makeThumbnail($source['contents'], max(80, min(1200, $width)), max(80, min(1200, $height)));

        if ($thumbnail === null) {
            return $source;
        }

        $directory = dirname($cachePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($cachePath, $thumbnail);

        return [
            'contents' => $thumbnail,
            'mime' => 'image/jpeg',
            'name' => pathinfo((string) $media->path, PATHINFO_FILENAME).'-thumb.jpg',
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

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public function updateFolderPermissionOverrides(string $folderIdentifier, array $overrides): array
    {
        $folder = $this->folderFromPath($folderIdentifier);

        if (! $folder) {
            throw new RuntimeException('Folder was not found.');
        }

        $allowedAbilities = array_keys(config('file_manager.abilities', []));
        $normalized = [];

        foreach ($allowedAbilities as $ability) {
            if (! array_key_exists($ability, $overrides) || $overrides[$ability] === null || $overrides[$ability] === '') {
                continue;
            }

            $normalized[$ability] = filter_var($overrides[$ability], FILTER_VALIDATE_BOOLEAN);
        }

        $folder->permission_overrides = $normalized;
        $folder->save();

        return $this->buildDirectoryItem($folder->refresh());
    }

    private function diskName(): string
    {
        return (string) config('file_manager.storage_disk', 'ftp');
    }

    private function folderFromPath(?string $path): ?MediaFolder
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            return null;
        }

        if (ctype_digit($path)) {
            return MediaFolder::query()->active()->find((int) $path);
        }

        $parentId = 0;
        $folder = null;

        foreach (explode('/', $path) as $segment) {
            $folder = MediaFolder::query()
                ->active()
                ->where('parent_id', $parentId)
                ->where('saved_name_into_storage', $segment)
                ->first();

            if (! $folder) {
                return null;
            }

            $parentId = $folder->id;
        }

        return $folder;
    }

    private function ensureFolderPath(?string $path): MediaFolder
    {
        $path = $this->normalizePath($path);
        $folder = $this->folderFromPath($path);

        if ($folder) {
            return $folder;
        }

        $parent = null;

        foreach (array_filter(explode('/', $path ?: 'uploads')) as $segment) {
            $existing = MediaFolder::query()
                ->active()
                ->where('parent_id', $parent?->id ?? 0)
                ->where('saved_name_into_storage', $segment)
                ->first();

            if ($existing) {
                $parent = $existing;
                continue;
            }

            $parent = MediaFolder::query()->create([
                'name' => Str::headline($segment),
                'saved_name_into_storage' => $segment,
                'parent_id' => $parent?->id ?? 0,
                'is_default' => 0,
                'creator' => auth()->id(),
                'slug' => Str::slug($segment),
                'status' => 1,
            ]);
        }

        return $parent ?: MediaFolder::query()->active()->where('is_default', 1)->firstOrFail();
    }

    private function mediaFromPath(?string $path): ?Media
    {
        $path = $this->normalizePath($path);

        if ($path === '') {
            return null;
        }

        if (ctype_digit($path)) {
            return Media::query()->active()->find((int) $path);
        }

        return Media::query()
            ->active()
            ->where('path', $path)
            ->first();
    }

    private function normalizePath(?string $path): string
    {
        $path = str_replace('\\', '/', (string) $path);
        $segments = collect(explode('/', $path))
            ->filter(fn (string $segment): bool => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values();

        return $segments->implode('/');
    }

    private function folderPath(?MediaFolder $folder): string
    {
        if (! $folder) {
            return '';
        }

        return $this->folderAncestors($folder)
            ->pluck('saved_name_into_storage')
            ->filter()
            ->implode('/');
    }

    private function folderDisplayPath(?MediaFolder $folder): string
    {
        if (! $folder) {
            return '';
        }

        return $this->folderAncestors($folder)
            ->pluck('name')
            ->filter()
            ->implode('/');
    }

    private function folderTrail(?MediaFolder $folder): array
    {
        return $this->folderAncestors($folder)
            ->map(fn (MediaFolder $item): array => [
            'id' => $item->id,
            'name' => $item->name,
            'path' => $this->folderPath($item),
            'display_path' => $this->folderDisplayPath($item),
        ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, MediaFolder>
     */
    private function folderAncestors(?MediaFolder $folder): Collection
    {
        $items = collect();
        $current = $folder;

        while ($current) {
            $items->prepend($current);
            $current = $current->parent_id ? MediaFolder::query()->find($current->parent_id) : null;
        }

        return $items->values();
    }

    /**
     * @return array<int, array{label: string, path: string, folder_id?: int|null}>
     */
    private function breadcrumbs(?MediaFolder $folder): array
    {
        $crumbs = [['label' => 'Home', 'path' => '', 'folder_id' => null]];

        foreach ($this->folderAncestors($folder) as $item) {
            $crumbs[] = [
                'label' => $item->name ?: 'Folder',
                'path' => $this->folderPath($item),
                'display_path' => $this->folderDisplayPath($item),
                'folder_id' => $item->id,
            ];
        }

        return $crumbs;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDirectoryItem(MediaFolder $folder): array
    {
        $path = $this->folderPath($folder);

        return [
            'id' => $folder->id,
            'name' => $folder->name ?: basename($path),
            'path' => $path,
            'display_path' => $this->folderDisplayPath($folder),
            'storage_path' => null,
            'folder_id' => $folder->id,
            'type' => 'directory',
            'extension' => null,
            'mime' => null,
            'size' => null,
            'size_label' => 'Folder',
            'url' => null,
            'preview_url' => null,
            'thumbnail_url' => null,
            'modified_at' => optional($folder->updated_at)->format('Y-m-d H:i:s'),
            'is_image' => false,
            'permission_overrides' => $folder->permission_overrides ?: [],
            'usage' => $this->usageService->summary((string) $folder->id, 'directory', 3),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTreeDirectoryItem(MediaFolder $folder): array
    {
        $path = $this->folderPath($folder);
        $childrenCount = (int) ($folder->children_count ?? 0);

        return [
            'id' => $folder->id,
            'folder_id' => $folder->id,
            'name' => $folder->name ?: basename($path),
            'path' => $path,
            'display_path' => $this->folderDisplayPath($folder),
            'type' => 'directory',
            'children' => [],
            'children_count' => $childrenCount,
            'has_children' => $childrenCount > 0,
            'children_loaded' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFileItem(Media $media): array
    {
        $mime = (string) $media->mime_type;
        $extension = strtolower((string) ($media->extension ?: pathinfo((string) $media->path, PATHINFO_EXTENSION)));
        $isImage = str_starts_with($mime, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
        $url = $this->publicUrl($media);

        return [
            'id' => $media->id,
            'media_id' => $media->id,
            'name' => $media->filename ?: basename((string) $media->path),
            'path' => trim((string) $media->path, '/'),
            'display_path' => trim($this->folderDisplayPath($media->folder).'/'.($media->filename ?: basename((string) $media->path)), '/'),
            'storage_path' => trim((string) $media->path, '/'),
            'folder_id' => $media->media_folder_id,
            'type' => 'file',
            'extension' => $extension,
            'mime' => $mime ?: null,
            'mime_type' => $mime ?: null,
            'size' => $media->size,
            'size_label' => $this->formatBytes($media->size),
            'url' => $url,
            'preview_url' => $isImage ? ($url ?: route('backend.file-manager.preview', ['path' => trim((string) $media->path, '/')])) : null,
            'thumbnail_url' => $isImage ? route('backend.file-manager.thumbnail', ['path' => trim((string) $media->path, '/')]) : null,
            'modified_at' => optional($media->updated_at)->format('Y-m-d H:i:s'),
            'is_image' => $isImage,
            'usage' => $this->usageService->summary((string) $media->path, 'file', 3),
        ];
    }

    private function mediaPathExists(string $path, string $disk, ?int $ignoreId = null): bool
    {
        return Media::query()
            ->active()
            ->where('disk', $disk)
            ->where('path', trim($path, '/'))
            ->when($ignoreId, fn ($builder) => $builder->whereKeyNot($ignoreId))
            ->exists();
    }

    private function mediaDisplayNameExists(?int $folderId, string $filename, ?int $ignoreId = null): bool
    {
        return Media::query()
            ->active()
            ->when(
                $folderId,
                fn ($builder) => $builder->where('media_folder_id', $folderId),
                fn ($builder) => $builder->whereNull('media_folder_id'),
            )
            ->where('filename', $filename)
            ->when($ignoreId, fn ($builder) => $builder->whereKeyNot($ignoreId))
            ->exists();
    }

    private function uniqueFileName(MediaFolder $folder, string $baseName, string $extension, string $disk): string
    {
        $folderPath = $this->folderPath($folder);
        $fileName = "{$baseName}.{$extension}";
        $counter = 2;

        while ($this->mediaPathExists(trim($folderPath.'/'.$fileName, '/'), $disk)) {
            $fileName = "{$baseName}-{$counter}.{$extension}";
            $counter++;
        }

        return $fileName;
    }

    private function uniqueFolderStorageName(?MediaFolder $parent, string $baseName, ?int $ignoreId = null): string
    {
        $name = $baseName;
        $counter = 2;

        while (MediaFolder::query()
            ->active()
            ->where('parent_id', $parent?->id ?? 0)
            ->where('saved_name_into_storage', $name)
            ->when($ignoreId, fn ($builder) => $builder->whereKeyNot($ignoreId))
            ->exists()) {
            $name = "{$baseName}-{$counter}";
            $counter++;
        }

        return $name;
    }

    private function thumbnailCachePath(string $path, int $width, int $height): string
    {
        $media = $this->mediaFromPath($path);
        $key = sha1($path.'|'.optional($media?->updated_at)->timestamp.'|'.($media?->size ?? 0).'|'.$width.'x'.$height);

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

    private function publicUrl(Media $media): ?string
    {
        $baseUrl = rtrim((string) config('filesystems.disks.'.$media->disk.'.url', ''), '/');

        if ($baseUrl === '') {
            return null;
        }

        $encodedPath = collect(explode('/', trim((string) $media->path, '/')))
            ->map(fn (string $segment): string => rawurlencode($segment))
            ->implode('/');

        return $baseUrl.'/'.$encodedPath;
    }
}
