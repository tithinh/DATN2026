<template>
  <div>
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card products slide-up" v-for="(stat, i) in stats" :key="i" :class="stat.type" :style="{ animationDelay: i * 0.1 + 's' }">
        <div class="stat-info">
          <h4>{{ stat.title }}</h4>
          <p class="stat-value">{{ stat.value }}</p>
          <span class="stat-change" :class="stat.changeType">
            <svg v-if="stat.changeType === 'up'" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m18 15-6-6-6 6"/></svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
            {{ stat.change }}
          </span>
        </div>
        <div class="stat-icon" :class="stat.type" v-html="stat.icon"></div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
      <!-- Revenue Chart -->
      <div class="admin-card">
        <div class="card-header">
          <h3>📊 Doanh thu theo tháng</h3>
          <select class="toolbar-filter" style="min-width: 100px;">
            <option>2026</option>
            <option>2025</option>
          </select>
        </div>
        <div class="chart-container">
          <div class="chart-bar-group">
            <div class="chart-bar-wrapper" v-for="(item, idx) in revenueData" :key="idx">
              <div class="chart-bar revenue-bar" :style="{ height: item.height + '%' }" :title="item.value"></div>
              <span class="chart-label">{{ item.label }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders Chart -->
      <div class="admin-card">
        <div class="card-header">
          <h3>📦 Đơn hàng theo ngày</h3>
          <select class="toolbar-filter" style="min-width: 120px;">
            <option>7 ngày qua</option>
            <option>30 ngày qua</option>
          </select>
        </div>
        <div class="chart-container">
          <div class="chart-bar-group">
            <div class="chart-bar-wrapper" v-for="(item, idx) in orderData" :key="idx">
              <div class="chart-bar order-bar" :style="{ height: item.height + '%' }" :title="item.value + ' đơn'"></div>
              <span class="chart-label">{{ item.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-card">
      <div class="card-header">
        <h3>🕐 Đơn hàng gần đây</h3>
        <router-link to="/admin/orders" class="admin-btn admin-btn-outline admin-btn-sm">
          Xem tất cả
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
        </router-link>
      </div>
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Khách hàng</th>
              <th>Sản phẩm</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Ngày đặt</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in recentOrders" :key="order.id">
              <td style="font-weight: 600; color: var(--admin-text);">#{{ order.id }}</td>
              <td>{{ order.customer }}</td>
              <td>{{ order.product }}</td>
              <td style="font-weight: 600; color: var(--admin-text);">{{ order.total }}</td>
              <td>
                <span class="status-badge" :class="order.statusClass">{{ order.status }}</span>
              </td>
              <td>{{ order.date }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const stats = ref([
  {
    type: 'products',
    title: 'Tổng sản phẩm',
    value: '1,284',
    change: '+12.5%',
    changeType: 'up',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>'
  },
  {
    type: 'orders',
    title: 'Tổng đơn hàng',
    value: '3,462',
    change: '+8.2%',
    changeType: 'up',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>'
  },
  {
    type: 'revenue',
    title: 'Tổng doanh thu',
    value: '2.45 tỷ',
    change: '+15.3%',
    changeType: 'up',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
  },
  {
    type: 'users',
    title: 'Tổng người dùng',
    value: '8,521',
    change: '-2.1%',
    changeType: 'down',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
  }
])

const revenueData = ref([
  { label: 'T1', height: 45, value: '180M' },
  { label: 'T2', height: 62, value: '248M' },
  { label: 'T3', height: 38, value: '152M' },
  { label: 'T4', height: 75, value: '300M' },
  { label: 'T5', height: 55, value: '220M' },
  { label: 'T6', height: 82, value: '328M' },
  { label: 'T7', height: 68, value: '272M' },
  { label: 'T8', height: 90, value: '360M' },
  { label: 'T9', height: 72, value: '288M' },
  { label: 'T10', height: 58, value: '232M' },
  { label: 'T11', height: 95, value: '380M' },
  { label: 'T12', height: 100, value: '400M' }
])

const orderData = ref([
  { label: 'T2', height: 65, value: 42 },
  { label: 'T3', height: 45, value: 28 },
  { label: 'T4', height: 80, value: 56 },
  { label: 'T5', height: 55, value: 35 },
  { label: 'T6', height: 92, value: 68 },
  { label: 'T7', height: 70, value: 48 },
  { label: 'CN', height: 38, value: 22 }
])

const recentOrders = ref([
  { id: 'DH2401', customer: 'Nguyễn Văn An', product: 'Ốp lưng iPhone 15 Pro', total: '450.000₫', status: 'Hoàn thành', statusClass: 'completed', date: '12/02/2026' },
  { id: 'DH2402', customer: 'Trần Thị Bình', product: 'Tai nghe Bluetooth Sony', total: '1.200.000₫', status: 'Đang giao', statusClass: 'shipping', date: '12/02/2026' },
  { id: 'DH2403', customer: 'Lê Hoàng Cường', product: 'Pin dự phòng 20000mAh', total: '680.000₫', status: 'Chờ xử lý', statusClass: 'pending', date: '11/02/2026' },
  { id: 'DH2404', customer: 'Phạm Minh Đức', product: 'Cáp sạc Baseus 65W', total: '250.000₫', status: 'Hoàn thành', statusClass: 'completed', date: '11/02/2026' },
  { id: 'DH2405', customer: 'Hoàng Thị Em', product: 'Kính cường lực Samsung', total: '150.000₫', status: 'Đã huỷ', statusClass: 'cancelled', date: '10/02/2026' },
])
</script>