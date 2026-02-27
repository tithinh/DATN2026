<template>
  <div class="news-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <span class="current">Tin tức</span>
        </nav>
        <h1 class="page-title">Tin tức & Bài viết</h1>
        <p class="page-subtitle">Cập nhật xu hướng công nghệ và mẹo hay sử dụng phụ kiện</p>
      </div>
    </div>

    <!-- News Content -->
    <div class="news-content">
      <div class="container">
        <div class="news-layout">
          <!-- Main Content -->
          <div class="news-main">
            <!-- Featured Post -->
            <article v-if="featuredPost" class="featured-post" @click="goToNewsDetail(featuredPost.slug)">
              <div class="featured-image">
                <img :src="featuredPost.image || 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=800&h=450&fit=crop'" :alt="featuredPost.title" />
                <span class="category-badge">{{ featuredPost.category }}</span>
              </div>
              <div class="featured-content">
                <h2 class="featured-title">{{ featuredPost.title }}</h2>
                <p class="featured-excerpt">{{ featuredPost.excerpt }}</p>
                <div class="post-meta">
                  <img :src="`https://ui-avatars.com/api/?name=${featuredPost.author}&background=ff6b35&color=fff`" alt="Author" class="author-avatar" />
                  <span class="author-name">{{ featuredPost.author }}</span>
                  <span class="divider">•</span>
                  <span class="post-date">{{ formatDate(featuredPost.created_at) }}</span>
                  <span class="divider">•</span>
                  <span class="read-time">{{ featuredPost.views || 0 }} lượt xem</span>
                </div>
              </div>
            </article>

            <!-- Loading State -->
            <div v-if="loading && !posts.length" class="loading-state">
              <div class="spinner"></div>
              <p>Đang tải tin tức...</p>
            </div>

            <!-- Post Grid -->
            <div v-else class="posts-grid">
              <article v-for="post in posts" :key="post.id" class="post-card" @click="goToNewsDetail(post.slug)">
                <div class="post-image">
                  <img :src="post.image || 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=250&fit=crop'" :alt="post.title" />
                  <span class="category-badge">{{ post.category }}</span>
                </div>
                <div class="post-content">
                  <h3 class="post-title">{{ post.title }}</h3>
                  <p class="post-excerpt">{{ post.excerpt }}</p>
                  <div class="post-meta">
                    <span class="post-date">{{ formatDate(post.created_at) }}</span>
                    <span class="read-time">{{ post.views || 0 }} lượt xem</span>
                  </div>
                </div>
              </article>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && !posts.length" class="empty-state">
              <p>Chưa có bài viết nào</p>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="pagination">
              <button class="page-btn prev" :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)">
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
              <button class="page-btn next" :disabled="pagination.current_page === pagination.last_page" @click="changePage(pagination.current_page + 1)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              </button>
            </div>
          </div>

          <!-- Sidebar -->
          <aside class="news-sidebar">
            <!-- Search -->
            <div class="sidebar-widget">
              <h3 class="widget-title">Tìm kiếm</h3>
              <div class="search-box">
                <input 
                  type="text" 
                  v-model="searchQuery" 
                  placeholder="Tìm bài viết..." 
                  class="search-input"
                  @keyup.enter="handleSearch"
                />
                <button class="search-btn" @click="handleSearch">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>
              </div>
            </div>

            <!-- Categories -->
            <div class="sidebar-widget">
              <h3 class="widget-title">Danh mục</h3>
              <ul class="category-list">
                <li>
                  <a href="#" :class="{ active: !selectedCategory }" @click.prevent="filterCategory(null)">
                    Tất cả <span>({{ totalCount }})</span>
                  </a>
                </li>
                <li v-for="cat in categories" :key="cat">
                  <a href="#" :class="{ active: selectedCategory === cat }" @click.prevent="filterCategory(cat)">
                    {{ cat }}
                  </a>
                </li>
              </ul>
            </div>

            <!-- Popular Posts -->
            <div class="sidebar-widget">
              <h3 class="widget-title">Bài viết phổ biến</h3>
              <div class="popular-posts">
                <article v-for="post in popularPosts" :key="post.id" class="popular-post" @click="goToNewsDetail(post.slug)">
                  <img :src="post.image || 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=80&h=80&fit=crop'" alt="Popular" />
                  <div>
                    <h4>{{ post.title }}</h4>
                    <span>{{ post.views || 0 }} lượt xem</span>
                  </div>
                </article>
              </div>
            </div>

            <!-- Tags -->
            <div class="sidebar-widget">
              <h3 class="widget-title">Tags phổ biến</h3>
              <div class="tags-cloud">
                <a href="#" class="tag" @click.prevent="filterCategory('iPhone')">iPhone</a>
                <a href="#" class="tag" @click.prevent="filterCategory('Samsung')">Samsung</a>
                <a href="#" class="tag" @click.prevent="filterCategory('Ốp lưng')">Ốp lưng</a>
                <a href="#" class="tag" @click.prevent="filterCategory('Tai nghe')">Tai nghe</a>
                <a href="#" class="tag" @click.prevent="filterCategory('Sạc nhanh')">Sạc nhanh</a>
                <a href="#" class="tag" @click.prevent="filterCategory('MagSafe')">MagSafe</a>
              </div>
            </div>

            <!-- Newsletter -->
            <div class="sidebar-widget newsletter-widget">
              <h3 class="widget-title">Đăng ký nhận tin</h3>
              <p>Nhận thông báo bài viết mới và ưu đãi độc quyền</p>
              <input v-model="newsletterEmail" type="email" placeholder="Email của bạn" class="newsletter-input" />
              <button class="newsletter-btn" @click="subscribeNewsletter">Đăng ký</button>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getNews, getNewsCategories, getPopularNews } from '@/api'

