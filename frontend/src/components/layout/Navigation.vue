<template>
  <nav class="bg-white shadow">
    <div class="container mx-auto px-4">
      <div class="flex justify-between h-16">
        <div class="flex">
          <!-- ... existing menu items ... -->
          <router-link
            v-if="isAdmin"
            to="/categories"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
            :class="{ 'text-blue-600': isActive('/categories') }"
          >
            Управление категориями
          </router-link>
          <router-link
            v-if="isAdmin"
            to="/statistics"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
            :class="{ 'text-blue-600': isActive('/statistics') }"
          >
            Статистика
          </router-link>
          <router-link
            v-if="isAdmin"
            to="/moderation"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
            :class="{ 'text-blue-600': isActive('/moderation') }"
          >
            Модерация
          </router-link>
          <router-link
            v-if="isAuthenticated"
            to="/notifications"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
            :class="{ 'text-blue-600': isActive('/notifications') }"
          >
            Уведомления
            <span
              v-if="unreadCount > 0"
              class="ml-2 px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full"
            >
              {{ unreadCount }}
            </span>
          </router-link>
          <!-- ... existing code ... -->
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const authStore = useAuthStore()
const unreadCount = ref(0)

const isAdmin = computed(() => authStore.user?.is_admin)
const isAuthenticated = computed(() => authStore.isAuthenticated)

const isActive = (path: string) => {
  return route.path === path
}

// Загрузка количества непрочитанных уведомлений
const loadUnreadCount = async () => {
  if (!isAuthenticated.value) return

  try {
    const response = await fetch(`api/notifications/unread-count`)
    if (!response.ok) throw new Error('Failed to load unread count')
    const data = await response.json()
    unreadCount.value = data.count
  } catch (error) {
    console.error('Error loading unread count:', error)
  }
}

onMounted(() => {
  loadUnreadCount()
})
</script>
