@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Настройки баннера</h1>
            <a href="{{ route('admin.banners.show', $banner) }}" class="text-gray-600 hover:text-gray-800">
                Назад к баннеру
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.banners.settings.update', $banner) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <!-- Адаптивность -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Адаптивность</h2>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_adaptive" value="1" {{ $banner->is_adaptive ? 'checked' : '' }}
                            class="form-checkbox h-4 w-4 text-blue-600">
                        <span class="ml-2 text-gray-700">Включить адаптивность</span>
                    </label>
                </div>

                <div id="adaptive-settings" class="{{ $banner->is_adaptive ? '' : 'hidden' }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Мобильные устройства
                            </label>
                            <input type="number" name="adaptive_settings[mobile][width]" 
                                value="{{ $banner->adaptive_settings['mobile']['width'] ?? 320 }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Ширина">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Планшеты
                            </label>
                            <input type="number" name="adaptive_settings[tablet][width]"
                                value="{{ $banner->adaptive_settings['tablet']['width'] ?? 768 }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Ширина">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Расписание показов -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Расписание показов</h2>
                @php
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                @endphp
                @foreach($days as $day)
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            {{ ucfirst($day) }}
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="time" name="schedule_settings[{{ $day }}][start]"
                                    value="{{ $banner->schedule_settings[$day]['start'] ?? '00:00' }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <div>
                                <input type="time" name="schedule_settings[{{ $day }}][end]"
                                    value="{{ $banner->schedule_settings[$day]['end'] ?? '23:59' }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Лимит показов -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Лимит показов</h2>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Максимальное количество показов в день
                    </label>
                    <input type="number" name="daily_impression_limit"
                        value="{{ $banner->daily_impression_limit }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Оставьте пустым для снятия ограничения">
                </div>
            </div>

            <!-- Настройки браузеров -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Настройки браузеров</h2>
                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="browser_settings[chrome]" value="1"
                                {{ isset($banner->browser_settings['chrome']) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Google Chrome</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="browser_settings[firefox]" value="1"
                                {{ isset($banner->browser_settings['firefox']) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Mozilla Firefox</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="browser_settings[safari]" value="1"
                                {{ isset($banner->browser_settings['safari']) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Safari</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="browser_settings[edge]" value="1"
                                {{ isset($banner->browser_settings['edge']) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-gray-700">Microsoft Edge</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Сохранить настройки
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Показ/скрытие настроек адаптивности
    document.querySelector('input[name="is_adaptive"]').addEventListener('change', function(e) {
        document.getElementById('adaptive-settings').classList.toggle('hidden', !e.target.checked);
    });
</script>
@endpush
@endsection 