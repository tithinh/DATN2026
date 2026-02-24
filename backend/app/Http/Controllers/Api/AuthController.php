<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Đăng ký người dùng mới
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name'             => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'address'               => 'nullable|string|max:255',
            'password'              => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'address'   => $validated['address'] ?? null,
            'password'  => $validated['password'], // mutator sẽ tự hash
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user'    => [
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'address'   => $user->address,
                'is_active' => $user->is_active,
            ],
            'token'   => $token,
        ], 201);
    }

    /**
     * Đăng nhập
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Kiểm tra user tồn tại VÀ password đúng
        if (!$user || !Hash::check($validated['password'], $user->password ?? '')) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không chính xác.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Tài khoản của bạn đã bị khóa.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user'    => $user->only(['id', 'full_name', 'email', 'phone', 'address', 'is_active']),
            'token'   => $token,
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công',
        ]);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'address'   => $user->address,
                'is_active' => $user->is_active,
            ],
        ]);
    }
    public function adminLogin(Request $request)
{
    $request->validate([
        'email' => 'required_without:username|email',
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $admin = Admin::where('username', $request->username)
                  ->orWhere('email', $request->username)
                  ->first();

    if (!$admin || !Hash::check($request->password, $admin->password_hash)) {
        return response()->json([
            'message' => 'Thông tin đăng nhập không chính xác'
        ], 401);
    }

    // Tạo token Sanctum cho admin
    $token = $admin->createToken('admin-token')->plainTextToken;

    return response()->json([
        'message' => 'Đăng nhập admin thành công',
        'token' => $token,
        'admin' => [
            'admin_id' => $admin->admin_id,
            'username' => $admin->username,
            'email' => $admin->email,
            'full_name' => $admin->full_name,
            'role' => $admin->role,
        ]
    ]);
}
}
