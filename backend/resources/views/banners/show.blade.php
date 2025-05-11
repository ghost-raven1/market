@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Детали баннера</h1>
            <div class="space-x-4">
                <a href="{{ route('banners.edit', $banner) }}" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded">
                    Редактировать
                </a>
                <form action="{{ route('banners.destroy', $banner) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Вы уверены?')">
                        Удалить
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Информация о баннере</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Реклама</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $banner->advertisement->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Размер</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $banner->width }}x{{ $banner->height }}px</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Позиция</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @switch($banner->position)
                                        @case('top')
                                            Верх страницы
                                            @break
                                        @case('bottom')
                                            Низ страницы
                                            @break
                                        @case('sidebar')
                                            Боковая панель
                                            @break
                                    @endswitch
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Статус</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $banner->is_active ? 'Активен' : 'Неактивен' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Целевая ссылка</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ $banner->target_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $banner->target_url }}
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold mb-4">Статистика</h2>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Показы</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($statistics['impressions']) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Клики</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($statistics['clicks']) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">CTR</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($statistics['ctr'], 2) }}%</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-4">Предпросмотр баннера</h2>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->alt_text }}" class="mx-auto">
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-4">Код для вставки</h2>
                    <div class="bg-gray-100 rounded-lg p-4">
                        <pre class="text-sm"><code>&lt;a href="{{ route('banners.click', $banner) }}" target="_blank"&gt;
    &lt;img src="{{ route('banners.impression', $banner) }}" 
         alt="{{ $banner->alt_text }}"
         width="{{ $banner->width }}"
         height="{{ $banner->height }}"&gt;
&lt;/a&gt;</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 