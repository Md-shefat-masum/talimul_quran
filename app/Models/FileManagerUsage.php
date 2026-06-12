<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FileManagerUsage extends Model
{
    protected $fillable = [
        'usage_hash',
        'disk',
        'path',
        'url',
        'module',
        'owner_type',
        'owner_id',
        'field_name',
        'collection',
        'label',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function scopeForDisk(Builder $query, string $disk): Builder
    {
        return $query->where('disk', $disk);
    }

    public function scopeForPath(Builder $query, string $path): Builder
    {
        return $query->where('path', trim($path, '/'));
    }
}
