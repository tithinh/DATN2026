<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Tạo link thanh toán VNPay/Momo cho order
     */
    public function createPayment(Request $request, $orderId)
    {
        $order = \App\Models\Order::where('order_id', $orderId)->firstOrFail();

        $validated = $request->validate([
            'provider' => 'required|in:vnpay,momo',
            'return_url' => 'required|url',
        ]);

        $provider = $validated['provider'];
        $amount = $order->final_amount * 100; // VNPay/Momo dùng cents
        $orderCode = $order->order_code;
        $returnUrl = $validated['return_url'];

        if ($provider === 'vnpay') {
            return $this->createVNPayPayment($orderCode, $amount, $returnUrl);
        } elseif ($provider === 'momo') {
            return $this->createMomoPayment($orderCode, $amount, $returnUrl);
        }
    }

    private function createVNPayPayment($orderCode, $amount, $returnUrl)
    {
        $vnp_TmnCode = config('payment.vnpay.tmn_code');
        $vnp_HashSecret = config('payment.vnpay.hash_secret');
        $vnp_Url = config('payment.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_Returnurl = $returnUrl;

        $vnp_TxnRef = $orderCode;
        $vnp_OrderInfo = 'Thanh toan don hang ' . $orderCode;
        $vnp_OrderType = 'other';
        $vnp_Amount = $amount;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $this->getClientIp(request()); // Pass request
        $vnp_CreateDate = date('YmdHis');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $vnp_CreateDate,
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&');
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        return response()->json([
            'payment_url' => $vnp_Url,
            'order_code' => $orderCode,
        ]);
    }

    private function createMomoPayment($orderCode, $amount, $returnUrl)
    {
        $endpoint = config('payment.momo.endpoint', 'https://test-payment.momo.vn/gw_payment/transactionProcessor');
        $partnerCode = config('payment.momo.partner_code');
        $accessKey = config('payment.momo.access_key');
        $secretKey = config('payment.momo.secret_key');

        $orderId = time() . '';
        $requestId = time() . '';
        $amountStr = (string)$amount;
        $orderInfo = 'Thanh toan don hang ' . $orderCode;
        $returnUrlMomo = $returnUrl;
        $notifyurl = config('app.url') . '/api/v1/payment/momo/ipn';
        $extraData = '';

        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amountStr . "&extraData=" . $extraData . "&ipnUrl=" . $notifyurl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $returnUrlMomo . "&requestId=" . $requestId . "&requestType=captureWallet";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => 'FiveTech Store',
            'storeId' => 'MOMO5TECH',
            'requestId' => $requestId,
            'amount' => $amountStr,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $returnUrlMomo,
            'ipnUrl' => $notifyurl,
            'lang' => 'vn',
            'extraData' => $extraData,
            'requestType' => 'captureWallet',
            'signature' => $signature,
        ];

        $result = Http::post($endpoint, $data);

        return response()->json([
            'pay_url' => $result['payUrl'] ?? null,
            'order_code' => $orderCode,
            'resultCode' => $result['resultCode'] ?? null,
        ]);
    }

    /**
     * VNPay/Momo callback
     */
    public function handleCallback(Request $request)
    {
        $inputData = $request->all();
        $orderCode = $inputData['vnp_TxnRef'] ?? $inputData['orderId'] ?? null;
        $status = $inputData['vnp_ResponseCode'] ?? $inputData['resultCode'] ?? null;

        $order = \App\Models\Order::where('order_code', $orderCode)->first();

        if ($order && $status == 0 || $status == '00') {
            $order->update(['status' => 'paid']);
            return 'Payment success!';
        }

        return 'Payment failed!';
    }

    private function getClientIp(Request $request)
    {
        return $request->ip();
    }
}


