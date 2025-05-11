@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Мои рекламные объявления</h1>
            <a href="{{ route('advertisements.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Создать рекламу
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($advertisements->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-500 mb-4">У вас пока нет рекламных объявлений</p>
                <a href="{{ route('advertisements.create') }}" class="text-indigo-600 hover:text-indigo-800">
                    Создать первое объявление →
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Название</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Бюджет</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Показы</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Клики</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($advertisements as $ad)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @switch($ad->type)
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ad->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @switch($ad->status)
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
                                        @switch($ad->status)
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($ad->budget, 2) }}₽
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($ad->statistics['impressions'] ?? 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($ad->statistics['clicks'] ?? 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('advertisements.show', $ad) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Подробнее
                                    </a>
                                    @if($ad->status === 'active')
                                        <a href="{{ route('advertisements.statistics', $ad) }}" class="text-green-600 hover:text-green-900">
                                            Статистика
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $advertisements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 