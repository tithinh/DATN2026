<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý liên hệ</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo tên, email..." @input="handleSearch" />
        </div>
        <select class="toolbar-filter" v-model="filterStatus" @change="handleFilter">
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xử lý</option>
          <option value="replied">Đã phản hồi</option>
          <option value="spam">Spam</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Đang tải dữ liệu...</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Khách hàng</th>
              <th>Email</th>
              <th>Số điện thoại</th>
              <th>Chủ đề</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="msg in messages" :key="msg.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ msg.id }}</td>
              <td style="font-weight:500; color: var(--admin-text);">{{ msg.name }}</td>
              <td>{{ msg.email }}</td>
              <td>{{ msg.phone || '-' }}</td>
              <td>
                <span class="role-badge" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">{{ formatSubject(msg.subject) }}</span>
              </td>
              <td style="color: var(--admin-text-soft);">{{ formatDate(msg.created_at) }}</td>
              <td>
                <span class="status-badge" :class="getStatusClass(msg.status)">{{ getStatusText(msg.status) }}</span>
              </td>
              <td>
                <div class="action-btns">
                  <button class="action-btn view" @click="viewMessage(msg)" title="Xem chi tiết">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                  <button class="action-btn delete" @click="deleteMessage(msg.id)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && !messages.length" class="empty-state">
        <p>Chưa có liên hệ nào</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="admin-pagination">
        <div class="pagination-info">Hiển thị {{ messages.length }} trên tổng số {{ pagination.total }} liên hệ</div>
        <div class="pagination-btns">
          <button class="page-btn" :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button 
            v-for="page in visiblePages" 
            :key="page" 
            class="page-btn" 
            :class="{ active: page === pagination.current_page }"
            @click="changePage(page)"
          >
            {{ page }}
          </button>
          <button class="page-btn" :disabled="pagination.current_page === pagination.last_page" @click="changePage(pagination.current_page + 1)">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal View Detail -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Chi tiết liên hệ #{{ currentMessage.id }}</h3>
          <button class="close-modal" @click="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="message-detail-grid">
            <div class="detail-row">
              <span class="label">Người gửi:</span>
              <span class="value">{{ currentMessage.name }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Email:</span>
              <span class="value">{{ currentMessage.email }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Số điện thoại:</span>
              <span class="value">{{ currentMessage.phone || 'Không có' }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Chủ đề:</span>
              <span class="value">{{ formatSubject(currentMessage.subject) }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Ngày gửi:</span>
              <span class="value">{{ formatDate(currentMessage.created_at) }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Trạng thái:</span>
              <span class="status-badge" :class="getStatusClass(currentMessage.status)">{{ getStatusText(currentMessage.status) }}</span>
            </div>
            <div class="detail-row full-width">
              <span class="label">Nội dung:</span>
              <div class="message-content">
                {{ currentMessage.content }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="admin-btn admin-btn-outline" @click="markAsSpam">Đánh dấu Spam</button>
          <a :href="'mailto:' + currentMessage.email" class="admin-btn admin-btn-primary">Phản hồi qua Email</a>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getAdminContacts, updateContactStatus, deleteContact, markContactAsSpam } from '@/api'

// State
const messages = ref([])
const loading = ref(true)
const searchQuery = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const currentMessage = ref({})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

// Computed
const visiblePages = computed(() => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Methods
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatSubject = (subject) => {
  const subjects = {
    'order': 'Đơn hàng',
    'product': 'Sản phẩm',
    'warranty': 'Bảo hành',
    'return': 'Đổi trả',
    'other': 'Khác'
  }
  return subjects[subject] || subject || 'Khác'
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'pending',
    'replied': 'completed',
    'spam': 'cancelled'
  }
  return classes[status] || 'pending'
}

const getStatusText = (status) => {
  const texts = {
    'pending': 'Chờ xử lý',
    'replied': 'Đã phản hồi',
    'spam': 'Spam'
  }
  return texts[status] || 'Chờ xử lý'
}

const fetchMessages = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: 15,
      search: searchQuery.value || undefined,
      status: filterStatus.value || undefined
    }
    
    const response = await getAdminContacts(params)
    const data = response.data
    
    messages.value = data.data || data
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || data.length
    }
  } catch (error) {
    console.error('Error fetching contacts:', error)
    messages.value = []
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.value.current_page = 1
  fetchMessages(1)
}

const handleFilter = () => {
  pagination.value.current_page = 1
  fetchMessages(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchMessages(page)
  }
}

const viewMessage = (msg) => {
  currentMessage.value = msg
  showModal.value = true
}

const deleteMessage = async (id) => {
  if(confirm('Bạn có chắc muốn xóa liên hệ này?')) {
    try {
      await deleteContact(id)
      fetchMessages(pagination.value.current_page)
    } catch (error) {
      console.error('Error deleting contact:', error)
      alert('Có lỗi xảy ra khi xóa liên hệ')
    }
  }
}

const markAsSpam = async () => {
  try {
    await markContactAsSpam(currentMessage.value.id)
    // Update local state
    const index = messages.value.findIndex(m => m.id === currentMessage.value.id)
    if (index !== -1) {
      messages.value[index].status = 'spam'
    }
    closeModal()
  } catch (error) {
    console.error('Error marking as spam:', error)
    alert('Có lỗi xảy ra')
  }
}

const closeModal = () => {
  showModal.value = false
}

// Lifecycle
onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--admin-text-muted);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--admin-border);
  border-top-color: #ff6b35;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--admin-text-muted);
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: var(--admin-bg-card);
  width: 100%;
  max-width: 600px;
  border-radius: var(--admin-radius-lg);
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--admin-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 { margin: 0; font-size: 18px; color: var(--admin-text); }

.close-modal { background: none; border: none; font-size: 24px; color: var(--admin-text-muted); cursor: pointer; }

.modal-body { padding: 24px; }

.modal-footer {
  padding: 20px 24px;
  border-top: 1px solid var(--admin-border);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

/* Detail Grid */
.message-detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.detail-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-row.full-width {
  grid-column: span 2;
}

.label {
  font-size: 12px;
  color: var(--admin-text-muted);
  font-weight: 600;
}

.value {
  font-size: 14px;
  color: var(--admin-text);
  font-weight: 500;
}

.message-content {
  padding: 16px;
  background: var(--admin-bg);
  border-radius: 8px;
  border: 1px solid var(--admin-border);
  min-height: 100px;
  white-space: pre-wrap;
  color: var(--admin-text-soft);
}

/* Status Badges */
.status-badge.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #f59e0b;
}

.status-badge.completed {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
}

.status-badge.cancelled {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}
</style>
