// src/stores/auth.ts
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any | null,           // hoặc định nghĩa interface User
    isAuthenticated: false,
    // token: null as string | null,    // thêm nếu backend trả token
  }),

  getters: {
    avatar: (state) => {
      if (!state.user) return 'https://ui-avatars.com/api/?name=User&background=random'
      return state.user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(state.user.name || 'User')}&background=random`
    },
    // fullName: (state) => state.user?.name || 'Khách',
  },

  actions: {
    login(userData: any) {  // thay any bằng interface User nếu có
      this.user = userData
      this.isAuthenticated = true
      localStorage.setItem('user', JSON.stringify(userData))
      // if (userData.token) localStorage.setItem('token', userData.token)
    },

    logout() {
      this.user = null
      this.isAuthenticated = false
      localStorage.removeItem('user')
      // localStorage.removeItem('token')
      // Nếu dùng router: router.push('/login')
    },

    init() {
      try {
        const saved = localStorage.getItem('user')
        if (saved) {
          const parsed = JSON.parse(saved)
          // Optional: kiểm tra token hết hạn hoặc gọi API verify nếu cần
          this.user = parsed
          this.isAuthenticated = true
        }
      } catch (err) {
        console.error('Failed to parse saved user from localStorage', err)
        this.logout() // xóa dữ liệu hỏng
      }
    },

    // Optional: nếu sau này cần gọi API refresh hoặc verify
    // async verifyToken() { ... }
  },
})
