Báo cáo Phân tích & Kịch bản Bảo vệ Đồ án: FiveTech Store
PHẦN 1 — PHÂN TÍCH TỔNG QUAN DỰ ÁN
1.1 Stack Công nghệ (từ composer.json + package.json)
Tầng	Công nghệ	Phiên bản
Backend Framework	Laravel	12.x (mới nhất 2025)
PHP Runtime	PHP	≥ 8.2
Frontend Framework	Vue 3 (Composition API)	3.5.x
Ngôn ngữ Frontend	TypeScript	5.9.x (strict mode)
Build Tool	Vite (rolldown-vite)	7.x
State Management	Pinia	3.x
HTTP Client	Axios	1.13.x
Icon Library	Lucide Vue	0.56.x
UI Utility	@vueuse/core	14.x
CSS Framework	Tailwind CSS	4.x
API Auth	Laravel Sanctum	(token-based)
Social Login	Laravel Socialite	5.24
Database	MySQL / MariaDB	—
Queue	Laravel Queue (database driver)	—
Email	Laravel Mail + Gmail SMTP	—
Dev Tools	Laravel Pail, Pint, PHPUnit	—
Điểm đáng chú ý: Dự án dùng rolldown-vite — một Rust-based bundler thay thế esbuild, cho tốc độ build nhanh hơn đáng kể. Đây là lựa chọn kỹ thuật tiên tiến.

1.2 Kiến trúc Hệ thống

┌─────────────────────────────────────────────────────────┐
│                   CLIENT (Browser)                       │
│                                                          │
│  ┌──────────────┐     ┌────────────────────────────┐    │
│  │  Vue 3 SPA   │     │     Admin Panel (Vue 3)     │    │
│  │  (client/)   │     │       (admin/)              │    │
│  └──────┬───────┘     └────────────┬───────────────┘    │
│         │ Axios + Bearer Token      │ Bearer admin_token │
└─────────┼───────────────────────────┼───────────────────┘
          │                           │
          ▼                           ▼
┌─────────────────────────────────────────────────────────┐
│              Laravel 12 REST API                         │
│                /api/v1/...                               │
│                                                          │
│  ┌────────────┐  ┌──────────────┐  ┌─────────────────┐  │
│  │   Public   │  │   Protected  │  │   Admin         │  │
│  │   Routes   │  │  (Sanctum)   │  │ (Sanctum+admin) │  │
│  └─────┬──────┘  └──────┬───────┘  └────────┬────────┘  │
│        │                │                   │            │
│        └────────────────┴───────────────────┘            │
│                         │                                │
│              ┌──────────▼──────────┐                     │
│              │     Controllers     │                     │
│              │  (15 Controllers)   │                     │
│              └──────────┬──────────┘                     │
│                         │                                │
│              ┌──────────▼──────────┐                     │
│              │   Eloquent Models   │                     │
│              │  (14 Models, ORM)   │                     │
│              └──────────┬──────────┘                     │
│                         │        ┌──────────────────┐    │
│              ┌──────────▼──┐     │  Event/Listener  │    │
│              │   MySQL DB  │     │  (Email Queue)   │    │
│              └─────────────┘     └──────────────────┘    │
└─────────────────────────────────────────────────────────┘
Luồng dữ liệu tổng thể:

Vue 3 SPA gọi API qua Axios với Authorization: Bearer <token>
Laravel Router phân quyền → 3 tầng middleware: Public / Sanctum / Sanctum+Admin
Controller xử lý business logic → Eloquent ORM tương tác DB
Với các tác vụ nặng (email), Laravel dispatch Event → Listener chạy qua Queue (không block HTTP response)
Response JSON trả về Vue → Pinia store cập nhật state → Component re-render
1.3 Tổ chức Mã nguồn & Design Patterns
Backend (Laravel — MVC + Event-Driven):


