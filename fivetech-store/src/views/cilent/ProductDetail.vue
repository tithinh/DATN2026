<template>
  <div class="product-detail-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/products">Sản phẩm</router-link>
          <span class="separator">/</span>
          <span class="current">{{ product?.category?.name || 'Danh mục' }}</span>
          <span class="separator">/</span>
          <span class="current">{{ product?.name || 'Đang tải...' }}</span>
        </nav>
      </div>
    </div>

    <!-- Loading / Error -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải thông tin sản phẩm...</p>
    </div>
    <div v-else-if="error" class="error-container">
      <p>{{ error }}</p>
      <router-link to="/products">Quay lại danh sách sản phẩm</router-link>
    </div>

    <!-- Nội dung chính -->
    <div v-else class="content">
      <!-- Product Section -->
      <section class="product-section">
        <div class="container">
          <div class="product-layout">
            <!-- Product Gallery -->
            <div class="product-gallery">
              <div class="main-image">
                <span v-if="product.is_featured" class="product-badge badge-hot">Hot</span>
                <img
                  :src="getMainImage"
                  :alt="product.name || 'Sản phẩm'"
                  class="gallery-image"
                  loading="lazy"
                  @error="handleImageError"
                />
                
              </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
              <h1 class="product-title">{{ product.name }}</h1>

              <div class="product-meta">
                <div class="rating">
                  <span class="stars">★★★★★</span>
                  <span class="rating-text">{{ averageRating.toFixed(1) }} ({{ product.comments?.length || 0 }} đánh giá)</span>
                </div>
                <span class="divider">|</span>
                <span class="sold">Đã bán {{ product.sales_count || '0' }}</span>
                <span class="divider">|</span>
                <span class="sku">SKU: {{ selectedVariant?.sku || product.slug?.toUpperCase() }}</span>
              </div>

              <div class="price-box">
                <span class="current-price">{{ formatPrice(selectedVariant?.discount_price || product.discount_price || product.base_price) }}đ</span>
                <span v-if="discountPercent > 0" class="old-price">{{ formatPrice(selectedVariant?.base_price || product.base_price) }}đ</span>
                <span v-if="discountPercent > 0" class="discount-badge">-{{ discountPercent }}%</span>
              </div>

              <!-- Variants -->
              <div class="variant-section" v-if="product.variants?.length">
                <h4 class="variant-title">Màu sắc / Biến thể</h4>
                <div class="variant-options">
                  <button
                    v-for="variant in product.variants"
                    :key="variant.variant_id"
                    class="variant-btn"
                    :class="{ active: selectedVariant?.variant_id === variant.variant_id }"
                    @click="selectVariant(variant)"
                  >
                    <span class="color-dot" :style="{ backgroundColor: variant.color || '#000' }"></span>
                    {{ variant.name || variant.color || variant.storage_size || 'Mặc định' }}
                  </button>
                </div>
              </div>

              <!-- Quantity -->
              <div class="quantity-section">
                <h4 class="variant-title">Số lượng</h4>
                <div class="quantity-box">
                  <button class="qty-btn" @click="quantity = Math.max(1, quantity - 1)">−</button>
                  <input type="number" v-model.number="quantity" min="1" :max="maxQuantity" class="qty-input" />
                  <button class="qty-btn" @click="quantity = Math.min(maxQuantity, quantity + 1)">+</button>
                </div>
                <span class="stock-info">Còn {{ selectedVariant?.stock || product.stock_total || 0 }} sản phẩm</span>
              </div>

              <!-- Actions -->
              <div class="action-buttons">
                <button class="btn-add-cart" @click="addToCart">
                  <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="8" cy="21" r="1"/>
                      <circle cx="19" cy="21" r="1"/>
                      <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                    </svg>
                  </span>
                  Thêm vào giỏ hàng
                </button>
                <button class="btn-buy-now" @click="buyNow">Mua ngay</button>
              </div>

            </div>
          </div>
        </div>
      </section>

      <!-- Tabs Section -->
      <section class="tabs-section">
        <div class="container">
          <div class="tabs-header">
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'description' }"
              @click="activeTab = 'description'"
            >
              Mô tả sản phẩm
            </button>
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'specs' }"
              @click="activeTab = 'specs'"
            >
              Thông số kỹ thuật
            </button>
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'reviews' }"
              @click="activeTab = 'reviews'"
            >
              Đánh giá ({{ product?.comments?.length || 0 }})
            </button>
          </div>

          <div class="tab-content">
            <!-- Mô tả sản phẩm -->
            <div v-if="activeTab === 'description'" class="tab-pane">
              <h3>{{ product?.name }}</h3>
              <p v-html="product?.description || product?.short_desc || 'Không có mô tả chi tiết.'"></p>

              <h4>Đặc điểm nổi bật:</h4>
              <ul>
                <li v-for="(feature, i) in productFeatures" :key="i">{{ feature }}</li>
              </ul>
            </div>

            <!-- Thông số kỹ thuật -->
            <div v-if="activeTab === 'specs'" class="tab-pane">
              <h3>Thông số kỹ thuật</h3>
              <ul>
                <li v-for="(spec, i) in specifications" :key="i">{{ spec }}</li>
              </ul>
              <p v-if="!specifications.length">Chưa có thông số kỹ thuật chi tiết.</p>
            </div>

            <!-- Đánh giá -->
            <div v-if="activeTab === 'reviews'" class="tab-pane">
              <h3>Đánh giá từ khách hàng</h3>

              <div class="reviews-summary">
                <div class="rating-overview">
                  <span class="big-rating">{{ averageRating.toFixed(1) }}</span>
                  <div class="rating-details">
                    <span class="stars-big">★★★★★</span>
                    <span class="total-reviews">{{ product.comments.length }} đánh giá</span>
                  </div>
                </div>
              </div>

              <!-- Form đánh giá mới -->
              <div v-if="auth.isAuthenticated" class="comment-form">
                <h4>Viết đánh giá của bạn</h4>
                <div class="rating-input">
                  <span>Đánh giá:</span>
                  <div class="stars-select">
                    <span 
                      v-for="star in 5" 
                      :key="star"
                      class="star" 
                      :class="{ active: newRating >= star }"
                      @click="newRating = star"
                    >★</span>
                  </div>
                </div>
                <textarea 
                  v-model="newComment" 
                  placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." 
                  class="comment-textarea"
                  rows="4"
                ></textarea>
                <button 
                  class="submit-comment-btn" 
                  :disabled="!newComment.trim() || newRating === 0 || submittingComment"
                  @click="submitComment"
                >
                  {{ submittingComment ? 'Đang gửi...' : 'Gửi đánh giá' }}
                </button>
              </div>

              <div v-else class="login-to-comment">
                <p>Vui lòng <router-link to="/login">đăng nhập</router-link> để viết đánh giá!</p>
              </div>

              <!-- Danh sách bình luận -->
              <div class="reviews-list" v-if="product.comments.length">
                <div v-for="comment in paginatedComments" :key="comment.comment_id" class="review-card">
                  <!-- Nội dung bình luận giữ nguyên, chỉ cần đảm bảo ảnh reviewer dùng đúng đường dẫn nếu có -->
                  <div class="review-header">
                    <img
                      :src="comment.user?.avatar ? 'http://localhost:8000/storage/' + comment.user.avatar : 'https://ui-avatars.com/api/?name=' + (comment.user?.full_name?.charAt(0) || 'K')"
                      alt="Avatar"
                      class="reviewer-avatar"
                    />
                    <!-- Phần còn lại giữ nguyên -->
                  </div>
                  <!-- ... nội dung bình luận, sửa/xóa, trả lời ... giữ nguyên -->
                </div>
              </div>
              <p v-else>Chưa có đánh giá nào.</p>

              <!-- Phân trang bình luận giữ nguyên -->
            </div>
          </div>
        </div>
      </section>

      <!-- Related Products -->
      <section class="related-section">
        <div class="container">
          <h2 class="section-title">Sản phẩm liên quan</h2>
          <div class="related-grid">
            <router-link
              v-for="related in relatedProducts"
              :key="related.product_id"
              :to="`/products/${related.slug}`"
              class="related-card-link"
            >
              <article class="product-card">
                <span v-if="related.is_featured" class="product-badge badge-new">New</span>
                <div class="product-image-wrapper">
                  <img 
                    :src="getRelatedImage(related)"
                    :alt="related.name"
                    class="product-image"
                    loading="lazy"
                    @error="handleImageError"
                  />
                </div>
                <div class="product-card-info">
                  <h3 class="product-name">{{ related.name }}</h3>
                  <div class="product-price">
                    <span class="current-price">{{ formatPrice(related.discount_price || related.base_price) }}đ</span>
                  </div>
                  <div class="product-rating"><span class="stars">★★★★★</span></div>
                </div>
              </article>
            </router-link>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const product = ref(null)
