<template>
  <div class="checkout-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/checkout">Thanh toán</router-link>
          <span class="separator">/</span>
          <span class="current">Chờ thanh toán VietQR</span>
        </nav>
        <h1 class="page-title">Thanh toán VietQR</h1>
        <p class="page-subtitle">Vui lòng quét mã QR và chuyển khoản. Đơn hàng sẽ tự động cập nhật sau 1-2 phút.</p>
      </div>
    </div>

    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Đang tải thông tin đơn hàng...</p>
    </div>

    <div v-else-if="error" class="error-container">
      <h2>Có lỗi xảy ra</h2>
      <p>{{ error }}</p>
      <div class="error-actions">
        <router-link to="/account?tab=orders" class="btn-primary">Xem đơn hàng</router-link>
        <button @click="fetchOrderDetails" class="btn-secondary">Thử lại</button>
      </div>
    </div>

    <div v-else-if="!orderData" class="error-container">
      <h2>Không tìm thấy đơn hàng</h2>
      <router-link to="/checkout" class="btn-primary">Quay lại thanh toán</router-link>
    </div>

    <div v-else-if="orderData.payment_status === 'paid'" class="success-redirect">
      <div class="checkmark-circle-large">
        <svg class="checkmark-icon-large" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <h2>Thanh toán thành công!</h2>
      <p>Đang chuyển đến trang đơn hàng...</p>
      <router-link :to="{ name: 'OrderSuccess', query: { orderCode: orderCode } }" class="btn-primary">Xem đơn hàng</router-link>
    </div>

    <div v-else class="payment-pending-page">
      <div class="container">
        <!-- Order Info -->
        <div class="pending-order-info">
          <h2>Đơn hàng #{{ orderData.order_code }}</h2>
          <div class="pending-status pending">
            <span class="status-dot"></span>
            <span>Chờ thanh toán</span>
          </div>
          <p class="pending-amount">Số tiền cần thanh toán: <strong>{{ formatPrice(orderData.final_amount) }}</strong></p>
        </div>

        <!-- QR Payment -->
        <div class="qr-payment-section">
          <div class="qr-container">
            <img :src="qrUrl" alt="VietQR Code" class="qr-code" @error="qrError = true" />
            <div v-if="qrError" class="qr-error">
              <p>Không tải được mã QR. Vui lòng liên hệ admin.</p>
            </div>
            <div class="qr-instructions">
              <h4>Hướng dẫn thanh toán:</h4>
              <ul>
                <li>Mở app ngân hàng → Chọn quét mã QR</li>
                <li>Chuyển đúng <strong>{{ formatPrice(orderData.final_amount) }}</strong></li>
                <li>Nội dung: <code>Thanh toan {{ orderData.order_code }}</code></li>
              </ul>
            </div>
          </div>

          <div class="bank-info">
            <h4>Thông tin nhận tiền:</h4>
            <div class="bank-details">
              <div class="bank-row"><span>Ngân hàng:</span> <strong>Vietcombank (VCB)</strong></div>
              <div class="bank-row"><span>Số TK:</span> <strong>1021850576</strong></div>
              <div class="bank-row"><span>Chủ TK:</span> <strong>NGUYỄN TIẾN THỊNH</strong></div>
            </div>
          </div>
        </div>

        <!-- Timer & Actions -->
        <div class="pending-actions">
          <div class="payment-timer">
            <span>Tự động kiểm tra sau:</span>
            <div class="timer">{{ formatTime(pollCountdown) }}</div>
          </div>
          <div class="action-buttons">
            <button @click="confirmPayment" :disabled="confirmLoading" class="btn-confirm">
              <span v-if="confirmLoading">Đang xác nhận...</span>
              <span v-else>Đã chuyển khoản, xác nhận ngay</span>
            </button>
            <router-link :to="{ name: 'OrderSuccess', query: { orderCode: orderCode } }" class="btn-view-order">
              Xem chi tiết đơn hàng →
            </router-link>
          </div>
          <div class="timeout-warning" v-if="pollCountdown < 300">
            <svg class="warning-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Hơn 15 phút chưa thanh toán? <router-link to="/account?tab=orders">Xem danh sách đơn hàng</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'

const route = useRoute()
const router = useRouter()
const orderCode = computed(() => route.query.orderCode)

const loading = ref(true)
const error = ref('')
const orderData = ref(null)
const qrError = ref(false)
const confirmLoading = ref(false)
const pollInterval = ref(null)
const pollCountdown = ref(5) // Poll every 5s

