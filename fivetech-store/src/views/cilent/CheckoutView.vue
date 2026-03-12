<template>
  <div class="checkout-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/cart">Giỏ hàng</router-link>
          <span class="separator">/</span>
          <span class="current">Thanh toán</span>
        </nav>
        <h1 class="page-title">Thanh toán</h1>
        <p class="page-subtitle">Hoàn tất thông tin để đặt hàng</p>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <!-- Checkout Content -->
    <div class="checkout-content">
      <div class="container">
        <div class="checkout-steps">
          <div class="step-item completed">
            <span class="step-number">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            <span class="step-label">Giỏ hàng</span>
          </div>
          <div class="step-connector completed"></div>
          <div class="step-item active">
            <span class="step-number">2</span>
            <span class="step-label">Thanh toán</span>
          </div>
          <div class="step-connector"></div>
          <div class="step-item">
            <span class="step-number">3</span>
            <span class="step-label">Hoàn tất</span>
          </div>
        </div>

        <!-- Checkout Layout -->
        <div class="checkout-layout">
          <!-- Left Column: Forms -->
          <div class="checkout-form">
            <!-- Customer Info - Thông tin tự động lấy từ user đã đăng nhập -->
            <div class="checkout-section" v-if="authStore.isAuthenticated && authStore.user">
              <h3 class="checkout-section-title">
                <span class="section-icon info-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                Thông tin người nhận
              </h3>

              <div class="customer-info-display">
                <div class="info-row">
                  <span class="info-label">User ID:</span>
                  <span class="info-value">{{ authStore.user.id }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Họ và tên:</span>
                  <span class="info-value">{{ authStore.user.full_name }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Số điện thoại:</span>
                  <span class="info-value">{{ authStore.user.phone || 'Chưa cập nhật' }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Địa chỉ:</span>
                  <span class="info-value">{{ authStore.user.address || 'Chưa cập nhật' }}</span>
                </div>
              </div>
              
              <router-link to="/profile/edit" class="edit-profile-link">
                Cập nhật thông tin tại đây
              </router-link>
            </div>

            <!-- Thông tin cho khách chưa đăng nhập -->
            <div class="checkout-section" v-else>
              <h3 class="checkout-section-title">
                <span class="section-icon info-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                Thông tin khách hàng
              </h3>
              
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Họ và tên <span class="required">*</span></label>
                  <input 
                    type="text" 
                    class="form-input" 
                    v-model="customerInfo.name"
                    placeholder="Nguyễn Văn A"
                    :class="{ 'error': errors.name }"
                  />
                  <span class="form-error" v-if="errors.name">{{ errors.name }}</span>
                </div>

                <div class="form-group">
                  <label class="form-label">Số điện thoại <span class="required">*</span></label>
                  <input 
                    type="tel" 
                    class="form-input" 
                    v-model="customerInfo.phone"
                    placeholder="0912 345 678"
                    :class="{ 'error': errors.phone }"
                  />
                  <span class="form-error" v-if="errors.phone">{{ errors.phone }}</span>
                </div>

                <div class="form-group full-width">
                  <label class="form-label">Email <span class="required">*</span></label>
                  <input 
                    type="email" 
                    class="form-input" 
                    v-model="customerInfo.email"
                    placeholder="email@example.com"
                    :class="{ 'error': errors.email }"
                  />
                  <span class="form-error" v-if="errors.email">{{ errors.email }}</span>
                </div>

                <div class="form-group full-width">
                  <label class="form-label">Địa chỉ nhận hàng <span class="required">*</span></label>
                  <input 
                    type="text" 
                    class="form-input" 
                    v-model="customerInfo.address"
                    placeholder="Số nhà, đường, phường/xã, quận/huyện, thành phố"
                    :class="{ 'error': errors.address }"
                  />
                  <span class="form-error" v-if="errors.address">{{ errors.address }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon payment-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </span>
                Phương thức thanh toán
              </h3>

              <div class="payment-methods">
                <label 
                  class="payment-option" 
                  :class="{ selected: selectedPayment === 'cod' }"
                  @click="selectedPayment = 'cod'"
                >
                  <input type="radio" name="payment" value="cod" v-model="selectedPayment" />
                  <span class="payment-radio"></span>
                  <span class="payment-icon cod">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                  </span>
                  <div class="payment-details">
                    <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                    <div class="payment-desc">Trả tiền mặt khi nhận được hàng</div>
                  </div>
                </label>

                <!-- Có thể thêm các phương thức khác nếu backend hỗ trợ -->
              </div>
            </div>

            <!-- Order Note -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon note-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </span>
                Ghi chú đơn hàng
              </h3>
              <textarea 
                class="form-textarea" 
                placeholder="Ghi chú thêm cho đơn hàng (không bắt buộc)... VD: Giao giờ hành chính, gọi trước 30 phút..."
                v-model="orderNote"
                :disabled="loading"
              ></textarea>
            </div>
          </div>

          <!-- Right Column: Order Summary -->
          <div class="order-summary-sidebar">
            <div class="summary-header">
              <h3>Đơn hàng của bạn</h3>
              <span class="items-count">{{ cartStore.items.length }} sản phẩm</span>
            </div>

            <!-- Order Items -->
            <div class="order-items">
              <div class="order-item" v-for="item in cartStore.items" :key="item.id">
                <img 
                  :src="item.image || item.variant?.image_urls?.[0] || `https://via.placeholder.com/80?text=${encodeURIComponent(item.name || 'SP')}`" 
                  :alt="item.name" 
                  class="order-item-image" 
                />
                <div class="order-item-info">
                  <div class="order-item-name">{{ item.name }}</div>
                  <div class="order-item-variant">Phân loại: {{ item.variant?.name || 'Mặc định' }}</div>
                  <div class="order-item-qty">x{{ item.quantity }}</div>
                </div>
                <span class="order-item-price">{{ formatPrice(calculatedUnitPrice(item) * item.quantity) }}</span>
              </div>
            </div>

            <!-- Calculations -->
            <div class="summary-calculations">
              <div class="calc-row">
                <span>Tạm tính</span>
                <span class="calc-value">{{ formatPrice(cartStore.subtotal) }}</span>
              </div>
              <div class="calc-row discount" v-if="cartStore.hasDiscount">
                <span>Giảm giá</span>
                <span class="calc-value text-red-600">-{{ formatPrice(cartStore.discount) }}</span>
              </div>
              <div class="calc-row shipping">
                <span>Phí vận chuyển</span>
                <span class="calc-value" :class="{ free: cartStore.finalShippingFee === 0 }">
                  {{ cartStore.finalShippingFee === 0 ? 'Miễn phí' : formatPrice(cartStore.finalShippingFee) }}
                </span>
              </div>
            </div>

            <!-- Total -->
            <div class="summary-total">
              <span class="total-label">Tổng cộng</span>
              <span class="total-value">{{ formatPrice(cartStore.grandTotal) }}</span>
            </div>

            <!-- Place Order Button -->
            <button 
              class="place-order-btn" 
              @click="placeOrder"
              :disabled="loading || cartStore.isEmpty || !formValid"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <span v-if="!loading">Đặt hàng</span>
              <span v-else>Đang xử lý...</span>
            </button>

            <div class="order-secure-note">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Giao dịch được bảo mật SSL 256-bit
            </div>

            <router-link to="/cart" class="back-to-cart">← Quay lại giỏ hàng</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

const authStore = useAuthStore()
const router = useRouter()
const cartStore = useCartStore()

const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})

// Customer Info
const customerInfo = ref({
  name: '',
  phone: '',
  email: '',
  address: ''
})

// Shipping & Payment
const selectedPayment = ref('cod')
const orderNote = ref('')

// Computed: Kiểm tra form hợp lệ
const formValid = computed(() => {
  // Nếu đã đăng nhập → luôn hợp lệ
  if (authStore.isAuthenticated && authStore.user) {
    return true
  }
  
  // Nếu khách chưa đăng nhập → kiểm tra thông tin bắt buộc
  const { name, phone, email, address } = customerInfo.value
  return name?.trim() && phone?.trim() && email?.trim() && address?.trim()
})

// Tính giá đơn vị sản phẩm (hỗ trợ giá biến thể)
const calculatedUnitPrice = (item) => {
  if (!item) return 0
  // Giá gốc = product discount_price hoặc base_price
  const basePrice = Number(item.product?.discount_price || item.product?.base_price || 0)
  // Cộng thêm price_extra của variant nếu có
  const extra = (item.variant?.price_extra && Number(item.variant.price_extra) > 0) 
    ? Number(item.variant.price_extra) 
    : 0
  return basePrice + extra
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫'
}

// Load dữ liệu khi mount
onMounted(async () => {
  // Nếu giỏ hàng rỗng → chuyển về cart
  if (cartStore.isEmpty) {
    router.push('/cart')
    return
  }

  // Nếu đã đăng nhập → điền sẵn thông tin
  if (authStore.isAuthenticated && authStore.user) {
    customerInfo.value = {
      name: authStore.user.full_name || '',
      phone: authStore.user.phone || '',
      email: authStore.user.email || '',
      address: authStore.user.address || ''
    }
  }

  // Tải giỏ hàng (đã hỗ trợ guest)
  await cartStore.fetchCart()
})

// Đặt hàng
const placeOrder = async () => {
  // Validation cho khách chưa đăng nhập
  if (!authStore.isAuthenticated) {
    errors.value = {}
    
    if (!customerInfo.value.name?.trim()) {
      errors.value.name = 'Vui lòng nhập họ tên'
    }
    if (!customerInfo.value.phone?.trim()) {
      errors.value.phone = 'Vui lòng nhập số điện thoại'
    }
    if (!customerInfo.value.email?.trim()) {
      errors.value.email = 'Vui lòng nhập email'
    }
    if (!customerInfo.value.address?.trim()) {
      errors.value.address = 'Vui lòng nhập địa chỉ'
    }
    
    if (Object.keys(errors.value).length > 0) {
      errorMessage.value = 'Vui lòng điền đầy đủ thông tin!'
      return
    }
  }

  if (!formValid.value) {
    errorMessage.value = 'Không thể đặt hàng!'
    return
  }

  loading.value = true
  errorMessage.value = ''
  errors.value = {}

  try {
    // Chuẩn bị payload
    const payload = {
      items: cartStore.items.map(item => ({
        cart_item_id: item.id,
        quantity: item.quantity,
        variant_id: item.variant_id || item.variant?.variant_id || null
      })),
      payment_method: selectedPayment.value,
      note: orderNote.value.trim(),
      coupon_code: cartStore.couponCode?.trim() || null
    }

    // Nếu khách chưa đăng nhập → thêm thông tin khách hàng
    if (!authStore.isAuthenticated) {
      payload.customer_name = customerInfo.value.name.trim()
      payload.phone = customerInfo.value.phone.trim()
      payload.email = customerInfo.value.email.trim()
      payload.shipping_address = customerInfo.value.address.trim()
    }


    const res = await api.post('/orders', payload)

    // Thành công - chuyển sang trang hoàn tất
    router.push({
      name: 'OrderSuccess',
      query: { orderCode: res.data.order_code }
    })

    // Clear giỏ hàng
    await cartStore.clearCart()
    } catch (err) {

    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      errorMessage.value =
        Object.values(errors.value)[0]?.[0] ||
        'Dữ liệu không hợp lệ.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import '@/views/cilent/css/checkout.css';

/* Thêm style lỗi */
.input-error {
  border-color: var(--admin-danger) !important;
}

.error-text {
  color: var(--admin-danger);
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

.error-message {
  background: var(--admin-danger-soft);
  color: var(--admin-danger);
  padding: 12px 16px;
  border-radius: 8px;
  margin: 16px 0;
  text-align: center;
}
</style>
