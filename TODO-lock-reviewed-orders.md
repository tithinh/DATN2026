# TODO: Lock Review After Submit

## Mô tả
- Sau khi đánh giá xong đơn hàng, không thể đánh giá lại
- Khi nhấn "Đánh giá" ở account, có thể xem đánh giá nhưng khóa khu vực gửi đánh giá cho các sản phẩm đã được đánh giá

## Các file cần sửa

### 1. Backend - OrderController (`backend/app/Http/Controllers/Api/OrderController.php`)
- [x] Trong `index()`, truy vấn thêm các đánh giá hiện có của user theo `product_id`
- [x] Thêm trường `is_reviewed` và `review: {rating, content, comment_id}` vào mỗi `item` trong response

### 2. Frontend - UserProfile (`fivetech-store/src/views/cilent/UserProfile.vue`)
- [x] Cập nhật logic nút "Đánh giá": 
  - Tất cả đã đánh giá → "✓ Đã đánh giá" (disabled)
  - Có chưa đánh giá → "★ Đánh giá" 
  - Một vài đã đánh giá → "★ Xem / Đánh giá"
- [x] `openReviewModal()`: pre-fill dữ liệu đánh giá cũ vào `ratings[]` và `contents[]`
- [x] Modal đánh giá: hiển thị read-only cho sản phẩm đã đánh giá (disabled stars, locked textarea, badge "Đã đánh giá")
- [x] `submitReviews()`: skip các item đã được đánh giá
- [x] Disable nút "Gửi đánh giá" nếu tất cả đã được đánh giá

