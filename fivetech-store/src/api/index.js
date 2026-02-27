import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

// Interceptor request: tự động lấy CSRF + thêm token
api.interceptors.request.use(async (config) => {
  const methodsNeedCsrf = ['post', 'put', 'patch', 'delete']
  if (methodsNeedCsrf.includes(config.method)) {
    try {
      await axios.get('/sanctum/csrf-cookie', {
        baseURL: api.defaults.baseURL,
        withCredentials: true,
      })
    } catch (csrfErr) {
      console.warn('Không lấy được CSRF token:', csrfErr)
    }
  }

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

    if (status === 401 || status === 419) {
      localStorage.removeItem('token')
      localStorage.removeItem('admin_token')
      localStorage.removeItem('user')

      if (window.location.pathname.startsWith('/admin')) {
        router.push('/admin/login')
      } else {
        router.push('/login')
      }

      alert('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.')
    }

    return Promise.reject(error)
  }
)

// ======================
// NEWS API
// ======================

// Public: Get all news
export const getNews = (params) => api.get('/news', { params })

// Public: Get single news by slug
export const getNewsBySlug = (slug) => api.get(`/news/${slug}`)

// Public: Get news categories
export const getNewsCategories = () => api.get('/news/categories')

// Public: Get popular news
export const getPopularNews = (limit = 5) => api.get('/news/popular', { params: { limit } })

// Admin: Get all news (including drafts)
export const getAdminNews = (params) => api.get('/admin/news', { params })

// Admin: Get single news
export const getAdminNewsById = (id) => api.get(`/admin/news/${id}`)

// Admin: Create news
export const createNews = (data) => api.post('/admin/news', data)

// Admin: Update news
export const updateNews = (id, data) => api.put(`/admin/news/${id}`, data)

// Admin: Delete news
export const deleteNews = (id) => api.delete(`/admin/news/${id}`)

// Admin: Toggle news status
export const toggleNewsStatus = (id) => api.put(`/admin/news/${id}/toggle-status`)

// ======================
// CONTACTS API
// ======================

// Public: Submit contact form
export const submitContact = (data) => api.post('/contacts', data)

// Admin: Get all contacts
export const getAdminContacts = (params) => api.get('/admin/contacts', { params })

// Admin: Get single contact
export const getAdminContactById = (id) => api.get(`/admin/contacts/${id}`)

// Admin: Update contact status
export const updateContactStatus = (id, status) => api.put(`/admin/contacts/${id}/status`, { status })

// Admin: Delete contact
export const deleteContact = (id) => api.delete(`/admin/contacts/${id}`)

// Admin: Mark as spam
export const markContactAsSpam = (id) => api.put(`/admin/contacts/${id}/spam`)

// ======================
// SOCIAL LOGIN API
// ======================

// Redirect to Google
export const redirectToGoogle = () => {
  window.location.href = api.defaults.baseURL + '/auth/google'
}

// Redirect to Facebook
export const redirectToFacebook = () => {
  window.location.href = api.defaults.baseURL + '/auth/facebook'
}

// Handle social callback (call this after redirect back)
export const handleSocialCallback = async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')
  const user = urlParams.get('user')
  
  if (token) {
    localStorage.setItem('token', token)
    if (user) {
      localStorage.setItem('user', JSON.stringify(JSON.parse(decodeURIComponent(user))))
    }
    window.history.replaceState({}, document.title, window.location.pathname)
    return true
  }
  return false
}

// ======================
// PASSWORD RESET API
// ======================

// Request password reset link
export const forgotPassword = (email) => api.post('/password/forgot', { email })

// Reset password with token
export const resetPassword = (data) => api.post('/password/reset', data)

export default api
