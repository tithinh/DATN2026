<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý đánh giá và bình luận</h1>
    </div>

    <!-- Stats Cards -->
    <div class="stats-row" v-if="stats.total > 0 || !loading">
      <div class="stat-card">
        <div class="stat-icon total">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.total }}</span>
          <span class="stat-label">Tổng cộng</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon ratings">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.total_ratings }}</span>
          <span class="stat-label">Đánh giá ⭐</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon comments">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number">{{ stats.total_comments }}</span>
          <span class="stat-label">Bình luận 💬</span>
        </div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo nội dung, tên ngườii dùng..." @input="debouncedFetch" />
        </div>
        <!-- <select class="toolbar-filter" v-model="filterType" @change="onFilterChange">
          <option value="">Tất cả loại</option>
          <option value="comment">💬 Chỉ bình luận</option>
          <option value="rating">⭐ Chỉ đánh giá</option>
        </select> -->
        <select class="toolbar-filter" v-model="filterRating" @change="onFilterChange">
          <option value="">Tất cả số sao</option>
          <option value="5">★★★★★ 5 sao</option>
          <option value="4">★★★★☆ 4 sao</option>
          <option value="3">★★★☆☆ 3 sao</option>
          <option value="2">★★☆☆☆ 2 sao</option>
          <option value="1">★☆☆☆☆ 1 sao</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách đánh giá...</p>
    </div>

    <!-- Empty -->
    <div v-else-if="reviews.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--admin-border); margin-bottom: 12px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      <p>Chưa có đánh giá nào.</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Ngườii đánh giá</th>
              <th>Nội dung</th>
              <th>Sản phẩm</th>
              <th>Số sao</th>
              <th>Ngày</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="review in reviews" :key="review.id">
              <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                  <div :style="{
                    width: '34px', height: '34px', borderRadius: '8px', flexShrink: 0,
                    background: review.avatar_color || '#6366f1', display: 'flex', alignItems: 'center',
                    justifyContent: 'center', fontWeight: '700', fontSize: '13px', color: '#fff'
                  }">{{ (review.author || 'K').charAt(0).toUpperCase() }}</div>
                  <div>
                    <span style="font-weight: 500; color: var(--admin-text); white-space: nowrap; display: block;">{{ review.author }}</span>
                    <span v-if="review.author_email" style="font-size: 12px; color: var(--admin-text-soft); white-space: nowrap;">{{ review.author_email }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="content-cell" @click="openDetail(review)" title="Xem đầy đủ">
                  {{ review.content }}
                </span>
              </td>
              <td>
                <span class="product-name">{{ review.product_name }}</span>
              </td>
              <td>
                <div v-if="review.rating > 0" class="star-display">
                  <span v-for="s in 5" :key="s" :class="s <= review.rating ? 'star-filled' : 'star-empty'">★</span>
                  <span class="rating-num">{{ review.rating }}/5</span>
                </div>
                <span v-else class="type-badge comment-type">💬 Bình luận</span>
              </td>
              <td style="white-space: nowrap; color: var(--admin-text-soft);">{{ formatDate(review.created_at) }}</td>
              <td>
                <div class="action-btns">
                  <button class="action-btn reply" @click="openReply(review)" title="Trả lờii">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  </button>
                  <button class="action-btn view" @click="openDetail(review)" title="Xem chi tiết">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination">
        <span class="pagination-info">Hiển thị {{ startItem }}–{{ endItem }} / {{ totalItems }} đánh giá</span>
        <div class="pagination-btns">
          <button class="page-btn" :disabled="currentPage === 1" @click="goPage(1)">Đầu</button>
          <button class="page-btn" :disabled="currentPage === 1" @click="goPage(currentPage - 1)">Trước</button>
          <button
            v-for="page in visiblePages" :key="page"
            class="page-btn" :class="{ active: page === currentPage }"
            @click="goPage(page)"
          >{{ page }}</button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="goPage(currentPage + 1)">Sau</button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="goPage(totalPages)">Cuối</button>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="admin-modal-overlay" v-if="showDetail" @click.self="showDetail = false">
      <div class="admin-modal slide-up" style="max-width: 540px;">
        <div class="admin-modal-header">
          <h3>Chi tiết đánh giá</h3>
          <button class="modal-close" @click="showDetail = false">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body" v-if="detailReview">
          <div class="detail-header">
            <div :style="{
              width: '44px', height: '44px', borderRadius: '10px', flexShrink: 0,
              background: detailReview.avatar_color || '#6366f1', display: 'flex', alignItems: 'center',
              justifyContent: 'center', fontWeight: '700', fontSize: '16px', color: '#fff'
            }">{{ (detailReview.author || 'K').charAt(0).toUpperCase() }}</div>
            <div class="detail-meta">
              <span class="detail-author">{{ detailReview.author }}</span>
              <span v-if="detailReview.author_email" style="font-size: 12px; color: var(--admin-text-soft);">{{ detailReview.author_email }}</span>
              <span class="detail-date">{{ formatDate(detailReview.created_at) }}</span>
            </div>
          </div>

          <div class="detail-product">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
            Sản phẩm: <strong>{{ detailReview.product_name }}</strong>
          </div>

          <div class="detail-stars">
            <span v-for="s in 5" :key="s" :class="s <= detailReview.rating ? 'star-filled-lg' : 'star-empty-lg'">★</span>
            <span class="rating-label">{{ detailReview.rating }}/5 sao</span>
          </div>

          <div class="detail-content">{{ detailReview.content }}</div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="showDetail = false">Đóng</button>
          <button class="admin-btn admin-btn-primary" @click="openReply(detailReview); showDetail = false">Trả lờii</button>
        </div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div class="admin-modal-overlay" v-if="showReplyModal" @click.self="showReplyModal = false">
      <div class="admin-modal slide-up" style="max-width: 520px;">
        <div class="admin-modal-header">
          <h3>Trả lờii đánh giá</h3>
          <button class="modal-close" @click="showReplyModal = false">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body" v-if="replyTarget">
          <div class="detail-header" style="margin-bottom: 14px;">
            <div :style="{
              width: '40px', height: '40px', borderRadius: '10px', flexShrink: 0,
              background: replyTarget.avatar_color || '#6366f1', display: 'flex', alignItems: 'center',
              justifyContent: 'center', fontWeight: '700', fontSize: '15px', color: '#fff'
            }">{{ (replyTarget.author || 'K').charAt(0).toUpperCase() }}</div>
            <div class="detail-meta">
              <span class="detail-author">{{ replyTarget.author }}</span>
              <span class="detail-date">{{ formatDate(replyTarget.created_at) }}</span>
            </div>
          </div>
          <div class="detail-content" style="margin-bottom: 16px; font-size: 13px;">{{ replyTarget.content }}</div>
          <textarea
            v-model="replyContent"
            placeholder="Nhập nội dung trả lờii..."
            class="comment-textarea"
            rows="4"
            style="width: 100%; padding: 12px; border: 1px solid var(--admin-border); border-radius: 8px; resize: vertical;"
          ></textarea>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="showReplyModal = false">Huỷ</button>
          <button class="admin-btn admin-btn-primary" :disabled="!replyContent.trim() || submittingReply" @click="submitReply">{{ submittingReply ? 'Đang gửi...' : 'Gửi trả lờii' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const filterType = ref('')
const filterRating = ref('')
const currentPage = ref(1)
const itemsPerPage = 10
const loading = ref(false)

const reviews = ref([])
const totalItems = ref(0)
const totalPages = ref(1)
const stats = ref({
  total: 0,
  total_comments: 0,
  total_ratings: 0,
})

const showDetail = ref(false)
const detailReview = ref(null)

const showReplyModal = ref(false)
const replyTarget = ref(null)
const replyContent = ref('')
const submittingReply = ref(false)

// Debounce
let debounceTimer
const debouncedFetch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => { currentPage.value = 1; fetchReviews() }, 500)
}

