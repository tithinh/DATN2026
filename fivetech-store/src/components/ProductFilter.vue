<template>
  <aside class="filter-sidebar">
    <div class="filter-header">
      <h3 class="filter-title">Bộ lọc</h3>
      <button class="clear-filter" @click="clearAll">Xóa tất cả</button>
    </div>

    <!-- Loading khi fetch -->
    <div v-if="loading" class="filter-loading">Đang tải bộ lọc...</div>

    <!-- Category Filter -->
    <div class="filter-group" v-else>
      <h4 class="filter-group-title">Danh mục</h4>
      <ul class="filter-list">
        <li v-for="cat in categories" :key="cat.category_id" class="filter-item">
          <label class="filter-label">
            <input 
              type="checkbox" 
              class="filter-checkbox" 
              :value="cat.category_id" 
              v-model="selectedCategories" 
              @change="emitFilter"
            />
            <span class="checkmark"></span>
            <span class="label-text">{{ cat.name }}</span>
            <span class="count">({{ cat.products_count || cat.product_count || 0 }})</span>
          </label>
        </li>
      </ul>
    </div>

    <!-- Price Filter -->
    <div class="filter-group" v-if="!loading">
      <h4 class="filter-group-title">Khoảng giá</h4>
      <div class="price-range">
        <input 
          type="range" 
          :min="priceRange.min" 
          :max="priceRange.max" 
          v-model="maxPrice" 
          class="range-slider" 
          @input="emitFilter"
        />
        <div class="price-labels">
          <span>{{ formatPrice(priceRange.min) }}</span>
          <span>{{ formatPrice(maxPrice) }}</span>
        </div>
      </div>
      <div class="price-inputs">
        <input 
          type="number" 
          placeholder="Từ" 
          class="price-input" 
          v-model.number="minPrice" 
          :min="priceRange.min" 
          @input="emitFilter"
        />
        <span class="price-separator">-</span>
        <input 
          type="number" 
          placeholder="Đến" 
          class="price-input" 
          v-model.number="maxPrice" 
          :max="priceRange.max" 
          @input="emitFilter"
        />
      </div>
    </div>

    <!-- Brand Filter (tạm hard-code, có thể fetch sau) -->
    <div class="filter-group" v-if="!loading">
      <h4 class="filter-group-title">Thương hiệu</h4>
      <ul class="filter-list">
        <li v-for="brand in brands" :key="brand" class="filter-item">
          <label class="filter-label">
            <input 
              type="checkbox" 
              class="filter-checkbox" 
              :value="brand" 
              v-model="selectedBrands" 
              @change="emitFilter"
            />
            <span class="checkmark"></span>
            <span class="label-text">{{ brand }}</span>
          </label>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import api from '@/api'

// Emits
const emit = defineEmits(['update:filter', 'clear-all'])

// State
const loading = ref(true)
const categories = ref<any[]>([])
const brands = ref<string[]>(['Apple', 'Samsung', 'Baseus', 'Anker', 'Xiaomi', 'Joyroom']) // tạm hard-code, có thể fetch sau
const selectedCategories = ref<number[]>([])
const selectedBrands = ref<string[]>([])
const minPrice = ref<number | null>(null)
const maxPrice = ref<number>(5000000)

// Khoảng giá động
const priceRange = ref({
  min: 0,
  max: 10000000
})

// Fetch data từ API khi mount
onMounted(async () => {
  try {
    loading.value = true

    // 1. Lấy danh mục + số lượng sản phẩm
    const resCat = await api.get('/categories')
    categories.value = resCat.data.map((cat: any) => ({
      ...cat,
      product_count: cat.products_count || cat.count || cat.product_count || 0
    }))

    // 2. Lấy khoảng giá min/max (nếu backend có endpoint)
    // Nếu chưa có, dùng giá trị mặc định hoặc tính từ API products
    // Ví dụ endpoint giả định:
    // const resPrice = await api.get('/products/price-range')
    // priceRange.value = resPrice.data
    // maxPrice.value = priceRange.value.max

    // 3. Nếu muốn fetch brands động:
    // const resProducts = await api.get('/products?per_page=100')
    // const brandSet = new Set(resProducts.data.data.map((p: any) => p.brand).filter(Boolean))
    // brands.value = Array.from(brandSet)

  } catch (err) {
    console.error('Lỗi tải dữ liệu bộ lọc:', err)
  } finally {
    loading.value = false
  }
})

// Emit filter lên parent (Products.vue)
const emitFilter = () => {
  emit('update:filter', {
    category_ids: selectedCategories.value,
    brands: selectedBrands.value,
    min_price: minPrice.value,
    max_price: maxPrice.value > priceRange.value.max ? null : maxPrice.value
  })
}

// Watch thay đổi để emit ngay
watch([selectedCategories, selectedBrands, minPrice, maxPrice], () => {
  emitFilter()
})

// Clear all filters
const clearAll = () => {
  selectedCategories.value = []
  selectedBrands.value = []
  minPrice.value = null
  maxPrice.value = priceRange.value.max
  emit('clear-all')
  emitFilter()
}

// Format giá
const formatPrice = (price: number) => {
  if (price === null || price === undefined) return '0đ'
  return new Intl.NumberFormat('vi-VN').format(Math.round(price)) + 'đ'
}
</script>


<style scoped>
/* Giữ nguyên toàn bộ style của bạn */
.filter-loading {
  padding: 20px;
  text-align: center;
  color: #64748b;
  font-style: italic;
}


.filter-sidebar {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  height: fit-content;
  position: sticky;
  top: 100px;
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.filter-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
  color: #0f172a;
}

.clear-filter {
  background: none;
  border: none;
  color: #ff6b35;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
}

.filter-group {
  margin-bottom: 28px;
}

.filter-group-title {
  font-size: 15px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 16px 0;
}

.filter-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.filter-item {
  margin-bottom: 12px;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #475569;
  transition: color 0.3s;
}

.filter-label:hover { color: #0f172a; }

.filter-checkbox {
  width: 18px;
  height: 18px;
  accent-color: #ff6b35;
  cursor: pointer;
}

.label-text { flex: 1; }

.count {
  font-size: 12px;
  color: #94a3b8;
}

/* Price Range */
.price-range {
  margin-bottom: 16px;
}

.range-slider {
  width: 100%;
  accent-color: #ff6b35;
  cursor: pointer;
}

.price-labels {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #94a3b8;
  margin-top: 8px;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: 8px;
}

.price-input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s;
  width: 100%; /* Fix width */
}

.price-input:focus { border-color: #ff6b35; }

.price-separator {
  color: #94a3b8;
}
</style>
