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

    <!-- Checkout Steps -->
    <div class="checkout-content">
      <div class="container">
        <div class="checkout-steps">
          <div class="step-item completed">
            <span class="step-number">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
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
            <!-- Customer Info -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon info-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                Thông tin người nhận
              </h3>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Họ và tên <span class="required">*</span></label>
                  <input 
                    type="text" 
                    class="form-input" 
                    placeholder="Nguyễn Văn A" 
                    v-model="customerInfo.name" 
                    :disabled="loading"
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Số điện thoại <span class="required">*</span></label>
                  <input 
                    type="tel" 
                    class="form-input" 
                    placeholder="0912 345 678" 
                    v-model="customerInfo.phone" 
                    :disabled="loading"
                  />
                </div>
              </div>

              <div class="form-row full">
                <div class="form-group">
                  <label class="form-label">Email <span class="required">*</span></label>
                  <input 
                    type="email" 
                    class="form-input" 
                    placeholder="email@example.com" 
                    v-model="customerInfo.email" 
                    :disabled="loading"
                  />
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Tỉnh / Thành phố <span class="required">*</span></label>
                  <select class="form-input form-select" v-model="customerInfo.city" :disabled="loading">
                    <option value="">Chọn tỉnh/thành phố</option>
                    <!-- Thay bằng API lấy tỉnh/thành nếu có -->
                    <option value="hanoi">Hà Nội</option>
                    <option value="hcm">TP. Hồ Chí Minh</option>
                    <option value="danang">Đà Nẵng</option>
                    <option value="haiphong">Hải Phòng</option>
                    <option value="cantho">Cần Thơ</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Quận / Huyện <span class="required">*</span></label>
                  <select class="form-input form-select" v-model="customerInfo.district" :disabled="loading">
                    <option value="">Chọn quận/huyện</option>
                    <!-- Có thể load động theo city -->
                    <option value="q1">Quận 1</option>
                    <option value="q2">Quận 2</option>
                    <option value="bthanh">Bình Thạnh</option>
                    <option value="govap">Gò Vấp</option>
                  </select>
                </div>
              </div>

              <div class="form-row full">
                <div class="form-group">
                  <label class="form-label">Địa chỉ cụ thể <span class="required">*</span></label>
                  <input 
                    type="text" 
                    class="form-input" 
                    placeholder="Số nhà, tên đường, phường/xã" 
                    v-model="customerInfo.address" 
                    :disabled="loading"
                  />
                </div>
              </div>
            </div>

            <!-- Shipping Method -->
            <!-- <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon shipping-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </span>
                Phương thức vận chuyển
              </h3>

              <div class="shipping-methods">
                <label 
                  class="shipping-option" 
                  :class="{ selected: selectedShipping === 'standard' }"
                  @click="selectedShipping = 'standard'"
                >
                  <input type="radio" name="shipping" value="standard" v-model="selectedShipping" />
                  <span class="shipping-radio"></span>
                  <div class="shipping-details">
                    <div class="shipping-name">Giao hàng tiêu chuẩn</div>
                    <div class="shipping-desc">3-5 ngày làm việc</div>
                  </div>
                  <span class="shipping-price free">Miễn phí</span>
                </label>

                <label 
                  class="shipping-option" 
                  :class="{ selected: selectedShipping === 'express' }"
                  @click="selectedShipping = 'express'"
                >
                  <input type="radio" name="shipping" value="express" v-model="selectedShipping" />
                  <span class="shipping-radio"></span>
                  <div class="shipping-details">
                    <div class="shipping-name">Giao hàng nhanh</div>
                    <div class="shipping-desc">1-2 ngày làm việc</div>
                  </div>
                  <span class="shipping-price">30.000đ</span>
                </label>

                <label 
                  class="shipping-option" 
                  :class="{ selected: selectedShipping === 'same-day' }"
                  @click="selectedShipping = 'same-day'"
                >
                  <input type="radio" name="shipping" value="same-day" v-model="selectedShipping" />
                  <span class="shipping-radio"></span>
                  <div class="shipping-details">
                    <div class="shipping-name">Giao trong ngày</div>
                    <div class="shipping-desc">Nhận hàng trong 2-4 giờ (nội thành)</div>
                  </div>
                  <span class="shipping-price">50.000đ</span>
                </label>
              </div>
            </div> -->

            <!-- Payment Method -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon payment-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                  </span>
                  <div class="payment-details">
                    <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                    <div class="payment-desc">Trả tiền mặt khi nhận được hàng</div>
                  </div>
                </label>

                <!-- Các phương thức khác giữ nguyên -->
                <!-- ... (bank, momo, vnpay) ... -->
              </div>
            </div>

            <!-- Order Note -->
            <div class="checkout-section">
              <h3 class="checkout-section-title">
                <span class="section-icon note-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
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
              <span class="items-count">{{ cartStore.itemCount }} sản phẩm</span>
            </div>

            <!-- Order Items -->
            <div class="order-items">
              <div class="order-item" v-for="item in cartStore.items" :key="item.id">
                <img 
                  :src="item.image || item.variant?.image_urls?.[0] || `https://via.placeholder.com/80?text=${encodeURIComponent(item.name || 'SP')}`" 
                  :alt="item.name || 'Sản phẩm'" 
                  class="order-item-image" 
                />
                <div class="order-item-info">
                  <span class="order-item-name">{{ item.name }}</span>
                  <span class="order-item-variant">Phân loại: {{ item.variant?.name || 'Mặc định' }}</span>
                  <span class="order-item-qty">x{{ item.quantity }}</span>
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
              Đặt hàng
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

