<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xác nhận đơn hàng</title>
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

    .order-box { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
    .order-box h2 { font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
    .order-meta { display: flex; flex-wrap: wrap; gap: 12px; }
    .meta-item { flex: 1; min-width: 140px; }
    .meta-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
    .meta-value { font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 4px; }
    .meta-value.code { color: #ff6b35; font-size: 17px; }
    .meta-value.status { display: inline-block; background: #fef3c7; color: #d97706; padding: 3px 12px; border-radius: 20px; font-size: 13px; }

    .items-section h2 { font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
    .item-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
    .item-row:last-child { border-bottom: none; }
    .item-info .item-name { font-size: 15px; font-weight: 600; color: #0f172a; }
    .item-info .item-variant { font-size: 13px; color: #64748b; margin-top: 3px; }
    .item-price { text-align: right; }
    .item-price .unit-price { font-size: 13px; color: #94a3b8; }
    .item-price .total-price { font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 2px; }

    .summary { background: #f8fafc; border-radius: 12px; padding: 20px 24px; margin-top: 24px; }
    .summary-row { display: flex; justify-content: space-between; font-size: 14px; color: #475569; padding: 6px 0; }
    .summary-row.discount { color: #16a34a; }
    .summary-row.total { font-size: 17px; font-weight: 800; color: #0f172a; border-top: 1.5px solid #e2e8f0; margin-top: 8px; padding-top: 14px; }
    .summary-row.total span:last-child { color: #ff6b35; }

    .payment-section { margin-top: 28px; padding: 20px 24px; border: 1.5px solid #e2e8f0; border-radius: 12px; }
    .payment-section h2 { font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .payment-method { font-size: 15px; font-weight: 600; color: #0f172a; }
    @if($order->payment_method === 'vietqr')
    .vietqr-info { margin-top: 12px; padding: 14px; background: #fff7ed; border-radius: 10px; border: 1px solid #fed7aa; }
    .vietqr-info p { font-size: 13px; color: #9a3412; line-height: 1.7; }
    @endif

    .address-section { margin-top: 20px; padding: 20px 24px; border: 1.5px solid #e2e8f0; border-radius: 12px; }
    .address-section h2 { font-size: 15px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
    .address-section p { font-size: 14px; color: #334155; line-height: 1.7; }

    .cta { text-align: center; margin-top: 32px; }
    .cta-btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 15px rgba(255,107,53,0.3); }

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
    <h1>Đơn hàng đã được đặt thành công!</h1>
    <p>Cảm ơn bạn đã tin tưởng và mua sắm tại FiveTech Store</p>
  </div>

  <!-- Body -->
  <div class="body">
    <p class="greeting">
      Xin chào <strong>{{ $order->customer_name ?? 'Quý khách' }}</strong>,<br>
      Chúng tôi đã nhận được đơn hàng của bạn và đang tiến hành xử lý. Dưới đây là thông tin chi tiết đơn hàng của bạn.
    </p>

    <!-- Order Info -->
    <div class="order-box">
      <h2>Thông tin đơn hàng</h2>
      <div class="order-meta">
        <div class="meta-item">
          <div class="meta-label">Mã đơn hàng</div>
          <div class="meta-value code">#{{ $order->order_code }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Ngày đặt</div>
          <div class="meta-value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Trạng thái</div>
          <div class="meta-value"><span class="status">Chờ xử lý</span></div>
        </div>
      </div>
    </div>

    <!-- Items -->
    <div class="items-section">
      <h2>Sản phẩm đã đặt</h2>
      @foreach($order->items as $item)
        @php
          $product = $item->product ?? $item->variant?->product;
          $productName = $product?->name ?? 'Sản phẩm';
          $variantText = $item->variant ? ($item->variant->color . ($item->variant->storage_size ? ' / ' . $item->variant->storage_size : '')) : null;
          $price = $item->price_at_purchase ?? $item->price ?? 0;
        @endphp
        <div class="item-row">
          <div class="item-info">
            <div class="item-name">{{ $productName }}</div>
            @if($variantText)
              <div class="item-variant">{{ $variantText }} &times; {{ $item->quantity }}</div>
            @else
              <div class="item-variant">Số lượng: {{ $item->quantity }}</div>
            @endif
          </div>
          <div class="item-price">
            <div class="unit-price">{{ number_format($price, 0, ',', '.') }}đ / cái</div>
            <div class="total-price">{{ number_format($price * $item->quantity, 0, ',', '.') }}đ</div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Summary -->
    <div class="summary">
      <div class="summary-row">
        <span>Tạm tính</span>
        <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
      </div>
      @if($order->discount_amount > 0)
      <div class="summary-row discount">
        <span>Giảm giá</span>
        <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
      </div>
      @endif
      @php
        $shipping = ($order->final_amount ?? $order->total_amount) - $order->total_amount + ($order->discount_amount ?? 0);
      @endphp
      @if($shipping > 0)
      <div class="summary-row">
        <span>Phí vận chuyển</span>
        <span>{{ number_format($shipping, 0, ',', '.') }}đ</span>
      </div>
      @else
      <div class="summary-row">
        <span>Phí vận chuyển</span>
        <span style="color:#16a34a">Miễn phí</span>
      </div>
      @endif
      <div class="summary-row total">
        <span>Tổng thanh toán</span>
        <span>{{ number_format($order->final_amount ?? $order->total_amount, 0, ',', '.') }}đ</span>
      </div>
    </div>

    <!-- Payment -->
    <div class="payment-section">
      <h2>Phương thức thanh toán</h2>
      @if($order->payment_method === 'cod')
        <div class="payment-method">💵 Thanh toán khi nhận hàng (COD)</div>
      @elseif($order->payment_method === 'vietqr')
        <div class="payment-method">🏦 Chuyển khoản VietQR</div>
        <div class="vietqr-info">
          <p>
            <strong>Ngân hàng:</strong> Vietcombank (VCB)<br>
            <strong>Số tài khoản:</strong> 1021850576<br>
            <strong>Chủ tài khoản:</strong> NGUYEN TIEN THINH<br>
            <strong>Nội dung CK:</strong> Thanh toan {{ $order->order_code }}<br>
            <strong>Số tiền:</strong> {{ number_format($order->final_amount ?? $order->total_amount, 0, ',', '.') }}đ
          </p>
        </div>
      @endif
    </div>

    <!-- Address -->
    @if($order->shipping_address || $order->customer_address)
    <div class="address-section">
      <h2>Địa chỉ giao hàng</h2>
      <p>
        <strong>{{ $order->customer_name ?? 'Quý khách' }}</strong><br>
        {{ $order->customer_phone ?? '' }}<br>
        {{ $order->shipping_address ?? $order->customer_address }}
      </p>
    </div>
    @endif

    <!-- CTA -->
    <div class="cta">
      <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/orders/{{ $order->order_code }}" class="cta-btn">
        Xem chi tiết đơn hàng
      </a>
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
