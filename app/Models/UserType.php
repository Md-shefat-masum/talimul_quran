<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /**
     * Return only active options for forms and dropdown APIs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
