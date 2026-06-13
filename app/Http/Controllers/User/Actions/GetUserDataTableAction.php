<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;
use Illuminate\Http\Request;

class GetUserDataTableAction
{
    public function __construct(
        private readonly BuildUserListQueryAction $buildUserListQueryAction,
    ) {
    }

    /**
     * Return a DataTables server-side payload.
     */
    public function execute(Request $request): array
    {
        $filters = [
            'search' => $request->input('search.value', ''),
            'status' => $request->input('filters.status'),
            'user_type_id' => $request->input('filters.user_type_id'),
        ];

        $recordsTotal = User::query()->count();
        $query = $this->buildUserListQueryAction->execute($filters);
        $recordsFiltered = (clone $query)->count();

        $sortMap = [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'status' => 'status',
            'created_at' => 'created_at',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $requestedColumn = (string) $request->input("columns.{$orderColumnIndex}.data", 'created_at');
        $sortColumn = $sortMap[$requestedColumn] ?? 'created_at';
        $sortDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $start = max((int) $request->input('start', 0), 0);
        $length = min(max((int) $request->input('length', 10), 1), 100);

        $users = $query
            ->orderBy($sortColumn, $sortDirection)
            ->skip($start)
            ->take($length)
            ->get();

        $rows = $users->values()->map(function (User $user, int $index) use ($start): array {
            return [
                'serial' => $start + $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?: '—',
                'avatar_url' => $user->profileImageUrl() ?: $user->avatar_url,
                'document_count' => count($user->additional_image_paths ?: $user->document_urls ?: []),
                'user_type' => $user->userType?->name ?: 'Not assigned',
                'status' => $user->status ? 1 : 0,
                'status_label' => $user->status ? 'Active' : 'Inactive',
                'created_at' => $user->created_at?->format('d M Y, h:i A'),
            ];
        })->all();

        return [
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
            'summary' => [
                'total' => $recordsTotal,
                'active' => User::query()->where('status', true)->count(),
                'inactive' => User::query()->where('status', false)->count(),
                'filtered' => $recordsFiltered,
            ],
        ];
    }
}
