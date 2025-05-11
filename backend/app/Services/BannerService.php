<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class BannerService
{
    protected $imageOptimizationService;

    public function __construct(ImageOptimizationService $imageOptimizationService)
    {
        $this->imageOptimizationService = $imageOptimizationService;
    }

    public function createBanner(Advertisement $advertisement, array $data): Banner
    {
        try {
            // Оптимизируем изображение баннера
            $optimizedImagePath = $this->imageOptimizationService->optimize(
                $data['image_path'],
                [
                    'width' => $data['width'] ?? 728,
                    'height' => $data['height'] ?? 90,
                    'quality' => 85
                ]
            );

            // Создаем баннер
            $banner = Banner::create([
                'advertisement_id' => $advertisement->id,
                'image_path' => $optimizedImagePath,
                'width' => $data['width'] ?? 728,
                'height' => $data['height'] ?? 90,
                'alt_text' => $data['alt_text'] ?? '',
                'target_url' => $data['target_url'],
                'position' => $data['position'] ?? 'top',
                'is_active' => true
            ]);

            return $banner;
        } catch (\Exception $e) {
            Log::error('Failed to create banner: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateBanner(Banner $banner, array $data): Banner
    {
        try {
            if (isset($data['image_path'])) {
                // Оптимизируем новое изображение
                $optimizedImagePath = $this->imageOptimizationService->optimize(
                    $data['image_path'],
                    [
                        'width' => $data['width'] ?? $banner->width,
                        'height' => $data['height'] ?? $banner->height,
                        'quality' => 85
                    ]
                );

                // Удаляем старое изображение
                if ($banner->image_path) {
                    Storage::delete($banner->image_path);
                }

                $data['image_path'] = $optimizedImagePath;
            }

            $banner->update($data);
            return $banner;
        } catch (\Exception $e) {
            Log::error('Failed to update banner: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteBanner(Banner $banner): bool
    {
        try {
            // Удаляем изображение
            if ($banner->image_path) {
                Storage::delete($banner->image_path);
            }

            return $banner->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete banner: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getActiveBanners(string $position = null): array
    {
        $query = Banner::where('is_active', true)
            ->whereHas('advertisement', function ($query) {
                $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            });

        if ($position) {
            $query->where('position', $position);
        }

        return $query->get()->toArray();
    }

    public function trackImpression(Banner $banner): void
    {
        try {
            $banner->increment('impressions');
            $banner->advertisement->increment('impressions');
        } catch (\Exception $e) {
            Log::error('Failed to track banner impression: ' . $e->getMessage());
        }
    }

    public function trackClick(Banner $banner): void
    {
        try {
            $banner->increment('clicks');
            $banner->advertisement->increment('clicks');
        } catch (\Exception $e) {
            Log::error('Failed to track banner click: ' . $e->getMessage());
        }
    }

    public function getBannerStatistics(Banner $banner): array
    {
        return [
            'impressions' => $banner->impressions,
            'clicks' => $banner->clicks,
            'ctr' => $banner->impressions > 0 ? ($banner->clicks / $banner->impressions) * 100 : 0,
            'image_info' => $this->imageOptimizationService->getImageInfo($banner->image_path)
        ];
    }
} 