<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý bình luận</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo nội dung, người bình luận..." @input="debouncedFetchComments" />
        </div>
        <select class="toolbar-filter" v-model="filterStatus" @change="fetchComments">
          <option value="">Tất cả trạng thái</option>
          <option value="pending">Chờ duyệt</option>
          <option value="approved">Đã duyệt</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách bình luận...</p>
    </div>

    <!-- Table -->
    <div v-else-if="comments.length === 0" class="empty-state">
      <p>Chưa có bình luận nào.</p>
    </div>
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Người bình luận</th>
              <th>Nội dung</th>
              <th>Sản phẩm</th>
              <th>Đánh giá</th>
              <th>Ngày</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="comment in paginatedComments" :key="comment.id">
              <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                  <div :style="{
                    width: '32px', height: '32px', borderRadius: '8px',
                    background: comment.avatar_color || '#6366f1', display: 'flex', alignItems: 'center',
                    justifyContent: 'center', fontWeight: '700', fontSize: '12px', color: '#fff'
                  }">{{ comment.author.charAt(0).toUpperCase() }}</div>
                  <span style="font-weight: 500; color: var(--admin-text);">{{ comment.author }}</span>
                </div>
              </td>
              <td style="max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ comment.content }}
              </td>
              <td style="font-weight: 500; color: var(--admin-text);">{{ comment.product_name || '—' }}</td>
              <td>
                <div style="display: flex; gap: 2px;">
                  <span v-for="star in 5" :key="star" :style="{ color: star <= comment.rating ? '#f59e0b' : 'var(--admin-border)', fontSize: '14px' }">★</span>
                </div>
              </td>
              <td>{{ formatDate(comment.created_at) }}</td>
              <td>
                <span class="status-badge" :class="comment.status === 'approved' ? 'active' : 'pending'">
                  {{ comment.status === 'approved' ? 'Đã duyệt' : 'Chờ duyệt' }}
                </span>
              </td>
              <td>
                <div class="action-btns">
                  <button
                    v-if="comment.status === 'pending'"
                    class="action-btn approve"
                    @click="approveComment(comment)"
                    title="Duyệt"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                  </button>
                  <button class="action-btn delete" @click="confirmDelete(comment)" title="Xoá">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination">
        <span class="pagination-info">Hiển thị {{ startItem }}-{{ endItem }} / {{ totalItems }} bình luận</span>
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

    <!-- Delete Confirmation -->
    <div class="admin-modal-overlay" v-if="showDeleteModal" @click.self="showDeleteModal = false">
      <div class="admin-modal slide-up" style="max-width: 420px;">
        <div class="admin-modal-body">
          <div class="confirm-dialog">
            <div class="confirm-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            <h4>Xoá bình luận?</h4>
            <p>Bạn có chắc muốn xoá bình luận của "{{ deleteTarget?.author }}"? Hành động này không thể hoàn tác.</p>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="showDeleteModal = false">Huỷ</button>
          <button class="admin-btn admin-btn-danger" @click="deleteComment">Xoá</button>
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
const itemsPerPage = 10
const loading = ref(false)
const showDeleteModal = ref(false)
const deleteTarget = ref(null)

const comments = ref([])
const totalItems = ref(0)
const totalPages = ref(1)

// Debounce search
let debounceTimer
const debouncedFetchComments = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchComments, 500)
}

// Fetch comments from API
const fetchComments = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      status: filterStatus.value || null,
      per_page: itemsPerPage,
      page: currentPage.value
    }
    const res = await api.get('/admin/comments', { params })
    comments.value = res.data.data || res.data || []
    totalItems.value = res.data.total || comments.value.length
    totalPages.value = res.data.last_page || Math.ceil(totalItems.value / itemsPerPage)
  } catch (err) {
    console.error('Lỗi tải bình luận:', err)
  } finally {
    loading.value = false
  }
}

// Pagination
const paginatedComments = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return comments.value.slice(start, start + itemsPerPage)
})

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
  fetchComments()
})

onMounted(() => {
  fetchComments()
})

// Format date
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })

// Approve comment
const approveComment = async (comment) => {
  try {
    await api.put(`/admin/comments/${comment.id}/approve`)
    comment.status = 'approved'
  } catch (err) {
    console.error('Lỗi duyệt bình luận:', err)
    alert('Có lỗi khi duyệt bình luận')
  }
}

// Delete comment
const confirmDelete = (comment) => {
  deleteTarget.value = comment
  showDeleteModal.value = true
}

const deleteComment = async () => {
  try {
    await api.delete(`/admin/comments/${deleteTarget.value.id}`)
    comments.value = comments.value.filter(c => c.id !== deleteTarget.value.id)
    showDeleteModal.value = false
  } catch (err) {
    console.error('Lỗi xoá bình luận:', err)
    alert('Có lỗi khi xoá bình luận')
  }
}
</script>

<style scoped>
/* Giữ nguyên style cũ của bạn, thêm nếu cần */
.status-badge {
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active {
  background: var(--admin-success-soft);
  color: var(--admin-success);
}

.status-badge.pending {
  background: var(--admin-warning-soft);
  color: var(--admin-warning);
}
</style>