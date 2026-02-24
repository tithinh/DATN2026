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
                <button class="zoom-btn" title="Phóng to">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="zoom-icon">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                    <line x1="11" y1="8" x2="11" y2="14"></line>
                    <line x1="8" y1="11" x2="14" y2="11"></line>
                  </svg>
                </button>
              </div>
              <div class="thumbnail-list">
              <button
                v-for="(img, index) in variantImages"
                :key="index"
                class="thumbnail"
                :class="{ active: selectedImage === img }"
                @click="selectedImage = img"
              >
                <img :src="img" :alt="`Hình ${index + 1}`" loading="lazy" />
              </button>
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

              <!-- Features -->
              <div class="features-list">
                <div class="feature-item">
                  <span class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                  </span>
                  <span>Hỗ trợ MagSafe - Sạc không dây</span>
                </div>
                <!-- Giữ nguyên các feature khác -->
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

  <!-- Form đánh giá mới - chỉ hiện khi đã đăng nhập -->
  <div v-if="auth.isAuthenticated" class="comment-form">
    <h4>Viết đánh giá của bạn</h4>

    <!-- Chọn số sao -->
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

  <!-- Thông báo đăng nhập -->
  <div v-else class="login-to-comment">
    <p>Vui lòng <router-link to="/login">đăng nhập</router-link> để viết đánh giá!</p>
  </div>

  <!-- Danh sách bình luận -->
  <div class="reviews-list" v-if="product.comments.length">
    <div v-for="comment in paginatedComments" :key="comment.comment_id" class="review-card">
      <div class="review-header">
        <img
          :src="comment.user?.avatar || 'https://ui-avatars.com/api/?name=' + (comment.user?.full_name?.charAt(0) || 'K')"
          alt="Avatar"
          class="reviewer-avatar"
        />
        <div class="reviewer-info">
          <h4 class="reviewer-name">{{ comment.user?.full_name || 'Khách hàng' }}</h4>
          <div class="review-meta">
            <span class="stars">
              <span v-for="n in 5" :key="n" :class="{ filled: n <= comment.rating }">★</span>
            </span>
            <span class="review-date">{{ formatDate(comment.created_at) }}</span>
          </div>
        </div>

        <!-- Chỉ chủ bình luận thấy nút sửa/xóa -->
        <div v-if="auth.isAuthenticated && comment.user_id === auth.user?.id" class="review-actions">
          <button class="action-btn edit" @click="editComment(comment)">Sửa</button>
          <button class="action-btn delete" @click="deleteComment(comment.comment_id)">Xóa</button>
        </div>
      </div>

      <!-- Nội dung bình luận -->
      <p v-if="!comment.isEditing" class="review-content">{{ comment.content }}</p>

      <!-- Form sửa bình luận -->
      <div v-if="comment.isEditing" class="edit-form">
        <!-- Giữ nguyên rating cũ khi sửa (hoặc thêm chọn lại sao nếu muốn) -->
        <div class="rating-input-edit">
          <span>Đánh giá:</span>
          <div class="stars-select">
            <span 
              v-for="star in 5" 
              :key="star"
              class="star" 
              :class="{ active: comment.editRating >= star }"
              @click="comment.editRating = star"
            >★</span>
          </div>
        </div>

        <textarea v-model="comment.editContent" class="comment-textarea" rows="3"></textarea>
        <div class="edit-actions">
          <button class="save-btn" @click="saveEdit(comment)">Lưu</button>
          <button class="cancel-btn" @click="cancelEdit(comment)">Hủy</button>
        </div>
      </div>

      <!-- Hình ảnh (nếu có) -->
      <div class="review-images" v-if="comment.images?.length">
        <img v-for="(img, i) in comment.images" :key="i" :src="img" alt="Hình đánh giá" />
      </div>

      <!-- Nút trả lời -->
      <button 
        v-if="auth.isAuthenticated" 
        class="reply-btn" 
        @click="toggleReplyForm(comment.comment_id)"
      >
        Trả lời
      </button>

      <!-- Form trả lời -->
      <div v-if="replyForms[comment.comment_id]" class="reply-form">
        <textarea 
          v-model="replyContent[comment.comment_id]" 
          placeholder="Trả lời bình luận này..." 
          class="comment-textarea"
          rows="3"
        ></textarea>
        <button 
          class="submit-reply-btn" 
          :disabled="!replyContent[comment.comment_id]?.trim() || replying"
          @click="submitReply(comment.comment_id)"
        >
          {{ replying ? 'Đang gửi...' : 'Gửi trả lời' }}
        </button>
      </div>

      <!-- Danh sách trả lời -->
      <div v-if="comment.replies?.length" class="replies-list">
        <div v-for="reply in comment.replies" :key="reply.comment_id" class="reply-item">
          <div class="reply-header">
            <img
              :src="reply.user?.avatar || 'https://ui-avatars.com/api/?name=' + (reply.user?.full_name?.charAt(0) || 'K')"
              alt="Avatar"
              class="reply-avatar"
            />
            <div class="reply-info">
              <h5 class="reply-name">{{ reply.user?.full_name || 'Khách hàng' }}</h5>
              <span class="reply-date">{{ formatDate(reply.created_at) }}</span>
            </div>
          </div>
          <p class="reply-content">{{ reply.content }}</p>
        </div>
      </div>
    </div>
  </div>
  <p v-else>Chưa có đánh giá nào.</p>

  <!-- Phân trang bình luận -->
  <div class="comment-pagination" v-if="totalCommentPages > 1">
    <button @click="currentCommentPage = Math.max(1, currentCommentPage - 1)" :disabled="currentCommentPage === 1">Trước</button>
    <span>Trang {{ currentCommentPage }} / {{ totalCommentPages }}</span>
    <button @click="currentCommentPage = Math.min(totalCommentPages, currentCommentPage + 1)" :disabled="currentCommentPage === totalCommentPages">Sau</button>
  </div>
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
                    :src="related.variants?.[0]?.image_urls?.[0] || 'https://via.placeholder.com/400?text=' + encodeURIComponent(related.name)"
                    :alt="related.name"
                    class="product-image"
                  />
                </div>
                <div class="product-card-info">
                  <h3 class="product-name">{{ related.name }}</h3>
                  <div class="product-price">
                    <span class="current-price">{{ formatPrice(related.discount_price || related.base_price) }}đ</span>
                  </div>
                  <div class="product-rating"><span class="stars">★★★★★</span><span class="count">(45)</span></div>
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

