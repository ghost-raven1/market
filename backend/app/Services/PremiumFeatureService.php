<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\PremiumFeature;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PremiumFeatureService
{
    protected array $prices = [
        'boost' => 199,
        'highlight' => 299,
        'top_placement' => 499,
        'vip' => 999,
    ];

    protected array $durations = [
        'boost' => 7, // дней
        'highlight' => 30,
        'top_placement' => 7,
        'vip' => 30,
    ];

    public function activateFeature(Advertisement $advertisement, string $type, array $settings = []): PremiumFeature
    {
        try {
            DB::beginTransaction();

            // Создаем транзакцию для оплаты
            $transaction = Transaction::create([
                'user_id' => $advertisement->user_id,
                'type' => 'premium_feature',
                'amount' => $this->prices[$type],
                'status' => 'pending',
                'metadata' => [
                    'feature_type' => $type,
                    'advertisement_id' => $advertisement->id,
                ],
            ]);

            // Обрабатываем платеж
            $paymentService = app(PaymentService::class);
            $success = $paymentService->processPayment($transaction);

            if (!$success) {
                throw new \Exception('Payment processing failed');
            }

            // Создаем премиум-функцию
            $feature = PremiumFeature::create([
                'advertisement_id' => $advertisement->id,
                'type' => $type,
                'starts_at' => now(),
                'ends_at' => now()->addDays($this->durations[$type]),
                'settings' => $settings,
            ]);

            // Применяем эффекты премиум-функции
            $this->applyFeatureEffects($advertisement, $feature);

            DB::commit();
            return $feature;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to activate premium feature: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function applyFeatureEffects(Advertisement $advertisement, PremiumFeature $feature): void
    {
        match ($feature->type) {
            'boost' => $this->applyBoost($advertisement),
            'highlight' => $this->applyHighlight($advertisement, $feature->settings),
            'top_placement' => $this->applyTopPlacement($advertisement),
            'vip' => $this->applyVipStatus($advertisement),
            default => throw new \InvalidArgumentException('Unknown feature type'),
        };
    }

    protected function applyBoost(Advertisement $advertisement): void
    {
        // Обновляем дату публикации для поднятия в списке
        $advertisement->update(['published_at' => now()]);
    }

    protected function applyHighlight(Advertisement $advertisement, array $settings): void
    {
        // Применяем цветовое выделение
        $advertisement->update([
            'highlight_color' => $settings['color'] ?? '#FFD700',
        ]);
    }

    protected function applyTopPlacement(Advertisement $advertisement): void
    {
        // Устанавливаем флаг топового размещения
        $advertisement->update(['is_top_placement' => true]);
    }

    protected function applyVipStatus(Advertisement $advertisement): void
    {
        // Устанавливаем VIP-статус
        $advertisement->update(['is_vip' => true]);
    }

    public function getPrice(string $type): float
    {
        return $this->prices[$type] ?? 0;
    }

    public function getDuration(string $type): int
    {
        return $this->durations[$type] ?? 0;
    }
} 