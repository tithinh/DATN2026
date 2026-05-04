<template>
  <div class="track-order-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <span class="current">Tra cứu đơn hàng</span>
        </nav>
        <h1 class="page-title">Tra cứu đơn hàng</h1>
        <p class="page-subtitle">Nhập email bạn đã dùng để đặt hàng, chúng tôi sẽ gửi link tra cứu đến hộp thư của bạn</p>
      </div>

    <!-- Main Content -->
    <div class="track-content">
      <div class="container">
        <div class="track-card">
          <!-- Success State -->
          <div v-if="successMessage" class="success-box">
            <div class="success-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h2>Đã gửi email tra cứu!</h2>
            <p>{{ successMessage }}</p>
            <button class="btn-primary" @click="resetForm">Gửi email khác</button>
          </div>

          <!-- Form State -->
          <form v-else @submit.prevent="submitTracking" class="track-form">
            <div class="form-group">
              <label class="form-label">Địa chỉ email <span class="required">*</span></label>
              <input
                type="email"
                class="form-input"
                v-model="email"
                placeholder="email@example.com"
                :class="{ 'error': errors.email }"
                :disabled="loading"
              />
              <span class="form-error" v-if="errors.email">{{ errors.email }}</span>
            </div>

            <button type="submit" class="submit-btn" :disabled="loading || !email.trim()">
              <span v-if="!loading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Gửi link tra cứu
              </span>
              <span v-else>Đang gửi...</span>
            </button>

            <div v-if="errorMessage" class="error-box">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              {{ errorMessage }}
            </div>
          </form>

          <!-- Info Box -->
          <div class="info-box">
            <h4>Lưu ý:</h4>
            <ul>
              <li>Chúng tôi chỉ tìm kiếm đơn hàng được đặt trong <strong>90 ngày</strong> qua.</li>
              <li>Email tra cứu sẽ chứa link trực tiếp đến từng đơn hàng, <strong>không cần đăng nhập</strong>.</li>
              <li>Nếu bạn đã có tài khoản, bạn có thể <router-link to="/login">đăng nhập</router-link> để xem tất cả đơn hàng.</li>
            </ul>
          </div>

      </div>
      </div>
      </div>
      </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api'

const email = ref('')
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

const submitTracking = async () => {
  errors.value = {}
  errorMessage.value = ''
  successMessage.value = ''

  if (!email.value.trim()) {
    errors.value.email = 'Vui lòng nhập email'
    return
  }

  loading.value = true
  try {
            const res = await api.post('/orders/track', { email: email.value.trim() })
            successMessage.value = res.data.message || 'Email tra cứu đã được gửi thành công!'
  } catch (err) {
    if (err.response?.status === 404) {
      errorMessage.value = 'Không tìm thấy đơn hàng nào được đặt bằng email này trong 90 ngày qua.'
    } else if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      errorMessage.value = Object.values(errors.value)[0]?.[0] || 'Dữ liệu không hợp lệ.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Không thể gửi email, vui lòng thử lại sau.'
    }
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  email.value = ''
  successMessage.value = ''
  errorMessage.value = ''
  errors.value = {}
}
</script>

<style scoped>
.track-order-page {
  min-height: 100vh;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 40px 0;
  margin-bottom: 30px;
}

.container {
  max-width: 600px;
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
  transition: color 0.2s;
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
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 8px 0;
}

.page-subtitle {
  color: #94a3b8;
  font-size: 15px;
  margin: 0;
}

/* Track Content */
.track-content {
  padding-bottom: 60px;
}

.track-card {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Form */
.track-form {
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 8px;
}

.form-label .required {
  color: #ef4444;
}

.form-input {
  width: 100%;
  padding: 14px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  color: #0f172a;
  background: #f8fafc;
  transition: all 0.2s ease;
  outline: none;
}

.form-input:focus {
  border-color: #ff6b35;
  background: #ffffff;
}

.form-input.error {
  border-color: #ef4444;
  background: #fef2f2;
}

.form-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-error {
  display: block;
  margin-top: 6px;
  font-size: 13px;
  color: #ef4444;
}

.submit-btn {
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s ease;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Success Box */
.success-box {
  text-align: center;
  padding: 20px 0;
}

.success-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 20px;
  background: #dcfce7;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #16a34a;
}

.success-box h2 {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.success-box p {
  font-size: 15px;
  color: #64748b;
  margin: 0 0 24px 0;
  line-height: 1.6;
}

.btn-primary {
  padding: 12px 28px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

/* Error Box */
.error-box {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding: 12px 16px;
  background: #fef2f2;
  color: #dc2626;
  border-radius: 10px;
  font-size: 14px;
}

/* Info Box */
.info-box {
  margin-top: 24px;
  padding: 20px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.info-box h4 {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 10px 0;
}

.info-box ul {
  margin: 0;
  padding-left: 18px;
}

.info-box li {
  font-size: 13px;
  color: #64748b;
  line-height: 1.8;
}

.info-box li:not(:last-child) {
  margin-bottom: 4px;
}

.info-box a {
  color: #ff6b35;
  text-decoration: none;
  font-weight: 600;
}

.info-box a:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 640px) {
  .track-card {
    padding: 24px 20px;
  }

  .page-title {
    font-size: 24px;
  }
}
</style>
