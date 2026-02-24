<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý sản phẩm</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm kiếm sản phẩm..." @input="debouncedFetchProducts" />
        </div>
        <select class="toolbar-filter" v-model="filterCategory" @change="fetchProducts">
          <option value="">Tất cả danh mục</option>
          <option v-for="cat in categories" :key="cat.category_id" :value="cat.category_id">{{ cat.name }}</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm sản phẩm
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <p>Đang tải danh sách sản phẩm...</p>
    </div>

    <!-- Table -->
    <div v-else class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Hình ảnh</th>
              <th>Tên sản phẩm</th>
              <th>Giá</th>
              <th>Danh mục</th>
              <th>Số lượng</th>
              <th>Trạng thái</th>
              <th>Ngày tạo</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in paginatedProducts" :key="p.product_id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ p.product_id }}</td>
              <td>
                <img
                  :src="getProductThumbnail(p)"
                  :alt="p.name"
                  class="table-product-img"
                  @error="handleImageError"
                />
              </td>
              <td style="font-weight:500; color: var(--admin-text); max-width: 200px;">{{ p.name }}</td>
              <td style="font-weight:600; color: var(--admin-warning);">{{ formatPrice(p.final_price) }}</td>
              <td>{{ p.category?.name || 'Chưa phân loại' }}</td>
              <td>
                <span :style="{ color: p.stock_total < 10 ? 'var(--admin-danger)' : 'var(--admin-text-soft)' }">
                  {{ p.stock_total }}
                </span>
              </td>
              <td>
                <div class="admin-toggle" @click="toggleVisibility(p)">
                  <button class="toggle-switch" :class="{ active: p.is_visible }" type="button"></button>
                  <span style="font-size: 14px; color: var(--admin-text-soft);">{{ p.is_visible ? 'Hiển thị' : 'Ẩn' }}</span>
                </div>
              </td>
              <td>{{ formatDate(p.created_at) }}</td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="openEditModal(p)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="admin-pagination">
        <span class="pagination-info">Hiển thị {{ startItem }}-{{ endItem }} / {{ totalItems }} sản phẩm</span>
        <div class="pagination-btns">
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage = 1">Đầu</button>
          <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">Trước</button>
          <button class="page-btn" v-for="page in visiblePages" :key="page" :class="{ active: page === currentPage }" @click="currentPage = page">
            {{ page }}
          </button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">Sau</button>
          <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage = totalPages">Cuối</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="admin-modal-overlay" v-if="showModal" @click.self="closeModal">
      <div class="admin-modal wide slide-up">
        <div class="admin-modal-header">
          <h3>{{ isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' }}</h3>
          <button class="modal-close" @click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>
        <div class="admin-modal-body">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label>Tên sản phẩm <span class="required">*</span></label>
              <input class="admin-input" v-model="form.name" placeholder="Nhập tên sản phẩm" />
            </div>
            <div class="admin-form-group">
              <label>Danh mục <span class="required">*</span></label>
              <select class="admin-select" v-model="form.category_id">
                <option value="">Chọn danh mục</option>
                <option v-for="cat in categories" :key="cat.category_id" :value="cat.category_id">{{ cat.name }}</option>
              </select>
            </div>
            <div class="admin-form-group">
              <label>Giá gốc (VNĐ) <span class="required">*</span></label>
              <input class="admin-input" type="number" v-model="form.base_price" placeholder="0" />
            </div>
            <div class="admin-form-group">
              <label>Giá giảm (VNĐ)</label>
              <input class="admin-input" type="number" v-model="form.discount_price" placeholder="0" />
            </div>
            <div class="admin-form-group">
              <label>Số lượng tổng <span class="required">*</span></label>
              <input class="admin-input" type="number" v-model="form.stock_total" placeholder="0" />
            </div>
            <div class="admin-form-group">
              <label>Slug (URL)</label>
              <input class="admin-input" v-model="form.slug" placeholder="cap-sac-lightning-anker-powerline" />
            </div>
          </div>

          <div class="admin-form-group">
            <label>Mô tả ngắn</label>
            <textarea class="admin-textarea" v-model="form.short_desc" placeholder="Mô tả ngắn gọn..." rows="2"></textarea>
          </div>
          <div class="admin-form-group">
            <label>Mô tả chi tiết</label>
            <textarea class="admin-textarea" v-model="form.description" placeholder="Mô tả đầy đủ..." rows="6"></textarea>
          </div>

          <!-- Upload ảnh đại diện sản phẩm -->
          <div class="admin-form-group">
            <label>Ảnh đại diện sản phẩm</label>
            <div class="upload-area" @click="triggerMainUpload">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
              <p>Kéo thả hoặc click để upload ảnh đại diện</p>
              <span class="upload-hint">PNG, JPG, WEBP (tối đa 5MB)</span>
            </div>
            <input type="file" id="mainFileInput" ref="mainFileInput" style="display:none" accept="image/*" @change="handleMainImageUpload" />
            <div v-if="form.main_image_preview" style="margin-top: 8px;">
              <img :src="form.main_image_preview" style="max-width: 150px; border-radius: 8px;" />
            </div>
          </div>

          <!-- Variants Section -->
          <div class="admin-form-group" style="margin-top: 20px; border-top: 1px solid var(--admin-border); padding-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
              <label style="margin: 0;">Biến thể sản phẩm ({{ form.variants.length }})</label>
              <button class="admin-btn admin-btn-sm admin-btn-outline" @click="showAddVariant = !showAddVariant">
                {{ showAddVariant ? 'Huỷ thêm' : '+ Thêm biến thể' }}
              </button>
            </div>

            <!-- Add Variant Form -->
            <div v-if="showAddVariant" style="background: var(--admin-bg); padding: 12px; border-radius: 8px; margin-bottom: 16px; border: 1px dashed var(--admin-primary);">
              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                <input class="admin-input" v-model="newVariant.sku" placeholder="SKU (Mã)" />
                <input class="admin-input" v-model="newVariant.color" placeholder="Màu sắc" />
                <input class="admin-input" v-model="newVariant.storage_size" placeholder="Kích thước" />
                <input class="admin-input" type="number" v-model="newVariant.price_extra" placeholder="Giá phụ thu" />
                <input class="admin-input" type="number" v-model="newVariant.stock" placeholder="Số lượng tồn" />
              </div>

              <!-- Upload ảnh cho biến thể -->
              <div class="admin-form-group" style="margin-top: 12px;">
                <label>Ảnh biến thể</label>
                <div class="upload-area small" @click="triggerVariantUpload">
                  <p>Click để upload ảnh biến thể</p>
                </div>
                <input type="file" id="variantFileInput" ref="variantFileInput" style="display:none" accept="image/*" @change="handleVariantImageUpload" />
                <div v-if="newVariant.image_preview" style="margin-top: 8px;">
                  <img :src="newVariant.image_preview" style="max-width: 100px; border-radius: 6px;" />
                </div>
              </div>

              <button class="admin-btn admin-btn-primary admin-btn-sm" style="width: 100%; justify-content: center; margin-top: 12px;" @click="addVariant">Lưu biến thể này</button>
            </div>

            <!-- Variants List -->
            <div v-if="form.variants.length > 0" style="display: flex; flex-direction: column; gap: 8px;">
              <div v-for="(v, idx) in form.variants" :key="idx" style="display: flex; align-items: center; justify-content: space-between; background: var(--admin-bg-card); padding: 8px 12px; border: 1px solid var(--admin-border); border-radius: 6px;">
                <div style="display: flex; gap: 12px; align-items: center;">
                  <div style="width: 24px; height: 24px; background: var(--admin-bg); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: var(--admin-text-muted);">{{ idx + 1 }}</div>
                  <div v-if="v.image_preview" style="width: 40px; height: 40px;">
                    <img :src="v.image_preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" />
                  </div>
                  <div>
                    <div style="font-size: 13px; font-weight: 600; color: var(--admin-text);">{{ v.sku || 'N/A' }} - {{ v.color || 'N/A' }} / {{ v.storage_size || 'N/A' }}</div>
                    <div style="font-size: 11px; color: var(--admin-text-muted);">Giá phụ: {{ v.price_extra ? formatPrice(v.price_extra) : 'Theo SP' }} | Kho: {{ v.stock }}</div>
                  </div>
                </div>
                <button class="action-btn delete" @click="removeVariant(idx)" style="color: var(--admin-danger);">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                </button>
              </div>
            </div>
            <div v-else style="text-align: center; padding: 20px; font-size: 12px; color: var(--admin-text-muted); border: 1px dashed var(--admin-border); border-radius: 8px;">
              Chưa có biến thể nào
            </div>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeModal">Huỷ</button>
          <button class="admin-btn admin-btn-primary" @click="submitForm">
            {{ isEditing ? 'Cập nhật' : 'Thêm sản phẩm' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const filterCategory = ref('')
const currentPage = ref(1)
const itemsPerPage = 8
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const showAddVariant = ref(false)
const newVariant = ref({ sku: '', color: '', storage_size: '', price_extra: 0, stock: 0, image: null, image_preview: null })

const form = ref({
  product_id: null,
  name: '',
  slug: '',
  category_id: '',
  base_price: 0,
  discount_price: null,
  stock_total: 0,
  short_desc: '',
  description: '',
  is_visible: true,
  main_image: null,
  main_image_preview: null,
  variants: []
})

const products = ref([])
const categories = ref([])
const totalItems = ref(0)

// Fetch categories (public)
const fetchCategories = async () => {
  try {
    const res = await api.get('/categories')
    categories.value = res.data.data || res.data || []
  } catch (err) {
    console.error('Lỗi tải danh mục:', err)
  }
}

// Debounce search
let debounceTimer
const debouncedFetchProducts = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchProducts, 500)
}

// Fetch products (public)
const fetchProducts = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      category_id: filterCategory.value,
      per_page: itemsPerPage,
      page: currentPage.value
    }
    const res = await api.get('/products', { params })
    products.value = res.data.data || res.data || []
    totalItems.value = res.data.total || products.value.length
  } catch (err) {
    console.error('Lỗi tải sản phẩm:', err)
  } finally {
    loading.value = false
  }
}

