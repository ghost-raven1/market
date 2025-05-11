<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // Для каждого пользователя создаем 1-3 оценки
        foreach ($users as $user) {
            $ratingsCount = fake()->numberBetween(1, 3);
            $randomUsers = $users->random($ratingsCount);

            foreach ($randomUsers as $rater) {
                // Пропускаем, если пользователь оценивает сам себя
                if ($rater->id === $user->id) {
                    continue;
                }

                Rating::create([
                    'user_id' => $user->id,
                    'rater_id' => $rater->id,
                    'rating' => fake()->numberBetween(1, 5),
                    'comment' => fake()->optional(0.7)->sentence(), // 70% оценок имеют комментарий
                    'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
} 