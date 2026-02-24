<template>
  <article class="product-card">
    <!-- Badge -->
    <span v-if="product.is_featured" class="product-badge badge-hot">Hot</span>
    <span v-else-if="isNewProduct" class="product-badge badge-new">New</span>
    <span v-else-if="hasDiscount" class="product-badge badge-sale">-{{ discountPercent }}%</span>

    <div class="product-image-wrapper">
      <!-- Hình ảnh -->
      <img
        :src="product.variants?.[0]?.image_urls?.[0] || 'https://via.placeholder.com/400?text=' + encodeURIComponent(product.name)"
        :alt="product.name"
        class="product-image"
        loading="lazy"
      />

      <!-- Overlay actions -->
      <div class="product-actions">
        <!-- Yêu thích -->
        <button 
          class="action-btn wishlist-btn" 
          :class="{ active: isWishlisted }" 
          title="Yêu thích" 
          @click.stop="toggleWishlist"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
        </button>

        <!-- Xem nhanh -->
        <button 
          class="action-btn quickview-btn" 
          title="Xem nhanh" 
          @click.stop="$emit('quick-view', product)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="action-icon">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        </button>
      </div>

      <!-- Overlay link đến chi tiết -->
      <router-link 
        :to="`/products/${product.slug}`" 
        class="product-link-overlay"
      ></router-link>
    </div>

    <div class="product-info">
      <!-- Tên sản phẩm (link đến chi tiết) -->
      <router-link 
        :to="`/products/${product.slug}`" 
        class="product-name-link"
      >
        <h3 class="product-name">{{ product.name }}</h3>
      </router-link>

      <!-- Giá -->
      <div class="product-price">
        <span class="current-price">{{ formatPrice(product.discount_price || product.base_price) }}</span>
        <span v-if="hasDiscount" class="old-price">{{ formatPrice(product.base_price) }}</span>
      </div>

      <!-- Đánh giá -->
      <div class="product-rating">
        <span class="stars">
          <span v-for="i in 5" :key="i" :class="{ filled: i <= Math.round(averageRating) }">★</span>
        </span>
        <span class="review-count">({{ product.comments_count || product.reviewCount || 0 }})</span>
      </div>

      <!-- Nút hành động -->
      <div class="product-buttons">
        <router-link 
          :to="`/products/${product.slug}`" 
          class="view-detail-btn"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
          <span>Xem chi tiết</span>
        </router-link>

        <button 
          class="add-to-cart-btn" 
          @click="addToCart"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <span>Thêm vào giỏ</span>
        </button>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api' // axios instance

const props = defineProps<{
  product: {
    product_id: number | string
    name: string
    slug: string
    base_price: number
    discount_price?: number
    final_price?: number
    image?: string
    variants?: Array<{
      variant_id: number
      image_urls?: string[]
      stock: number
      sku?: string
      color?: string
      storage_size?: string
      name?: string
    }>
    is_featured?: boolean
    comments_count?: number
    average_rating?: number
    rating?: number
    reviewCount?: number
    likes_count?: number
    stock_total?: number
    [key: string]: any
  }
}>()

const emit = defineEmits(['add-to-cart', 'quick-view'])

const router = useRouter()
const isWishlisted = ref(false)

// Computed
const hasDiscount = computed(() => {
  return props.product.discount_price && props.product.discount_price < props.product.base_price
})

const discountPercent = computed(() => {
  if (!hasDiscount.value) return 0
  return Math.round((props.product.base_price - props.product.discount_price) / props.product.base_price * 100)
})

const averageRating = computed(() => {
  return props.product.average_rating || props.product.rating || 5
})

const isNewProduct = computed(() => {
  // Logic tùy ý: ví dụ sản phẩm tạo trong 7 ngày qua
  const createdAt = new Date(props.product.created_at)
  const now = new Date()
  const diffDays = (now.getTime() - createdAt.getTime()) / (1000 * 3600 * 24)
  return diffDays <= 7
})

// Methods
const formatPrice = (price: number) => {
  if (!price) return '0đ'
  return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + 'đ'
}

const toggleWishlist = async () => {
  if (!localStorage.getItem('token')) {
    return alert('Vui lòng đăng nhập để sử dụng tính năng yêu thích!')
  }

  try {
    if (isWishlisted.value) {
      await api.delete(`/wishlist/remove/${props.product.product_id}`)
      isWishlisted.value = false
      alert('Đã xóa khỏi danh sách yêu thích')
    } else {
      await api.post(`/wishlist/add/${props.product.product_id}`)
      isWishlisted.value = true
      alert('Đã thêm vào danh sách yêu thích')
    }
  } catch (err) {
    console.error('Lỗi wishlist:', err)
    alert('Có lỗi xảy ra. Vui lòng thử lại!')
  }
}

