import { ref } from 'vue'

interface Notification {
  id: number
  message: string
  type: string
  read_at: string | null
  created_at: string
}

export function useNotifications() {
  const notifications = ref<Notification[]>([])
  const unreadCount = ref(0)
  const loading = ref(false)

  // Загрузка уведомлений
  const loadNotifications = async () => {
    try {
      loading.value = true
      const response = await fetch(`api/notifications`)
      if (!response.ok) throw new Error('Failed to load notifications')
      notifications.value = await response.json()
    } catch (error) {
      console.error('Error loading notifications:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Загрузка количества непрочитанных уведомлений
  const loadUnreadCount = async () => {
    try {
      const response = await fetch(`api/notifications/unread-count`)
      if (!response.ok) throw new Error('Failed to load unread count')
      const data = await response.json()
      unreadCount.value = data.count
    } catch (error) {
      console.error('Error loading unread count:', error)
      throw error
    }
  }

  // Отметить уведомление как прочитанное
  const markAsRead = async (notificationId: number) => {
    try {
      const response = await fetch(`api/notifications/${notificationId}/read`, {
        method: 'POST'
      })
      if (!response.ok) throw new Error('Failed to mark notification as read')

      const notification = notifications.value.find(n => n.id === notificationId)
      if (notification) {
        notification.read_at = new Date().toISOString()
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
    } catch (error) {
      console.error('Error marking notification as read:', error)
      throw error
    }
  }

  // Отметить все уведомления как прочитанные
  const markAllAsRead = async () => {
    try {
      const response = await fetch(`api/notifications/read-all`, {
        method: 'POST'
      })
      if (!response.ok) throw new Error('Failed to mark all notifications as read')

      notifications.value = notifications.value.map(n => ({
        ...n,
        read_at: n.read_at || new Date().toISOString()
      }))
      unreadCount.value = 0
    } catch (error) {
      console.error('Error marking all notifications as read:', error)
      throw error
    }
  }

  return {
    notifications,
    unreadCount,
    loading,
    loadNotifications,
    loadUnreadCount,
    markAsRead,
    markAllAsRead
  }
}
