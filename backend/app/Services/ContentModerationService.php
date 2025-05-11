<?php

namespace App\Services;

use App\Models\Advertisement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentModerationService
{
    protected $apiKey;
    protected $apiEndpoint;

    public function __construct()
    {
        $this->apiKey = config('services.content_moderation.api_key');
        $this->apiEndpoint = config('services.content_moderation.endpoint');
    }

    public function moderateContent(Advertisement $advertisement): array
    {
        try {
            // Проверка текстового контента
            $textResults = $this->moderateText($advertisement->content);
            
            // Проверка изображения
            $imageResults = $advertisement->image_url ? $this->moderateImage($advertisement->image_url) : [];

            // Проверка URL
            $urlResults = $advertisement->target_url ? $this->moderateUrl($advertisement->target_url) : [];

            return [
                'is_approved' => $textResults['is_approved'] && 
                               (!isset($imageResults['is_approved']) || $imageResults['is_approved']) &&
                               (!isset($urlResults['is_approved']) || $urlResults['is_approved']),
                'violations' => array_merge(
                    $textResults['violations'] ?? [],
                    $imageResults['violations'] ?? [],
                    $urlResults['violations'] ?? []
                ),
                'confidence' => min(
                    $textResults['confidence'] ?? 1,
                    $imageResults['confidence'] ?? 1,
                    $urlResults['confidence'] ?? 1
                )
            ];
        } catch (\Exception $e) {
            Log::error('Content moderation failed: ' . $e->getMessage());
            return [
                'is_approved' => false,
                'violations' => ['error' => 'Failed to moderate content'],
                'confidence' => 0
            ];
        }
    }

    protected function moderateText(string $content): array
    {
        // Здесь будет интеграция с API для проверки текста
        // Например, Google Cloud Content Moderation API или аналогичный сервис
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->apiEndpoint . '/text', [
            'content' => $content
        ]);

        return $response->json();
    }

    protected function moderateImage(string $imageUrl): array
    {
        // Здесь будет интеграция с API для проверки изображений
        // Например, Google Cloud Vision API или аналогичный сервис
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->apiEndpoint . '/image', [
            'image_url' => $imageUrl
        ]);

        return $response->json();
    }

    protected function moderateUrl(string $url): array
    {
        // Проверка URL на безопасность
        // Например, Google Safe Browsing API или аналогичный сервис
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->apiEndpoint . '/url', [
            'url' => $url
        ]);

        return $response->json();
    }
} 