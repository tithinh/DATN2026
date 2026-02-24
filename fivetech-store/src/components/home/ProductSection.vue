<template>
  <section class="product-section">
    <div class="section-container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="section-title-wrapper">
          <h2 class="section-title">
            <slot name="title">Sản phẩm nổi bật</slot>
          </h2>
          <p class="section-subtitle">
            <slot name="subtitle">Những sản phẩm được yêu thích nhất</slot>
          </p>
        </div>
        <a href="/products" class="view-all-link">
          Xem tất cả
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"></path>
            <path d="m12 5 7 7-7 7"></path>
          </svg>
        </a>
      </div>

      <!-- Products Grid -->
      <div class="products-grid">
        <slot name="products">
          <!-- Default products will be passed through slot -->
        </slot>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* ================= PRODUCT SECTION ================= */
.product-section {
  padding: 80px 0;
  background: #f8fafc;
}

.section-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 24px;
}

/* Section Header */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 48px;
  gap: 24px;
}

.section-title-wrapper {
  flex: 1;
}

.section-title {
  font-size: 36px;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 8px 0;
  position: relative;
  display: inline-block;
}

.section-title::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -8px;
  width: 60px;
  height: 4px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border-radius: 2px;
}

.section-subtitle {
  font-size: 16px;
  color: #64748b;
  margin: 16px 0 0 0;
}

.view-all-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  color: #ffffff;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.view-all-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(10, 22, 40, 0.3);
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

/* ================= RESPONSIVE ================= */
@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 640px) {
  .product-section {
    padding: 60px 0;
  }

  .section-title {
    font-size: 28px;
  }

  .products-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}
</style>
