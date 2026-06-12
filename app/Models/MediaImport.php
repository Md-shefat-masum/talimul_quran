<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaImport extends Model
{
    protected $fillable = [
        'disk',
        'root',
        'recursive',
        'dry_run',
        'limit',
        'status',
        'scanned',
        'created',
        'updated',
        'skipped',
        'failed',
        'items',
        'errors',
        'creator',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'recursive' => 'boolean',
            'dry_run' => 'boolean',
            'items' => 'array',
            'errors' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFileManagerArray(): array
    {
        return [
            'id' => $this->id,
            'disk' => $this->disk,
            'root' => $this->root,
            'recursive' => $this->recursive,
            'dry_run' => $this->dry_run,
            'limit' => $this->limit,
            'status' => $this->status,
            'scanned' => $this->scanned,
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'failed' => $this->failed,
            'errors' => $this->errors ?: [],
            'started_at' => $this->started_at?->toDateTimeString(),
            'finished_at' => $this->finished_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
