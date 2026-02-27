<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý tin tức</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm kiếm bài viết..." @input="handleSearch" />
        </div>
        <select class="toolbar-filter" v-model="filterCategory" @change="handleFilter">
          <option value="">Tất cả danh mục</option>
          <option value="Đánh giá">Đánh giá</option>
          <option value="Hướng dẫn">Hướng dẫn</option>
          <option value="Tin tức">Tin tức</option>
          <option value="So sánh">So sánh</option>
          <option value="Mẹo hay">Mẹo hay</option>
        </select>
        <select class="toolbar-filter" v-model="filterStatus" @change="handleFilter">
          <option value="">Tất cả trạng thái</option>
          <option value="published">Đã đăng</option>
          <option value="draft">Nháp</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm bài viết
        </button>
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
              <th>Hình ảnh</th>
              <th>Tiêu đề</th>
              <th>Danh mục</th>
              <th>Tác giả</th>
              <th>Ngày đăng</th>
              <th>Lượt xem</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in posts" :key="post.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ post.id }}</td>
              <td>
                <img :src="post.image || '/images/default-product.jpg'" class="table-product-img" alt="Post" style="border-radius: 6px; width: 60px; height: 60px; object-fit: cover;" />
              </td>
              <td>
                <div style="font-weight:600; color: var(--admin-text); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ post.title }}</div>
              </td>
              <td>
                <span class="role-badge" style="background: rgba(99, 102, 241, 0.1); color: #818cf8;">{{ post.category }}</span>
              </td>
              <td style="color: var(--admin-text-soft);">{{ post.author }}</td>
              <td style="color: var(--admin-text-soft);">{{ formatDate(post.created_at) }}</td>
              <td style="color: var(--admin-text-soft);">{{ post.views || 0 }}</td>
              <td>
                <span class="status-badge" :class="post.status === 'published' ? 'active' : 'inactive'">
                  {{ post.status === 'published' ? 'Đã đăng' : 'Nháp' }}
                </span>
              </td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="editPost(post)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                  </button>
                  <button class="action-btn delete" @click="deletePost(post.id)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Empty State -->
      <div v-if="!loading && !posts.length" class="empty-state">
        <p>Chưa có bài viết nào</p>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="admin-pagination">
        <div class="pagination-info">Hiển thị {{ posts.length }} trên tổng số {{ pagination.total }} bài viết</div>
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

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới' }}</h3>
          <button class="close-modal" @click="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="admin-form-group">
            <label>Tiêu đề bài viết <span class="required">*</span></label>
            <input type="text" class="admin-input" v-model="currentPost.title" />
          </div>
          <div class="admin-form-group">
            <label>Danh mục</label>
            <select class="admin-select" v-model="currentPost.category">
              <option value="Đánh giá">Đánh giá</option>
              <option value="Hướng dẫn">Hướng dẫn</option>
              <option value="Tin tức">Tin tức</option>
              <option value="So sánh">So sánh</option>
              <option value="Mẹo hay">Mẹo hay</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label>Hình ảnh (URL)</label>
            <input type="text" class="admin-input" v-model="currentPost.image" placeholder="https://..." />
          </div>
          <div class="admin-form-group">
            <label>Tóm tắt</label>
            <textarea class="admin-textarea" rows="3" v-model="currentPost.excerpt"></textarea>
          </div>
          <div class="admin-form-group">
            <label>Nội dung</label>
            <textarea class="admin-textarea" rows="8" v-model="currentPost.content" placeholder="Nội dung bài viết..."></textarea>
          </div>
          <div class="admin-form-group">
            <label>Tác giả</label>
            <input type="text" class="admin-input" v-model="currentPost.author" />
          </div>
          <div class="admin-form-group">
            <label class="admin-toggle">
              <input type="checkbox" v-model="currentPost.isPublished" hidden>
              <span class="toggle-switch" :class="{ active: currentPost.isPublished }"></span>
              <span>Xuất bản ngay</span>
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeModal">Hủy</button>
          <button class="admin-btn admin-btn-primary" @click="savePost" :disabled="saving">
            {{ saving ? 'Đang lưu...' : 'Lưu bài viết' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getAdminNews, createNews, updateNews, deleteNews } from '@/api'

// State
const posts = ref([])
const loading = ref(true)
const saving = ref(false)
const searchQuery = ref('')
const filterCategory = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const isEditing = ref(false)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const currentPost = ref({
  id: null,
  title: '',
  category: 'Tin tức',
  image: '',
  excerpt: '',
  content: '',
  author: 'Admin',
  isPublished: true
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

const fetchPosts = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: 15,
      search: searchQuery.value || undefined,
      category: filterCategory.value || undefined,
      status: filterStatus.value || undefined
    }
    
    const response = await getAdminNews(params)
    const data = response.data
    
    posts.value = data.data || data
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || data.length
    }
  } catch (error) {
    console.error('Error fetching news:', error)
    posts.value = []
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  pagination.value.current_page = 1
  fetchPosts(1)
}

