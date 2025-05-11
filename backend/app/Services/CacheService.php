<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 час

    public static function remember(string $key, $data, int $ttl = self::DEFAULT_TTL)
    {
        return Cache::remember($key, $ttl, function () use ($data) {
            return is_callable($data) ? $data() : $data;
        });
    }

    public static function forget(string $key)
    {
        Cache::forget($key);
    }

    public static function tags(array $tags, string $key, $data, int $ttl = self::DEFAULT_TTL)
    {
        return Cache::tags($tags)->remember($key, $ttl, function () use ($data) {
            return is_callable($data) ? $data() : $data;
        });
    }

    public static function forgetTags(array $tags)
    {
        Cache::tags($tags)->flush();
    }

    public static function getModelCacheKey(Model $model, string $suffix = ''): string
    {
        return sprintf(
            '%s:%s%s',
            class_basename($model),
            $model->getKey(),
            $suffix ? ":{$suffix}" : ''
        );
    }
} 