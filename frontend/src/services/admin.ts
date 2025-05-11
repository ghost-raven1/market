import api from './api'
import { User } from './auth'
import { Advertisement } from './advertisements'

export interface AdminStats {
  total_users: number
  total_ads: number
  monthly_income: number
}

export const adminService = {
  async getStats() {
    const response = await api.get(`api/admin/stats`)
    return response.data as AdminStats
  },

  async getUsers() {
    const response = await api.get(`api/admin/users`)
    return response.data as User[]
  },

  async toggleUserStatus(userId: number) {
    const response = await api.post(`api/admin/users/${userId}/toggle-status`)
    return response.data as User
  },

  async getAdvertisements() {
    const response = await api.get(`api/admin/advertisements`)
    return response.data as Advertisement[]
  },

  async reviewAdvertisement(id: number, status: 'active' | 'rejected') {
    const response = await api.post(`api/admin/advertisements/${id}/review`, { status })
    return response.data as Advertisement
  },

  async deleteAdvertisement(id: number) {
    await api.delete(`api/admin/advertisements/${id}`)
  }
}
