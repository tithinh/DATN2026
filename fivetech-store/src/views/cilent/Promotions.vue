<template>
  <div class="products-page">
    <!-- Page Header / Breadcrumb -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <span class="current">Sản phẩm giảm giá</span>
        </nav>
        <h1 class="page-title">Sản phẩm giảm giá</h1>
        <p class="page-subtitle">Khám phá những ưu đãi hot nhất - Giá sốc có hạn!</p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="products-content">
      <div class="content-container">
        <!-- Sidebar Filter -->
        <ProductFilter 
          @update:filter="applyFilters" 
          @clear-all="clearFilters" 
        />

        <!-- Products Main -->
        <div class="products-main">
          <!-- Toolbar -->
          <div class="products-toolbar">
            <div class="toolbar-actions">
              <!-- Search Input -->
              <div class="search-box">
                <input 
                  type="text" 
                  v-model="searchQuery" 
                  placeholder="Tìm sản phẩm giảm giá..." 
                  class="search-input"
                  @keyup.enter="handleSearch"
                />
                <button class="search-btn" @click="handleSearch">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                  </svg>
                </button>
              </div>
              <select class="sort-select" v-model="sortBy" @change="handleSortChange">
                <option value="discount_desc">Giảm giá cao nhất</option>
                <option value="price_asc">Giá thấp đến cao</option>
                <option value="price_desc">Giá cao đến thấp</option>
                <option value="newest">Mới nhất</option>
                <option value="bestseller">Bán chạy</option>
              </select>
            </div>
            <div class="promo-badge">
              <span class="badge-text">🔥 Chỉ hiển thị sản phẩm đang giảm giá</span>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <p>Đang tải ưu đãi hot...</p>
          </div>

          <!-- Products Grid -->
          <div v-else-if="products.length" class="products-grid">
            <ProductCard 
              v-for="product in products" 
              :key="product.product_id" 
              :product="product"
              :is-wishlisted="wishlistProductIds.has(product.product_id)"
              @add-to-cart="addToCart"
              @quick-view="quickView"
              @toggle-wishlist="toggleWishlistFromCard"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="empty-state">
            <p>😔 Hiện tại chưa có sản phẩm giảm giá nào.</p>
            <p>Hãy quay lại <router-link to="/products">danh sách sản phẩm</router-link> để xem thêm!</p>
          </div>

          <!-- Pagination -->
          <div class="pagination" v-if="pagination.total > pagination.per_page">
            <button 
              class="page-btn prev" 
              :disabled="pagination.current_page === 1"
              @click="changePage(pagination.current_page - 1)"
            >
              ← Trước
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

            <span v-if="pagination.last_page > 5" class="page-dots">...</span>

            <button 
              class="page-btn next" 
              :disabled="pagination.current_page === pagination.last_page"
              @click="changePage(pagination.current_page + 1)"
            >
              Sau →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick View Modal -->
    <ProductQuickView 
      :is-open="quickViewOpen" 
      :product="selectedProduct" 
      @close="quickViewOpen = false"
      @add-to-cart="handleQuickViewAddToCart"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/api'
import ProductFilter from '@/components/ProductFilter.vue'
import ProductCard from '@/components/ProductCard.vue'
import ProductQuickView from '@/components/ProductQuickView.vue'

// State chính
const filters = ref({
  category_ids: [] as number[],
  min_price: null as number | null,
  max_price: null as number | null
})
const products = ref<any[]>([])
const loading = ref(true)
const sortBy = ref('discount_desc')  // Default sort by discount
const currentPage = ref(1)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0,
  from: 1,
  to: 12
})

// Search state
const searchQuery = ref('')

// Wishlist state
const wishlistProductIds = ref<Set<number | string>>(new Set())
const wishlistLoading = ref(false)

// Fetch danh sách sản phẩm GIẢM GIÁ
const fetchProducts = async () => {
  loading.value = true
  try {
    const params: any = {
      page: currentPage.value,
      per_page: 999,  // Load all for client filter
      sort: sortBy.value,
      filter: 'sale'
    }

    // Áp dụng filter từ sidebar
    if (filters.value.category_ids && filters.value.category_ids.length) {
      params.category_id = filters.value.category_ids.join(',')
    }
    if (filters.value.min_price !== null && filters.value.min_price !== undefined) {
      params.min_price = filters.value.min_price
    }
    if (filters.value.max_price !== null && filters.value.max_price !== undefined) {
      params.max_price = filters.value.max_price
    }

    // Áp dụng search
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim()
    }

    const res = await api.get('/products', { params })

    // Backend đã filter sale, nhưng double-check client-side
    products.value = (res.data.data || []).filter((p: any) => 
      p.discount_price && p.discount_price > 0 && p.discount_price < p.base_price
    )

    // Pagination cho filtered results (simple client-side for now)
    const pageSize = 12
    const start = (currentPage.value - 1) * pageSize
    const end = start + pageSize
    const paginatedProducts = products.value.slice(start, end)
    
    pagination.value = {
      current_page: currentPage.value,
      last_page: Math.ceil(products.value.length / pageSize),
      per_page: pageSize,
      total: products.value.length,
      from: start + 1,
      to: Math.min(end, products.value.length)
    }

    // Update products to paginated slice
    products.value = paginatedProducts
  } catch (err) {
    console.error('Lỗi tải sản phẩm giảm giá:', err)
  } finally {
    loading.value = false
  }
}

