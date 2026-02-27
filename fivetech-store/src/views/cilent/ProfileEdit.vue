<template>
  <div class="edit-profile-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <nav class="breadcrumb">
          <router-link to="/">Trang chủ</router-link>
          <span class="separator">/</span>
          <router-link to="/account">Tài khoản</router-link>
          <span class="separator">/</span>
          <span class="current">Chỉnh sửa thông tin</span>
        </nav>
        <h1 class="page-title">Chỉnh sửa thông tin</h1>
      </div>
    </div>

    <!-- Main Content -->
    <section class="profile-section">
      <div class="container">
        <div class="profile-layout">
          <!-- Sidebar -->
          <aside class="profile-sidebar">
            <div class="user-avatar-card">
              <div class="avatar-wrapper">
                <img
                  :src="form.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(form.full_name || 'User')}&background=0D8ABC&color=fff`"
                  :alt="form.full_name"
                  class="avatar-image"
                />
                <button class="avatar-edit-btn" @click="triggerAvatarUpload">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                </button>
              </div>
              <input 
                type="file" 
                ref="avatarInput" 
                accept="image/*" 
                style="display: none"
                @change="handleAvatarChange"
              />
              <h3 class="user-name">{{ form.full_name }}</h3>
              <p class="user-email">{{ form.email }}</p>
            </div>

            <nav class="sidebar-nav">
              <router-link to="/account" class="nav-item">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                </span>
                <span class="nav-text">Thông tin cá nhân</span>
              </router-link>
              <router-link to="/profile/edit" class="nav-item active">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                </span>
                <span class="nav-text">Chỉnh sửa thông tin</span>
              </router-link>
              <router-link to="/profile/change-password" class="nav-item">
                <span class="nav-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <span class="nav-text">Đổi mật khẩu</span>
              </router-link>
            </nav>
          </aside>

          <!-- Main Content -->
          <main class="profile-content">
            <div class="form-panel">
              <div class="panel-header">
                <h2 class="panel-title">Thông tin cá nhân</h2>
                <p class="panel-desc">Cập nhật thông tin để bảo mật tài khoản và nhận ưu đãi</p>
              </div>

              <form @submit.prevent="handleSubmit" class="profile-form">
                <!-- Họ và tên -->
                <div class="form-group">
                  <label for="full_name">Họ và tên</label>
                  <input 
                    type="text" 
                    id="full_name" 
                    v-model="form.full_name"
                    placeholder="Nhập họ và tên"
                    required
                  />
                </div>

                <!-- Email -->
                <div class="form-group">
                  <label for="email">Email</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="form.email"
                    disabled
                    class="disabled"
                  />
                  <p class="form-hint">Email không thể thay đổi</p>
                </div>

                <!-- Số điện thoại -->
                <div class="form-group">
                  <label for="phone">Số điện thoại</label>
                  <input 
                    type="tel" 
                    id="phone" 
                    v-model="form.phone"
                    placeholder="Nhập số điện thoại"
                  />
                </div>

                <!-- Ngày sinh -->
                <div class="form-group">
                  <label for="birthday">Ngày sinh</label>
                  <input 
                    type="date" 
                    id="birthday" 
                    v-model="form.birthday"
                  />
                </div>

                <!-- Giới tính -->
                <div class="form-group">
                  <label for="gender">Giới tính</label>
                  <select id="gender" v-model="form.gender">
                    <option value="">Chọn giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                  </select>
                </div>

                <!-- Địa chỉ -->
                <div class="form-group">
                  <label for="address">Địa chỉ</label>
                  <textarea 
                    id="address" 
                    v-model="form.address"
                    placeholder="Nhập địa chỉ"
                    rows="3"
                  ></textarea>
                </div>

                <!-- Submit -->
                <div class="form-actions">
                  <button type="button" class="btn-cancel" @click="$router.back()">Hủy</button>
                  <button type="submit" class="btn-submit" :disabled="loading">
                    {{ loading ? 'Đang lưu...' : 'Lưu thay đổi' }}
                  </button>
                </div>

                <!-- Error/Success Message -->
                <div v-if="message" :class="['alert', messageType]">
                  {{ message }}
                </div>
              </form>
            </div>
          </main>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

const router = useRouter()
const auth = useAuthStore()