// Bình luận mới
const newComment = ref('')
const newRating = ref(0) // số sao từ 1-5
const submittingComment = ref(false)

// Trả lời bình luận
const replyForms = ref({})
const replyContent = ref({})
const replying = ref(false)

// Sửa bình luận
const editingCommentId = ref(null)

// Computed rating hiển thị
const averageRating = computed(() => {
  if (!product.value?.comments?.length) return 0
  const sum = product.value.comments.reduce((acc, c) => acc + (Number(c.rating) || 5), 0)
  return sum / product.value.comments.length
})

// Methods mới
const submitComment = async () => {
  if (!newComment.value.trim() || newRating.value === 0) {
    return alert('Vui lòng nhập nội dung và chọn số sao!')
  }

  submittingComment.value = true
  try {
    await api.post(`/products/${product.value.product_id}/comments`, {
      content: newComment.value.trim(),
      rating: newRating.value,
    })

    const res = await api.get(`/products/${route.params.slug}`)
    product.value = res.data

    // Reset form
    newComment.value = ''
    newRating.value = 0
    alert('Đã gửi đánh giá thành công!')
  } catch (err) {
    alert(err.response?.data?.message || 'Không thể gửi đánh giá!')
  } finally {
    submittingComment.value = false
  }
}


// Trả lời bình luận
const toggleReplyForm = (commentId) => {
  replyForms.value[commentId] = !replyForms.value[commentId]
}

