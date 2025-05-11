<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Уведомления</h1>
        <button
          v-if="notifications.length"
          @click="markAllAsRead"
          class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900"
        >
          Отметить все как прочитанные
        </button>
      </div>

      <div class="bg-white rounded-lg shadow">
        <div v-if="loading" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        <div v-else-if="!notifications.length" class="p-8 text-center text-gray-500">
          У вас пока нет уведомлений
        </div>
        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="p-4 hover:bg-gray-50 transition-colors duration-200"
            :class="{ 'bg-blue-50': !notification?.read_at }"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="text-sm text-gray-900">{{ notification.message }}</p>
                <p class="mt-1 text-xs text-gray-500">
                  {{ formatDate(notification?.created_at) }}
                </p>
              </div>
              <div class="ml-4 flex-shrink-0">
                <button
                  v-if="!notification?.read_at"
                  @click="markAsRead(notification.id)"
                  class="text-sm text-blue-600 hover:text-blue-800"
                >
                  Отметить как прочитанное
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
import { onMounted } from 'vue'
import { useNotifications } from '@/composables/useNotifications'

const { notifications, loading, loadNotifications, markAsRead, markAllAsRead } = useNotifications()

// Форматирование даты
const formatDate = (date: string) => {
  return new Date(date).toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadNotifications()
})
</script> 