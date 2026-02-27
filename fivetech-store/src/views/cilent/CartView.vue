<template>
  <div class="cart-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <a href="/">Trang chủ</a>
          <span class="separator">/</span>
          <span class="current">Giỏ hàng</span>
        </nav>
        <h1 class="page-title">Giỏ hàng của bạn</h1>
        <p class="page-subtitle">Kiểm tra lại các sản phẩm đã chọn trước khi thanh toán</p>
      </div>
    </div>

    <!-- Cart Content -->
    <div class="cart-content">
      <div class="container">
        <div v-if="cartStore.loading" class="loading-state">
          <p>Đang tải giỏ hàng...</p>
        </div>

        <div v-else-if="cartStore.error" class="error-state">
          <p>{{ cartStore.error }}</p>
          <button @click="cartStore.fetchCart">Thử lại</button>
        </div>

        <div v-else-if="cartStore.items.length === 0" class="empty-cart">
          <span class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#e2e8f0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="8" cy="21" r="1"/>
              <circle cx="19" cy="21" r="1"/>
              <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
          </span>
          <h2>Giỏ hàng trống</h2>
          <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
          <a href="/products" class="btn-primary">Tiếp tục mua sắm</a>
        </div>

        <div v-else class="cart-layout">
          <!-- Left: Cart Items -->
          <div class="cart-items-wrapper">
            <div class="cart-items">
              <div class="cart-header">
                <div class="col-product">Sản phẩm</div>
                <div class="col-price">Đơn giá</div>
                <div class="col-qty">Số lượng</div>
                <div class="col-subtotal">Thành tiền</div>
                <div class="col-action"></div>
              </div>

              <div class="cart-item" v-for="item in cartStore.items" :key="item.id">
                <div class="item-product" data-label="Sản phẩm">
                  <img :src="item.image" :alt="item.name" class="item-image" />
                  <div class="item-info">
                      {{ item.product.name }}
                    <span class="item-variant">
                      Phân loại: 
                      <template v-if="item.variant">
                        {{ item.variant.color ? item.variant.color + ' - ' : '' }}
                        {{ item.variant.name || 'Không xác định' }}
                        {{ item.variant.storage_size ? ' (' + item.variant.storage_size + ')' : '' }}
                      </template>
                      <template v-else>Mặc định</template>
                    </span>
                  </div>
                </div>

                <div class="item-price" data-label="Đơn giá">
                  {{ formatPrice(
                    item.variant?.price_extra && item.variant.price_extra > 0 
                      ? (item.price || 0) + Number(item.variant.price_extra)
                      : (item.discount_price || item.price || item.variant?.product?.discount_price || 0)
                  ) }}
                </div>

                <div class="item-qty" data-label="Số lượng">
                  <div class="quantity-box">
                    <button 
                      class="qty-btn" 
                      @click="updateQty(item.id, item.quantity - 1)" 
                      :disabled="item.quantity <= 1 || cartStore.loading"
                    >−</button>
                    <input type="text" class="qty-input" :value="item.quantity" readonly />
                    <button 
                      class="qty-btn" 
                      @click="updateQty(item.id, item.quantity + 1)" 
                      :disabled="cartStore.loading"
                    >+</button>
                  </div>
                </div>

                <div class="item-subtotal col-subtotal" data-label="Thành tiền">
                  {{ formatPrice(
                    (item.variant?.price_extra && Number(item.variant.price_extra) > 0
                      ? (item.price || 0) + Number(item.variant.price_extra)
                      : (item.discount_price || item.price || item.variant?.product?.discount_price || 0)
                    ) * item.quantity
                  ) }}
                </div>

                <div class="item-action">
                  <button 
  class="remove-btn" 
  @click="remove(item.id)"
  :disabled="cartStore.loading"
  title="Xóa sản phẩm"
