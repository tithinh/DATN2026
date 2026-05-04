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
                  <button class="action-btn delete" @click="deleteProduct(p)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
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
          <div class="admin-form-group variant-section">
            <div class="variant-header">
              <label style="margin: 0; font-weight: 600;">Biến thể sản phẩm <span style="font-weight:400; color: var(--admin-text-muted);">({{ form.variants.length }})</span></label>
              <button class="admin-btn admin-btn-sm admin-btn-outline" @click="openAddVariantForm">
                + Thêm biến thể
              </button>
            </div>

            <!-- Add/Edit Variant Form -->
            <div v-if="showAddVariant" class="variant-form-box">
              <div class="variant-form-title">{{ editingVariantIdx !== null ? 'Chỉnh sửa biến thể' : 'Thêm biến thể mới' }}</div>
              <div class="variant-form-grid">
                <!-- Màu sắc -->
                <div class="admin-form-group">
                  <label>Màu sắc <span class="required">*</span></label>
                  <div style="display: flex; gap: 8px; align-items: center;">
                    <input class="admin-input" v-model="newVariant.color" placeholder="Tên màu (vd: Đen, Trắng)" style="flex:1" />
                    <input type="color" v-model="newVariant.color_hex" style="width:40px; height:38px; border:1px solid var(--admin-border); border-radius:6px; cursor:pointer; padding:2px;" title="Chọn mã màu hiển thị" />
                  </div>
                </div>
                <!-- Giá biến thể -->
                <div class="admin-form-group">
                  <label>Giá biến thể (VNĐ) <span class="required" >*</span></label>
                  <input class="admin-input" type="number" v-model="newVariant.price_extra" placeholder="Nhập giá cộng thêm (VNĐ)" min="0" />
                </div>
                <!-- Số lượng -->
                <div class="admin-form-group">
                  <label>Số lượng tồn kho</label>
                  <input class="admin-input" type="number" v-model="newVariant.stock" placeholder="0" min="0" />
                </div>
                <!-- SKU (optional) -->
                <div class="admin-form-group">
                  <label>SKU <span style="font-size:11px; color:var(--admin-text-muted);">(tuỳ chọn)</span></label>
                  <input class="admin-input" v-model="newVariant.sku" placeholder="Mã SKU biến thể" />
                </div>
              </div>

              <!-- Upload ảnh biến thể -->
              <div class="admin-form-group" style="margin-top: 4px;">
                <label>Ảnh biến thể</label>
                <div style="display: flex; gap: 12px; align-items: flex-start;">
                  <div class="variant-img-upload" @click="triggerVariantUpload">
                    <div v-if="newVariant.image_preview" class="variant-img-preview">
                      <img :src="newVariant.image_preview" />
                      <span class="change-img-label">Đổi ảnh</span>
                    </div>
                    <div v-else class="variant-img-placeholder">
                      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                      <span>Upload ảnh</span>
                    </div>
                  </div>
                  <div v-if="newVariant.image_preview" style="font-size: 12px; color: var(--admin-text-muted); margin-top: 4px;">Ảnh đã chọn. Click để thay đổi.</div>
                </div>
                <input type="file" id="variantFileInput" ref="variantFileInput" style="display:none" accept="image/*" @change="handleVariantImageUpload" />
              </div>

              <div style="display: flex; gap: 8px; margin-top: 12px;">
                <button class="admin-btn admin-btn-primary admin-btn-sm" style="flex:1; justify-content: center;" @click="saveVariant">
                  {{ editingVariantIdx !== null ? 'Cập nhật biến thể' : 'Thêm biến thể' }}
                </button>
                <button class="admin-btn admin-btn-outline admin-btn-sm" @click="cancelVariantForm">Huỷ</button>
              </div>
            </div>

            <!-- Variants List -->
            <div v-if="form.variants.length > 0" class="variant-list">
              <div v-for="(v, idx) in form.variants" :key="idx" class="variant-item" :class="{ editing: editingVariantIdx === idx }">
                <!-- Ảnh variant -->
                <div class="variant-item-img">
                  <img v-if="v.image_preview || getVariantImageUrl(v)" :src="v.image_preview || getVariantImageUrl(v)" @error="$event.target.style.display='none'" />
                  <div v-else class="variant-no-img">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                  </div>
                </div>
                <!-- Info -->
                <div class="variant-item-info">
                  <div class="variant-item-name">
                    <span v-if="v.color_hex" :style="{ display: 'inline-block', width: '12px', height: '12px', borderRadius: '50%', background: v.color_hex, border: '1px solid #ccc', marginRight: '6px', verticalAlign: 'middle' }"></span>
                    {{ v.color || 'Biến thể ' + (idx+1) }}
                    <span v-if="v.storage_size" style="color: var(--admin-text-muted);"> / {{ v.storage_size }}</span>
                  </div>
                  <div class="variant-item-details">
