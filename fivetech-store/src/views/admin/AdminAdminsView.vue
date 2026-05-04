<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý admin</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm kiếm admin" @input="debouncedFetchAdmins" />
        </div>
        <select class="toolbar-filter" v-model="filterRole" @change="fetchAdmins">
          <option value="">Tất cả vai trò</option>
          <option value="admin">Admin</option>
        </select>
        <select class="toolbar-filter" v-model="filterActive" @change="fetchAdmins">
          <option value="">Tất cả trạng thái</option>
          <option value="1">Hoạt động</option>
          <option value="0">Vô hiệu hóa</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm admin
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách admin...</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên vai trò</th>
              <th>Email</th>
              <th>Vai trò</th>
              <th>Trạng thái</th>
              <th>Ngày tạo</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="admin in paginatedAdmins" :key="admin.admin_id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ admin.admin_id }}</td>
              <td style="font-weight:500;">{{ admin.full_name }}</td>
              <td style="color: var(--admin-text-soft); font-size: 14px;">{{ admin.email }}</td>
              <td>
                <span class="role-badge" :class="admin.role">{{ formatRole(admin.role) }}</span>
              </td>
              <td>
                <div class="admin-toggle" @click="toggleStatus(admin)">
                  <button class="toggle-switch" :class="{ active: admin.is_active }" type="button"></button>
                  <span style="font-size: 14px; color: var(--admin-text-soft);">{{ admin.is_active ? 'Hoạt động' : 'Vô hiệu hóa' }}</span>
                </div>
              </td>
              <td>{{ formatDate(admin.created_at) }}</td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="openEditModal(admin)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                  </button>
                  <button class="action-btn delete" @click="deleteAdmin(admin)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedAdmins.length === 0">
              <td colspan="9" style="text-align: center; padding: 40px; color: var(--admin-text-muted);">
                Không tìm thấy admin nào
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination" v-if="totalItems > 0">
        <span class="pagination-info">Hiển thị {{ startItem }}-{{ endItem }} / {{ totalItems }} admin</span>
        <div class="pagination-btns">
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage = 1">Đầu</button>
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">Trước</button>
          <button v-for="page in visiblePages" :key="page" class="page-btn" :class="{ active: page === currentPage }" @click="currentPage = page">
            {{ page }}
          </button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Sau</button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage = totalPages">Cuối</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Admin Modal -->
    <div class="admin-modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="admin-modal" style="max-width: 550px;">
        <div class="admin-modal-header">
          <h3>{{ isEditing ? 'Chỉnh sửa admin' : 'Thêm admin mới' }}</h3>
          <button class="modal-close" @click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label>Email <span class="required">*</span></label>
              <input class="admin-input" type="email" v-model="form.email" placeholder="example@admin.com" />
            </div>
            <div class="admin-form-group">
              <label>Vai trò <span class="required">*</span></label>
              <select class="admin-select" v-model="form.role">
                <option value="">Chọn vai trò</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
          </div>
          <div class="admin-form-group">
            <label>{{ isEditing ? 'Mật khẩu mới (để trống nếu không đổi)' : 'Mật khẩu <span class="required">*</span>' }}</label>
            <input class="admin-input" type="password" v-model="form.password" :placeholder="isEditing ? 'Nhập mật khẩu mới (tùy chọn)' : 'Nhập mật khẩu (tối thiểu 6 ký tự)'" />
            <small v-if="!isEditing" style="color: var(--admin-text-muted);">Mật khẩu tối thiểu 6 ký tự</small>
          </div>
          <div class="admin-form-group">
            <label style="display: flex; align-items: center; gap: 8px;">
              <input type="checkbox" v-model="form.is_active" id="is_active" />
              <span>Kích hoạt tài khoản</span>
            </label>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeModal">Huỷ</button>
          <button class="admin-btn admin-btn-primary" @click="submitForm" :disabled="!isFormValid">
            {{ isEditing ? 'Cập nhật' : 'Thêm admin' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const filterRole = ref('')
const filterActive = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)

const form = ref({
  admin_id: null,
  username: '',
  email: '',
  full_name: '',
  role: '',
  password: '',
  is_active: true
})

const admins = ref([])
const totalItems = ref(0)

// Debounce search
let debounceTimer
const debouncedFetchAdmins = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchAdmins, 500)
}

