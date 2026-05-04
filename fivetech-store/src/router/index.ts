import { createRouter, createWebHistory } from 'vue-router'

import MainLayout from '@/layouts/MainLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { useAdminAuthStore } from '@/stores/adminAuth'
// Public routes (không cần đăng nhập)
const publicRoutes = [
  // Home page with its own header/footer (standalone)
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/cilent/Home.vue'),
    meta: { title: 'Techfive - Phụ kiện điện thoại chính hãng' }
  },
  // Auth pages (standalone - no layout)
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/cilent/Login.vue'),
    meta: { title: 'Đăng nhập', guestOnly: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/cilent/Register.vue'),
    meta: { title: 'Đăng ký', guestOnly: true }
  },
  {
    path: '/account',
    name: 'UserProfile',
    component: () => import('@/views/cilent/UserProfile.vue'),
    meta: { title: 'Tài khoản của tôi', requiresAuth: true }
  },
  {
    path: '/profile/edit',
    name: 'ProfileEdit',
    component: () => import('@/views/cilent/ProfileEdit.vue'),
    meta: { title: 'Chỉnh sửa thông tin', requiresAuth: true }
  },
  {
    path: '/profile/change-password',
    name: 'ChangePassword',
    component: () => import('@/views/cilent/ChangePassword.vue'),
    meta: { title: 'Đổi mật khẩu', requiresAuth: true }
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: () => import('@/views/cilent/ForgotPassword.vue'),
    meta: { title: 'Quên mật khẩu', guestOnly: true }
  },
  {
    path: '/reset-password/:token',
    name: 'ResetPassword',
    component: () => import('@/views/cilent/ResetPassword.vue'),
    props: true,
    meta: { title: 'Đặt lại mật khẩu', guestOnly: true }
  },
  // Other pages use MainLayout
  {
    path: '/',
    component: MainLayout,
    children: [
      // Sản phẩm giảm giá
      {
        path: 'promotions',
        name: 'Promotions',
        component: () => import('@/views/cilent/Promotions.vue'),
        meta: { title: 'Sản phẩm giảm giá' }
      },
      // Danh sách sản phẩm (phải nằm TRƯỚC chi tiết)
      {
        path: 'products',
        name: 'Products',
        component: () => import('@/views/cilent/Products.vue'),
        meta: { title: 'Tất cả sản phẩm' }
      },

      // Chi tiết sản phẩm - dùng :slug (đặt SAU danh mục)
      {
        path: 'products/:slug',
        name: 'ProductDetail',
        component: () => import('@/views/cilent/ProductDetail.vue'),
        props: true,
        meta: { title: 'Chi tiết sản phẩm' }
      },

      // Các trang khác
      {
        path: 'cart',
        name: 'Cart',
        component: () => import('@/views/cilent/CartView.vue'),
        meta: { title: 'Giỏ hàng' }
      },
      {
        path: 'news',
        name: 'News',
        component: () => import('@/views/cilent/News.vue'),
        meta: { title: 'Tin tức & Bài viết' }
      },
      // Chi tiết tin tức - phải đặt SAU danh sách news
      {
        path: 'news/:slug',
        name: 'NewsDetail',
        component: () => import('@/views/cilent/NewsDetail.vue'),
        props: true,
        meta: { title: 'Chi tiết tin tức' }
      },
      {
        path: 'contact',
        name: 'Contact',
        component: () => import('@/views/cilent/Contact.vue'),
        meta: { title: 'Liên hệ' }
      },
      { path: 'checkout', name: 'Checkout', component: () => import('@/views/cilent/CheckoutView.vue'), meta: { title: 'Thanh toán' } },
      { path: 'payment-pending', name: 'PaymentPending', component: () => import('@/views/cilent/PaymentPending.vue'), meta: { title: 'Chờ thanh toán VietQR' } },
      { path: 'order-success', name: 'OrderSuccess', component: () => import('@/views/cilent/OrderSuccess.vue'), meta: { title: 'Đặt hàng thành công' } },
      { path: 'track-order', name: 'TrackOrder', component: () => import('@/views/cilent/TrackOrder.vue'), meta: { title: 'Tra cứu đơn hàng' } },

      { path: 'orders/:id', name: 'OrderDetail', component: () => import('@/views/cilent/OrderDetail.vue'), meta: { title: 'Chi tiết đơn hàng', requiresAuth: true } }
    ]
  }
]

