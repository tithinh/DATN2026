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
                    <button class="edit-btn" @click="addAddress">Thêm địa chỉ</button>
                  </div>
                  <div class="card-body">
                    <div class="address-list">
                      <div class="address-item" v-for="(addr, index) in auth.user?.addresses || []" :key="index">
                        <div class="address-badge" v-if="addr.is_default">Mặc định</div>
                        <div class="address-info">
                          <strong>{{ auth.user?.full_name }}</strong> | {{ auth.user?.phone }}
                          <p>{{ addr.address }}, {{ addr.district }}, {{ addr.city }}</p>
                        </div>
                        <div class="address-actions">
                          <button class="action-btn">Sửa</button>
                          <button class="action-btn danger">Xóa</button>
                        </div>
                      </div>
                      <div v-if="!auth.user?.addresses?.length" class="empty-address">
                        <p>Bạn chưa có địa chỉ giao hàng nào</p>
                        <button class="btn-primary small" @click="addAddress">Thêm địa chỉ mới</button>
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

            <!-- Đơn hàng hiện tại -->
            <div v-if="activeTab === 'orders'" class="tab-panel">
              <div class="panel-header">
                <h2 class="panel-title">Đơn hàng hiện tại</h2>
                <p class="panel-desc">Theo dõi tình trạng đơn hàng đang xử lý</p>
              </div>

              <div v-if="loadingOrders" class="loading-state">
                <div class="spinner"></div>
                <p>Đang tải đơn hàng...</p>
              </div>

              <div v-else-if="currentOrders.length === 0" class="empty-state">
                <p>Bạn chưa có đơn hàng nào đang xử lý.</p>
                <router-link to="/products" class="btn-primary">Tiếp tục mua sắm</router-link>
              </div>

              <div v-else class="orders-list">
                <div class="order-card" v-for="order in currentOrders" :key="order.id">
                  <div class="order-header">
                    <div class="order-id">
                      <span class="label">Mã đơn:</span>
                      <span class="value">#{{ order.id }}</span>
                    </div>
                    <span class="order-status" :class="order.status">{{ order.status_text }}</span>
                  </div>

                  <div class="order-progress">
                    <div class="progress-track">
                      <div class="progress-fill" :style="{ width: order.progress + '%' }"></div>
                    </div>
                    <div class="progress-steps">
                      <div class="step" :class="{ completed: order.step >= 1 }"><span class="step-dot"></span><span class="step-label">Đặt hàng</span></div>
                      <div class="step" :class="{ completed: order.step >= 2 }"><span class="step-dot"></span><span class="step-label">Xác nhận</span></div>
                      <div class="step" :class="{ completed: order.step >= 3 }"><span class="step-dot"></span><span class="step-label">Vận chuyển</span></div>
                      <div class="step" :class="{ completed: order.step >= 4 }"><span class="step-dot"></span><span class="step-label">Thành công</span></div>
                    </div>
                  </div>

                  <div class="order-items">
                    <div class="item" v-for="item in order.items" :key="item.id">
                      <img :src="item.image" :alt="item.name" class="item-image" />
                      <div class="item-info">
                        <h4 class="item-name">{{ item.name }}</h4>
                        <p class="item-variant">{{ item.variant }}</p>
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
                      <button class="action-btn outline" @click="viewOrderDetail(order.id)">Xem chi tiết</button>
                      <button class="action-btn primary" v-if="order.status === 'shipping'">Theo dõi vận chuyển</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Lịch sử mua hàng -->
            <div v-if="activeTab === 'history'" class="tab-panel">
              <!-- ... giữ nguyên phần lịch sử, nhưng thay data bằng API nếu cần ... -->
              <!-- Ví dụ: gọi API /orders?status=completed -->
            </div>

            <!-- Sản phẩm yêu thích -->
            <div v-if="activeTab === 'wishlist'" class="tab-panel">
              <!-- ... giữ nguyên phần wishlist, gọi API /wishlist ... -->
            </div>
          </main>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

const router = useRouter()
const auth = useAuthStore()

const activeTab = ref('personal')
const loadingOrders = ref(false)
const currentOrders = ref([])
const orderHistory = ref([])
const wishlistItems = ref([])

const tabs = [
  {
    id: 'personal',
    label: 'Thông tin cá nhân',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>'
  },
  {
    id: 'orders',
    label: 'Đơn hàng hiện tại',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>',
  },
  {
    id: 'history',
    label: 'Lịch sử mua hàng',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'
  },
  {
    id: 'wishlist',
    label: 'Sản phẩm yêu thích',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
  }
]

const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
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
  router.push('/profile/addresses/add')
}

const changePassword = () => {
  router.push('/profile/change-password')
}

const viewOrderDetail = (orderId) => {
  router.push(`/orders/${orderId}`)
}

// Load data khi mount
onMounted(async () => {
  if (!auth.isAuthenticated) {
    router.push('/login')
    return
  }

  loadingOrders.value = true
  try {
    // Lấy đơn hàng hiện tại (status pending/processing/shipping)
    const ordersRes = await api.get('/orders')
    currentOrders.value = ordersRes.data.current || []
    orderHistory.value = ordersRes.data.history || []

    // Lấy wishlist
    const wishlistRes = await api.get('/wishlist')
    wishlistItems.value = wishlistRes.data || []
  } catch (err) {
    console.error('Load profile data failed:', err)
  } finally {
    loadingOrders.value = false
  }
})
</script>

<style scoped>
/* Giữ nguyên style cũ + thêm nếu cần */
@import '@/views/cilent/css/user-profile.css';

/* Thêm style cho loading/error nếu cần */
.loading-state {
  text-align: center;
  padding: 60px 0;
  color: #64748b;
}
</style>
