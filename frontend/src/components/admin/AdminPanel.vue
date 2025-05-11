<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Админ-панель</h1>

    <!-- Loading Overlay -->
    <div v-if="loading" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white shadow rounded-lg">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="currentTab = tab.id"
            :class="[
              currentTab === tab.id
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <div class="p-6">
        <UserManagement v-if="currentTab === 'users'" />
        <CategoryManagement v-if="currentTab === 'categories'" />
      </div>
    </div>

    <!-- Advertisements Tab -->
    <div v-if="currentTab === 'advertisements'" class="space-y-6">
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div v-if="loading" class="p-4 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Загрузка объявлений...</p>
        </div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Название
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Пользователь
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Статус
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                VIP
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Действия
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ad in advertisements" :key="ad.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ ad.title }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ ad.user.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': ad.status === 'active',
                    'bg-yellow-100 text-yellow-800': ad.status === 'inactive',
                    'bg-red-100 text-red-800': ad.status === 'blocked'
                  }"
                >
                  {{ ad.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <button
                  @click="toggleVip(ad)"
                  class="text-sm font-medium"
                  :class="ad.is_vip ? 'text-indigo-600' : 'text-gray-500'"
                >
                  {{ ad.is_vip ? 'VIP' : 'Обычное' }}
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="toggleStatus(ad)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  {{ ad.status === 'active' ? 'Заблокировать' : 'Разблокировать' }}
                </button>
                <button
                  @click="deleteAdvertisement(ad)"
                  class="text-red-600 hover:text-red-900"
                >
                  Удалить
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Confirm Modal -->
    <ConfirmModal
      :is-visible="showConfirmModal"
      :title="confirmModal.title"
      :message="confirmModal.message"
      :type="confirmModal.type"
      :confirm-text="confirmModal.confirmText"
      :loading="loading"
      @confirm="handleConfirm"
      @cancel="showConfirmModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'
import UserManagement from './UserManagement.vue'
import CategoryManagement from './CategoryManagement.vue'
import ConfirmModal from '../common/ConfirmModal.vue'

interface User {
  id: number
  name: string
}

interface Advertisement {
  id: number
  title: string
  status: 'active' | 'inactive' | 'blocked'
  is_vip: boolean
  user: User
}

const tabs = [
  { id: 'advertisements', name: 'Объявления' },
  { id: 'users', name: 'Пользователи' },
  { id: 'categories', name: 'Категории' }
]

const currentTab = ref('advertisements')
const advertisements = ref<Advertisement[]>([])
const loading = ref(false)
const actionLoading = ref(false)
const notification = useNotification()

const showConfirmModal = ref(false)
const confirmModal = ref({
  title: '',
  message: '',
  type: 'warning' as 'warning' | 'error' | 'info' | 'success',
  confirmText: 'Подтвердить',
  action: null as (() => Promise<void>) | null
})

const fetchAdvertisements = async () => {
  try {
    loading.value = true
    const response = await axios.get(`api/advertisements`)
    advertisements.value = response.data.data
  } catch (err) {
    console.error('Error fetching advertisements:', err)
    notification.error(
      'Ошибка загрузки',
      'Не удалось загрузить список объявлений'
    )
  } finally {
    loading.value = false
  }
}

const showConfirm = (options: {
  title: string
  message: string
  type?: 'warning' | 'error' | 'info' | 'success'
  confirmText?: string
  action: () => Promise<void>
}) => {
  confirmModal.value = {
    title: options.title,
    message: options.message,
    type: options.type || 'warning',
    confirmText: options.confirmText || 'Подтвердить',
    action: options.action
  }
  showConfirmModal.value = true
}

const handleConfirm = async () => {
  if (confirmModal.value.action) {
    try {
      actionLoading.value = true
      await confirmModal.value.action()
    } catch (err) {
      console.error('Error performing action:', err)
    } finally {
      actionLoading.value = false
      showConfirmModal.value = false
    }
  }
}

const toggleVip = async (ad: Advertisement) => {
  showConfirm({
    title: ad.is_vip ? 'Отменить VIP-статус' : 'Установить VIP-статус',
    message: `Вы уверены, что хотите ${ad.is_vip ? 'отменить' : 'установить'} VIP-статус для объявления "${ad.title}"?`,
    type: 'info',
    confirmText: ad.is_vip ? 'Отменить VIP' : 'Установить VIP',
    action: async () => {
      try {
        actionLoading.value = true
        await axios.patch(`api/advertisements/${ad.id}`, {
          is_vip: !ad.is_vip
        })
        ad.is_vip = !ad.is_vip
        notification.success(
          'Статус обновлен',
          `VIP-статус ${ad.is_vip ? 'установлен' : 'отменен'} для объявления "${ad.title}"`
        )
      } catch (error) {
        notification.error(
          'Ошибка',
          'Не удалось обновить VIP-статус объявления'
        )
        throw error
      }
    }
  })
}

const toggleStatus = async (ad: Advertisement) => {
  const newStatus = ad.status === 'active' ? 'blocked' : 'active'
  showConfirm({
    title: newStatus === 'active' ? 'Разблокировать объявление' : 'Заблокировать объявление',
    message: `Вы уверены, что хотите ${newStatus === 'active' ? 'разблокировать' : 'заблокировать'} объявление "${ad.title}"?`,
    type: newStatus === 'active' ? 'info' : 'warning',
    confirmText: newStatus === 'active' ? 'Разблокировать' : 'Заблокировать',
    action: async () => {
      try {
        actionLoading.value = true
        await axios.patch(`api/advertisements/${ad.id}`, {
          status: newStatus
        })
        ad.status = newStatus
        notification.success(
          'Статус обновлен',
          `Объявление "${ad.title}" ${newStatus === 'active' ? 'разблокировано' : 'заблокировано'}`
        )
      } catch (error) {
        notification.error(
          'Ошибка',
          'Не удалось обновить статус объявления'
        )
        throw error
      }
    }
  })
}

const deleteAdvertisement = async (ad: Advertisement) => {
  showConfirm({
    title: 'Удаление объявления',
    message: `Вы уверены, что хотите удалить объявление "${ad.title}"? Это действие нельзя будет отменить.`,
    type: 'error',
    confirmText: 'Удалить',
    action: async () => {
      try {
        actionLoading.value = true
        await axios.delete(`api/advertisements/${ad.id}`)
        advertisements.value = advertisements.value.filter(a => a.id !== ad.id)
        notification.success(
          'Объявление удалено',
          `Объявление "${ad.title}" успешно удалено`
        )
      } catch (error) {
        notification.error(
          'Ошибка',
          'Не удалось удалить объявление'
        )
        throw error
      }
    }
  })
}

onMounted(() => {
  fetchAdvertisements()
})
</script>
