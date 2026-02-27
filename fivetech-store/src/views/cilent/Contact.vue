<template>
  <div class="contact-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
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
            
            <!-- Success Message -->
            <div v-if="submitSuccess" class="success-message">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <h3>Gửi liên hệ thành công!</h3>
              <p>Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi sớm nhất có thể.</p>
              <button class="admin-btn admin-btn-primary" @click="resetForm">Gửi liên hệ khác</button>
            </div>

            <!-- Contact Form -->
            <form v-else class="contact-form" @submit.prevent="handleSubmit">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Họ và tên *</label>
                  <input 
                    type="text" 
                    class="form-input" 
                    v-model="formData.name"
                    placeholder="Nhập họ và tên"
                    :class="{ error: errors.name }"
                  />
                  <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
                </div>
                <div class="form-group">
                  <label class="form-label">Email *</label>
                  <input 
                    type="email" 
                    class="form-input" 
                    v-model="formData.email"
                    placeholder="Nhập email"
                    :class="{ error: errors.email }"
                  />
                  <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Số điện thoại</label>
                  <input 
                    type="tel" 
                    class="form-input" 
                    v-model="formData.phone"
                    placeholder="Nhập số điện thoại"
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Chủ đề</label>
                  <select class="form-input form-select" v-model="formData.subject">
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
                <textarea 
                  class="form-input form-textarea" 
                  v-model="formData.content"
                  placeholder="Nhập nội dung..."
                  rows="5"
                  :class="{ error: errors.content }"
                ></textarea>
                <span v-if="errors.content" class="error-text">{{ errors.content }}</span>
              </div>
              
              <button type="submit" class="submit-btn" :disabled="submitting">
                <span v-if="submitting">Đang gửi...</span>
                <span v-else>Gửi tin nhắn</span>
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
              <a href="https://maps.google.com" target="_blank" class="directions-btn">
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

<script setup>
import { ref, reactive } from 'vue'
import { submitContact } from '@/api'

// Form state
const formData = reactive({
  name: '',
  email: '',
  phone: '',
  subject: '',
  content: ''
})

const errors = reactive({})
const submitting = ref(false)
const submitSuccess = ref(false)

// Methods
const validateForm = () => {
  // Clear errors
  Object.keys(errors).forEach(key => delete errors[key])
  
  let isValid = true
  
  if (!formData.name.trim()) {
    errors.name = 'Vui lòng nhập họ và tên'
    isValid = false
  }
  
  if (!formData.email.trim()) {
    errors.email = 'Vui lòng nhập email'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
    errors.email = 'Email không hợp lệ'
    isValid = false
  }
  
  if (!formData.content.trim()) {
    errors.content = 'Vui lòng nhập nội dung tin nhắn'
    isValid = false
  }
  
  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) return
  
  submitting.value = true
  
  try {
    const response = await submitContact({
      name: formData.name,
      email: formData.email,
      phone: formData.phone || null,
      subject: formData.subject || null,
      content: formData.content
    })
    
    if (response.data.message) {
      submitSuccess.value = true
    }
  } catch (error) {
    console.error('Error submitting contact:', error)
    if (error.response?.data?.message) {
      alert(error.response.data.message)
    } else {
      alert('Có lỗi xảy ra. Vui lòng thử lại sau.')
    }
  } finally {
    submitting.value = false
  }
}

const resetForm = () => {
  formData.name = ''
  formData.email = ''
  formData.phone = ''
  formData.subject = ''
  formData.content = ''
  Object.keys(errors).forEach(key => delete errors[key])
  submitSuccess.value = false
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.contact-page {
  font-family: 'Inter', sans-serif;
  color: #0f172a;
  background: #f8fafc;
}

/* Page Header */
.page-header {
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
  padding: 60px 0;
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.breadcrumb a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.3s;
}

.breadcrumb a:hover {
  color: #ff6b35;
}

.breadcrumb .separator {
  color: #475569;
}

.breadcrumb .current {
  color: #ffffff;
}

.page-title {
  font-size: 42px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 12px 0;
}

.page-subtitle {
  font-size: 16px;
  color: #94a3b8;
  margin: 0;
}

/* Contact Info Section */
.contact-info-section {
  padding: 60px 0;
  background: #ffffff;
}

.info-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
}

