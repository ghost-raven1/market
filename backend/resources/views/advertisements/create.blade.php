@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Создание рекламного объявления</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('advertisements.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Тип рекламы</label>
                <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Выберите тип</option>
                    <option value="banner" data-min="999" data-unit="день">Баннер на главной (от 999₽/день)</option>
                    <option value="category" data-min="499" data-unit="день">Реклама в категории (от 499₽/день)</option>
                    <option value="email" data-min="1999" data-unit="рассылка">Email-рассылка (от 1999₽/рассылка)</option>
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Заголовок</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Содержание</label>
                <textarea name="content" id="content" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>
            </div>

            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700">URL изображения</label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="target_url" class="block text-sm font-medium text-gray-700">Целевая ссылка</label>
                <input type="url" name="target_url" id="target_url" value="{{ old('target_url') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700">Дата начала</label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700">Дата окончания</label>
                    <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700">Бюджет (₽)</label>
                <input type="number" name="budget" id="budget" value="{{ old('budget') }}" required min="0" step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-sm text-gray-500" id="budget-hint"></p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Создать рекламу
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const budgetInput = document.getElementById('budget');
    const budgetHint = document.getElementById('budget-hint');

    function updateBudgetHint() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        if (selectedOption.value) {
            const minBudget = selectedOption.dataset.min;
            const unit = selectedOption.dataset.unit;
            budgetHint.textContent = `Минимальный бюджет: ${minBudget}₽/${unit}`;
            budgetInput.min = minBudget;
        } else {
            budgetHint.textContent = '';
        }
    }

    typeSelect.addEventListener('change', updateBudgetHint);
    updateBudgetHint();
});
</script>
@endpush
@endsection 