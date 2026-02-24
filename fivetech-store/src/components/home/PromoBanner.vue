<template>
  <section class="promo-banner">
    <div class="banner-container">
      <div class="banner-content">
        <div class="banner-text">
          <span class="banner-tag">🎁 Ưu đãi đặc biệt</span>
          <h2 class="banner-title">Giảm đến 50% Phụ kiện iPhone</h2>
          <p class="banner-description">
            Áp dụng cho tất cả ốp lưng, cáp sạc, kính cường lực iPhone 15 Series.
          </p>
          <a href="/promotions" class="banner-btn">
            <span>Mua ngay</span>
          </a>
        </div>
        <div class="banner-image">
          <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=600&h=400&fit=crop" alt="Khuyến mãi"/>
          <div class="banner-discount">
            <span class="discount-percent">50%</span>
            <span class="discount-text">OFF</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.promo-banner { padding: 40px 0; background: #f8fafc; }
.banner-container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
.banner-content {
  display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 60px;
  padding: 60px 80px; background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  border-radius: 30px; overflow: hidden;
}
.banner-tag {
  display: inline-block; padding: 8px 20px; background: rgba(255, 107, 53, 0.2);
  border: 1px solid rgba(255, 107, 53, 0.3); border-radius: 50px;
  font-size: 14px; font-weight: 600; color: #ff6b35; margin-bottom: 20px;
}
.banner-title { font-size: 40px; font-weight: 800; color: #ffffff; margin: 0 0 16px 0; }
.banner-description { font-size: 16px; color: #94a3b8; margin: 0 0 28px 0; }
.banner-btn {
  display: inline-flex; padding: 16px 32px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff; text-decoration: none; font-weight: 700; border-radius: 12px;
  transition: all 0.3s ease; box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
}
.banner-btn:hover { transform: translateY(-3px); }
.banner-image { position: relative; }
.banner-image img { width: 100%; height: 320px; object-fit: cover; border-radius: 20px; }
.banner-discount {
  position: absolute; top: -20px; right: -20px; width: 100px; height: 100px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); border-radius: 50%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.discount-percent { font-size: 28px; font-weight: 800; color: #ffffff; }
.discount-text { font-size: 14px; font-weight: 700; color: rgba(255, 255, 255, 0.9); }
@media (max-width: 1024px) {
  .banner-content { grid-template-columns: 1fr; padding: 50px; text-align: center; }
}
@media (max-width: 640px) {
  .banner-content { padding: 40px 24px; }
  .banner-title { font-size: 26px; }
  .banner-image img { height: 200px; }
}
</style>