const relatedProducts = ref([])
const loading = ref(true)
const error = ref(null)
const selectedVariant = ref(null)
const selectedImage = ref(null)
const quantity = ref(1)

// Tab active
const activeTab = ref('description')

// Bình luận mới (giữ nguyên)
const newComment = ref('')
const newRating = ref(0)
const submittingComment = ref(false)

// Trả lời & sửa bình luận (giữ nguyên)
const replyForms = ref({})
const replyContent = ref({})
const replying = ref(false)

// Computed rating (giữ nguyên)
const averageRating = computed(() => {
  if (!product.value?.comments?.length) return 0
  const sum = product.value.comments.reduce((acc, c) => acc + (Number(c.rating) || 5), 0)
  return sum / product.value.comments.length
})

// Computed ảnh chính (ưu tiên ảnh được chọn → ảnh đầu của variant đang chọn → ảnh đầu của variant đầu tiên)
const getMainImage = computed(() => {
  // 1. Ảnh thumbnail được chọn
  if (selectedImage.value) {
    return selectedImage.value.startsWith('http') 
      ? selectedImage.value 
      : `http://localhost:8000/storage/${selectedImage.value.replace(/^\/+/, '')}`
  }

  // 2. Ảnh đầu tiên của variant đang chọn
  let urls = []
  if (selectedVariant.value?.image_urls) {
    try {
      urls = typeof selectedVariant.value.image_urls === 'string'
        ? JSON.parse(selectedVariant.value.image_urls)
        : selectedVariant.value.image_urls || []
    } catch (e) {}
  }

  // 3. Fallback về variant đầu tiên nếu chưa chọn variant
  if (urls.length === 0 && product.value?.variants?.length) {
    try {
      const firstVariant = product.value.variants[0]
      urls = typeof firstVariant.image_urls === 'string'
        ? JSON.parse(firstVariant.image_urls)
        : firstVariant.image_urls || []
    } catch (e) {}
  }

  if (urls.length > 0) {
    const firstImage = urls[0]
    return firstImage.startsWith('http') 
      ? firstImage 
      : `http://localhost:8000/storage/${firstImage.replace(/^\/+/, '')}`
  }

  return ''
})

