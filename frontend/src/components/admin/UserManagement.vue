<template>
  <div class="space-y-6">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div v-if="loading" class="p-4 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Загрузка пользователей...</p>
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Имя
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Email
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Дата регистрации
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Роль
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Действия
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ user.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ formatDate(user.created_at) }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                :class="user.is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
              >
                {{ user.is_admin ? 'Администратор' : 'Пользователь' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="toggleAdmin(user)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                {{ user.is_admin ? 'Убрать админа' : 'Сделать админом' }}
              </button>
              <button
                @click="deleteUser(user)"
                class="text-red-600 hover:text-red-900"
              >
                Удалить
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Confirm Modal -->
    <ConfirmModal
      :is-visible="showConfirmModal"
      :title="confirmModal.title"
      :message="confirmModal.message"
      :type="confirmModal.type"
      :confirm-text="confirmModal.confirmText"
      :loading="actionLoading"
      @confirm="handleConfirm"
      @cancel="showConfirmModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'
import ConfirmModal from '../common/ConfirmModal.vue'

interface User {
  id: number
  name: string
  email: string
  created_at: string
  is_admin: boolean
}

const users = ref<User[]>([])
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

const fetchUsers = async () => {
  try {
    loading.value = true
    const response = await axios.get(`api/users`)
    users.value = response.data.data
  } catch (error) {
    console.error('Error fetching users:', error)
    notification.error(
      'Ошибка загрузки',
      'Не удалось загрузить список пользователей'
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
    } catch (error) {
      console.error('Error performing action:', error)
    } finally {
      actionLoading.value = false
      showConfirmModal.value = false
    }
  }
}

const toggleAdmin = async (user: User) => {
  showConfirm({
    title: user.is_admin ? 'Убрать права администратора' : 'Назначить администратором',
    message: `Вы уверены, что хотите ${user.is_admin ? 'убрать права администратора у' : 'назначить администратором'} пользователя "${user.name}"?`,
    type: 'warning',
    confirmText: user.is_admin ? 'Убрать права' : 'Назначить',
    action: async () => {
      try {
        await axios.patch(`api/users/${user.id}`, {
          is_admin: !user.is_admin
        })
        user.is_admin = !user.is_admin
        notification.success(
          'Статус обновлен',
          `Пользователь "${user.name}" ${user.is_admin ? 'назначен администратором' : 'лишен прав администратора'}`
        )
      } catch (error) {
        notification.error(
          'Ошибка',
          'Не удалось обновить статус пользователя'
        )
        throw error
      }
    }
  })
}

const deleteUser = async (user: User) => {
  showConfirm({
    title: 'Удаление пользователя',
    message: `Вы уверены, что хотите удалить пользователя "${user.name}"? Это действие нельзя будет отменить.`,
    type: 'error',
    confirmText: 'Удалить',
    action: async () => {
      try {
        await axios.delete(`api/users/${user.id}`)
        users.value = users.value.filter(u => u.id !== user.id)
        notification.success(
          'Пользователь удален',
          `Пользователь "${user.name}" успешно удален`
        )
      } catch (error) {
        notification.error(
          'Ошибка',
          'Не удалось удалить пользователя'
        )
        throw error
      }
    }
  })
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('ru-RU')
}

onMounted(() => {
  fetchUsers()
})
</script>
