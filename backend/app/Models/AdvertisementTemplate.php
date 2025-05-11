<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'type',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }
} 