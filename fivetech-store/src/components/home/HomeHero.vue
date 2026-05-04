<template>
  <section class="hero-section">
    <!-- Background Overlay -->
    <div class="hero-overlay"></div>

    <!-- Decorative Elements -->
    <div class="hero-decoration hero-decoration-1"></div>
    <div class="hero-decoration hero-decoration-2"></div>
    <div class="hero-decoration hero-decoration-3"></div>

    <div class="hero-content">
      <div class="hero-text">
        <h1 class="hero-title">
          Phụ Kiện Điện Thoại
          <span class="hero-title-accent">Chính Hãng 100%</span>
        </h1>
        <p class="hero-description">
          Techfive - Điểm đến tin cậy cho mọi phụ kiện điện thoại.
          Giá tốt nhất, giao hàng nhanh 2-4 tiếng nội thành.
        </p>
        <div class="hero-buttons">
          <a href="/products" class="hero-btn hero-btn-primary">
            <span>Mua ngay</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14"></path>
              <path d="m12 5 7 7-7 7"></path>
            </svg>
          </a>
          <a href="/promotions" class="hero-btn hero-btn-secondary">
            Xem ưu đãi
          </a>
        </div>
      </div>
      <div class="hero-image">
        <div class="hero-image-wrapper">
          <img src="https://images.unsplash.com/photo-1606229365485-93a3b8ee0385?w=600&h=700&fit=crop" alt="Phụ kiện điện thoại" />
          <div class="hero-image-badge">
            <span class="discount">-30%</span>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<style scoped>
/* ================= HERO SECTION ================= */
.hero-section {
  position: relative;
  height: 600px;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 50%, #0f2847 100%);
  overflow: hidden;
  display: flex;
  align-items: center;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(255, 107, 53, 0.15) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(247, 147, 30, 0.1) 0%, transparent 50%);
}

/* Decorative Elements */
.hero-decoration {
  position: absolute;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(255, 107, 53, 0.3), rgba(247, 147, 30, 0.1));
  filter: blur(60px);
  animation: float 8s ease-in-out infinite;
}

.hero-decoration-1 {
  width: 400px;
  height: 400px;
  top: -100px;
  right: -100px;
  animation-delay: 0s;
}

.hero-decoration-2 {
  width: 300px;
  height: 300px;
  bottom: -50px;
  left: -50px;
  animation-delay: 2s;
}

.hero-decoration-3 {
  width: 200px;
  height: 200px;
  top: 50%;
  left: 30%;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-30px) scale(1.05); }
}

.hero-content {
  position: relative;
  z-index: 10;
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
  padding: 0 24px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}

/* Hero Text */
.hero-text {
  color: #ffffff;
}

.hero-badge {
  display: inline-block;
  padding: 8px 20px;
  background: linear-gradient(135deg, rgba(255, 107, 53, 0.2), rgba(247, 147, 30, 0.2));
  border: 1px solid rgba(255, 107, 53, 0.3);
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  color: #ff6b35;
  margin-bottom: 24px;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(255, 107, 53, 0.4); }
  50% { box-shadow: 0 0 20px 10px rgba(255, 107, 53, 0); }
}

.hero-title {
  font-size: 52px;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 24px;
  letter-spacing: -1px;
}

.hero-title-accent {
  display: block;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-description {
  font-size: 18px;
  line-height: 1.7;
  color: #94a3b8;
  margin-bottom: 32px;
  max-width: 500px;
}

/* Hero Buttons */
.hero-buttons {
  display: flex;
  gap: 16px;
  margin-bottom: 48px;
}

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.hero-btn-primary {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
  box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
}

.hero-btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(255, 107, 53, 0.5);
}

.hero-btn-secondary {
  background: transparent;
  color: #ffffff;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.hero-btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.5);
}

/* Hero Stats */
.hero-stats {
  display: flex;
  gap: 40px;
}

.hero-stat {
  display: flex;
  flex-direction: column;
}

.hero-stat-number {
  font-size: 28px;
  font-weight: 800;
  color: #ffffff;
}

.hero-stat-label {
  font-size: 14px;
  color: #64748b;
}

/* Hero Image */
.hero-image {
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-image-wrapper {
  position: relative;
  width: 450px;
  height: 500px;
  border-radius: 30px;
  overflow: hidden;
  box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
  transform: rotate(3deg);
  transition: transform 0.5s ease;
}

.hero-image-wrapper:hover {
  transform: rotate(0deg) scale(1.02);
}

.hero-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hero-image-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  padding: 12px 20px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
}

.hero-image-badge .discount {
  display: block;
  font-size: 24px;
  font-weight: 800;
  color: #ffffff;
}

.hero-image-badge .text {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.9);
}

/* ================= RESPONSIVE ================= */
@media (max-width: 1024px) {
  .hero-content {
    grid-template-columns: 1fr;
    text-align: center;
    padding-top: 60px;
  }

  .hero-text {
    order: 1;
  }

  .hero-image {
    display: none;
  }

  .hero-title {
    font-size: 40px;
  }

  .hero-description {
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
  }

  .hero-buttons {
    justify-content: center;
  }

  .hero-stats {
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .hero-section {
    height: auto;
    min-height: 500px;
    padding: 60px 0;
  }

  .hero-title {
    font-size: 32px;
  }

  .hero-description {
    font-size: 16px;
  }

  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }

  .hero-btn {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }

  .hero-stats {
    flex-wrap: wrap;
    gap: 24px;
  }
}
</style>