// Computed pagination
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value))

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return products.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage))
const visiblePages = computed(() => {
  const pages = []
  for (let i = 1; i <= totalPages.value; i++) pages.push(i)
  return pages
})

watch([searchQuery, filterCategory, currentPage], () => {
  fetchProducts()
})

onMounted(() => {
  fetchCategories()
  fetchProducts()
})

// Format price & date
const formatPrice = (price) => price ? price.toLocaleString('vi-VN') + '₫' : '0₫'
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN')

// Modal add/edit
const openAddModal = () => {
  isEditing.value = false
  form.value = {
    product_id: null,
    name: '',
    slug: '',
    category_id: '',
    base_price: 0,
    discount_price: null,
    stock_total: 0,
    short_desc: '',
    description: '',
    is_visible: true,
    main_image: null,
    main_image_preview: null,
    variants: []
  }
  showModal.value = true
  showAddVariant.value = false
}

const openEditModal = (p) => {
  isEditing.value = true
  form.value = {
    product_id: p.product_id,
    name: p.name,
    slug: p.slug,
    category_id: p.category_id,
    base_price: p.base_price,
    discount_price: p.discount_price,
    stock_total: p.stock_total,
    short_desc: p.short_desc,
    description: p.description,
    is_visible: p.is_visible,
    main_image: null,
    main_image_preview: null,
    variants: p.variants || []
  }
  showModal.value = true
  showAddVariant.value = false
}

