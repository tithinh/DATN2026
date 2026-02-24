<template>
  <div class="products-page">
    <!-- Page Header / Breadcrumb -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <span class="current">Sản phẩm</span>
        </nav>
        <h1 class="page-title">Tất cả sản phẩm</h1>
        <p class="page-subtitle">Khám phá hơn {{ totalProducts }} phụ kiện điện thoại chính hãng</p>
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
            <div class="results-count">
              Hiển thị <strong>{{ products.length }}</strong> / {{ totalProducts }} sản phẩm
            </div>
            <div class="toolbar-actions">
              <select class="sort-select" v-model="sortBy" @change="handleSortChange">
                <option value="newest">Mới nhất</option>
                <option value="price_asc">Giá thấp đến cao</option>
                <option value="price_desc">Giá cao đến thấp</option>
                <option value="bestseller">Bán chạy nhất</option>
                <option value="rating">Đánh giá cao</option>
              </select>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <p>Đang tải sản phẩm...</p>
          </div>

          <!-- Products Grid -->
          <div v-else-if="products.length" class="products-grid">
            <ProductCard 
              v-for="product in products" 
              :key="product.product_id" 
              :product="product" 
              @add-to-cart="addToCart"
              @quick-view="quickView"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="empty-state">
            Không tìm thấy sản phẩm nào phù hợp với bộ lọc hiện tại.
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
const sortBy = ref('newest') // mặc định
const currentPage = ref(1)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 12,
  total: 0,
  from: 1,
  to: 12
})

// Tổng số sản phẩm (từ API)
const totalProducts = computed(() => pagination.value.total)

// Fetch danh sách sản phẩm
const fetchProducts = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: 12,
      sort: sortBy.value, // gửi param sort lên backend
    }

    const res = await api.get('/products', { params })

    products.value = res.data.data || []
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
      total: res.data.total,
      from: res.data.from,
      to: res.data.to
    }
  } catch (err) {
    console.error('Lỗi tải sản phẩm:', err)
  } finally {
    loading.value = false
  }
}

// Khi thay đổi sort → reset trang 1 và fetch lại
const handleSortChange = () => {
  currentPage.value = 1
  fetchProducts()
}

// Quick View
const quickViewOpen = ref(false)
const selectedProduct = ref<any>(null)

// Computed pagination hiển thị
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

// const totalProducts = computed(() => pagination.value.total)

// // Fetch products
// const fetchProducts = async () => {
//   loading.value = true
//   try {
//     const params: any = {
//       page: currentPage.value,
//       per_page: pagination.value.per_page,
//       sort: sortBy.value
//     }

//     // Áp dụng filter từ sidebar
//     if (filters.value.category_ids.length) {
//       params.category_id = filters.value.category_ids.join(',')
//     }
//     if (filters.value.min_price !== null) {
//       params.min_price = filters.value.min_price
//     }
//     if (filters.value.max_price !== null) {
//       params.max_price = filters.value.max_price
//     }

//     const res = await api.get('/products', { params })

//     products.value = res.data.data || []
//     pagination.value = {
//       current_page: res.data.current_page || 1,
//       last_page: res.data.last_page || 1,
//       per_page: res.data.per_page || 12,
//       total: res.data.total || 0,
//       from: res.data.from || 1,
//       to: res.data.to || 12
//     }
//   } catch (err) {
//     console.error('Lỗi tải danh sách sản phẩm:', err)
//   } finally {
//     loading.value = false
//   }
// }

// Change page
const changePage = (page: number) => {
  if (page < 1 || page > pagination.value.last_page) return
  currentPage.value = page
  fetchProducts()
}

// Watch sort & page change
watch([sortBy, currentPage], () => {
  fetchProducts()
})

// Nhận filter từ ProductFilter
const applyFilters = (newFilters: any) => {
  filters.value = newFilters
  currentPage.value = 1 // reset về trang 1 khi filter thay đổi
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

// Cart & Quick View
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

// Load ban đầu
onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
/* Giữ nguyên style của bạn, chỉ thêm nếu cần cho pagination */
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

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 12px;
  margin-top: 40px;
}
</style>