backend/
├── app/
│   ├── Events/           ← Domain Events (OrderCreated, OrderCompleted)
│   ├── Listeners/        ← Event Handlers (SendOrderConfirmationEmail)
│   ├── Http/
│   │   ├── Controllers/Api/   ← 15 Controllers (RESTful)
│   │   └── Middleware/        ← CheckAdmin, auth
│   ├── Mail/             ← Mailables (OrderConfirmation, OrderCompleted)
│   ├── Models/           ← 14 Eloquent Models
│   └── Providers/
│       └── AppServiceProvider.php  ← Event registration, DI bindings
├── resources/views/emails/   ← Blade email templates
└── routes/api.php            ← 3 route groups, ~55 endpoints
Frontend (Vue 3 — Component-based + Flux-like):


fivetech-store/src/
├── api/index.js          ← Single Axios instance (interceptors)
├── stores/               ← Pinia stores (auth, adminAuth, cart, products)
├── router/index.ts       ← Vue Router (guards, lazy loading)
├── layouts/              ← MainLayout, AdminLayout
└── views/
    ├── cilent/           ← 21 customer-facing pages
    └── admin/            ← 11 admin pages
Design Patterns được áp dụng:

Repository-like Pattern: Pinia stores đóng vai trò data access layer cho frontend
Observer Pattern: Laravel Events/Listeners (email notification system)
Strategy Pattern: ProductController dùng switch-case cho sorting/filtering
Accessor Pattern: Eloquent Model accessors (getFinalPriceAttribute, getRealStockTotalAttribute)
Guard Pattern: Router beforeEach guard + Laravel Middleware stack
Interceptor Pattern: Axios request/response interceptors
1.4 Đánh giá Kỹ thuật
Điểm mạnh:

✅ Tách biệt hai guard system: admin_token vs token trong localStorage — Admin và User dùng hai bảng DB riêng (admins + users), tránh leo thang đặc quyền

✅ Stock được bảo vệ server-side: Giá và phí ship không tin từ client — tính lại hoàn toàn ở backend (OrderController line 73-144)

✅ Hỗ trợ Guest checkout: Không cần tài khoản vẫn mua được, dùng user_id=1 làm placeholder, lưu customer_email riêng

✅ Event-Driven Email: Email chạy qua Queue, không làm chậm response đặt hàng

✅ Soft Delete trên Order model — dữ liệu lịch sử không bị mất vĩnh viễn

Điểm có thể tối ưu hóa (chuẩn bị trước cho câu hỏi của hội đồng):

⚡ Thiếu Redis Cache: Danh sách sản phẩm query mỗi request, chưa có cache layer. Giải pháp: thêm Cache::remember() 5 phút cho /products — giảm 90% DB queries

⚡ Chưa có API Rate Limiting: Endpoint public POST /orders và POST /track-order chưa có throttle. Giải pháp: thêm throttle:60,1 middleware

PHẦN 2 — CHỨC NĂNG & LUỒNG XỬ LÝ CHI TIẾT
Module 1: Xác thực & Phân quyền (AuthController)
Mục đích kinh doanh: Cho phép khách hàng có tài khoản cá nhân, theo dõi lịch sử mua hàng; tách biệt hoàn toàn vai trò Admin.

Luồng đăng nhập thường:


[Vue Login.vue]
  → POST /api/v1/login {email, password}
  → AuthController::login()
     ├─ Validate email + password
     ├─ User::where('email',...)->first()
     ├─ Hash::check(password, user.password)  ← bcrypt verify
     ├─ Check is_active (tài khoản bị khóa → 403)
     └─ $user->createToken('auth_token')->plainTextToken
  → Response: {token, user{...}}
  → [Pinia auth store] localStorage.setItem('token', ...)
  → Axios interceptor tự động đính kèm token cho mọi request tiếp theo
Luồng Social Login (Google/Facebook):


[Click "Đăng nhập Google"] → redirectToGoogle()
  → window.location.href = /api/v1/auth/google
  → Laravel Socialite redirect → Google OAuth consent screen
  → User đồng ý → callback /api/v1/auth/google/callback
  → Socialite::driver('google')->user()
  → User::firstOrCreate(['email' => googleUser.email])
  → Tạo Sanctum token
  → Redirect về frontend: /social-callback?token=xxx&user=yyy (URL params)
  → SocialCallback.vue: đọc params → lưu localStorage → redirect Home
Module 2: Sản phẩm & Tìm kiếm (ProductController)
Mục đích kinh doanh: Hiển thị catalog sản phẩm với filter đa chiều, tìm kiếm full-text, hỗ trợ biến thể (màu/dung lượng).

