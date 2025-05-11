<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Сообщения</h1>

    <AnimatedTransition>
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow p-4">
          <div class="flex items-center space-x-4">
            <SkeletonLoader type="circle" :size="48" />
            <div class="flex-1 space-y-2">
              <SkeletonLoader type="text" />
              <SkeletonLoader type="text" />
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="messages.length === 0" class="text-center py-8">
        <p class="text-gray-500">У вас пока нет сообщений</p>
      </div>

      <div v-else class="space-y-4">
        <AnimatedTransition v-for="message in messages" :key="message.id">
          <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                  <span class="text-xl font-bold text-gray-600">
                    {{ message.user.name.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div>
                  <h3 class="font-semibold">{{ message.advertisement.title }}</h3>
                  <p class="text-sm text-gray-500">{{ message.user.name }}</p>
                </div>
              </div>
              <button
                @click="openChat(message.id)"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200"
              >
                Открыть чат
              </button>
            </div>
            <p class="mt-2 text-gray-600 line-clamp-2">{{ message.content }}</p>
            <p class="mt-2 text-sm text-gray-400">{{ formatDate(message.created_at) }}</p>
          </div>
        </AnimatedTransition>
      </div>
    </AnimatedTransition>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useNotification } from '@/composables/useNotification'
import AnimatedTransition from '@/components/common/AnimatedTransition.vue'
import SkeletonLoader from '@/components/common/SkeletonLoader.vue'

interface User {
  id: number
  name: string
  email: string
}

interface Advertisement {
  id: number
  title: string
}

interface Message {
  id: number
  content: string
  created_at: string
  user: User
  advertisement: Advertisement
}

const router = useRouter()
const notification = useNotification()
const loading = ref(true)
const messages = ref<Message[]>([])

const fetchMessages = async () => {
  try {
    const response = await fetch(`api/messages`)
    if (!response.ok) throw new Error('Failed to fetch messages')
    messages.value = await response.json()
  } catch (error) {
    notification.error('Ошибка загрузки', 'Не удалось загрузить сообщения')
    console.error('Error fetching messages:', error)
  } finally {
    loading.value = false
  }
}

const openChat = (messageId: number) => {
  router.push(`/messages/${messageId}`)
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('ru-RU', {
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

onMounted(fetchMessages)
</script>
