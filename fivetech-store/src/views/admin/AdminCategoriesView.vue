<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý danh mục</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm kiếm danh mục..." @input="debouncedFetchCategories" />
        </div>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm danh mục
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách danh mục...</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Mô tả</th>
              <th>Số sản phẩm</th>
              <th>Ngày tạo</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cat in categories" :key="cat.category_id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ cat.category_id }}</td>
              <td style="font-weight:600; color: var(--admin-text);">
                <span style="display: inline-flex; align-items: center; gap: 8px;">
                  <span :style="{ width: '8px', height: '8px', borderRadius: '50%', background: cat.color || '#6366f1', display: 'inline-block' }"></span>
                  {{ cat.name }}
                </span>
              </td>
              <td style="max-width: 300px;">{{ cat.description || '—' }}</td>
              <td>
                <span style="background: var(--admin-info-soft); color: var(--admin-info); padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                  {{ cat.products_count || 0 }} sản phẩm
                </span>
              </td>
              <td>{{ formatDate(cat.created_at) }}</td>
              <td>
                <div class="admin-toggle" @click="toggleVisibility(cat)">
                  <button class="toggle-switch" :class="{ active: cat.is_visible }" type="button"></button>
                  <span style="font-size: 14px; color: var(--admin-text-soft);">{{ cat.is_visible ? 'Hiển thị' : 'Ẩn' }}</span>
                </div>
              </td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="openEditModal(cat)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                  </button>
                  <button class="action-btn delete" @click="deleteCategory(cat)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="admin-modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="admin-modal slide-up">
        <div class="admin-modal-header">
          <h3>{{ isEditing ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' }}</h3>
          <button class="modal-close" @click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body">
          <div class="admin-form-group">
            <label>Tên danh mục <span class="required">*</span></label>
            <input class="admin-input" v-model="form.name" placeholder="Nhập tên danh mục" />
          </div>
          <div class="admin-form-group">
            <label>Màu sắc (hex)</label>
            <input class="admin-input" v-model="form.color" placeholder="#6366f1" />
          </div>
          <div class="admin-form-group">
            <label>Mô tả</label>
            <textarea class="admin-textarea" v-model="form.description" placeholder="Nhập mô tả danh mục..." rows="4"></textarea>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeModal">Huỷ</button>
          <button class="admin-btn admin-btn-primary" @click="submitForm">
            {{ isEditing ? 'Cập nhật' : 'Thêm danh mục' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import { useAdminAuthStore } from '@/stores/adminAuth'
import api from '@/api'

const adminStore = useAdminAuthStore()

const searchQuery = ref('')
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const errorMessage = ref('')

const form = ref({
  id: null,
  name: '',
  slug: '',
  color: '#6366f1',
  description: '',
  is_visible: true
})

const categories = ref([])

// ✅ FIX: slug hỗ trợ tiếng Việt
const generateSlug = (name) => {
  if (!name) return ''
  return name
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

// ✅ Auto generate slug
watch(() => form.value.name, (newName) => {
  form.value.slug = generateSlug(newName)
})

// ✅ Fetch categories (safe)
const fetchCategories = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await api.get('/admin/categories', {
      params: { search: searchQuery.value.trim() }
    })

    let rawCategories = Array.isArray(res.data?.data)
      ? res.data.data
      : Array.isArray(res.data)
      ? res.data
      : [];
    
    categories.value = rawCategories.map(cat => ({
      ...cat,
      is_visible: Boolean(cat.is_active)
    }))
  } catch (err) {
    console.error(err)
    errorMessage.value = err.response?.data?.message || 'Lỗi tải danh mục'
  } finally {
    loading.value = false
  }
}

// ✅ Debounce (chỉ dùng 1 chỗ)
let debounceTimer
watch(searchQuery, () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchCategories, 500)
})

onMounted(fetchCategories)

// Format date
const formatDate = (date) =>
  date ? new Date(date).toLocaleDateString('vi-VN') : '—'

// Modal
const openAddModal = () => {
  isEditing.value = false
  form.value = {
    id: null,
    name: '',
    slug: '',
    color: '#6366f1',
    description: '',
    is_visible: true
  }
  showModal.value = true

  nextTick(() => {
    document.querySelector('.admin-input')?.focus()
  })
}

const openEditModal = (cat) => {
  isEditing.value = true
  form.value = {
    id: cat.category_id,
    name: cat.name,
    slug: cat.slug,
    color: cat.color || '#6366f1',
    description: cat.description || '',
    is_visible: cat.is_active ?? cat.is_visible ?? true
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

// Delete
const deleteCategory = async (cat) => {
  if (!confirm(`Bạn có chắc muốn xóa "${cat.name}"?`)) return
  try {
    await api.delete(`/admin/categories/${cat.category_id}`)
    await fetchCategories()
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || 'Lỗi khi xóa danh mục'
  }
}

// Submit
const submitForm = async () => {
  if (!form.value.name.trim() || !form.value.slug.trim()) {
    errorMessage.value = 'Vui lòng nhập tên và slug'
    return
  }

  submitting.value = true
  errorMessage.value = ''

  try {
    const payload = {
      name: form.value.name.trim(),
      slug: form.value.slug.trim().toLowerCase(),
      description: form.value.description || null,
      color: form.value.color,
      is_active: form.value.is_visible,
      is_featured: false,
      parent_id: null,
      icon: null,
      sort_order: null
    }

    if (isEditing.value) {
      await api.put(`/admin/categories/${form.value.id}`, payload)
    } else {
      await api.post('/admin/categories', payload)
    }

    await fetchCategories()
    closeModal()
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      errorMessage.value = errors
        ? Object.values(errors)[0]?.[0]
        : 'Dữ liệu không hợp lệ'
    } else if (err.response?.status === 409) {
      errorMessage.value = 'Slug đã tồn tại'
    } else {
      errorMessage.value =
        err.response?.data?.message || 'Lỗi khi lưu danh mục'
    }
  } finally {
    submitting.value = false
  }
}

// ✅ Toggle chuẩn (sync backend)
const toggleVisibility = async (cat) => {
  try {
    await api.put(`/admin/categories/${cat.category_id}/toggle-status`)
    await fetchCategories()
  } catch (err) {
    errorMessage.value = 'Lỗi cập nhật trạng thái'
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