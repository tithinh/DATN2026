<template>
  <section class="testimonials-section">
    <div class="section-container">
      <div class="section-header">
        <h2 class="section-title">Ý kiến khách hàng</h2>
        <p class="section-subtitle">Hàng nghìn khách hàng tin tưởng Techfive</p>
      </div>
      <div class="testimonials-grid">
        <!-- Testimonial 1 -->
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="customer-avatar">
              <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop" alt="Avatar"/>
            </div>
            <div class="customer-info">
              <h4 class="customer-name">Nguyễn Văn An</h4>
              <span class="customer-title">Khách hàng VIP</span>
            </div>
          </div>
          <div class="testimonial-rating">
            <span class="star">★</span><span class="star">★</span><span class="star">★</span>
            <span class="star">★</span><span class="star">★</span>
          </div>
          <p class="testimonial-content">
            "Sản phẩm chất lượng, giao hàng nhanh. Mình đã mua ốp lưng và cáp sạc, rất hài lòng!"
          </p>
          <span class="testimonial-date">20/01/2026</span>
        </div>
        <!-- Testimonial 2 -->
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="customer-avatar">
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop" alt="Avatar"/>
            </div>
            <div class="customer-info">
              <h4 class="customer-name">Trần Thị Mai</h4>
              <span class="customer-title">Khách hàng thân thiết</span>
            </div>
          </div>
          <div class="testimonial-rating">
            <span class="star">★</span><span class="star">★</span><span class="star">★</span>
            <span class="star">★</span><span class="star">★</span>
          </div>
          <p class="testimonial-content">
            "Giá cả hợp lý, nhân viên tư vấn nhiệt tình. Đã giới thiệu cho bạn bè!"
          </p>
          <span class="testimonial-date">18/01/2026</span>
        </div>
        <!-- Testimonial 3 -->
        <div class="testimonial-card">
          <div class="testimonial-header">
            <div class="customer-avatar">
              <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop" alt="Avatar"/>
            </div>
            <div class="customer-info">
              <h4 class="customer-name">Lê Hoàng Nam</h4>
              <span class="customer-title">Khách hàng mới</span>
            </div>
          </div>
          <div class="testimonial-rating">
            <span class="star">★</span><span class="star">★</span><span class="star">★</span>
            <span class="star">★</span><span class="star half">★</span>
          </div>
          <p class="testimonial-content">
            "Lần đầu mua hàng tại Techfive, rất ấn tượng với dịch vụ. Sẽ quay lại!"
          </p>
          <span class="testimonial-date">15/01/2026</span>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.testimonials-section { padding: 80px 0; background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%); }
.section-container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
.section-header { text-align: center; margin-bottom: 48px; }
.section-title { font-size: 36px; font-weight: 800; color: #ffffff; margin: 0 0 12px 0; }
.section-subtitle { font-size: 16px; color: #94a3b8; margin: 0; }
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.testimonial-card {
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px; padding: 30px; transition: all 0.3s ease;
}
.testimonial-card:hover { background: rgba(255,255,255,0.08); transform: translateY(-5px); }
.testimonial-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
.customer-avatar img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #ff6b35; }
.customer-name { font-size: 16px; font-weight: 700; color: #ffffff; margin: 0; }
.customer-title { font-size: 13px; color: #64748b; }
.testimonial-rating { margin-bottom: 16px; }
.star { color: #fbbf24; font-size: 18px; }
.testimonial-content { font-size: 15px; color: #cbd5e1; line-height: 1.7; margin: 0 0 16px 0; }
.testimonial-date { font-size: 13px; color: #64748b; }
@media (max-width: 1024px) { .testimonials-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) {
  .testimonials-section { padding: 60px 0; }
  .section-title { font-size: 28px; }
  .testimonials-grid { grid-template-columns: 1fr; }
}
</style>
