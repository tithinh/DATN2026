<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = UserAddress::where('user_id', $request->user()->getKey())
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:500',
            'is_default'=> 'boolean',
        ]);

        $userId = $request->user()->getKey();

        if (!empty($validated['is_default'])) {
            UserAddress::where('user_id', $userId)->update(['is_default' => false]);
        }

        // Nếu chưa có địa chỉ nào, tự set default
        if (UserAddress::where('user_id', $userId)->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = UserAddress::create(array_merge($validated, ['user_id' => $userId]));

        return response()->json($address, 201);
    }

    public function update(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', $request->user()->getKey())
            ->firstOrFail();

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:500',
            'is_default'=> 'boolean',
        ]);

        if (!empty($validated['is_default'])) {
            UserAddress::where('user_id', $request->user()->getKey())
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json($address);
    }

    public function destroy(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', $request->user()->getKey())
            ->firstOrFail();

        $wasDefault = $address->is_default;
        $address->delete();

        // Nếu xóa địa chỉ mặc định, tự set địa chỉ đầu tiên còn lại làm default
        if ($wasDefault) {
            $first = UserAddress::where('user_id', $request->user()->getKey())->first();
            if ($first) $first->update(['is_default' => true]);
        }

        return response()->json(['message' => 'Đã xóa địa chỉ']);
    }

    public function setDefault(Request $request, $id)
    {
        $userId = $request->user()->getKey();

        UserAddress::where('user_id', $userId)->update(['is_default' => false]);

        $address = UserAddress::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $address->update(['is_default' => true]);

        return response()->json(['message' => 'Đã đặt địa chỉ mặc định']);
    }
}
