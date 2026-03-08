<template>
  <div class="auth-page">
    <div class="auth-container">
      <!-- Left Side - Branding -->
      <div class="auth-branding">
        <div class="brand-content">
          <div class="brand-logo">
            <span class="logo-icon">T5</span>
            <span class="logo-text">Techfive</span>
          </div>
          <h1 class="brand-title">Tham gia ngay!</h1>
          <p class="brand-description">
            Đăng ký thành viên để nhận ngay voucher giảm 50K cho đơn hàng đầu tiên và nhiều ưu đãi khác.
          </p>
          <div class="brand-features">
            <div class="feature-item">
              <span class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5.8 11.3 2 22l10.7-3.79 4.26 7 1.93-3.97 1.38 3.01L22 2l-10.9 9.3Z"/><path d="m20 2-7.54 11.16"/></svg>
              </span>
              <span style="color: #f97316">Giảm 50K đơn đầu tiên</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2" ry="2"/><polyline points="16 8 20 8 23 11 23 16 16 16"/><path d="M16 16v-8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              </span>
              <span style="color: #22c55e">Freeship đơn từ 300K</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
              </span>
              <span style="color: #f43f5e">Tích điểm đổi quà hấp dẫn</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
              </span>
              <span style="color: #8b5cf6">Ưu đãi sinh nhật đặc biệt</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Register Form -->
      <div class="auth-form-wrapper">
        <div class="auth-form-container">

          <div class="back-to-home">
            <router-link to="/" class="back-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
              </svg>
              <span>Trở về trang chủ</span>
            </router-link>
          </div>
          
          <h2 class="form-title">Đăng ký tài khoản</h2>
          <p class="form-subtitle">Nhập thông tin để tạo tài khoản mới</p>

          <!-- Hiển thị lỗi -->
          <div v-if="errorMessage" class="error-message">
            {{ errorMessage }}
          </div>

          <form class="auth-form" @submit.prevent="handleRegister">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Họ</label>
                <div class="input-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <input 
                    type="text" 
                    v-model="lastName" 
                    placeholder="Nguyễn" 
                    class="form-input"
                    required
                    :disabled="loading"
                  />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Tên</label>
                <div class="input-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <input 
                    type="text" 
                    v-model="firstName" 
                    placeholder="Văn A" 
                    class="form-input"
                    required
                    :disabled="loading"
                  />
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Email</label>
              <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
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

            <div class="form-group">
              <label class="form-label">Số điện thoại</label>
              <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <input 
                  type="tel" 
                  v-model="phone" 
                  placeholder="0912345678" 
                  class="form-input"
                  required
                  :disabled="loading"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Mật khẩu</label>
              <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  v-model="password" 
                  placeholder="Tối thiểu 8 ký tự" 
                  class="form-input"
                  required
                  :disabled="loading"
                />
                <button type="button" class="toggle-password" @click="showPassword = !showPassword" :disabled="loading">
                  <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
                    <path d="m2 2 20 20"></path>
                  </svg>
                </button>
              </div>
              <div class="password-strength">
                <div class="strength-bar" :class="passwordStrength"></div>
                <span class="strength-text">{{ passwordStrengthText }}</span>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Xác nhận mật khẩu</label>
              <div class="input-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input 
                  :type="showConfirmPassword ? 'text' : 'password'" 
                  v-model="confirmPassword" 
                  placeholder="Nhập lại mật khẩu" 
                  class="form-input"
                  required
                  :disabled="loading"
                />
                <button type="button" class="toggle-password" @click="showConfirmPassword = !showConfirmPassword" :disabled="loading">
                  <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
                    <path d="m2 2 20 20"></path>
                  </svg>
                </button>
              </div>
            </div>

            <label class="checkbox-wrapper">
              <input type="checkbox" v-model="agreeTerms" :disabled="loading" required />
              <span class="checkbox-label">
                Tôi đồng ý với <a href="/terms" class="terms-link">Điều khoản dịch vụ</a> và <a href="/privacy" class="terms-link">Chính sách bảo mật</a>
              </span>
            </label>

            <button type="submit" class="btn-submit" :disabled="loading">
              {{ loading ? 'Đang đăng ký...' : 'Đăng ký' }}
            </button>
          </form>

          <div class="divider">
            <span>hoặc đăng ký với</span>
          </div>

          <div class="social-login">
            <button class="social-btn google-btn" @click="handleGoogleRegister">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              <span>Google</span>
            </button>
            <button class="social-btn facebook-btn" @click="handleFacebookRegister">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span>Facebook</span>
            </button>
          </div>

          <p class="auth-switch">
            Đã có tài khoản? 
            <a href="/login" class="switch-link">Đăng nhập</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

