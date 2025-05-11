import { ref } from 'vue'
import axios, { AxiosError } from 'axios'

interface ErrorState {
  isVisible: boolean
  title: string
  message: string
  details?: string
}

export function useError() {
  const error = ref<ErrorState>({
    isVisible: false,
    title: '',
    message: '',
    details: undefined
  })

  const showError = (title: string, message: string, details?: string) => {
    error.value = {
      isVisible: true,
      title,
      message,
      details
    }
  }

  const hideError = () => {
    error.value.isVisible = false
  }

  const handleAxiosError = (err: unknown) => {
    if (axios.isAxiosError(err)) {
      const axiosError = err as AxiosError<{ message: string; errors?: Record<string, string[]> }>
      
      if (axiosError.response) {
        const { status, data } = axiosError.response
        
        // Обработка ошибок валидации
        if (status === 422 && data.errors) {
          const errorMessages = Object.entries(data.errors)
            .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
            .join('\n')
          
          showError(
            'Ошибка валидации',
            'Пожалуйста, проверьте введенные данные',
            errorMessages
          )
          return
        }

        // Обработка ошибок аутентификации
        if (status === 401) {
          showError(
            'Ошибка аутентификации',
            'Пожалуйста, войдите в систему'
          )
          return
        }

        // Обработка ошибок авторизации
        if (status === 403) {
          showError(
            'Ошибка доступа',
            'У вас нет прав для выполнения этого действия'
          )
          return
        }

        // Обработка ошибок сервера
        if (status >= 500) {
          showError(
            'Ошибка сервера',
            'Произошла внутренняя ошибка сервера. Пожалуйста, попробуйте позже'
          )
          return
        }

        // Обработка других ошибок с сообщением от сервера
        if (data.message) {
          showError(
            'Ошибка',
            data.message
          )
          return
        }
      }

      // Обработка ошибок сети
      if (axiosError.code === 'ECONNABORTED') {
        showError(
          'Ошибка сети',
          'Превышено время ожидания ответа от сервера'
        )
        return
      }

      if (!axiosError.response) {
        showError(
          'Ошибка сети',
          'Не удалось подключиться к серверу'
        )
        return
      }
    }

    // Обработка неизвестных ошибок
    showError(
      'Неизвестная ошибка',
      'Произошла непредвиденная ошибка',
      err instanceof Error ? err.message : String(err)
    )
  }

  return {
    error,
    showError,
    hideError,
    handleAxiosError
  }
} 