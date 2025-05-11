<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'advertisement_id',
        'image_path',
        'width',
        'height',
        'alt_text',
        'target_url',
        'position',
        'is_active',
        'impressions',
        'clicks',
        'device_settings',
        'schedule_settings',
        'daily_impression_limit',
        'browser_settings',
        'is_adaptive',
        'adaptive_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'device_settings' => 'array',
        'schedule_settings' => 'array',
        'browser_settings' => 'array',
        'is_adaptive' => 'boolean',
        'adaptive_settings' => 'array'
    ];

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function getCtrAttribute(): float
    {
        return $this->impressions > 0 ? ($this->clicks / $this->impressions) * 100 : 0;
    }

    public function canBeShown(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Проверка лимита показов
        if ($this->daily_impression_limit && $this->impressions >= $this->daily_impression_limit) {
            return false;
        }

        // Проверка расписания
        if ($this->schedule_settings) {
            $now = now();
            $dayOfWeek = strtolower($now->format('l'));
            $currentTime = $now->format('H:i');

            if (!isset($this->schedule_settings[$dayOfWeek]) || 
                !$this->isTimeInRange($currentTime, $this->schedule_settings[$dayOfWeek])) {
                return false;
            }
        }

        return true;
    }

    protected function isTimeInRange(string $time, array $range): bool
    {
        return $time >= $range['start'] && $time <= $range['end'];
    }

    public function getDeviceSpecificImage(): string
    {
        if (!$this->is_adaptive || !$this->adaptive_settings) {
            return $this->image_url;
        }

        // Здесь будет логика выбора изображения в зависимости от устройства
        return $this->image_url;
    }

    public function isCompatibleWithBrowser(string $userAgent): bool
    {
        if (!$this->browser_settings) {
            return true;
        }

        // Здесь будет логика проверки совместимости с браузером
        return true;
    }
} 