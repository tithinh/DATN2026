<template>
  <div class="change-password-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/profile">Tài khoản</router-link>
          <span class="separator">/</span>
          <span class="current">Đổi mật khẩu</span>
        </nav>
        <h1 class="page-title">Đổi mật khẩu</h1>
      </div>
    </div>

    <!-- Main Content -->
    <section class="profile-section">
      <div class="container">
        <div class="profile-layout">
          <!-- Sidebar -->
          <aside class="profile-sidebar">
            <div class="user-avatar-card">
              <div class="avatar-wrapper">
                <img
                  :src="auth.user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.full_name || 'User')}&background=0D8ABC&color=fff`"
                  :alt="auth.user?.full_name"
                  class="avatar-image"
                />
              </div>
              <h3 class="user-name">{{ auth.user?.full_name }}</h3>
              <p class="user-email">{{ auth.user?.email }}</p>
            </div>

            <nav class="sidebar-nav">
              <router-link to="/profile" class="nav-item">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </span>
                <span class="nav-text">Thông tin cá nhân</span>
              </router-link>
              <router-link to="/profile/edit" class="nav-item">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                </span>
                <span class="nav-text">Chỉnh sửa thông tin</span>
              </router-link>
              <router-link to="/profile/change-password" class="nav-item active">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <span class="nav-text">Đổi mật khẩu</span>
              </router-link>
            </nav>
          </aside>

          <!-- Main Content -->
          <main class="profile-content">
            <div class="form-panel">
              <div class="panel-header">
                <h2 class="panel-title">Đổi mật khẩu</h2>
                <p class="panel-desc">Để bảo mật tài khoản, vui lòng sử dụng mật khẩu mạnh</p>
              </div>

              <form @submit.prevent="handleSubmit" class="password-form">
                <!-- Mật khẩu hiện tại -->
                <div class="form-group">
                  <label for="current_password">Mật khẩu hiện tại</label>
                  <div class="password-input-wrapper">
                    <input 
                      :type="showCurrentPassword ? 'text' : 'password'" 
                      id="current_password" 
                      v-model="form.current_password"
                      placeholder="Nhập mật khẩu hiện tại"
                      required
                    />
                    <button type="button" class="toggle-password" @click="showCurrentPassword = !showCurrentPassword">
                      <svg v-if="!showCurrentPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" x2="23" y1="1" y2="23"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Mật khẩu mới -->
                <div class="form-group">
                  <label for="password">Mật khẩu mới</label>
                  <div class="password-input-wrapper">
                    <input 
                      :type="showPassword ? 'text' : 'password'" 
                      id="password" 
                      v-model="form.password"
                      placeholder="Nhập mật khẩu mới (ít nhất 8 ký tự)"
                      required
                      minlength="8"
                    />
                    <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                      <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" x2="23" y1="1" y2="23"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Xác nhận mật khẩu -->
                <div class="form-group">
                  <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                  <div class="password-input-wrapper">
                    <input 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      id="password_confirmation" 
                      v-model="form.password_confirmation"
                      placeholder="Nhập lại mật khẩu mới"
                      required
                    />
                    <button type="button" class="toggle-password" @click="showConfirmPassword = !showConfirmPassword">
                      <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" x2="23" y1="1" y2="23"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Password requirements -->
                <div class="password-requirements">
                  <p class="requirements-title">Mật khẩu mạnh cần có:</p>
                  <ul class="requirements-list">
                    <li :class="{ valid: form.password.length >= 8 }">
                      <svg v-if="form.password.length >= 8" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>Ít nhất 8 ký tự</span>
                    </li>
                    <li :class="{ valid: passwordMatch }">
                      <svg v-if="passwordMatch" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>Mật khẩu xác nhận khớp</span>
                    </li>
                  </ul>
                </div>

                <!-- Submit -->
                <div class="form-actions">
                  <button type="button" class="btn-cancel" @click="$router.back()">Hủy</button>
                  <button type="submit" class="btn-submit" :disabled="loading || !isFormValid">
                    {{ loading ? 'Đang xử lý...' : 'Đổi mật khẩu' }}
                  </button>
                </div>

                <!-- Error/Success Message -->
                <div v-if="message" :class="['alert', messageType]">
                  {{ message }}
                </div>
              </form>
            </div>
          </main>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

