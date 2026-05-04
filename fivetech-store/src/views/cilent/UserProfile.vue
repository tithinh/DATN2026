<template>
  <div class="user-profile-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <span class="current">Tài khoản của tôi</span>
        </nav>
        <h1 class="page-title">Tài khoản của tôi</h1>
        <p class="page-subtitle">Quản lý thông tin cá nhân và theo dõi đơn hàng</p>
      </div>
    </div>

    <!-- Main Content -->
    <section class="profile-section">
      <div class="container">
        <div class="profile-layout">
          <!-- Sidebar Navigation -->
          <aside class="profile-sidebar">
            <div class="user-avatar-card">
              <div class="avatar-wrapper">
                <img
                  :src="auth.user?.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user?.full_name || 'User')}&background=0D8ABC&color=fff`"
                  :alt="auth.user?.full_name || 'Avatar'"
                  class="avatar-image"
                />
                <button class="avatar-edit-btn" @click="editAvatar">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </button>
              </div>
              <h3 class="user-name">{{ auth.user?.full_name || 'Tài khoản' }}</h3>
              <p class="user-email">{{ auth.user?.email || 'Chưa có email' }}</p>
              <span class="member-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                Thành viên
              </span>
            </div>

            <nav class="sidebar-nav">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                class="nav-item"
                :class="{ active: activeTab === tab.id }"
                @click="activeTab = tab.id"
              >
                <span class="nav-icon" v-html="tab.icon"></span>
                <span class="nav-text">{{ tab.label }}</span>
                <span v-if="tab.badge" class="nav-badge">{{ tab.badge }}</span>
              </button>
            </nav>

            <button class="logout-btn" @click="handleLogout">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
              Đăng xuất
            </button>
          </aside>

          <!-- Main Content Area -->
          <main class="profile-content">
            <!-- Thông tin cá nhân -->
            <div v-if="activeTab === 'personal'" class="tab-panel">
              <div class="panel-header">
                <h2 class="panel-title">Thông tin cá nhân</h2>
                <p class="panel-desc">Cập nhật thông tin để bảo mật tài khoản và nhận ưu đãi</p>
              </div>

              <div class="info-cards-grid">
                <!-- Basic Info -->
                <div class="info-card">
                  <div class="card-header">
                    <span class="card-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                    </span>
                    <h3>Thông tin cơ bản</h3>
                    <button class="edit-btn" @click="editProfile">Chỉnh sửa</button>
                  </div>
                  <div class="card-body">
                    <div class="info-row">
                      <span class="label">Họ và tên</span>
                      <span class="value">{{ auth.user?.full_name || 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="info-row">
                      <span class="label">Ngày sinh</span>
                      <span class="value">{{ auth.user?.birthday || 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="info-row">
                      <span class="label">Giới tính</span>
                      <span class="value">{{ auth.user?.gender || 'Chưa cập nhật' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Contact Info -->
                <div class="info-card">
                  <div class="card-header">
                    <span class="card-icon contact">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <h3>Thông tin liên hệ</h3>
                    <button class="edit-btn" @click="editProfile">Chỉnh sửa</button>
                  </div>
                  <div class="card-body">
                    <div class="info-row">
                      <span class="label">Email</span>
                      <span class="value">{{ auth.user?.email || 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="info-row">
                      <span class="label">Số điện thoại</span>
                      <span class="value">{{ auth.user?.phone || 'Chưa cập nhật' }}</span>
                    </div>
                  </div>
                </div>

                <!-- Address -->
                <div class="info-card full-width">
                  <div class="card-header">
                    <span class="card-icon address">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <h3>Địa chỉ giao hàng</h3>
                  </div>
                  <div class="card-body">
                    <div class="current-address">
                      <div class="address-main">
                        <div class="address-top">
                          <strong>{{ auth.user?.full_name || 'Chưa cập nhật' }}</strong>
                          <span class="address-phone">{{ auth.user?.phone || 'Chưa cập nhật' }}</span>
                        </div>
                        <p class="address-text">{{ auth.user?.address || 'Chưa cập nhật' }}</p>
                      </div>
                      <div class="address-actions">
                        <router-link to="/profile/edit" class="action-btn primary small">Cập nhật</router-link>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Security -->
                <div class="info-card full-width">
                  <div class="card-header">
                    <span class="card-icon security">
                      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <h3>Bảo mật</h3>
                  </div>
                  <div class="card-body">
                    <div class="security-options">
                      <div class="security-item">
                        <div class="security-info">
                          <strong>Mật khẩu</strong>
                          <p>Đổi mật khẩu định kỳ để bảo vệ tài khoản</p>
                        </div>
                        <button class="submit-btn small" @click="changePassword">Đổi mật khẩu</button>
                      </div>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Đơn hàng -->
            <div v-if="activeTab === 'orders'" class="tab-panel">
              <div class="panel-header">
                <h2 class="panel-title">Đơn hàng của tôi</h2>
                <p class="panel-desc">Theo dõi và quản lý tất cả đơn hàng của bạn</p>
              </div>

              <div v-if="loadingOrders" class="loading-state">
                <div class="spinner"></div>
                <p>Đang tải đơn hàng...</p>
              </div>

              <div v-else-if="allOrders.length === 0" class="empty-state">
                <p>Bạn chưa có đơn hàng nào.</p>
                <router-link to="/products" class="btn-primary">Tiếp tục mua sắm</router-link>
              </div>

              <div v-else class="orders-list">
                <div class="order-card" v-for="order in allOrders" :key="order.id">
                  <div class="order-header">
                    <div class="order-id">
                      <span class="label">Mã đơn:</span>
                      <span class="value">{{ order.order_code }}</span>
                    </div>
                    <span class="order-status" :class="order.status">{{ order.status_text }}</span>
                  </div>

                  <div class="order-items">
                    <div class="item" v-for="item in order.items" :key="item.id">
                      <img :src="item.image ? storageUrl(item.image) : 'https://via.placeholder.com/60x60?text=SP'" :alt="item.name" class="item-image" @error="handleImageError" />
                      <div class="item-info">
                        <h4 class="item-name">{{ item.name }}</h4>
                        <p class="item-variant">{{ item.variant || 'Mặc định' }}</p>
                      </div>
                      <div class="item-qty">x{{ item.quantity }}</div>
                      <div class="item-price">{{ formatPrice(item.price) }}</div>
                    </div>
                  </div>

                  <div class="order-footer">
                    <div class="order-total">
                      <span class="label">Tổng tiền:</span>
                      <span class="value">{{ formatPrice(order.total) }}</span>
                    </div>
                    <div class="order-actions">
                      <button class="action-btn outline" @click="viewOrderDetail(order.order_code)">Xem chi tiết</button>
                      <button class="action-btn danger" v-if="['pending'].includes(order.status)" @click="cancelOrder(order)">Hủy đơn</button>
                      <span v-if="order.status === 'cancelled'" class="status-badge cancelled">Đã hủy</span>
                      <button class="action-btn primary" v-if="order.status === 'delivered'" @click="confirmReceived(order)">Đã nhận hàng</button>
                      <button class="action-btn review" v-if="order.status === 'completed'" @click="openReviewModal(order)">★ Đánh giá</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sản phẩm yêu thích -->
            <div v-if="activeTab === 'wishlist'" class="tab-panel">
              <div class="panel-header">
                <h2 class="panel-title">Sản phẩm yêu thích</h2>
                <p class="panel-desc">Danh sách sản phẩm bạn đã lưu</p>
              </div>

              <div v-if="loadingWishlist" class="loading-state">
                <div class="spinner"></div>
                <p>Đang tải wishlist...</p>
              </div>

              <div v-else-if="wishlistItems.length === 0" class="empty-state">
                <p>Chưa có sản phẩm yêu thích nào.</p>
                <router-link to="/products" class="btn-primary">Khám phá sản phẩm</router-link>
              </div>

              <div v-else class="wishlist-grid">
                <div class="wishlist-item" v-for="item in wishlistItems" :key="item.wishlist_id">
                  <router-link :to="`/products/${item.slug}`" class="wishlist-item-link">
                    <div class="wishlist-item-image">
                      <img :src="item.image || '/images/default-product.jpg'" :alt="item.name" />
                    </div>
                    <div class="wishlist-item-info">
                      <h3 class="item-name">{{ item.name }}</h3>
                      <div class="item-price">
                        <span class="current-price">{{ formatPrice(item.final_price) }}</span>
                        <span v-if="item.discount_price && item.discount_price < item.base_price" class="old-price">
                          {{ formatPrice(item.base_price) }}
                        </span>
                      </div>
                    </div>
                  </router-link>
                  <div class="wishlist-item-actions">
                    <button class="add-to-cart-btn" @click="addToCartFromWishlist(item)">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                      </svg>
                      Thêm vào giỏ
                    </button>
                    <button class="remove-btn" @click="removeFromWishlist(item.product_id)">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal Đánh giá -->
  <div v-if="reviewModal.open" class="review-overlay" @click.self="closeReviewModal">
    <div class="review-modal">
      <div class="review-modal-header">
        <h3>Đánh giá đơn hàng <span>#{{ reviewModal.orderCode }}</span></h3>
        <button class="close-btn" @click="closeReviewModal">✕</button>
      </div>
      <div class="review-modal-body">
        <div class="review-item" v-for="(item, idx) in reviewModal.items" :key="item.id">
          <div class="review-product">
            <img :src="item.image ? storageUrl(item.image) : 'https://via.placeholder.com/56x56?text=SP'" :alt="item.name" @error="handleImageError" />
            <div class="review-product-info">
              <p class="review-product-name">{{ item.name }}</p>
              <p class="review-product-variant" v-if="item.variant">{{ item.variant }}</p>
            </div>
          </div>
          <div class="star-rating">
            <span
              v-for="star in 5"
              :key="star"
              class="star"
              :class="{ active: star <= (reviewModal.ratings[idx] || 0) }"
              @click="reviewModal.ratings[idx] = star"
            >★</span>
          </div>
          <textarea
            v-model="reviewModal.contents[idx]"
            class="review-textarea"
            placeholder="Nhận xét của bạn về sản phẩm này..."
            rows="3"
          ></textarea>
        </div>
      </div>
      <div class="review-modal-footer">
        <button class="btn-cancel" @click="closeReviewModal">Bỏ qua</button>
        <button class="btn-submit-review" @click="submitReviews" :disabled="reviewModal.submitting">
          {{ reviewModal.submitting ? 'Đang gửi...' : 'Gửi đánh giá' }}
        </button>
      </div>
    </div>
  </div>

  <!-- Modal thêm/sửa địa chỉ -->
  <div v-if="addressModal.open" class="review-overlay" @click.self="closeAddressModal">
    <div class="review-modal" style="max-width:500px">
      <div class="review-modal-header">
        <h3>{{ addressModal.editId ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới' }}</h3>
        <button class="close-btn" @click="closeAddressModal">✕</button>
      </div>
      <div class="review-modal-body" style="padding:20px 24px">
        <div class="form-group" style="margin-bottom:14px">
          <label class="form-label">Họ và tên <span style="color:#ef4444">*</span></label>
          <input class="form-input" v-model="addressModal.full_name" placeholder="Nguyễn Văn A" />
        </div>
        <div class="form-group" style="margin-bottom:14px">
          <label class="form-label">Số điện thoại <span style="color:#ef4444">*</span></label>
          <input class="form-input" v-model="addressModal.phone" placeholder="0912 345 678" />
        </div>
        <div class="form-group" style="margin-bottom:14px">
          <label class="form-label">Địa chỉ đầy đủ <span style="color:#ef4444">*</span></label>
          <input class="form-input" v-model="addressModal.address" placeholder="Số nhà, đường, phường/xã, quận/huyện, thành phố" />
        </div>
        <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" v-model="addressModal.is_default" />
          Đặt làm địa chỉ mặc định
        </label>
        <p v-if="addressModal.error" style="color:#ef4444;margin-top:10px;font-size:13px">{{ addressModal.error }}</p>
      </div>
      <div class="review-modal-footer">
        <button class="btn-cancel" @click="closeAddressModal">Hủy</button>
        <button class="btn-submit-review" @click="saveAddress" :disabled="addressModal.saving">
          {{ addressModal.saving ? 'Đang lưu...' : 'Lưu địa chỉ' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { storageUrl } from '@/utils/image'
import api from '@/api'

const router = useRouter()
const auth = useAuthStore()
const route = useRoute()

const activeTab = ref('personal')
const loadingOrders = ref(false)
const loadingWishlist = ref(false)
const loadingAddresses = ref(false)
const allOrders = ref([])
const wishlistItems = ref([])
const addresses = ref([])

// Address modal
const addressModal = ref({
  open: false, editId: null,
  full_name: '', phone: '', address: '', is_default: false,
  saving: false, error: '',
})

// Review modal state
const reviewModal = ref({
  open: false,
  orderCode: '',
  items: [],
  ratings: [],
  contents: [],
  submitting: false,
})

const tabs = [
  {
    id: 'personal',
    label: 'Thông tin cá nhân',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>'
  },
  {
    id: 'orders',
    label: 'Đơn hàng',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>',
  },
  {
    id: 'wishlist',
    label: 'Sản phẩm yêu thích',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
  }
]

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price || 0) + '₫'
}

const handleLogout = async () => {
  try {
    await api.post('/logout')
    auth.logout() // xóa token, user trong store
    router.push('/login')
  } catch (err) {
    console.error('Logout failed:', err)
  }
}

const editProfile = () => {
  router.push('/profile/edit')
}

const addAddress = () => {
  openAddressModal()
}

const changePassword = () => {
  router.push('/profile/change-password')
}

const viewOrderDetail = (orderId) => {
  router.push(`/orders/${orderId}`)
}

const reorder = (order) => {
  // Chuyển hướng đến trang sản phẩm đầu tiên trong đơn hàng
  if (order.items && order.items.length > 0) {
    router.push(`/products/${order.items[0].slug}`)
  }
}

const cancelOrder = async (order) => {
  if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) return
  try {
    await api.post(`/orders/${order.id}/cancel`)
    alert('Đã hủy đơn hàng thành công!')
    const ordersRes = await api.get('/orders')
    allOrders.value = ordersRes.data.orders || []
  } catch (err) {
    console.error('Cancel order failed:', err)
    alert(err.response?.data?.message || 'Không thể hủy đơn hàng. Vui lòng thử lại!')
  }
}

const confirmReceived = async (order) => {
  if (!confirm('Xác nhận bạn đã nhận được hàng?')) return
  try {
    await api.post(`/orders/${order.id}/confirm-received`)
    alert('Xác nhận nhận hàng thành công!')
    const ordersRes = await api.get('/orders')
    allOrders.value = ordersRes.data.orders || []
  } catch (err) {
    console.error('Confirm received failed:', err)
    alert(err.response?.data?.message || 'Không thể xác nhận. Vui lòng thử lại!')
  }
}

const addToCartFromWishlist = async (item) => {
  try {
    // Lấy variant đầu tiên nếu có
    const variantId = item.variants?.[0]?.variant_id
    if (!variantId) {
      alert('Sản phẩm này chưa có biến thể')
      return
    }

    await api.post('/cart/add', {
      variant_id: variantId,
      quantity: 1
    })
    alert('Đã thêm vào giỏ hàng!')
  } catch (err) {
    console.error('Add to cart failed:', err)
    alert('Không thể thêm sản phẩm. Vui lòng thử lại!')
  }
}

const removeFromWishlist = async (productId) => {
  if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
    return
  }

  try {
    await api.delete(`/wishlist/remove/${productId}`)
    // Cập nhật lại danh sách
    wishlistItems.value = wishlistItems.value.filter(item => item.product_id !== productId)
    alert('Đã xóa khỏi danh sách yêu thích!')
  } catch (err) {
    console.error('Remove from wishlist failed:', err)
    alert('Không thể xóa sản phẩm. Vui lòng thử lại!')
  }
}

const editAvatar = () => {
  router.push('/profile/edit')
}

// ==================== Address ====================
const loadAddresses = async () => {
  loadingAddresses.value = true
  try {
    const res = await api.get('/addresses')
    addresses.value = res.data
  } catch (err) {
    console.error('Load addresses failed:', err)
  } finally {
    loadingAddresses.value = false
  }
}

const openAddressModal = (addr = null) => {
  addressModal.value = {
    open: true,
    editId: addr?.id || null,
    full_name: addr?.full_name || '',
    phone: addr?.phone || '',
    address: addr?.address || '',
    is_default: addr?.is_default || false,
    saving: false,
    error: '',
  }
}

const closeAddressModal = () => {
  addressModal.value.open = false
}

const saveAddress = async () => {
  const m = addressModal.value
  if (!m.full_name.trim() || !m.phone.trim() || !m.address.trim()) {
    m.error = 'Vui lòng điền đầy đủ thông tin'
    return
  }
  m.saving = true
  m.error = ''
  try {
    const payload = { full_name: m.full_name, phone: m.phone, address: m.address, is_default: m.is_default }
    if (m.editId) {
      await api.put(`/addresses/${m.editId}`, payload)
    } else {
      await api.post('/addresses', payload)
    }
    closeAddressModal()
    await loadAddresses()
  } catch (err) {
    m.error = err.response?.data?.message || 'Lưu địa chỉ thất bại'
  } finally {
    m.saving = false
  }
}

const setDefaultAddress = async (id) => {
  try {
    await api.post(`/addresses/${id}/default`)
    await loadAddresses()
  } catch (err) {
    console.error('Set default failed:', err)
  }
}

const deleteAddress = async (id) => {
  if (!confirm('Xóa địa chỉ này?')) return
  try {
    await api.delete(`/addresses/${id}`)
    await loadAddresses()
  } catch (err) {
    console.error('Delete address failed:', err)
  }
}

const openReviewModal = (order) => {
  reviewModal.value = {
    open: true,
    orderCode: order.order_code,
    items: order.items || [],
    ratings: (order.items || []).map(() => 5),
    contents: (order.items || []).map(() => ''),
    submitting: false,
  }
}

const closeReviewModal = () => {
  reviewModal.value.open = false
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/60x60?text=SP'
}

const submitReviews = async () => {
  reviewModal.value.submitting = true
  const { items, ratings, contents } = reviewModal.value
  let successCount = 0

  for (let i = 0; i < items.length; i++) {
    const productId = items[i].product_id
    const rating = ratings[i] || 5
    const content = contents[i]?.trim()
    if (!content || !productId) continue
    try {
      await api.post(`/products/${productId}/comments`, { rating, content })
      successCount++
    } catch {}
  }

  reviewModal.value.submitting = false
  reviewModal.value.open = false
  if (successCount > 0) alert(`Đã gửi ${successCount} đánh giá thành công!`)
  else alert('Vui lòng nhập nhận xét cho ít nhất 1 sản phẩm.')
}

// Load data khi mount
onMounted(async () => {
  // Set active tab from URL query param (for track order from OrderSuccess)
  if (route.query.tab === 'orders') {
    activeTab.value = 'orders'
  }
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }

  loadingOrders.value = true
  loadingWishlist.value = true
  try {
    // Lấy đơn hàng
    const ordersRes = await api.get('/orders')
    allOrders.value = ordersRes.data.orders || []

    // Lấy địa chỉ
    await loadAddresses()

    // Lấy wishlist
    const wishlistRes = await api.get('/wishlist')
    wishlistItems.value = wishlistRes.data?.data || wishlistRes.data || []
  } catch (err) {
    console.error('Load profile data failed:', err)
  } finally {
    loadingOrders.value = false
    loadingWishlist.value = false
  }
})
</script>

<style scoped>
@import '@/views/cilent/css/user-profile.css';

.loading-state {
  text-align: center;
  padding: 60px 0;
  color: #64748b;
}

/* Review Modal */
.review-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.review-modal {
  background: #fff;
  border-radius: 20px;
  width: 100%;
  max-width: 560px;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.review-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}

.review-modal-header h3 {
  font-size: 18px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.review-modal-header h3 span {
  color: #ff6b35;
}

.close-btn {
  width: 36px;
  height: 36px;
  background: #f1f5f9;
  border: none;
  border-radius: 50%;
  font-size: 16px;
  color: #64748b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.close-btn:hover { background: #e2e8f0; color: #0f172a; }

.review-modal-body {
  padding: 20px 24px;
  overflow-y: auto;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.review-item {
  border: 1.5px solid #f1f5f9;
  border-radius: 14px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.review-product {
  display: flex;
  align-items: center;
  gap: 12px;
}

.review-product img {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  object-fit: cover;
  border: 1px solid #e2e8f0;
  flex-shrink: 0;
}

.review-product-name {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 4px;
}

.review-product-variant {
  font-size: 13px;
  color: #64748b;
  margin: 0;
}

.star-rating {
  display: flex;
  gap: 6px;
}

.star {
  font-size: 28px;
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.15s;
  user-select: none;
}
.star:hover, .star.active { color: #fbbf24; transform: scale(1.1); }

.review-textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  color: #0f172a;
  resize: vertical;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s;
  box-sizing: border-box;
}
.review-textarea:focus { border-color: #ff6b35; }
.review-textarea::placeholder { color: #94a3b8; }

.review-modal-footer {
  display: flex;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
  justify-content: flex-end;
}

.btn-cancel {
  padding: 10px 20px;
  background: #f1f5f9;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-cancel:hover { background: #e2e8f0; }

.btn-submit-review {
  padding: 10px 24px;
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-submit-review:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(255,107,53,0.35); }
.btn-submit-review:disabled { opacity: 0.6; cursor: not-allowed; }

/* Wishlist Grid Styles */
.wishlist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.wishlist-item {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
}

.wishlist-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.wishlist-item-link {
  display: block;
  text-decoration: none;
  color: inherit;
}

.wishlist-item-image {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}

.wishlist-item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.wishlist-item-info {
  padding: 16px;
}

.wishlist-item-info .item-name {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 8px 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.wishlist-item-info .item-price {
  display: flex;
  align-items: center;
  gap: 8px;
}

.wishlist-item-info .current-price {
  font-size: 16px;
  font-weight: 700;
  color: #ff6b35;
}

.wishlist-item-info .old-price {
  font-size: 13px;
  color: #94a3b8;
  text-decoration: line-through;
}

.wishlist-item-actions {
  display: flex;
  gap: 8px;
  padding: 0 16px 16px;
}

.add-to-cart-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 12px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.add-to-cart-btn:hover {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
}

.remove-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  opacity: 0;
  transform: translateX(10px);
}

.wishlist-item-actions {
  overflow: hidden;
}

.wishlist-item-actions:hover .remove-btn {
  opacity: 1;
  transform: translateX(0);
}

.remove-btn:hover {
  background: #f87171;
  color: #ffffff;
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(248, 113, 113, 0.4);
}

.remove-btn:hover svg {
  stroke: #ffffff;
}

/* History Order Styles */
.order-card.history {
  border-left: 4px solid #94a3b8;
}

/* Spinner */
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

/* Responsive */
@media (max-width: 640px) {
  .wishlist-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .wishlist-item-info {
    padding: 12px;
  }
  
  .wishlist-item-info .item-name {
    font-size: 13px;
  }
  
  .wishlist-item-actions {
    flex-direction: column;
    padding: 0 12px 12px;
  }
  
  .add-to-cart-btn {
    width: 100%;
  }
  
  .remove-btn {
    width: 100%;
  }
}
</style>
