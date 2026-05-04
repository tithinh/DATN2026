<template>
  <div class="order-detail-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/account">Tài khoản</router-link>
          <span class="separator">/</span>
          <span class="current">Chi tiết đơn hàng</span>
        </nav>
        <h1 class="page-title">Chi tiết đơn hàng</h1>
      </div>
    </div>

    <!-- Main Content -->
    <section class="detail-section">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Đang tải thông tin đơn hàng...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <p>{{ error }}</p>
          <router-link to="/account" class="btn-primary">Quay lại tài khoản</router-link>
        </div>

        <!-- Order Detail -->
        <div v-else class="order-detail">
          <!-- Order Header -->
          <div class="order-header-card">
            <div class="order-info">
              <div class="order-code">
                <span class="label">Mã đơn hàng:</span>
                <span class="value">{{ order.order_code }}</span>
              </div>
              <div class="order-date">
                <span class="label">Ngày đặt:</span>
                <span class="value">{{ formatDate(order.created_at) }}</span>
              </div>
            </div>
            <div class="order-status-wrapper">
              <span class="order-status" :class="order.status">{{ order.status_text }}</span>
            </div>
          </div>

          <!-- Progress Steps -->
          <div class="order-progress" v-if="order.status !== 'completed'">
            <div class="progress-track">
              <div class="progress-fill" :style="{ width: order.progress + '%' }"></div>
            </div>
            <div class="progress-steps">
              <div class="step" :class="{ completed: order.progress >= 33 }">
                <span class="step-dot"></span>
                <span class="step-label">Chờ xử lý</span>
              </div>
              <div class="step" :class="{ completed: order.progress >= 66 }">
                <span class="step-dot"></span>
                <span class="step-label">Đang giao</span>
              </div>
              <div class="step" :class="{ completed: order.progress >= 100 }">
                <span class="step-dot"></span>
                <span class="step-label">Hoàn thành</span>
              </div>
            </div>
          </div>

          <!-- Order Info Grid -->
          <div class="order-info-grid">
            <!-- Customer Info -->
            <div class="info-card">
              <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                Thông tin khách hàng
              </h3>
              <div class="card-content">
                <div class="info-row">
                  <span class="label">Họ tên:</span>
                  <span class="value">{{ order.customer?.name }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Số điện thoại:</span>
                  <span class="value">{{ order.customer?.phone }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Email:</span>
                  <span class="value">{{ order.customer?.email }}</span>
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="info-card">
              <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                Địa chỉ giao hàng
              </h3>
              <div class="card-content">
                <p class="address">{{ order.shipping_address || order.customer?.address }}</p>
                <p v-if="order.customer?.district" class="district">{{ order.customer?.district }}</p>
              </div>
            </div>

            <!-- Payment Info -->
            <div class="info-card">
              <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                Thanh toán
              </h3>
              <div class="card-content">
                <div class="info-row">
                  <span class="label">Phương thức:</span>
                  <span class="value">{{ getPaymentMethodText(order.payment_method) }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Trạng thái:</span>
                  <span class="value status-paid">Chưa thanh toán</span>
                </div>
              </div>
            </div>

            <!-- Shipping Method -->
            <!-- <div class="info-card">
              <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="13" x="4" y="5" rx="2"/><polygon points="4 5 2 5 2 19 16 19 16 13 4 13"/><line x1="6" x2="9" y1="9" y2="9"/><line x1="10" x2="10" y1="9" y2="9"/></svg>
                Vận chuyển
              </h3>
              <div class="card-content">
                <div class="info-row">
                  <span class="label">Phương thức:</span>
                  <span class="value">{{ getShippingMethodText(order.shipping_method) }}</span>
                </div>
              </div>
            </div> -->
          </div>

          <!-- Order Items -->
          <div class="order-items-section">
            <h3 class="section-title">Danh sách sản phẩm</h3>
            <div class="items-list">
              <div class="item-card" v-for="item in order.items" :key="item.id">
                <div class="item-image">
                  <img :src="storageUrl(item.product?.thumbnail || item.image || item.variant?.image_urls?.[0])"/>
                </div>
                <div class="item-info">
                  <h4 class="item-name">{{ item.name }}</h4>
                  <p class="item-variant" v-if="item.variant">{{ item.variant }}</p>
                  <p class="item-variant" v-else>Mặc định</p>
                </div>
                <div class="item-price">
                  <span class="price">{{ formatPrice(item.price) }}</span>
                  <span class="quantity">x{{ item.quantity }}</span>
                </div>
                <div class="item-total">
                  <span class="total-price">{{ formatPrice(item.price * item.quantity) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="order-summary">
            <div class="summary-row">
              <span class="label">Tạm tính:</span>
              <span class="value">{{ formatPrice(order.total_amount) }}</span>
            </div>
            <div class="summary-row">
              <span class="label">Phí vận chuyển:</span>
              <span class="value">{{ order.shipping_fee > 0 ? formatPrice(order.shipping_fee) : 'Miễn phí' }}</span>
            </div>
            <div class="summary-row discount" v-if="order.discount_amount > 0">
              <span class="label">Giảm giá:</span>
              <span class="value">-{{ formatPrice(order.discount_amount) }}</span>
            </div>
            <div class="summary-row total">
              <span class="label">Tổng cộng:</span>
              <span class="value">{{ formatPrice(order.final_amount) }}</span>
            </div>
          </div>

          <!-- Order Actions -->
          <div class="order-actions">
            <router-link to="/account" class="btn-back">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
              Quay lại
            </router-link>
            <button 
              v-if="order.status === 'pending'" 
              class="btn-cancel"
              @click="cancelOrder"
            >
              Hủy đơn hàng
            </button>
            <button 
              v-if="order.status === 'shipping'" 
              class="btn-confirm"
              @click="confirmReceived"
            >
              Xác nhận đã nhận hàng
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { storageUrl } from '@/utils/image'
import api from '@/api'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const loading = ref(true)
const error = ref(null)
const order = ref(null)

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫'
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getPaymentMethodText = (method) => {
  const methods = {
    'cod': 'Tiền mặt (COD)',
    'bank': 'Chuyển khoản',
    'momo': 'MoMo',
    'vnpay': 'VNPay',
    'paypal': 'PayPal'
  }
  return methods[method] || method
}

const getShippingMethodText = (method) => {
  const methods = {
    'standard': 'Giao hàng tiêu chuẩn',
    'express': 'Giao hàng nhanh',
    'same-day': 'Giao hàng trong ngày'
  }
  return methods[method] || method
}

const fetchOrderDetail = async () => {
  try {
    loading.value = true
    error.value = null
    
    const orderId = route.params.id
    const response = await api.get(`/orders/${orderId}`)
    order.value = response.data
  } catch (err) {
    console.error('Fetch order detail failed:', err)
    error.value = 'Không thể tải thông tin đơn hàng. Vui lòng thử lại sau.'
  } finally {
    loading.value = false
  }
}

const cancelOrder = async () => {
  if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
    return
  }
  
  try {
    const orderId = route.params.id
    await api.post(`/orders/${orderId}/cancel`)
    alert('Hủy đơn hàng thành công!')
    fetchOrderDetail()
  } catch (err) {
    console.error('Cancel order failed:', err)
    alert('Không thể hủy đơn hàng. Vui lòng thử lại sau.')
  }
}

const confirmReceived = async () => {
  try {
    const orderId = route.params.id
    await api.post(`/orders/${orderId}/confirm-received`)
    alert('Xác nhận đã nhận hàng thành công!')
    fetchOrderDetail()
  } catch (err) {
    console.error('Confirm received failed:', err)
    alert('Không thể xác nhận. Vui lòng thử lại sau.')
  }
}

onMounted(() => {
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }
  fetchOrderDetail()
})
</script>

<style scoped>
.order-detail-page {
  min-height: 100vh;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 40px 0;
  margin-bottom: 30px;
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
  margin: 0;
}

.detail-section {
  padding-bottom: 60px;
}

/* Loading & Error States */
.loading-state,
.error-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.error-state .btn-primary {
  margin-top: 20px;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Order Header Card */
.order-header-card {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.order-info {
  display: flex;
  gap: 40px;
}

.order-code .label,
.order-date .label {
  display: block;
  font-size: 13px;
  color: #64748b;
  margin-bottom: 4px;
}

.order-code .value {
  font-size: 20px;
  font-weight: 700;
  color: #0f172a;
}

.order-date .value {
  font-size: 15px;
  color: #0f172a;
}

.order-status {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
}

.order-status.pending {
  background: #fef3c7;
  color: #d97706;
}

.order-status.shipping {
  background: #dbeafe;
  color: #2563eb;
}

.order-status.completed {
  background: #dcfce7;
  color: #16a34a;
}

/* Progress Steps */
.order-progress {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.progress-track {
  height: 4px;
  background: #e2e8f0;
  border-radius: 2px;
  margin-bottom: 24px;
  position: relative;
}

.progress-fill {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #0f172a);
  border-radius: 2px;
  transition: width 0.5s ease;
}

.progress-steps {
  display: flex;
  justify-content: space-between;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.step-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #e2e8f0;
  transition: all 0.3s ease;
}

.step.completed .step-dot {
  background: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
}

.step-label {
  font-size: 12px;
  color: #64748b;
}

.step.completed .step-label {
  color: #0f172a;
  font-weight: 500;
}

/* Order Info Grid */
.order-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.info-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 16px 0;
}

.card-title svg {
  color: #3b82f6;
}

.card-content .info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

.card-content .info-row:last-child {
  border-bottom: none;
}

.card-content .label {
  font-size: 14px;
  color: #64748b;
}

.card-content .value {
  font-size: 14px;
  color: #0f172a;
  font-weight: 500;
}

.card-content .address,
.card-content .district {
  margin: 0;
  font-size: 14px;
  color: #0f172a;
  line-height: 1.6;
}

.status-paid {
  color: #16a34a !important;
}

/* Order Items Section */
.order-items-section {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 20px 0;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.item-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 12px;
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 4px 0;
}

.item-variant {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.item-price {
  text-align: right;
}

.item-price .price {
  display: block;
  font-size: 14px;
  color: #0f172a;
  font-weight: 500;
}

.item-price .quantity {
  font-size: 13px;
  color: #64748b;
}

.item-total {
  text-align: right;
  min-width: 100px;
}

.total-price {
  font-size: 16px;
  font-weight: 700;
  color: #0f172a;
}

/* Order Summary */
.order-summary {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  font-size: 15px;
}

.summary-row .label {
  color: #64748b;
}

.summary-row .value {
  color: #0f172a;
  font-weight: 500;
}

.summary-row.discount .value {
  color: #16a34a;
}

.summary-row.total {
  border-top: 2px solid #f1f5f9;
  margin-top: 10px;
  padding-top: 16px;
}

.summary-row.total .label {
  font-size: 16px;
  font-weight: 600;
  color: #0f172a;
}

.summary-row.total .value {
  font-size: 20px;
  font-weight: 700;
  color: #ff6b35;
}

/* Order Actions */
.order-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: #f1f5f9;
  color: #0f172a;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-back:hover {
  background: #e2e8f0;
}

.btn-cancel {
  padding: 12px 24px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #fecaca;
}

.btn-confirm {
  padding: 12px 24px;
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .order-info-grid {
    grid-template-columns: 1fr;
  }
  
  .order-info {
    flex-direction: column;
    gap: 16px;
  }
  
  .item-card {
    flex-wrap: wrap;
  }
  
  .item-total {
    width: 100%;
    text-align: left;
    margin-top: 8px;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .order-actions button,
  .order-actions a {
    width: 100%;
    justify-content: center;
  }
}
</style>
