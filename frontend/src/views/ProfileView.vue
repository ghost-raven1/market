<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Профиль пользователя
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Личная информация и настройки аккаунта
          </p>
        </div>
        <div class="border-t border-gray-200">
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Полное имя
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ user?.name }}
              </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Email
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ user?.email }}
              </dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Телефон
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ user?.phone || 'Не указан' }}
              </dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">
                Дата регистрации
              </dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {{ formatDate(user?.created_at || '') }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Мои объявления
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Список ваших активных объявлений
          </p>
        </div>
        <div class="border-t border-gray-200">
          <div class="bg-white px-4 py-5 sm:p-6">
            <div v-if="isLoading" class="text-center py-4">
              <svg class="animate-spin h-5 w-5 text-blue-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <div v-else-if="userAds.length === 0" class="text-center py-4 text-gray-500">
              У вас пока нет объявлений
            </div>
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="ad in userAds"
                :key="ad.id"
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
              >
                <div class="flex-1 min-w-0">
                  <router-link
                    :to="'/advertisements/' + ad.id"
                    class="focus:outline-none"
                  >
                    <span
                      class="absolute inset-0"
                      aria-hidden="true"
                    />
                    <p class="text-sm font-medium text-gray-900">
                      {{ ad.title }}
                    </p>
                    <p class="text-sm text-gray-500 truncate">
                      {{ ad.price }} ₽
                    </p>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Настройки безопасности
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Управление паролем и безопасностью аккаунта
          </p>
        </div>
        <div class="border-t border-gray-200">
          <div class="bg-white px-4 py-5 sm:p-6">
            <div class="space-y-6">
              <div>
                <button
                  type="button"
                  @click="handleChangePassword"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Изменить пароль
                </button>
              </div>
              <div>
                <button
                  type="button"
                  @click="handleDeleteAccount"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  Удалить аккаунт
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
import { authService, User } from '@/services/auth'
import { advertisementService, Advertisement } from '@/services/advertisements'

const user = ref<User | null>(null)
const userAds = ref<Advertisement[]>([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    const response = await authService.getCurrentUser()
    user.value = response.data
    userAds.value = await advertisementService.getUserAdvertisements()
  } catch (error) {
    console.error('Error loading profile data:', error)
  } finally {
    isLoading.value = false
  }
})

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleChangePassword = async () => {
  console.log('Change password')
}

const handleDeleteAccount = async () => {
  if (confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие нельзя отменить.')) {
    try {
      await authService.deleteAccount()
      console.log('Account deleted')
      window.location.href = '/'
    } catch (error) {
      console.error('Error deleting account:', error)
    }
  }
}
</script> 