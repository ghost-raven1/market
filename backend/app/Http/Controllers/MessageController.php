<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class MessageController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for($request->user()->conversations())
            ->allowedFilters([
                'advertisement_id',
                'recipient_id',
                'is_read',
                AllowedFilter::exact('advertisement_id'),
                AllowedFilter::exact('recipient_id'),
                AllowedFilter::exact('is_read'),
            ])
            ->allowedSorts(['created_at', 'updated_at'])
            ->with(['sender', 'recipient', 'advertisement']);

        return $query->paginate(10);
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $advertisement->user_id,
            'advertisement_id' => $advertisement->id,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load(['sender', 'recipient', 'advertisement'])
        ], 201);
    }

    /**
     * Display the specified conversation.
     */
    public function show(Request $request, Advertisement $advertisement)
    {
        $messages = Message::where('advertisement_id', $advertisement->id)
            ->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user()->id)
                    ->orWhere('recipient_id', $request->user()->id);
            })
            ->with(['sender', 'recipient', 'advertisement'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, Advertisement $advertisement)
    {
        Message::where('advertisement_id', $advertisement->id)
            ->where('recipient_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Messages marked as read'
        ]);
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        
        $message->delete();

        return response()->json(null, 204);
    }
}