>
  <svg xmlns="http://www.w3.org/2000/svg"
       width="16"
       height="16"
       viewBox="0 0 24 24"
       fill="none"
       stroke="currentColor"
       stroke-width="2"
       stroke-linecap="round"
       stroke-linejoin="round">
    <line x1="18" y1="6" x2="6" y2="18"></line>
    <line x1="6" y1="6" x2="18" y2="18"></line>
  </svg>
</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Summary -->
          <div class="cart-summary">
            <h3 class="summary-title">Thông tin đơn hàng</h3>

            <div class="coupon-section">
              <label class="form-label" style="color: #94a3b8; margin-bottom: 8px; display: block;">Mã giảm giá</label>
              <div class="coupon-input-group">
                <input 
                  type="text" 
                  v-model="cartStore.couponCode" 
                  placeholder="Nhập mã voucher" 
                  class="coupon-input" 
                  :disabled="cartStore.loading"
                />
                <button 
                  class="coupon-btn" 
                  @click="cartStore.applyCoupon" 
                  :disabled="cartStore.loading"
                >Áp dụng</button>
              </div>
            </div>

            <div class="summary-details">
              <div class="summary-row">
                <span>Tạm tính</span>
                <span class="summary-value">
                  {{ formatPrice(cartStore.subtotal) }}
                </span>
              </div>

              <div class="summary-row" v-if="cartStore.hasDiscount">
                <span>Giảm giá</span>
                <span class="summary-value text-red-600">
                  -{{ formatPrice(cartStore.discount) }}
                </span>
              </div>

             <div class="summary-row">
              <span>Phí vận chuyển</span>
              <span class="summary-value" :class="{ 'text-green-600': cartStore.finalShippingFee === 0 }">
                {{ cartStore.finalShippingFee === 0 ? 'Miễn phí' : formatPrice(cartStore.finalShippingFee) }}
              </span>
            </div>

            <div class="summary-row total">
              <span>Tổng cộng</span>
              <span class="total-value">
                {{ formatPrice(cartStore.grandTotal) }}
              </span>
            </div>
            </div>

            <button 
              class="checkout-btn" 
              @click="goToCheckout" 
              :disabled="cartStore.loading || cartStore.items.length === 0"
            >
              Tiến hành thanh toán
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>

            <a href="/products" class="continue-shopping">← Tiếp tục mua sắm</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'

const cartStore = useCartStore()
const auth = useAuthStore()
const router = useRouter()

const couponCode = ref('')

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
}

const updateQty = async (id, qty) => {
  if (qty < 1) return
  try {
    await cartStore.updateQuantity(id, qty)
  } catch (err) {
    alert('Không thể cập nhật số lượng: ' + (err.response?.data?.message || err.message))
  }
}

const remove = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return
  try {
    await cartStore.removeItem(id)
  } catch (err) {
    alert('Không thể xóa sản phẩm: ' + (err.response?.data?.message || err.message))
  }
}

const applyCoupon = async () => {
  if (!couponCode.value.trim()) return alert('Vui lòng nhập mã giảm giá')
  try {
    await cartStore.applyCoupon(couponCode.value.trim())
    alert('Áp dụng mã giảm giá thành công!')
  } catch (err) {
    alert('Mã giảm giá không hợp lệ hoặc đã hết hạn: ' + (err.response?.data?.message || err.message))
  }
}

const goToCheckout = () => {
  router.push('/checkout')
}

const goToLogin = () => {
  router.push({ path: '/login', query: { redirect: '/cart' } })
}

onMounted(async () => {
  // Luôn fetch cart (cho cả user và guest)
  await cartStore.fetchCart()
})
</script>

<style scoped>
/* Giữ nguyên style cũ, chỉ thêm vài class hỗ trợ */
.loading-state, .error-state {
  text-align: center;
  padding: 60px 0;
  color: #94a3b8;
}

.error-state button {
  margin-top: 16px;
  padding: 10px 20px;
  background: #ff6b35;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
  
/* Các class khác giữ nguyên từ code cũ của bạn */
</style>