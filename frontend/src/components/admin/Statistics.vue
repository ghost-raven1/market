<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-bold mb-8">Статистика платформы</h1>

      <!-- Фильтры -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Период
            </label>
            <select
              v-model="period"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="today">Сегодня</option>
              <option value="week">Неделя</option>
              <option value="month">Месяц</option>
              <option value="year">Год</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Категория
            </label>
            <select
              v-model="categoryId"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Все категории</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Тип статистики
            </label>
            <select
              v-model="statType"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="advertisements">Объявления</option>
              <option value="users">Пользователи</option>
              <option value="views">Просмотры</option>
              <option value="favorites">Избранное</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Основные показатели -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
          v-for="metric in metrics"
          :key="metric.title"
          class="bg-white rounded-lg shadow p-6"
        >
          <h3 class="text-sm font-medium text-gray-500 mb-1">{{ metric.title }}</h3>
          <p class="text-2xl font-bold text-gray-900">{{ metric.value }}</p>
          <p
            class="text-sm mt-2"
            :class="metric.change >= 0 ? 'text-green-600' : 'text-red-600'"
          >
            {{ metric.change >= 0 ? '+' : '' }}{{ metric.change }}%
            <span class="text-gray-500">с прошлого периода</span>
          </p>
        </div>
      </div>

      <!-- График -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Динамика</h2>
        <div class="h-80">
          <canvas ref="chartRef"></canvas>
        </div>
      </div>

      <!-- Таблица с детальной статистикой -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="column in columns"
                :key="column.key"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                {{ column.label }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="row in tableData" :key="row.id">
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
              >
                {{ row[column.key] }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import { useNotification } from '@/composables/useNotification'

Chart.register(...registerables)

interface Category {
  id: number
  name: string
}

interface Metric {
  title: string
  value: number | string
  change: number
}

interface Column {
  key: string
  label: string
}

const notification = useNotification()
const loading = ref(false)
const period = ref('week')
const categoryId = ref('')
const statType = ref('advertisements')
const categories = ref<Category[]>([])
const metrics = ref<Metric[]>([])
const tableData = ref<any[]>([])
const chartRef = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

const columns: Column[] = [
  { key: 'date', label: 'Дата' },
  { key: 'count', label: 'Количество' },
  { key: 'change', label: 'Изменение' }
]

// Загрузка категорий
const loadCategories = async () => {
  try {
    const response = await fetch(`api/categories`)
    if (!response.ok) throw new Error('Failed to load categories')
    categories.value = await response.json()
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить категории')
    console.error('Error loading categories:', error)
  }
}

// Загрузка статистики
const loadStatistics = async () => {
  try {
    loading.value = true
    const params = new URLSearchParams({
      period: period.value,
      category_id: categoryId.value,
      type: statType.value
    })

    const response = await fetch(`api/statistics?${params}`)
    if (!response.ok) throw new Error('Failed to load statistics')
    const data = await response.json()

    metrics.value = data.metrics
    tableData.value = data.tableData
    updateChart(data.chartData)
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить статистику')
    console.error('Error loading statistics:', error)
  } finally {
    loading.value = false
  }
}

// Обновление графика
const updateChart = (data: any) => {
  if (!chartRef.value) return

  if (chart) {
    chart.destroy()
  }

  chart = new Chart(chartRef.value, {
    type: 'line',
    data: {
      labels: data.labels,
      datasets: [
        {
          label: 'Значение',
          data: data.values,
          borderColor: 'rgb(59, 130, 246)',
          tension: 0.1
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  })
}

// Следим за изменениями фильтров
watch([period, categoryId, statType], () => {
  loadStatistics()
})

onMounted(() => {
  loadCategories()
  loadStatistics()
})
</script>
