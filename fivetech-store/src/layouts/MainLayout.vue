<!-- src/layouts/MainLayout.vue -->
<template>
  <div class="main-layout">
    <InnerHeader />

    <main class="main-content">
      <router-view />
    </main>

    <InnerFooter />
  </div>
</template>

<script setup>
import InnerHeader from '@/components/layout/InnerHeader.vue'
import InnerFooter from '@/components/layout/InnerFooter.vue'
</script>

<style scoped>
.main-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.main-content {
  flex: 1;
}
</style>
