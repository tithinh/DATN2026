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
            <tr v-for="voucher in filteredVouchers" :key="voucher.id">
              <td style="font-weight:600; color: var(--admin-text);">#{{ voucher.id }}</td>
              <td>
                <span class="role-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 700; letter-spacing: 1px;">{{ voucher.code }}</span>
              </td>
              <td style="font-weight:600; color: var(--admin-text);">
                {{ formatDiscount(voucher) }}
              </td>
              <td>{{ formatCurrency(voucher.minOrder) }}</td>
              <td>{{ voucher.used }} / {{ voucher.limit === -1 ? '∞' : voucher.limit }}</td>
              <td style="color: var(--admin-text-soft);">{{ voucher.endDate }}</td>
              <td>
                <span class="status-badge" :class="getVoucherStatusClass(voucher)">{{ getVoucherStatusText(voucher) }}</span>
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
        <div class="pagination-info">Hiển thị 1-{{ filteredVouchers.length }} trên tổng số {{ vouchers.length }} mã</div>
        <div class="pagination-btns">
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>
          <button class="page-btn active">1</button>
          <button class="page-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>
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
                <option value="percent">Phần trăm (%)</option>
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
import { ref, computed } from 'vue'

const searchQuery = ref('')
const filterStatus = ref('')
const showModal = ref(false)
const isEditing = ref(false)

const vouchers = ref([
  {
    id: 1,
    code: 'WELCOME',
    type: 'percent',
    value: 10,
    minOrder: 0,
    maxDiscount: 50000,
    startDate: '2026-01-01',
    endDate: '2026-12-31',
    limit: -1,
    used: 156,
    isActive: true
  },
  {
    id: 2,
    code: 'TET2026',
    type: 'fixed',
    value: 50000,
    minOrder: 500000,
    maxDiscount: null,
    startDate: '2026-01-15',
    endDate: '2026-02-15',
    limit: 100,
    used: 89,
    isActive: true
  },
  {
    id: 3,
    code: 'FREESHIP',
    type: 'fixed',
    value: 30000,
    minOrder: 200000,
    maxDiscount: null,
    startDate: '2026-01-01',
    endDate: '2026-06-30',
    limit: 500,
    used: 120,
    isActive: false
  }
])

const currentVoucher = ref({
  id: null,
  code: '',
  type: 'percent',
  value: 0,
  minOrder: 0,
  maxDiscount: 0,
  startDate: '',
  endDate: '',
  limit: -1,
  isActive: true
})

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
  if (status === 'active') return 'active' // Green
  if (status === 'expired') return 'cancelled' // Red
  return 'inactive' // Gray/Orange
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value)
}

const formatDiscount = (v) => {
  if (v.type === 'percent') return `${v.value}%`
  return formatCurrency(v.value)
}

const filteredVouchers = computed(() => {
  return vouchers.value.filter(v => {
    const matchSearch = v.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    const status = getVoucherStatus(v)
    const matchStatus = filterStatus.value ? status === filterStatus.value : true
    return matchSearch && matchStatus
  })
})

const openAddModal = () => {
  isEditing.value = false
  currentVoucher.value = {
    id: null,
    code: '',
    type: 'percent',
    value: 10,
    minOrder: 0,
    maxDiscount: 0,
    startDate: new Date().toISOString().split('T')[0],
    endDate: '',
    limit: -1,
    isActive: true
  }
  showModal.value = true
}

const editVoucher = (v) => {
  isEditing.value = true
  currentVoucher.value = { ...v }
  showModal.value = true
}

const deleteVoucher = (id) => {
  if(confirm('Bạn có chắc muốn xóa mã voucher này?')) {
    vouchers.value = vouchers.value.filter(v => v.id !== id)
  }
}

const closeModal = () => {
  showModal.value = false
}

const saveVoucher = () => {
  if (isEditing.value) {
    const index = vouchers.value.findIndex(v => v.id === currentVoucher.value.id)
    if (index !== -1) {
      vouchers.value[index] = { ...vouchers.value[index], ...currentVoucher.value }
    }
  } else {
    vouchers.value.unshift({
      id: vouchers.value.length + 10,
      ...currentVoucher.value,
      used: 0
    })
  }
  closeModal()
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
