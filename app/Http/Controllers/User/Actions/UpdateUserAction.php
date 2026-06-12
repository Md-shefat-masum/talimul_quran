<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;
use App\Services\FileManager\FileManagerUsageService;

class UpdateUserAction
{
    public function __construct(
        private readonly FileManagerUsageService $usageService,
    ) {
    }

    public function execute(User $user, array $data): User
    {
        $oldAvatarPath = $user->avatar_path;
        $oldDocumentPaths = $user->document_paths ?: [];
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
            'document_urls' => $this->jsonValues($data['document_urls'] ?? null),
            'document_paths' => $this->jsonValues($data['document_paths'] ?? null),
            'user_type_id' => $data['user_type_id'],
            'status' => $data['status'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);
        $user = $user->refresh();

        if ($oldAvatarPath && $oldAvatarPath !== $user->avatar_path) {
            $this->usageService->forget($oldAvatarPath, [
                'module' => 'user-management',
                'owner_type' => User::class,
                'owner_id' => (string) $user->id,
                'field_name' => 'avatar_url',
            ]);
        }

        foreach (array_diff($oldDocumentPaths, $user->document_paths ?: []) as $removedPath) {
            $this->usageService->forget($removedPath, [
                'module' => 'user-management',
                'owner_type' => User::class,
                'owner_id' => (string) $user->id,
                'field_name' => 'document_urls',
            ]);
        }

        $this->trackAvatarUsage($user);
        $this->trackDocumentUsage($user);

        return $user;
    }

    private function trackAvatarUsage(User $user): void
    {
        if (! $user->avatar_path && ! $user->avatar_url) {
            return;
        }

        $this->usageService->track([
            [
                'path' => $user->avatar_path,
                'url' => $user->avatar_url,
            ],
        ], [
            'module' => 'user-management',
            'owner_type' => User::class,
            'owner_id' => (string) $user->id,
            'field_name' => 'avatar_url',
            'label' => $user->name.' avatar',
        ]);
    }

    private function trackDocumentUsage(User $user): void
    {
        $paths = $user->document_paths ?: [];
        $urls = $user->document_urls ?: [];
        $items = [];

        foreach ($paths as $index => $path) {
            $items[] = [
                'path' => $path,
                'url' => $urls[$index] ?? null,
            ];
        }

        if ($items === []) {
            return;
        }

        $this->usageService->track($items, [
            'module' => 'user-management',
            'owner_type' => User::class,
            'owner_id' => (string) $user->id,
            'field_name' => 'document_urls',
            'label' => $user->name.' documents',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function jsonValues(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? array_values(array_filter($decoded)) : [];
    }
}
