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
                <span class="sku">SKU: {{ selectedVariant?.sku || product.slug?.toUpperCase() }}</span>
              </div>

              <div class="price-box">
                <span class="current-price">{{ formatPrice(currentVariantPrice) }}</span>
                <span v-if="discountPercent > 0" class="old-price">{{ formatPrice(currentVariantOldPrice) }}</span>
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
                    :class="{ active: selectedVariant?.variant_id === variant.variant_id, 'out-of-stock': variant.stock <= 0 }"
                    @click="variant.stock > 0 && selectVariant(variant)"
                    :title="variant.stock <= 0 ? 'Hết hàng' : ''"
                  >

                    <img
                      v-if="variant.image_urls && variant.image_urls.length"
                      :src="getVariantImage(variant.image_urls[0])"
                      class="variant-icon"
                      :alt="variant.name || 'Variant'"
                      @error="$event.target.style.display = 'none'"
                    />
                    <span
                      v-else-if="variant.color_hex"
                      class="color-dot"
                      :style="{ backgroundColor: variant.color_hex }"
                    ></span>

                    <span class="variant-label">{{ variant.name || variant.color || variant.storage_size || 'Mặc định' }}</span>
                    
                    <span v-if="variant.stock <= 0" class="variant-oos-badge">Hết</span>
                  </button>
                </div>

                <!-- Thumbnail ảnh variant đang chọn -->
                <div v-if="variantImages.length > 1" class="variant-thumbs">
                  <img
                    v-for="(img, i) in variantImages"
                    :key="i"
                    :src="img"
                    class="variant-thumb"
                    :class="{ active: selectedImage === img }"
                    @click="selectedImage = img"
                    @error="$event.target.style.display='none'"
                  />
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
              Đánh giá ({{ totalRatings }})
            </button>
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'comments' }"
              @click="activeTab = 'comments'"
            >
              Bình luận ({{ totalComments }})
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

              <!-- Enhanced Reviews Summary -->
              <div class="reviews-summary">
                <div class="rating-overview">
                  <span class="big-rating" v-if="totalRatings > 0">{{ averageRating.toFixed(1) }}</span>
                  <span class="big-rating no-rating" v-else>—</span>
                  <div class="rating-details">
                    <span class="stars-big" v-if="totalRatings > 0">★★★★★</span>
                    <span class="total-reviews">{{ totalRatings }} đánh giá</span>
                  </div>
                </div>
              </div>

              <!-- Form đánh giá -->
              <!-- <div v-if="auth.isAuthenticated" class="comment-form">
                <h4>Viết đánh giá</h4>
                <div class="rating-input">
                  <span>Đánh giá sao của bạn:</span>
                  <div class="stars-select">
                    <span
                      v-for="star in 5"
                      :key="star"
                      class="star"
                      :class="{ active: newRating >= star }"
                      @click="newRating = star"
                    >★</span>
                    <span class="rating-label" v-if="newRating > 0">({{ newRating }}/5)</span>
                  </div>
                </div>
                <textarea
                  v-model="newComment"
                  placeholder="Chia sẻ cảm nhận chi tiết kèm đánh giá sao..."
                  class="comment-textarea"
                  rows="4"
                ></textarea>
                <button
                  class="submit-comment-btn"
                  :disabled="!newComment.trim() || newRating === 0 || submittingComment"
                  @click="submitRating"
                >
                  {{ submittingComment ? 'Đang gửi...' : 'Gửi đánh giá' }}
                </button>
              </div>
              <div v-else class="login-to-comment">
                <p>Vui lòng <router-link to="/login">đăng nhập</router-link> để đánh giá.</p>
              </div> -->

              <!-- Danh sách Đánh giá -->
              <div v-if="ratingsList.length" class="reviews-section">
                <h4 class="section-title">⭐ Đánh giá ({{ ratingsList.length }})</h4>
                <div class="reviews-list">
                  <div v-for="comment in paginatedRatings" :key="comment.comment_id" class="review-card">
                    <div class="review-header">
                      <img
                        :src="comment.user?.avatar ? storageUrl(comment.user.avatar) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user?.full_name?.charAt(0) || 'K')"
                        alt="Avatar"
                        class="reviewer-avatar"
                      />
                      <div class="review-meta">
                        <span class="reviewer-name">{{ comment.user?.full_name || 'Khách' }}</span>
                        <span class="review-date">{{ formatDate(comment.created_at) }}</span>
                      </div>
                      <div class="review-stars">
                        <span v-for="s in 5" :key="s" :class="s <= comment.rating ? 'star-on' : 'star-off'">★</span>
                        <span class="rating-label-small">({{ comment.rating }}/5)</span>
                      </div>
                    </div>
                    <p class="review-content">{{ comment.content }}</p>
                    <div v-if="auth.isAuthenticated" class="reply-actions">
                      <button class="reply-link" @click="toggleReplyForm(comment.comment_id)">Trả lời</button>
                    </div>
                    <div v-if="replyForms[comment.comment_id]" class="reply-form">
                      <textarea
                        v-model="replyContent[comment.comment_id]"
                        placeholder="Nhập nội dung trả lời..."
                        class="comment-textarea"
                        rows="2"
                      ></textarea>
                      <div class="reply-form-actions">
                        <button class="btn-cancel" @click="cancelReply(comment.comment_id)">Hủy</button>
                        <button class="submit-comment-btn" :disabled="!replyContent[comment.comment_id]?.trim() || replying" @click="submitReply(comment.comment_id)">
                          {{ replying ? 'Đang gửi...' : 'Gửi trả lời' }}
                        </button>
                      </div>
                    </div>
                    <!-- Replies -->
                    <div v-if="comment.replies?.length" class="replies-list">
                      <div v-for="reply in comment.replies" :key="reply.comment_id" class="reply-card">
                        <img
                          :src="reply.user?.avatar ? storageUrl(reply.user.avatar) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(reply.user?.full_name?.charAt(0) || 'K')"
                          alt="Avatar"
                          class="reviewer-avatar small"
                        />
                        <div class="reply-body">
                          <span class="reviewer-name">{{ reply.user?.full_name || 'Khách' }}</span>
                          <p class="review-content">{{ reply.content }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="totalRatingPages > 1" class="comment-pagination">
                  <button :disabled="ratingPage === 1" @click="ratingPage--">Trước</button>
                  <span>{{ ratingPage }} / {{ totalRatingPages }}</span>
                  <button :disabled="ratingPage === totalRatingPages" @click="ratingPage++">Sau</button>
                </div>
              </div>

              <p v-if="!ratingsList.length">Chưa có đánh giá nào.</p>
            </div>

            <!-- Bình luận -->
            <div v-if="activeTab === 'comments'" class="tab-pane">
              <h3>Bình luận</h3>

              <!-- Form bình luận -->
              <div v-if="auth.isAuthenticated" class="comment-form">
                <h4>Viết bình luận</h4>
                <textarea
                  v-model="newCommentOnly"
                  placeholder="Chia sẻ suy nghĩ của bạn về sản phẩm..."
                  class="comment-textarea"
                  rows="4"
                ></textarea>
                <button
                  class="submit-comment-btn"
                  :disabled="!newCommentOnly.trim() || submittingCommentOnly"
                  @click="submitCommentOnly"
                >
                  {{ submittingCommentOnly ? 'Đang gửi...' : 'Gửi bình luận' }}
                </button>
              </div>
              <div v-else class="login-to-comment">
                <p>Vui lòng <router-link to="/login">đăng nhập</router-link> để bình luận.</p>
              </div>

              <!-- Danh sách Bình luận -->
              <div v-if="commentsList.length" class="reviews-section">
                <h4 class="section-title">💬 Bình luận ({{ commentsList.length }})</h4>
                <div class="reviews-list">
                  <div v-for="comment in paginatedCommentsOnly" :key="comment.comment_id" class="review-card">
                    <div class="review-header">
                      <img
                        :src="comment.user?.avatar ? storageUrl(comment.user.avatar) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user?.full_name?.charAt(0) || 'K')"
                        alt="Avatar"
                        class="reviewer-avatar"
                      />
                      <div class="review-meta">
                        <span class="reviewer-name">{{ comment.user?.full_name || 'Khách' }}</span>
                        <span class="review-date">{{ formatDate(comment.created_at) }}</span>
                      </div>
                    </div>
                    <p class="review-content">{{ comment.content }}</p>
                    <div v-if="auth.isAuthenticated" class="reply-actions">
                      <button class="reply-link" @click="toggleReplyForm(comment.comment_id)">Trả lời</button>
                    </div>
                    <div v-if="replyForms[comment.comment_id]" class="reply-form">
                      <textarea
                        v-model="replyContent[comment.comment_id]"
                        placeholder="Nhập nội dung trả lời..."
                        class="comment-textarea"
                        rows="2"
                      ></textarea>
                      <div class="reply-form-actions">
                        <button class="btn-cancel" @click="cancelReply(comment.comment_id)">Hủy</button>
                        <button class="submit-comment-btn" :disabled="!replyContent[comment.comment_id]?.trim() || replying" @click="submitReply(comment.comment_id)">
                          {{ replying ? 'Đang gửi...' : 'Gửi trả lời' }}
                        </button>
                      </div>
                    </div>
                    <!-- Replies -->
                    <div v-if="comment.replies?.length" class="replies-list">
                      <div v-for="reply in comment.replies" :key="reply.comment_id" class="reply-card">
                        <img
                          :src="reply.user?.avatar ? storageUrl(reply.user.avatar) : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(reply.user?.full_name?.charAt(0) || 'K')"
                          alt="Avatar"
                          class="reviewer-avatar small"
                        />
                        <div class="reply-body">
                          <span class="reviewer-name">{{ reply.user?.full_name || 'Khách' }}</span>
                          <p class="review-content">{{ reply.content }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="totalCommentOnlyPages > 1" class="comment-pagination">
                  <button :disabled="commentOnlyPage === 1" @click="commentOnlyPage--">Trước</button>
                  <span>{{ commentOnlyPage }} / {{ totalCommentOnlyPages }}</span>
                  <button :disabled="commentOnlyPage === totalCommentOnlyPages" @click="commentOnlyPage++">Sau</button>
                </div>
              </div>

              <p v-if="!commentsList.length">Chưa có bình luận nào.</p>
            </div>
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
import { storageUrl } from '@/utils/image'

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

// Form đánh giá
const newComment = ref('')
const newRating = ref(0)
const submittingComment = ref(false)

// Form bình luận
const newCommentOnly = ref('')
const submittingCommentOnly = ref(false)

// Trả lời & sửa bình luận (giữ nguyên)
const replyForms = ref({})
const replyContent = ref({})
const replying = ref(false)

// Sửa bình luận
const editingCommentId = ref(null)
const editContent = ref('')
const editRating = ref(0)
const isEditing = ref(false)

// Phân trang đánh giá & bình luận riêng
const ratingPage = ref(1)
const commentOnlyPage = ref(1)
const itemsPerPage = 5

const ratingsList = computed(() => product.value?.comments?.filter(c => c.rating > 0) || [])
const commentsList = computed(() => product.value?.comments?.filter(c => !c.rating || c.rating === 0) || [])

const paginatedRatings = computed(() => {
  const start = (ratingPage.value - 1) * itemsPerPage
  return ratingsList.value.slice(start, start + itemsPerPage)
})
const totalRatingPages = computed(() => Math.ceil(ratingsList.value.length / itemsPerPage))

const paginatedCommentsOnly = computed(() => {
  const start = (commentOnlyPage.value - 1) * itemsPerPage
  return commentsList.value.slice(start, start + itemsPerPage)
})
const totalCommentOnlyPages = computed(() => Math.ceil(commentsList.value.length / itemsPerPage))

const specifications = computed(() => {
  return product.value?.specifications?.split('\n').filter(s => s.trim()) || []
})

// Separate counts for ratings vs comments
const totalRatings = computed(() => ratingsList.value.length)
const totalComments = computed(() => commentsList.value.length)

// Average rating (only from ratings)
const averageRating = computed(() => {
  if (!ratingsList.value.length) return 0
  const sum = ratingsList.value.reduce((acc, c) => acc + Number(c.rating), 0)
  return sum / ratingsList.value.length
})

// Computed ảnh chính (ưu tiên ảnh được chọn → ảnh đầu của variant đang chọn → ảnh đầu của variant đầu tiên)
const getMainImage = computed(() => {
  // 1. Ảnh thumbnail được chọn
  if (selectedImage.value) {
    return selectedImage.value.startsWith('http') 
      ? selectedImage.value 
      : storageUrl(selectedImage.value)
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
      : storageUrl(firstImage)
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
      : storageUrl(url)
  })
})

// Ảnh cho Related Products

const getVariantImage = (imagePath) => {
  if (!imagePath) return ''
  return imagePath.startsWith('http') ? imagePath : storageUrl(imagePath)
}

const getRelatedImage = (related) => {
  const firstVariant = related.variants?.[0]
  if (firstVariant?.image_urls?.length) {
    let path = firstVariant.image_urls[0]
    if (Array.isArray(path)) path = path[0]
    if (path && !path.startsWith('http')) {
      return storageUrl(path)
    }
    return path
  }
}


// Các computed khác giữ nguyên
const currentVariantPrice = computed(() => {
  if (selectedVariant.value?.price != null && selectedVariant.value.price > 0) {
    return Number(selectedVariant.value.price)
  }
  const base = product.value?.discount_price || product.value?.base_price || 0
  const extra = selectedVariant.value?.price_extra || 0
  return Number(base) + Number(extra)
})

const currentVariantOldPrice = computed(() => {
  if (selectedVariant.value?.price != null && selectedVariant.value.price > 0) {
    return Number(product.value?.base_price || selectedVariant.value.price)
  }
  const base = product.value?.base_price || 0
  const extra = selectedVariant.value?.price_extra || 0
  return Number(base) + Number(extra)
})

// Helper: lấy giá hiển thị của 1 variant (dùng trong danh sách variant buttons)
const getVariantPrice = (variant) => {
  if (variant.price != null && variant.price > 0) return Number(variant.price)
  const base = product.value?.discount_price || product.value?.base_price || 0
  return Number(base) + Number(variant.price_extra || 0)
}

const discountPercent = computed(() => {
  const base = currentVariantOldPrice.value
  const final = currentVariantPrice.value
  return base && final && base > final ? Math.round((base - final) / base * 100) : 0
})

const maxQuantity = computed(() => selectedVariant.value?.stock || product.value?.stock_total || 999)

const productFeatures = computed(() => {
  return product.value?.features?.split('\n').filter(f => f.trim()) || [

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
    selectedImage.value = storageUrl(first)
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
  event.target.style.display = 'none'
}

const submitRating = async () => {
  if (!newComment.value.trim() || newRating.value === 0) return
  submittingComment.value = true
  try {
    await api.post(`/products/${product.value.product_id}/comments`, {
      content: newComment.value,
      rating: newRating.value,
    })
    alert('Đánh giá của bạn đã được đăng thành công!')
    newComment.value = ''
    newRating.value = 0
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi gửi đánh giá')
  } finally {
    submittingComment.value = false
  }
}

const submitCommentOnly = async () => {
  if (!newCommentOnly.value.trim()) return
  submittingCommentOnly.value = true
  try {
    await api.post(`/products/${product.value.product_id}/comments`, {
      content: newCommentOnly.value,
      rating: null,
    })
    alert('Bình luận của bạn đã được đăng thành công!')
    newCommentOnly.value = ''
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi gửi bình luận')
  } finally {
    submittingCommentOnly.value = false
  }
}

const toggleReplyForm = (commentId) => {
  replyForms.value[commentId] = !replyForms.value[commentId]
  if (!replyContent.value[commentId]) replyContent.value[commentId] = ''
}

const cancelReply = (commentId) => {
  replyForms.value[commentId] = false
  replyContent.value[commentId] = ''
}

const submitReply = async (commentId) => {
  const content = replyContent.value[commentId]?.trim()
  if (!content) return
  replying.value = true
  try {
    const res = await api.post(`/comments/${commentId}/reply`, { content })
    const newReply = res.data.reply
    const comment = product.value?.comments?.find(c => c.comment_id === commentId)
    if (comment) {
      if (!comment.replies) comment.replies = []
      comment.replies.push(newReply)
    }
    alert('Trả lời thành công!')
    replyForms.value[commentId] = false
    replyContent.value[commentId] = ''
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi gửi trả lời')
  } finally {
    replying.value = false
  }
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
  color: #ccc;
  transition: color 0.2s ease;
}

.star.active {
  color: #ffc107;
}

/* Variant buttons */

.variant-btn {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 110px;
  min-height: 60px;
  width: 70px;
}

.variant-btn:hover:not(.out-of-stock) {
  border-color: #ff6b35;
  transform: translateY(-2px);
}

.variant-btn:hover:not(.out-of-stock) {
  border-color: #ff6b35;
  transform: translateY(-2px);
}

.variant-btn:hover:not(.out-of-stock) {
  border-color: #ff6b35;
}
.variant-btn.active {
  border-color: #ff6b35;
  background: #fff5f0;
  box-shadow: 0 0 0 3px rgba(255,107,53,0.15);
}
.variant-btn.out-of-stock {
  opacity: 0.45;
  cursor: not-allowed;
}
.variant-label {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.2;
}

.color-dot {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: block;
  border: 2px solid #f1f5f9;
}

.variant-icon {
  width: 32px;
  height: 32px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #f1f5f9;
  transition: transform 0.2s;
}

.variant-btn:hover .variant-icon {
  transform: scale(1.1);
}
.variant-price-hint {
  font-size: 12px;
  color: #ff6b35;
  font-weight: 700;
}
.variant-oos-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  font-size: 9px;
  background: #ef4444;
  color: white;
  padding: 1px 5px;
  border-radius: 4px;
}

/* Variant thumbnails */
.variant-thumbs {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}
.variant-thumb {
  width: 56px;
  height: 56px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid #e2e8f0;
  cursor: pointer;
  transition: border-color 0.2s;
}
.variant-thumb:hover, .variant-thumb.active {
  border-color: #ff6b35;
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

/* Review card */
.reviews-list {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.review-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}

.reviewer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.reviewer-avatar.small {
  width: 30px;
  height: 30px;
}

.review-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.reviewer-name {
  font-weight: 600;
  font-size: 14px;
  color: #1e293b;
}

.review-date {
  font-size: 12px;
  color: #94a3b8;
}

/* .review-stars {
  margin-left: auto;
} */

.star-on  { color: #f59e0b; font-size: 16px; }
.star-off { color: #cbd5e1; font-size: 16px; }

/* Mode toggle (now visible) */
.mode-toggle {
  display: flex;
  background: #f1f5f9;
  border-radius: 10px;
  padding: 4px;
  margin-bottom: 16px;
}

.mode-toggle button {
  flex: 1;
  padding: 10px 8px;
  border: none;
  background: transparent;
  border-radius: 6px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  border: 2px solid transparent;
}

.mode-toggle button.active,
.mode-toggle button:hover {
  background: #3b82f6;
  color: white;
}

/* Enhanced summary styles */
.big-rating {
  font-size: 2.5rem;
  font-weight: 700;
  color: #f59e0b;
  line-height: 1;
}

.big-rating.no-rating {
  color: #94a3b8;
}

.comment-count {
  color: #64748b;
  font-weight: 500;
}

.rating-label,
.rating-label-small {
  font-size: 0.875rem;
  color: #64748b;
  margin-left: 0.5rem;
  font-weight: 500;
}

.review-type-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #dbeafe;
  color: #1e40af;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  margin-left: auto;
}

.review-content {
  font-size: 14px;
  line-height: 1.7;
  color: #334155;
  margin: 0;
}

/* Reply actions */
.reply-actions {
  margin-top: 8px;
}

.reply-link {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  padding: 0;
}

.reply-link:hover {
  text-decoration: underline;
}

/* Reply form */
.reply-form {
  margin-top: 12px;
  padding: 16px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}

.reply-form .comment-textarea {
  min-height: 60px;
  margin-bottom: 12px;
}

.reply-form-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn-cancel {
  background: #f1f5f9;
  color: #64748b;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cancel:hover {
  background: #e2e8f0;
}

/* Replies */
.replies-list {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px dashed #e2e8f0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.reply-card {
  display: flex;
  gap: 10px;
  padding: 10px 14px;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.reply-body {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
</style>

            