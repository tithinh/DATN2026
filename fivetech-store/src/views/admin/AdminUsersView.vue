<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý người dùng</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo tên, email, SĐT..." @input="debouncedFetchUsers" />
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách người dùng...</p>
    </div>

    <!-- Table -->
    <div v-else-if="users.length === 0" class="empty-state">
      <p>Chưa có người dùng nào.</p>
    </div>
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên người dùng</th>
              <th>Email</th>
              <th>SĐT</th>
              <th>Trạng thái</th>
              <th>Ngày tạo</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in paginatedUsers" :key="user.admin_id || user.user_id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ user.admin_id || user.user_id }}</td>
              <td>
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div :style="{
                    width: '36px', height: '36px', borderRadius: '10px',
                    background: '#6366f1', display: 'flex', alignItems: 'center',
                    justifyContent: 'center', fontWeight: '700', fontSize: '14px', color: '#fff'
                  }">
                    {{ (user.full_name || user.name || 'U').charAt(0).toUpperCase() }}
                  </div>
                  <span style="font-weight: 500; color: var(--admin-text);">{{ user.full_name || user.name }}</span>
                </div>
              </td>
              <td>{{ user.email || '—' }}</td>
              <td>{{ user.phone || '—' }}</td>
              <td>
                <div class="admin-toggle" @click="toggleUserStatus(user)">
                  <button class="toggle-switch" :class="{ active: user.is_active }" type="button"></button>
                  <span style="font-size: 14px; color: var(--admin-text-soft);">{{ user.is_active ? 'Hoạt động' : 'Bị khoá' }}</span>
                </div>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="openEditModal(user)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination">
        <span class="pagination-info">Hiển thị {{ startItem }}-{{ endItem }} / {{ totalItems }} người dùng</span>
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

    <!-- Edit User Modal -->
    <div class="admin-modal-overlay" v-if="showEditModal" @click.self="showEditModal = false">
      <div class="admin-modal slide-up">
        <div class="admin-modal-header">
          <h3>Chỉnh sửa người dùng</h3>
          <button class="modal-close" @click="showEditModal = false">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body" v-if="editingUser">
          <div class="admin-form-group">
            <label>Họ tên</label>
            <input class="admin-input" v-model="editingUser.full_name" />
          </div>
          <div class="admin-form-group">
            <label>Email</label>
            <input class="admin-input" v-model="editingUser.email" type="email" />
          </div>
          <div class="admin-form-group">
            <label>Số điện thoại</label>
            <input class="admin-input" v-model="editingUser.phone" />
          </div>
          <div class="admin-form-group">
            <label>Địa chỉ</label>
            <input class="admin-input" v-model="editingUser.address" />
          </div>
          <div class="admin-form-group">
            <label>Trạng thái</label>
            <div class="admin-toggle" @click="editingUser.is_active = !editingUser.is_active">
              <button class="toggle-switch" :class="{ active: editingUser.is_active }" type="button"></button>
              <span style="font-size: 14px; color: var(--admin-text-soft);">{{ editingUser.is_active ? 'Hoạt động' : 'Bị khoá' }}</span>
            </div>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="showEditModal = false">Huỷ</button>
          <button class="admin-btn admin-btn-primary" @click="saveUser">Lưu thay đổi</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const loading = ref(false)
const showEditModal = ref(false)
const editingUser = ref(null)

const users = ref([])
const totalItems = ref(0)
const totalPages = ref(1)

// Debug log
const debouncedFetchUsers = () => {
  clearTimeout(window.userDebounce)
  window.userDebounce = setTimeout(fetchUsers, 500)
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      per_page: itemsPerPage,
      page: currentPage.value
    }

    console.log('🔄 Gọi API users:', '/admin/users', 'params:', params)

    const res = await api.get('/admin/users', { params })

    console.log('✅ Response users:', res.data)

    users.value = res.data.data || res.data || []
    totalItems.value = res.data.total || users.value.length
    totalPages.value = res.data.last_page || Math.ceil(totalItems.value / itemsPerPage)
  } catch (err) {
    console.error('❌ Lỗi tải người dùng:', err)
    console.log('Response lỗi:', err.response?.data)
  } finally {
    loading.value = false
  }
}

// Pagination
const paginatedUsers = computed(() => users.value)

const visiblePages = computed(() => {
  const pages = []
  const maxPages = Math.min(totalPages.value, 5)
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(totalPages.value, start + maxPages - 1)
  if (end - start + 1 < maxPages) start = Math.max(1, end - maxPages + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

watch([searchQuery, currentPage], () => {
  fetchUsers()
})

onMounted(() => {
  fetchUsers()
})

// Format date
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })

// Toggle status
const toggleUserStatus = async (user) => {
  try {
    const newStatus = !user.is_active
    await api.put(`/admin/users/${user.admin_id || user.user_id}/toggle-status`, { is_active: newStatus })
    user.is_active = newStatus
  } catch (err) {
    console.error('Lỗi toggle status:', err)
    alert('Có lỗi khi cập nhật trạng thái')
  }
}

// Edit modal
const openEditModal = (user) => {
  editingUser.value = { ...user }
  showEditModal.value = true
}

const saveUser = async () => {
  try {
    const id = editingUser.value.admin_id || editingUser.value.user_id
    const payload = {
      full_name: editingUser.value.full_name,
      email: editingUser.value.email,
      phone: editingUser.value.phone,
      address: editingUser.value.address,
      is_active: editingUser.value.is_active
    }
    await api.put(`/admin/users/${id}`, payload)
    fetchUsers()
    showEditModal.value = false
  } catch (err) {
    console.error('Lỗi cập nhật user:', err)
    alert('Có lỗi khi lưu thay đổi')
  }
}
</script>

<style scoped>
/* Giữ nguyên style cũ của bạn */
.admin-toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.toggle-switch {
  width: 40px;
  height: 20px;
  background: #cbd5e1;
  border-radius: 20px;
  position: relative;
  transition: background 0.3s;
}

.toggle-switch.active {
  background: var(--admin-success);
}

.toggle-switch::before {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  background: white;
  border-radius: 50%;
  top: 2px;
  left: 2px;
  transition: transform 0.3s;
}

.toggle-switch.active::before {
  transform: translateX(20px);
}
</style>