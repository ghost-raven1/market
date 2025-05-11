<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Services\AdvertisingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdvertisementController extends Controller
{
    protected AdvertisingService $advertisingService;

    public function __construct(AdvertisingService $advertisingService)
    {
        $this->advertisingService = $advertisingService;
    }

    public function index()
    {
        $advertisements = Advertisement::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function show(Advertisement $advertisement)
    {
        $advertisement->load('user');
        return view('admin.advertisements.show', compact('advertisement'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:banner,category,email',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'target_url' => 'nullable|url',
            'targeting' => 'nullable|array',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'budget' => 'required|numeric|min:0',
        ]);

        try {
            $advertisement = $this->advertisingService->createAdvertisement($request->all());
            
            return redirect()
                ->route('admin.advertisements.show', $advertisement)
                ->with('success', 'Рекламное объявление успешно создано');
        } catch (\Exception $e) {
            Log::error('Failed to create advertisement: ' . $e->getMessage());
            return back()->with('error', 'Не удалось создать рекламное объявление');
        }
    }

    public function approve(Advertisement $advertisement)
    {
        try {
            $advertisement->update(['status' => 'active']);
            return back()->with('success', 'Рекламное объявление одобрено');
        } catch (\Exception $e) {
            Log::error('Failed to approve advertisement: ' . $e->getMessage());
            return back()->with('error', 'Не удалось одобрить рекламное объявление');
        }
    }

    public function reject(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            $advertisement->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
            ]);
            return back()->with('success', 'Рекламное объявление отклонено');
        } catch (\Exception $e) {
            Log::error('Failed to reject advertisement: ' . $e->getMessage());
            return back()->with('error', 'Не удалось отклонить рекламное объявление');
        }
    }

    public function statistics()
    {
        $stats = [
            'total_ads' => Advertisement::count(),
            'active_ads' => Advertisement::where('status', 'active')->count(),
            'total_revenue' => Advertisement::sum('budget'),
            'by_type' => Advertisement::selectRaw('type, count(*) as count, sum(budget) as total_budget')
                ->groupBy('type')
                ->get(),
            'recent_impressions' => Advertisement::where('status', 'active')
                ->sum('statistics->impressions'),
            'recent_clicks' => Advertisement::where('status', 'active')
                ->sum('statistics->clicks'),
        ];

        return view('admin.advertisements.statistics', compact('stats'));
    }
} 