<?php

namespace App\Services;

use App\Models\Moderation;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    protected array $forbiddenWords = [
        'спам', 'реклама', 'обман', 'мошенничество',
        // Добавьте другие запрещенные слова
    ];

    public function moderate($model, string $type): Moderation
    {
        $checkResults = match ($type) {
            'text' => $this->moderateText($model),
            'image' => $this->moderateImage($model),
            'user' => $this->moderateUser($model),
            default => throw new \InvalidArgumentException('Неподдерживаемый тип модерации'),
        };

        $status = $this->determineStatus($type, $checkResults);

        return Moderation::create([
            'moderatable_type' => get_class($model),
            'moderatable_id' => $model->id,
            'type' => $type,
            'status' => $status,
            'check_results' => $checkResults,
        ]);
    }

    protected function moderateText($model): array
    {
        $text = $model->description ?? $model->content ?? '';
        
        return [
            'forbidden_words' => $this->checkForbiddenWords($text),
            'sentiment' => $this->analyzeSentiment($text),
            'spam' => $this->checkSpam($text),
        ];
    }

    protected function moderateImage($model): array
    {
        // TODO: Интеграция с сервисом анализа изображений
        return [
            'forbidden_content' => false,
            'quality_check' => true,
            'duplicate_check' => false,
        ];
    }

    protected function moderateUser($model): array
    {
        return [
            'rating' => $this->checkUserRating($model),
            'activity' => $this->analyzeUserActivity($model),
            'violations' => $this->checkUserViolations($model),
        ];
    }

    protected function checkForbiddenWords(string $text): array
    {
        $found = [];
        foreach ($this->forbiddenWords as $word) {
            if (stripos($text, $word) !== false) {
                $found[] = $word;
            }
        }
        return $found;
    }

    protected function analyzeSentiment(string $text): array
    {
        // TODO: Интеграция с сервисом анализа тональности
        return [
            'score' => 0,
            'is_negative' => false,
        ];
    }

    protected function checkSpam(string $text): array
    {
        // TODO: Интеграция с антиспам системой
        return [
            'is_spam' => false,
            'confidence' => 0,
        ];
    }

    protected function checkUserRating($user): array
    {
        return [
            'current_rating' => $user->rating ?? 0,
            'is_satisfactory' => ($user->rating ?? 0) >= 4.0,
        ];
    }

    protected function analyzeUserActivity($user): array
    {
        return [
            'active_listings' => $user->advertisements()->active()->count(),
            'total_listings' => $user->advertisements()->count(),
            'last_activity' => $user->last_activity_at ?? null,
        ];
    }

    protected function checkUserViolations($user): array
    {
        return [
            'total_violations' => $user->violations()->count(),
            'active_violations' => $user->violations()->active()->count(),
            'is_blocked' => $user->is_blocked ?? false,
        ];
    }

    protected function determineStatus(string $type, array $checkResults): string
    {
        return match ($type) {
            'text' => $this->determineTextStatus($checkResults),
            'image' => $this->determineImageStatus($checkResults),
            'user' => $this->determineUserStatus($checkResults),
            default => 'pending',
        };
    }

    protected function determineTextStatus(array $checkResults): string
    {
        if (!empty($checkResults['forbidden_words'])) {
            return 'rejected';
        }

        if ($checkResults['spam']['is_spam']) {
            return 'rejected';
        }

        if ($checkResults['sentiment']['is_negative']) {
            return 'pending';
        }

        return 'approved';
    }

    protected function determineImageStatus(array $checkResults): string
    {
        if ($checkResults['forbidden_content']) {
            return 'rejected';
        }

        if (!$checkResults['quality_check']) {
            return 'pending';
        }

        return 'approved';
    }

    protected function determineUserStatus(array $checkResults): string
    {
        if ($checkResults['violations']['is_blocked']) {
            return 'rejected';
        }

        if ($checkResults['violations']['active_violations'] > 0) {
            return 'pending';
        }

        if (!$checkResults['rating']['is_satisfactory']) {
            return 'pending';
        }

        return 'approved';
    }
} 