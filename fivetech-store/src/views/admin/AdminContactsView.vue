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
          <input type="text" v-model="searchQuery" placeholder="Tìm theo tên, email..." />
        </div>
        <select class="toolbar-filter" v-model="filterStatus">
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ xử lý</option>
          <option value="replied">Đã phản hồi</option>
          <option value="spam">Spam</option>
        </select>
      </div>
      <!-- No Add Button for Contacts -->
    </div>

    <!-- Table -->
    <div class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Khách hàng</th>
              <th>Email</th>
              <th>Chủ đề</th>
              <th>Ngày gửi</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="msg in filteredMessages" :key="msg.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ msg.id }}</td>
              <td style="font-weight:500; color: var(--admin-text);">{{ msg.name }}</td>
              <td>{{ msg.email }}</td>
              <td>
                <span class="role-badge" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">{{ msg.subject }}</span>
              </td>
              <td style="color: var(--admin-text-soft);">{{ msg.date }}</td>
              <td>
                <span class="status-badge" :class="msg.statusClass">{{ msg.statusText }}</span>
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

      <!-- Pagination -->
      <div class="admin-pagination">
        <div class="pagination-info">Hiển thị 1-{{ filteredMessages.length }} trên tổng số {{ messages.length }} liên hệ</div>
        <div class="pagination-btns">
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>
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
              <span class="value">{{ currentMessage.subject }}</span>
            </div>
            <div class="detail-row">
              <span class="label">Ngày gửi:</span>
              <span class="value">{{ currentMessage.date }}</span>
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
import { ref, computed } from 'vue'

const searchQuery = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const currentMessage = ref({})

const messages = ref([
  {
    id: 1001,
    name: 'Nguyễn Văn A',
    email: 'nguyenvana@example.com',
    phone: '0912345678',
    subject: 'Bảo hành',
    content: 'Sản phẩm tôi mua hôm qua bị lỗi màn hình, tôi muốn được bảo hành.',
    date: '12/02/2026',
    status: 'pending',
    statusText: 'Chờ xử lý',
    statusClass: 'pending'
  },
  {
    id: 1002,
    name: 'Trần Thị B',
    email: 'tranthib@example.com',
    phone: '0987654321',
    subject: 'Đơn hàng',
    content: 'Tôi muốn thay đổi địa chỉ giao hàng cho đơn #DH1234.',
    date: '11/02/2026',
    status: 'replied',
    statusText: 'Đã phản hồi',
    statusClass: 'completed'
  },
  {
    id: 1003,
    name: 'Lê Văn C',
    email: 'levanc@example.com',
    phone: '',
    subject: 'Sản phẩm',
    content: 'Khi nào thì sản phẩm iPhone 16 Pro Max có hàng lại?',
    date: '10/02/2026',
    status: 'pending',
    statusText: 'Chờ xử lý',
    statusClass: 'pending'
  }
])

const filteredMessages = computed(() => {
  return messages.value.filter(msg => {
    const matchSearch = msg.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                      msg.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchStatus = filterStatus.value ? msg.status === filterStatus.value : true
    return matchSearch && matchStatus
  })
})

const viewMessage = (msg) => {
  currentMessage.value = msg
  showModal.value = true
}

const deleteMessage = (id) => {
  if(confirm('Bạn có chắc muốn xóa liên hệ này?')) {
    messages.value = messages.value.filter(m => m.id !== id)
  }
}

const markAsSpam = () => {
  // Logic mark as spam
  const index = messages.value.findIndex(m => m.id === currentMessage.value.id)
  if (index !== -1) {
    messages.value[index].status = 'spam'
    messages.value[index].statusText = 'Spam'
    messages.value[index].statusClass = 'cancelled'
  }
  closeModal()
}

const closeModal = () => {
  showModal.value = false
}
</script>

<style scoped>
/* Reuse modal styles */
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
</style>
