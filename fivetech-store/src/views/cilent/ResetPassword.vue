<template>
  <div class="reset-password-page">
    <div class="container">
      <div class="reset-password-card">
        <div class="card-header">
          <router-link to="/login" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Quay lại đăng nhập
          </router-link>
          <h1 class="title">Đặt lại mật khẩu</h1>
          <p class="subtitle">Nhập mật khẩu mới cho tài khoản của bạn</p>
        </div>

        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <form @submit.prevent="handleSubmit" v-if="!successMessage">
          <div class="form-group">
            <label class="form-label">Mật khẩu mới</label>
            <div class="input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="input-icon">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="password" 
                placeholder="Nhập mật khẩu mới" 
                class="form-input"
                required
                :disabled="loading"
              />
              <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="m2 2 20 20"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Xác nhận mật khẩu</label>
            <div class="input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="input-icon">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="passwordConfirmation" 
                placeholder="Xác nhận mật khẩu mới" 
                class="form-input"
                required
                :disabled="loading"
              />
            </div>
          </div>

          <button type="submit" class="btn-submit" :disabled="loading">
            {{ loading ? 'Đang xử lý...' : 'Đặt lại mật khẩu' }}
          </button>
        </form>

        <div class="card-footer" v-if="successMessage">
          <router-link to="/login" class="btn-login">Đăng nhập ngay</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { resetPassword } from '@/api'

const route = useRoute()
const router = useRouter()

const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleSubmit = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'Mật khẩu xác nhận không khớp!'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const token = route.params.token
    await resetPassword({
      token,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })
    successMessage.value = 'Đặt lại mật khẩu thành công! Đang chuyển về trang đăng nhập...'
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (err) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message
    } else {
      errorMessage.value = 'Có lỗi xảy ra. Vui lòng thử lại.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.reset-password-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.container {
  width: 100%;
  max-width: 480px;
}

.reset-password-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.card-header {
  text-align: center;
  margin-bottom: 32px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-size: 14px;
  text-decoration: none;
  margin-bottom: 24px;
  transition: color 0.2s;
}

.back-link:hover {
  color: #ff6b35;
}

.title {
  font-size: 28px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 12px 0;
}

.subtitle {
  font-size: 15px;
  color: #64748b;
  margin: 0;
  line-height: 1.6;
}

.success-message {
  background: #dcfce7;
  color: #16a34a;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 24px;
  text-align: center;
  font-size: 14px;
}

.error-message {
  background: #fee2e2;
  color: #dc2626;
  padding: 16px;
  border-radius: 12px;
  margin-bottom: 24px;
  text-align: center;
  font-size: 14px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.form-input {
  width: 100%;
  padding: 16px 48px 16px 48px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  color: #0f172a;
  background: #f8fafc;
  transition: all 0.3s;
  outline: none;
}

.form-input:focus {
  border-color: #ff6b35;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
}

.toggle-password {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
}

.toggle-password:hover {
  color: #64748b;
}

.btn-submit {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 8px;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.card-footer {
  text-align: center;
  margin-top: 24px;
}

.btn-login {
  display: inline-block;
  padding: 14px 32px;
  background: #ff6b35;
  color: #ffffff;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-login:hover {
  background: #f97316;
}
</style>
