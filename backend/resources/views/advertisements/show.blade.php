@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('advertisements.index') }}" class="text-indigo-600 hover:text-indigo-900">
                ← Назад к списку
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $advertisement->title }}</h1>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @switch($advertisement->status)
                            @case('pending')
                                bg-yellow-100 text-yellow-800
                                @break
                            @case('active')
                                bg-green-100 text-green-800
                                @break
                            @case('completed')
                                bg-gray-100 text-gray-800
                                @break
                            @case('rejected')
                                bg-red-100 text-red-800
                                @break
                        @endswitch
                    ">
                        @switch($advertisement->status)
                            @case('pending')
                                На модерации
                                @break
                            @case('active')
                                Активно
                                @break
                            @case('completed')
                                Завершено
                                @break
                            @case('rejected')
                                Отклонено
                                @break
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Основная информация -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Основная информация</h2>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Тип рекламы</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @switch($advertisement->type)
                                        @case('banner')
                                            Баннер на главной
                                            @break
                                        @case('category')
                                            Реклама в категории
                                            @break
                                        @case('email')
                                            Email-рассылка
                                            @break
                                    @endswitch
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Бюджет</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($advertisement->budget, 2) }}₽</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Период показа</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $advertisement->starts_at->format('d.m.Y') }} - {{ $advertisement->ends_at->format('d.m.Y') }}
                                </dd>
                            </div>

                            @if($advertisement->targeting)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Таргетинг</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <ul class="list-disc list-inside">
                                        @foreach($advertisement->targeting as $key => $value)
                                            <li>{{ $key }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Статистика -->
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Статистика</h2>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Показы</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($advertisement->statistics['impressions'] ?? 0) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Клики</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($advertisement->statistics['clicks'] ?? 0) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">CTR</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @php
                                        $impressions = $advertisement->statistics['impressions'] ?? 0;
                                        $clicks = $advertisement->statistics['clicks'] ?? 0;
                                        $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
                                    @endphp
                                    {{ number_format($ctr, 2) }}%
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Потрачено</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($advertisement->spent, 2) }}₽</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Контент -->
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Контент</h2>
                    <div class="prose max-w-none">
                        {!! $advertisement->content !!}
                    </div>

                    @if($advertisement->image_url)
                    <div class="mt-4">
                        <img src="{{ $advertisement->image_url }}" alt="{{ $advertisement->title }}" class="max-w-lg rounded-lg shadow">
                    </div>
                    @endif
                </div>

                <!-- Действия -->
                @if($advertisement->status === 'active')
                <div class="mt-8 flex space-x-4">
                    <a href="{{ route('advertisements.statistics', $advertisement) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Подробная статистика
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 