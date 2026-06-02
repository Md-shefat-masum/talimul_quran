<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\UserType;

class GetUserTypeOptionsAction
{
    private const PAGE_SIZE = 10;

    public function execute(string $search = '', int $page = 1): array
    {
        $page = max($page, 1);
        $search = trim($search);

        $query = UserType::query()
            ->active()
            ->select(['id', 'name'])
            ->orderBy('name');

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $paginator = $query->paginate(self::PAGE_SIZE, ['*'], 'page', $page);

        return [
            'results' => collect($paginator->items())->map(fn (UserType $type): array => [
                'id' => $type->id,
                'text' => $type->name,
            ])->values()->all(),
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ];
    }
}
