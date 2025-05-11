<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Избранные объявления</h1>
      <p class="mt-2 text-sm text-gray-600">
        Здесь вы можете просматривать и управлять избранными объявлениями
      </p>
    </div>

    <div class="bg-white shadow rounded-lg">
      <div class="p-6">
        <div class="flex justify-between items-center mb-6">
          <div class="flex space-x-4">
            <select
              v-model="sortBy"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            >
              <option value="date">По дате</option>
              <option value="price">По цене</option>
            </select>
            <select
              v-model="sortOrder"
              class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
            >
              <option value="desc">По убыванию</option>
              <option value="asc">По возрастанию</option>
            </select>
          </div>
        </div>

        <div v-if="isLoading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>

        <div v-else-if="favorites.length === 0" class="text-center py-12">
          <svg
            class="mx-auto h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Нет избранных объявлений</h3>
          <p class="mt-1 text-sm text-gray-500">
            Начните добавлять объявления в избранное, чтобы они появились здесь.
          </p>
          <div class="mt-6">
            <router-link
              to="/"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Перейти к объявлениям
            </router-link>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="favorite in favorites"
            :key="favorite.id"
            class="bg-white overflow-hidden shadow rounded-lg"
          >
            <div class="relative pb-48">
              <img
                :src="favorite.image"
                :alt="favorite.title"
                class="absolute h-full w-full object-cover"
              />
            </div>
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">{{ favorite.title }}</h3>
              <p class="mt-2 text-sm text-gray-500">{{ favorite.description }}</p>
              <div class="mt-4 flex justify-between items-center">
                <span class="text-lg font-medium text-gray-900">{{ favorite.price }} ₽</span>
                <button
                  @click="removeFromFavorites(favorite.id)"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  Удалить из избранного
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { advertisementService } from '@/services/advertisement'

interface FavoriteItem {
  id: number
  title: string
  description: string
  price: number
  image: string
}

const favorites = ref<FavoriteItem[]>([])
const isLoading = ref(true)
const sortBy = ref('date')
const sortOrder = ref('desc')

onMounted(async () => {
  try {
    const response = await advertisementService.getFavorites()
    favorites.value = response.data
  } catch (error) {
    console.error('Error loading favorites:', error)
  } finally {
    isLoading.value = false
  }
})

const removeFromFavorites = async (id: number) => {
  try {
    await advertisementService.removeFromFavorites(id)
    favorites.value = favorites.value.filter(item => item.id !== id)
    console.log('Favorite removed:', id)
  } catch (error) {
    console.error('Error removing favorite:', error)
  }
}
</script>