Luồng filter/search tại /products:


[Vue Products.vue sidebar] → GET /api/v1/products?
    category_id=1,2&min_price=100000&max_price=500000
    &search=airpods&sort=price_asc&filter=sale

→ ProductController::index()
   ├─ Base query: Product::with(['variants','category'])->where('is_visible',1)
   ├─ Filter 'sale': whereNotNull('discount_price')
   │                  ->whereRaw('discount_price < base_price')
   │                  ->orderByRaw('(base_price - discount_price) DESC')
   ├─ category_id: whereIn('category_id', [1,2])
   ├─ Price range: COALESCE(discount_price, base_price) BETWEEN min AND max
   ├─ Search: LOWER(name/short_desc/description) LIKE '%airpods%'
   └─ Sort: COALESCE(discount_price, base_price) ASC
   → paginate(12) → JSON {data:[], links:{}, meta:{total,per_page,...}}
Điểm kỹ thuật đáng chú ý: Dùng COALESCE(discount_price, base_price) trong WHERE và ORDER BY — đảm bảo sort/filter theo giá thực tế kể cả khi có hoặc không có giảm giá. ProductVariant tự động cập nhật stock_total của Product cha thông qua Eloquent Model Events (booted() hook).

Module 3: Giỏ hàng & Đặt hàng (CartController + OrderController)
Đây là module phức tạp nhất — cũng là điểm sáng kỹ thuật nhất của dự án.

Luồng đặt hàng end-to-end:


1. [CheckoutView.vue] — User điền form
   customer_name, phone, email*, address, payment_method (cod/vietqr)
   coupon_code (tùy chọn)

2. [Pinia cart store] → POST /api/v1/orders {
     items: [{cart_item_id, quantity, variant_id},...],
     payment_method: "cod",
     shipping_address: "...",
     customer_email: "user@gmail.com",
     coupon_code: "SUMMER10"
   }

3. [OrderController::store()]
   ├─ DB::beginTransaction()   ← atomic transaction
   │
   ├─ LOOP qua từng item:
   │   ├─ CartItem::findOrFail(cart_item_id)
   │   ├─ Tính unitPrice = product.discount_price ?? product.base_price
   │   │                   + variant.price_extra (nếu có)
   │   ├─ Check stock: quantity > variant.stock → throw Exception
   │   └─ Cộng vào $subtotal
   │
   ├─ Coupon validation (nếu có coupon_code):
   │   ├─ Tìm Promotion: is_active, start≤now≤end
   │   ├─ Check user đã dùng chưa (Order với promo_id này)
   │   ├─ Check max_uses & used_count
   │   ├─ Check min_order_amount
   │   └─ Tính discount: percentage (%) hoặc fixed (đồng)
   │       min(discountAmount, subtotal)  ← không giảm quá tổng tiền
   │       $promo->increment('used_count')
   │
   ├─ Shipping fee server-side:
   │   subtotal >= 300.000đ → 0đ | else → 30.000đ
   │
   ├─ Order::create({status:'pending', ...})
   ├─ foreach items: order->items()->create(...)
   ├─ foreach items: ProductVariant::decrement('stock', quantity)  ← trừ kho
   ├─ CartItem::whereIn('id', usedIds)->delete()  ← xóa cart items đã dùng
   │
   ├─ DB::commit()
   └─ OrderCreated::dispatch(order->fresh(['items.product','items.variant']))
       → Queue → SendOrderConfirmationEmail
           → Mail::to(customer_email)->send(new OrderConfirmation($order))
           → Gmail SMTP gửi email xác nhận

4. Response 201: {message, order_code, order_id, total, payment_status}

5. [Frontend]:
   - payment_method === 'cod' → redirect /order-success
   - payment_method === 'vietqr' → redirect /payment-pending (hiện QR)
Module 4: Tra cứu Đơn hàng (TrackOrderController)
Mục đích kinh doanh: Khách hàng không cần đăng nhập vẫn tra được đơn — giải quyết pain point của guest checkout.


[TrackOrder.vue] → POST /api/v1/track-order
  {order_code: "FT-20260503-0001"} HOẶC {phone: "0901234567"}

