<template>
  <div class="contact-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <a href="/">Trang chủ</a>
          <span class="separator">/</span>
          <span class="current">Liên hệ</span>
        </nav>
        <h1 class="page-title">Liên hệ với chúng tôi</h1>
        <p class="page-subtitle">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
      </div>
    </div>

    <!-- Contact Info Cards -->
    <section class="contact-info-section">
      <div class="container">
        <div class="info-cards">
          <div class="info-card">
            <div class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <h3 class="info-title">Địa chỉ cửa hàng</h3>
            <p class="info-text">123 Đường ABC, Phường XYZ<br/>Quận 1, TP. Hồ Chí Minh</p>
          </div>
          <div class="info-card">
            <div class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <h3 class="info-title">Hotline hỗ trợ</h3>
            <p class="info-text">1900 1234 56<br/>Miễn phí cuộc gọi</p>
          </div>
          <div class="info-card">
            <div class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <h3 class="info-title">Email</h3>
            <p class="info-text">support@techfive.vn<br/>sales@techfive.vn</p>
          </div>
          <div class="info-card">
            <div class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="info-title">Giờ làm việc</h3>
            <p class="info-text">8:00 - 22:00<br/>Thứ 2 - Chủ nhật</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Map -->
    <section class="contact-main">
      <div class="container">
        <div class="contact-layout">
          <!-- Contact Form -->
          <div class="contact-form-wrapper">
            <h2 class="section-title">Gửi tin nhắn cho chúng tôi</h2>
            <p class="section-desc">Điền thông tin bên dưới, chúng tôi sẽ phản hồi trong vòng 24 giờ</p>
            
            <form class="contact-form">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Họ và tên *</label>
                  <input type="text" class="form-input" placeholder="Nhập họ và tên" />
                </div>
                <div class="form-group">
                  <label class="form-label">Email *</label>
                  <input type="email" class="form-input" placeholder="Nhập email" />
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Số điện thoại</label>
                  <input type="tel" class="form-input" placeholder="Nhập số điện thoại" />
                </div>
                <div class="form-group">
                  <label class="form-label">Chủ đề</label>
                  <select class="form-input form-select">
                    <option value="">Chọn chủ đề</option>
                    <option value="order">Đơn hàng</option>
                    <option value="product">Sản phẩm</option>
                    <option value="warranty">Bảo hành</option>
                    <option value="return">Đổi trả</option>
                    <option value="other">Khác</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                <label class="form-label">Nội dung tin nhắn *</label>
                <textarea class="form-input form-textarea" placeholder="Nhập nội dung..." rows="5"></textarea>
              </div>
              
              <button type="submit" class="submit-btn">
                <span>Gửi tin nhắn</span>
                <span class="btn-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </span>
              </button>
            </form>
          </div>

          <!-- Map -->
          <div class="map-wrapper">
            <h2 class="section-title">Bản đồ cửa hàng</h2>
            <div class="map-container">
              <div class="map-placeholder">
                <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600&h=400&fit=crop" alt="Map" class="map-image" />
                <div class="map-overlay">
                  <div class="map-marker">
                    <span class="marker-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#ef4444" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3" fill="#ffffff"/></svg>
                    </span>
                    <div class="marker-info">
                      <strong>Techfive Store</strong>
                      <span>123 Đường ABC, Q.1, TP.HCM</span>
                    </div>
                  </div>
                </div>
              </div>
              <a href="#" class="directions-btn">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" x2="9" y1="3" y2="18"/><line x1="15" x2="15" y1="6" y2="21"/></svg>
                </span>
                Xem chỉ đường trên Google Maps
              </a>
            </div>

            <!-- Store Images -->
            <div class="store-gallery">
              <h3 class="gallery-title">Hình ảnh cửa hàng</h3>
              <div class="gallery-grid">
                <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=200&fit=crop" alt="Store 1" />
                <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=300&h=200&fit=crop" alt="Store 2" />
                <img src="https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?w=300&h=200&fit=crop" alt="Store 3" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
      <div class="container">
        <h2 class="section-title-center">Câu hỏi thường gặp</h2>
        <p class="section-subtitle-center">Những thắc mắc phổ biến của khách hàng</p>
        
        <div class="faq-grid">
          <div class="faq-item">
            <div class="faq-question">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              <h4>Làm sao để theo dõi đơn hàng?</h4>
            </div>
            <p class="faq-answer">Bạn có thể theo dõi đơn hàng bằng cách đăng nhập vào tài khoản hoặc sử dụng mã đơn hàng được gửi qua email/SMS.</p>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              <span class="faq-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              </span>
              <h4>Thời gian giao hàng là bao lâu?</h4>
            </div>
            <p class="faq-answer">Nội thành TP.HCM: 2-4 tiếng. Các tỉnh thành khác: 2-5 ngày làm việc tùy khu vực.</p>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              <span class="faq-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              </span>
              <h4>Chính sách đổi trả như thế nào?</h4>
            </div>
            <p class="faq-answer">Bạn có thể đổi trả trong vòng 7 ngày nếu sản phẩm còn nguyên vẹn, chưa qua sử dụng và còn đầy đủ phụ kiện.</p>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              <span class="faq-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              </span>
              <h4>Có hỗ trợ trả góp không?</h4>
            </div>
            <p class="faq-answer">Có, chúng tôi hỗ trợ trả góp 0% lãi suất qua các ngân hàng và ví điện tử cho đơn hàng từ 3 triệu đồng.</p>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              <span class="faq-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              </span>
              <h4>Sản phẩm có bảo hành không?</h4>
            </div>
            <p class="faq-answer">Tất cả sản phẩm đều được bảo hành từ 6-12 tháng tùy theo chính sách của nhà sản xuất.</p>
          </div>
          
          <div class="faq-item">
            <div class="faq-question">
              <span class="faq-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff6b35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
              </span>
              <h4>Các hình thức thanh toán?</h4>
            </div>
            <p class="faq-answer">Chúng tôi chấp nhận: COD, chuyển khoản ngân hàng, thẻ tín dụng/ghi nợ, và các ví điện tử MoMo, ZaloPay, VNPay.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-title">Cần hỗ trợ ngay?</h2>
          <p class="cta-text">Liên hệ hotline để được tư vấn trực tiếp từ đội ngũ chuyên gia</p>
          <a href="tel:19001234" class="cta-btn">
            <span class="cta-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </span>
            <span>Gọi ngay: 1900 1234 56</span>
          </a>
        </div>
      </div>
    </section>
  </div>
</template>


