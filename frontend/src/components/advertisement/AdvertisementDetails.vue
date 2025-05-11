<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else-if="localAdvertisement" class="max-w-4xl mx-auto">
      <!-- Заголовок и действия -->
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-3xl font-bold mb-2">
            {{ localAdvertisement.title }}
            <span
              v-if="localAdvertisement.is_vip"
              class="ml-2 px-2 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded"
            >
              VIP
            </span>
          </h1>
          <div class="flex items-center space-x-4 text-sm text-gray-500">
            <span>{{ localAdvertisement.category.name }}</span>
            <span>{{ localAdvertisement.address }}</span>
            <span>{{ formatPrice(localAdvertisement.price) }} ₽</span>
          </div>
        </div>
        <div class="flex space-x-2">
          <button
            @click="toggleFavorite"
            class="p-2 text-gray-400 hover:text-red-500 transition-colors duration-200"
            :class="{ 'text-red-500': localAdvertisement.is_favorite }"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
              />
            </svg>
          </button>
          <button
            v-if="canEdit"
            @click="handleEdit"
            class="p-2 text-gray-400 hover:text-blue-500 transition-colors duration-200"
          >
            <svg
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
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              />
            </svg>
          </button>
          <button
            v-if="canEdit"
            @click="handleDelete"
            class="p-2 text-gray-400 hover:text-red-500 transition-colors duration-200"
          >
            <svg
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
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Галерея изображений -->
      <div class="mb-8">
        <div class="relative aspect-video rounded-lg overflow-hidden">
          <img
            v-if="currentImage"
            :src="currentImage.path"
            :alt="localAdvertisement.title"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400"
          >
            Нет изображения
          </div>

          <!-- Навигация по изображениям -->
          <button
            v-if="localAdvertisement.images.length > 1"
            @click="previousImage"
            class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75 transition-opacity duration-200"
          >
            <svg
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
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <button
            v-if="localAdvertisement.images.length > 1"
            @click="nextImage"
            class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75 transition-opacity duration-200"
          >
            <svg
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
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>

        <!-- Миниатюры -->
        <div
          v-if="localAdvertisement.images.length > 1"
          class="grid grid-cols-6 gap-2 mt-2"
        >
          <button
            v-for="(image, index) in localAdvertisement.images"
            :key="index"
            @click="currentImageIndex = index"
            class="aspect-square rounded-lg overflow-hidden"
            :class="{
              'ring-2 ring-blue-500': currentImageIndex === index
            }"
          >
            <img
              :src="image.path"
              :alt="`${localAdvertisement.title} - изображение ${index + 1}`"
              class="w-full h-full object-cover"
            />
          </button>
        </div>
      </div>

      <!-- Описание -->
      <div class="prose max-w-none mb-8">
        <h2 class="text-xl font-semibold mb-4">Описание</h2>
        <p class="text-gray-600 whitespace-pre-line">{{ localAdvertisement.description }}</p>
      </div>

      <!-- Рейтинг -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Рейтинг</h2>
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <div class="flex">
              <svg
                v-for="i in 5"
                :key="i"
                class="h-6 w-6"
                :class="i <= localAdvertisement.rating ? 'text-yellow-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </div>
            <span class="ml-2 text-sm text-gray-500">
              {{ localAdvertisement.rating.toFixed(1) }} ({{ localAdvertisement.ratings_count }} оценок)
            </span>
          </div>
          <button
            v-if="!hasRated"
            @click="showRatingModal = true"
            class="text-sm text-blue-600 hover:text-blue-700"
          >
            Оценить
          </button>
        </div>
      </div>

      <!-- Карта -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Местоположение</h2>
        <div class="h-[400px] rounded-lg overflow-hidden">
          <AdvertisementMap
            :advertisements="[localAdvertisement]"
            :center="{ lat: localAdvertisement.latitude, lng: localAdvertisement.longitude }"
            :zoom="15"
          />
        </div>
      </div>

      <!-- Информация о продавце -->
      <div>
        <h2 class="text-xl font-semibold mb-4">Информация о продавце</h2>
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
              <span class="text-xl font-semibold text-gray-600">
                {{ localAdvertisement.user.name[0] }}
              </span>
            </div>
            <div>
              <h3 class="font-semibold">{{ localAdvertisement.user.name }}</h3>
              <p class="text-sm text-gray-500">
                На сайте с {{ formatDate(localAdvertisement.user.created_at) }}
              </p>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="text-gray-500">Рейтинг продавца:</span>
              <span class="ml-2 font-medium">{{ localAdvertisement.user.rating.toFixed(1) }}</span>
            </div>
            <div>
              <span class="text-gray-500">Объявлений:</span>
              <span class="ml-2 font-medium">{{ localAdvertisement.user.advertisements_count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно оценки -->
    <div
      v-if="showRatingModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-xl font-semibold mb-4">Оцените объявление</h3>
        <div class="flex justify-center mb-6">
          <div class="flex space-x-2">
            <button
              v-for="i in 5"
              :key="i"
              @click="rating = i"
              class="p-2"
            >
              <svg
                class="h-8 w-8"
                :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                />
              </svg>
            </button>
          </div>
        </div>
        <div class="flex justify-end space-x-2">
          <button
            @click="showRatingModal = false"
            class="px-4 py-2 text-gray-600 hover:text-gray-700"
          >
            Отмена
          </button>
          <button
            @click="submitRating"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
            :disabled="!rating"
          >
            Оценить
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useNotification } from '@/composables/useNotification'
import { useAuthStore } from '@/stores/auth'
import AdvertisementMap from './AdvertisementMap.vue'
import type { Advertisement } from '@/types/advertisement'

const props = defineProps<{
  advertisement: Advertisement
}>()

const emit = defineEmits<{
  (e: 'update:advertisement', value: Advertisement): void
}>()

const router = useRouter()
const route = useRoute()
const notification = useNotification()
const authStore = useAuthStore()

const loading = ref(true)
const currentImageIndex = ref(0)
const showRatingModal = ref(false)
const rating = ref(0)
const hasRated = ref(false)

const localAdvertisement = ref<Advertisement>(props.advertisement)

const currentImage = computed(() => {
  return localAdvertisement.value.images[currentImageIndex.value]
})

const canEdit = computed(() => {
  if (!authStore.user) return false
  return localAdvertisement.value.user.id === authStore.user.id
})

// Загрузка объявления
const loadAdvertisement = async () => {
  try {
    const response = await fetch(`api/advertisements/${route.params.id}`)
    if (!response.ok) throw new Error('Failed to load advertisement')
    localAdvertisement.value = await response.json()
    emit('update:advertisement', localAdvertisement.value)
  } catch (error) {
    notification.error('Ошибка', 'Не удалось загрузить объявление')
    console.error('Error loading advertisement:', error)
    router.push('/advertisements')
  } finally {
    loading.value = false
  }
}

// Переключение избранного
const toggleFavorite = async () => {
  try {
    const response = await fetch(`api/advertisements/${localAdvertisement.value.id}/favorite`, {
      method: localAdvertisement.value.is_favorite ? 'DELETE' : 'POST'
    })

    if (!response.ok) throw new Error('Failed to toggle favorite')

    const updatedAdvertisement = {
      ...localAdvertisement.value,
      is_favorite: !localAdvertisement.value.is_favorite
    }
    emit('update:advertisement', updatedAdvertisement)
  } catch (error) {
    notification.error('Ошибка', 'Не удалось обновить избранное')
    console.error('Error toggling favorite:', error)
  }
}

// Редактирование объявления
const handleEdit = () => {
  router.push(`/advertisements/${localAdvertisement.value.id}/edit`)
}

// Удаление объявления
const handleDelete = async () => {
  if (!confirm('Вы уверены, что хотите удалить это объявление?')) return

  try {
    const response = await fetch(`api/advertisements/${localAdvertisement.value.id}`, {
      method: 'DELETE'
    })

    if (!response.ok) throw new Error('Failed to delete advertisement')

    notification.success('Успешно', 'Объявление удалено')
    router.push('/advertisements')
  } catch (error) {
    notification.error('Ошибка', 'Не удалось удалить объявление')
    console.error('Error deleting advertisement:', error)
  }
}

// Навигация по изображениям
const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  } else {
    currentImageIndex.value = localAdvertisement.value.images.length - 1
  }
}

const nextImage = () => {
  if (currentImageIndex.value < localAdvertisement.value.images.length - 1) {
    currentImageIndex.value++
  } else {
    currentImageIndex.value = 0
  }
}

// Оценка объявления
const submitRating = async () => {
  if (!rating.value) return

  try {
    const response = await fetch(`api/advertisements/${localAdvertisement.value.id}/rate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ rating: rating.value })
    })

    if (!response.ok) throw new Error('Failed to submit rating')

    const data = await response.json()
    const updatedAdvertisement = {
      ...localAdvertisement.value,
      rating: data.rating,
      ratings_count: data.ratings_count
    }
    emit('update:advertisement', updatedAdvertisement)
    hasRated.value = true
    showRatingModal.value = false
  } catch (error) {
    notification.error('Ошибка', 'Не удалось сохранить оценку')
    console.error('Error submitting rating:', error)
  }
}

// Форматирование цены
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

// Форматирование даты
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU')
}

onMounted(() => {
  loadAdvertisement()
})
</script>

<style>
  /* Add your styles here */
</style>