<span class="variant-price-tag">{{ formatPrice(v.price_extra || 0) }}</span>
                    <span class="variant-stock-tag">Kho: {{ v.stock || 0 }}</span>
                    <span v-if="v.sku" class="variant-sku-tag">SKU: {{ v.sku }}</span>
                  </div>
                </div>
                <!-- Actions -->
                <div class="variant-item-actions">
                  <button class="action-btn edit" @click="editVariant(idx)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                  </button>
                  <button class="action-btn delete" @click="removeVariant(idx)" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                  </button>
                </div>
              </div>
            </div>
            <div v-else style="text-align: center; padding: 24px; font-size: 13px; color: var(--admin-text-muted); border: 1px dashed var(--admin-border); border-radius: 8px;">
              Chưa có biến thể nào. Nhấn "+ Thêm biến thể" để thêm.
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
const editingVariantIdx = ref(null)
const newVariant = ref({ sku: '', color: '', color_hex: '#000000', storage_size: '', price: 0, price_extra: 0, stock: 0, image: null, image_preview: null })

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

// Fetch categories (admin)
const fetchCategories = async () => {
  try {
    const res = await api.get('/admin/categories')
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

// Fetch products (admin)
const fetchProducts = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value.trim(),
      category_id: filterCategory.value,
      per_page: itemsPerPage,
      page: currentPage.value
    }
    const res = await api.get('/admin/products', { params })
    products.value = res.data.data || []
    totalItems.value = res.data.total || 0
    // Sync with backend pagination
    if (res.data.current_page) currentPage.value = res.data.current_page
  } catch (err) {
    console.error('Lỗi tải sản phẩm:', err)
  } finally {
    loading.value = false
  }
}

// Computed pagination
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value))

const paginatedProducts = computed(() => products.value)

const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage))
const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let startPage = Math.max(1, currentPage.value - 2)
  let endPage = Math.min(totalPages.value, startPage + maxVisible - 1)
  if (endPage - startPage + 1 < maxVisible) startPage = Math.max(1, endPage - maxVisible + 1)
  for (let i = startPage; i <= endPage; i++) pages.push(i)
  return pages
})

const generateSlug = (text) => {
  return text.toString().toLowerCase()
    .replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a')
    .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e')
    .replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i')
    .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o')
    .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u')
    .replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y')
    .replace(/đ/gi, 'd')
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '')
}

watch(() => form.value.name, (newName) => {
  if (!isEditing.value) {
    form.value.slug = generateSlug(newName || '')
  }
})

watch([searchQuery, filterCategory], () => {
  currentPage.value = 1
  fetchProducts()
})
watch(currentPage, () => {
  fetchProducts()
})

onMounted(() => {
  fetchCategories()
  fetchProducts()
})

// Format price & date
const formatPrice = (price) => price ? new Intl.NumberFormat('vi-VN').format(price) + '₫' : '0₫'
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
    variants: (p.variants || []).map(v => ({
      ...v,
      price: null,
      price_extra: v.price_extra || 0,
      color_hex: v.color_hex || '#000000',
      image: null,
      image_preview: getVariantImageUrl(v) || null,
    }))
  }
  showModal.value = true
  showAddVariant.value = false
  editingVariantIdx.value = null
}

