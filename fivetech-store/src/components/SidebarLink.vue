<template>
  <router-link
    :to="to"
    class="flex items-center gap-3
           w-full px-4 py-3 rounded-lg
           font-medium transition
           hover:bg-gray-800"
    :class="{
      'bg-gray-800 text-white': $route.path === to,
      'text-gray-300': $route.path !== to
    }"
  >
    <!-- Icon -->
    <component
      :is="iconComponent"
      class="w-5 h-5 flex-shrink-0"
    />

    <!-- Text -->
    <span>{{ label }}</span>
  </router-link>
</template>

<script setup>
import {
  LayoutDashboard,
  Package,
  Folder,
  ShoppingCart,
  Users
} from 'lucide-vue-next'

const props = defineProps({
  to: String,
  label: String,
  icon: String
})

const icons = {
  Dashboard: LayoutDashboard,
  Package,
  Folder,
  ShoppingCart,
  Users
}

const iconComponent = icons[props.icon]
</script>
