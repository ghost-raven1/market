<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        // Подписки для продавцов
        $subscriptions = [
            [
                'name' => 'Базовый',
                'type' => 'subscription',
                'price' => 0,
                'duration' => 30,
                'features' => [
                    'Базовая комиссия 5%',
                    'До 10 активных объявлений',
                    'Базовая поддержка'
                ],
            ],
            [
                'name' => 'Стандарт',
                'type' => 'subscription',
                'price' => 999,
                'duration' => 30,
                'features' => [
                    'Комиссия 3%',
                    'До 50 активных объявлений',
                    'Приоритетная поддержка',
                    'Поднятие объявлений 1 раз в неделю'
                ],
            ],
            [
                'name' => 'Премиум',
                'type' => 'subscription',
                'price' => 2999,
                'duration' => 30,
                'features' => [
                    'Комиссия 1%',
                    'Неограниченное количество объявлений',
                    'VIP поддержка 24/7',
                    'Поднятие объявлений 3 раза в неделю',
                    'Выделение цветом',
                    'Закрепление в топе'
                ],
            ],
        ];

        // Премиум-функции
        $premiumFeatures = [
            [
                'name' => 'Поднятие объявления',
                'type' => 'premium_feature',
                'price' => 199,
                'duration' => 1,
                'features' => [
                    'Поднятие объявления в топ категории',
                    'Действует 24 часа'
                ],
            ],
            [
                'name' => 'Выделение цветом',
                'type' => 'premium_feature',
                'price' => 299,
                'duration' => 7,
                'features' => [
                    'Выделение объявления цветом',
                    'Действует 7 дней'
                ],
            ],
            [
                'name' => 'Закрепление в топе',
                'type' => 'premium_feature',
                'price' => 499,
                'duration' => 7,
                'features' => [
                    'Закрепление объявления в топе категории',
                    'Действует 7 дней'
                ],
            ],
        ];

        // Рекламные возможности
        $advertising = [
            [
                'name' => 'Баннер на главной',
                'type' => 'advertising',
                'price' => 999,
                'duration' => 1,
                'features' => [
                    'Размещение баннера на главной странице',
                    'Действует 24 часа'
                ],
            ],
            [
                'name' => 'Реклама в категории',
                'type' => 'advertising',
                'price' => 499,
                'duration' => 1,
                'features' => [
                    'Размещение рекламы в выбранной категории',
                    'Действует 24 часа'
                ],
            ],
            [
                'name' => 'Email-рассылка',
                'type' => 'advertising',
                'price' => 1999,
                'duration' => null,
                'features' => [
                    'Рассылка по базе пользователей',
                    'Одноразовая рассылка'
                ],
            ],
        ];

        foreach (array_merge($subscriptions, $premiumFeatures, $advertising) as $tariff) {
            Tariff::create($tariff);
        }
    }
} 