→ TrackOrderController::track()
   ├─ Validate: ít nhất 1 trong 2 trường phải có
   ├─ Query: Order::with(['items.product','items.variant'])
   │          ->where('order_code', value)  // nếu có order_code
   │          hoặc ->where('customer_phone', value)
   │          ->orderBy('created_at','desc')->first()
   ├─ 404 nếu không tìm thấy
   └─ Return: {order_code, status, status_text, status_progress(0-100),
               customer_name, items[{name,variant,qty,price,image}],
               total_amount, discount_amount, final_amount}

[TrackOrder.vue]:
   → Hiển thị status banner (màu theo trạng thái)
   → Progress bar 0-100%
   → Danh sách sản phẩm + tổng tiền
Module 5: Hệ thống Email Notification (Event-Driven)

AppServiceProvider::boot():
  Event::listen(OrderCreated::class, SendOrderConfirmationEmail::class)
  Event::listen(OrderCompleted::class, SendOrderCompletedEmail::class)

Khi OrderController::store() xong:
  OrderCreated::dispatch($order)  → Queue job

Worker (php artisan queue:listen):
  → Dequeue job → SendOrderConfirmationEmail::handle(OrderCreated $event)
  → Mail::to($order->customer_email)->send(new OrderConfirmation($order))
  → Gmail SMTP → Inbox

Khi Admin cập nhật status → 'completed':
  OrderCompleted::dispatch($order)  → Queue → SendOrderCompletedEmail
Module 6: Admin Dashboard & Phân quyền
Admin dùng bảng admins riêng (không phải users) — đảm bảo cô lập hoàn toàn. Middleware admin check Auth::guard('sanctum')->user() là instance của Admin model.


DashboardController::stats() trả về:
  - Tổng users, users mới tháng này
  - Tổng sản phẩm, tổng kho, sản phẩm sắp hết hàng (<10)
  - Tổng đơn hàng, đơn chờ xử lý
  - Doanh thu tổng + tháng này (chỉ tính đơn shipping/completed)
  - Đơn hàng gần đây (5 đơn mới nhất)
  - Sản phẩm bán chạy nhất (top 5)
PHẦN 3 — KỊCH BẢN BẢO VỆ ĐỒ ÁN TỐT NGHIỆP (~15 phút)
🎯 Mở đầu (1 phút)
"Kính thưa Hội đồng, kính thưa quý thầy cô,

Em tên là [Tên bạn], mã số sinh viên [MSSV]. Hôm nay em xin trình bày đồ án tốt nghiệp với đề tài: Xây dựng hệ thống thương mại điện tử bán phụ kiện điện thoại — FiveTech Store.

Lý do em chọn đề tài này xuất phát từ thực tế: thị trường phụ kiện điện thoại tại Việt Nam đang tăng trưởng mạnh, nhưng hầu hết các shop nhỏ vẫn bán hàng thủ công qua mạng xã hội, thiếu hệ thống quản lý bài bản. Em muốn xây dựng một nền tảng end-to-end — từ trải nghiệm mua sắm của khách hàng cho đến bộ công cụ quản trị cho chủ cửa hàng — giải quyết đúng bài toán thực tế đó.

Buổi trình bày của em gồm 4 phần: Kiến trúc & Công nghệ, Demo chức năng, Khó khăn kỹ thuật, và Định hướng phát triển."

🏗️ Kiến trúc & Công nghệ (2 phút)
"Hệ thống được thiết kế theo mô hình Decoupled Architecture — Backend và Frontend hoàn toàn tách biệt.

Về Backend, em sử dụng Laravel 12 — phiên bản mới nhất năm 2025 — với PHP 8.2. Laravel cung cấp ORM Eloquent mạnh mẽ, hệ thống Middleware phân tầng, và đặc biệt là Event-Driven Architecture mà em đã tận dụng cho module email notification.

Về Frontend, em chọn Vue 3 với Composition API và TypeScript strict mode. Lý do chọn Vue 3 thay vì React là vì cú pháp template trực quan hơn, learning curve thấp hơn, và <script setup> của Vue 3 cho phép viết code gọn gàng, dễ đọc. Pinia đóng vai trò state management — nhẹ hơn Vuex, tích hợp native với Vue 3.

