<template>
  <div class="social-callback">
    <div v-if="status === 'loading'" class="callback-box">
      <div class="spinner"></div>
      <p>Đang xử lý đăng nhập...</p>
    </div>
    <div v-else-if="status === 'success'" class="callback-box">
      <div class="icon-success">✓</div>
      <p>Đăng nhập thành công! Đang chuyển hướng...</p>
    </div>
    <div v-else class="callback-box error">
      <div class="icon-error">✗</div>
      <p>{{ errorMsg }}</p>
      <button @click="closeWindow">Đóng</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const status = ref('loading')
const errorMsg = ref('')

onMounted(() => {
  const params = new URLSearchParams(window.location.search)
  const token = params.get('token')
  const user = params.get('user')
  const error = params.get('error')

  if (error) {
    status.value = 'error'
    errorMsg.value = decodeURIComponent(error)
    // Notify parent về lỗi
    if (window.opener) {
      window.opener.postMessage({ type: 'SOCIAL_LOGIN_ERROR', error: decodeURIComponent(error) }, window.location.origin)
    }
    return
  }

  if (token) {
    status.value = 'success'
    let userData = null
    try {
      userData = user ? JSON.parse(decodeURIComponent(user)) : null
    } catch {
      userData = null
    }

    // Gửi token về parent window
    if (window.opener) {
      window.opener.postMessage(
        { type: 'SOCIAL_LOGIN_SUCCESS', token, user: userData },
        window.location.origin
      )
      setTimeout(() => window.close(), 800)
    } else {
      // Fallback: không có popup, lưu token và redirect
      localStorage.setItem('token', token)
      if (userData) localStorage.setItem('user', JSON.stringify(userData))
      window.location.href = '/'
    }
    return
  }

  // Không có token và không có lỗi → redirect về login
  status.value = 'error'
  errorMsg.value = 'Không nhận được thông tin đăng nhập.'
  if (window.opener) {
    window.opener.postMessage({ type: 'SOCIAL_LOGIN_ERROR', error: errorMsg.value }, window.location.origin)
  }
})

const closeWindow = () => window.close()
</script>

<style scoped>
.social-callback {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: #f8fafc;
  font-family: Inter, sans-serif;
}

.callback-box {
  text-align: center;
  padding: 40px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  min-width: 280px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #ff6b35;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin { to { transform: rotate(360deg); } }

.icon-success {
  width: 56px;
  height: 56px;
  background: #10b981;
  color: #fff;
  border-radius: 50%;
  font-size: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.icon-error {
  width: 56px;
  height: 56px;
  background: #ef4444;
  color: #fff;
  border-radius: 50%;
  font-size: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.callback-box p {
  color: #475569;
  margin-bottom: 16px;
}

.callback-box button {
  padding: 8px 24px;
  background: #ff6b35;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}
</style>
