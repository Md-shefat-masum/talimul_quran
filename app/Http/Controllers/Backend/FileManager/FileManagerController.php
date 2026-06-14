<?php

namespace App\Http\Controllers\Backend\FileManager;

use App\Exceptions\FileManager\DuplicateFileException;
use App\Exceptions\FileManager\FileInUseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileManager\UploadPhotoRequest;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Services\FileManager\FileManagerPermissionService;
use App\Services\FileManager\FileManagerService;
use App\Services\FileManager\FileManagerUsageService;
use App\Services\FileManager\MediaImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileManagerController extends Controller
{
    public function __construct(
        private readonly FileManagerService $fileManagerService,
        private readonly FileManagerUsageService $usageService,
        private readonly FileManagerPermissionService $permissionService,
        private readonly MediaImportService $mediaImportService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:120'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);
        $folder = $this->folderFromIdentifier((string) ($validated['folder_id'] ?? $validated['path'] ?? ''));

        if ($response = $this->denyUnlessAllowed($request, 'read', $folder)) {
            return $response;
        }

        try {
            $list = $this->fileManagerService->list(
                (string) ($validated['folder_id'] ?? $validated['path'] ?? ''),
                (int) ($validated['page'] ?? 1),
                (int) ($validated['per_page'] ?? 60),
                (string) ($validated['q'] ?? ''),
            );
            $listFolder = $this->folderFromIdentifier((string) ($list['folder_id'] ?? ''));

            return response()->json([
                'success' => true,
                'data' => array_merge(
                    $list,
                    ['permissions' => $this->permissionService->permissions($request, $listFolder)],
                ),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Could not read the media library.',
                'data' => [
                    'path' => (string) $request->query('path', ''),
                    'breadcrumbs' => [['label' => 'Home', 'path' => '']],
                    'items' => [],
                    'pagination' => [
                        'page' => (int) $request->query('page', 1),
                        'per_page' => (int) $request->query('per_page', 60),
                        'total' => 0,
                        'shown' => 0,
                        'has_more' => false,
                        'next_page' => null,
                        'query' => (string) $request->query('q', ''),
                    ],
                    'error' => 'Could not read the media library.',
                    'debug' => config('app.debug') ? $exception->getMessage() : null,
                ],
            ]);
        }
    }

    public function tree(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $folder = $this->folderFromIdentifier((string) ($validated['folder_id'] ?? $validated['path'] ?? ''));

        if ($response = $this->denyUnlessAllowed($request, 'read', $folder)) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'data' => $this->fileManagerService->tree((string) ($validated['folder_id'] ?? $validated['path'] ?? '')),
        ]);
    }

    public function uploadPhoto(UploadPhotoRequest $request): JsonResponse
    {
        $folder = $this->folderFromIdentifier((string) ($request->input('folder_id') ?: $request->input('path', '')), true);

        if ($response = $this->denyUnlessAllowed($request, 'upload', $folder)) {
            return $response;
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully.',
                'data' => $this->fileManagerService->uploadPhoto(
                    $request->file('photo'),
                    (string) ($request->input('folder_id') ?: $request->input('path', '')),
                    $request->input('name'),
                    (string) $request->input('conflict_strategy', 'rename'),
                ),
            ], 201);
        } catch (DuplicateFileException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'conflict' => $exception->conflict(),
            ], Response::HTTP_CONFLICT);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not upload the selected photo.');
        }
    }

    public function folder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:120'],
        ]);
        $folder = $this->folderFromIdentifier((string) ($validated['folder_id'] ?? $validated['path'] ?? ''), true);

        if ($response = $this->denyUnlessAllowed($request, 'create_folder', $folder)) {
            return $response;
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully.',
                'data' => $this->fileManagerService->createFolder(
                    (string) ($validated['folder_id'] ?? $validated['path'] ?? ''),
                    (string) $validated['name'],
                ),
            ], 201);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not create the folder.');
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'id' => ['nullable', 'integer', 'min:1'],
            'media_id' => ['nullable', 'integer', 'min:1'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['required', 'in:file,directory'],
            'force' => ['nullable', 'boolean'],
        ]);
        $ability = (bool) ($validated['force'] ?? false) ? 'force_delete' : 'delete';

        if ($response = $this->denyUnlessAllowed($request, $ability, $this->folderForItem($validated))) {
            return $response;
        }

        try {
            $this->fileManagerService->delete(
                $this->itemIdentifier($validated),
                (string) $validated['type'],
                (bool) ($validated['force'] ?? false),
            );

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.',
            ]);
        } catch (FileInUseException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'This item is used and cannot be deleted without force.',
                'usage' => $exception->usageSummary(),
            ], Response::HTTP_CONFLICT);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not delete the selected item.');
        }
    }

    public function usage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'id' => ['nullable', 'integer', 'min:1'],
            'media_id' => ['nullable', 'integer', 'min:1'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', 'in:file,directory'],
        ]);

        if ($response = $this->denyUnlessAllowed($request, 'read', $this->folderForItem($validated))) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'data' => $this->usageService->summary(
                $this->itemIdentifier($validated),
                (string) ($validated['type'] ?? 'file'),
            ),
        ]);
    }

    public function trackUsage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.path' => ['nullable', 'string', 'max:500'],
            'items.*.url' => ['nullable', 'string', 'max:1000'],
            'items.*.id' => ['nullable', 'integer', 'min:1'],
            'items.*.media_id' => ['nullable', 'integer', 'min:1'],
            'module' => ['required', 'string', 'max:100'],
            'owner_type' => ['nullable', 'string', 'max:160'],
            'owner_id' => ['nullable', 'string', 'max:80'],
            'field_name' => ['required_without:field', 'nullable', 'string', 'max:120'],
            'field' => ['required_without:field_name', 'nullable', 'string', 'max:120'],
            'collection' => ['nullable', 'string', 'max:120'],
            'label' => ['nullable', 'string', 'max:160'],
            'metadata' => ['nullable', 'array'],
        ]);

        if ($response = $this->denyUnlessAllowed($request, 'track_usage', $this->folderForUsageItems($validated['items']))) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'message' => 'File usage tracked successfully.',
            'data' => $this->usageService->track(
                $validated['items'],
                $validated,
            ),
        ], 201);
    }

    public function forgetUsage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'id' => ['nullable', 'integer', 'min:1'],
            'media_id' => ['nullable', 'integer', 'min:1'],
            'module' => ['nullable', 'string', 'max:100'],
            'owner_type' => ['nullable', 'string', 'max:160'],
            'owner_id' => ['nullable', 'string', 'max:80'],
            'field_name' => ['nullable', 'string', 'max:120'],
            'collection' => ['nullable', 'string', 'max:120'],
        ]);

        if ($response = $this->denyUnlessAllowed($request, 'forget_usage', $this->folderForItem($validated))) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'message' => 'File usage removed successfully.',
            'data' => [
                'deleted' => $this->usageService->forget(
                    (string) ($validated['media_id'] ?? $validated['id'] ?? $validated['path'] ?? ''),
                    $validated,
                ),
            ],
        ]);
    }

    public function rename(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'id' => ['nullable', 'integer', 'min:1'],
            'media_id' => ['nullable', 'integer', 'min:1'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['required', 'in:file,directory'],
            'name' => ['required', 'string', 'max:160'],
        ]);

        if ($response = $this->denyUnlessAllowed($request, 'rename', $this->folderForItem($validated))) {
            return $response;
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Item renamed successfully.',
                'data' => $this->fileManagerService->rename(
                    $this->itemIdentifier($validated),
                    (string) $validated['type'],
                    (string) $validated['name'],
                ),
            ]);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not rename the selected item.');
        }
    }

    public function move(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'id' => ['nullable', 'integer', 'min:1'],
            'media_id' => ['nullable', 'integer', 'min:1'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['required', 'in:file,directory'],
            'destination' => ['nullable', 'string', 'max:500'],
            'destination_folder_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $sourceFolder = $this->folderForItem($validated);
        $destinationFolder = $this->folderFromIdentifier((string) ($validated['destination_folder_id'] ?? $validated['destination'] ?? ''), true);

        if ($response = $this->denyUnlessAllowed($request, 'move', $sourceFolder)) {
            return $response;
        }

        if ($destinationFolder && ($response = $this->denyUnlessAllowed($request, 'move', $destinationFolder))) {
            return $response;
        }

        try {
            return response()->json([
                'success' => true,
                'message' => 'Item moved successfully.',
                'data' => $this->fileManagerService->move(
                    $this->itemIdentifier($validated),
                    (string) $validated['type'],
                    (string) ($validated['destination_folder_id'] ?? $validated['destination'] ?? ''),
                ),
            ]);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not move the selected item.');
        }
    }

    public function preview(Request $request): Response
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        if (! $this->permissionService->allows($request, 'read', $this->folderForMediaIdentifier((string) $validated['path']))) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to use the file manager.');
        }

        $file = $this->fileManagerService->readForPreview((string) $request->query('path'));

        return response($file['contents'], 200, [
            'Content-Type' => $file['mime'],
            'Content-Disposition' => 'inline; filename="'.$file['name'].'"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function thumbnail(Request $request): Response
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'width' => ['nullable', 'integer', 'min:80', 'max:1200'],
            'height' => ['nullable', 'integer', 'min:80', 'max:1200'],
        ]);

        if (! $this->permissionService->allows($request, 'read', $this->folderForMediaIdentifier((string) $validated['path']))) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to use the file manager.');
        }

        $file = $this->fileManagerService->readThumbnail(
            (string) $validated['path'],
            (int) ($validated['width'] ?? 360),
            (int) ($validated['height'] ?? 270),
        );

        return response($file['contents'], 200, [
            'Content-Type' => $file['mime'],
            'Content-Disposition' => 'inline; filename="'.$file['name'].'"',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function thumbnailCache(Request $request): JsonResponse
    {
        if ($response = $this->denyUnlessAllowed($request, 'maintenance')) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'data' => $this->fileManagerService->thumbnailCacheStats(),
        ]);
    }

    public function clearThumbnailCache(Request $request): JsonResponse
    {
        if ($response = $this->denyUnlessAllowed($request, 'maintenance')) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'message' => 'Thumbnail cache cleared successfully.',
            'data' => $this->fileManagerService->clearThumbnailCache(),
        ]);
    }

    public function importMedia(Request $request): JsonResponse
    {
        if ($response = $this->denyUnlessAllowed($request, 'maintenance')) {
            return $response;
        }

        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'recursive' => ['nullable', 'boolean'],
            'dry_run' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Media import completed.',
            'data' => $this->mediaImportService->import(
                (string) ($validated['path'] ?? 'uploads'),
                (bool) ($validated['recursive'] ?? true),
                (bool) ($validated['dry_run'] ?? false),
                isset($validated['limit']) ? (int) $validated['limit'] : null,
            ),
        ]);
    }

    public function importHistory(Request $request): JsonResponse
    {
        if ($response = $this->denyUnlessAllowed($request, 'maintenance')) {
            return $response;
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->mediaImportService->recentImports((int) ($validated['limit'] ?? 5)),
        ]);
    }

    public function updateFolderPermissions(Request $request): JsonResponse
    {
        if ($response = $this->denyUnlessAllowed($request, 'maintenance')) {
            return $response;
        }

        $validated = $request->validate([
            'folder_id' => ['required_without:path', 'nullable', 'integer', 'min:1'],
            'path' => ['required_without:folder_id', 'nullable', 'string', 'max:500'],
            'overrides' => ['nullable', 'array'],
            'overrides.*' => ['nullable', 'boolean'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder permission overrides saved.',
            'data' => $this->fileManagerService->updateFolderPermissionOverrides(
                (string) ($validated['folder_id'] ?? $validated['path'] ?? ''),
                $validated['overrides'] ?? [],
            ),
        ]);
    }

    private function errorResponse(\Throwable $exception, string $fallbackMessage): JsonResponse
    {
        report($exception);

        return response()->json([
            'success' => false,
            'message' => $fallbackMessage,
            'error' => config('app.debug') ? $exception->getMessage() : null,
        ], 422);
    }

    private function denyUnlessAllowed(Request $request, string $ability, ?MediaFolder $folder = null): ?JsonResponse
    {
        if ($this->permissionService->allows($request, $ability, $folder)) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to use this file manager action.',
            'ability' => $ability,
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function itemIdentifier(array $validated): string
    {
        if (($validated['type'] ?? 'file') === 'directory') {
            return (string) ($validated['folder_id'] ?? $validated['id'] ?? $validated['path'] ?? '');
        }

        return (string) ($validated['media_id'] ?? $validated['id'] ?? $validated['path'] ?? '');
    }

    /**
     * @param array<string, mixed> $item
     */
    private function folderForItem(array $item): ?MediaFolder
    {
        if (($item['type'] ?? 'file') === 'directory') {
            return $this->folderFromIdentifier((string) ($item['folder_id'] ?? $item['id'] ?? $item['path'] ?? ''));
        }

        return $this->folderForMediaIdentifier((string) ($item['media_id'] ?? $item['id'] ?? $item['path'] ?? ''));
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function folderForUsageItems(array $items): ?MediaFolder
    {
        $first = $items[0] ?? [];

        return $this->folderForMediaIdentifier((string) ($first['media_id'] ?? $first['id'] ?? $first['path'] ?? ''));
    }

    private function folderForMediaIdentifier(string $identifier): ?MediaFolder
    {
        $media = $this->mediaFromIdentifier($identifier);

        return $media?->folder;
    }

    private function mediaFromIdentifier(string $identifier): ?Media
    {
        $identifier = $this->normalizePath($identifier);

        if ($identifier === '') {
            return null;
        }

        if (ctype_digit($identifier)) {
            return Media::query()->active()->with('folder')->find((int) $identifier);
        }

        return Media::query()
            ->active()
            ->with('folder')
            ->where('path', $identifier)
            ->first();
    }

    private function folderFromIdentifier(string $identifier, bool $nearest = false): ?MediaFolder
    {
        $identifier = $this->normalizePath($identifier);

        if ($identifier === '') {
            return null;
        }

        if (ctype_digit($identifier)) {
            return MediaFolder::query()->active()->find((int) $identifier);
        }

        $parentId = 0;
        $folder = null;
        $lastFolder = null;

        foreach (explode('/', $identifier) as $segment) {
            $folder = MediaFolder::query()
                ->active()
                ->where('parent_id', $parentId)
                ->where('saved_name_into_storage', $segment)
                ->first();

            if (! $folder) {
                return $nearest ? $lastFolder : null;
            }

            $lastFolder = $folder;
            $parentId = $folder->id;
        }

        return $folder;
    }

    private function normalizePath(string $path): string
    {
        return collect(explode('/', str_replace('\\', '/', $path)))
            ->filter(fn (string $segment): bool => $segment !== '' && $segment !== '.' && $segment !== '..')
            ->values()
            ->implode('/');
    }
}
