<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $advertisements = Advertisement::all();

        // Создаем 100 случайных сообщений
        for ($i = 0; $i < 100; $i++) {
            $sender = $users->random();
            $receiver = $users->random();
            $advertisement = $advertisements->random();

            // Пропускаем, если отправитель и получатель совпадают
            if ($sender->id === $receiver->id) {
                continue;
            }

            Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'advertisement_id' => $advertisement->id,
                'content' => fake()->paragraph(2),
                'is_read' => fake()->boolean(70), // 70% сообщений прочитаны
                'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
} 