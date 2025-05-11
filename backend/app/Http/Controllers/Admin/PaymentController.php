<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Tariff;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $transactions = Transaction::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user');
        return view('admin.payments.show', compact('transaction'));
    }

    public function process(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            $success = $this->paymentService->processPayment($transaction);

            DB::commit();

            return redirect()
                ->route('admin.payments.show', $transaction)
                ->with('success', 'Платеж успешно обработан');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при обработке платежа: ' . $e->getMessage());
        }
    }

    public function refund(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            $transaction->status = 'refunded';
            $transaction->save();

            // Если это была подписка, отменяем её
            if ($transaction->type === 'subscription') {
                $subscription = $transaction->user->subscription;
                if ($subscription) {
                    $subscription->status = 'cancelled';
                    $subscription->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.payments.show', $transaction)
                ->with('success', 'Платеж успешно возвращен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при возврате платежа: ' . $e->getMessage());
        }
    }

    public function statistics()
    {
        $stats = [
            'total_revenue' => Transaction::where('status', 'completed')->sum('amount'),
            'pending_payments' => Transaction::where('status', 'pending')->count(),
            'subscriptions' => [
                'basic' => Subscription::whereHas('tariff', fn($q) => $q->where('name', 'Базовый'))->count(),
                'standard' => Subscription::whereHas('tariff', fn($q) => $q->where('name', 'Стандарт'))->count(),
                'premium' => Subscription::whereHas('tariff', fn($q) => $q->where('name', 'Премиум'))->count(),
            ],
            'recent_transactions' => Transaction::with('user')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.payments.statistics', compact('stats'));
    }
} 