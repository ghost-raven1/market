<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $advertisements = Advertisement::all();

        // Для каждого пользователя создаем 1-5 избранных объявлений
        foreach ($users as $user) {
            $favoritesCount = fake()->numberBetween(1, 5);
            $randomAdvertisements = $advertisements->random($favoritesCount);

            foreach ($randomAdvertisements as $advertisement) {
                Favorite::create([
                    'user_id' => $user->id,
                    'advertisement_id' => $advertisement->id,
                    'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }
} 