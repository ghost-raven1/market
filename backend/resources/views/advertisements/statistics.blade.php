@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Статистика рекламы</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Общая статистика -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Всего объявлений</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_ads'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Активных объявлений</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_ads'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Всего потрачено</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($stats['total_spent'], 2) }}₽</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Показов за последние 30 дней</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($stats['recent_impressions']) }}</p>
            </div>
        </div>

        <!-- Статистика по типам -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Статистика по типам рекламы</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Количество</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Потрачено</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stats['by_type'] as $type)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @switch($type->type)
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $type->count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($type->total_spent, 2) }}₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Графики -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Показы и клики</h3>
                <canvas id="impressionsChart"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Расходы по типам</h3>
                <canvas id="spendingChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // График показов и кликов
    const impressionsCtx = document.getElementById('impressionsChart').getContext('2d');
    new Chart(impressionsCtx, {
        type: 'line',
        data: {
            labels: ['Показы', 'Клики'],
            datasets: [{
                label: 'Статистика',
                data: [{{ $stats['recent_impressions'] }}, {{ $stats['recent_clicks'] }}],
                backgroundColor: [
                    'rgba(79, 70, 229, 0.2)',
                    'rgba(16, 185, 129, 0.2)'
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(16, 185, 129, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // График расходов по типам
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    new Chart(spendingCtx, {
        type: 'pie',
        data: {
            labels: [
                'Баннеры',
                'Категории',
                'Email-рассылки'
            ],
            datasets: [{
                data: [
                    {{ $stats['by_type']->where('type', 'banner')->sum('total_spent') }},
                    {{ $stats['by_type']->where('type', 'category')->sum('total_spent') }},
                    {{ $stats['by_type']->where('type', 'email')->sum('total_spent') }}
                ],
                backgroundColor: [
                    'rgba(79, 70, 229, 0.2)',
                    'rgba(16, 185, 129, 0.2)',
                    'rgba(245, 158, 11, 0.2)'
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
});
</script>
@endpush
@endsection 