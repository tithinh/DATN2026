<template>
  <Transition name="modal-fade">
    <div v-if="isOpen" class="quickview-overlay" @click.self="close">
      <div class="quickview-modal">
        <button class="close-btn" @click="close">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        <div class="quickview-content">
          <!-- Product Gallery -->
          <div class="quickview-gallery">
            <div class="main-image">
              <img 
                :src="activeImage || 'https://via.placeholder.com/500?text=' + encodeURIComponent(product.name)" 
                :alt="product.name" 
              />
            </div>

            <div class="thumbnail-list" v-if="images.length">
              <button 
                v-for="(img, index) in images" 
                :key="index"
                class="thumbnail"
                :class="{ active: activeImage === img }"
                @click="activeImage = img"
              >
                <img :src="img" :alt="`${product.name} ${+index + 1}`" />
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="quickview-info">
            <h2 class="product-title">{{ product.name }}</h2>
            
            <div class="product-meta">
              <div class="rating">
                <span class="stars">★★★★★</span>
                <span class="rating-text">{{ averageRating.toFixed(1) }} ({{ product.comments?.length || 0 }} đánh giá)</span>
              </div>
              <span class="divider">|</span>
              <span class="sku">SKU: {{ selectedVariant?.sku || product.slug?.toUpperCase() || 'N/A' }}</span>
            </div>

            <div class="price-box">
              <span class="current-price">{{ formatPrice(selectedVariant?.discount_price	 || product.discount_price	 || product.base_price) }}</span>
              <span v-if="discount > 0" class="old-price">{{ formatPrice(selectedVariant?.base_price || product.base_price) }}</span>
              <span v-if="discount > 0" class="discount-badge">-{{ discount }}%</span>
            </div>

            <div class="short-description">
              {{ product.short_desc || product.description?.substring(0, 150) + '...' || 'Mô tả ngắn về sản phẩm đang được cập nhật. Sản phẩm chất lượng cao, chính hãng...' }}
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
                <button class="qty-btn" @click="decreaseQty">−</button>
                <input type="number" v-model.number="quantity" class="qty-input" min="1" :max="maxQuantity" />
                <button class="qty-btn" @click="increaseQty">+</button>
              </div>
              <span class="stock-info">Còn {{ selectedVariant?.stock || product.stock_total || 0 }} sản phẩm</span>
            </div>

            <!-- Actions -->
            <div class="action-buttons">
              <button class="btn-add-cart" @click="addToCart" :disabled="addingToCart">
                <span v-if="addingToCart" class="loading-spinner"></span>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="8" cy="21" r="1"/>
                  <circle cx="19" cy="21" r="1"/>
                  <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
                <span>{{ addingToCart ? 'Đang thêm...' : 'Thêm vào giỏ' }}</span>
              </button>

              <router-link 
                :to="`/products/${product.slug}`" 
                class="btn-view-detail" 
                @click="close"
              >
                Xem chi tiết
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    </Transition>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

const props = defineProps<{
  isOpen: boolean
  product: any // { name, slug, base_price, discount_price, discount_price	, variants: [], short_desc, description, stock_total, ... }
}>()

const emit = defineEmits(['close', 'add-to-cart'])

const router = useRouter()

const quantity = ref(1)
const activeImage = ref('')
const selectedVariant = ref<any>(null)
const addingToCart = ref(false)

// Computed
const images = computed(() => {
  return selectedVariant.value?.image_urls || props.product.variants?.[0]?.image_urls || [props.product.image || '']
})

const discount = computed(() => {
  const base = selectedVariant.value?.base_price || props.product.base_price
  const final = selectedVariant.value?.discount_price	 || props.product.discount_price	 || props.product.base_price
  return base && final && base > final ? Math.round((base - final) / base * 100) : 0
})

const maxQuantity = computed(() => selectedVariant.value?.stock || props.product.stock_total || 999)

const averageRating = computed(() => {
  if (!props.product.comments?.length) return 0
  const sum = props.product.comments.reduce((acc: number, c: any) => acc + (Number(c.rating) || 5), 0)
  return sum / props.product.comments.length
})

// Methods
const selectVariant = (variant: any) => {
  selectedVariant.value = variant
  activeImage.value = variant.image_urls?.[0] || images.value[0]
}

const formatPrice = (price: number) => {
  if (!price) return '0đ'
  return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + 'đ'
}

const increaseQty = () => {
  if (quantity.value < maxQuantity.value) quantity.value++
}

const decreaseQty = () => {
  if (quantity.value > 1) quantity.value--
}

