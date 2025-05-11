export interface User {
  id: number
  name: string
  email: string
  created_at: string
  is_admin: boolean
}

export interface AuthState {
  user: User | null
  token: string | null
}

export interface AuthStore {
  user: User | null
  token: string | null
  isAuthenticated: boolean
  getUser: User | null
  login: (email: string, password: string) => Promise<boolean>
  register: (name: string, email: string, password: string) => Promise<boolean>
  logout: () => Promise<void>
  fetchUser: () => Promise<boolean>
  initialize: () => void
} 