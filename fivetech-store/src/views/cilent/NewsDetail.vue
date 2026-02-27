<template>
  <div class="news-detail-page">
    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Đang tải bài viết...</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="error-state">
      <h2>{{ error }}</h2>
      <router-link to="/news" class="back-link">Quay lại tin tức</router-link>
    </div>

    <!-- Article Content -->
    <article v-else class="article">
      <!-- Article Header -->
      <header class="article-header">
        <div class="container">
          <nav class="breadcrumb">
            <router-link to="/">Trang chủ</router-link>
            <span class="separator">/</span>
            <router-link to="/news">Tin tức</router-link>
            <span class="separator">/</span>
            <span class="current">{{ post.title }}</span>
          </nav>
          
          <span class="category-badge">{{ post.category }}</span>
          <h1 class="article-title">{{ post.title }}</h1>
          
          <div class="article-meta">
            <img :src="`https://ui-avatars.com/api/?name=${post.author}&background=ff6b35&color=fff`" alt="Author" class="author-avatar" />
            <div class="meta-info">
              <span class="author-name">{{ post.author }}</span>
              <div class="meta-details">
                <span>{{ formatDate(post.created_at) }}</span>
                <span class="divider">•</span>
                <span>{{ post.views || 0 }} lượt xem</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <div class="featured-image-container">
        <div class="container">
          <img :src="post.image || 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=1200&h=600&fit=crop'" :alt="post.title" class="featured-image" />
        </div>
      </div>

      <!-- Article Body -->
      <div class="article-body">
        <div class="container">
          <div class="content-wrapper">
            <!-- Share & Tags -->
            <div class="article-actions">
              <div class="share-buttons">
                <span>Chia sẻ:</span>
                <button class="share-btn facebook">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </button>
                <button class="share-btn twitter">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                </button>
                <button class="share-btn copy" @click="copyLink">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
              </div>
            </div>

            <!-- Excerpt -->
            <div v-if="post.excerpt" class="article-excerpt">
              <p>{{ post.excerpt }}</p>
            </div>

            <!-- Content -->
            <div class="article-content" v-html="post.content || '<p>Nội dung đang được cập nhật...</p>'"></div>

            <!-- Tags -->
            <div v-if="post.category" class="article-tags">
              <span class="tag-label">Tags:</span>
              <router-link :to="`/news?category=${post.category}`" class="tag">{{ post.category }}</router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Articles -->
      <div class="related-section">
        <div class="container">
          <h3 class="related-title">Bài viết liên quan</h3>
          <div class="related-grid">
            <article v-for="article in relatedPosts" :key="article.id" class="related-card" @click="goToArticle(article.slug)">
              <img :src="article.image || 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=250&fit=crop'" :alt="article.title" />
              <div class="related-content">
                <span class="category-badge">{{ article.category }}</span>
                <h4>{{ article.title }}</h4>
                <span class="related-date">{{ formatDate(article.created_at) }}</span>
              </div>
            </article>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div class="container">
        <router-link to="/news" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
          Quay lại tin tức
        </router-link>
      </div>
    </article>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getNewsBySlug, getNews } from '@/api'

const route = useRoute()
const router = useRouter()

// Props
const props = defineProps({
  slug: {
    type: String,
    required: true
  }
})

// State
const post = ref({})
const relatedPosts = ref([])
const loading = ref(true)
const error = ref(null)

// Methods
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const fetchPost = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await getNewsBySlug(props.slug)
    post.value = response.data
    
    // Fetch related posts (same category, excluding current)
    await fetchRelatedPosts()
  } catch (err) {
    console.error('Error fetching post:', err)
    error.value = 'Không tìm thấy bài viết'
  } finally {
    loading.value = false
  }
}

const fetchRelatedPosts = async () => {
  try {
    const response = await getNews({ category: post.value.category, per_page: 3 })
    const allPosts = response.data.data || response.data
    // Filter out current post
    relatedPosts.value = allPosts.filter(p => p.id !== post.value.id).slice(0, 3)
  } catch (err) {
    console.error('Error fetching related posts:', err)
  }
}

const goToArticle = (slug) => {
  router.push(`/news/${slug}`)
}

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href)
  alert('Đã sao chép liên kết!')
}

