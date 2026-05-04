<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
class AdminController extends Controller
{
    /**
     * Display a listing of admins (paginated, searchable).
     */
    public function index(Request $request)
    {
        try {
            $query = Admin::query()
                ->select('admin_id', 'username', 'email', 'full_name', 'role', 'is_active', 'created_at');

            // Search
            if ($request->filled('search')) {
                $search = '%' . $request->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('full_name', 'like', $search);
                });
            }

            // Filter active
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $perPage = $request->input('per_page', 10);
            $admins = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'data' => $admins->items(),
                'total' => $admins->total(),
                'current_page' => $admins->currentPage(),
                'last_page' => $admins->lastPage(),
                'per_page' => $admins->perPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách admin: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống'], 500);
        }
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'required|email|unique:admins,email',
            'full_name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'is_active' => 'boolean',
        ]);

$admin = Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
            'password_hash' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Tạo admin thành công',
            'admin' => $admin,
        ], 201);
    }

    /**
     * Display the specified admin.
     */
    public function show($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);
        return response()->json($admin);
    }

    /**
     * Update the specified admin.
     */
    public function update($admin_id, Request $request)
    {
        $admin = Admin::findOrFail($admin_id);

        $validated = $request->validate([
            'username' => 'sometimes|string|max:255|unique:admins,username,' . $admin_id . ',admin_id',
            'email' => 'sometimes|email|unique:admins,email,' . $admin_id . ',admin_id',
            'full_name' => 'sometimes|string|max:255',
            'role' => ['sometimes', Rule::in(['admin', 'super_admin'])],
            'new_password' => 'nullable|string|min:6',
            'is_active' => 'boolean',
        ]);

        // Update password if provided
        if (!empty($validated['new_password'])) {
            $admin->password = $validated['new_password'];
            unset($validated['new_password']);
        }

        $admin->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật admin thành công',
            'admin' => $admin,
        ]);
    }

    /**
     * Toggle admin status.
     */
    public function toggleStatus($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);
        $admin->is_active = !$admin->is_active;
        $admin->save();

        return response()->json([
            'success' => true,
            'is_active' => $admin->is_active,
            'message' => $admin->is_active ? 'Kích hoạt admin' : 'Vô hiệu hóa admin',
        ]);
    }

    /**
     * Remove the specified admin (prevent self-delete).
     */
    public function destroy($admin_id)
    {
        $admin = Admin::findOrFail($admin_id);
        $authAdmin = request()->user();

        if ($admin->admin_id === $authAdmin->admin_id) {
            return response()->json(['message' => 'Không thể xóa chính mình'], 403);
        }

        $admin->delete();

        return response()->json(['message' => 'Xóa admin thành công']);
    }
}