const closeModal = () => {
  showModal.value = false
}

const deleteProduct = async (product) => {
  if (!confirm(`Bạn có chắc muốn xóa sản phẩm "${product.name}"?`)) return
  try {
    await api.delete(`/admin/products/${product.product_id}`)
    currentPage.value = 1
    await fetchProducts()
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi xóa sản phẩm')
  }
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

// Open variant add form
const openAddVariantForm = () => {
  editingVariantIdx.value = null
  newVariant.value = { sku: '', color: '', color_hex: '#000000', storage_size: '', price: 0, price_extra: 0, stock: 0, image: null, image_preview: null }
  showAddVariant.value = true
}

// Edit existing variant
const editVariant = (idx) => {
  editingVariantIdx.value = idx
  const v = form.value.variants[idx]
  newVariant.value = {
    sku: v.sku || '',
    color: v.color || '',
    color_hex: v.color_hex || '#000000',
    storage_size: v.storage_size || '',
    price: null,
    price_extra: v.price_extra || 0,
    stock: v.stock || 0,
    image: null,
    image_preview: v.image_preview || null,
    // Giữ lại image_urls cũ nếu không upload ảnh mới
    existing_image_urls: v.image_urls || null,
    variant_id: v.variant_id || null,
  }
  showAddVariant.value = true
}

// Save variant (add or update)
const saveVariant = () => {
  if (!newVariant.value.color) return alert('Vui lòng nhập màu sắc')
  // if (!newVariant.value.price_extra || newVariant.value.price_extra <= 0) return alert('Vui lòng nhập giá biến thể')

  const variantData = { ...newVariant.value }

  if (editingVariantIdx.value !== null) {
    // Cập nhật variant đã có
    const existing = form.value.variants[editingVariantIdx.value]
    form.value.variants[editingVariantIdx.value] = {
      ...existing,
      ...variantData,
      // Giữ image_urls cũ nếu không upload ảnh mới
      image_urls: variantData.image ? null : existing.image_urls,
    }
  } else {
    form.value.variants.push(variantData)
  }

  cancelVariantForm()
}

const cancelVariantForm = () => {
  showAddVariant.value = false
  editingVariantIdx.value = null
  newVariant.value = { sku: '', color: '', color_hex: '#000000', storage_size: '', price: 0, price_extra: 0, stock: 0, image: null, image_preview: null }
}

// Add variant (legacy - kept for compatibility)
const addVariant = () => saveVariant()

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
}

