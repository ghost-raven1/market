<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-bold mb-8">Модерация контента</h1>

      <!-- Фильтры -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Статус
            </label>
            <select
              v-model="status"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="pending">Ожидает проверки</option>
              <option value="approved">Одобренные</option>
              <option value="rejected">Отклоненные</option>
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
              Сортировка
            </label>
            <select
              v-model="sortBy"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="created_at">По дате создания</option>
              <option value="title">По названию</option>
              <option value="price">По цене</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Список объявлений -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="loading" class="p-8 text-center">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
        </div>
        <div v-else-if="advertisements.length === 0" class="p-8 text-center text-gray-500">
          Нет объявлений для отображения
        </div>
        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="advertisement in advertisements"
            :key="advertisement.id"
            class="p-6 hover:bg-gray-50"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                  {{ advertisement.title }}
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                  {{ advertisement.description }}
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                  <span>Категория: {{ advertisement.category?.name }}</span>
                  <span>Цена: {{ advertisement.price }} ₽</span>
                  <span>Автор: {{ advertisement.user?.name }}</span>
                  <span>Создано: {{ formatDate(advertisement.created_at) }}</span>
                </div>
              </div>
              <div class="ml-6 flex items-center space-x-4">
                <button
                  v-if="advertisement.status === 'pending'"
                  @click="approveAdvertisement(advertisement.id)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Одобрить
                </button>
                <button
                  v-if="advertisement.status === 'pending'"
                  @click="rejectAdvertisement(advertisement.id)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  Отклонить
                </button>
                <router-link
                  :to="`/advertisements/${advertisement.id}`"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Просмотр
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Пагинация -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Назад
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Вперед
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Показано
                <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                -
                <span class="font-medium">{{ Math.min(currentPage * perPage, total) }}</span>
                из
                <span class="font-medium">{{ total }}</span>
                результатов
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="prevPage"
                  :disabled="currentPage === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  Назад
                </button>
                <button
                  v-for="page in displayedPages"
                  :key="page"
                  @click="currentPage = page"
                  :class="[
                    currentPage === page
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                  ]"
                >
                  {{ page }}
                </button>
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                >
                  Вперед
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useNotification } from '@/composables/useNotification'

interface Category {
  id: number
  name: string
}

interface User {
  id: number
  name: string
}

interface Advertisement {
  id: number
  title: string
  description: string
  price: number
  status: 'pending' | 'approved' | 'rejected'
  created_at: string
  category?: Category
  user?: User
}

const notification = useNotification()
const loading = ref(false)
const status = ref('pending')
const categoryId = ref('')
const sortBy = ref('created_at')
const advertisements = ref<Advertisement[]>([])
const categories = ref<Category[]>([])
const currentPage = ref(1)
const perPage = 10
const total = ref(0)

const totalPages = computed(() => Math.ceil(total.value / perPage))

const displayedPages = computed(() => {
  const pages = []
  const maxPages = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  let end = Math.min(totalPages.value, start + maxPages - 1)

  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

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

// Загрузка объявлений
const loadAdvertisements = async () => {
  try {
    loading.value = true
    const params = new URLSearchParams({
      status: status.value,
      category_id: categoryId.value,
      sort_by: sortBy.value,
      page: currentPage.value.toString(),
      per_page: perPage.toString()
    })

    const response = await fetch(`api/admin/advertisements?${params}`)
    if (!response.ok) throw new Error('Failed to load advertisements')
    const data = await response.json()

    advertisements.value = data.data
    total.value = data.total
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить объявления')
    console.error('Error loading advertisements:', error)
  } finally {
    loading.value = false
  }
}

// Одобрение объявления
const approveAdvertisement = async (id: number) => {
  try {
    const response = await fetch(`api/admin/advertisements/${id}/approve`, {
      method: 'POST'
    })
    if (!response.ok) throw new Error('Failed to approve advertisement')

    notification.success('Успех', 'Объявление одобрено')
    loadAdvertisements()
  } catch (error) {
    notification.error('Ошибка', 'Не удалось одобрить объявление')
    console.error('Error approving advertisement:', error)
  }
}

// Отклонение объявления
const rejectAdvertisement = async (id: number) => {
  try {
    const response = await fetch(`api/admin/advertisements/${id}/reject`, {
      method: 'POST'
    })
    if (!response.ok) throw new Error('Failed to reject advertisement')

    notification.success('Успех', 'Объявление отклонено')
    loadAdvertisements()
  } catch (error) {
    notification.error('Ошибка', 'Не удалось отклонить объявление')
    console.error('Error rejecting advertisement:', error)
  }
}

// Форматирование даты
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Навигация по страницам
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

// Следим за изменениями фильтров и страницы
watch([status, categoryId, sortBy, currentPage], () => {
  loadAdvertisements()
})

onMounted(() => {
  loadCategories()
  loadAdvertisements()
})
</script>