// Admin routes (prefix /admin, cần quyền admin)
const adminRoutes = [
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('@/views/admin/AdminLoginView.vue'),
    meta: { title: 'Admin Login' }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      { path: '', name: 'AdminDashboard', component: () => import('@/views/admin/AdminDashboardView.vue'), meta: { title: 'Dashboard' } },
      { path: 'products', name: 'AdminProducts', component: () => import('@/views/admin/AdminProductsView.vue'), meta: { title: 'Quản lý sản phẩm' } },
      { path: 'categories', name: 'AdminCategories', component: () => import('@/views/admin/AdminCategoriesView.vue'), meta: { title: 'Quản lý danh mục' } },
      { path: 'orders', name: 'AdminOrders', component: () => import('@/views/admin/AdminOrdersView.vue'), meta: { title: 'Quản lý đơn hàng' } },
      { path: 'users', name: 'AdminUsers', component: () => import('@/views/admin/AdminUsersView.vue'), meta: { title: 'Quản lý người dùng' } },
      { path: 'comments', name: 'AdminComments', component: () => import('@/views/admin/AdminCommentsView.vue'), meta: { title: 'Quản lý đánh giá' } },
      { path: 'news', name: 'AdminNews', component: () => import('@/views/admin/AdminNewsView.vue'), meta: { title: 'Quản lý tin tức' } },
      { path: 'contacts', name: 'AdminContacts', component: () => import('@/views/admin/AdminContactsView.vue'), meta: { title: 'Quản lý liên hệ' } },
      { path: 'admins', name: 'AdminAdmins', component: () => import('@/views/admin/AdminAdminsView.vue'), meta: { title: 'Quản lý admin' } },
      { path: 'vouchers', name: 'AdminVouchers', component: () => import('@/views/admin/AdminVouchersView.vue'), meta: { title: 'Quản lý mã giảm giá' } },
    ]
  }
]

// Social Login Callback (popup handler)
const socialCallbackRoute = {
  path: '/social-callback',
  name: 'SocialCallback',
  component: () => import('@/views/cilent/SocialCallback.vue'),
  meta: { title: 'Đang xử lý đăng nhập...' }
}

// 404 Not Found
const notFoundRoute = {
  path: '/:pathMatch(.*)*',
  name: 'NotFound',
  component: () => import('@/views/cilent/NotFoundView.vue'),
  meta: { title: 'Không tìm thấy trang' }
}

const routes = [
  ...publicRoutes,
  ...adminRoutes,
  socialCallbackRoute,
  notFoundRoute
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  }
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  // Cập nhật title
  document.title = to.meta.title ? `${to.meta.title} | FiveTech` : 'FiveTech'

  // Kiểm tra admin auth
  const adminAuth = useAdminAuthStore()
  adminAuth.init()
  const isAdmin = adminAuth.isAuthenticated

  if (to.meta.requiresAdmin && !isAdmin) {
    return next({ name: 'AdminLogin' })
  }

  if (to.meta.requiresAdmin && isAdmin && to.name === 'AdminLogin') {
    return next({ name: 'AdminDashboard' })
  }

  // Kiểm tra user auth
  if (to.meta.requiresAuth && !to.meta.requiresAdmin) {
    const userToken = localStorage.getItem('token')
    if (!userToken) {
      return next({ name: 'Login', query: { redirect: to.fullPath } })
    }
  }

  // Chặn trang guestOnly khi đã đăng nhập
  if (to.meta.guestOnly && localStorage.getItem('token')) {
    return next({ name: 'UserProfile' })
  }

  next()
})

export default router

