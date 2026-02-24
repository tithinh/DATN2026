// stores/cart.js
import { defineStore } from 'pinia'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

export const useCartStore = defineStore('cart', {
  state: () => ({
    id: null as number | null,
    items: [],           // array các item trong giỏ
    err: null,
    quantity: 0,      // tổng số lượng sản phẩm
    subtotal: 0,         // tổng trước giảm giá + phí ship
    discount: 0,         // số tiền giảm từ coupon
    shippingFee: 30000,  // phí ship mặc định 30.000đ
    total: 0,            // tổng cuối cùng (subtotal - discount + shippingFee)
    finalShippingFee: 30000, // phí ship sau khi áp dụng điều kiện miễn phí
    couponCode: '',
    couponApplied: null, // lưu thông tin coupon nếu cần hiển thị chi tiết
    loading: false,
    error: null,
  }),

  getters: {
    itemCount: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    isEmpty: (state) => state.items.length === 0,
    hasDiscount: (state) => state.discount > 0,
    // Tính phí ship động (ví dụ: miễn phí nếu subtotal ≥ 300.000đ)
    calculatedFinalShippingFee: (state) => {
      // Điều kiện miễn phí ship (bạn có thể thay đổi)
      return state.subtotal >= 300000 ? 0 : state.shippingFee
    },
    // Tổng cuối cùng (dùng trong component)
    grandTotal: (state) => Math.max(0, state.subtotal - state.discount + state.finalShippingFee),
  },

  actions: {
    // Tải giỏ hàng từ backend
    async fetchCart() {
      const auth = useAuthStore()

      if (!auth.isAuthenticated) {
        this.resetCart()
        return
      }

      this.loading = true
      this.error = null

      try {
        const res = await api.get('/cart')
        this.items = res.data?.items || res.data || []
        this.couponCode = res.data?.coupon_code || ''
        this.calculateTotals()
      } catch (err) {
        this.error = err.response?.data?.message || 'Không thể tải giỏ hàng. Vui lòng thử lại.'
        if (err.response?.status === 401) {
          auth.logout()
        }
      } finally {
        this.loading = false
      }
    },

    // Cập nhật số lượng sản phẩm
    async updateQuantity(id, qty) {
      if (qty < 1) return

      this.loading = true
      try {
        const item = this.items.find(i => i.id === id)
        if (!item) throw new Error('Không tìm thấy sản phẩm')

        await api.put('/cart/update', {
          id,
          quantity: qty,
          variant_id: item.variant_id || item.variant?.variant_id || null
        })

        await this.fetchCart()
      } catch (err) {
        throw new Error(err.response?.data?.message || 'Không thể cập nhật số lượng')
      } finally {
        this.loading = false
      }
    },

  // Xóa 1 item khỏi giỏ hàng
  async removeItem(id) {
    if (!id) {
      this.error = 'Không có ID sản phẩm để xóa';
      return;
    }

    this.loading = true;
    this.error = null;

    try {
      await api.delete(`/cart/remove/${id}`);

      // Refresh giỏ hàng sau khi xóa thành công
      await this.fetchCart();

      // Optional: thông báo thành công (nếu muốn hiển thị toast)
      // this.success = 'Đã xóa sản phẩm khỏi giỏ hàng';
    } catch (err) {
      const errorMessage = err.response?.data?.message 
        || err.response?.data?.error 
        || err.message 
        || 'Không thể xóa sản phẩm. Vui lòng thử lại.';

      this.error = errorMessage;
      console.error('Lỗi xóa sản phẩm:', err);

      // Nếu token hết hạn → logout
      if (err.response?.status === 401) {
        const auth = useAuthStore();
        auth.logout();
      }
    } finally {
      this.loading = false;
    }
  },

    // Áp dụng mã giảm giá
    async applyCoupon(code) {
      if (!code.trim()) return

      this.loading = true
      try {
        const res = await api.post('/promotions/check', { code })
        this.discount = res.data?.discount_amount || 0
        this.couponApplied = res.data
        this.couponCode = code
        this.calculateTotals()
      } catch (err) {
        throw new Error(err.response?.data?.message || 'Mã giảm giá không hợp lệ hoặc đã hết hạn')
      } finally {
        this.loading = false
      }
    },

    // Xóa mã giảm giá
    async removeCoupon() {
      this.loading = true
      try {
        await api.post('/promotions/remove') // nếu có endpoint
        this.discount = 0
        this.couponApplied = null
        this.couponCode = ''
        this.calculateTotals()
      } catch (err) {
        console.error('Remove coupon failed:', err)
      } finally {
        this.loading = false
      }
    },

    // Tính toán lại tất cả tổng tiền
    calculateTotals() {
      // Tính subtotal (tổng trước giảm giá)
      this.subtotal = this.items.reduce((sum, item) => {
        const unitPrice = (
          item.variant?.price_extra && Number(item.variant.price_extra) > 0
            ? (item.price || 0) + Number(item.variant.price_extra)
            : (item.discount_price || item.price || item.variant?.product?.discount_price || 0)
        )
        return sum + unitPrice * item.quantity
      }, 0)

      // Tổng cuối cùng = subtotal - discount + phí ship (động)
      this.total = Math.max(0, this.subtotal - this.discount + this.finalShippingFee)
    },

    // Reset giỏ hàng
    resetCart() {
      this.items = []
      this.subtotal = 0
      this.discount = 0
      this.total = 0
      this.couponCode = ''
      this.couponApplied = null
      this.error = null
    },

    // Xóa toàn bộ giỏ hàng (gọi khi logout hoặc theo yêu cầu)
    async clearCart() {
      try {
        await api.delete('/cart/clear') // nếu có endpoint
      } catch (err) {
        console.error('Clear cart failed:', err)
      }
      this.resetCart()
    },
  },
})