const addToCart = async () => {
  if (!localStorage.getItem('token')) {
    alert('Vui lòng đăng nhập để thêm vào giỏ hàng!')
    return
  }

  const variant = selectedVariant.value || props.product.variants?.[0]
  if (!variant?.variant_id) {
    alert('Sản phẩm này chưa có biến thể khả dụng')
    return
  }

  addingToCart.value = true
  try {
    await api.post('/cart/add', {
      variant_id: variant.variant_id,
      quantity: quantity.value
    })
    alert('Đã thêm vào giỏ hàng!')
    emit('add-to-cart', { ...props.product, quantity: quantity.value })
  } catch (err: any) {
    console.error('Lỗi thêm giỏ hàng:', err)
    alert(err.response?.data?.message || 'Không thể thêm sản phẩm. Vui lòng thử lại!')
  } finally {
    addingToCart.value = false
  }
}

const close = () => {
  emit('close')
}

// Watch khi product thay đổi
watch(() => props.product, (newProduct) => {
  if (newProduct) {
    activeImage.value = newProduct.variants?.[0]?.image_urls?.[0] || newProduct.image || ''
    selectedVariant.value = newProduct.variants?.[0] || null
    quantity.value = 1
  }
}, { immediate: true })
</script>

<style scoped>
.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #fff;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 8px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.quickview-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.quickview-modal {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 900px;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  max-height: 90vh;
  overflow-y: auto;
}

.close-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  background: transparent;
  border: none;
  cursor: pointer;
  color: #64748b;
  transition: all 0.2s;
  z-index: 10;
  padding: 5px;
  border-radius: 50%;
}

.close-btn:hover {
  background: #f1f5f9;
  color: #ef4444;
}

.quickview-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  padding: 40px;
}

/* Gallery */
.quickview-gallery {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.main-image {
  width: 100%;
  aspect-ratio: 1;
  background: #f8fafc;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.thumbnail-list {
  display: flex;
  gap: 10px;
}

.thumbnail {
  width: 70px;
  height: 70px;
  border-radius: 12px;
  border: 2px solid transparent;
  overflow: hidden;
  cursor: pointer;
  padding: 0;
  background: #f8fafc;
}

.thumbnail.active {
  border-color: #ff6b35;
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Info */
.quickview-info {
  display: flex;
  flex-direction: column;
}

.product-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 12px 0;
  line-height: 1.3;
}

.product-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  font-size: 14px;
  color: #64748b;
}

.divider { color: #e2e8f0; }

.price-box {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.current-price {
  font-size: 28px;
  font-weight: 800;
  color: #ff6b35;
}

.old-price {
  font-size: 16px;
  color: #94a3b8;
  text-decoration: line-through;
}

.discount-badge {
  background: #fef2f2;
  color: #ef4444;
  padding: 4px 8px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 13px;
}

.short-description {
  font-size: 15px;
  color: #475569;
  line-height: 1.6;
  margin-bottom: 24px;
}

.variant-title {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
  margin-bottom: 12px;
}

.variant-options {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
}

.variant-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  font-size: 14px;
  color: #475569;
  transition: all 0.2s;
}

.variant-btn.active {
  border-color: #ff6b35;
  color: #ff6b35;
  background: #fff7ed;
}

.color-dot {
  width: 16px;
  height: 16px;
  border-radius: 50%;
}

.quantity-section {
  margin-bottom: 30px;
}

.quantity-box {
  display: inline-flex;
  align-items: center;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 4px;
}

.qty-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 18px;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.qty-btn:hover { background: #f1f5f9; }

.qty-input {
  width: 50px;
  text-align: center;
  border: none;
  font-weight: 600;
  font-size: 16px;
  color: #0f172a;
}

.qty-input:focus { outline: none; }

/* Actions */
.action-buttons {
  display: flex;
  gap: 16px;
}

.btn-add-cart {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 24px;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: white;
  border: none;
  border-radius: 14px;
  font-weight: 600;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-add-cart:hover {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.35);
}

.btn-view-detail {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 14px 24px;
  background: transparent;
  color: #0f172a;
  border: 2px solid #e2e8f0;
  border-radius: 14px;
  font-weight: 600;
  font-size: 16px;
  text-decoration: none;
  transition: all 0.3s;
}

.btn-view-detail:hover {
  border-color: #0f172a;
  background: #f8fafc;
}

/* Animation */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active .quickview-modal,
.modal-fade-leave-active .quickview-modal {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-fade-enter-from .quickview-modal,
.modal-fade-leave-to .quickview-modal {
  transform: scale(0.9);
}

/* Responsive */
@media (max-width: 768px) {
  .quickview-content {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 24px;
  }
  
  .product-title { font-size: 20px; }
  .current-price { font-size: 24px; }
}
</style>