const onFilterChange = () => {
  currentPage.value = 1
  fetchReviews()
}

const fetchReviews = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim() || undefined,
      type: filterType.value || undefined,
      rating: filterRating.value || undefined,
      per_page: itemsPerPage,
      page: currentPage.value,
    }
    const res = await api.get('/admin/comments', { params })
    reviews.value = res.data.data || []
    totalItems.value = res.data.total || reviews.value.length
    totalPages.value = res.data.last_page || Math.ceil(totalItems.value / itemsPerPage) || 1
    if (res.data.stats) stats.value = res.data.stats
  } catch (err) {
    console.error('Lỗi tải đánh giá:', err)
  } finally {
    loading.value = false
  }
}

const goPage = (page) => {
  currentPage.value = page
  fetchReviews()
}

// Pagination display
const startItem = computed(() => totalItems.value === 0 ? 0 : (currentPage.value - 1) * itemsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value))

const visiblePages = computed(() => {
  const pages = []
  const maxShow = 5
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(totalPages.value, start + maxShow - 1)
  if (end - start + 1 < maxShow) start = Math.max(1, end - maxShow + 1)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

onMounted(fetchReviews)

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

// Detail modal
const openDetail = (review) => {
  detailReview.value = review
  showDetail.value = true
}

// Reply modal
const openReply = (review) => {
  replyTarget.value = review
  replyContent.value = ''
  showReplyModal.value = true
}

const submitReply = async () => {
  if (!replyContent.value.trim() || !replyTarget.value) return
  submittingReply.value = true
  try {
    await api.post(`/admin/comments/${replyTarget.value.id}/reply`, {
      content: replyContent.value,
    })
    alert('Trả lờii thành công')
    replyContent.value = ''
    showReplyModal.value = false
  } catch (err) {
    console.error('Lỗi trả lờii:', err)
    alert(err.response?.data?.message || 'Có lỗi khi trả lờii')
  } finally {
    submittingReply.value = false
  }
}
</script>

<style scoped>
/* Stats Row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 20px;
}

.stat-card {
  background: var(--admin-card);
  border: 1px solid var(--admin-border);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.stat-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon.total { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
.stat-icon.ratings { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.stat-icon.comments { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }

.type-badge.comment-type {
  background: rgba(59, 130, 246, 0.12);
  color: #3b82f6;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 22px;
  font-weight: 700;
  color: var(--admin-text);
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: var(--admin-text-soft);
  margin-top: 4px;
}

/* Content cell */
.content-cell {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  max-width: 260px;
  font-size: 13.5px;
  color: var(--admin-text);
  cursor: pointer;
  line-height: 1.5;
}

.content-cell:hover {
  color: var(--admin-primary);
}

.product-name {
  font-size: 13px;
  font-weight: 500;
  color: var(--admin-text);
  max-width: 160px;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Stars */
.star-display {
  display: flex;
  align-items: center;
  gap: 2px;
}

.star-filled { color: #f59e0b; font-size: 15px; }
.star-empty  { color: var(--admin-border); font-size: 15px; }
.rating-num  { font-size: 12px; color: var(--admin-text-soft); margin-left: 4px; }

/* Action buttons */
.action-btn.view {
  background: rgba(99, 102, 241, 0.1);
  color: #6366f1;
  border: none;
  border-radius: 7px;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.action-btn.view:hover { background: rgba(99, 102, 241, 0.2); }

.action-btn.reply {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  border: none;
  border-radius: 7px;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
}

.action-btn.reply:hover { background: rgba(59, 130, 246, 0.2); }

/* Detail modal */
.detail-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.detail-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-author {
  font-weight: 600;
  color: var(--admin-text);
  font-size: 15px;
}

.detail-date {
  font-size: 12px;
  color: var(--admin-text-soft);
}

.detail-product {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--admin-text-soft);
  background: var(--admin-bg);
  border-radius: 8px;
  padding: 8px 12px;
  margin-bottom: 14px;
}

.detail-product strong {
  color: var(--admin-text);
}

.detail-stars {
  display: flex;
  align-items: center;
  gap: 3px;
  margin-bottom: 14px;
}

.star-filled-lg { color: #f59e0b; font-size: 22px; }
.star-empty-lg  { color: var(--admin-border); font-size: 22px; }
.rating-label   { font-size: 13px; color: var(--admin-text-soft); margin-left: 6px; }

.detail-content {
  background: var(--admin-bg);
  border-radius: 10px;
  padding: 14px 16px;
  font-size: 14px;
  line-height: 1.7;
  color: var(--admin-text);
  white-space: pre-wrap;
  word-break: break-word;
}
</style>