const fetchAdmins = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      role: filterRole.value,
      is_active: filterActive.value || null,
      per_page: itemsPerPage,
      page: currentPage.value
    }
    const res = await api.get('/admin/admins', { params })
    admins.value = res.data.data || []
    totalItems.value = res.data.total || 0
  } catch (err) {
    console.error('Lỗi tải admins:', err)
  } finally {
    loading.value = false
  }
}

// Computed
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value))
const paginatedAdmins = computed(() => admins.value)
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage))
const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let startPage = Math.max(1, currentPage.value - 2)
  let endPage = Math.min(totalPages.value, startPage + maxVisible - 1)
  if (endPage - startPage + 1 < maxVisible) startPage = Math.max(1, endPage - maxVisible + 1)
  for (let i = startPage; i <= endPage; i++) pages.push(i)
  return pages
})

const isFormValid = computed(() => {
  const f = form.value
  return f.username && f.email && f.full_name && f.role && (isEditing.value || f.password.length >= 6)
})

// Methods
const formatRole = (role) => role === 'super_admin' ? 'Super Admin' : 'Admin'
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN')

const openAddModal = () => {
  isEditing.value = false
  form.value = { admin_id: null, username: '', email: '', full_name: '', role: '', password: '', is_active: true }
  showModal.value = true
  nextTick()
}

const openEditModal = (admin) => {
  isEditing.value = true
  form.value = {
    admin_id: admin.admin_id,
    username: admin.username,
    email: admin.email,
    full_name: admin.full_name,
    role: admin.role,
    password: '',
    is_active: admin.is_active
  }
  showModal.value = true
  nextTick()
}

const closeModal = () => {
  showModal.value = false
}

const deleteAdmin = async (admin) => {
  if (!confirm(`Xóa admin "${admin.full_name}" (${admin.username})?`)) return
  try {
    await api.delete(`/admin/admins/${admin.admin_id}`)
    fetchAdmins()
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi xóa admin')
  }
}

const toggleStatus = async (admin) => {
  try {
    await api.put(`/admin/admins/${admin.admin_id}/toggle-status`)
    admin.is_active = !admin.is_active
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi cập nhật trạng thái')
  }
}

const submitForm = async () => {
  try {
    let res
    if (isEditing.value) {
      res = await api.put(`/admin/admins/${form.value.admin_id}`, form.value)
    } else {
      res = await api.post('/admin/admins', form.value)
    }
    currentPage.value = 1
    fetchAdmins()
    closeModal()
  } catch (err) {
    const msg = err.response?.data?.message || 'Lỗi lưu admin'
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      const firstError = Object.values(errors)[0][0]
      alert(firstError)
    } else {
      alert(msg)
    }
  }
}

// Watchers
watch([searchQuery, filterRole, filterActive], () => {
  currentPage.value = 1
  fetchAdmins()
})

watch(currentPage, fetchAdmins)

onMounted(fetchAdmins)
</script>

<style scoped>
/* Scoped styles for custom elements */
.table-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: white;
  font-size: 14px;
  text-transform: uppercase;
}

.username-badge {
  background: var(--admin-bg);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  border: 1px solid var(--admin-border);
}

.role-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.role-badge.admin { background: #fef3c7; color: #92400e; }
.role-badge.super_admin { background: #dbeafe; color: #1e3a8a; }

.admin-modal {
  animation: slide-up 0.3s ease;
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.required {
  color: var(--admin-danger);
}
</style>

