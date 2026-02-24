<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Thêm bình luận mới cho sản phẩm
     * POST /api/v1/products/{product_id}/comments
     */
    public function store(Request $request, $product_id)
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để bình luận',
            ], 401);
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating'  => 'nullable|integer|between:1,5',
            'parent_id' => 'nullable|exists:comments,comment_id', // bình luận trả lời (reply)
        ]);

        // Kiểm tra sản phẩm tồn tại
        $product = Product::findOrFail($product_id);

        // Tạo bình luận
        $comment = Comment::create([
            'product_id'  => $product_id,
            'user_id'     => Auth::id(),
            'parent_id'   => $validated['parent_id'] ?? null,
            'content'     => $validated['content'],
            'rating'      => $validated['rating'] ?? null,
            'status'      => 'pending', // chờ duyệt hoặc 'approved' nếu không cần duyệt
        ]);

        // Load thêm thông tin user để trả về
        $comment->load('user:id,full_name,avatar');

        return response()->json([
            'message' => 'Bình luận đã được gửi, đang chờ duyệt',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Trả lời bình luận (reply)
     * POST /api/v1/comments/{comment_id}/reply
     */
    public function reply(Request $request, $comment_id)
{
    try {
        // Tìm comment cha
        $parent = Comment::find($comment_id);

        if (!$parent) {
            return response()->json(['message' => 'Bình luận gốc không tồn tại'], 404);
        }

        // Validate
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // Tạo reply
        $reply = Comment::create([
            'product_id' => $parent->product_id,
            'user_id'    => Auth::id(),
            'parent_id'  => $comment_id,
            'content'    => $validated['content'],
            'rating'     => null, // reply không cần rating
            'status'     => 'approved',
        ]);

        $reply->load('user');

        return response()->json([
            'message' => 'Trả lời bình luận thành công',
            'reply' => $reply,
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Reply comment failed: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        return response()->json([
            'message' => 'Lỗi hệ thống khi trả lời bình luận: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Cập nhật bình luận của chính người dùng
     * PUT /api/v1/comments/{comment_id}
     */
    public function update(Request $request, $comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        // Chỉ cho phép chủ sở hữu chỉnh sửa
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Bạn không có quyền chỉnh sửa bình luận này',
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating'  => 'nullable|integer|between:1,5',
        ]);

        $comment->update([
            'content' => $validated['content'],
            'rating'  => $validated['rating'] ?? $comment->rating,
            'status'  => 'pending', // đưa về chờ duyệt lại nếu chỉnh sửa
        ]);

        return response()->json([
            'message' => 'Bình luận đã được cập nhật',
            'comment' => $comment,
        ]);
    }

    /**
     * Xóa bình luận (chỉ chủ sở hữu hoặc admin)
     * DELETE /api/v1/comments/{comment_id}
     */

    /**
     * Like bình luận
     * POST /api/v1/comments/{comment_id}/like
     */
    public function like($comment_id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Vui lòng đăng nhập để like'], 401);
        }

        $comment = Comment::findOrFail($comment_id);

        // Kiểm tra đã like chưa (tùy cách lưu like của bạn)
        // Ví dụ: dùng bảng pivot likes (comment_id, user_id)
        $alreadyLiked = $comment->likes()->where('user_id', Auth::id())->exists();

        if ($alreadyLiked) {
            return response()->json(['message' => 'Bạn đã like bình luận này'], 409);
        }

        $comment->likes()->attach(Auth::id());

        return response()->json([
            'message' => 'Đã like bình luận',
            'likes_count' => $comment->likes()->count(),
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = Comment::query()
            ->with(['user' => function ($q) {
                $q->select('user_id', 'full_name');
            }, 'product' => function ($q) {
                $q->select('product_id', 'name');
            }])
            ->select('id', 'user_id', 'product_id', 'content', 'rating', 'status', 'created_at');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('content', 'like', $search);
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $comments = $query->paginate($perPage);

        // Transform dữ liệu
        $comments->getCollection()->transform(function ($comment) {
            return [
                'id'           => $comment->id,
                'author'       => $comment->user->full_name ?? 'Khách',
                'content'      => $comment->content,
                'product_name' => $comment->product->name ?? '—',
                'rating'       => $comment->rating,
                'status'       => $comment->status,
                'created_at'   => $comment->created_at,
                'avatar_color' => '#' . substr(md5($comment->user->full_name ?? 'guest'), 0, 6),
            ];
        });

        return response()->json([
            'data'         => $comments->items(),
            'total'        => $comments->total(),
            'current_page' => $comments->currentPage(),
            'last_page'    => $comments->lastPage(),
            'per_page'     => $comments->perPage(),
        ]);
    }

    /**
     * Duyệt bình luận
     */
    public function approve($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->status = 'approved';
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt bình luận'
        ]);
    }

    /**
     * Xoá bình luận
     */
    public function destroy($comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá bình luận'
        ]);
    }
}
