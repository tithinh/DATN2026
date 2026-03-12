// src/stores/auth.ts
import { defineStore } from 'pinia'
// @ts-ignore - api is a JS module
import api from '@/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any | null,
    isAuthenticated: false,
  }),

  getters: {
    avatar: (state) => {
      if (!state.user) return 'https://ui-avatars.com/api/?name=User&background=random'
      return state.user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(state.user.full_name || 'User')}&background=random`
    },
  },

  actions: {
    login(userData: any) {
      this.user = userData
      this.isAuthenticated = true
      localStorage.setItem('user', JSON.stringify(userData))
    },

    logout() {
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    },

    init() {
      try {
        const saved = localStorage.getItem('user')
        const token = localStorage.getItem('token')
        if (saved && token) {
          const parsed = JSON.parse(saved)
          this.user = parsed
          this.isAuthenticated = true
          // Đồng bộ thông tin user mới nhất từ server
          this.fetchUser()
        }
      } catch (err) {
        console.error('Failed to parse saved user from localStorage', err)
        this.logout()
      }
    },

    // Lấy thông tin user mới nhất từ server (đồng bộ khi admin sửa)
    async fetchUser() {
      try {
        const res = await api.get('/user')
        if (res.data?.user) {
          this.user = res.data.user
          localStorage.setItem('user', JSON.stringify(res.data.user))
        }
      } catch (err: any) {
        // Nếu token hết hạn hoặc user bị khóa → tự đăng xuất
        if (err.response?.status === 401 || err.response?.status === 403) {
          this.logout()
        }
        console.error('Failed to fetch user info:', err)
      }
    },
  },
})