const firstName       = ref('')
const lastName        = ref('')
const email           = ref('')
const phone           = ref('')
const password        = ref('')
const confirmPassword = ref('')
const showPassword    = ref(false)
const showConfirmPassword = ref(false)
const agreeTerms      = ref(false)
const loading         = ref(false)
const errorMessage    = ref('')

const router = useRouter()

const passwordStrength = computed(() => {
  if (!password.value) return ''
  if (password.value.length < 8) return 'weak'
  if (password.value.length < 12) return 'medium'
  return 'strong'
})

const passwordStrengthText = computed(() => {
  if (!password.value) return ''
  if (password.value.length < 8) return 'Yếu'
  if (password.value.length < 12) return 'Trung bình'
  return 'Mạnh'
})

const handleRegister = async () => {
  errorMessage.value = ''

  if (password.value !== confirmPassword.value) {
    errorMessage.value = 'Mật khẩu xác nhận không khớp!'
    return
  }

  if (!agreeTerms.value) {
    errorMessage.value = 'Bạn cần đồng ý với điều khoản!'
    return
  }

  loading.value = true

  try {
    // Không cần gọi csrf thủ công nữa nếu interceptor đã xử lý
    const response = await api.post('/register', {
      full_name: `${lastName.value.trim()} ${firstName.value.trim()}`.trim(),
      email: email.value.trim(),
      phone: phone.value.trim() || null,
      password: password.value,
      password_confirmation: confirmPassword.value,
    })

    const data = response.data

    if (data.token) {
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user || {}))
      
      // Update auth store
      const auth = useAuthStore()
      auth.login(data.user || {})
    }

    alert(data.message || 'Đăng ký thành công!')

    router.push('/')

  } catch (err) {
    console.error('Register error:', err)

    if (err.response) {
      const { status, data } = err.response

      if (status === 422 && data.errors) {
        const firstError = Object.values(data.errors)[0]?.[0]
        errorMessage.value = firstError || 'Thông tin không hợp lệ!'
      } else if (status === 419) {
        errorMessage.value = 'CSRF token không hợp lệ. Vui lòng thử lại!'
      } else if (status === 429) {
        errorMessage.value = 'Quá nhiều yêu cầu. Vui lòng chờ!'
      } else {
        errorMessage.value = data.message || 'Đăng ký thất bại!'
      }
    } else {
      errorMessage.value = 'Không kết nối được server. Kiểm tra mạng!'
    }
  } finally {
    loading.value = false
  }
}

const handleGoogleRegister = () => {
  // Open Google login in popup
  const width = 500
  const height = 600
  const left = (screen.width - width) / 2
  const top = (screen.height - height) / 2
  
  const popup = window.open(
    `${import.meta.env.VITE_API_URL || "http://localhost:8000"}/auth/google`,
    'google-register',
    `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`
  )
  
  // Listen for message from popup
  const checkPopup = setInterval(() => {
    if (popup.closed) {
      clearInterval(checkPopup)
      // Refresh and check if user is logged in
      window.location.reload()
    }
  }, 500)
}

const handleFacebookRegister = () => {
  // Open Facebook login in popup
  const width = 500
  const height = 600
  const left = (screen.width - width) / 2
  const top = (screen.height - height) / 2
  
  const popup = window.open(
    `${import.meta.env.VITE_API_URL || "http://localhost:8000"}/auth/facebook`,
    'facebook-register',
    `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`
  )
  
  // Listen for message from popup
  const checkPopup = setInterval(() => {
    if (popup.closed) {
      clearInterval(checkPopup)
      // Refresh and check if user is logged in
      window.location.reload()
    }
  }, 500)
}
</script>

