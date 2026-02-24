import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import api from './api'

// Import Tailwind CSS (hoặc file style chính)
import './style.css'
// Tạo app
const app = createApp(App)
const pinia = createPinia()

// Sử dụng router và Pinia
app.use(router)
app.use(pinia)

const auth = useAuthStore()
auth.init()

// Mount vào #app
app.mount('#app')

// Optional: Global error handler (hữu ích khi phát triển)
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err, info)
}