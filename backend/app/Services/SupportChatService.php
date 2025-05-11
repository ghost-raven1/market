<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SupportChatService
{
    protected $cachePrefix = 'support_chat:';
    protected $messageLimit = 50;

    public function createRoom(User $user): ChatRoom
    {
        return ChatRoom::create([
            'user_id' => $user->id,
            'status' => 'active'
        ]);
    }

    public function sendMessage(ChatRoom $room, User $user, string $message): ChatMessage
    {
        try {
            $chatMessage = ChatMessage::create([
                'chat_room_id' => $room->id,
                'user_id' => $user->id,
                'message' => $message
            ]);

            // Очистка кэша сообщений
            $this->clearRoomCache($room);

            // Отправка уведомления
            event(new NewChatMessage($chatMessage));

            return $chatMessage;
        } catch (\Exception $e) {
            Log::error('Failed to send chat message: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getMessages(ChatRoom $room, int $page = 1): array
    {
        $cacheKey = $this->cachePrefix . "room:{$room->id}:messages:{$page}";

        return Cache::remember($cacheKey, 300, function () use ($room, $page) {
            return ChatMessage::where('chat_room_id', $room->id)
                ->with('user')
                ->latest()
                ->paginate($this->messageLimit, ['*'], 'page', $page)
                ->toArray();
        });
    }

    public function getActiveRooms(): array
    {
        return ChatRoom::with(['user', 'lastMessage'])
            ->where('status', 'active')
            ->latest('updated_at')
            ->get()
            ->toArray();
    }

    public function closeRoom(ChatRoom $room, string $reason = null): void
    {
        $room->update([
            'status' => 'closed',
            'closed_at' => now(),
            'close_reason' => $reason
        ]);

        // Очистка кэша
        $this->clearRoomCache($room);
    }

    protected function clearRoomCache(ChatRoom $room): void
    {
        $pattern = $this->cachePrefix . "room:{$room->id}:*";
        $keys = Cache::get($pattern);
        if ($keys) {
            Cache::delete($keys);
        }
    }

    public function isUserOnline(User $user): bool
    {
        return Cache::has("user:{$user->id}:online");
    }

    public function markUserOnline(User $user): void
    {
        Cache::put("user:{$user->id}:online", true, 300); // 5 минут
    }

    public function markUserOffline(User $user): void
    {
        Cache::forget("user:{$user->id}:online");
    }
} 