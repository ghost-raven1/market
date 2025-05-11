import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost/api'
})

export interface Advertisement {
  id: number
  title: string
  description: string
  price: number
  image: string
  created_at: string
  updated_at: string
  user_id: number
  category_id: number
  status: 'active' | 'sold' | 'archived'
}

export const advertisementService = {
  async getAdvertisements(params?: {
    page?: number
    per_page?: number
    sort_by?: string
    sort_order?: 'asc' | 'desc'
    category_id?: number
    search?: string
  }) {
    const response = await api.get<{ data: Advertisement[]; meta: any }>(`api/advertisements`, {
      params
    })
    return response.data
  },

  async getAdvertisement(id: number) {
    const response = await api.get<{ data: Advertisement }>(`api/advertisements/${id}`)
    return response.data
  },

  async createAdvertisement(data: Omit<Advertisement, 'id' | 'created_at' | 'updated_at'>) {
    const response = await api.post<{ data: Advertisement }>(`api/advertisements`, data)
    return response.data
  },

  async updateAdvertisement(id: number, data: Partial<Advertisement>) {
    const response = await api.put<{ data: Advertisement }>(`api/advertisements/${id}`, data)
    return response.data
  },

  async deleteAdvertisement(id: number) {
    await api.delete(`/advertisements/${id}`)
  },

  async getFavorites() {
    const response = await api.get<{ data: Advertisement[] }>(`api/favorites`)
    return response.data
  },

  async addToFavorites(id: number) {
    const response = await api.post<{ data: Advertisement }>(`api/favorites/${id}`)
    return response.data
  },

  async removeFromFavorites(id: number) {
    await api.delete(`api/favorites/${id}`)
  }
}
