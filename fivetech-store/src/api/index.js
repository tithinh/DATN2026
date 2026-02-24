import axios from 'axios'
import { useRouter } from 'vue-router' // nếu dùng router để redirect khi 401

const router = useRouter() // chỉ dùng nếu bạn dùng vue-router

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  withCredentials: true, // BẮT BUỘC để cookie (XSRF-TOKEN, laravel_session) hoạt động
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

// Interceptor request: tự động lấy CSRF + thêm token
api.interceptors.request.use(async (config) => {
  // Các method cần CSRF (Sanctum yêu cầu XSRF-TOKEN cho state-changing requests)
  const methodsNeedCsrf = ['post', 'put', 'patch', 'delete']
  if (methodsNeedCsrf.includes(config.method)) {
    try {
      // Gọi CSRF endpoint để set cookie XSRF-TOKEN
      await axios.get('/sanctum/csrf-cookie', {
        baseURL: api.defaults.baseURL,
        withCredentials: true,
      })
    } catch (csrfErr) {
      console.warn('Không lấy được CSRF token:', csrfErr)
    }
  }

  // Thêm token admin nếu gọi route admin
  if (config.url.startsWith('/admin') || config.url.startsWith('/api/v1/admin')) {
    const adminToken = localStorage.getItem('admin_token')
    if (adminToken) {
      config.headers.Authorization = `Bearer ${adminToken}`
    } else {
      console.warn('Không có admin_token khi gọi route admin:', config.url)
    }
  } else {
    const userToken = localStorage.getItem('token')
    if (userToken) {
      config.headers.Authorization = `Bearer ${userToken}`
    }
  }
  return config
})

// Interceptor response: xử lý lỗi global
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status

    // 401 Unauthorized hoặc 419 CSRF token mismatch → logout và redirect
    if (status === 401 || status === 419) {
      // Xóa token
      localStorage.removeItem('token')
      localStorage.removeItem('admin_token')
      localStorage.removeItem('user')

      // Redirect về login (tùy theo route bạn muốn)
      if (window.location.pathname.startsWith('/admin')) {
        router.push('/admin/login')
      } else {
        router.push('/login')
      }

      // Optional: hiển thị thông báo
      alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.')
    }

    return Promise.reject(error)
  }
)

export default api