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
    
    <!-- Success Message -->
    <div v-if="successMessage" class="success-message" style="background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin: 16px 0; text-align: center; border: 1px solid #a7f3d0;">
      {{ successMessage }}
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
            <!-- Thông tin người nhận -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon info-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                Thông tin người nhận
              </h3>

              <div class="form-grid">
                <div class="form-group" v-show="true">
                  <label class="form-label">Họ và tên <span class="required">*</span></label>
                  <input
                    type="text"
                    class="form-input"
                    v-model="customerInfo.name"
                    placeholder="Nhập họ và tên của bạn"
                    :class="{ 'error': errors.name }"
                    :disabled="authStore.isAuthenticated && !!authStore.user?.full_name"
                  />
                  <span class="form-error" v-if="errors.name">{{ errors.name }}</span>
                </div>

                <div class="form-group" v-show="true">
                  <label class="form-label">Số điện thoại <span class="required">*</span></label>
                  <input
                    type="tel"
                    class="form-input"
                    v-model="customerInfo.phone"
                    placeholder="Nhập số điện thoại (10 số)"
                    :class="{ 'error': errors.phone }"
                  />
                  <span class="form-error" v-if="errors.phone">{{ errors.phone }}</span>
                </div>

                <div class="form-group full-width" v-show="true">
                  <label class="form-label">Email <span class="required">*</span></label>
                  <input
                    type="email"
                    class="form-input"
                    v-model="customerInfo.email"
                    placeholder="email@example.com"
                    :disabled="authStore.isAuthenticated && !!authStore.user?.email"
                  />
                  <span class="form-error" v-if="errors.email">{{ errors.email }}</span>
                </div>

                <div class="form-group full-width" v-show="true">
                  <label class="form-label">Địa chỉ nhận hàng <span class="required">*</span></label>
                  <input
                    type="text"
                    class="form-input"
                    v-model="customerInfo.address"
                    :placeholder="authStore.isAuthenticated && authStore.user?.address ? 'Địa chỉ hiện tại: ' + authStore.user.address : 'Số nhà, đường, phường/xã, quận/huyện, thành phố'"
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
                    <div class="payment-desc">Trả tiền mặt khi nhận hàng</div>
                  </div>
                </label>

                <label
                  class="payment-option"
                  :class="{ selected: selectedPayment === 'vietqr' }"
                  @click="selectedPayment = 'vietqr'"
                >
                  <input type="radio" name="payment" value="vietqr" v-model="selectedPayment" />
                  <span class="payment-radio"></span>
                  <span class="payment-icon vietqr">
                    <span class="payment-badge vietqr-badge">VietQR</span>
                  </span>
                  <div class="payment-details">
                    <div class="payment-name">Chuyển khoản VietQR</div>
                    <div class="payment-desc">Vietcombank · 1021850576 · Nguyễn Tiến Thịnh</div>
                  </div>
                </label>
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
      :src="getOrderItemImage(item)"
      :alt="item.product?.name || item.name" 
      class="order-item-image" 
      @error="e => e.target.src = '/images/default-product.jpg'"
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
                <span>Giảm giá <span class="coupon-tag">{{ cartStore.couponCode }}</span></span>
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
import { storageUrl } from '@/utils/image'

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
const successMessage = ref('')

// Computed: Kiểm tra form hợp lệ
const formValid = computed(() => {
  const { name, phone, address } = customerInfo.value
  const nameValid = !!name?.trim()
  const phoneValid = !!phone?.trim() && phone.trim().length === 10
  const addressValid = !!address?.trim()
  console.log('Form validation:', {nameValid, phoneValid, addressValid, name: name?.trim(), phone: phone?.trim().length, addressLength: address?.trim().length})
  return nameValid && phoneValid && addressValid
})


// Tính giá đơn vị sản phẩm (hỗ trợ giá biến thể)
const calculatedUnitPrice = (item) => {
  if (!item) return 0
  // Giá gốc = product discount_price hoặc base_price
  const basePrice = Number(item.product?.discount_price || item.product?.base_price || 0)
  // Cộng thêm price_extra của variant nếu có
  const extra = item.variant?.price_extra ? Number(item.variant.price_extra) : 0
  return basePrice + extra
}

const getOrderItemImage = (item) => {
  if (!item) return '/images/default-product.jpg'

  // Variant image first
  let urls = item.variant?.image_urls || []
  if (typeof urls === 'string') {
    try {
      urls = JSON.parse(urls)
    } catch {
      urls = []
    }
  }
  if (Array.isArray(urls) && urls.length > 0) {
    return storageUrl(urls[0])
  }

  // Fallback
  return storageUrl(item.product?.thumbnail || item.image || '/images/default-product.jpg')
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

  // Nếu đã đăng nhập → điền sẵn thông tin + load địa chỉ
  if (authStore.isAuthenticated && authStore.user) {
    customerInfo.value = {
      name: authStore.user.full_name || '',
      phone: authStore.user.phone || '',
      email: authStore.user.email || '',
      address: authStore.user.address || ''
    }

    // Skip addresses - use user info only
  } 


  // Tải giỏ hàng (đã hỗ trợ guest)
  await cartStore.fetchCart()
})