// Danh sách thumbnail (chỉ ảnh của variant đang chọn)
const variantImages = computed(() => {
  let urls = []

  if (selectedVariant.value?.image_urls) {
    try {
      urls = typeof selectedVariant.value.image_urls === 'string'
        ? JSON.parse(selectedVariant.value.image_urls)
        : selectedVariant.value.image_urls || []
    } catch (e) {}
  }

  return urls.map(url => {
    return url.startsWith('http') 
      ? url 
      : `http://localhost:8000/storage/${url.replace(/^\/+/, '')}`
  })
})

// Ảnh cho Related Products
const getRelatedImage = (related) => {
  const firstVariant = related.variants?.[0]
  if (firstVariant?.image_urls?.length) {
    let path = firstVariant.image_urls[0]
    if (Array.isArray(path)) path = path[0]
    if (path && !path.startsWith('http')) {
      return `http://localhost:8000/storage/${path.replace(/^\/+/, '')}`
    }
    return path
  }
}

// Các computed khác giữ nguyên
const discountPercent = computed(() => {
  const base = selectedVariant.value?.base_price || product.value?.base_price
  const final = selectedVariant.value?.discount_price || product.value?.discount_price || product.value?.base_price
  return base && final && base > final ? Math.round((base - final) / base * 100) : 0
})

const maxQuantity = computed(() => selectedVariant.value?.stock || product.value?.stock_total || 999)

const productFeatures = computed(() => {
  return product.value?.features?.split('\n').filter(f => f.trim()) || [
    'Chất liệu TPU cao cấp, độ bền cao, chống trầy xước',
    'Thiết kế slim fit, không làm dày máy',
    'Gờ camera nổi bảo vệ cụm camera khỏi trầy xước',
    '4 góc chống sốc, bảo vệ máy khi rơi',
    'Tương thích hoàn toàn với MagSafe và các phụ kiện từ tính',
    'Dễ dàng lắp đặt và tháo gỡ'
  ]
})

// Methods
const selectVariant = (variant) => {
  selectedVariant.value = variant
  // Cập nhật ảnh chính và thumbnail tự động
  let urls = []
  try {
    urls = typeof variant.image_urls === 'string' 
      ? JSON.parse(variant.image_urls) 
      : variant.image_urls || []
  } catch (e) {}
  
  if (urls.length > 0) {
    const first = urls[0]
    selectedImage.value = first.startsWith('http') ? first : `http://localhost:8000/storage/${first.replace(/^\/+/, '')}`
  } else {
    selectedImage.value = null
  }
}