const loading = ref(false)
const message = ref('')
const messageType = ref('success')
const avatarInput = ref(null)

const form = reactive({
  full_name: '',
  email: '',
  phone: '',
  birthday: '',
  gender: '',
  address: '',
  avatar: ''
})

onMounted(() => {
  // Load user data
  if (auth.user) {
    form.full_name = auth.user.full_name || ''
    form.email = auth.user.email || ''
    form.phone = auth.user.phone || ''
    form.birthday = auth.user.birthday || ''
    form.gender = auth.user.gender || ''
    form.address = auth.user.address || ''
    form.avatar = auth.user.avatar || ''
  }
})

const triggerAvatarUpload = () => {
  avatarInput.value?.click()
}

const handleAvatarChange = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('avatar', file)

  try {
    const res = await api.post('/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.avatar = res.data.avatar
    auth.user.avatar = res.data.avatar
    localStorage.setItem('user', JSON.stringify(auth.user))
  } catch (err) {
    console.error('Upload avatar failed:', err)
  }
}

const handleSubmit = async () => {
  loading.value = true
  message.value = ''

  try {
    const res = await api.put('/user/profile', {
      full_name: form.full_name,
      phone: form.phone,
      birthday: form.birthday,
      gender: form.gender,
      address: form.address
    })

    // Update local storage
    auth.user = { ...auth.user, ...res.data.user }
    localStorage.setItem('user', JSON.stringify(auth.user))

    message.value = 'Cập nhật thông tin thành công!'
    messageType.value = 'success'

    setTimeout(() => {
      router.push('/account')
    }, 1500)
  } catch (err) {
    console.error('Update profile failed:', err)
    message.value = err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại!'
    messageType.value = 'error'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.edit-profile-page {
  min-height: 100vh;
  background: #f8fafc;
}

.page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 40px 0;
  margin-bottom: 40px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  font-size: 14px;
}

.breadcrumb a {
  color: #94a3b8;
  text-decoration: none;
}

.breadcrumb a:hover {
  color: #fff;
}

.breadcrumb .separator {
  color: #64748b;
}

.breadcrumb .current {
  color: #fff;
}

.page-title {
  color: #fff;
  font-size: 32px;
  font-weight: 700;
  margin: 0;
}

.profile-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 30px;
}

.profile-sidebar {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  height: fit-content;
  position: sticky;
  top: 20px;
}

.user-avatar-card {
  text-align: center;
  padding-bottom: 24px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 24px;
}

.avatar-wrapper {
  position: relative;
  display: inline-block;
  margin-bottom: 16px;
}

.avatar-image {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e2e8f0;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #0f172a;
  color: #fff;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.avatar-edit-btn:hover {
  background: #3b82f6;
}

.user-name {
  font-size: 18px;
  font-weight: 600;
  color: #0f172a;
  margin: 0 0 4px 0;
}

.user-email {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 10px;
  text-decoration: none;
  color: #475569;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-item:hover {
  background: #f1f5f9;
}

.nav-item.active {
  background: #0f172a;
  color: #fff;
}

.nav-icon {
  display: flex;
  align-items: center;
}

.profile-content {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
}

.panel-header {
  margin-bottom: 32px;
}

.panel-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0 0 8px 0;
}

.panel-desc {
  color: #64748b;
  margin: 0;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #0f172a;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
}

.form-group input.disabled {
  background: #f1f5f9;
  cursor: not-allowed;
}

.form-hint {
  font-size: 12px;
  color: #64748b;
  margin: 0;
}

.form-actions {
  display: flex;
  gap: 16px;
  margin-top: 16px;
}

.btn-cancel,
.btn-submit {
  padding: 14px 28px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: transparent;
  border: 2px solid #e2e8f0;
  color: #475569;
}

.btn-cancel:hover {
  background: #f1f5f9;
}

.btn-submit {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  border: none;
  color: #fff;
}

.btn-submit:hover:not(:disabled) {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.alert {
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 14px;
}

.alert.success {
  background: #dcfce7;
  color: #166534;
}

.alert.error {
  background: #fee2e2;
  color: #991b1b;
}

@media (max-width: 768px) {
  .profile-layout {
    grid-template-columns: 1fr;
  }

  .profile-sidebar {
    position: static;
  }
}
</style>
