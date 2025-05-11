<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            AdvertisementSeeder::class,
            FavoriteSeeder::class,
            MessageSeeder::class,
            RatingSeeder::class,
            MediaSeeder::class,
            TariffSeeder::class,
        ]);
    }
}
