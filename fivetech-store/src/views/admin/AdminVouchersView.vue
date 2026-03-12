<template>
  <div>
    <!-- Page Header -->
    <div class="page-header">
      <h1>Quản lý mã giảm giá</h1>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="toolbar-search">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <input type="text" v-model="searchQuery" placeholder="Tìm theo mã code..." />
        </div>
        <select class="toolbar-filter" v-model="filterStatus">
          <option value="">Tất cả trạng thái</option>
          <option value="active">Đang hoạt động</option>
          <option value="expired">Đã hết hạn</option>
          <option value="disabled">Đã vô hiệu hóa</option>
        </select>
      </div>
      <div class="toolbar-right">
        <button class="admin-btn admin-btn-primary" @click="openAddModal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
          Thêm voucher
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="admin-card">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Mã Code</th>
              <th>Giảm giá</th>
              <th>Đơn tối thiểu</th>
              <th>Đã dùng</th>
              <th>Hạn sử dụng</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="8" style="text-align:center; padding: 40px; color: var(--admin-text-muted);">Đang tải...</td>
            </tr>
            <tr v-else-if="filteredVouchers.length === 0">
              <td colspan="8" style="text-align:center; padding: 40px; color: var(--admin-text-muted);">Không có dữ liệu</td>
            </tr>
            <tr v-for="voucher in filteredVouchers" :key="voucher.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ voucher.id }}</td>
              <td>
                <span class="role-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 700; letter-spacing: 1px;">{{ voucher.code }}</span>
              </td>
              <td style="font-weight:600; color: var(--admin-text);">
                {{ formatDiscount(voucher) }}
              </td>
              <td>{{ formatCurrency(voucher.minOrder) }}</td>
              <td>{{ voucher.used }} / {{ voucher.limit === null || voucher.limit === -1 ? '∞' : voucher.limit }}</td>
              <td style="color: var(--admin-text-soft);">{{ voucher.endDate }}</td>
              <td>
                <span class="status-badge" :class="getVoucherStatusClass(voucher)" style="cursor:pointer;" @click="toggleVoucherStatus(voucher)" title="Click để thay đổi trạng thái">{{ getVoucherStatusText(voucher) }}</span>
              </td>
              <td>
                <div class="action-btns">
                  <button class="action-btn edit" @click="editVoucher(voucher)" title="Sửa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                  </button>
                  <button class="action-btn delete" @click="deleteVoucher(voucher.id)" title="Xóa">
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
        <div class="pagination-info">Tổng cộng {{ total }} mã giảm giá</div>
        <div class="pagination-btns">
          <button class="page-btn" @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button v-for="p in lastPage" :key="p" class="page-btn" :class="{ active: p === currentPage }" @click="goToPage(p)">{{ p }}</button>
          <button class="page-btn" @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Chỉnh sửa voucher' : 'Thêm voucher mới' }}</h3>
          <button class="close-modal" @click="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="admin-form-group">
            <label>Mã Voucher <span class="required">*</span></label>
            <input type="text" class="admin-input" v-model="currentVoucher.code" placeholder="VD: SALE50, TET2026" style="text-transform: uppercase;" />
          </div>
          
          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label>Loại giảm giá</label>
              <select class="admin-select" v-model="currentVoucher.type">
                <option value="percentage">Phần trăm (%)</option>
                <option value="fixed">Số tiền cố định (VNĐ)</option>
              </select>
            </div>
            <div class="admin-form-group">
              <label>Giá trị giảm <span class="required">*</span></label>
              <input type="number" class="admin-input" v-model="currentVoucher.value" placeholder="VD: 10 hoặc 50000" />
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label>Đơn tối thiểu</label>
              <input type="number" class="admin-input" v-model="currentVoucher.minOrder" placeholder="0 = Không giới hạn" />
            </div>
            <div class="admin-form-group">
              <label>Giảm tối đa</label>
              <input type="number" class="admin-input" v-model="currentVoucher.maxDiscount" :disabled="currentVoucher.type === 'fixed'" placeholder="Áp dụng cho %" />
            </div>
          </div>

          <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="admin-form-group">
              <label>Ngày bắt đầu</label>
              <input type="date" class="admin-input" v-model="currentVoucher.startDate" />
            </div>
            <div class="admin-form-group">
              <label>Ngày kết thúc</label>
              <input type="date" class="admin-input" v-model="currentVoucher.endDate" />
            </div>
          </div>

          <div class="admin-form-group">
            <label>Giới hạn sử dụng</label>
            <input type="number" class="admin-input" v-model="currentVoucher.limit" placeholder="-1 = Không giới hạn" />
          </div>

          <div class="admin-form-group">
            <label class="admin-toggle">
              <input type="checkbox" v-model="currentVoucher.isActive" hidden>
              <span class="toggle-switch" :class="{ active: currentVoucher.isActive }"></span>
              <span>Kích hoạt ngay</span>
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button class="admin-btn admin-btn-outline" @click="closeModal">Hủy</button>
          <button class="admin-btn admin-btn-primary" @click="saveVoucher">Lưu voucher</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/api'

const searchQuery = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const isEditing = ref(false)
const isLoading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const vouchers = ref([])

