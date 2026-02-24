<template>
  <div class="home-page">
    <!-- HEADER -->
    <HomeHeader />

    <!-- HERO SECTION -->
    <HomeHero />

    <!-- PHỤ KIỆN BÁN CHẠY -->
    <section class="product-section">
      <div class="section-container">
        <div class="section-header">
          <div class="section-title-wrapper">
            <h2 class="section-title">Phụ kiện bán chạy</h2>
            <p class="section-subtitle">Sản phẩm được khách hàng yêu thích nhất</p>
          </div>
          <router-link to="/products" class="view-all-link">Xem tất cả →</router-link>
        </div>

        <div class="products-grid" v-if="bestSellers.length">
          <router-link
            v-for="product in bestSellers"
            :key="product.product_id"
            :to="`/products/${product.slug}`"
            class="product-card-link"
          >
            <article class="product-card">
              <span class="product-badge badge-hot">Hot</span>
              <div class="product-image-wrapper">
                <img
                  :src="product.variants?.[0]?.image_urls?.[0] || 'https://via.placeholder.com/400?text=' + product.name"
                  :alt="product.name"
                  class="product-image"
                />
                <button
                  class="wishlist-btn"
                  :class="{ active: isInWishlist(product.product_id) }"
                  @click.stop="toggleWishlist(product.product_id)"
                  title="Yêu thích"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                  </svg>
                </button>
              </div>
              <div class="product-info">
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-price">
                  <span class="current-price">{{ formatPrice(product.discount_price || product.base_price) }}đ</span>
                  <span v-if="product.discount_price" class="old-price">{{ formatPrice(product.base_price) }}đ</span>
                </div>
                <div class="product-rating">
                  <span class="stars">★★★★★</span>
                  <span class="review-count">(128)</span>
                </div>
                <button class="add-to-cart-btn" @click.stop="addToCart(product)">Thêm vào giỏ</button>
              </div>
            </article>
          </router-link>
        </div>
        <p v-else-if="loading">Đang tải sản phẩm...</p>
        <p v-else>Không có sản phẩm nào</p>
      </div>
    </section>

    <!-- BANNER QUẢNG CÁO 1 -->
    <PromoBanner />

    <!-- PHỤ KIỆN MỚI VỀ -->
    <section class="product-section section-alt">
      <div class="section-container">
        <div class="section-header">
          <div class="section-title-wrapper">
            <h2 class="section-title">Phụ kiện mới về</h2>
            <p class="section-subtitle">Cập nhật những sản phẩm mới nhất</p>
          </div>
          <router-link to="/products?filter=new" class="view-all-link">Xem tất cả →</router-link>
        </div>

        <div class="products-grid" v-if="newArrivals.length">
          <router-link
            v-for="product in newArrivals"
            :key="product.product_id"
            :to="`/products/${product.slug}`"
            class="product-card-link"
          >
            <article class="product-card">
              <span class="product-badge badge-new">New</span>
              <div class="product-image-wrapper">
                <img
                  :src="product.variants?.[0]?.image_urls?.[0] || 'https://via.placeholder.com/400?text=' + product.name"
                  :alt="product.name"
                  class="product-image"
                />
              </div>
              <div class="product-info">
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-price">
                  <span class="current-price">{{ formatPrice(product.discount_price || product.base_price) }}đ</span>
                </div>
                <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(12)</span></div>
                <button class="add-to-cart-btn" @click.stop="addToCart(product)">Thêm vào giỏ</button>
              </div>
            </article>
          </router-link>
        </div>
        <p v-else-if="loading">Đang tải...</p>
      </div>
    </section>

    <!-- BANNER 2 -->
    <section class="promo-banner">
      <div class="banner-container">
        <div class="banner-content banner-reverse">
          <div class="banner-image">
            <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&h=400&fit=crop" alt="Tai nghe"/>
            <div class="banner-discount"><span class="discount-percent">40%</span><span class="discount-text">OFF</span></div>
          </div>
          <div class="banner-text">
            <span class="banner-tag">🎧 Flash Sale</span>
            <h2 class="banner-title">Tai nghe Bluetooth giảm sốc</h2>
            <p class="banner-description">Ưu đãi có hạn cho các dòng tai nghe chống ồn cao cấp.</p>
            <router-link to="/promotions" class="banner-btn">Mua ngay</router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- PHỤ KIỆN KHUYẾN MÃI -->
    <section class="product-section">
      <div class="section-container">
        <div class="section-header">
          <div class="section-title-wrapper">
            <h2 class="section-title">🔥 Phụ kiện khuyến mãi</h2>
            <p class="section-subtitle">Giảm giá sốc - Số lượng có hạn</p>
          </div>
          <router-link to="/promotions" class="view-all-link">Xem tất cả →</router-link>
        </div>

        <div class="products-grid" v-if="onSale.length">
          <router-link
            v-for="product in onSale"
            :key="product.product_id"
            :to="`/products/${product.slug}`"
            class="product-card-link"
          >
            <article class="product-card">
              <span class="product-badge badge-sale">-{{ Math.round((1 - (product.discount_price / product.base_price)) * 100) }}%</span>
              <div class="product-image-wrapper">
                <img
                  :src="product.variants?.[0]?.image_urls?.[0] || 'https://via.placeholder.com/400?text=' + product.name"
                  :alt="product.name"
                  class="product-image"
                />
              </div>
              <div class="product-info">
                <h3 class="product-name">{{ product.name }}</h3>
                <div class="product-price">
                  <span class="current-price">{{ formatPrice(product.discount_price) }}đ</span>
                  <span class="old-price">{{ formatPrice(product.base_price) }}đ</span>
                </div>
                <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(56)</span></div>
                <button class="add-to-cart-btn" @click.stop="addToCart(product)">Thêm vào giỏ</button>
              </div>
            </article>
          </router-link>
        </div>
        <p v-else-if="loading">Đang tải...</p>
      </div>
    </section>

    <!-- BANNER 3 -->
    <section class="promo-banner">
      <div class="banner-container">
        <div class="banner-content">
          <div class="banner-text">
            <span class="banner-tag">🚚 Miễn phí vận chuyển</span>
            <h2 class="banner-title">Freeship đơn từ 300K toàn quốc</h2>
            <p class="banner-description">Áp dụng cho tất cả đơn hàng từ 300.000đ. Giao hàng nhanh 2-4 tiếng nội thành.</p>
            <router-link to="/products" class="banner-btn">Mua sắm ngay</router-link>
          </div>
          <div class="banner-image">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&h=400&fit=crop" alt="Freeship"/>
          </div>
        </div>
      </div>
    </section>

    <!-- BÀI VIẾT -->
    <BlogSection />

    <!-- Ý KIẾN KHÁCH HÀNG -->
    <TestimonialsSection />

    <!-- FOOTER -->
    <HomeFooter />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'  // axios instance với baseURL + interceptor token