const router = useRouter()

// State
const posts = ref([])
const featuredPost = ref(null)
const popularPosts = ref([])
const categories = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref(null)
const newsletterEmail = ref('')

// Pagination
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
})

const totalCount = computed(() => pagination.value.total)

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
      per_page: 9,
      search: searchQuery.value || undefined,
      category: selectedCategory.value || undefined
    }
    
    const response = await getNews(params)
    const data = response.data
    
    posts.value = data.data || data
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      per_page: data.per_page || 9,
      total: data.total || data.length
    }
    
    // First post is featured
    if (page === 1 && posts.value.length > 0 && !searchQuery.value && !selectedCategory.value) {
      featuredPost.value = posts.value[0]
      posts.value = posts.value.slice(1)
    } else {
      featuredPost.value = null
    }
  } catch (error) {
    console.error('Error fetching news:', error)
    posts.value = []
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await getNewsCategories()
    categories.value = response.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchPopularPosts = async () => {
  try {
    const response = await getPopularNews(5)
    popularPosts.value = response.data || []
  } catch (error) {
    console.error('Error fetching popular posts:', error)
  }
}

const handleSearch = () => {
  pagination.value.current_page = 1
  fetchPosts(1)
}

const filterCategory = (category) => {
  selectedCategory.value = category
  pagination.value.current_page = 1
  fetchPosts(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchPosts(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const goToNewsDetail = (slug) => {
  router.push(`/news/${slug}`)
}

const subscribeNewsletter = () => {
  if (!newsletterEmail.value) {
    alert('Vui lòng nhập email!')
    return
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newsletterEmail.value)) {
    alert('Email không hợp lệ!')
    return
  }
  alert('Đăng ký nhận tin thành công!')
  newsletterEmail.value = ''
}

// Lifecycle
onMounted(() => {
  fetchPosts()
  fetchCategories()
  fetchPopularPosts()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.news-page {
  font-family: 'Inter', sans-serif;
  color: #0f172a;
  background: #f8fafc;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  padding: 60px 0;
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.breadcrumb a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.3s;
}

.breadcrumb a:hover {
  color: #ff6b35;
}

.breadcrumb .separator {
  color: #475569;
}

.breadcrumb .current {
  color: #ffffff;
}

.page-title {
  font-size: 42px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 12px 0;
}

.page-subtitle {
  font-size: 16px;
  color: #94a3b8;
  margin: 0;
}

/* Content */
.news-content {
  padding: 60px 0;
}

.news-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 40px;
}

/* Featured Post */
.featured-post {
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  margin-bottom: 40px;
  cursor: pointer;
  transition: transform 0.3s;
}

.featured-post:hover {
  transform: translateY(-4px);
}

.featured-image {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.featured-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}

.featured-post:hover .featured-image img {
  transform: scale(1.05);
}

.category-badge {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  border-radius: 50px;
}

.featured-content {
  padding: 32px;
}

.featured-title {
  font-size: 28px;
  font-weight: 800;
  margin: 0 0 16px 0;
  line-height: 1.3;
  color: #0f172a;
}

.featured-excerpt {
  font-size: 16px;
  color: #64748b;
  line-height: 1.7;
  margin: 0 0 20px 0;
}

.post-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  color: #64748b;
}

.author-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.author-name { font-weight: 600; color: #0f172a; }
.divider { color: #cbd5e1; }

/* Posts Grid */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
  margin-bottom: 40px;
}

.post-card {
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  transition: all 0.3s;
  cursor: pointer;
}

.post-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.post-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.post-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s;
}

.post-card:hover .post-image img {
  transform: scale(1.1);
}

.post-content {
  padding: 24px;
}

.post-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 12px 0;
  line-height: 1.4;
  color: #0f172a;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.post-excerpt {
  font-size: 14px;
  color: #64748b;
  line-height: 1.6;
  margin: 0 0 16px 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.post-card .post-meta {
  font-size: 13px;
  gap: 16px;
}

/* Loading & Empty States */
.loading-state, .empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #64748b;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #ff6b35;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
}

.page-btn {
  min-width: 44px;
  height: 44px;
  padding: 0 16px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  color: #475569;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.page-btn:hover:not(:disabled) { border-color: #ff6b35; color: #ff6b35; }

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-btn.active {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border-color: transparent;
  color: #ffffff;
}

/* Sidebar */
.news-sidebar {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.sidebar-widget {
  background: #ffffff;
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

.widget-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 20px 0;
  color: #0f172a;
  padding-bottom: 12px;
  border-bottom: 2px solid #f1f5f9;
}

/* Search */
.search-box {
  display: flex;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  overflow: hidden;
}

.search-input {
  flex: 1;
  padding: 12px 16px;
  border: none;
  font-size: 14px;
  outline: none;
}

.search-btn {
  padding: 12px 16px;
  border: none;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  cursor: pointer;
}

/* Categories */
.category-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.category-list li {
  margin-bottom: 12px;
}

.category-list a {
  display: flex;
  justify-content: space-between;
  color: #475569;
  text-decoration: none;
  font-size: 14px;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
  transition: color 0.3s;
}

.category-list a:hover, .category-list a.active {
  color: #ff6b35;
}

.category-list span { color: #94a3b8; }

/* Popular Posts */
.popular-posts {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.popular-post {
  display: flex;
  gap: 16px;
  cursor: pointer;
  transition: opacity 0.3s;
}

.popular-post:hover {
  opacity: 0.8;
}

.popular-post img {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  object-fit: cover;
}

.popular-post h4 {
  font-size: 14px;
  font-weight: 600;
  margin: 0 0 8px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.popular-post span {
  font-size: 12px;
  color: #94a3b8;
}

/* Tags */
.tags-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag {
  padding: 8px 16px;
  background: #f1f5f9;
  color: #475569;
  text-decoration: none;
  font-size: 13px;
  border-radius: 50px;
  transition: all 0.3s;
}

.tag:hover {
  background: #ff6b35;
  color: #ffffff;
}

/* Newsletter */
.newsletter-widget {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
}

.newsletter-widget .widget-title {
  color: #ffffff;
  border-bottom-color: rgba(255, 255, 255, 0.1);
}

.newsletter-widget p {
  color: #94a3b8;
  font-size: 14px;
  margin: 0 0 16px 0;
}

.newsletter-input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  color: #ffffff;
  font-size: 14px;
  margin-bottom: 12px;
  outline: none;
}

.newsletter-input::placeholder { color: #64748b; }

.newsletter-btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border: none;
  border-radius: 10px;
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.newsletter-btn:hover {
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
  .news-layout { grid-template-columns: 1fr; }
  .news-sidebar { order: -1; }
}

@media (max-width: 768px) {
  .posts-grid { grid-template-columns: 1fr; }
  .page-title { font-size: 32px; }
  .featured-title { font-size: 22px; }
  .featured-image { height: 250px; }
}
</style>
