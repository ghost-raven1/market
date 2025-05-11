<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function store(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'required|string|max:1000'
        ]);

        try {
            $complaint = Complaint::create([
                'advertisement_id' => $advertisement->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'description' => $validated['description'],
                'status' => 'pending'
            ]);

            // Отправка уведомления модераторам
            event(new ComplaintCreated($complaint));

            return response()->json([
                'message' => 'Жалоба успешно отправлена',
                'complaint' => $complaint
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create complaint: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не удалось отправить жалобу'
            ], 500);
        }
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:resolved,rejected',
            'resolution_notes' => 'required|string|max:1000'
        ]);

        try {
            $complaint->update([
                'status' => $validated['status'],
                'resolution_notes' => $validated['resolution_notes'],
                'resolved_by' => Auth::id(),
                'resolved_at' => now()
            ]);

            // Отправка уведомления пользователю
            event(new ComplaintResolved($complaint));

            return response()->json([
                'message' => 'Жалоба успешно обработана',
                'complaint' => $complaint
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resolve complaint: ' . $e->getMessage());
            return response()->json([
                'message' => 'Не удалось обработать жалобу'
            ], 500);
        }
    }

    public function index()
    {
        $complaints = Complaint::with(['advertisement', 'user'])
            ->latest()
            ->paginate(20);

        return view('complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['advertisement', 'user', 'resolver']);
        return view('complaints.show', compact('complaint'));
    }
} 