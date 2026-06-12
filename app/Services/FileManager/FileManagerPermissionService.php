<?php

namespace App\Services\FileManager;

use App\Models\MediaFolder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FileManagerPermissionService
{
    /**
     * @return array<string, bool>
     */
    public function permissions(?Request $request = null, ?MediaFolder $folder = null): array
    {
        $request ??= request();

        return collect(array_keys(config('file_manager.abilities', [])))
            ->mapWithKeys(fn (string $ability): array => [$ability => $this->allows($request, $ability, $folder)])
            ->all();
    }

    public function allows(Request $request, string $ability, ?MediaFolder $folder = null): bool
    {
        $user = $request->user();

        if (! $user) {
            $allowed = (bool) config("file_manager.guest_permissions.{$ability}", config('file_manager.allow_guest', false));

            return $this->applyFolderOverride($folder, $ability, $allowed);
        }

        $allowed = $this->allowsByPermissionPackage($user, $ability)
            ?? $this->allowsByGate($user, $ability)
            ?? (bool) config("file_manager.default_authenticated_permissions.{$ability}", false);

        return $this->applyFolderOverride($folder, $ability, $allowed);
    }

    private function allowsByPermissionPackage(Authenticatable $user, string $ability): ?bool
    {
        $permissionNames = $this->permissionNames($ability);

        if (method_exists($user, 'hasAnyPermission')) {
            try {
                return (bool) $user->hasAnyPermission($permissionNames);
            } catch (\Throwable) {
                return null;
            }
        }

        if (! method_exists($user, 'hasPermissionTo')) {
            return null;
        }

        foreach ($permissionNames as $permissionName) {
            try {
                if ($user->hasPermissionTo($permissionName)) {
                    return true;
                }
            } catch (\Throwable) {
                return null;
            }
        }

        return false;
    }

    private function allowsByGate(Authenticatable $user, string $ability): ?bool
    {
        foreach ($this->permissionNames($ability) as $permissionName) {
            if (Gate::has($permissionName)) {
                return Gate::forUser($user)->allows($permissionName);
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function permissionNames(string $ability): array
    {
        $permissions = config("file_manager.abilities.{$ability}", []);

        return is_array($permissions) ? $permissions : [];
    }

    private function applyFolderOverride(?MediaFolder $folder, string $ability, bool $allowed): bool
    {
        foreach ($this->folderOverrideTrail($folder) as $item) {
            $overrides = $item->permission_overrides;

            if (! is_array($overrides) || ! array_key_exists($ability, $overrides)) {
                continue;
            }

            $allowed = (bool) $overrides[$ability];
        }

        return $allowed;
    }

    /**
     * @return array<int, MediaFolder>
     */
    private function folderOverrideTrail(?MediaFolder $folder): array
    {
        $items = [];
        $current = $folder;
        $seen = [];

        while ($current && ! in_array($current->id, $seen, true)) {
            $items[] = $current;
            $seen[] = $current->id;
            $current = $current->parent_id ? MediaFolder::query()->find($current->parent_id) : null;
        }

        return array_reverse($items);
    }
}
