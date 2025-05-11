import api from './api'

export interface Advertisement {
  id: number
  title: string
  description: string
  price: number
  image: string
  status: 'active' | 'pending' | 'rejected'
  user_id: number
  created_at: string
  updated_at: string
}

export interface AdvertisementFilters {
  search?: string
  category?: string
  min_price?: number
  max_price?: number
  sort_by?: 'price_asc' | 'price_desc' | 'date_asc' | 'date_desc'
  page?: number
  per_page?: number
}

export const advertisementService = {
  async getAdvertisements(filters: AdvertisementFilters = {}) {
    const response = await api.get(`api/advertisements`, { params: filters })
    return response.data
  },

  async getAdvertisement(id: number) {
    const response = await api.get(`api/advertisements/${id}`)
    return response.data
  },

  async createAdvertisement(data: FormData) {
    const response = await api.post(`api/advertisements`, data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  },

  async updateAdvertisement(id: number, data: FormData) {
    const response = await api.post(`api/advertisements/${id}`, data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  },

  async deleteAdvertisement(id: number) {
    await api.delete(`api/advertisements/${id}`)
  },

  async toggleFavorite(id: number) {
    const response = await api.post(`api/advertisements/${id}/favorite`)
    return response.data
  },

  async getFavorites() {
    const response = await api.get(`api/favorites`)
    return response.data
  },

  async getUserAdvertisements() {
    const response = await api.get(`api/user/advertisements`)
    return response.data
  }
}