// Submit form (thêm/sửa sản phẩm với ảnh)
const submitForm = async () => {
  try {

    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('slug', form.value.slug || generateSlug(form.value.name))
    formData.append('category_id', form.value.category_id)
    formData.append('base_price', form.value.base_price)
    // Gửi rỗng nếu không có discount_price để backend lưu NULL (tránh lưu 0)
    if (form.value.discount_price && Number(form.value.discount_price) > 0) {
      formData.append('discount_price', form.value.discount_price)
    }
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
      if (v.variant_id) {
        formData.append(`variants[${index}][variant_id]`, v.variant_id)
      }
      formData.append(`variants[${index}][sku]`, v.sku || '')
      formData.append(`variants[${index}][color]`, v.color || '')
      formData.append(`variants[${index}][color_hex]`, v.color_hex || '')
      formData.append(`variants[${index}][storage_size]`, v.storage_size || '')
formData.append(`variants[${index}][price]`, '')
formData.append(`variants[${index}][price_extra]`, v.price_extra || 0)
      formData.append(`variants[${index}][stock]`, v.stock || 0)

      if (v.image) {
        formData.append(`variant_images[${index}]`, v.image)
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

    currentPage.value = 1
    await fetchProducts()
    closeModal()
  } catch (err) {
    console.error('Lỗi submit sản phẩm:', err)
    let errorMsg = err.response?.data?.message || 'Kiểm tra console'
    if (err.response?.status === 422 && err.response?.data?.errors) {
      errorMsg = Object.values(err.response.data.errors)[0][0]
    }
    alert('Có lỗi khi lưu sản phẩm: ' + errorMsg)
  }
}

// Toggle visibility (ẩn/hiện sản phẩm)
const toggleVisibility = async (p) => {
  try {
    const newStatus = !p.is_visible

    await api.put(`/admin/products/${p.product_id}/toggle-visibility`, { is_visible: newStatus })
    p.is_visible = newStatus
  } catch (err) {
    console.error('Lỗi cập nhật trạng thái:', err)
    alert('Có lỗi khi cập nhật trạng thái. Kiểm tra console.')
  }
}
import { storageUrl } from '@/utils/image'

// fallback chung
// const DEFAULT_IMAGE = '/images/default-product.jpg'
 // Removed: No default placeholder, only real main images

// Lấy thumbnail sản phẩm
const getProductThumbnail = (p) => {
  if (!p) return ''

  // ưu tiên thumbnail
  if (p.thumbnail) return storageUrl(p.thumbnail)

  // fallback qua variant đầu tiên
  const firstVariant = p.variants?.[0]
  const variantImage = getVariantImageUrl(firstVariant)

  return variantImage || ''
}

// xử lý lỗi ảnh
const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Lấy ảnh đầu tiên của variant
const getVariantImageUrl = (v) => {
  if (!v?.image_urls) return null

  let urls = v.image_urls

  // nếu là string thì parse JSON
  if (typeof urls === 'string') {
    try {
      urls = JSON.parse(urls)
    } catch (err) {
      console.warn('image_urls parse lỗi:', err)
      return null
    }
  }

  // nếu là array hợp lệ
  if (Array.isArray(urls) && urls.length > 0) {
    return storageUrl(urls[0])
  }

  return null
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

/* Variant Section */
.variant-section {
  margin-top: 20px;
  border-top: 1px solid var(--admin-border);
  padding-top: 20px;
}
.variant-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.variant-form-box {
  background: var(--admin-bg);
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  border: 1px dashed var(--admin-primary);
}
.variant-form-title {
  font-weight: 600;
  font-size: 13px;
  margin-bottom: 12px;
  color: var(--admin-primary);
}
.variant-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* Variant image upload */
.variant-img-upload {
  width: 80px;
  height: 80px;
  border: 2px dashed var(--admin-border);
  border-radius: 8px;
  cursor: pointer;
  overflow: hidden;
  flex-shrink: 0;
  position: relative;
  transition: border-color 0.2s;
}
.variant-img-upload:hover { border-color: var(--admin-primary); }
.variant-img-preview {
  width: 100%;
  height: 100%;
  position: relative;
}
.variant-img-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.change-img-label {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.45);
  color: white;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}
.variant-img-upload:hover .change-img-label { opacity: 1; }
.variant-img-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  color: var(--admin-text-muted);
  font-size: 10px;
}

/* Variant list */
.variant-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.variant-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--admin-bg-card);
  padding: 10px 12px;
  border: 1px solid var(--admin-border);
  border-radius: 8px;
  transition: border-color 0.2s;
}
.variant-item.editing {
  border-color: var(--admin-primary);
  background: color-mix(in srgb, var(--admin-primary) 5%, transparent);
}
.variant-item-img {
  width: 52px;
  height: 52px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: var(--admin-bg);
  display: flex;
  align-items: center;
  justify-content: center;
}
.variant-item-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.variant-no-img {
  color: var(--admin-text-muted);
}
.variant-item-info {
  flex: 1;
  min-width: 0;
}
.variant-item-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--admin-text);
  margin-bottom: 4px;
}
.variant-item-details {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.variant-price-tag {
  font-size: 12px;
  font-weight: 700;
  color: var(--admin-warning);
}
.variant-stock-tag {
  font-size: 11px;
  color: var(--admin-text-muted);
  background: var(--admin-bg);
  padding: 1px 6px;
  border-radius: 4px;
}
.variant-sku-tag {
  font-size: 11px;
  color: var(--admin-text-muted);
  font-family: monospace;
}
.variant-item-actions {
  display: flex;
  gap: 4px;
  flex-shrink: 0;
}
</style>