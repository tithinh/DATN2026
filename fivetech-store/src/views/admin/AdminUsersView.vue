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
                  <div style="display: flex; flex-direction: column; gap: 2px;">
                    <span style="font-weight: 500; color: var(--admin-text);">{{ user.full_name || user.name }}</span>
                    <span v-if="user.google_id" class="google-badge">
                      <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"><path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/><path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2970244 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/><path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/><path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/></svg>
                      Google
                    </span>
                  </div>
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
          <div class="admin-form-group">
            <label>Đặt lại mật khẩu <span style="font-size: 12px; color: var(--admin-text-soft); font-weight: 400;">(bỏ trống nếu không đổi)</span></label>
            <div v-if="editingUser.google_id" class="google-login-note">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/><path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2970244 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/><path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/><path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/></svg>
              Người dùng đăng nhập qua Google — chưa có mật khẩu gốc. Có thể đặt mật khẩu để cho phép đăng nhập bằng email.
            </div>
            <div class="password-input-wrapper">
              <input
                class="admin-input"
                v-model="editingUser.new_password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                autocomplete="new-password"
              />
              <button type="button" class="password-toggle-btn" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
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
const showPassword = ref(false)

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


    const res = await api.get('/admin/users', { params })


    users.value = res.data.data || res.data || []
    totalItems.value = res.data.total || users.value.length
    totalPages.value = res.data.last_page || Math.ceil(totalItems.value / itemsPerPage)
  } catch (err) {
    console.error('❌ Lỗi tải người dùng:', err)
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
  editingUser.value = { ...user, new_password: '' }
  showPassword.value = false
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
    // Chỉ gửi mật khẩu mới nếu admin có nhập
    if (editingUser.value.new_password && editingUser.value.new_password.trim()) {
      if (editingUser.value.new_password.length < 6) {
        alert('Mật khẩu phải có ít nhất 6 ký tự!')
        return
      }
      payload.new_password = editingUser.value.new_password
    }
    await api.put(`/admin/users/${id}`, payload)
    fetchUsers()
    showEditModal.value = false
    alert('Cập nhật thông tin người dùng thành công!' + (payload.new_password ? ' Mật khẩu đã được đặt lại.' : ''))
  } catch (err) {
    console.error('Lỗi cập nhật user:', err)
    alert(err.response?.data?.message || 'Có lỗi khi lưu thay đổi')
  }
}

const deleteUser = async (user) => {
  if (!confirm(`Bạn có chắc muốn xóa người dùng "${user.full_name || user.email}"?`)) return
  try {
    await api.delete(`/admin/users/${user.user_id}`)
    await fetchUsers()
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi xóa người dùng')
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

.password-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-wrapper .admin-input {
  padding-right: 44px;
  width: 100%;
}

.password-toggle-btn {
  position: absolute;
  right: 8px;
  background: none;
  border: none;
  cursor: pointer;
  color: var(--admin-text-soft);
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: color 0.2s;
}

.password-toggle-btn:hover {
  color: var(--admin-text);
}

.google-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 600;
  color: #5f6368;
  background: #f1f3f4;
  border: 1px solid #dadce0;
  border-radius: 4px;
  padding: 1px 6px;
  width: fit-content;
}

.google-login-note {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 12px;
  color: #5f6368;
  background: #f8f9fa;
  border: 1px solid #dadce0;
  border-radius: 8px;
  padding: 8px 12px;
  margin-bottom: 8px;
  line-height: 1.5;
}

.google-login-note svg {
  flex-shrink: 0;
  margin-top: 1px;
}
</style>