const addToCart = async () => {
  if (!localStorage.getItem('token')) {
    return alert('Vui lòng đăng nhập để thêm vào giỏ hàng!')
  }

  try {
    const variant = props.product.variants?.[0]
    if (!variant?.variant_id) {
      return alert('Sản phẩm này chưa có biến thể khả dụng')
    }

    await api.post('/cart/add', {
      variant_id: variant.variant_id,
      quantity: 1
    })
    alert('Đã thêm vào giỏ hàng!')
  } catch (err) {
    console.error('Lỗi thêm giỏ hàng:', err)
    alert('Không thể thêm sản phẩm. Vui lòng thử lại!')
  }
}
</script>

<style scoped>
.product-card {
  position: relative;
  background: #ffffff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #f1f5f9;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.product-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 10;
  padding: 6px 14px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  border-radius: 50px;
}

.badge-hot { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: #ffffff; }
.badge-new { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; }
.badge-sale { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; }

.product-image-wrapper {
  position: relative;
  width: 100%;
  padding-top: 100%; /* 1:1 Aspect Ratio */
  overflow: hidden;
  background: #f8fafc;
}

.product-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image { transform: scale(1.08); }

.product-actions {
  position: absolute;
  top: 16px;
  right: 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  opacity: 1;
  transform: translateX(0);
  transition: all 0.3s ease;
  z-index: 20;
}

.product-card:hover .product-actions {
  opacity: 1;
  transform: translateX(0);
}

.action-btn {
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
  aspect-ratio: 1 / 1;
  padding: 0;
  border: none;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
}

.action-btn svg {
  transition: all 0.3s ease;
}

/* Nút Yêu thích */
.wishlist-btn:hover {
  transform: scale(1.15);
  background: #fef2f2;
  color: #ef4444;
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.25);
}

.wishlist-btn:hover svg {
  fill: #ef4444;
}

.wishlist-btn.active {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

.wishlist-btn.active svg {
  fill: white;
}

/* Nút Xem nhanh */
.quickview-btn:hover {
  transform: scale(1.15);
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
}

/* Nút Zoom */
.zoom-btn:hover {
  transform: scale(1.15);
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(139, 92, 246, 0.35);
}

/* Icon trong action button */
.action-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.product-link-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 10;
}

.product-info { padding: 20px; flex: 1; display: flex; flex-direction: column; }

.product-name-link {
  text-decoration: none;
  color: inherit;
}

.product-name {
  font-size: 16px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 12px 0;
  line-height: 1.4;
  min-height: 44px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.2s;
}

.product-name:hover { color: #ff6b35; }

.product-price {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.current-price { font-size: 18px; font-weight: 800; color: #ff6b35; }
.old-price { font-size: 14px; color: #94a3b8; text-decoration: line-through; }

.product-rating {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 16px;
}

.stars { color: #e2e8f0; font-size: 14px; }
.stars .filled { color: #fbbf24; }
.review-count { font-size: 12px; color: #94a3b8; }

/* Wrapper cho các nút */
.product-buttons {
  margin-top: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Nút Xem chi tiết */
.view-detail-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  background: transparent;
  color: #0f172a;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.view-detail-btn:hover {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border-color: transparent;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.view-detail-btn svg {
  transition: transform 0.3s ease;
}

.view-detail-btn:hover svg {
  transform: scale(1.1);
}

/* Nút Thêm vào giỏ */
.add-to-cart-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.add-to-cart-btn:hover {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(255, 107, 53, 0.35);
}

.add-to-cart-btn svg {
  transition: transform 0.3s ease;
}

.add-to-cart-btn:hover svg {
  transform: scale(1.1);
}

/* Responsive */
@media (max-width: 640px) {
  .product-info {
    padding: 16px;
  }
  
  .product-name {
    font-size: 14px;
    min-height: 38px;
  }
  
  .view-detail-btn,
  .add-to-cart-btn {
    padding: 10px 12px;
    font-size: 13px;
  }
  
  .action-btn {
    width: 36px;
    height: 36px;
  }
  
  .action-btn svg {
    width: 16px;
    height: 16px;
  }
}
</style>