const handleFilter = () => {
  pagination.value.current_page = 1
  fetchPosts(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchPosts(page)
  }
}

const openAddModal = () => {
  isEditing.value = false
  currentPost.value = {
    id: null,
    title: '',
    category: 'Tin tức',
    image: '',
    excerpt: '',
    content: '',
    author: 'Admin',
    isPublished: true
  }
  showModal.value = true
}

const editPost = (post) => {
  isEditing.value = true
  currentPost.value = {
    id: post.id,
    title: post.title,
    category: post.category,
    image: post.image,
    excerpt: post.excerpt,
    content: post.content,
    author: post.author,
    isPublished: post.status === 'published'
  }
  showModal.value = true
}

const deletePost = async (id) => {
  if(confirm('Bạn có chắc muốn xóa bài viết này?')) {
    try {
      await deleteNews(id)
      fetchPosts(pagination.value.current_page)
    } catch (error) {
      console.error('Error deleting post:', error)
      alert('Có lỗi xảy ra khi xóa bài viết')
    }
  }
}

const closeModal = () => {
  showModal.value = false
}

const savePost = async () => {
  if (!currentPost.value.title.trim()) {
    alert('Vui lòng nhập tiêu đề bài viết')
    return
  }
  
  saving.value = true
  
  try {
    const data = {
      title: currentPost.value.title,
      category: currentPost.value.category,
      image: currentPost.value.image || null,
      excerpt: currentPost.value.excerpt || null,
      content: currentPost.value.content || null,
      author: currentPost.value.author || 'Admin',
      status: currentPost.value.isPublished ? 'published' : 'draft'
    }
    
    if (isEditing.value) {
      await updateNews(currentPost.value.id, data)
    } else {
      await createNews(data)
    }
    
    closeModal()
    fetchPosts(pagination.value.current_page)
  } catch (error) {
    console.error('Error saving post:', error)
    alert('Có lỗi xảy ra khi lưu bài viết')
  } finally {
    saving.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchPosts()
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
  max-width: 700px;
  border-radius: var(--admin-radius-lg);
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
  max-height: 90vh;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--admin-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  color: var(--admin-text);
}

.close-modal {
  background: none;
  border: none;
  font-size: 24px;
  color: var(--admin-text-muted);
  cursor: pointer;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
}

.modal-footer {
  padding: 20px 24px;
  border-top: 1px solid var(--admin-border);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.admin-form-group {
  margin-bottom: 20px;
}

.admin-form-group label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: var(--admin-text);
  margin-bottom: 8px;
}

.admin-form-group .required {
  color: #ef4444;
}

.admin-input, .admin-select, .admin-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid var(--admin-border);
  border-radius: 8px;
  font-size: 14px;
  background: var(--admin-bg);
  color: var(--admin-text);
}

.admin-input:focus, .admin-select:focus, .admin-textarea:focus {
  outline: none;
  border-color: #ff6b35;
}

.admin-textarea {
  resize: vertical;
  min-height: 100px;
}

.admin-toggle {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
}

.toggle-switch {
  width: 48px;
  height: 26px;
  background: var(--admin-border);
  border-radius: 13px;
  position: relative;
  transition: all 0.3s;
}

.toggle-switch::after {
  content: '';
  position: absolute;
  top: 3px;
  left: 3px;
  width: 20px;
  height: 20px;
  background: #ffffff;
  border-radius: 50%;
  transition: all 0.3s;
}

.toggle-switch.active {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
}

.toggle-switch.active::after {
  transform: translateX(22px);
}
</style>