const formatPrice = (price) => {
  if (!price) return '0đ'
  return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + 'đ'
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const addToCart = async (showAlert = true) => {
  if (!selectedVariant.value?.variant_id) {
    return alert('Vui lòng chọn biến thể trước!')
  }

  if (quantity.value > (selectedVariant.value.stock || 999)) {
    return alert('Số lượng vượt quá tồn kho!')
  }

  try {
    const payload = {
      variant_id: selectedVariant.value.variant_id,
      quantity: quantity.value
    }

    await api.post('/cart/add', payload)

    if (showAlert) {
      alert('Đã thêm vào giỏ hàng thành công!')
    }

    return true
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi thêm vào giỏ hàng!')
    return false
  }
}

const buyNow = async () => {
  const success = await addToCart(false)
  if (success) {
    router.push('/checkout')
  }
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/600x600?text=Ảnh+không+tải+được'
  event.target.alt = 'Ảnh sản phẩm không khả dụng'
}

// Fetch data
onMounted(async () => {
  const slug = route.params.slug

  if (!slug || typeof slug !== 'string') {
    error.value = 'Slug sản phẩm không hợp lệ'
    return router.push('/products')
  }

  loading.value = true
  error.value = null

  try {
    const res = await api.get(`/products/${slug}`)
    console.log('API response:', res.data)

    product.value = res.data.product || res.data.data || res.data || null

    if (!product.value) {
      error.value = 'Không tìm thấy sản phẩm'
      return router.push('/products')
    }

    // Parse image_urls cho tất cả variants
    if (product.value.variants?.length) {
      product.value.variants.forEach(variant => {
        if (typeof variant.image_urls === 'string') {
          try {
            variant.image_urls = JSON.parse(variant.image_urls) || []
          } catch (e) {
            variant.image_urls = []
          }
        } else if (!Array.isArray(variant.image_urls)) {
          variant.image_urls = []
        }
      })

      // Chọn variant mặc định
      selectVariant(product.value.variants[0])
    }

    // Related products
    if (product.value.category_id) {
      const relatedRes = await api.get('/products', {
        params: { category_id: product.value.category_id, per_page: 4 }
      })
      relatedProducts.value = (relatedRes.data.data || relatedRes.data || []).filter(
        p => p.product_id !== product.value.product_id
      )
    }
  } catch (err) {
    console.error('Lỗi tải sản phẩm:', err)
    error.value = err.response?.data?.message || 'Không thể tải sản phẩm'
    if (err.response?.status === 404) router.push('/products')
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
  .star {
  font-size: 24px;
  cursor: pointer;
  color: #ccc; /* màu mặc định (xám) */
  transition: color 0.2s ease;
}

.star.active {
  color: #ffc107; /* vàng đẹp */
}
/* Giữ nguyên style cũ của bạn, chỉ thêm class hỗ trợ loading/error */
.loading-container, .error-container {
  text-align: center;
  padding: 100px 20px;
  font-size: 1.2rem;
  color: #64748b;
}
.error-container a {
  margin-top: 20px;
  display: inline-block;
  padding: 10px 20px;
  background: #ff6b35;
  color: white;
  text-decoration: none;
  border-radius: 8px;
}
.tab-btn {
  padding: 12px 24px;
  border: none;
  background: #f1f5f9;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.tab-btn.active {
  background: #0f172a;
  color: white;
  border-radius: 8px 8px 0 0;
}

.tab-content {
  padding: 32px 32px;
  background: white;
  border-radius: 0 0 12px 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.tab-pane {
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Pagination bình luận */
.comment-pagination {
  margin-top: 20px;
  text-align: center;
}

.comment-pagination button {
  padding: 8px 16px;
  margin: 0 8px;
  border: 1px solid #cbd5e1;
  background: white;
  cursor: pointer;
  border-radius: 6px;
}

.comment-pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-container, .error-container {
  text-align: center;
  padding: 100px 20px;
  font-size: 1.2rem;
  color: #64748b;
}
.error-container a {
  margin-top: 20px;
  display: inline-block;
  padding: 10px 20px;
  background: #ff6b35;
  color: white;
  text-decoration: none;
  border-radius: 8px;
}

/* Form bình luận mới */
.comment-form {
  margin: 32px 0 48px;
  padding: 24px;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.comment-form h4 {
  margin-bottom: 16px;
  font-size: 1.25rem;
}

.comment-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  resize: vertical;
  min-height: 100px;
  margin-bottom: 16px;
}

.submit-comment-btn {
  background: #22c55e;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.submit-comment-btn:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.login-to-comment {
  text-align: center;
  padding: 32px;
  background: #f8fafc;
  border-radius: 12px;
  margin: 32px 0;
}

.login-to-comment a {
  color: #3b82f6;
  font-weight: 600;
}
</style>

            