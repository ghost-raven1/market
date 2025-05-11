@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Редактирование баннера</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                    Изображение баннера
                </label>
                <div class="mb-2">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->alt_text }}" class="max-w-full h-auto">
                </div>
                <input type="file" name="image" id="image" accept="image/*"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs mt-1">Оставьте пустым, чтобы сохранить текущее изображение. Максимальный размер файла: 2MB</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="width">
                        Ширина (px)
                    </label>
                    <input type="number" name="width" id="width" value="{{ $banner->width }}" required min="1"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="height">
                        Высота (px)
                    </label>
                    <input type="number" name="height" id="height" value="{{ $banner->height }}" required min="1"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="alt_text">
                    Альтернативный текст
                </label>
                <input type="text" name="alt_text" id="alt_text" value="{{ $banner->alt_text }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Описание изображения для SEO">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="target_url">
                    Целевая ссылка
                </label>
                <input type="url" name="target_url" id="target_url" value="{{ $banner->target_url }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="https://example.com">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                    Позиция размещения
                </label>
                <select name="position" id="position" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="top" {{ $banner->position === 'top' ? 'selected' : '' }}>Верх страницы</option>
                    <option value="bottom" {{ $banner->position === 'bottom' ? 'selected' : '' }}>Низ страницы</option>
                    <option value="sidebar" {{ $banner->position === 'sidebar' ? 'selected' : '' }}>Боковая панель</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}
                        class="form-checkbox h-4 w-4 text-blue-600">
                    <span class="ml-2 text-gray-700">Активен</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Сохранить изменения
                </button>
                <a href="{{ route('banners.show', $banner) }}" class="text-gray-600 hover:text-gray-800">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Предпросмотр изображения
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    document.getElementById('width').value = img.width;
                    document.getElementById('height').value = img.height;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection 