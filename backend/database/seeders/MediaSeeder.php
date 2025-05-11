<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $advertisements = Advertisement::all();

        // Для каждого объявления создаем 1-5 изображений
        foreach ($advertisements as $advertisement) {
            $mediaCount = fake()->numberBetween(1, 5);

            for ($i = 0; $i < $mediaCount; $i++) {
                Media::create([
                    'advertisement_id' => $advertisement->id,
                    'file_name' => 'sample-' . fake()->numberBetween(1, 10) . '.jpg',
                    'file_path' => 'advertisements/' . $advertisement->id . '/sample-' . fake()->numberBetween(1, 10) . '.jpg',
                    'file_type' => 'image/jpeg',
                    'file_size' => fake()->numberBetween(100000, 5000000), // 100KB - 5MB
                    'created_at' => $advertisement->created_at,
                    'updated_at' => $advertisement->updated_at,
                ]);
            }
        }
    }
} 