Điểm đặc biệt: em dùng rolldown-vite — một bundler viết bằng Rust, nhanh hơn esbuild truyền thống — phản ánh việc theo dõi xu hướng công nghệ mới nhất.

Toàn bộ giao tiếp qua REST API với prefix /api/v1, xác thực bằng Laravel Sanctum — Bearer Token lưu trong localStorage."

🖥️ Demo Thực tế (8 phút)
Demo 1: Luồng mua hàng Guest (3 phút)
"Em sẽ demo luồng quan trọng nhất — khách hàng mua hàng mà không cần tài khoản.

(Click vào trang Products) Đây là trang danh sách sản phẩm. Em sẽ thử bộ lọc đa chiều: chọn danh mục 'Ốp lưng', kéo thanh giá từ 50.000đ đến 200.000đ, sort theo 'Giá thấp đến cao'.*

Ở đây hệ thống gọi API GET /products?category_id=X&min_price=50000&max_price=200000&sort=price_asc. Điểm đáng chú ý: backend dùng câu lệnh COALESCE(discount_price, base_price) để sort theo giá thực tế — kể cả sản phẩm đang giảm giá hay không.

(Click vào sản phẩm) Trang chi tiết hiển thị biến thể theo màu và dung lượng. (Chọn màu Đen, 128GB) Giá tự động cập nhật — đây là base_price + variant.price_extra.*

(Add to cart → Checkout) Tại trang thanh toán, khách không cần đăng nhập. Em điền tên, số điện thoại, và email là bắt buộc — để nhận thông báo đơn hàng. Chọn phương thức COD. (Click Đặt hàng)

Lúc này backend thực hiện một database transaction: tính giá lại từ đầu server-side, kiểm tra tồn kho, trừ stock, lưu đơn hàng, xóa cart — tất cả trong một atomic operation. Sau khi commit thành công, hệ thống dispatch một Event — email xác nhận được gửi bất đồng bộ qua Queue, không làm chậm response.

(Trang order-success hiện ra) Đơn hàng đã tạo với mã FT-20260503-0001.*

(Mở Gmail) Và đây là email xác nhận được gửi đến, với đầy đủ chi tiết đơn hàng, phương thức thanh toán, địa chỉ giao hàng."*

Demo 2: Tra cứu Đơn hàng (1.5 phút)
"(Mở tab /track-order)* Đây là trang tra cứu đơn hàng hoàn toàn công khai — không cần đăng nhập.*

Khách hàng có 2 cách tìm: nhập mã đơn hàng hoặc số điện thoại. Em nhập mã FT-20260503-0001 vừa đặt.

(Kết quả hiện ra) Hệ thống trả về đầy đủ: trạng thái đơn hàng với progress bar trực quan, thông tin sản phẩm đã đặt, và tổng tiền. Đây giải quyết pain point lớn nhất của guest checkout — khách không cần tài khoản vẫn theo dõi được đơn."*

Demo 3: Admin Dashboard (2.5 phút)
"(Mở /admin)* Đây là bảng điều khiển quản trị. Hệ thống Admin dùng bảng admins hoàn toàn tách biệt với bảng users — đảm bảo không ai có thể leo thang đặc quyền từ tài khoản khách hàng thường.*

Dashboard hiển thị realtime: tổng doanh thu, đơn hàng đang chờ xử lý, sản phẩm sắp hết hàng, và biểu đồ doanh thu theo tháng.

(Vào Quản lý Đơn hàng) Admin có thể cập nhật trạng thái theo luồng: pending → confirmed → processing → shipping → delivered → completed. (Chuyển một đơn sang 'completed') Ngay lập tức, hệ thống dispatch OrderCompleted Event — khách hàng sẽ nhận email thông báo đơn hàng hoàn thành.*

(Vào Quản lý Sản phẩm → Thêm sản phẩm) Admin quản lý sản phẩm với biến thể (màu, dung lượng). Khi cập nhật stock của một variant, Eloquent Model Event tự động cộng lại stock_total của sản phẩm cha — không cần admin cập nhật thủ công."*

🔧 Khó khăn Kỹ thuật & Giải pháp (2 phút)
"Em muốn chia sẻ 2 thách thức kỹ thuật nổi bật em đã giải quyết:

