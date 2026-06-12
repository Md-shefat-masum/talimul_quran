<?php

namespace App\Services\FileManager;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\MediaInUse;
use Illuminate\Support\Collection;

class FileManagerUsageService
{
    /**
     * @param array<int, array<string, mixed>> $items
     * @param array<string, mixed> $context
     * @return array<int, array<string, mixed>>
     */
    public function track(array $items, array $context): array
    {
        $fieldName = trim((string) ($context['field_name'] ?? $context['field'] ?? ''));

        if ($fieldName === '') {
            return [];
        }

        return collect($items)
            ->map(fn (array $item): ?MediaInUse => $this->trackItem($item, $context, $fieldName))
            ->filter()
            ->map(fn (MediaInUse $usage): array => $this->formatUsage($usage))
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $context
     */
    public function forget(string $path, array $context = []): int
    {
        $media = $this->mediaFromPathOrUrl($path);

        if (! $media) {
            return 0;
        }

        $query = MediaInUse::query()->where('media_id', $media->id);
        $this->applyContextFilters($query, $context);

        return $query->delete();
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(string $path, string $type = 'file', int $limit = 8): array
    {
        $query = MediaInUse::query()->active()->with('media');

        if ($type === 'directory') {
            $path = $this->normalizePath($path);
            $folder = $this->folderFromPath($path);
            $folderIds = $folder ? $this->folderAndDescendantIds($folder) : collect();
            $mediaIds = Media::query()
                ->active()
                ->when(
                    $folder,
                    fn ($builder) => $builder->whereIn('media_folder_id', $folderIds),
                    fn ($builder) => $path === '' ? $builder : $builder->whereRaw('1 = 0'),
                )
                ->pluck('id');

            $query->whereIn('media_id', $mediaIds);
        } else {
            $media = $this->mediaFromPathOrUrl($path);
            $query->where('media_id', $media?->id ?: 0);
        }

        $count = (clone $query)->count();
        $items = (clone $query)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (MediaInUse $usage): array => $this->formatUsage($usage))
            ->values();

        return [
            'count' => $count,
            'has_usage' => $count > 0,
            'items' => $items,
        ];
    }

    public function usageCount(string $path, string $type = 'file'): int
    {
        return (int) $this->summary($path, $type, 0)['count'];
    }

    public function remapPath(string $oldPath, string $newPath, string $type = 'file'): int
    {
        return 0;
    }

    /**
     * @param array<string, mixed> $item
     * @param array<string, mixed> $context
     */
    private function trackItem(array $item, array $context, string $fieldName): ?MediaInUse
    {
        $media = $this->mediaFromItem($item);

        if (! $media) {
            return null;
        }

        $model = $this->nullableString($context['owner_type'] ?? null)
            ?: $this->nullableString($context['module'] ?? null);
        $modelId = $this->nullableString($context['owner_id'] ?? null);

        return MediaInUse::query()->updateOrCreate(
            [
                'media_id' => $media->id,
                'model' => $model,
                'model_id' => $modelId,
                'col_name' => $fieldName,
            ],
            [
                'product_website_id' => $media->product_website_id,
                'creator' => auth()->id(),
                'slug' => $this->nullableString($context['module'] ?? null),
                'status' => 1,
            ],
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    private function mediaFromItem(array $item): ?Media
    {
        $id = $item['media_id'] ?? $item['id'] ?? null;

        if ($id && is_numeric($id)) {
            $media = Media::query()->active()->find((int) $id);

            if ($media) {
                return $media;
            }
        }

        $path = (string) ($item['path'] ?? '');

        if ($path !== '') {
            return $this->mediaFromPathOrUrl($path);
        }

        $url = (string) ($item['url'] ?? '');

        return $url !== '' ? $this->mediaFromPathOrUrl($url) : null;
    }

    private function mediaFromPathOrUrl(string $pathOrUrl): ?Media
    {
        $path = $this->normalizePath($pathOrUrl);

        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            $path = $this->pathFromUrl($pathOrUrl);
        }

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

    private function folderFromPath(string $path): ?MediaFolder
    {
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

    /**
     * @return Collection<int, int>
     */
    private function folderAndDescendantIds(MediaFolder $folder): Collection
    {
        $ids = collect([$folder->id]);
        $children = MediaFolder::query()
            ->active()
            ->where('parent_id', $folder->id)
            ->get();

        foreach ($children as $child) {
            $ids = $ids->merge($this->folderAndDescendantIds($child));
        }

        return $ids->unique()->values();
    }

    private function pathFromUrl(string $url): string
    {
        $disk = (string) config('file_manager.storage_disk', 'ftp');
        $baseUrl = rtrim((string) config('filesystems.disks.'.$disk.'.url', ''), '/');

        if ($baseUrl !== '' && str_starts_with($url, $baseUrl.'/')) {
            return $this->normalizePath(rawurldecode(substr($url, strlen($baseUrl) + 1)));
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) ? $this->normalizePath(rawurldecode($path)) : '';
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function applyContextFilters($query, array $context): void
    {
        if ($ownerType = $this->nullableString($context['owner_type'] ?? $context['model'] ?? null)) {
            $query->where('model', $ownerType);
        }

        if ($ownerId = $this->nullableString($context['owner_id'] ?? $context['model_id'] ?? null)) {
            $query->where('model_id', $ownerId);
        }

        if ($field = $this->nullableString($context['field_name'] ?? $context['field'] ?? $context['col_name'] ?? null)) {
            $query->where('col_name', $field);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formatUsage(MediaInUse $usage): array
    {
        return [
            'id' => $usage->id,
            'media_id' => $usage->media_id,
            'module' => $usage->slug,
            'owner_type' => $usage->model,
            'owner_id' => $usage->model_id,
            'field_name' => $usage->col_name,
            'collection' => null,
            'label' => $this->labelFor($usage),
            'updated_at' => $usage->updated_at?->toDateTimeString(),
        ];
    }

    private function labelFor(MediaInUse $usage): string
    {
        return Collection::make([$usage->slug, $usage->model_id, $usage->col_name])
            ->filter()
            ->implode(' / ');
    }
}
