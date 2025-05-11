<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Moderation extends Model
{
    protected $fillable = [
        'type',
        'status',
        'check_results',
        'rejection_reason',
        'moderator_id',
        'moderated_at',
    ];

    protected $casts = [
        'check_results' => 'array',
        'moderated_at' => 'datetime',
    ];

    public function moderatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function approve(int $moderatorId): void
    {
        $this->update([
            'status' => 'approved',
            'moderator_id' => $moderatorId,
            'moderated_at' => now(),
        ]);
    }

    public function reject(int $moderatorId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'moderator_id' => $moderatorId,
            'moderated_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
} 