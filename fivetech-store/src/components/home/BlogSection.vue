<template>
  <section class="blog-section">
    <div class="section-container">
      <div class="section-header">
        <div class="section-title-wrapper">
          <h2 class="section-title">Tin tức & Bài viết</h2>
          <p class="section-subtitle">Cập nhật xu hướng công nghệ mới nhất</p>
        </div>
        <a href="/news" class="view-all-link">Xem tất cả</a>
      </div>
      <div class="blog-grid">
        <!-- Blog Post 1 -->
        <article class="blog-card">
          <div class="blog-image">
            <img src="https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=400&h=250&fit=crop" alt="Blog"/>
            <span class="blog-category">Đánh giá</span>
          </div>
          <div class="blog-content">
            <h3 class="blog-title">Top 10 ốp lưng iPhone 15 Pro Max đáng mua nhất 2024</h3>
            <p class="blog-excerpt">Khám phá những mẫu ốp lưng đẹp, bền, bảo vệ tốt nhất cho iPhone 15 Pro Max.</p>
            <div class="blog-meta">
              <span class="blog-date">20/01/2026</span>
              <span class="blog-read-time">5 phút đọc</span>
            </div>
          </div>
        </article>
        <!-- Blog Post 2 -->
        <article class="blog-card">
          <div class="blog-image">
            <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=250&fit=crop" alt="Blog"/>
            <span class="blog-category">Hướng dẫn</span>
          </div>
          <div class="blog-content">
            <h3 class="blog-title">Cách chọn tai nghe Bluetooth phù hợp với nhu cầu</h3>
            <p class="blog-excerpt">Hướng dẫn chi tiết giúp bạn chọn tai nghe không dây phù hợp nhất.</p>
            <div class="blog-meta">
              <span class="blog-date">18/01/2026</span>
              <span class="blog-read-time">4 phút đọc</span>
            </div>
          </div>
        </article>
        <!-- Blog Post 3 -->
        <article class="blog-card">
          <div class="blog-image">
            <img src="https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=250&fit=crop" alt="Blog"/>
            <span class="blog-category">Tin tức</span>
          </div>
          <div class="blog-content">
            <h3 class="blog-title">Apple ra mắt chuẩn sạc nhanh mới cho iPhone 16</h3>
            <p class="blog-excerpt">Tìm hiểu về công nghệ sạc nhanh mới nhất từ Apple.</p>
            <div class="blog-meta">
              <span class="blog-date">15/01/2026</span>
              <span class="blog-read-time">3 phút đọc</span>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<style scoped>
.blog-section { padding: 80px 0; background: #f8fafc; }
.section-container { max-width: 1400px; margin: 0 auto; padding: 0 24px; }
.section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 48px; }
.section-title { font-size: 36px; font-weight: 800; color: #0f172a; margin: 0; }
.section-subtitle { font-size: 16px; color: #64748b; margin: 8px 0 0 0; }
.view-all-link { color: #ff6b35; font-weight: 600; text-decoration: none; }
.blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.blog-card {
  background: #ffffff; border-radius: 20px; overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06); transition: all 0.3s ease;
}
.blog-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
.blog-image { position: relative; overflow: hidden; }
.blog-image img { width: 100%; height: 200px; object-fit: cover; transition: transform 0.5s; }
.blog-card:hover .blog-image img { transform: scale(1.1); }
.blog-category {
  position: absolute; top: 16px; left: 16px; padding: 6px 14px;
  background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff;
  font-size: 12px; font-weight: 600; border-radius: 50px;
}
.blog-content { padding: 24px; }
.blog-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 12px 0; line-height: 1.4; }
.blog-excerpt { font-size: 14px; color: #64748b; margin: 0 0 16px 0; line-height: 1.6; }
.blog-meta { display: flex; gap: 16px; font-size: 13px; color: #94a3b8; }
@media (max-width: 1024px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) {
  .blog-section { padding: 60px 0; }
  .section-title { font-size: 28px; }
  .blog-grid { grid-template-columns: 1fr; }
}
</style>
