<template>
  <header class="admin-header">
    <div class="header-left">
      <h2>{{ pageTitle }}</h2>
    </div>

    
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  collapsed: Boolean
})

defineEmits(['toggle-sidebar'])

const route = useRoute()

const pageTitles = {
  'AdminDashboard': 'Dashboard',
  'AdminProducts': 'Quản lý sản phẩm',
  'AdminCategories': 'Quản lý danh mục',
  'AdminOrders': 'Quản lý đơn hàng',
  'AdminOrderDetail': 'Chi tiết đơn hàng',
  'AdminUsers': 'Quản lý người dùng',
  'AdminComments': 'Quản lý đánh giá'
}

const pageTitle = computed(() => {
  return pageTitles[route.name] || route.meta?.title || 'Admin Panel'
})
</script>