const router = useRouter()
const auth = useAuthStore()

const loading = ref(false)
const message = ref('')
const messageType = ref('success')
const showCurrentPassword = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const passwordMatch = computed(() => {
  return form.password === form.password_confirmation && form.password_confirmation !== ''
})

const isFormValid = computed(() => {
  return form.current_password && 
         form.password.length >= 8 && 
         passwordMatch.value
})

const handleSubmit = async () => {
  if (!isFormValid.value) return

  loading.value = true
  message.value = ''

  try {
    const res = await api.put('/user/password', {
      current_password: form.current_password,
      password: form.password,
      password_confirmation: form.password_confirmation
    })

    message.value = 'Đổi mật khẩu thành công!'
    messageType.value = 'success'

    // Update token if needed
    if (res.data.token) {
      localStorage.setItem('token', res.data.token)
    }

    setTimeout(() => {
      router.push('/profile')
    }, 1500)
  } catch (err) {
    console.error('Change password failed:', err)
    message.value = err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại!'
    messageType.value = 'error'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.change-password-page {
  min-height: 100vh;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 40px 0;
  margin-bottom: 40px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.breadcrumb a {
  color: #94a3b8;
  text-decoration: none;
}

.breadcrumb a:hover {
  color: #fff;
}

.breadcrumb .separator {
  color: #64748b;
}

.breadcrumb .current {
  color: #fff;
}

.page-title {
  color: #fff;
  font-size: 32px;
  font-weight: 700;
  margin: 0;
}

.profile-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
}

.profile-sidebar {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  height: fit-content;
  position: sticky;
  top: 20px;
}

.user-avatar-card {
  text-align: center;
  padding-bottom: 24px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 24px;
}

.avatar-wrapper {
  position: relative;
  display: inline-block;
  margin-bottom: 16px;
}

.avatar-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e2e8f0;
}

.user-name {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 4px 0;
}

.user-email {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  text-decoration: none;
  color: #475569;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-item:hover {
  background: #f1f5f9;
}

.nav-item.active {
  background: #0f172a;
  color: #fff;
}

.nav-icon {
  display: flex;
  align-items: center;
}

.profile-content {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
}

.panel-header {
  margin-bottom: 32px;
}

.panel-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.panel-desc {
  color: #64748b;
  margin: 0;
}

.password-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 500px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #0f172a;
  font-size: 14px;
}

.password-input-wrapper {
  position: relative;
}

.password-input-wrapper input {
  width: 100%;
  padding: 12px 48px 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.2s;
}

.password-input-wrapper input:focus {
  outline: none;
  border-color: #3b82f6;
}

.toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  padding: 4px;
  display: flex;
  align-items: center;
}

.toggle-password:hover {
  color: #0f172a;
}

.password-requirements {
  background: #f8fafc;
  padding: 16px;
  border-radius: 10px;
}

.requirements-title {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 12px 0;
}

.requirements-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.requirements-list li {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #64748b;
}

.requirements-list li.valid {
  color: #16a34a;
}

.requirements-list li svg {
  color: #16a34a;
}

.form-actions {
  display: flex;
  gap: 16px;
  margin-top: 16px;
}

.btn-cancel,
.btn-submit {
  padding: 14px 28px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: transparent;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.btn-cancel:hover {
  background: #f1f5f9;
}

.btn-submit {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  border: none;
  color: #fff;
}

.btn-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.alert {
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 14px;
}

.alert.success {
  background: #dcfce7;
  color: #166534;
}

.alert.error {
  background: #fee2e2;
  color: #991b1b;
}

@media (max-width: 768px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }

  .profile-sidebar {
    position: static;
  }
}
</style>
