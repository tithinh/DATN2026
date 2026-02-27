<template>
  <div class="forgot-password-page">
    <div class="container">
      <div class="forgot-password-card">
        <div class="card-header">
          <router-link to="/login" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Quay lại đăng nhập
          </router-link>
          <h1 class="title">Quên mật khẩu?</h1>
          <p class="subtitle">Nhập email của bạn để nhận liên kết đặt lại mật khẩu</p>
        </div>

        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>

        <form @submit.prevent="handleSubmit" v-if="!successMessage">
          <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-wrapper">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="input-icon">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
              <input 
                type="email" 
                v-model="email" 
                placeholder="email@example.com" 
                class="form-input"
                required
                :disabled="loading"
              />
            </div>
          </div>

          <button type="submit" class="btn-submit" :disabled="loading">
            {{ loading ? 'Đang gửi...' : 'Gửi liên kết' }}
          </button>
        </form>

        <div class="card-footer">
          <p>Nhớ mật khẩu? <router-link to="/login" class="link">Đăng nhập ngay</router-link></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { forgotPassword } from '@/api'

const email = ref('')
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await forgotPassword(email.value)
    successMessage.value = response.data.message || 'Vui lòng kiểm tra email để đặt lại mật khẩu!'
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
.forgot-password-page {
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

.forgot-password-card {
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
  margin-bottom: 24px;
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
  padding: 16px 16px 16px 48px;
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
  font-size: 14px;
  color: #64748b;
}

.link {
  color: #ff6b35;
  text-decoration: none;
  font-weight: 600;
}

.link:hover {
  text-decoration: underline;
}
</style>
