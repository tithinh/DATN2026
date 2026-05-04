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

    /* Header */
    .header { background: linear-gradient(135deg, #0a1628 0%, #1a2d4a 100%); padding: 36px 40px; text-align: center; }
    .header .logo { display: inline-block; margin-bottom: 20px; }
    .header .logo img { height: 64px; width: auto; display: block; }
    .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
    .header p { color: #94a3b8; font-size: 14px; margin-top: 6px; }

    /* Body */
    .body { padding: 36px 40px; }
    .greeting { font-size: 16px; color: #334155; margin-bottom: 24px; line-height: 1.7; }
    .greeting strong { color: #0f172a; }

    /* Order box — dùng table để tránh dính nhau */
    .order-box { background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 28px; }
    .order-box h2 { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
    .order-meta-table { width: 100%; border-collapse: collapse; }
    .order-meta-table td { vertical-align: top; padding-right: 16px; width: 33%; }
    .order-meta-table td:last-child { padding-right: 0; }
    .meta-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 6px; }
    .meta-value { font-size: 14px; font-weight: 700; color: #0f172a; display: block; }
    .meta-value.code { color: #ff6b35; font-size: 15px; }
    .meta-value.status { display: inline-block; background: #fef3c7; color: #d97706; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }

    /* Items — dùng table */
    .items-section { margin-bottom: 4px; }
    .items-section h2 { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table tr { border-bottom: 1px solid #f1f5f9; }
    .items-table tr:last-child { border-bottom: none; }
    .items-table td { padding: 14px 0; vertical-align: middle; }
    .items-table td.td-info { width: 65%; padding-right: 16px; }
    .items-table td.td-price { width: 35%; text-align: right; }
    .item-name { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
    .item-variant { font-size: 12px; color: #64748b; }
    .unit-price { font-size: 12px; color: #94a3b8; margin-bottom: 3px; }
    .total-price { font-size: 14px; font-weight: 700; color: #0f172a; }

    /* Summary — dùng table */
    .summary { background: #f8fafc; border-radius: 12px; padding: 20px 24px; margin-top: 20px; }
    .summary-table { width: 100%; border-collapse: collapse; }
    .summary-table tr td { padding: 7px 0; font-size: 14px; color: #475569; }
    .summary-table tr td:last-child { text-align: right; font-weight: 600; color: #0f172a; }
    .summary-table tr.discount td { color: #16a34a; }
    .summary-table tr.total td { font-size: 16px; font-weight: 800; color: #0f172a; border-top: 1.5px solid #e2e8f0; padding-top: 14px; }
    .summary-table tr.total td:last-child { color: #ff6b35; }

    /* Payment */
    .payment-section { margin-top: 24px; padding: 20px 24px; border: 1.5px solid #e2e8f0; border-radius: 12px; }
    .payment-section h2 { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .payment-method { font-size: 14px; font-weight: 600; color: #0f172a; }
    .vietqr-info { margin-top: 12px; padding: 14px; background: #fff7ed; border-radius: 10px; border: 1px solid #fed7aa; }
    .vietqr-info p { font-size: 13px; color: #9a3412; line-height: 1.8; }

    /* Address */
    .address-section { margin-top: 16px; padding: 20px 24px; border: 1.5px solid #e2e8f0; border-radius: 12px; }
    .address-section h2 { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
    .address-section p { font-size: 14px; color: #334155; line-height: 1.8; }

    /* CTA */
    .cta { text-align: center; margin-top: 32px; }
    .cta-btn { display: inline-block; padding: 14px 36px; background: linear-gradient(135deg, #ff6b35, #f7931e); color: #fff; text-decoration: none; border-radius: 10px; font-size: 15px; font-weight: 700; box-shadow: 0 4px 15px rgba(255,107,53,0.3); }

    /* Footer */
    .footer { background: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; margin-top: 0; }
    .footer p { font-size: 13px; color: #94a3b8; line-height: 1.7; }
    .footer a { color: #ff6b35; text-decoration: none; }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="header">
    <div class="logo">
      <img src="{{ config('app.frontend_url', 'http://localhost:5173') }}/images/logo-t5.png" alt="FiveTech Store" />
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
      <table class="order-meta-table">
        <tr>
          <td>
            <span class="meta-label">Mã đơn hàng</span>
            <span class="meta-value code">#{{ $order->order_code }}</span>
          </td>
          <td>
            <span class="meta-label">Ngày đặt</span>
            <span class="meta-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
          </td>
          <td>
            <span class="meta-label">Trạng thái</span>
            <span class="meta-value"><span class="status">Chờ xử lý</span></span>
          </td>
        </tr>
      </table>
    </div>

    <!-- Items -->
    <div class="items-section">
      <h2>Sản phẩm đã đặt</h2>
      <table class="items-table">
        @foreach($order->items as $item)
          @php
            $product = $item->product ?? $item->variant?->product;
            $productName = $product?->name ?? 'Sản phẩm';
            $variantText = $item->variant ? ($item->variant->color . ($item->variant->storage_size ? ' / ' . $item->variant->storage_size : '')) : null;
            $price = $item->price_at_purchase ?? $item->price ?? 0;
          @endphp
          <tr>
            <td class="td-info">
              <div class="item-name">{{ $productName }}</div>
              @if($variantText)
                <div class="item-variant">{{ $variantText }} &times; {{ $item->quantity }}</div>
              @else
                <div class="item-variant">Số lượng: {{ $item->quantity }}</div>
              @endif
            </td>
            <td class="td-price">
              <div class="unit-price">{{ number_format($price, 0, ',', '.') }}đ / cái</div>
              <div class="total-price">{{ number_format($price * $item->quantity, 0, ',', '.') }}đ</div>
            </td>
          </tr>
        @endforeach
      </table>
    </div>

    <!-- Summary -->
    <div class="summary">
      <table class="summary-table">
        <tr>
          <td>Tạm tính</td>
          <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr class="discount">
          <td>Giảm giá</td>
          <td>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
        </tr>
        @endif
        @php
          $shipping = ($order->final_amount ?? $order->total_amount) - $order->total_amount + ($order->discount_amount ?? 0);
        @endphp
        <tr>
          <td>Phí vận chuyển</td>
          @if($shipping > 0)
            <td>{{ number_format($shipping, 0, ',', '.') }}đ</td>
          @else
            <td style="color:#16a34a;">Miễn phí</td>
          @endif
        </tr>
        <tr class="total">
          <td>Tổng thanh toán</td>
          <td>{{ number_format($order->final_amount ?? $order->total_amount, 0, ',', '.') }}đ</td>
        </tr>
      </table>
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
      <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/track-order" class="cta-btn">
        Tra cứu đơn hàng
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
