<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'product_website_id',
        'disk',
        'path',
        'filename',
        'extension',
        'mime_type',
        'size',
        'folders',
        'media_folder_id',
        'creator',
        'slug',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'folders' => 'array',
            'status' => 'integer',
        ];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MediaInUse::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }
}
