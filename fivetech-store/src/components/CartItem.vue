<template>
  <div class="flex gap-6 bg-white p-6 rounded-xl shadow">
    <img
      :src="item.variant.image_urls?.[0] || 'https://via.placeholder.com/120'"
      class="w-32 h-32 object-cover rounded-lg"
    />

    <div class="flex-1">
      <h3 class="font-semibold text-lg mb-2">
        {{ item.variant.name || 'Biến thể' }}
      </h3>

      <p class="text-gray-600 mb-2">
        Màu: {{ item.variant.color }} • Size: {{ item.variant.size || 'N/A' }}
      </p>

      <div class="flex items-center justify-between">
        <div class="text-xl font-bold text-blue-600">
          {{ formatPrice(item.variant.price * item.quantity) }}
        </div>

        <div class="flex items-center border rounded">
          <button @click="updateQuantity(-1)" class="px-4 py-1">-</button>
          <span class="px-6 py-1 font-medium">{{ item.quantity }}</span>
          <button @click="updateQuantity(1)" class="px-4 py-1">+</button>
        </div>
      </div>

      <button
        @click="cartStore.removeItem(item.variant.variant_id)"
        class="text-red-600 mt-3 hover:underline"
      >
        Xóa
      </button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const auth = useAuthStore()
const router = useRouter()

onMounted(async () => {
  if (auth.isAuthenticated) {
    await cartStore.fetchCart()
  } else {
    // Optional: redirect hoặc thông báo
    // router.push('/login?redirect=/cart')
  }
})

// Ví dụ hàm thêm sản phẩm (nếu có nút "Thêm vào giỏ")
const addToCart = async (productId, variantId, quantity = 1) => {
  try {
    await api.post('/cart/add', { product_id: productId, variant_id: variantId, quantity })
    await cartStore.fetchCart()
  } catch (err) {
    alert('Không thể thêm vào giỏ: ' + (err.response?.data?.message || err.message))
  }
}
const props = defineProps({
  item: Object
})


const formatPrice = (price) =>
  price.toLocaleString('vi-VN') + ' ₫'

const updateQuantity = (change) => {
  const id = props.item.variant.variant_id

  if (change === 1) {
    cartStore.increase(id)
  }

  if (change === -1 && props.item.quantity > 1) {
    cartStore.decrease(id)
  }
}
</script>
