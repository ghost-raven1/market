import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost/api'
})

export interface User {
  id: number
  name: string
  email: string
  phone?: string
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
  remember_me?: boolean
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export const authService = {
  async login(credentials: LoginCredentials) {
    const response = await api.post<{ data: { user: User; token: string } }>('/login', credentials)
    const { token } = response.data.data
    localStorage.setItem('token', token)
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    return response.data
  },

  async register(data: RegisterData) {
    const response = await api.post<{ data: { user: User; token: string } }>('/register', data)
    const { token } = response.data.data
    localStorage.setItem('token', token)
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    return response.data
  },

  async logout() {
    await api.post('/logout')
    localStorage.removeItem('token')
    delete api.defaults.headers.common['Authorization']
  },

  async getCurrentUser() {
    const response = await api.get<{ data: User }>('/user')
    return response.data
  },

  isAuthenticated() {
    return !!localStorage.getItem('token')
  },

  async updateProfile(data: Partial<User>) {
    const response = await api.put('/profile', data)
    return response.data
  },

  async changePassword(data: { current_password: string; new_password: string; new_password_confirmation: string }) {
    const response = await api.put('/password', data)
    return response.data
  },

  async deleteAccount() {
    await api.delete('/profile')
    localStorage.removeItem('token')
  }
}