// Lifecycle
onMounted(() => {
  fetchPost()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.news-detail-page {
  font-family: 'Inter', sans-serif;
  color: #0f172a;
  background: #f8fafc;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Loading & Error States */
.loading-state, .error-state {
  text-align: center;
  padding: 100px 20px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e2e8f0;
  border-top-color: #ff6b35;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state h2 {
  color: #ef4444;
  margin-bottom: 20px;
}

.back-link {
  color: #ff6b35;
  text-decoration: none;
  font-weight: 600;
}

/* Article Header */
.article-header {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  padding: 40px 0 60px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 24px;
  font-size: 14px;
  flex-wrap: wrap;
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
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.category-badge {
  display: inline-block;
  padding: 8px 16px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
  font-size: 12px;
  font-weight: 600;
  border-radius: 50px;
  margin-bottom: 16px;
}

.article-title {
  font-size: 36px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 24px 0;
  line-height: 1.3;
}

.article-meta {
  display: flex;
  align-items: center;
  gap: 16px;
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.meta-info {
  display: flex;
  flex-direction: column;
}

.author-name {
  font-size: 16px;
  font-weight: 600;
  color: #ffffff;
}

.meta-details {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #94a3b8;
}

.divider {
  color: #475569;
}

/* Featured Image */
.featured-image-container {
  margin-top: -40px;
  margin-bottom: 40px;
}

.featured-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* Article Body */
.article-body {
  padding-bottom: 60px;
}

.content-wrapper {
  background: #ffffff;
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

/* Share Buttons */
.article-actions {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 24px;
  padding-bottom: 24px;
  border-bottom: 1px solid #e2e8f0;
}

.share-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
}

.share-buttons span {
  font-size: 14px;
  color: #64748b;
}

.share-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
}

.share-btn.facebook {
  background: #1877f2;
  color: #ffffff;
}

.share-btn.twitter {
  background: #1da1f2;
  color: #ffffff;
}

.share-btn.copy {
  background: #e2e8f0;
  color: #475569;
}

.share-btn:hover {
  transform: translateY(-2px);
}

/* Excerpt */
.article-excerpt {
  font-size: 18px;
  color: #475569;
  line-height: 1.7;
  padding: 24px;
  background: #f8fafc;
  border-radius: 12px;
  margin-bottom: 32px;
  border-left: 4px solid #ff6b35;
}

/* Content */
.article-content {
  font-size: 16px;
  line-height: 1.8;
  color: #334155;
}

.article-content :deep(p) {
  margin-bottom: 20px;
}

.article-content :deep(h2) {
  font-size: 24px;
  font-weight: 700;
  margin: 40px 0 20px;
  color: #0f172a;
}

.article-content :deep(h3) {
  font-size: 20px;
  font-weight: 700;
  margin: 30px 0 16px;
  color: #0f172a;
}

.article-content :deep(ul), .article-content :deep(ol) {
  margin: 20px 0;
  padding-left: 24px;
}

.article-content :deep(li) {
  margin-bottom: 12px;
}

.article-content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 12px;
  margin: 24px 0;
}

.article-content :deep(blockquote) {
  border-left: 4px solid #ff6b35;
  padding-left: 20px;
  margin: 24px 0;
  color: #64748b;
  font-style: italic;
}

/* Tags */
.article-tags {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
}

.tag-label {
  font-size: 14px;
  color: #64748b;
  font-weight: 600;
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

/* Related Posts */
.related-section {
  background: #ffffff;
  padding: 60px 0;
  margin-top: 40px;
}

.related-title {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 32px 0;
  color: #0f172a;
}

.related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

.related-card {
  background: #f8fafc;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.related-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.related-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
}

.related-content {
  padding: 20px;
}

.related-content .category-badge {
  font-size: 11px;
  padding: 6px 12px;
  margin-bottom: 12px;
}

.related-content h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 8px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.related-date {
  font-size: 12px;
  color: #94a3b8;
}

/* Back Button */
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 24px;
  background: #ffffff;
  color: #475569;
  text-decoration: none;
  font-weight: 600;
  border-radius: 10px;
  margin: 40px 0 60px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  transition: all 0.3s;
}

.back-btn:hover {
  color: #ff6b35;
  transform: translateX(-4px);
}

/* Responsive */
@media (max-width: 768px) {
  .article-title {
    font-size: 24px;
  }
  
  .featured-image {
    height: 250px;
    border-radius: 12px;
  }
  
  .content-wrapper {
    padding: 24px;
  }
  
  .related-grid {
    grid-template-columns: 1fr;
  }
  
  .article-excerpt {
    font-size: 16px;
  }
}
</style>
