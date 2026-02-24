<template>
  <div class="bg-white border border-gray-200 rounded-xl p-6 text-center hover:border-blue-500 transition cursor-pointer">
    <div class="text-5xl mb-4">{{ category.icon }}</div>
    <h3 class="font-semibold text-lg">{{ category.name }}</h3>
  </div>
</template>

<script setup>
defineProps({
  category: Object
})
</script>