Thách thức 1: Race condition khi đặt hàng đồng thời

Kịch bản: 2 user cùng thêm sản phẩm cuối cùng còn lại (stock=1) vào giỏ và cùng đặt hàng trong cùng 1 giây. Nếu không xử lý, cả 2 đều thành công và stock trở thành -1.

Giải pháp: Em bọc toàn bộ logic tạo đơn trong DB::beginTransaction(). Bước kiểm tra stock (if quantity > variant.stock → throw Exception) và bước trừ stock (ProductVariant::decrement()) diễn ra trong cùng transaction. Khi 2 request đến đồng thời, MySQL đảm bảo chỉ 1 transaction thành công — transaction kia gặp conflict và rollback, trả về lỗi 'Hết hàng' cho user thứ 2.

Thách thức 2: Email làm chậm response đặt hàng

Ban đầu em gửi email đồng bộ (QUEUE_CONNECTION=sync) — mỗi đơn hàng mất 3-5 giây chờ Gmail SMTP. Đây là UX rất tệ.

Giải pháp: Em chuyển sang Event-Driven Architecture với Queue database driver. Sau khi DB::commit(), em chỉ dispatch event (tốc độ microseconds). Queue worker chạy độc lập xử lý email. Response 201 trả về ngay lập tức — user thấy 'Đặt hàng thành công' trong < 500ms."

🚀 Kết luận & Hướng phát triển (1 phút)
"Giá trị dự án mang lại: FiveTech Store là một hệ thống thương mại điện tử hoàn chỉnh — từ trải nghiệm mua sắm mượt mà cho khách hàng, hỗ trợ cả guest lẫn người dùng đã đăng nhập, đến bộ công cụ quản trị đầy đủ cho chủ shop.

Về hướng phát triển trong tương lai:
- Redis Cache: Cache danh sách sản phẩm 5 phút, giảm tải DB
- Elasticsearch: Full-text search nâng cao, tìm kiếm mờ (fuzzy search)
- PWA: Biến thành Progressive Web App, hỗ trợ offline và push notification
- Analytics: Tích hợp Google Analytics 4 hoặc Mixpanel để theo dõi funnel mua hàng
- Docker: Container hóa để deploy dễ dàng lên cloud (AWS/GCP)

Em xin cảm ơn Hội đồng đã lắng nghe. Em sẵn sàng trả lời câu hỏi."

PHẦN 4 — DỰ ĐOÁN Q&A & KỊCH BẢN TRẢ LỜI XUẤT SẮC
❓ Câu hỏi 1 (Bẫy bảo mật):
"Tại sao bạn lưu token trong localStorage thay vì HttpOnly Cookie? Điều đó không tạo ra lỗ hổng XSS sao?"

Trả lời xuất sắc:
"Đây là trade-off có chủ đích. HttpOnly Cookie bảo vệ tốt hơn khỏi XSS nhưng lại dễ bị CSRF attack hơn nếu không cấu hình SameSite đúng. Với kiến trúc SPA + REST API hoàn toàn tách biệt — frontend port 5173, backend port 8000 — việc dùng CORS cookie yêu cầu cấu hình phức tạp hơn nhiều.

Với localStorage + Bearer Token: CSRF không thể tấn công vì attacker không thể đọc token từ cross-origin. XSS thì đúng là rủi ro, nhưng nếu XSS xảy ra, attacker đã có thể execute arbitrary JS — HttpOnly Cookie cũng không bảo vệ được nhiều hơn vì họ vẫn có thể gọi API thay mặt user.

Để mitigate: em đã implement Content-Type validation trên API, CORS whitelist chỉ cho localhost:5173, và Sanctum token có thể revoke bất cứ lúc nào. Trong production, cần thêm CSP headers."

❓ Câu hỏi 2 (Performance):
"Nếu website có 10.000 sản phẩm và 1.000 user đồng thời, hệ thống có chịu được không?"

Trả lời xuất sắc:
"Với kiến trúc hiện tại thì sẽ có bottleneck ở DB layer. Em nhận thức được điều này và đã chuẩn bị lộ trình scale:

