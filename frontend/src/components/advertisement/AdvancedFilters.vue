<template>
  <div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Фильтр по рейтингу -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Рейтинг продавца
        </label>
        <select
          v-model="filters.rating"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Любой рейтинг</option>
          <option value="4">4+ звезд</option>
          <option value="3">3+ звезд</option>
          <option value="2">2+ звезд</option>
        </select>
      </div>

      <!-- Фильтр по дате -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Дата публикации
        </label>
        <select
          v-model="filters.dateRange"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Любая дата</option>
          <option value="today">Сегодня</option>
          <option value="week">За неделю</option>
          <option value="month">За месяц</option>
          <option value="year">За год</option>
        </select>
      </div>

      <!-- Фильтр по местоположению -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Город
        </label>
        <input
          v-model="filters.city"
          type="text"
          placeholder="Введите город"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Фильтр по радиусу -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Радиус поиска
        </label>
        <select
          v-model="filters.radius"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">Любой радиус</option>
          <option value="1">1 км</option>
          <option value="5">5 км</option>
          <option value="10">10 км</option>
          <option value="20">20 км</option>
          <option value="50">50 км</option>
        </select>
      </div>
    </div>

    <!-- Дополнительные опции -->
    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="flex items-center">
        <input
          v-model="filters.withPhoto"
          type="checkbox"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label class="ml-2 block text-sm text-gray-700">
          Только с фото
        </label>
      </div>
      <div class="flex items-center">
        <input
          v-model="filters.verifiedOnly"
          type="checkbox"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label class="ml-2 block text-sm text-gray-700">
          Только проверенные продавцы
        </label>
      </div>
      <div class="flex items-center">
        <input
          v-model="filters.activeOnly"
          type="checkbox"
          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label class="ml-2 block text-sm text-gray-700">
          Только активные объявления
        </label>
      </div>
    </div>

    <!-- Кнопки управления -->
    <div class="mt-6 flex justify-end space-x-4">
      <button
        @click="resetFilters"
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Сбросить
      </button>
      <button
        @click="applyFilters"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Применить
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

interface Filters {
  rating: string
  dateRange: string
  city: string
  radius: string
  withPhoto: boolean
  verifiedOnly: boolean
  activeOnly: boolean
}

const emit = defineEmits<{
  (e: 'update:filters', filters: Filters): void
}>()

const filters = ref<Filters>({
  rating: '',
  dateRange: '',
  city: '',
  radius: '',
  withPhoto: false,
  verifiedOnly: false,
  activeOnly: false
})

// Сброс фильтров
const resetFilters = () => {
  filters.value = {
    rating: '',
    dateRange: '',
    city: '',
    radius: '',
    withPhoto: false,
    verifiedOnly: false,
    activeOnly: false
  }
  emit('update:filters', filters.value)
}

// Применение фильтров
const applyFilters = () => {
  emit('update:filters', filters.value)
}

// Следим за изменениями фильтров
watch(filters, (newFilters) => {
  emit('update:filters', newFilters)
}, { deep: true })
</script> 