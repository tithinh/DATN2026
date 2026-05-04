<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * Danh sách người dùng (admin hoặc user, tùy route)
     */
    public function index(Request $request)
    {
        try {
            $query = User::query()
                ->select('user_id', 'full_name', 'email', 'phone', 'address', 'role', 'is_active', 'created_at', 'google_id');

            // Tìm kiếm
            if ($request->filled('search')) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('username', 'like', $search);
                });
            }

            // Nếu là route admin, cho phép lọc theo role
            if ($request->route()->getPrefix() === 'admin') {
                $query->whereIn('role', ['admin', 'super_admin', 'user']); // tùy chỉnh
            }

            $perPage = $request->input('per_page', 10);
            $users = $query->paginate($perPage);

            return response()->json([
                'data' => $users->items(),
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách người dùng: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống'], 500);
        }
    }

    /**
     * Cập nhật thông tin người dùng/admin
     */
    public function update($user_id, Request $request)
    {
        $user = User::findOrFail($user_id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'nullable|in:user,admin,super_admin',
            'is_active' => 'boolean',
            'new_password' => 'nullable|string|min:6',
        ]);

        // Nếu là route admin, cho phép đổi role
        if ($request->route()->getPrefix() === 'admin') {
            $user->role = $validated['role'] ?? $user->role;
        }

        // Nếu admin đặt mật khẩu mới cho user
        if (!empty($validated['new_password'])) {
            $user->password = $validated['new_password'];
        }

        // Loại bỏ new_password khỏi validated trước khi update hàng loạt
        unset($validated['new_password']);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'user' => $user
        ]);
    }

    /**
     * Toggle trạng thái hoạt động (khóa/mở khoá)
     */
    public function toggleStatus($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => $user->is_active ? 'Đã mở khoá tài khoản' : 'Đã khoá tài khoản'
        ]);
    }

    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);

        try {
            $user->delete();
            return response()->json(['message' => 'Đã xóa người dùng thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể xóa người dùng vì đang có đơn hàng liên quan'], 409);
        }
    }

    /**
     * Login admin (nếu cần tách riêng)
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->whereIn('role', ['admin', 'super_admin'])
            ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không chính xác'], 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập admin thành công',
            'token' => $token,
            'admin' => $admin->only(['user_id', 'username', 'email', 'full_name', 'role'])
        ]);
    }
}