<style scoped>
/* Style cho nút trở về trang chủ - thêm mới */
.back-to-home {
  margin-bottom: 24px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #64748b;
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.2s ease;
}

.back-link:hover {
  color: #3b82f6; /* màu primary của bạn */
}

.back-link svg {
  transition: transform 0.2s ease;
}

.back-link:hover svg {
  transform: translateX(-4px); /* hiệu ứng mũi tên lùi lại khi hover */
}
/* Thêm style cho error message */
.error-message {
  background: #fee2e2;
  color: #dc2626;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-size: 14px;
  text-align: center;
  border: 1px solid #fecaca;
}
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.auth-page {
  min-height: 100vh;
  background: #f8fafc;
  font-family: 'Inter', sans-serif;
}

.auth-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
}

/* Branding Side */
.auth-branding {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px;
  position: relative;
  overflow: hidden;
}

.auth-branding::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255, 107, 53, 0.1) 0%, transparent 70%);
}

.brand-content {
  position: relative;
  z-index: 1;
  max-width: 480px;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}

.logo-icon {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 22px;
  font-weight: 800;
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.logo-text {
  font-size: 32px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.5px;
}

.brand-title {
  font-size: 48px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 20px 0;
  line-height: 1.2;
}

.brand-description {
  font-size: 18px;
  color: #94a3b8;
  line-height: 1.7;
  margin: 0 0 40px 0;
}

.brand-features {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #ffffff;
  padding: 16px 20px;
  border-radius: 16px;
  font-size: 15px;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.feature-item:hover {
  transform: translateX(5px);
}

.feature-icon {
  font-size: 24px;
}

/* Form Side */
.auth-form-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 60px;
  background: #ffffff;
  overflow-y: auto;
}

.auth-form-container {
  width: 100%;
  max-width: 420px;
}

.form-title {
  font-size: 32px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.form-subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 0 0 24px 0;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #334155;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  color: #94a3b8;
}

.form-input {
  width: 100%;
  padding: 14px 14px 14px 44px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  color: #0f172a;
  background: #f8fafc;
  transition: all 0.3s ease;
  outline: none;
}

.form-input:focus {
  border-color: #ff6b35;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
}

.form-input::placeholder {
  color: #94a3b8;
}

.toggle-password {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggle-password:hover {
  color: #64748b;
}

.password-strength {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.strength-bar {
  height: 4px;
  width: 80px;
  background: #e2e8f0;
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.strength-bar::after {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.strength-bar.weak::after {
  width: 33%;
  background: #ef4444;
}

.strength-bar.medium::after {
  width: 66%;
  background: #f59e0b;
}

.strength-bar.strong::after {
  width: 100%;
  background: #22c55e;
}

.strength-text {
  font-size: 12px;
  color: #64748b;
}

.checkbox-wrapper {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  cursor: pointer;
}

.checkbox-wrapper input {
  width: 18px;
  height: 18px;
  accent-color: #ff6b35;
  margin-top: 2px;
}

.checkbox-label {
  font-size: 13px;
  color: #475569;
  line-height: 1.5;
}

.terms-link {
  color: #ff6b35;
  text-decoration: none;
}

.terms-link:hover {
  text-decoration: underline;
}

.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.divider {
  display: flex;
  align-items: center;
  margin: 20px 0;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e2e8f0;
}

.divider span {
  padding: 0 16px;
  font-size: 13px;
  color: #94a3b8;
}

.social-login {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  background: #ffffff;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  transition: all 0.3s ease;
}

.social-btn:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.auth-switch {
  text-align: center;
  margin-top: 20px;
  font-size: 15px;
  color: #64748b;
}

.switch-link {
  color: #ff6b35;
  text-decoration: none;
  font-weight: 600;
}

.switch-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 1024px) {
  .auth-container {
    grid-template-columns: 1fr;
  }
  
  .auth-branding {
    display: none;
  }
  
  .auth-form-wrapper {
    padding: 40px 24px;
  }
}
</style>
