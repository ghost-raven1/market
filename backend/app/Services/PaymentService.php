<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\Tariff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(Transaction $transaction): bool
    {
        try {
            DB::beginTransaction();

            // Симуляция обработки платежа
            $success = $this->simulatePaymentProcessing($transaction);

            if ($success) {
                $transaction->update([
                    'status' => 'completed',
                    'payment_id' => 'PAY-' . uniqid(),
                ]);

                // Обработка подписки, если это платеж за подписку
                if ($transaction->type === 'subscription') {
                    $this->handleSubscription($transaction);
                }

                DB::commit();
                return true;
            }

            $transaction->update(['status' => 'failed']);
            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            return false;
        }
    }

    public function processRefund(Transaction $transaction): bool
    {
        try {
            DB::beginTransaction();

            // Симуляция возврата средств
            $success = $this->simulateRefundProcessing($transaction);

            if ($success) {
                $transaction->update([
                    'status' => 'refunded',
                    'payment_id' => 'REF-' . uniqid(),
                ]);

                // Отмена подписки при возврате
                if ($transaction->type === 'subscription') {
                    $this->cancelSubscription($transaction);
                }

                DB::commit();
                return true;
            }

            DB::commit();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing error: ' . $e->getMessage());
            return false;
        }
    }

    protected function simulatePaymentProcessing(Transaction $transaction): bool
    {
        // TODO: Интеграция с реальными платежными системами
        return true;
    }

    protected function simulateRefundProcessing(Transaction $transaction): bool
    {
        // TODO: Интеграция с реальными платежными системами
        return true;
    }

    protected function handleSubscription(Transaction $transaction): void
    {
        $tariff = Tariff::find($transaction->metadata['tariff_id'] ?? null);
        if (!$tariff) {
            throw new \Exception('Tariff not found');
        }

        $subscription = Subscription::create([
            'user_id' => $transaction->user_id,
            'tariff_id' => $tariff->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($tariff->duration ?? 30),
            'status' => 'active',
            'commission_rate' => $this->getCommissionRate($tariff->name),
        ]);

        Log::info('Subscription created', ['subscription_id' => $subscription->id]);
    }

    protected function cancelSubscription(Transaction $transaction): void
    {
        $subscription = Subscription::where('user_id', $transaction->user_id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            Log::info('Subscription cancelled', ['subscription_id' => $subscription->id]);
        }
    }

    protected function getCommissionRate(string $tariffName): float
    {
        return match (strtolower($tariffName)) {
            'basic' => 0.05, // 5%
            'standard' => 0.03, // 3%
            'premium' => 0.01, // 1%
            default => 0.05, // По умолчанию 5%
        };
    }
} 