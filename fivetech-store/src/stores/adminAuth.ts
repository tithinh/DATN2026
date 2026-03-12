// Admin Authentication Store
import { defineStore } from 'pinia'
import api from '@/api'

interface Admin {
  admin_id: number
  username: string
  email: string
  full_name: string
  role: string
}

export const useAdminAuthStore = defineStore('adminAuth', {
  state: () => ({
    admin: null as Admin | null,
    isAuthenticated: false
  }),

  actions: {
    async login(username: string, password: string) {
      try {
        const res = await api.post('/admin/login', { username, password })
        const { token, admin } = res.data
        
        localStorage.setItem('admin_token', token)
        localStorage.setItem('admin', JSON.stringify(admin))
        
        this.admin = admin
        this.isAuthenticated = true
        
        return { success: true }
      } catch (error: any) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Đăng nhập thất bại' 
        }
      }
    },

    logout() {
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin')
      this.admin = null
      this.isAuthenticated = false
    },

    init() {
      const token = localStorage.getItem('admin_token')
      const adminStr = localStorage.getItem('admin')
      
      if (token && adminStr) {
        try {
          this.admin = JSON.parse(adminStr)
          this.isAuthenticated = true
        } catch {
          this.logout()
        }
      }
    },

    hasPermission(requiredRole: string): boolean {
      if (!this.admin) return false
      
      const roleHierarchy: Record<string, number> = {
        'super_admin': 3,
        'admin': 2,
        'manager': 1,
        'staff': 0
      }
      
      const adminLevel = roleHierarchy[this.admin.role] || 0
      const requiredLevel = roleHierarchy[requiredRole] || 0
      
      return adminLevel >= requiredLevel
    }
  }
})
