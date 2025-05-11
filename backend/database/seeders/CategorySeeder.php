<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Электроника',
                'slug' => 'electronics',
                'description' => 'Смартфоны, ноутбуки, планшеты и другая электроника',
            ],
            [
                'name' => 'Одежда',
                'slug' => 'clothing',
                'description' => 'Мужская, женская и детская одежда',
            ],
            [
                'name' => 'Дом и сад',
                'slug' => 'home-and-garden',
                'description' => 'Мебель, декор, садовые инструменты',
            ],
            [
                'name' => 'Спорт',
                'slug' => 'sports',
                'description' => 'Спортивный инвентарь, одежда и аксессуары',
            ],
            [
                'name' => 'Красота и здоровье',
                'slug' => 'beauty-and-health',
                'description' => 'Косметика, парфюмерия, товары для здоровья',
            ],
            [
                'name' => 'Авто',
                'slug' => 'auto',
                'description' => 'Автомобили, запчасти, аксессуары',
            ],
            [
                'name' => 'Книги',
                'slug' => 'books',
                'description' => 'Художественная и учебная литература',
            ],
            [
                'name' => 'Игрушки',
                'slug' => 'toys',
                'description' => 'Детские игрушки и игры',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 