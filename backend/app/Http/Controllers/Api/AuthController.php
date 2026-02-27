<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

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
            'password'  => $validated['password'],
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
                'id'         => $user->id,
                'full_name'  => $user->full_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'address'    => $user->address,
                'birthday'   => $user->birthday,
                'gender'     => $user->gender,
                'avatar'     => $user->avatar,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Cập nhật thông tin profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name'  => 'sometimes|string|max:255',
            'phone'      => 'sometimes|string|max:20',
            'address'    => 'sometimes|string|max:255',
            'birthday'   => 'sometimes|date',
            'gender'     => 'sometimes|in:male,female,other',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user'    => [
                'id'         => $user->id,
                'full_name'  => $user->full_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'address'    => $user->address,
                'birthday'   => $user->birthday,
                'gender'     => $user->gender,
                'avatar'     => $user->avatar,
            ],
        ]);
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác',
            ], 400);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đổi mật khẩu thành công',
            'token'   => $token,
        ]);
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => '/storage/' . $avatarPath]);
        }

        return response()->json([
            'message' => 'Cập nhật avatar thành công',
            'avatar'  => $user->avatar,
        ]);
    }

    /**
     * Đăng nhập admin
     */
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

    // ========================
    // SOCIAL LOGIN (Google/Facebook)
    // ========================

    /**
     * Redirect to Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            return $this->handleSocialLogin($googleUser, 'google');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng nhập Google thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redirect to Facebook
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            
            return $this->handleSocialLogin($facebookUser, 'facebook');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng nhập Facebook thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle social login - find or create user
     */
    protected function handleSocialLogin($socialUser, $provider)
    {
        // Find user by social ID or email
        $user = User::where("{$provider}_id", $socialUser->getId())
                   ->orWhere('email', $socialUser->getEmail())
                   ->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'full_name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'     => $socialUser->getEmail(),
                'password'  => Hash::make(Str::random(16)),
                'avatar'    => $socialUser->getAvatar(),
                'is_active' => true,
                "{$provider}_id" => $socialUser->getId(),
            ]);
        } else {
            // Update existing user with social ID if not set
            if (empty($user->{$provider . '_id'})) {
                $user->update([
                    "{$provider}_id" => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar() ?? $user->avatar,
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập ' . ucfirst($provider) . ' thành công',
            'user'    => [
                'id'         => $user->id,
                'full_name'  => $user->full_name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'address'    => $user->address,
                'avatar'     => $user->avatar,
                'is_active'  => $user->is_active,
                'provider'   => $provider,
            ],
            'token'   => $token,
        ]);
    }

    // ========================
    // PASSWORD RESET
    // ========================

    /**
     * Gửi link reset mật khẩu
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate reset token
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link đặt lại mật khẩu đã được gửi đến email của bạn.',
            ]);
        }

        return response()->json([
            'message' => 'Không thể gửi link đặt lại mật khẩu. Vui lòng thử lại sau.',
        ], 500);
    }

    /**
     * Reset mật khẩu
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Mật khẩu đã được đặt lại thành công.',
            ]);
        }

        return response()->json([
            'message' => 'Token không hợp lệ hoặc đã hết hạn.',
        ], 500);
    }
}