Tầng 1 — không cần thay code: thêm MySQL indexes trên các cột hay query (category_id, is_visible, created_at). Với COALESCE(discount_price, base_price) hay dùng trong sort, có thể thêm Generated Column để index được.

Tầng 2 — thêm Cache layer: Redis cache kết quả GET /products trong 5 phút với cache key theo query params. Giảm 90% DB queries cho trang listing.

Tầng 3 — scale ngang: Laravel hỗ trợ horizontal scaling tốt — stateless API, Session không dùng cookie — dễ dàng deploy nhiều app server sau Load Balancer.

Với Laravel Queue + Redis, email notification không bao giờ là bottleneck kể cả 10.000 đơn/ngày."

❓ Câu hỏi 3 (Database Design):
"Tại sao bạn dùng custom primary key như product_id, order_id thay vì id mặc định của Laravel?"

Trả lời xuất sắc:
"Đây là quyết định thiết kế để tăng tính readability và domain clarity. Khi join nhiều bảng, cột id xuất hiện ở mọi nơi gây nhầm lẫn trong debug. Với product_id, order_id — khi đọc raw SQL hay log, ngay lập tức biết foreign key đó thuộc entity nào.

Trade-off là phải khai báo protected $primaryKey = 'product_id' trong mỗi Model và specify foreign key trong các relationship. Laravel Eloquent hỗ trợ hoàn toàn — không có performance penalty.

Đây cũng là convention phổ biến trong các dự án lớn để tránh shadow khi eager loading nhiều relation."

❓ Câu hỏi 4 (Business Logic):
"Làm sao đảm bảo coupon code không bị dùng nhiều lần bởi cùng một user?"

Trả lời xuất sắc:
"Em xử lý ở 3 tầng trong OrderController::store():

Tầng 1 — User đã dùng chưa: Order::where('user_id', authUser->user_id)->where('promo_id', promo->promo_id)->exists(). Nếu đã từng đặt hàng với promo này → skip discount.

Tầng 2 — Global usage limit: $promo->used_count < $promo->max_uses. Giới hạn tổng số lượt toàn hệ thống.

Tầng 3 — Atomic increment: $promo->increment('used_count') dùng SQL UPDATE ... SET used_count = used_count + 1 — atomic ở database level, không bị race condition khi nhiều user dùng cùng lúc.

Với guest checkout (không có user_id), chỉ kiểm tra global limit — đây là trade-off chấp nhận được vì guest không có session persistent."

❓ Câu hỏi 5 (Architecture):
"Tại sao không dùng Next.js hoặc Nuxt.js thay vì Vue 3 + Laravel tách biệt? SSR không tốt hơn cho SEO sao?"

Trả lời xuất sắc:
"Câu hỏi rất hay. Đúng là SSR tốt hơn cho SEO với trang thương mại điện tử. Tuy nhiên em đưa ra quyết định này có cân nhắc:

Thứ nhất, đây là shop phụ kiện điện thoại nhỏ — traffic chủ yếu từ social media và direct, không phụ thuộc organic search nhiều. SEO không phải yêu cầu ưu tiên số 1.

Thứ hai, độ phức tạp triển khai: Nuxt + Laravel SSR yêu cầu Node.js server + PHP server chạy song song, CI/CD phức tạp hơn nhiều — không phù hợp với scope đồ án.

Thứ ba, trải nghiệm phát triển: Tách biệt hoàn toàn Frontend/Backend cho phép em tập trung vào từng phần, dễ debug, dễ maintain.

Nếu scale lên và SEO trở thành priority, hướng phát triển là thêm Vue Router với prerender-spa-plugin cho các trang tĩnh như danh mục, hoặc migrate Frontend sang Nuxt 3 với API vẫn là Laravel không đổi — kiến trúc decoupled giúp điều này dễ thực hiện."

Lời khuyên cuối: Nắm chắc 4 điểm kỹ thuật này để tự tin trả lời bất kỳ câu hỏi nào:

Transaction + Stock decrement trong OrderController — đây là logic phức tạp nhất
Event-Driven Email — tại sao dùng Queue, không dùng sync
Tách bảng admins/users — tại sao không dùng role trong cùng bảng
COALESCE trong SQL — tại sao sort/filter theo giá cần xử lý đặc biệt