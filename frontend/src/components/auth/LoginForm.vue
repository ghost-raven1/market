<template>
  <div class="max-w-md mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Вход</h2>
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.email }"
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          required
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          :class="{ 'border-red-500': errors.password }"
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
      </div>

      <div>
        <button
          type="submit"
          class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          :disabled="loading"
        >
          {{ loading ? 'Вход...' : 'Войти' }}
        </button>
      </div>

      <div class="text-center">
        <router-link to="/register" class="text-sm text-indigo-600 hover:text-indigo-500">
          Нет аккаунта? Зарегистрируйтесь
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const notification = useNotification()
const loading = ref(false)
const errors = reactive<Record<string, string[]>>({})

const form = reactive({
  email: '',
  password: ''
})

const handleSubmit = async () => {
  loading.value = true
  errors.email = []
  errors.password = []

  try {
    const response = await axios.post(`api/login`, form)
    localStorage.setItem('token', response.data.token)
    await authStore.initialize()
    notification.success(
      'Вход выполнен',
      'Вы успешно вошли в систему'
    )
    router.push('/')
  } catch (error: any) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors)
      notification.error(
        'Ошибка входа',
        'Пожалуйста, проверьте введенные данные'
      )
    } else {
      notification.error(
        'Ошибка',
        'Не удалось войти в систему. Пожалуйста, попробуйте позже.'
      )
    }
  } finally {
    loading.value = false
  }
}
</script>
