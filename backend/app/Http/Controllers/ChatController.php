<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Services\SupportChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(SupportChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index()
    {
        $rooms = $this->chatService->getActiveRooms();
        return view('chat.index', compact('rooms'));
    }

    public function show(ChatRoom $room)
    {
        $messages = $this->chatService->getMessages($room);
        return view('chat.show', compact('room', 'messages'));
    }

    public function store(Request $request, ChatRoom $room)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $message = $this->chatService->sendMessage(
                $room,
                Auth::user(),
                $validated['message']
            );

            return response()->json([
                'message' => $message,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Не удалось отправить сообщение',
                'status' => 'error'
            ], 500);
        }
    }

    public function create()
    {
        $room = $this->chatService->createRoom(Auth::user());
        return redirect()->route('chat.show', $room);
    }

    public function close(Request $request, ChatRoom $room)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $this->chatService->closeRoom($room, $validated['reason'] ?? null);

        return redirect()->route('chat.index')
            ->with('success', 'Чат успешно закрыт');
    }

    public function markAsRead(ChatRoom $room)
    {
        $room->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
} 