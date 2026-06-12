<?php

namespace App\Http\Controllers\Backend\FileManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileManager\UploadPhotoRequest;
use App\Services\FileManager\FileManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FileManagerController extends Controller
{
    public function __construct(
        private readonly FileManagerService $fileManagerService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->fileManagerService->list((string) $request->query('path', '')),
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Could not read the FTP disk.',
                'data' => [
                    'path' => (string) $request->query('path', ''),
                    'breadcrumbs' => [['label' => 'Home', 'path' => '']],
                    'items' => [],
                    'error' => 'Could not read the FTP disk.',
                    'debug' => config('app.debug') ? $exception->getMessage() : null,
                ],
            ]);
        }
    }

    public function uploadPhoto(UploadPhotoRequest $request): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully.',
                'data' => $this->fileManagerService->uploadPhoto(
                    $request->file('photo'),
                    (string) $request->input('path', ''),
                    $request->input('name'),
                ),
            ], 201);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not upload the selected photo.');
        }
    }

    public function folder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'path' => ['nullable', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:120'],
        ]);

        try {
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully.',
                'data' => $this->fileManagerService->createFolder(
                    (string) ($validated['path'] ?? ''),
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
            'path' => ['required', 'string', 'max:500'],
            'type' => ['required', 'in:file,directory'],
        ]);

        try {
            $this->fileManagerService->delete((string) $validated['path'], (string) $validated['type']);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.',
            ]);
        } catch (\Throwable $exception) {
            return $this->errorResponse($exception, 'Could not delete the selected item.');
        }
    }

    public function preview(Request $request): Response
    {
        $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        $file = $this->fileManagerService->readForPreview((string) $request->query('path'));

        return response($file['contents'], 200, [
            'Content-Type' => $file['mime'],
            'Content-Disposition' => 'inline; filename="'.$file['name'].'"',
            'Cache-Control' => 'private, max-age=300',
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
}