import HomeHeader from '@/components/home/HomeHeader.vue'
import HomeHero from '@/components/home/HomeHero.vue'
import PromoBanner from '@/components/home/PromoBanner.vue'
import BlogSection from '@/components/home/BlogSection.vue'
import TestimonialsSection from '@/components/home/TestimonialsSection.vue'
import HomeFooter from '@/components/home/HomeFooter.vue'

// Data
const bestSellers = ref([])
const newArrivals = ref([])
const onSale = ref([])
const wishlist = ref([]) // array IDs sản phẩm yêu thích
const loading = ref(true)
const error = ref(null)

// Helpers
const formatPrice = (price) => {
  return new Intl.NumberFormat('vi-VN').format(price) + 'đ'
}

const isInWishlist = (id) => wishlist.value.includes(id)

const toggleWishlist = async (productId) => {
  if (!api.defaults.headers.common['Authorization']) {
    alert('Vui lòng đăng nhập để thêm yêu thích!')
    return router.push('/login')
  }

  try {
    if (isInWishlist(productId)) {
      await api.delete(`/wishlist/remove/${productId}`)
      wishlist.value = wishlist.value.filter(id => id !== productId)
    } else {
      await api.post(`/wishlist/add/${productId}`)
      wishlist.value.push(productId)
    }
  } catch (err) {
    console.error(err)
    alert('Có lỗi xảy ra khi cập nhật yêu thích')
  }
}

