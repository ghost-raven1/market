import { defineStore } from 'pinia'
import axios from 'axios'

interface User {
  id: number
  name: string
  email: string
  created_at: string
  is_admin: boolean
}

interface AuthState {
  user: User | null
  token: string | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem('token')
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    getUser: (state) => state.user
  },

  actions: {
    async login(email: string, password: string) {
      try {
        const response = await axios.post(`/login`, {
          email,
          password
        })

        const { token, user } = response.data
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return true
      } catch (error) {
        console.error('Login error:', error)
        return false
      }
    },

    async register(name: string, email: string, password: string) {
      try {
        const response = await axios.post(`/register`, {
          name,
          email,
          password
        })

        const { token, user } = response.data
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return true
      } catch (error) {
        console.error('Registration error:', error)
        return false
      }
    },

    async logout() {
      try {
        await axios.post(`/logout`)
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get(`/user`)
        this.user = response.data
        return true
      } catch (error) {
        console.error('Fetch user error:', error)
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
        return false
      }
    },

    initialize() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.fetchUser()
      }
    }
  }
})
