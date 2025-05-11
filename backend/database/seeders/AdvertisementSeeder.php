<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        // Создаем 50 случайных объявлений
        for ($i = 0; $i < 50; $i++) {
            Advertisement::create([
                'title' => fake()->sentence(3),
                'description' => fake()->paragraph(3),
                'price' => fake()->numberBetween(100, 100000),
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'status' => fake()->randomElement(['active', 'pending', 'sold']),
                'views_count' => fake()->numberBetween(0, 1000),
                'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
} 