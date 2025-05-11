<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="p-4 border-b">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                <span class="text-xl font-bold text-gray-600">
                  {{ advertisement?.user?.name?.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <h2 class="text-xl font-semibold">{{ advertisement?.title }}</h2>
                <p class="text-sm text-gray-500">{{ advertisement?.user?.name }}</p>
              </div>
            </div>
            <button
              @click="router.back()"
              class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Messages -->
        <div
          ref="messagesContainer"
          class="h-[calc(100vh-16rem)] overflow-y-auto p-4 space-y-4"
        >
          <AnimatedTransition v-if="loading">
            <div class="space-y-4">
              <div v-for="i in 3" :key="i" class="flex items-start space-x-4">
                <SkeletonLoader type="circle" :size="40" />
                <div class="flex-1 space-y-2">
                  <SkeletonLoader type="text" />
                  <SkeletonLoader type="text" />
                </div>
              </div>
            </div>
          </AnimatedTransition>

          <template v-else>
            <AnimatedTransition v-for="message in messages" :key="message.id">
              <div
                :class="[
                  'flex',
                  message.user_id === currentUserId ? 'justify-end' : 'justify-start'
                ]"
              >
                <div
                  :class="[
                    'max-w-[70%] rounded-lg p-3',
                    message.user_id === currentUserId
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-900'
                  ]"
                >
                  <p class="text-sm">{{ message.content }}</p>
                  <p
                    :class="[
                      'text-xs mt-1',
                      message.user_id === currentUserId ? 'text-blue-100' : 'text-gray-500'
                    ]"
                  >
                    {{ formatDate(message.created_at) }}
                  </p>
                </div>
              </div>
            </AnimatedTransition>
          </template>
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t">
          <form @submit.prevent="sendMessage" class="flex space-x-4">
            <input
              v-model="newMessage"
              type="text"
              placeholder="Введите сообщение..."
              class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :disabled="sendingMessage"
            />
            <button
              type="submit"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="!newMessage.trim() || sendingMessage"
            >
              <span v-if="sendingMessage">Отправка...</span>
              <span v-else>Отправить</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useNotification } from '@/composables/useNotification'
import { useAuthStore } from '@/stores/auth'
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
  user: User
}

interface Message {
  id: number
  content: string
  created_at: string
  user_id: number
}

const route = useRoute()
const router = useRouter()
const notification = useNotification()
const authStore = useAuthStore()

const loading = ref(true)
const sendingMessage = ref(false)
const messages = ref<Message[]>([])
const advertisement = ref<Advertisement | null>(null)
const newMessage = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

const currentUserId = authStore.user?.id

const fetchMessages = async () => {
  try {
    const response = await fetch(`api/messages/${route.params.id}`)
    if (!response.ok) throw new Error('Failed to fetch messages')
    const data = await response.json()
    messages.value = data.messages
    advertisement.value = data.advertisement
  } catch (error) {
    notification.error('Ошибка загрузки', 'Не удалось загрузить сообщения')
    console.error('Error fetching messages:', error)
  } finally {
    loading.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || sendingMessage.value) return

  sendingMessage.value = true
  try {
    const response = await fetch(`api/messages/${route.params.id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ content: newMessage.value })
    })

    if (!response.ok) throw new Error('Failed to send message')

    const message = await response.json()
    messages.value.push(message)
    newMessage.value = ''
    await nextTick()
    scrollToBottom()
  } catch (error) {
    notification.error('Ошибка отправки', 'Не удалось отправить сообщение')
    console.error('Error sending message:', error)
  } finally {
    sendingMessage.value = false
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('ru-RU', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

onMounted(() => {
  fetchMessages()
})
</script>
