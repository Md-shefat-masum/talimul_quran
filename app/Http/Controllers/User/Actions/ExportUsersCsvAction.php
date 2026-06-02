<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportUsersCsvAction
{
    public function __construct(
        private readonly BuildUserListQueryAction $buildUserListQueryAction,
    ) {
    }

    public function execute(array $filters = []): StreamedResponse
    {
        $fileName = 'users-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($filters): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'User Type', 'Status', 'Created At']);

            $this->buildUserListQueryAction
                ->execute($filters)
                ->orderBy('id')
                ->chunkById(200, function ($users) use ($handle): void {
                    /** @var User $user */
                    foreach ($users as $user) {
                        fputcsv($handle, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $user->phone,
                            $user->userType?->name,
                            $user->status ? 'Active' : 'Inactive',
                            $user->created_at?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