// Các hàm còn lại giống Products.vue (copy từ file gốc)
const fetchWishlist = async () => {
  const token = localStorage.getItem('token')
  if (!token) return
  
  wishlistLoading.value = true
  try {
    const res = await api.get('/wishlist')
    const wishlistItems = res.data.data || []
    wishlistProductIds.value = new Set(wishlistItems.map((item: any) => item.product_id))
  } catch (err) {
    console.error('Lỗi tải wishlist:', err)
  } finally {
    wishlistLoading.value = false
  }
}

const toggleWishlistFromCard = async (product: any) => {
  const token = localStorage.getItem('token')
  if (!token) {
    return alert('Vui lòng đăng nhập để sử dụng tính năng yêu thích!')
  }

  try {
    const productId = product.product_id
    if (wishlistProductIds.value.has(productId)) {
      await api.delete(`/wishlist/remove/${productId}`)
      wishlistProductIds.value.delete(productId)
      alert('Đã xóa khỏi danh sách yêu thích')
    } else {
      await api.post(`/wishlist/add/${productId}`)
      wishlistProductIds.value.add(productId)
      alert('Đã thêm vào danh sách yêu thích')
    }
  } catch (err: any) {
    console.error('Lỗi wishlist:', err)
    const message = err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại!'
    alert(message)
  }
}

const handleSearch = () => {
  currentPage.value = 1
  fetchProducts()
}

const handleSortChange = () => {
  currentPage.value = 1
  fetchProducts()
}

const quickViewOpen = ref(false)
const selectedProduct = ref<any>(null)

const visiblePages = computed(() => {
  const pages = []
  const maxPages = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2))
  let end = Math.min(pagination.value.last_page, start + maxPages - 1)

  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const changePage = (page: number) => {
  if (page < 1 || page > pagination.value.last_page) return
  currentPage.value = page
  fetchProducts()
}

watch([sortBy, currentPage], () => {
  fetchProducts()
})

const applyFilters = (newFilters: any) => {
  filters.value = newFilters
  currentPage.value = 1
  fetchProducts()
}

const clearFilters = () => {
  filters.value = {
    category_ids: [],
    min_price: null,
    max_price: null
  }
  currentPage.value = 1
  fetchProducts()
}

const addToCart = async (product: any) => {
  if (!localStorage.getItem('token')) {
    return alert('Vui lòng đăng nhập để thêm vào giỏ hàng!')
  }

  try {
    const variantId = product.variants?.[0]?.variant_id
    if (!variantId) return alert('Sản phẩm này chưa có biến thể')

    await api.post('/cart/add', {
      variant_id: variantId,
      quantity: 1
    })
    alert('Đã thêm vào giỏ hàng!')
  } catch (err) {
    console.error('Lỗi thêm giỏ hàng:', err)
    alert('Không thể thêm sản phẩm. Vui lòng thử lại!')
  }
}

const quickView = (product: any) => {
  selectedProduct.value = product
  quickViewOpen.value = true
}

const handleQuickViewAddToCart = (data: any) => {
  addToCart(data)
}

onMounted(() => {
  fetchProducts()
  fetchWishlist()
})
</script>

<style scoped>
/* Same styles as Products.vue */
.products-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.promo-badge {
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 25px;
  font-size: 0.875rem;
  font-weight: 500;
}

.page-subtitle {
  color: #64748b;
  margin-top: 0.5rem;
  font-size: 1.1rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-top: 40px;
}

.page-btn {
  padding: 8px 16px;
  border: 1px solid #cbd5e1;
  background: white;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: #f1f5f9;
}

.page-btn.active {
  background: #0f172a;
  color: white;
  border-color: #0f172a;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-dots {
  color: #64748b;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 60px 0;
  font-size: 1.2rem;
  color: #64748b;
}

.search-box {
  display: flex;
  align-items: center;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  transition: border-color 0.3s;
}

.search-box:focus-within {
  border-color: #ff6b35;
}

.search-input {
  border: none;
  outline: none;
  padding: 10px 14px;
  font-size: 14px;
  width: 200px;
  background: transparent;
}

.search-input::placeholder {
  color: #94a3b8;
}

.search-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px 14px;
  background: #ff6b35;
  border: none;
  cursor: pointer;
  color: white;
  transition: background 0.3s;
}

.search-btn:hover {
  background: #e55a2b;
}
</style>

