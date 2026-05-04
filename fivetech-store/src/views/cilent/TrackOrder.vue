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
        <p class="page-subtitle">Nhập mã đơn hàng hoặc số điện thoại để kiểm tra trạng thái</p>
      </div>
    </div>

    <section class="track-section">
      <div class="container">

        <!-- Search Form -->
        <div class="search-card">
          <form @submit.prevent="handleSearch" class="search-form">
            <div class="input-group">
              <label class="form-label">Mã đơn hàng</label>
              <input
                v-model="form.order_code"
                type="text"
                class="form-input"
                :class="{ 'input-error': errors.order_code }"
                placeholder="VD: FT-20250503-0001"
                @input="clearError('order_code')"
              />
              <span v-if="errors.order_code" class="error-text">{{ errors.order_code }}</span>
            </div>

            <div class="divider"><span>hoặc</span></div>

            <div class="input-group">
              <label class="form-label">Số điện thoại</label>
              <input
                v-model="form.phone"
                type="tel"
                class="form-input"
                :class="{ 'input-error': errors.phone }"
                placeholder="VD: 0901234567"
                @input="clearError('phone')"
              />
              <span v-if="errors.phone" class="error-text">{{ errors.phone }}</span>
            </div>

            <div v-if="globalError" class="alert-error">
              <span class="alert-icon">⚠</span>
              {{ globalError }}
            </div>

            <button type="submit" class="btn-search" :disabled="loading">
              <span v-if="loading" class="spinner"></span>
              <span>{{ loading ? 'Đang tìm kiếm...' : 'Tra cứu đơn hàng' }}</span>
            </button>
          </form>
        </div>

        <!-- Order Result -->
        <div v-if="order" class="order-result">

          <!-- Status Banner -->
          <div class="status-banner" :class="`status-${order.status}`">
            <span class="status-icon">{{ statusIcon(order.status) }}</span>
            <div class="status-info">
              <p class="status-label">Trạng thái đơn hàng</p>
              <p class="status-text">{{ order.status_text }}</p>
            </div>
            <div class="order-code-badge">#{{ order.order_code }}</div>
          </div>

          <!-- Progress Bar -->
          <div v-if="order.status !== 'cancelled'" class="progress-wrap">
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: order.status_progress + '%' }"></div>
            </div>
            <div class="progress-steps">
              <span :class="{ active: order.status_progress >= 20 }">Chờ xác nhận</span>
              <span :class="{ active: order.status_progress >= 55 }">Đang xử lý</span>
              <span :class="{ active: order.status_progress >= 75 }">Đang giao</span>
              <span :class="{ active: order.status_progress >= 100 }">Hoàn thành</span>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-card">
              <h3 class="card-title">Thông tin đơn hàng</h3>
              <div class="info-row">
                <span class="info-label">Mã đơn hàng</span>
                <span class="info-value code">{{ order.order_code }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Ngày đặt</span>
                <span class="info-value">{{ order.created_at }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Thanh toán</span>
                <span class="info-value">{{ paymentMethodText(order.payment_method) }}</span>
              </div>
            </div>

            <div class="info-card">
              <h3 class="card-title">Thông tin khách hàng</h3>
              <div class="info-row">
                <span class="info-label">Họ tên</span>
                <span class="info-value">{{ order.customer_name }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Số điện thoại</span>
                <span class="info-value">{{ order.customer_phone }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Địa chỉ</span>
                <span class="info-value">{{ order.customer_address }}</span>
              </div>
            </div>
          </div>

          <!-- Items -->
          <div class="items-card">
            <h3 class="card-title">Sản phẩm đã đặt</h3>
            <div
              v-for="(item, idx) in order.items"
              :key="idx"
              class="item-row"
            >
              <div class="item-img-wrap">
                <img
                  v-if="item.image"
                  :src="item.image"
                  :alt="item.name"
                  class="item-img"
                  @error="(e) => (e.target as HTMLImageElement).src = '/images/default-product.jpg'"
                />
                <div v-else class="item-img-placeholder">📦</div>
              </div>
              <div class="item-info">
                <p class="item-name">{{ item.name }}</p>
                <p v-if="item.variant" class="item-variant">{{ item.variant }}</p>
                <p class="item-qty">x{{ item.quantity }}</p>
              </div>
              <div class="item-price">
                {{ formatPrice(item.subtotal) }}
              </div>
            </div>
          </div>

          <!-- Totals -->
          <div class="totals-card">
            <div class="total-row">
              <span>Tạm tính</span>
              <span>{{ formatPrice(order.total_amount) }}</span>
            </div>
            <div v-if="order.discount_amount > 0" class="total-row discount">
              <span>Giảm giá</span>
              <span>-{{ formatPrice(order.discount_amount) }}</span>
            </div>
            <div class="total-row shipping">
              <span>Phí vận chuyển</span>
              <span v-if="shippingFee > 0">{{ formatPrice(shippingFee) }}</span>
              <span v-else class="free-ship">Miễn phí</span>
            </div>
            <div class="total-row final">
              <span>Tổng thanh toán</span>
              <span>{{ formatPrice(order.final_amount) }}</span>
            </div>
          </div>

        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { trackOrder as trackOrderApi } from '@/api/index.js'

interface OrderItem {
  name: string
  variant: string | null
  quantity: number
  price: number
  subtotal: number
  image: string | null
}

interface Order {
  order_code: string
  status: string
  status_text: string
  status_progress: number
  customer_name: string
  customer_phone: string
  customer_address: string
  payment_method: string
  total_amount: number
  discount_amount: number
  final_amount: number
  created_at: string
  items: OrderItem[]
}

const loading = ref(false)
const globalError = ref('')
const order = ref<Order | null>(null)

const form = reactive({
  order_code: '',
  phone: '',
})

const errors = reactive({
  order_code: '',
  phone: '',
})

const shippingFee = computed(() => {
  if (!order.value) return 0
  const fee = order.value.final_amount - order.value.total_amount + (order.value.discount_amount ?? 0)
  return fee > 0 ? fee : 0
})

function clearError(field: string) {
  ;(errors as Record<string, string>)[field] = ''
  globalError.value = ''
}

function validate(): boolean {
  const code = form.order_code.trim()
  const phone = form.phone.trim()

  if (!code && !phone) {
    globalError.value = 'Vui lòng nhập mã đơn hàng hoặc số điện thoại.'
    return false
  }
  if (phone && !/^(0|\+84)[0-9]{9}$/.test(phone)) {
    errors.phone = 'Số điện thoại không hợp lệ (phải có 10 chữ số).'
    return false
  }
  return true
}

async function handleSearch() {
  order.value = null
  globalError.value = ''
  errors.order_code = ''
  errors.phone = ''

  if (!validate()) return

  const payload = form.order_code.trim()
    ? { order_code: form.order_code.trim() }
    : { phone: form.phone.trim() }

  loading.value = true
  try {
    const res = await trackOrderApi(payload)
    order.value = res.data.order
  } catch (err: unknown) {
    const e = err as { response?: { status: number; data: { message: string } } }
    if (e.response?.status === 404) {
      globalError.value = e.response.data.message || 'Không tìm thấy đơn hàng.'
    } else if (e.response?.status === 422) {
      globalError.value = e.response.data.message || 'Dữ liệu không hợp lệ.'
    } else {
      globalError.value = 'Có lỗi xảy ra. Vui lòng thử lại sau.'
    }
  } finally {
    loading.value = false
  }
}

function formatPrice(value: number): string {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(value)
}

function statusIcon(status: string): string {
  const icons: Record<string, string> = {
    pending: '⏳',
    confirmed: '✔️',
    processing: '⚙️',
    shipping: '🚚',
    delivered: '📦',
    completed: '✅',
    cancelled: '❌',
  }
  return icons[status] ?? '📋'
}

function paymentMethodText(method: string): string {
  const map: Record<string, string> = {
    cod: 'Thanh toán khi nhận hàng (COD)',
    vietqr: 'Chuyển khoản VietQR',
    bank: 'Chuyển khoản ngân hàng',
    momo: 'Ví MoMo',
    vnpay: 'VNPay',
    paypal: 'PayPal',
  }
  return map[method] ?? method.toUpperCase()
}
</script>

<style scoped>
.track-order-page {
  min-height: 100vh;
  background: #f8fafc;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  padding: 48px 0 36px;
  color: #fff;
}

.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb {
  font-size: 13px;
  color: #94a3b8;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.breadcrumb a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumb a:hover {
  color: #fff;
}

.separator {
  color: #475569;
}

.current {
  color: #e2e8f0;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #fff;
  margin-bottom: 8px;
}

.page-subtitle {
  font-size: 14px;
  color: #94a3b8;
}

/* Track Section */
.track-section {
  padding: 40px 0 60px;
}

/* Search Card */
.search-card {
  background: #fff;
  border-radius: 16px;
  padding: 36px 40px;
  box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
  max-width: 600px;
  margin: 0 auto 32px;
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.form-input {
  height: 48px;
  padding: 0 16px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-size: 15px;
  color: #111827;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.form-input:focus {
  border-color: #ff6b35;
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-input.input-error {
  border-color: #ef4444;
}

.error-text {
  font-size: 13px;
  color: #ef4444;
}

.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #9ca3af;
  font-size: 13px;
  font-weight: 500;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e5e7eb;
}

.alert-error {
  background: #fef2f2;
  border: 1.5px solid #fecaca;
  border-radius: 10px;
  padding: 12px 16px;
  font-size: 14px;
  color: #dc2626;
  display: flex;
  align-items: center;
  gap: 8px;
}

.alert-icon {
  font-size: 16px;
}

.btn-search {
  height: 52px;
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: opacity 0.2s, transform 0.1s;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-search:hover:not(:disabled) {
  opacity: 0.9;
  transform: translateY(-1px);
}

.btn-search:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Order Result */
.order-result {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Status Banner */
.status-banner {
  border-radius: 14px;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.status-pending   { background: #fff7ed; border: 1.5px solid #fed7aa; }
.status-confirmed { background: #eff6ff; border: 1.5px solid #bfdbfe; }
.status-processing { background: #faf5ff; border: 1.5px solid #e9d5ff; }
.status-shipping  { background: #eff6ff; border: 1.5px solid #93c5fd; }
.status-delivered { background: #f0fdf4; border: 1.5px solid #86efac; }
.status-completed { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
.status-cancelled { background: #fef2f2; border: 1.5px solid #fecaca; }

.status-icon {
  font-size: 28px;
  line-height: 1;
}

.status-info {
  flex: 1;
}

.status-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-text {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin-top: 2px;
}

.order-code-badge {
  font-size: 15px;
  font-weight: 700;
  color: #ff6b35;
  background: #fff;
  border: 1.5px solid #ff6b35;
  border-radius: 8px;
  padding: 6px 14px;
}

/* Progress */
.progress-wrap {
  background: #fff;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.progress-bar {
  height: 8px;
  background: #e5e7eb;
  border-radius: 99px;
  overflow: hidden;
  margin-bottom: 12px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #ff6b35, #f7931e);
  border-radius: 99px;
  transition: width 0.5s ease;
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #9ca3af;
  font-weight: 500;
}

.progress-steps span.active {
  color: #ff6b35;
  font-weight: 700;
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.info-card,
.items-card,
.totals-card {
  background: #fff;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.card-title {
  font-size: 14px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 14px;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  color: #6b7280;
  flex-shrink: 0;
  padding-top: 1px;
}

.info-value {
  color: #111827;
  font-weight: 600;
  text-align: right;
}

.info-value.code {
  color: #ff6b35;
  font-size: 15px;
}

/* Items */
.item-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 14px 0;
  border-bottom: 1px solid #f3f4f6;
}

.item-row:last-child {
  border-bottom: none;
}

.item-img-wrap {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
}

.item-img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.item-img-placeholder {
  width: 60px;
  height: 60px;
  background: #f3f4f6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
}

.item-variant {
  font-size: 13px;
  color: #6b7280;
  margin-bottom: 2px;
}

.item-qty {
  font-size: 13px;
  color: #9ca3af;
}

.item-price {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
  white-space: nowrap;
}

/* Totals */
.total-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: #374151;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.total-row:last-child {
  border-bottom: none;
}

.total-row.discount {
  color: #16a34a;
}

.total-row.shipping {
  color: #374151;
}

.free-ship {
  color: #16a34a;
  font-weight: 600;
}

.total-row.final {
  font-size: 17px;
  font-weight: 800;
  color: #111827;
  border-top: 2px solid #e5e7eb;
  margin-top: 8px;
  padding-top: 16px;
  border-bottom: none;
}

.total-row.final span:last-child {
  color: #ff6b35;
}

/* Responsive */
@media (max-width: 640px) {
  .search-card {
    padding: 24px 20px;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .status-banner {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .order-code-badge {
    align-self: flex-start;
  }
}
</style>