const closeModal = () => {
  showModal.value = false
}

// Trigger upload main image
const triggerMainUpload = () => {
  document.getElementById('mainFileInput').click()
}

const handleMainImageUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.main_image = file
    form.value.main_image_preview = URL.createObjectURL(file)
  }
}

// Trigger upload variant image
const triggerVariantUpload = () => {
  document.getElementById('variantFileInput').click()
}

const handleVariantImageUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    newVariant.value.image = file
    newVariant.value.image_preview = URL.createObjectURL(file)
  }
}

// Add variant
const addVariant = () => {
  if (!newVariant.value.sku) return alert('Vui lòng nhập SKU')
  form.value.variants.push({ ...newVariant.value })
  newVariant.value = { sku: '', color: '', storage_size: '', price_extra: 0, stock: 0, image: null, image_preview: null }
  showAddVariant.value = false
}

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
}

// Submit form (thêm/sửa sản phẩm với ảnh)
const submitForm = async () => {
  try {
    console.log('Gọi submit sản phẩm với token admin:', localStorage.getItem('admin_token') ? 'Có' : 'Không có')
    console.log('URL submit:', api.defaults.baseURL + (isEditing.value ? `/admin/products/${form.value.product_id}` : '/admin/products'))

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('slug', form.value.slug)
    formData.append('category_id', form.value.category_id)
    formData.append('base_price', form.value.base_price)
    formData.append('discount_price', form.value.discount_price || 0)
    formData.append('stock_total', form.value.stock_total)
    formData.append('short_desc', form.value.short_desc)
    formData.append('description', form.value.description)
    formData.append('is_visible', form.value.is_visible ? 1 : 0)

    // Ảnh đại diện
    if (form.value.main_image) {
      formData.append('main_image', form.value.main_image)
    }

    // Biến thể
    form.value.variants.forEach((v, index) => {
      formData.append(`variants[${index}][sku]`, v.sku || '')
      formData.append(`variants[${index}][color]`, v.color || '')
      formData.append(`variants[${index}][storage_size]`, v.storage_size || '')
      formData.append(`variants[${index}][price_extra]`, v.price_extra || 0)
      formData.append(`variants[${index}][stock]`, v.stock || 0)

      if (v.image) {
        formData.append(`variants[${index}][image]`, v.image)
      }
    })

    let res
    if (isEditing.value) {
      formData.append('_method', 'PUT')
      res = await api.post(`/admin/products/${form.value.product_id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      res = await api.post('/admin/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    console.log('Submit thành công:', res.data)
    fetchProducts()
    closeModal()
  } catch (err) {
    console.error('Lỗi submit sản phẩm:', err)
    console.log('Response lỗi:', err.response?.data)
    console.log('Status:', err.response?.status)
    alert('Có lỗi khi lưu sản phẩm: ' + (err.response?.data?.message || 'Kiểm tra console'))
  }
}

// Toggle visibility (ẩn/hiện sản phẩm)
const toggleVisibility = async (p) => {
  try {
    const newStatus = !p.is_visible
    console.log('Gọi toggle visibility cho sản phẩm:', p.product_id)
    console.log('Token admin:', localStorage.getItem('admin_token') ? 'Có' : 'Không có')
    console.log('URL gọi:', api.defaults.baseURL + `/admin/products/${p.product_id}/toggle-visibility`)

    await api.put(`/admin/products/${p.product_id}/toggle-visibility`, { is_visible: newStatus })
    p.is_visible = newStatus
  } catch (err) {
    console.error('Lỗi cập nhật trạng thái:', err)
    console.log('Response lỗi:', err.response?.data)
    alert('Có lỗi khi cập nhật trạng thái. Kiểm tra console.')
  }
}

// Get thumbnail for table
const getProductThumbnail = (p) => {
  if (p.thumbnail) return p.thumbnail
  if (p.variants?.length && p.variants[0].image_urls?.length) {
    return `/storage/${p.variants[0].image_urls[0]}`
  }
  return '/images/default-product.jpg'
}

const handleImageError = (event) => {
  event.target.src = '/images/default-product.jpg'
}
</script>

<style scoped>
/* Giữ nguyên style cũ của bạn */
.table-product-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 8px;
  background: #f1f5f9;
}
.upload-area.small {
  padding: 8px;
  font-size: 12px;
}
</style>