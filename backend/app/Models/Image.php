<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Image",
 *     title="Image",
 *     description="Image model",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="advertisement_id", type="integer", format="int64"),
 *     @OA\Property(property="path", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="advertisement",
 *         ref="#/components/schemas/Advertisement"
 *     )
 * )
 */
class Image extends Model
{
    protected $fillable = [
        'advertisement_id',
        'path',
    ];

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }
} 