const addToCart = async (product) => {
  if (!localStorage.getItem('token')) {
    alert('Vui lòng đăng nhập để thêm vào giỏ!')
    return router.push('/login')
  }

  try {
    // Gọi CSRF trước POST
    await api.get('/sanctum/csrf-cookie')

    const variant = product.variants?.[0]
    if (!variant) return alert('Sản phẩm không có biến thể khả dụng')

    await api.post('/cart/add', {
      variant_id: variant.variant_id,
      quantity: 1
    })

    alert('Đã thêm vào giỏ hàng!')
  } catch (err) {
    console.error(err)
    if (err.response?.status === 419 || err.response?.data?.message?.includes('CSRF')) {
      alert('CSRF token không hợp lệ. Vui lòng thử lại.')
    } else {
      alert('Có lỗi xảy ra')
    }
  }
}
// Fetch data khi mount
onMounted(async () => {
  loading.value = true
  error.value = null

  try {
    // Fetch best sellers
    const resBest = await api.get('/products', { params: { filter: 'hot', per_page: 8 } })
    bestSellers.value = resBest.data.data || resBest.data

    // New arrivals
    const resNew = await api.get('/products', { params: { filter: 'new', per_page: 8 } })
    newArrivals.value = resNew.data.data || resNew.data

    // On sale
    const resSale = await api.get('/products', { params: { filter: 'sale', per_page: 8 } })
    onSale.value = resSale.data.data || resSale.data

    // Fetch wishlist nếu đã login
    if (localStorage.getItem('token')) {
      const resWish = await api.get('/wishlist')
      wishlist.value = resWish.data.map(item => item.product_id)
    }
  } catch (err) {
    console.error('Lỗi fetch dữ liệu:', err)
    error.value = 'Không thể tải dữ liệu. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
})

const router = useRouter()
</script>

<style scoped>
/* ================= IMPORT GOOGLE FONTS ================= */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

/* ================= BASE STYLES ================= */
.home-page {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #0f172a;
  background: #f8fafc;
  line-height: 1.6;
}

/* ================= PRODUCT SECTION ================= */
.product-section { padding: 80px 0; background: #f8fafc; }
.product-section.section-alt { background: #ffffff; }
.section-container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }

.section-header {
  display: flex; justify-content: space-between; align-items: flex-end;
  margin-bottom: 48px; gap: 24px;
}
.section-title {
  font-size: 36px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;
  position: relative; display: inline-block;
}
.section-title::after {
  content: ''; position: absolute; left: 0; bottom: -8px;
  width: 60px; height: 4px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border-radius: 2px;
}
.section-subtitle { font-size: 16px; color: #64748b; margin: 16px 0 0 0; }
.view-all-link {
  display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%); color: #ffffff;
  text-decoration: none; font-weight: 600; font-size: 14px; border-radius: 10px;
  transition: all 0.3s ease;
}
.view-all-link:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(10, 22, 40, 0.3); }

/* ================= PRODUCTS GRID ================= */
.products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }

