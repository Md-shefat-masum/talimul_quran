<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_url',
        'avatar_path',
        'profile_image_path',
        'additional_image_paths',
        'document_urls',
        'document_paths',
        'user_type_id',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'additional_image_paths' => 'array',
            'document_urls' => 'array',
            'document_paths' => 'array',
        ];
    }

    public function profileImageUrl(): ?string
    {
        return $this->publicMediaUrl($this->profile_image_path ?: $this->avatar_path);
    }

    /**
     * @return array<int, string>
     */
    public function additionalImageUrls(): array
    {
        return collect($this->additional_image_paths ?: [])
            ->map(fn (string $path): ?string => $this->publicMediaUrl($path))
            ->filter()
            ->values()
            ->all();
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    private function publicMediaUrl(?string $path): ?string
    {
        $path = trim((string) $path, '/');

        if ($path === '') {
            return null;
        }

        $disk = (string) config('file_manager.storage_disk', 'ftp');
        $baseUrl = rtrim((string) config("filesystems.disks.{$disk}.url", ''), '/');

        return $baseUrl !== '' ? $baseUrl.'/'.$path : null;
    }
}