// Đặt hàng
    const placeOrder = async () => {
      console.log('🔥 PLACE ORDER CLICKED!')
      console.log('cartStore.items:', cartStore.items)
      console.log('formValid:', formValid.value)
      console.log('loading:', loading.value)
      console.log('cartStore.isEmpty:', cartStore.isEmpty)
      
      // Force refresh cart before checkout (ensure latest IDs post-merge)
      await cartStore.fetchCart()
      
      console.log('cart after fetch:', cartStore.items)
      
      errors.value = {}
      errorMessage.value = ''

  // Validate các trường bắt buộc
  if (!customerInfo.value.name?.trim()) {
    errors.value.name = 'Vui lòng nhập họ tên'
    document.querySelector('.form-group input[placeholder*="tên"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
    document.querySelector('.form-group input[placeholder*="tên"]')?.focus()
    return
  }
  if (!customerInfo.value.phone?.trim() || customerInfo.value.phone.trim().length !== 10) {
    errors.value.phone = 'Số điện thoại phải đúng 10 ký tự'
    document.querySelector('.form-group input[placeholder*="điện thoại"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
    document.querySelector('.form-group input[placeholder*="điện thoại"]')?.focus()
    return
  }
  if (!customerInfo.value.address?.trim()) {
    const addrInput = document.querySelector('.form-group input[placeholder*="Số nhà, đường"]')
    addrInput?.scrollIntoView({ behavior: 'smooth', block: 'center' })
    addrInput?.focus()
    errorMessage.value = '❌ Không thể đặt hàng khi địa chỉ trống!'
    return
  }
  if (!customerInfo.value.email?.trim()) {
    errors.value.email = 'Vui lòng nhập email để nhận thông báo đơn hàng'
    return
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(customerInfo.value.email.trim())) {
    errors.value.email = 'Email không hợp lệ'
    return
  }

  loading.value = true

  try {
    const payload = {
      items: cartStore.items.map(item => ({
        cart_item_id: item.id,
        quantity: item.quantity,
        variant_id: item.variant_id || item.variant?.variant_id || null
      })),
      payment_method: selectedPayment.value,
      shipping_address: customerInfo.value.address.trim(),
      note: orderNote.value.trim(),
      coupon_code: cartStore.couponCode?.trim() || null,
      customer_name: customerInfo.value.name.trim(),
      phone: customerInfo.value.phone.trim(),
      email: customerInfo.value.email.trim(),
    }

    const res = await api.post('/orders', payload)

    // Conditional redirect based on payment_status
    const targetRoute = res.data.payment_status === 'paid' ? 'OrderSuccess' : 'PaymentPending'
    router.push({
      name: targetRoute,
      query: { orderCode: res.data.order_code }
    })

    await cartStore.clearCart()

  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
      errorMessage.value =
        Object.values(errors.value)[0]?.[0] || 'Dữ liệu không hợp lệ.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Đặt hàng thất bại, vui lòng thử lại!'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import '@/views/cilent/css/checkout.css';

/* Saved addresses */
.saved-addresses {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 8px;
}

.saved-address-option {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
}

.saved-address-option:hover { border-color: #f97316; background: #fff7ed; }
.saved-address-option.selected { border-color: #f97316; background: #fff7ed; }

.saved-address-option input[type="radio"] { display: none; }

.addr-radio {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid #cbd5e1;
  flex-shrink: 0;
  margin-top: 2px;
  transition: border-color 0.2s;
}
.saved-address-option.selected .addr-radio {
  border-color: #f97316;
  background: radial-gradient(circle, #f97316 40%, transparent 45%);
}

.addr-info { flex: 1; }
.addr-name { font-weight: 600; font-size: 14px; color: #1e293b; margin-right: 8px; }
.addr-phone { font-size: 13px; color: #64748b; margin-right: 8px; }
.addr-default {
  font-size: 11px; font-weight: 600; color: #f97316;
  background: #fff7ed; border: 1px solid #fed7aa;
  padding: 1px 7px; border-radius: 20px;
}
.addr-text { font-size: 13px; color: #475569; margin: 4px 0 0; }

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

.payment-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.vietqr-badge {
  background: #e8133a;
  color: #fff;
}

.missing-info-hint {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 14px;
  padding: 10px 14px;
  background: #fff7ed;
  border: 1px solid #fed7aa;
  border-radius: 8px;
  font-size: 13px;
  color: #c2410c;
  font-weight: 500;
}

.form-input:disabled {
  background: #f8fafc;
  color: #64748b;
  cursor: not-allowed;
  border-color: #e2e8f0;
}

.form-error {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #ef4444;
}

.coupon-tag {
  display: inline-block;
  margin-left: 6px;
  padding: 1px 6px;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
}
</style>