// Fetch order
const fetchOrderDetails = async () => {
  if (!orderCode.value) return

  loading.value = true
  try {
    const res = await api.get(`/orders/${orderCode.value}`)
    orderData.value = res.data
    
    // Auto redirect if paid
    if (orderData.value.payment_status === 'paid') {
      router.push({ name: 'OrderSuccess', query: { orderCode: orderData.value.order_code } })
      return
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Không thể tải thông tin đơn hàng'
  } finally {
    loading.value = false
  }
}

// Manual confirm payment
const confirmPayment = async () => {
  confirmLoading.value = true
  try {
    await api.post(`/orders/${orderCode.value}/confirm-payment`, { status: 'paid' })
    fetchOrderDetails() // Refresh
  } catch (err) {
    alert('Xác nhận thất bại: ' + (err.response?.data?.message || 'Lỗi server'))
  } finally {
    confirmLoading.value = false
  }
}

// QR URL
const qrUrl = computed(() => {
  if (!orderData.value) return ''
  const amount = Math.round(orderData.value.final_amount || 0)
  const addInfo = encodeURIComponent(`Thanh toan ${orderData.value.order_code}`)
  return `https://img.vietqr.io/image/VCB-1021850576-compact2.png?amount=${amount}&addInfo=${addInfo}&accountName=NGUYEN%20TIEN%20THINH`
})

// Formatters
const formatPrice = (price) => new Intl.NumberFormat('vi-VN').format(price || 0) + '₫'
const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

// Poll timer
const startPolling = () => {
  fetchOrderDetails()
  pollInterval.value = setInterval(() => {
    pollCountdown.value--
    if (pollCountdown.value <= 0) {
      fetchOrderDetails()
      pollCountdown.value = 5
    }
  }, 1000)
}

onMounted(() => {
  if (orderCode.value) {
    startPolling()
  }
})

onUnmounted(() => {
  if (pollInterval.value) clearInterval(pollInterval.value)
})
</script>

<style scoped>
@import '@/views/cilent/css/checkout.css';
@import '@/views/cilent/css/payment-pending.css';


.payment-pending-page {
  min-height: 80vh;
}

.pending-order-info {
  text-align: center;
  margin-bottom: 3rem;
  padding: 2rem 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 20px;
  max-width: 600px;
  margin: 0 auto 3rem;
}

.pending-order-info h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.pending-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.5rem;
  background: rgba(255,255,255,0.2);
  border-radius: 50px;
  font-weight: 500;
  backdrop-filter: blur(10px);
}

.status-dot {
  width: 10px;
  height: 10px;
  background: #facc15;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

.pending-amount {
  font-size: 1.3rem;
  margin-top: 1rem;
  opacity: 0.9;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.qr-payment-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  max-width: 1000px;
  margin: 0 auto 3rem;
}

.qr-container {
  text-align: center;
}

.qr-code {
  width: 250px;
  height: 250px;
  border: 3px solid #e5e7eb;
  border-radius: 16px;
  margin-bottom: 1.5rem;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.qr-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid #dc2626;
}

.qr-instructions {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 12px;
}

.qr-instructions h4 {
  margin-bottom: 1rem;
  color: #1e293b;
}

.qr-instructions ul {
  list-style: none;
  padding: 0;
}

.qr-instructions li {
  padding: 0.5rem 0;
  position: relative;
  padding-left: 1.5rem;
}

.qr-instructions li::before {
  content: '→';
  position: absolute;
  left: 0;
  color: #3b82f6;
  font-weight: bold;
}

.bank-info {
  background: white;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.bank-info h4 {
  margin-bottom: 1.5rem;
  color: #1e293b;
}

.bank-row {
  display: flex;
  justify-content: space-between;
  padding: 1rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.bank-row:last-child {
  border-bottom: none;
}

.bank-row span:first-child {
  opacity: 0.7;
}

.pending-actions {
  text-align: center;
  padding: 2rem;
  max-width: 600px;
  margin: 0 auto;
}

.payment-timer {
  background: #f8fafc;
  padding: 1rem 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  display: inline-flex;
  align-items: center;
  gap: 1rem;
  font-weight: 500;
}

.timer {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-size: 1.2rem;
  font-weight: 700;
  min-width: 80px;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 1.5rem;
}

.btn-confirm, .btn-view-order {
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-confirm {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
}

.btn-confirm:hover:not(:disabled), .btn-confirm:focus {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.btn-confirm:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-view-order {
  background: #f8fafc;
  color: #3b82f6;
  border: 2px solid #e2e8f0;
}

.btn-view-order:hover {
  background: #e2e8f0;
}

.timeout-warning {
  background: #fef3c7;
  color: #d97706;
  padding: 1rem;
  border-radius: 8px;
  border-left: 4px solid #f59e0b;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.warning-icon {
  flex-shrink: 0;
}

.loading-container, .error-container, .success-redirect {
  min-height: 50vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 2rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.success-redirect {
  color: #059669;
}

.checkmark-circle-large {
  width: 120px;
  height: 120px;
  background: #d1fae5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
}

.checkmark-icon-large {
  color: #10b981;
  width: 60px;
  height: 60px;
}

@media (max-width: 768px) {
  .qr-payment-section {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .qr-code {
    width: 200px;
    height: 200px;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>