const submitReply = async (parentId) => {
  const content = replyContent.value[parentId]?.trim()
  if (!content) return alert('Vui lòng nhập nội dung trả lời!')

  replying.value = true
  try {
    const res = await api.post(`/comments/${parentId}/reply`, { content })
    console.log('Reply success:', res.data)

    // Reload sản phẩm
    const productRes = await api.get(`/products/${route.params.slug}`)
    product.value = productRes.data

    // Reset
    replyContent.value[parentId] = ''
    replyForms.value[parentId] = false
    alert('Đã trả lời bình luận!')
  } catch (err) {
    console.error('Reply error:', err)
    alert(err.response?.data?.message || 'Không thể trả lời bình luận!')
  } finally {
    replying.value = false
  }
}

// Sửa bình luận
const editComment = (comment) => {
  comment.isEditing = true
  comment.editContent = comment.content
}

const saveEdit = async (comment) => {
  if (!comment.editContent.trim()) return alert('Nội dung không được để trống!')

  try {
    await api.put(`/comments/${comment.comment_id}`, { content: comment.editContent })

    // Reload sản phẩm
    const res = await api.get(`/products/${route.params.slug}`)
    product.value = res.data

    comment.isEditing = false
    alert('Đã cập nhật bình luận!')
  } catch (err) {
    alert(err.response?.data?.message || 'Không thể cập nhật!')
  }
}

const cancelEdit = (comment) => {
  comment.isEditing = false
  comment.editContent = ''
}

// Xóa bình luận
const deleteComment = async (commentId) => {
  if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return

  try {
    await api.delete(`/comments/${commentId}`)

    const res = await api.get(`/products/${route.params.slug}`)
    product.value = res.data

    alert('Đã xóa bình luận!')
  } catch (err) {
    alert(err.response?.data?.message || 'Không thể xóa bình luận!')
  }
}

// Pagination bình luận
const currentCommentPage = ref(1)
const commentsPerPage = 5

const paginatedComments = computed(() => {
  if (!product.value?.comments?.length) return []
  const start = (currentCommentPage.value - 1) * commentsPerPage
  return product.value.comments.slice(start, start + commentsPerPage)
})

const totalCommentPages = computed(() => {
  return product.value?.comments ? Math.ceil(product.value.comments.length / commentsPerPage) : 1
})

// Computed
const images = computed(() => selectedVariant.value?.image_urls || product.value?.variants?.[0]?.image_urls || [])

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
  selectedImage.value = variant.image_urls?.[0] || images.value[0]
}

