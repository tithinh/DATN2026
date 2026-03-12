import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

// Interceptor request: tự động thêm token
api.interceptors.request.use(async (config) => {
  // Bỏ qua CSRF vì đã dùng Bearer Token
  // CSRF chỉ cần cho web routes, không cần cho API routes

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

// Toast notification thay cho alert (không block UI, không reload trang)
const showToast = (message, type = 'error') => {
  // Xóa toast cũ nếu có
  const existingToast = document.getElementById('api-toast')
  if (existingToast) existingToast.remove()

  const toast = document.createElement('div')
  toast.id = 'api-toast'
  toast.textContent = message
  Object.assign(toast.style, {
    position: 'fixed',
    top: '20px',
    right: '20px',
    padding: '14px 24px',
    borderRadius: '12px',
    color: '#fff',
    fontSize: '14px',
    fontWeight: '500',
    zIndex: '99999',
    boxShadow: '0 8px 32px rgba(0,0,0,0.2)',
    background: type === 'error' ? '#ef4444' : '#10b981',
    animation: 'slideInToast 0.3s ease',
    maxWidth: '400px',
  })

  // Thêm animation CSS nếu chưa có
  if (!document.getElementById('toast-style')) {
    const style = document.createElement('style')
    style.id = 'toast-style'
    style.textContent = `
      @keyframes slideInToast { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
      @keyframes slideOutToast { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    `
    document.head.appendChild(style)
  }

  document.body.appendChild(toast)

  // Tự ẩn sau 3 giây
  setTimeout(() => {
    toast.style.animation = 'slideOutToast 0.3s ease'
    setTimeout(() => toast.remove(), 300)
  }, 3000)
}

// Flag tránh redirect lặp
let isRedirecting401 = false

// Interceptor response: xử lý lỗi global
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status
    const url = error.config?.url || ''

    // Bỏ qua interceptor cho các endpoint login và /user (để store tự xử lý)
    if (url.includes('/admin/login') || url === '/login' || url === '/user') {
      return Promise.reject(error)
    }

    if ((status === 401 || status === 419) && !isRedirecting401) {
      isRedirecting401 = true

      localStorage.removeItem('token')
      localStorage.removeItem('admin_token')
      localStorage.removeItem('user')

      showToast('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.')

      // Dùng setTimeout để đợi Vue Router sẵn sàng
      setTimeout(() => {
        const isAdmin = window.location.pathname.startsWith('/admin')
        const targetPath = isAdmin ? '/admin/login' : '/login'

        // Import router động để tránh circular dependency
        import('@/router').then(({ default: router }) => {
          router.push(targetPath)
          isRedirecting401 = false
        }).catch(() => {
          // Fallback nếu import thất bại
          window.location.href = targetPath
          isRedirecting401 = false
        })
      }, 500)
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

export { showToast }
export default api