.info-card {
  text-align: center;
  padding: 30px;
  background: #f8fafc;
  border-radius: 20px;
  transition: all 0.3s;
}

.info-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.info-icon {
  margin-bottom: 16px;
}

.info-title {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #0f172a;
}

.info-text {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  line-height: 1.6;
}

/* Contact Main */
.contact-main {
  padding: 60px 0;
}

.contact-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
}

/* Contact Form */
.contact-form-wrapper {
  background: #ffffff;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 8px 0;
  color: #0f172a;
}

.section-desc {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 32px 0;
}

/* Success Message */
.success-message {
  text-align: center;
  padding: 40px 20px;
}

.success-message svg {
  margin-bottom: 20px;
}

.success-message h3 {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #22c55e;
}

.success-message p {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 24px 0;
}

/* Form */
.contact-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
}

.form-input {
  padding: 14px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  transition: all 0.3s;
  background: #ffffff;
}

.form-input:focus {
  outline: none;
  border-color: #ff6b35;
  box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-input.error {
  border-color: #ef4444;
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.form-select {
  cursor: pointer;
}

.error-text {
  font-size: 12px;
  color: #ef4444;
}

.submit-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 16px 32px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border: none;
  border-radius: 10px;
  color: #ffffff;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.submit-btn:hover:not(:disabled) {
  box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
  transform: translateY(-2px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-icon {
  display: flex;
}

/* Map */
.map-wrapper {
  background: #ffffff;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

.map-container {
  margin-bottom: 30px;
}

.map-placeholder {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 16px;
}

.map-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
}

.map-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
}

.map-marker {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #ffffff;
  padding: 12px 20px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.marker-info {
  display: flex;
  flex-direction: column;
}

.marker-info strong {
  font-size: 14px;
  color: #0f172a;
}

.marker-info span {
  font-size: 12px;
  color: #64748b;
}

.directions-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  color: #475569;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s;
}

.directions-btn:hover {
  border-color: #ff6b35;
  color: #ff6b35;
}

/* Store Gallery */
.gallery-title {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 20px 0;
  color: #0f172a;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.gallery-grid img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: 12px;
  transition: transform 0.3s;
}

.gallery-grid img:hover {
  transform: scale(1.05);
}

/* FAQ Section */
.faq-section {
  padding: 80px 0;
  background: #ffffff;
}

.section-title-center {
  font-size: 32px;
  font-weight: 800;
  text-align: center;
  margin: 0 0 8px 0;
  color: #0f172a;
}

.section-subtitle-center {
  font-size: 16px;
  text-align: center;
  color: #64748b;
  margin: 0 0 48px 0;
}

.faq-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
}

.faq-item {
  padding: 24px;
  background: #f8fafc;
  border-radius: 16px;
}

.faq-question {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.faq-question svg {
  flex-shrink: 0;
  margin-top: 2px;
}

.faq-question h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  color: #0f172a;
}

.faq-answer {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  line-height: 1.6;
  padding-left: 36px;
}

/* CTA Section */
.cta-section {
  padding: 80px 0;
  background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%);
}

.cta-content {
  text-align: center;
}

.cta-title {
  font-size: 36px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 12px 0;
}

.cta-text {
  font-size: 16px;
  color: #94a3b8;
  margin: 0 0 32px 0;
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 18px 36px;
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  border-radius: 12px;
  color: #ffffff;
  text-decoration: none;
  font-size: 18px;
  font-weight: 600;
  transition: all 0.3s;
}

.cta-btn:hover {
  box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
  transform: translateY(-2px);
}

/* Admin Button */
.admin-btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  border: none;
}

.admin-btn-primary {
  background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
  color: #ffffff;
}

.admin-btn-primary:hover {
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
  .info-cards { grid-template-columns: repeat(2, 1fr); }
  .contact-layout { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
  .info-cards { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
  .faq-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: 1fr 1fr; }
  .page-title { font-size: 32px; }
}
</style>
