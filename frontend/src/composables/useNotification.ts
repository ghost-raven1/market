import { ref } from 'vue'

interface Notification {
  id: number
  type: 'success' | 'info' | 'warning' | 'error'
  title: string
  message: string
  duration?: number
}

export function useNotification() {
  const notifications = ref<Notification[]>([])
  let nextId = 1

  const add = (notification: Omit<Notification, 'id'>) => {
    const id = nextId++
    const newNotification = { ...notification, id }
    notifications.value.push(newNotification)

    if (notification.duration !== 0) {
      setTimeout(() => {
        remove(id)
      }, notification.duration || 5000)
    }

    return id
  }

  const remove = (id: number) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index !== -1) {
      notifications.value.splice(index, 1)
    }
  }

  const success = (title: string, message: string, duration?: number) => {
    return add({ type: 'success', title, message, duration })
  }

  const info = (title: string, message: string, duration?: number) => {
    return add({ type: 'info', title, message, duration })
  }

  const warning = (title: string, message: string, duration?: number) => {
    return add({ type: 'warning', title, message, duration })
  }

  const error = (title: string, message: string, duration?: number) => {
    return add({ type: 'error', title, message, duration })
  }

  return {
    notifications,
    add,
    remove,
    success,
    info,
    warning,
    error
  }
} 