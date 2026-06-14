<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BuildUserListQueryAction
{
    /**
     * Build the shared user list query used by DataTable and CSV export.
     * Only filtering lives here. Pagination and formatting stay in their own Actions.
     */
    public function execute(array $filters = []): Builder
    {
        $query = User::query()->with(['userType:id,name', 'roles:id,name']);

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('userType', function (Builder $typeQuery) use ($search): void {
                        $typeQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null) {
            $query->where('status', (bool) $filters['status']);
        }

        if (! empty($filters['user_type_id'])) {
            $query->where('user_type_id', (int) $filters['user_type_id']);
        }

        return $query;
    }
}