const formatPrice = (price) => {
  if (!price) return '0'
  return new Intl.NumberFormat('vi-VN').format(Math.round(price))
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const addToCart = async () => {
  if (!auth.isAuthenticated) {
    return router.push({ path: '/login', query: { redirect: route.fullPath } })
  }

  // Kiểm tra biến thể
  if (!selectedVariant.value?.variant_id) {
    return alert('Vui lòng chọn biến thể (màu sắc/kích thước) trước khi thêm vào giỏ!')
  }

  // Kiểm tra số lượng tồn kho
  if (quantity.value > (selectedVariant.value.stock || 999)) {
    return alert('Số lượng vượt quá tồn kho hiện có!')
  }

  try {
    const payload = {
      variant_id: selectedVariant.value.variant_id, // BẮT BUỘC gửi variant_id
      quantity: quantity.value
    }

    console.log('Payload add to cart:', payload) // debug

    await api.post('/cart/add', payload)

    alert('Đã thêm vào giỏ hàng thành công!')
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi thêm vào giỏ hàng. Vui lòng thử lại!')
  }
}

const buyNow = async () => {
  await addToCart()
  if (!error.value) { // nếu thêm thành công
    router.push('/cart')
  }
}

// Computed lấy ảnh chính (ưu tiên biến thể đang chọn → biến thể đầu tiên → ảnh mặc định)
const getMainImage = computed(() => {
  let urls = [];

  // 1. Ưu tiên biến thể đang chọn
  if (selectedVariant.value?.image_urls) {
    try {
      urls = JSON.parse(selectedVariant.value.image_urls || '[]');
    } catch (e) {
      console.error('Lỗi parse image_urls:', e);
    }
  }

  // 2. Nếu không có ảnh ở variant đang chọn → lấy từ biến thể đầu tiên
  if (urls.length === 0 && product.value?.variants?.length) {
    try {
      urls = JSON.parse(product.value.variants[0].image_urls || '[]');
    } catch (e) {
      console.error('Lỗi parse image_urls variant đầu:', e);
    }
  }

  // 3. Lấy ảnh đầu tiên nếu có
  if (urls.length > 0) {
    return `/storage/${urls[0]}`; // ảnh đầu tiên trong array
  }

  // 4. Fallback cuối cùng: ảnh mặc định local
  return '/images/default-product.jpg';
})

// Danh sách ảnh thumbnail (cho phần thumbnail dưới main image)
const variantImages = computed(() => {
  let urls = [];

  if (selectedVariant.value?.image_urls) {
    try {
      urls = JSON.parse(selectedVariant.value.image_urls || '[]');
    } catch (e) {}
  } else if (product.value?.variants?.length && product.value.variants[0]?.image_urls) {
    try {
      urls = JSON.parse(product.value.variants[0].image_urls || '[]');
    } catch (e) {}
  }

  return urls.map(url => `/storage/${url}`);
})

// Xử lý lỗi tải ảnh
const handleImageError = (event) => {
  event.target.src = '/images/default-product.jpg';
  event.target.alt = 'Ảnh sản phẩm không khả dụng';
}

// Fetch data
onMounted(async () => {
  const slug = route.params.slug;

  if (!slug || typeof slug !== 'string') {
    error.value = 'Slug sản phẩm không hợp lệ';
    return router.push('/products');
  }

  loading.value = true;
  error.value = null;

  try {
    const res = await api.get(`/products/${slug}`);
    console.log('API response status:', res.status);
    console.log('API response full data:', res.data); // <-- log để xem backend trả gì

    // Xử lý nhiều format response từ backend
    product.value = res.data.product 
      || res.data.data 
      || res.data 
      || null;

    if (!product.value) {
      error.value = 'Không tìm thấy sản phẩm';
      return router.push('/products');
    }

    // Kiểm tra product_id (nếu backend trả id khác tên, điều chỉnh ở đây)
    if (!product.value.product_id && !product.value.id) {
      error.value = 'Dữ liệu sản phẩm không hợp lệ (thiếu ID)';
      return router.push('/products');
    }

    // Parse image_urls an toàn (nếu backend chưa parse)
    if (product.value.variants?.length) {
      product.value.variants.forEach(variant => {
        if (typeof variant.image_urls === 'string') {
          try {
            variant.image_urls = JSON.parse(variant.image_urls) || [];
          } catch (e) {
            variant.image_urls = [];
          }
        } else if (!Array.isArray(variant.image_urls)) {
          variant.image_urls = [];
        }
      });

      // Chọn variant mặc định
      selectVariant(product.value.variants[0]);
    }

    // Related products
    if (product.value.category_id) {
      const relatedRes = await api.get('/products', {
        params: { category_id: product.value.category_id, per_page: 8 }
      });
      relatedProducts.value = (relatedRes.data.data || relatedRes.data || []).filter(
        p => p.product_id !== product.value.product_id
      );
    }
  } catch (err) {
    console.error('Lỗi tải sản phẩm:', err);
    error.value = err.response?.data?.message 
      || err.message 
      || 'Không thể tải sản phẩm. Vui lòng thử lại sau.';

    if (err.response?.status === 404 || err.response?.status === 500) {
      router.push('/products');
    }
  } finally {
    loading.value = false;
  }
});
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

            