/* ================= PRODUCT CARD ================= */
.product-card {
  position: relative; background: #ffffff; border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #f1f5f9;
}
.product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12); }
.product-badge {
  position: absolute; top: 16px; left: 16px; z-index: 10; padding: 6px 14px;
  font-size: 12px; font-weight: 700; text-transform: uppercase; border-radius: 50px;
}
.badge-hot { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: #ffffff; box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4); }
.badge-new { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
.badge-sale { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); }
.product-image-wrapper { position: relative; width: 100%; padding-top: 100%; overflow: hidden; background: #f8fafc; }
.product-image { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.product-card:hover .product-image { transform: scale(1.08); }

/* Nút yêu thích */
.wishlist-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 40px;
  height: 40px;
  min-width: 40px;
  min-height: 40px;
  aspect-ratio: 1 / 1;
  padding: 0;
  border: none;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #475569;
  z-index: 20;
}

.wishlist-btn:hover {
  transform: scale(1.15);
  background: #fef2f2;
  color: #ef4444;
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.25);
}

.wishlist-btn:hover svg {
  fill: #ef4444;
}

.wishlist-btn.active {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}

.wishlist-btn.active svg {
  fill: white;
}

.wishlist-btn svg {
  width: 18px;
  height: 18px;
  transition: all 0.3s ease;
}
.product-info { padding: 20px; }
.product-name { font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 12px 0; line-height: 1.4; min-height: 44px; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-price { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.current-price { font-size: 20px; font-weight: 800; color: #ff6b35; }
.old-price { font-size: 14px; color: #94a3b8; text-decoration: line-through; }
.product-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
.stars { color: #fbbf24; font-size: 14px; }
.review-count { font-size: 13px; color: #94a3b8; }
.add-to-cart-btn {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 14px 20px; background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  color: #ffffff; border: none; border-radius: 12px; font-weight: 600; font-size: 14px;
  cursor: pointer; transition: all 0.3s ease;
}
.add-to-cart-btn:hover { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3); transform: translateY(-2px); }

/* ================= PROMO BANNER ================= */
.promo-banner { padding: 40px 0; background: #f8fafc; }
.banner-container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
.banner-content {
  display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 60px;
  padding: 60px 80px; background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  border-radius: 30px; overflow: hidden;
}
.banner-reverse { direction: rtl; }
.banner-reverse > * { direction: ltr; }
.banner-tag {
  display: inline-block; padding: 8px 20px; background: rgba(255, 107, 53, 0.2);
  border: 1px solid rgba(255, 107, 53, 0.3); border-radius: 50px;
  font-size: 14px; font-weight: 600; color: #ff6b35; margin-bottom: 20px;
}
.banner-title { font-size: 40px; font-weight: 800; color: #ffffff; line-height: 1.2; margin: 0 0 16px 0; }
.banner-description { font-size: 16px; color: #94a3b8; line-height: 1.7; margin: 0 0 28px 0; }
.banner-btn {
  display: inline-flex; align-items: center; gap: 10px; padding: 16px 32px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: #ffffff;
  text-decoration: none; font-weight: 700; font-size: 16px; border-radius: 12px;
  transition: all 0.3s ease; box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
}
.banner-btn:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(255, 107, 53, 0.5); }
.banner-image { position: relative; }
.banner-image img { width: 100%; height: 320px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3); }
.banner-discount {
  position: absolute; top: -20px; right: -20px; width: 100px; height: 100px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border-radius: 50%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  box-shadow: 0 10px 30px rgba(255, 107, 53, 0.5);
}
.discount-percent { font-size: 28px; font-weight: 800; color: #ffffff; line-height: 1; }
.discount-text { font-size: 14px; font-weight: 700; color: rgba(255, 255, 255, 0.9); }

/* ================= RESPONSIVE ================= */
@media (max-width: 1200px) {
  .products-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 1024px) {
  .products-grid { grid-template-columns: repeat(2, 1fr); }
  .banner-content { grid-template-columns: 1fr; padding: 50px; text-align: center; }
  .banner-reverse { direction: ltr; }
  .section-header { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 640px) {
  .product-section { padding: 60px 0; }
  .section-title { font-size: 28px; }
  .products-grid { grid-template-columns: 1fr; gap: 20px; }
  .banner-content { padding: 40px 24px; border-radius: 20px; }
  .banner-title { font-size: 26px; }
  .banner-image img { height: 200px; }
  .product-info { padding: 16px; }
  .product-name { font-size: 15px; min-height: auto; }
  .current-price { font-size: 18px; }
}
.loading-text, .empty-text {
  text-align: center;
  padding: 40px 0;
  color: #64748b;
  font-size: 16px;
}

.product-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
  border-radius: 20px; /* đồng bộ với card */
  overflow: hidden; /* để badge không tràn */
}

/* Hover effect khi hover vào link (tăng trải nghiệm) */
.product-card-link:hover .product-card {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.product-card-link:hover .product-image {
  transform: scale(1.08);
}

/* Đảm bảo nút con không bị ảnh hưởng */
.add-to-cart-btn,
.wishlist-btn {
  position: relative;
  z-index: 10;
}
</style>