// Map backend Promotion → frontend voucher
const mapVoucher = (p) => ({
  id: p.promo_id,
  code: p.code,
  type: p.promo_type,
  value: parseFloat(p.discount_value),
  minOrder: parseFloat(p.min_order_amount || 0),
  limit: p.max_uses ?? -1,
  used: p.used_count ?? 0,
  startDate: p.start_date,
  endDate: p.end_date,
  isActive: p.is_active,
})

const fetchVouchers = async () => {
  isLoading.value = true
  try {
    const params = { page: currentPage.value, per_page: 10 }
    if (searchQuery.value) params.search = searchQuery.value
    if (filterStatus.value === 'active') params.status = 'active'
    else if (filterStatus.value === 'disabled') params.status = 'inactive'

    const res = await api.get('/admin/promotions', { params })
    vouchers.value = (res.data.data || []).map(mapVoucher)
    total.value = res.data.total || 0
    lastPage.value = res.data.last_page || 1
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi tải danh sách voucher')
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchVouchers)

let searchTimer = null
watch(searchQuery, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { currentPage.value = 1; fetchVouchers() }, 400)
})
watch(filterStatus, () => { currentPage.value = 1; fetchVouchers() })

const defaultVoucher = () => ({
  id: null,
  code: '',
  type: 'percentage',
  value: 10,
  minOrder: 0,
  limit: null,
  startDate: new Date().toISOString().split('T')[0],
  endDate: '',
  isActive: true,
})

const currentVoucher = ref(defaultVoucher())

const getVoucherStatus = (v) => {
  if (!v.isActive) return 'disabled'
  const now = new Date()
  const end = new Date(v.endDate)
  if (end < now) return 'expired'
  return 'active'
}

const getVoucherStatusText = (v) => {
  const status = getVoucherStatus(v)
  if (status === 'disabled') return 'Vô hiệu hóa'
  if (status === 'expired') return 'Hết hạn'
  return 'Đang hoạt động'
}

const getVoucherStatusClass = (v) => {
  const status = getVoucherStatus(v)
  if (status === 'active') return 'active'
  if (status === 'expired') return 'cancelled'
  return 'inactive'
}

const formatCurrency = (value) => {
  if (!value) return '0₫'
  return new Intl.NumberFormat('vi-VN').format(value) + '₫'
}

const formatDiscount = (v) => {
  if (v.type === 'percentage') return `${v.value}%`
  return formatCurrency(v.value)
}

// Chỉ lọc theo expired trên frontend (vì backend không lọc được expired)
const filteredVouchers = computed(() => {
  if (filterStatus.value !== 'expired') return vouchers.value
  return vouchers.value.filter(v => getVoucherStatus(v) === 'expired')
})

const openAddModal = () => {
  isEditing.value = false
  currentVoucher.value = defaultVoucher()
  showModal.value = true
}

const editVoucher = (v) => {
  isEditing.value = true
  currentVoucher.value = { ...v }
  showModal.value = true
}

const deleteVoucher = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa mã voucher này?')) return
  try {
    await api.delete(`/admin/promotions/${id}`)
    await fetchVouchers()
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi xóa voucher')
  }
}

const closeModal = () => {
  showModal.value = false
}

const saveVoucher = async () => {
  if (!currentVoucher.value.code.trim()) {
    alert('Vui lòng nhập mã voucher')
    return
  }
  if (!currentVoucher.value.endDate) {
    alert('Vui lòng nhập ngày kết thúc')
    return
  }

  const payload = {
    code: currentVoucher.value.code.toUpperCase().trim(),
    promo_type: currentVoucher.value.type,
    discount_value: currentVoucher.value.value,
    min_order_amount: currentVoucher.value.minOrder || 0,
    max_uses: currentVoucher.value.limit || null,
    start_date: currentVoucher.value.startDate,
    end_date: currentVoucher.value.endDate,
    is_active: currentVoucher.value.isActive,
  }

  try {
    if (isEditing.value) {
      await api.put(`/admin/promotions/${currentVoucher.value.id}`, payload)
    } else {
      await api.post('/admin/promotions', payload)
    }
    await fetchVouchers()
    closeModal()
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi lưu voucher')
  }
}

const toggleVoucherStatus = async (v) => {
  try {
    const res = await api.put(`/admin/promotions/${v.id}/toggle-status`)
    v.isActive = res.data.is_active
  } catch (err) {
    alert(err.response?.data?.message || 'Lỗi khi thay đổi trạng thái')
  }
}

const goToPage = (page) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchVouchers()
}
</script>

<style scoped>
/* Reuse modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal-content {
  background: var(--admin-bg-card);
  width: 100%;
  max-width: 600px;
  border-radius: var(--admin-radius-lg);
  box-shadow: 0 20px 50px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
  max-height: 90vh;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--admin-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 { margin: 0; font-size: 18px; color: var(--admin-text); }

.close-modal { background: none; border: none; font-size: 24px; color: var(--admin-text-muted); cursor: pointer; }

.modal-body { padding: 24px; overflow-y: auto; }

.modal-footer {
  padding: 20px 24px;
  border-top: 1px solid var(--admin-border);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}
</style>
