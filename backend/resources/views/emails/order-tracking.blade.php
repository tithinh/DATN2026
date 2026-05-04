<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tra cứu đơn hàng</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; color: #1a1a2e; }
    .wrapper { max-width: 620px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%); padding: 36px 40px; text-align: center; }
    .header .logo { display: inline-flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .logo-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #ff6b35, #f7931e); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; font-weight: 800; }
    .logo-text { color: #fff; font-size: 26px; font-weight: 800; }
    .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
    .header p { color: #94a3b8; font-size: 14px; margin-top: 6px; }

    .body { padding: 36px 40px; }

    .greeting { font-size: 16px; color: #334155; margin-bottom: 24px; line-height: 1.6; }
    .greeting strong { color: #0f172a; }

    .order-list { display: flex; flex-direction: column; gap: 14px; margin-bottom: 28px; }
    .order-card { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .order-info { flex: 1; min-width: 200px; }
    .order-code { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .order-code span { color: #ff6b35; }
    .order-meta { font-size: 13px; color: #64748b; }
    .order-status { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 8px; }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-processing { background: #e0e7ff; color: #4338ca; }
    .status-shipping { background: #dbeafe; color: #2563eb; }
    .status-delivered { background: #dcfce7; color: #16a34a; }
    .status-completed { background: #dcfce7; color: #16a34a; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }
    .order-link { display: inline-block; padding: 10px 22px; background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 15px rgba(255,107,53,0.3); white-space: nowrap; }
    .order-link:hover { box-shadow: 0 6px 20px rgba(255,107,53,0.4); }

    .notice { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 16px 20px; margin-top: 8px; }
    .notice p { font-size: 13px; color: #9a3412; line-height: 1.7; }

    .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
    .footer p { font-size: 13px; color: #94a3b8; line-height: 1.7; }
    .footer a { color: #ff6b35; text-decoration: none; }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Header -->
  <div class="header">
    <div class="logo">
      <span class="logo-icon">T5</span>
      <span class="logo-text">FiveTech</span>
    </div>
    <h1>Thông tin tra cứu đơn hàng</h1>
    <p>Danh sách đơn hàng được đặt bằng email của bạn</p>
  </div>

  <!-- Body -->
  <div class="body">
    <p class="greeting">
      Xin chào <strong>{{ $orders->first()->customer_name ?? 'Quý khách' }}</strong>,<br>
      Dưới đây là các đơn hàng được đặt bằng email <strong>{{ $orders->first()->customer_email ?? $orders->first()->user?->email }}</strong> trong 90 ngày qua. Bạn có thể nhấn vào từng đơn hàng để xem chi tiết mà <strong>không cần đăng nhập</strong>.
    </p>

    <!-- Order List -->
    <div class="order-list">
      @foreach($orders as $order)
        @php
          $statusClass = match($order->status) {
            'pending' => 'status-pending',
            'processing' => 'status-processing',
            'shipping' => 'status-shipping',
            'delivered' => 'status-delivered',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled',
            default => 'status-pending',
          };
          $statusText = match($order->status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => $order->status,
          };
        @endphp
        <div class="order-card">
          <div class="order-info">
            <div class="order-code">Mã đơn: <span>#{{ $order->order_code }}</span>
              <span class="order-status {{ $statusClass }}">{{ $statusText }}</span>
            </div>
            <div class="order-meta">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }} &nbsp;|&nbsp; Tổng: {{ number_format($order->final_amount ?? $order->total_amount, 0, ',', '.') }}đ</div>
          </div>
          <a href="{{ $frontendUrl }}/orders/{{ $order->order_code }}" class="order-link">Xem chi tiết</a>
        </div>
      @endforeach
    </div>

    <!-- Notice -->
    <div class="notice">
      <p>
        <strong>Lưu ý:</strong> Các link tra cứu đơn hàng là riêng tư. Vui lòng không chia sẻ email này cho ngườ    khác để đảm bảo an toàn thông tin đơn hàng của bạn.
      </p>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p>
      Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email
      <a href="mailto:support@fivetech.vn">support@fivetech.vn</a>
    </p>
    <p style="margin-top:8px">© {{ date('Y') }} FiveTech Store. All rights reserved.</p>
  </div>
</div>
</body>
</html>

