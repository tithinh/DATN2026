<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function active()
{
    return Promotion::where('is_active', 1)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->get();
}

public function checkCode(Request $request)
{
    $request->validate(['code' => 'required|string']);
    $promo = Promotion::where('code', $request->code)
        ->where('is_active', 1)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->first();

    if (!$promo) return response()->json(['error' => 'Mã không hợp lệ'], 422);

    return response()->json($promo);
}
}
