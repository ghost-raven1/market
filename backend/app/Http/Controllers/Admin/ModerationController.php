<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moderation;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function index()
    {
        $moderations = Moderation::with(['moderatable', 'moderator'])
            ->latest()
            ->paginate(20);

        return view('admin.moderations.index', compact('moderations'));
    }

    public function show(Moderation $moderation)
    {
        $moderation->load(['moderatable', 'moderator']);
        return view('admin.moderations.show', compact('moderation'));
    }

    public function approve(Moderation $moderation)
    {
        try {
            DB::beginTransaction();

            $moderation->approve(auth()->id());

            // Если это объявление, обновляем его статус
            if ($moderation->moderatable_type === 'App\Models\Advertisement') {
                $moderation->moderatable->update(['status' => 'active']);
            }

            DB::commit();

            return redirect()
                ->route('admin.moderations.show', $moderation)
                ->with('success', 'Контент успешно одобрен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при одобрении контента: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Moderation $moderation)
    {
        try {
            DB::beginTransaction();

            $moderation->reject(auth()->id(), $request->input('reason'));

            // Если это объявление, обновляем его статус
            if ($moderation->moderatable_type === 'App\Models\Advertisement') {
                $moderation->moderatable->update(['status' => 'rejected']);
            }

            DB::commit();

            return redirect()
                ->route('admin.moderations.show', $moderation)
                ->with('success', 'Контент отклонен');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при отклонении контента: ' . $e->getMessage());
        }
    }

    public function statistics()
    {
        $stats = [
            'total_pending' => Moderation::where('status', 'pending')->count(),
            'total_approved' => Moderation::where('status', 'approved')->count(),
            'total_rejected' => Moderation::where('status', 'rejected')->count(),
            'by_type' => [
                'text' => Moderation::where('type', 'text')->count(),
                'image' => Moderation::where('type', 'image')->count(),
                'user' => Moderation::where('type', 'user')->count(),
            ],
            'recent_moderations' => Moderation::with(['moderatable', 'moderator'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.moderations.statistics', compact('stats'));
    }
} 