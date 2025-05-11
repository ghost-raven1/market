<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-2xl font-bold mb-8">Объявления</h1>

      <!-- Расширенные фильтры -->
      <AdvancedFilters
        @update:filters="handleFiltersUpdate"
      />

      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Объявления</h1>
        <div class="flex items-center space-x-4">
          <button
            @click="viewMode = viewMode === 'list' ? 'map' : 'list'"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
            :class="{ 'bg-gray-100': viewMode === 'map' }"
          >
            <svg
              v-if="viewMode === 'list'"
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 10h16M4 14h16M4 18h16"
              />
            </svg>
          </button>
          <button
            v-if="isAuthenticated"
            @click="router.push('/advertisements/create')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
          >
            Создать объявление
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Поиск по названию..."
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
            <select
              v-model="filters.category_id"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Все категории</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input
              v-model.number="filters.min_price"
              type="number"
              placeholder="От"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <input
              v-model.number="filters.max_price"
              type="number"
              placeholder="До"
              class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div class="flex items-center">
            <input
              id="vip"
              v-model="filters.vip_only"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label for="vip" class="ml-2 text-sm text-gray-700">Только VIP</label>
          </div>
        </div>
      </div>

      <!-- Map View -->
      <div v-if="viewMode === 'map'" class="mb-6">
        <AdvertisementMap :advertisements="advertisements" :center="{ lat: 55.7558, lng: 37.6173 }" :zoom="10" />
      </div>

      <!-- List View -->
      <AnimatedTransition v-else>
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="i in 6" :key="i" class="bg-white rounded-lg shadow overflow-hidden">
            <SkeletonLoader type="image" :size="200" />
            <div class="p-4 space-y-2">
              <SkeletonLoader type="text" />
              <SkeletonLoader type="text" />
              <SkeletonLoader type="button" />
            </div>
          </div>
        </div>

        <div v-else-if="advertisements.length === 0" class="text-center py-12">
          <p class="text-gray-500">Объявления не найдены</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <AnimatedTransition v-for="ad in advertisements" :key="ad.id">
            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition-shadow duration-200">
              <div class="relative">
                <img
                  :src="ad.images[0]?.path || '/placeholder.jpg'"
                  :alt="ad.title"
                  class="w-full h-48 object-cover"
                />
                <div
                  v-if="ad.is_vip"
                  class="absolute top-2 right-2 bg-yellow-400 text-black px-2 py-1 rounded text-sm font-medium"
                >
                  VIP
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-semibold text-lg mb-2">{{ ad.title }}</h3>
                <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ ad.description }}</p>
                <div class="flex justify-between items-center">
                  <span class="text-lg font-bold text-blue-600">{{ formatPrice(ad.price) }} ₽</span>
                  <button
                    @click="router.push(`/advertisements/${ad.id}`)"
                    class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                  >
                    Подробнее
                  </button>
                </div>
              </div>
            </div>
          </AnimatedTransition>
        </div>
      </AnimatedTransition>

      <!-- Load More Button -->
      <div v-if="hasMore && viewMode === 'list'" class="mt-8 text-center">
        <button
          @click="loadMore"
          class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200"
          :disabled="loadingMore"
        >
          <span v-if="loadingMore">Загрузка...</span>
          <span v-else>Загрузить еще</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import AnimatedTransition from '@/components/common/AnimatedTransition.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'
import AdvertisementMap from '@/components/advertisement/AdvertisementMap.vue'
import AdvancedFilters from './AdvancedFilters.vue'
import type { Advertisement, Category } from '@/types/advertisement'

interface Filters {
  search: string
  category_id: number | null
  min_price: number | null
  max_price: number | null
  vip_only: boolean
}

const router = useRouter()
const authStore = useAuthStore()
const notification = useNotification()

const loading = ref(true)
const loadingMore = ref(false)
const advertisements = ref<Advertisement[]>([])
const categories = ref<Category[]>([])
const filters = ref<Filters>({
  search: '',
  category_id: null,
  min_price: null,
  max_price: null,
  vip_only: false
})
const cursor = ref<string | null>(null)
const hasMore = ref(true)
const viewMode = ref<'list' | 'map'>('list')
const currentPage = ref(1)
const perPage = ref(10)
const total = ref(0)
const error = ref<string | null>(null)

const isAuthenticated = computed(() => authStore.isAuthenticated)

const fetchAdvertisements = async (reset = true) => {
  try {
    if (reset) {
      loading.value = true
      cursor.value = null
      advertisements.value = []
    } else {
      loadingMore.value = true
    }

    const params = new URLSearchParams({
      ...(cursor.value && { cursor: cursor.value }),
      ...(filters.value.search && { search: filters.value.search }),
      ...(filters.value.category_id && { category_id: filters.value.category_id.toString() }),
      ...(filters.value.min_price && { min_price: filters.value.min_price.toString() }),
      ...(filters.value.max_price && { max_price: filters.value.max_price.toString() }),
      ...(filters.value.vip_only && { vip_only: '1' })
    })

    const response = await fetch(`api/advertisements?${params}`)
    if (!response.ok) throw new Error('Failed to fetch advertisements')

    const data = await response.json()
    if (reset) {
      advertisements.value = data.data
    } else {
      advertisements.value.push(...data.data)
    }

    cursor.value = data.next_cursor
    hasMore.value = !!data.next_cursor
  } catch (error) {
    notification.error('Ошибка загрузки', 'Не удалось загрузить объявления')
    console.error('Error fetching advertisements:', error)
  } finally {
    loading.value = false
    loadingMore.value = false
  }
}

const loadMore = () => {
  if (!loadingMore.value && hasMore.value) {
    fetchAdvertisements(false)
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

watch(filters, () => {
  fetchAdvertisements(true)
}, { deep: true })

onMounted(async () => {
  try {
    const response = await fetch(`api/advertisements`)
    if (!response.ok) throw new Error('Failed to load advertisements')
    advertisements.value = await response.json()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
})

// Обработка обновления фильтров
const handleFiltersUpdate = (newFilters: any) => {
  // Обновляем параметры запроса
  const params = new URLSearchParams({
    ...newFilters,
    page: currentPage.value.toString(),
    per_page: perPage.toString()
  })

  // Загружаем объявления с новыми фильтрами
  loadAdvertisements(params)
}

// Загрузка объявлений
const loadAdvertisements = async (params?: URLSearchParams) => {
  try {
    loading.value = true
    const queryParams = params || new URLSearchParams({
      page: currentPage.value.toString(),
      per_page: perPage.toString()
    })

    const response = await fetch(`api/advertisements?${queryParams}`)
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
</script>
