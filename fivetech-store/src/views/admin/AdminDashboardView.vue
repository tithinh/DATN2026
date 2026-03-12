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
          <h3>Doanh thu 7 ngày gần nhất</h3>
        </div>
        <div class="chart-container" v-if="revenueByDay.length > 0">
          <div class="chart-bar-group">
            <div class="chart-bar-wrapper" v-for="(item, idx) in revenueByDay" :key="idx">
              <div class="chart-bar revenue-bar" :style="{ height: item.height + '%' }" :title="item.value"></div>
              <span class="chart-label">{{ item.label }}</span>
            </div>
          </div>
        </div>
        <div v-else class="chart-container" style="text-align: center; padding: 40px; color: var(--admin-text-muted);">
          Chưa có dữ liệu doanh thu
        </div>
      </div>

      <!-- Orders Chart -->
      <div class="admin-card">
        <div class="card-header">
          <h3>Đơn hàng đang chờ xử lý</h3>
        </div>
        <div class="chart-container" style="display: flex; align-items: center; justify-content: center;">
          <div style="text-align: center;">
            <div style="font-size: 48px; font-weight: 700; color: var(--admin-warning);">
              {{ stats.find(s => s.type === 'orders')?.value || 0 }}
            </div>
            <div style="color: var(--admin-text-muted);">đơn hàng</div>
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
import { ref, onMounted, computed } from 'vue'
import api from '@/api'

const stats = ref([])
const recentOrders = ref([])
const revenueByDay = ref([])
const isLoading = ref(true)

const formatCurrency = (value) => {
  if (!value) return '0₫'
  return new Intl.NumberFormat('vi-VN').format(value) + '₫'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('vi-VN')
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'pending',
    'shipping': 'shipping',
    'completed': 'completed'
  }
  return classes[status] || 'pending'
}

const getStatusText = (status) => {
  const texts = {
    'pending': 'Chờ xử lý',
    'shipping': 'Đang giao',
    'completed': 'Hoàn thành'
  }
  return texts[status] || status
}

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard/stats')
    const data = res.data

    // Stats cards
    stats.value = [
      {
        type: 'products',
        title: 'Tổng sản phẩm',
        value: data.products.total,
        change: `${data.products.low_stock} sắp hết`,
        changeType: data.products.low_stock > 0 ? 'down' : 'up',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>'
      },
      {
        type: 'orders',
        title: 'Tổng đơn hàng',
        value: data.orders.total,
        change: `${data.orders.pending} chờ xử lý`,
        changeType: data.orders.pending > 0 ? 'down' : 'up',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>'
      },
      {
        type: 'revenue',
        title: 'Tổng doanh thu',
        value: formatCurrency(data.revenue.total),
        change: `${formatCurrency(data.revenue.this_month)} tháng này`,
        changeType: 'up',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
      },
      {
        type: 'users',
        title: 'Tổng người dùng',
        value: data.users.total,
        change: `${data.users.new_this_month} tháng này`,
        changeType: 'up',
        icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
      }
    ]

    // Recent orders
    recentOrders.value = data.orders.recent.map(order => ({
      id: order.order_code || `DH${order.order_id}`,
      customer: order.customer_name || order.user?.full_name || 'Khách vãng lai',
      product: 'Xem chi tiết',
      total: formatCurrency(order.final_amount),
      status: getStatusText(order.status),
      statusClass: getStatusClass(order.status),
      date: formatDate(order.created_at)
    }))

    // Revenue by day (last 7 days)
    revenueByDay.value = data.revenue_by_day.map(item => ({
      label: new Date(item.date).toLocaleDateString('vi-VN', { weekday: 'short' }),
      height: Math.max(10, (item.revenue / Math.max(...data.revenue_by_day.map(d => d.revenue), 1)) * 100),
      value: formatCurrency(item.revenue)
    }))

  } catch (error) {
    console.error('Failed to load dashboard stats:', error)
  } finally {
    isLoading.value = false
  }
})
</script>