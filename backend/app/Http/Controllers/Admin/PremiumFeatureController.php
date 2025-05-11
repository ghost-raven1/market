<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\PremiumFeature;
use App\Services\PremiumFeatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PremiumFeatureController extends Controller
{
    protected PremiumFeatureService $premiumService;

    public function __construct(PremiumFeatureService $premiumService)
    {
        $this->premiumService = $premiumService;
    }

    public function index()
    {
        $features = PremiumFeature::with(['advertisement.user'])
            ->latest()
            ->paginate(20);

        return view('admin.premium-features.index', compact('features'));
    }

    public function show(PremiumFeature $feature)
    {
        $feature->load(['advertisement.user']);
        return view('admin.premium-features.show', compact('feature'));
    }

    public function activate(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'type' => 'required|in:boost,highlight,top_placement,vip',
            'settings' => 'nullable|array',
        ]);

        try {
            $feature = $this->premiumService->activateFeature(
                $advertisement,
                $request->type,
                $request->settings ?? []
            );

            return redirect()
                ->route('admin.premium-features.show', $feature)
                ->with('success', 'Премиум-функция успешно активирована');
        } catch (\Exception $e) {
            Log::error('Failed to activate premium feature: ' . $e->getMessage());
            return back()->with('error', 'Не удалось активировать премиум-функцию');
        }
    }

    public function deactivate(PremiumFeature $feature)
    {
        try {
            $feature->update(['ends_at' => now()]);
            return back()->with('success', 'Премиум-функция деактивирована');
        } catch (\Exception $e) {
            Log::error('Failed to deactivate premium feature: ' . $e->getMessage());
            return back()->with('error', 'Не удалось деактивировать премиум-функцию');
        }
    }

    public function statistics()
    {
        $stats = [
            'total_features' => PremiumFeature::count(),
            'active_features' => PremiumFeature::where('ends_at', '>', now())->count(),
            'revenue' => PremiumFeature::sum('price'),
            'by_type' => PremiumFeature::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
        ];

        return view('admin.premium-features.statistics', compact('stats'));
    }
} 