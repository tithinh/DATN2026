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
          <input type="text" v-model="searchQuery" placeholder="Tìm kiếm bài viết..." />
        </div>
        <select class="toolbar-filter" v-model="filterCategory">
          <option value="">Tất cả danh mục</option>
          <option value="Đánh giá">Đánh giá</option>
          <option value="Hướng dẫn">Hướng dẫn</option>
          <option value="Tin tức">Tin tức</option>
          <option value="So sánh">So sánh</option>
          <option value="Mẹo hay">Mẹo hay</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm bài viết
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="admin-card">
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
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in filteredPosts" :key="post.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ post.id }}</td>
              <td>
                <img :src="post.image" class="table-product-img" alt="Post" style="border-radius: 6px;" />
              </td>
              <td>
                <div style="font-weight:600; color: var(--admin-text); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ post.title }}</div>
                <div style="font-size: 12px; color: var(--admin-text-muted);">{{ post.views }} lượt xem</div>
              </td>
              <td>
                <span class="role-badge" style="background: rgba(99, 102, 241, 0.1); color: #818cf8;">{{ post.category }}</span>
              </td>
              <td style="color: var(--admin-text-soft);">{{ post.author }}</td>
              <td style="color: var(--admin-text-soft);">{{ post.date }}</td>
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
      
      <!-- Pagination -->
      <div class="admin-pagination">
        <div class="pagination-info">Hiển thị 1-{{ filteredPosts.length }} trên tổng số {{ posts.length }} bài viết</div>
        <div class="pagination-btns">
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>
        </div>
      </div>
    </div>

    <!-- Modal (Simplified Mock) -->
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
            <input type="text" class="admin-input" v-model="currentPost.image" />
          </div>
          <div class="admin-form-group">
            <label>Tóm tắt</label>
            <textarea class="admin-textarea" rows="3" v-model="currentPost.excerpt"></textarea>
          </div>
          <div class="admin-form-group">
            <label>Nội dung</label>
            <textarea class="admin-textarea" rows="8" placeholder="Nội dung bài viết..."></textarea>
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
          <button class="admin-btn admin-btn-primary" @click="savePost">Lưu bài viết</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const filterCategory = ref('')
const showModal = ref(false)
const isEditing = ref(false)

const posts = ref([
  {
    id: 101,
    title: 'Top 10 ốp lưng iPhone 15 Pro Max đáng mua nhất năm 2026',
    excerpt: 'Khám phá những mẫu ốp lưng đẹp, bền, bảo vệ tốt nhất cho iPhone 15 Pro Max...',
    image: 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=80&h=80&fit=crop',
    category: 'Đánh giá',
    author: 'Nguyễn Văn An',
    date: '20/01/2026',
    views: 2500,
    status: 'published'
  },
  {
    id: 102,
    title: 'Cách chọn tai nghe Bluetooth phù hợp với nhu cầu sử dụng',
    excerpt: 'Hướng dẫn chi tiết giúp bạn chọn tai nghe không dây phù hợp nhất...',
    image: 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=80&h=80&fit=crop',
    category: 'Hướng dẫn',
    author: 'Trần Thị B',
    date: '18/01/2026',
    views: 1800,
    status: 'published'
  },
  {
    id: 103,
    title: 'Apple ra mắt chuẩn sạc nhanh mới cho iPhone 16 Series',
    excerpt: 'Tìm hiểu về công nghệ sạc nhanh mới nhất từ Apple hỗ trợ lên đến 50W.',
    image: 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=80&h=80&fit=crop',
    category: 'Tin tức',
    author: 'Lê Văn C',
    date: '15/01/2026',
    views: 1500,
    status: 'published'
  }
])

const currentPost = ref({
  id: null,
  title: '',
  category: 'Tin tức',
  image: '',
  excerpt: '',
  isPublished: true
})

const filteredPosts = computed(() => {
  return posts.value.filter(post => {
    const matchSearch = post.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchCat = filterCategory.value ? post.category === filterCategory.value : true
    return matchSearch && matchCat
  })
})

const openAddModal = () => {
  isEditing.value = false
  currentPost.value = {
    id: null,
    title: '',
    category: 'Tin tức',
    image: '',
    excerpt: '',
    isPublished: true
  }
  showModal.value = true
}

const editPost = (post) => {
  isEditing.value = true
  currentPost.value = { ...post, isPublished: post.status === 'published' }
  showModal.value = true
}

const deletePost = (id) => {
  if(confirm('Bạn có chắc muốn xóa bài viết này?')) {
    posts.value = posts.value.filter(p => p.id !== id)
  }
}

const closeModal = () => {
  showModal.value = false
}

const savePost = () => {
  if (isEditing.value) {
    // Update logic mock
    const index = posts.value.findIndex(p => p.id === currentPost.value.id)
    if (index !== -1) {
      posts.value[index] = { 
        ...posts.value[index], 
        ...currentPost.value, 
        status: currentPost.value.isPublished ? 'published' : 'draft' 
      }
    }
  } else {
    // Add logic mock
    posts.value.unshift({
      id: posts.value.length + 101,
      ...currentPost.value,
      author: 'Admin',
      date: new Date().toLocaleDateString('vi-VN'),
      views: 0,
      status: currentPost.value.isPublished ? 'published' : 'draft'
    })
  }
  closeModal()
}
</script>

<style scoped>
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
</style>
