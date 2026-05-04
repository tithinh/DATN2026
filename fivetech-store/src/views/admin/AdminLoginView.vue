<template>
  <div class="admin-login-page">
    <div class="login-card">
      <!-- Logo -->
      <div class="login-logo">
        <div class="logo-icon">FT</div>
        <h1>FiveTech</h1>
      </div>
      <p class="login-subtitle">Đăng nhập vào trang quản trị</p>

      <!-- Form -->
      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email hoặc Username</label>
          <div class="form-input-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>
            <input
              type="text"
              v-model="email"
              placeholder="admin@fivetech.vn"
              autocomplete="username"
              required
            />
          </div>
        </div>

        <div class="form-group">
          <label>Mật khẩu</label>
          <div class="form-input-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
            <button type="button" @click="showPassword = !showPassword" style="background: none; border: none; cursor: pointer; padding: 0; display: flex;">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--admin-text-muted);"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--admin-text-muted);"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
            </button>
          </div>
        </div>

        <!-- Options -->
        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" v-model="rememberMe" />
            Ghi nhớ đăng nhập
          </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="login-btn" :disabled="isLoading">
          <span v-if="!isLoading">Đăng nhập</span>
          <span v-else style="display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            Đang đăng nhập...
          </span>
        </button>

        <!-- Error Message -->
        <p v-if="errorMsg" style="color: var(--admin-danger); font-size: 13px; text-align: center; margin: 0; padding: 8px; background: var(--admin-danger-soft); border-radius: 8px;">
          {{ errorMsg }}
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminAuthStore } from '@/stores/adminAuth'

const router = useRouter()
const adminAuth = useAdminAuthStore()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)
const isLoading = ref(false)
const errorMsg = ref('')

const handleLogin = async () => {
  errorMsg.value = ''
  isLoading.value = true

  try {
    const result = await adminAuth.login(email.value.trim(), password.value)
    
    if (result.success) {
      if (rememberMe.value) {
        localStorage.setItem('rememberAdmin', 'true')
      }
      router.push('/admin')
    } else {
      errorMsg.value = result.message || 'Đăng nhập thất bại!'
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Đăng nhập thất bại!'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
@import '@/views/admin/css/admin.css';

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>