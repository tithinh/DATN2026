<!-- fivetech-store/src/components/layout/InnerHeader.vue -->
<template>
  <header class="home-header">
    <div class="header-container">
      <!-- Logo -->
      <a href="/" class="logo">
        <span class="logo-icon">T5</span>
        <span class="logo-text">Techfive</span>
      </a>

      <!-- Navigation Menu -->
      <nav class="nav-menu">
        <a href="/" class="nav-link active">Trang chủ</a>
        <a href="/products" class="nav-link">Sản phẩm</a>
        <a href="/news" class="nav-link">Tin tức</a>
        <a href="/contact" class="nav-link">Liên hệ</a>
      </nav>


      <!-- Auth Buttons -->
<div class="auth-buttons">
  <template v-if="!auth.isAuthenticated">
    <router-link to="/login" class="auth-btn login-btn">Đăng nhập</router-link>
    <router-link to="/register" class="auth-btn register-btn">Đăng ký</router-link>
  </template>

  <template v-else>
    <div class="relative user-menu">
      <button 
        class="flex items-center gap-2 focus:outline-none"
        @click="showUserMenu = !showUserMenu"
      >
        <img
          :src="auth.user?.avatar || 'https://ui-avatars.com/api/?name=' + (auth.user?.name || 'User')"
          alt="Avatar"
          class="w-9 h-9 rounded-full object-cover border border-gray-300"
        />
        <span class="hidden md:inline font-medium">{{ auth.user?.name || 'Tài khoản' }}</span>
      </button>

      <div 
        v-if="showUserMenu"
        class="dropdown show"
      >
        <router-link 
          to="/account" 
          class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50"
        >
          Trang cá nhân
        </router-link>
        <button 
          @click="handleLogout"
          class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"
        >
          Đăng xuất
        </button>
      </div>
    </div>
  </template>
</div>

      <!-- Cart Icon -->
      <a href="/cart" class="cart-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="8" cy="21" r="1"></circle>
          <circle cx="19" cy="21" r="1"></circle>
          <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
        </svg>
      </a>
    </div>
  </header>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const router = useRouter()

const searchQuery = ref('')
const results = ref([])
const showDropdown = ref(false)
const searchBox = ref(null)
const auth = useAuthStore()

const showUserMenu = ref(false)

const handleLogout = () => {
  auth.logout()
  showUserMenu.value = false
  // Nếu muốn redirect về trang chủ sau logout:
  // router.push('/')
}
let debounceTimer = null

// Gọi API search gợi ý
watch(searchQuery, (val) => {
  clearTimeout(debounceTimer)

  if (!val.trim()) {
    results.value = []
    return
  }

  debounceTimer = setTimeout(async () => {
    try {
      const res = await axios.get('/api/search', {
        params: { q: val, limit: 6 }
      })
      results.value = res.data.data
      showDropdown.value = true
    } catch {
      results.value = []
    }
  }, 300)
})

// Enter hoặc click icon
const goToSearch = () => {
  const q = searchQuery.value.trim()
  if (!q) return

  showDropdown.value = false
  router.push({
    name: 'Products',
    query: { search: q }
  })
}

// Click ra ngoài thì đóng dropdown
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!searchBox.value?.contains(e.target)) {
      showDropdown.value = false
    }
  })
})
</script>



<style scoped>
.search-box {
  position: relative;
}

.search-item {
  display: flex;
  gap: 12px;
  padding: 10px;
  text-decoration: none;
  color: #111;
}

.search-item:hover {
  background: #f3f4f6;
}

.search-item img {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
}

.name {
  font-weight: 600;
}

.price {
  color: #e11d48;
  font-size: 14px;
}

/* ================= HOME HEADER ================= */
.home-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.header-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.logo-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 18px;
  font-weight: 800;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
}

.logo-text {
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.5px;
}

/* Navigation */
.nav-menu {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  padding: 10px 20px;
  color: #cbd5e1;
  text-decoration: none;
  font-weight: 500;
  font-size: 15px;
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.1);
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #ff6b35, #f7931e);
  transition: width 0.3s ease;
}

.nav-link:hover::after {
  width: 60%;
}

/* Search Box */
.search-box {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.search-box:focus-within {
  background: rgba(255, 255, 255, 0.15);
  border-color: #ff6b35;
  box-shadow: 0 0 20px rgba(255, 107, 53, 0.2);
}

.search-input {
  width: 240px;
  padding: 12px 16px;
  border: none;
  background: transparent;
  color: #ffffff;
  font-size: 14px;
  outline: none;
}

.search-input::placeholder {
  color: #94a3b8;
}

.search-btn {
  padding: 12px 16px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border: none;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-btn:hover {
  background: linear-gradient(135deg, #e85a2a 0%, #e88619 100%);
}

/* Cart Icon */
.cart-icon {
  position: relative;
  width: 48px;
  height: 48px;
  padding: 0;
  color: #ffffff;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cart-icon:hover {
  background: rgba(255, 107, 53, 0.2);
  transform: translateY(-2px);
}

/* Auth Buttons */
/* Auth Buttons Container */
.auth-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Nút chung (Đăng nhập / Đăng ký) */
.auth-btn {
  padding: 10px 20px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  cursor: pointer;
  white-space: nowrap;
}

/* Đăng nhập */
.login-btn {
  color: #ffffff;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

.login-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.7);
  transform: translateY(-1px);
}

/* Đăng ký */
.register-btn {
  color: #ffffff;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.35);
}

.register-btn:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.45);
}

/* User Menu (khi đã đăng nhập) */
.user-menu {
  position: relative;
}

.user-menu button {
  display: flex;
  align-items: center;
  gap: 10px;
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 6px 8px;
  border-radius: 12px;
  transition: background 0.3s ease;
}

.user-menu button:hover {
  background: rgba(255, 255, 255, 0.1);
}

.user-menu img {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.user-menu span {
  color: #ffffff;
  font-weight: 600;
  font-size: 14px;
}

/* Dropdown menu */
.user-menu .dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 12px;
  width: 220px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  z-index: 1000;
  border: 1px solid rgba(0, 0, 0, 0.08);
  opacity: 0;
  transform: translateY(10px);
  pointer-events: none;
  transition: all 0.25s ease;
}

.user-menu .dropdown.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.dropdown a,
.dropdown button {
  display: block;
  width: 100%;
  padding: 12px 20px;
  text-align: left;
  font-size: 14px;
  color: #1f2937;
  text-decoration: none;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease;
}

.dropdown a:hover,
.dropdown button:hover {
  background: #f3f4f6;
}

.dropdown button.text-red-600:hover {
  background: #fee2e2;
}

/* Mobile responsive */
@media (max-width: 768px) {
  .auth-buttons {
    gap: 8px;
  }

  .user-menu span {
    display: none; /* ẩn tên trên mobile */
  }

  .user-menu img {
    width: 32px;
    height: 32px;
  }

  .auth-btn {
    padding: 8px 16px;
    font-size: 13px;
  }
}
/* ================= RESPONSIVE ================= */
@media (max-width: 1024px) {
  .nav-menu {
    display: none;
  }

  .search-box {
    flex: 1;
    max-width: 300px;
  }

  .search-input {
    width: 100%;
  }

  .mobile-menu-toggle {
    display: block;
  }
}

@media (max-width: 640px) {
  .header-container {
    padding: 0 16px;
    height: 64px;
  }

  .logo-text {
    display: none;
  }

  .search-box {
    display: none;
  }
}
</style>
