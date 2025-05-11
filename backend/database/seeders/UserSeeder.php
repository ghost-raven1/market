<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем администратора
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+7 (999) 123-45-67',
            'address' => 'г. Москва, ул. Примерная, д. 1',
            'email_verified_at' => now(),
        ]);

        // Создаем модератора
        User::create([
            'name' => 'Moderator User',
            'email' => 'moderator@example.com',
            'password' => Hash::make('password'),
            'role' => 'moderator',
            'phone' => '+7 (999) 234-56-78',
            'address' => 'г. Санкт-Петербург, пр. Невский, д. 2',
            'email_verified_at' => now(),
        ]);

        // Создаем обычных пользователей
        $users = [
            [
                'name' => 'Иван Петров',
                'email' => 'ivan@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+7 (999) 345-67-89',
                'address' => 'г. Москва, ул. Ленина, д. 10',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Мария Сидорова',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+7 (999) 456-78-90',
                'address' => 'г. Санкт-Петербург, ул. Гагарина, д. 5',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Алексей Иванов',
                'email' => 'alexey@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+7 (999) 567-89-01',
                'address' => 'г. Екатеринбург, ул. Мира, д. 15',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Елена Смирнова',
                'email' => 'elena@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+7 (999) 678-90-12',
                'address' => 'г. Новосибирск, ул. Советская, д. 20',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Дмитрий Козлов',
                'email' => 'dmitry@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '+7 (999) 789-01-23',
                'address' => 'г. Казань, ул. Баумана, д. 25',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
} 