const router = useRouter()
const cartStore = useCartStore()
const auth = useAuthStore()

const loading = ref(false)
const errorMessage = ref('')

// Customer Info
const customerInfo = ref({
  name: '',
  phone: '',
  email: '',
  city: '',
  district: '',
  address: ''
})

// Shipping & Payment
const selectedShipping = ref('standard')
const selectedPayment = ref('cod')
const orderNote = ref('')

// Computed: Kiểm tra form hợp lệ
const formValid = computed(() => {
  return (
    customerInfo.value.name.trim() &&
    customerInfo.value.phone.trim() &&
    customerInfo.value.email.trim() &&
    customerInfo.value.city &&
    customerInfo.value.district &&
    customerInfo.value.address.trim()
  )
})

// Tính giá đơn vị (dùng chung cho đơn giá và thành tiền)
const calculatedUnitPrice = (item) => {
  if (!item) return 0
  if (item.variant?.price_extra && Number(item.variant.price_extra) > 0) {
    return (item.price || 0) + Number(item.variant.price_extra)
  }
  return item.discount_price || item.price || item.variant?.product?.discount_price || 0
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
}

// Load dữ liệu khi mount
onMounted(async () => {
  if (!auth.isAuthenticated) {
    router.push({ path: '/login', query: { redirect: '/checkout' } })
    return
  }

  // Điền sẵn thông tin từ user nếu đã login
  if (auth.user) {
    customerInfo.value = {
      name: auth.user.full_name || '',
      phone: auth.user.phone || '',
      email: auth.user.email || '',
      city: '', // cần API tỉnh/thành nếu muốn
      district: '',
      address: auth.user.address || ''
    }
  }

  // Tải giỏ hàng
  await cartStore.fetchCart()
})

// Đặt hàng
const placeOrder = async () => {
  if (!formValid.value) {
    errorMessage.value = 'Vui lòng điền đầy đủ thông tin người nhận!'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const payload = {
      items: cartStore.items.map(item => ({
        cart_item_id: item.id,               // ID của cart_item
        quantity: item.quantity,
        variant_id: item.variant_id || item.variant?.variant_id || null
      })),
      customer_info: {
        name: customerInfo.value.name.trim(),
        phone: customerInfo.value.phone.trim(),
        email: customerInfo.value.email.trim(),
        city: customerInfo.value.city,
        district: customerInfo.value.district,
        address: customerInfo.value.address.trim()
      },
      shipping_method: selectedShipping.value,
      payment_method: selectedPayment.value,
      note: orderNote.value.trim(),
      coupon_code: cartStore.couponCode.trim() || null
    }

    console.log('Payload gửi đi:', payload)  // ← thêm dòng này để debug

    const res = await api.post('/orders', payload)

    // Thành công
    router.push({
      name: 'OrderSuccess',
      query: { orderId: res.data.order_id || 'FT' + Date.now().toString().slice(-8) }
    })

    await cartStore.clearCart()
  } catch (err) {
    if (err.response?.status === 422) {
      // Hiển thị lỗi validate chi tiết
      const errors = err.response.data.errors
      errorMessage.value = Object.values(errors)[0]?.[0] || 'Dữ liệu không hợp lệ. Vui lòng kiểm tra lại!'
      console.log('Validation errors:', errors)
    } else {
      errorMessage.value = err.response?.data?.message || 'Đặt hàng thất bại!'
    }
    console.error('Place order error:', err)
  } finally {
    loading.value = false
  }
}
</script>


<style scoped>
@import '@/views/cilent/css/checkout.css';
</style>