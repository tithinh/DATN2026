<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý đơn hàng</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo mã đơn, khách hàng..." @input="debouncedFetchOrders" />
        </div>
        <select class="toolbar-filter" v-model="filterStatus" @change="fetchOrders">
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xử lý</option>
          <option value="shipping">Đang giao</option>
          <option value="completed">Hoàn thành</option>
          <option value="cancelled">Đã huỷ</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách đơn hàng...</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Khách hàng</th>
              <th>SĐT</th>
              <th>Ngày đặt</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginatedOrders" :key="order.id">
              <td style="font-weight:700; color: var(--admin-text);">#{{ order.id }}</td>
              <td style="font-weight:500; color: var(--admin-text);">{{ order.customer_name }}</td>
              <td>{{ order.phone }}</td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td style="font-weight:600; color: var(--admin-warning);">{{ formatPrice(order.total) }}</td>
              <td>
                <span class="status-badge" :class="order.status">
                  {{ order.status_text }}
                </span>
              </td>
              <td>
                <div class="action-btns">
                  <button class="action-btn view" @click="openDetail(order)" title="Xem chi tiết">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination">
        <span class="pagination-info">Hiển thị {{ startItem }}-{{ endItem }} / {{ totalItems }} đơn hàng</span>
        <div class="pagination-btns">
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage = 1">Đầu</button>
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">Trước</button>
          <button class="page-btn" v-for="page in visiblePages" :key="page" :class="{ active: page === currentPage }" @click="currentPage = page">
            {{ page }}
          </button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Sau</button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage = totalPages">Cuối</button>
        </div>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="admin-modal-overlay" v-if="showDetail" @click.self="closeDetail">
      <div class="admin-modal wide slide-up">
        <div class="admin-modal-header">
          <h3>Chi tiết đơn hàng #{{ selectedOrder?.id }}</h3>
          <button class="modal-close" @click="closeDetail">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body" v-if="selectedOrder">
          <!-- Customer & Order Info -->
          <div class="order-detail-grid">
            <div class="order-info-card">
              <h4>👤 Thông tin khách hàng</h4>
              <div class="info-row">
                <span class="info-label">Họ tên:</span>
                <span class="info-value">{{ selectedOrder.customer_name }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ selectedOrder.email || 'Không có' }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Điện thoại:</span>
                <span class="info-value">{{ selectedOrder.phone }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Địa chỉ:</span>
                <span class="info-value">{{ selectedOrder.address }}</span>
              </div>
            </div>
            <div class="order-info-card">
              <h4>📦 Thông tin đơn hàng</h4>
              <div class="info-row">
                <span class="info-label">Mã đơn:</span>
                <span class="info-value" style="font-weight:700;">#{{ selectedOrder.id }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ formatDate(selectedOrder.created_at) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Tổng tiền:</span>
                <span class="info-value" style="color: var(--admin-warning); font-weight:700;">{{ formatPrice(selectedOrder.total) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value">
                  <span class="status-badge" :class="selectedOrder.status">{{ selectedOrder.status_text }}</span>
                </span>
              </div>
            </div>
          </div>

          <!-- Payment Info -->
          <div style="margin-bottom: 20px;">
            <h4 style="font-size: 14px; font-weight: 600; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">💳 Thông tin thanh toán</h4>
            <div class="admin-table-wrapper" style="background: var(--admin-bg); border: 1px solid var(--admin-border); border-radius: var(--admin-radius);">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Mã giao dịch</th>
                    <th>Ngày thanh toán</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="font-weight: 600;">{{ selectedOrder.payment_method || 'COD' }}</td>
                    <td>
                      <span class="status-badge" :class="selectedOrder.payment_status === 'paid' ? 'completed' : 'pending'">
                        {{ selectedOrder.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                      </span>
                    </td>
                    <td style="font-family: monospace;">{{ selectedOrder.transaction_id || '---' }}</td>
                    <td>{{ selectedOrder.payment_date || formatDate(selectedOrder.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Products in Order -->
          <div style="margin-bottom: 20px;">
            <h4 style="font-size: 14px; font-weight: 600; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">🛒 Sản phẩm trong đơn</h4>
            <div class="admin-table-wrapper" style="background: var(--admin-bg); border: 1px solid var(--admin-border); border-radius: var(--admin-radius);">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedOrder.items" :key="item.id">
                    <td style="font-weight: 500; color: var(--admin-text);">{{ item.product_name }}</td>
                    <td>{{ item.variant_name || 'Mặc định' }}</td>
                    <td>{{ formatPrice(item.price) }}</td>
                    <td>{{ item.quantity }}</td>
                    <td style="font-weight: 600; color: var(--admin-text);">{{ formatPrice(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Update Status -->
          <div class="admin-form-group">
            <label>Cập nhật trạng thái đơn hàng</label>
            <select class="admin-select" v-model="selectedOrder.status">
              <option value="pending">Chờ xử lý</option>
              <option value="shipping">Đang giao</option>
              <option value="completed">Hoàn thành</option>
              <option value="cancelled">Đã huỷ</option>
            </select>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeDetail">Đóng</button>
          <button class="admin-btn admin-btn-primary" @click="updateOrderStatus">Cập nhật trạng thái</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const filterStatus = ref('')
const currentPage = ref(1)
const itemsPerPage = 8
const loading = ref(false)
const showDetail = ref(false)
const selectedOrder = ref(null)
const orders = ref([])
const totalItems = ref(0)
const totalPages = ref(1)

// Debounce search
let debounceTimer
const debouncedFetchOrders = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchOrders, 500)
}

// Fetch orders
const fetchOrders = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      status: filterStatus.value,
      per_page: itemsPerPage,
      page: currentPage.value
    }
    const res = await api.get('/orders', { params })
    orders.value = res.data.data || res.data || []
    totalItems.value = res.data.total || orders.value.length
    totalPages.value = res.data.last_page || Math.ceil(totalItems.value / itemsPerPage)
  } catch (err) {
    console.error('Lỗi tải đơn hàng:', err)
  } finally {
    loading.value = false
  }
}

// Pagination
const paginatedOrders = computed(() => orders.value)

const visiblePages = computed(() => {
  const pages = []
  const maxPages = Math.min(totalPages.value, 5)
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(totalPages.value, start + maxPages - 1)
  if (end - start + 1 < maxPages) start = Math.max(1, end - maxPages + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

watch([searchQuery, filterStatus, currentPage], () => {
  fetchOrders()
})

onMounted(() => {
  fetchOrders()
})

// Format price
const formatPrice = (price) => price ? price.toLocaleString('vi-VN') + '₫' : '0₫'

// Format date
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })

// Modal detail
const openDetail = (order) => {
  selectedOrder.value = { ...order }
  showDetail.value = true
}

const closeDetail = () => {
  showDetail.value = false
  selectedOrder.value = null
}

const updateOrderStatus = async () => {
  try {
    await api.put(`/orders/${selectedOrder.value.id}`, { status: selectedOrder.value.status })
    fetchOrders()
    closeDetail()
  } catch (err) {
    console.error('Lỗi cập nhật trạng thái:', err)
    alert('Có lỗi khi cập nhật trạng thái đơn hàng')
  }
}
</script>

<style scoped>
/* Giữ nguyên style cũ của bạn */
.status-badge.pending { background: #fef3c7; color: #d97706; }
.status-badge.shipping { background: #dbeafe; color: #2563eb; }
.status-badge.completed { background: #d1fae5; color: #059669; }
.status-badge.cancelled { background: #fee2e2; color: #dc2626; }
</style>