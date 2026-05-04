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
          <option value="cancelled">Đã hủy</option>
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
                <th>Thanh toán</th>
                <th>Trạng thái</th>


            </tr>
          </thead>
          <tbody>
            <tr v-for="order in paginatedOrders" :key="order.id">
              <td style="font-weight:700; color: var(--admin-text);">{{ order.order_code || '#' + order.id }}</td>
              <td style="font-weight:500; color: var(--admin-text);">{{ order.customer_name }}</td>
              <td>{{ order.phone }}</td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td style="font-weight:600; color: var(--admin-warning);">{{ formatPrice(order.total_amount) }}</td>
              <td>
                <div class="status-cell" v-if="editingOrderId !== order.id">
                  <span 
                    class="status-badge" 
                    :class="order.status"
                    @click="startEditStatus(order)"
                    style="cursor: pointer; user-select: none;"
                    title="Click để chỉnh sửa trạng thái"
                  >
                    {{ order.status_text || statusTexts[order.status] }}
                  </span>
                </div>
                <div v-else class="status-edit-container">
<select 
                    v-model="tempStatus" 
                    class="status-select"
                    @change="confirmEditStatus(order)"
                    @blur="cancelEditStatus(order)"
                  >
                    <option value="pending">Chờ xử lý</option>
                    <option value="shipping">Đang giao</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                  </select>
                  <span 
                    v-if="updatingOrderId === order.id" 
                    class="status-loading"
                    title="Đang cập nhật..."
                  >
                    ⏳
                  </span>
                </div>
              </td>
              <td>
                <div class="action-btns">
                  <!-- <button class="action-btn delete" @click="openCancelModal(order)" title="Hủy đơn hàng">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                  </button> -->
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
                <span class="info-value">{{ selectedOrder.shipping_address || 'Không có' }}</span>
              </div>
            </div>
            <div class="order-info-card">
              <h4>📦 Thông tin đơn hàng</h4>
              <div class="info-row">
                <span class="info-label">Mã đơn:</span>
                <span class="info-value" style="font-weight:700;">{{ selectedOrder.order_code || '#' + selectedOrder.id }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ formatDate(selectedOrder.created_at) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Tổng tiền:</span>
                <span class="info-value" style="color: var(--admin-warning); font-weight:700;">{{ formatPrice(selectedOrder.total_amount) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Giảm giá:</span>
                <span class="info-value">{{ formatPrice(selectedOrder.discount_amount) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Thanh toán:</span>
                <span class="info-value" style="color: var(--admin-success); font-weight:700;">{{ formatPrice(selectedOrder.final_amount) }}</span>
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
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="font-weight: 600;">{{ selectedOrder.payment_method || 'COD' }}</td>
                    <td>
                      <span class="status-badge" :class="selectedOrder.status === 'completed' || selectedOrder.status === 'delivered' ? 'completed' : 'pending'">
                        {{ selectedOrder.status === 'completed' || selectedOrder.status === 'delivered' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Order Items -->
          <div style="margin-bottom: 20px;">
            <h4 style="font-size: 14px; font-weight: 600; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">📝 Sản phẩm trong đơn</h4>
            <div class="admin-table-wrapper" style="background: var(--admin-bg); border: 1px solid var(--admin-border); border-radius: var(--admin-radius);">
              <table class="admin-table">
                <thead>
                  <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in selectedOrder.items" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <div style="font-weight: 500;">{{ item.product_name }}</div>
                    </td>
                    <td>{{ item.variant_name || 'Mặc định' }}</td>
                    <td>{{ formatPrice(item.price) }}</td>
                    <td>{{ item.quantity }}</td>
                    <td style="font-weight: 600; color: var(--admin-warning);">{{ formatPrice(item.price * item.quantity) }}</td>
                  </tr>
                </tbody>
                <tfoot v-if="selectedOrder.items && selectedOrder.items.length > 0">
                  <tr style="background: var(--admin-bg);">
                    <td colspan="5" style="text-align: right; font-weight: 600;">Tạm tính:</td>
                    <td style="font-weight: 600;">{{ formatPrice(selectedOrder.total_amount) }}</td>
                  </tr>
                  <tr v-if="selectedOrder.discount_amount > 0" style="background: var(--admin-bg);">
                    <td colspan="5" style="text-align: right; font-weight: 600; color: var(--admin-success);">Giảm giá:</td>
                    <td style="font-weight: 600; color: var(--admin-success);">-{{ formatPrice(selectedOrder.discount_amount) }}</td>
                  </tr>
                  <tr style="background: var(--admin-bg);">
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 15px;">Tổng cộng:</td>
                    <td style="font-weight: 700; font-size: 15px; color: var(--admin-danger);">{{ formatPrice(selectedOrder.final_amount) }}</td>
                  </tr>
                </tfoot>
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
              <option value="cancelled">Đã hủy</option>
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

// Inline editing
const editingOrderId = ref(null)
const tempStatus = ref('')
const updatingOrderId = ref(null)

const statusTexts = {
  pending: 'Chờ xử lý',
  shipping: 'Đang giao',
  completed: 'Hoàn thành'
}

// Computed for pagination
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value))

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
    const res = await api.get('/admin/orders', { params })
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
const formatPrice = (price) => price ? new Intl.NumberFormat('vi-VN').format(price) + '₫' : '0₫'

// Format date
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })

// Modal detail
// Fetch order detail
const openDetail = async (order) => {
  try {
    const orderId = order.order_id || order.id
    const res = await api.get(`/admin/orders/${orderId}`)
    selectedOrder.value = res.data
    showDetail.value = true
  } catch (err) {
    console.error('Lỗi tải chi tiết đơn hàng:', err)
    // Fallback to basic info
    selectedOrder.value = { ...order }
    showDetail.value = true
  }
}

// Inline status editing functions
const startEditStatus = (order) => {
  editingOrderId.value = order.id || order.order_id
  tempStatus.value = order.status
}

const confirmEditStatus = async (order) => {
  if (tempStatus.value === order.status) {
    cancelEditStatus()
    return
  }

  updatingOrderId.value = order.id || order.order_id
  try {
    const orderId = order.id || order.order_id
    await api.put(`/admin/orders/${orderId}/status`, { status: tempStatus.value })
    
    // Optimistic update
    const orderIndex = orders.value.findIndex(o => (o.id || o.order_id) === orderId)
    if (orderIndex !== -1) {
      orders.value[orderIndex].status = tempStatus.value
      orders.value[orderIndex].status_text = statusTexts[tempStatus.value]
    }
    
    alert('Cập nhật trạng thái thành công!')
  } catch (err) {
    console.error('Lỗi cập nhật trạng thái:', err)
    alert('Có lỗi khi cập nhật trạng thái. Vui lòng thử lại.')
  } finally {
    updatingOrderId.value = null
    editingOrderId.value = null
  }
}

const cancelEditStatus = (order) => {
  editingOrderId.value = null
  tempStatus.value = ''
}


const closeDetail = () => {
  showDetail.value = false
  selectedOrder.value = null
}

const updateOrderStatus = async () => {
  try {
    const orderId = selectedOrder.value.order_id || selectedOrder.value.id
    await api.put(`/admin/orders/${orderId}/status`, { status: selectedOrder.value.status })
    fetchOrders()
    closeDetail()
    alert('Cập nhật trạng thái thành công!')
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

.status-cell {
  position: relative;
  display: inline-block;
}

.status-edit-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-select {
  padding: 4px 8px;
  border: 1px solid var(--admin-border);
  border-radius: 4px;
  background: white;
  font-size: 13px;
  min-width: 100px;
}

.status-loading {
  font-size: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
