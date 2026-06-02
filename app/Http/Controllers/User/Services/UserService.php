<?php

namespace App\Http\Controllers\User\Services;

use App\Http\Controllers\User\Actions\CreateUserAction;
use App\Http\Controllers\User\Actions\DeleteUserAction;
use App\Http\Controllers\User\Actions\ExportUsersCsvAction;
use App\Http\Controllers\User\Actions\FindUserAction;
use App\Http\Controllers\User\Actions\GetUserDataTableAction;
use App\Http\Controllers\User\Actions\GetUserTypeOptionsAction;
use App\Http\Controllers\User\Actions\UpdateUserAction;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserService
{
    public function __construct(
        private readonly GetUserDataTableAction $getUserDataTableAction,
        private readonly FindUserAction $findUserAction,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly GetUserTypeOptionsAction $getUserTypeOptionsAction,
        private readonly ExportUsersCsvAction $exportUsersCsvAction,
    ) {
    }

    public function getDataTableData(Request $request): array
    {
        return $this->getUserDataTableAction->execute($request);
    }

    public function findUser(User $user): array
    {
        return $this->findUserAction->execute($user);
    }

    public function createUser(array $data): User
    {
        return $this->createUserAction->execute($data);
    }

    public function updateUser(User $user, array $data): User
    {
        return $this->updateUserAction->execute($user, $data);
    }

    public function deleteUser(User $user, ?int $authenticatedUserId): void
    {
        $this->deleteUserAction->execute($user, $authenticatedUserId);
    }

    public function getUserTypeOptions(string $search, int $page): array
    {
        return $this->getUserTypeOptionsAction->execute($search, $page);
    }

    public function exportUsersCsv(array $filters): StreamedResponse
    {
        return $this->exportUsersCsvAction->execute($filters);
    }
}
