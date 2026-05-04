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
        $authUser = Auth::guard('sanctum')->user();

        if (!$authUser) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để đánh giá',
            ], 401);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating'  => 'nullable|integer|between:1,5',
            'parent_id' => 'nullable|exists:comments,comment_id',
        ]);

$product = Product::findOrFail($product_id);

        // Check if user already rated
        if (Comment::userRated($authUser->getKey(), $product_id)->exists()) {
            return response()->json([
                'message' => 'Bạn đã đánh giá sản phẩm này. Vui lòng vào Chi tiết đơn hàng để xem/sửa/xóa đánh giá.',
            ], 409);
        }

        $comment = Comment::create([
            'product_id'  => $product_id,
            'user_id'     => $authUser->getKey(),
            'parent_id'   => $validated['parent_id'] ?? null,
            'content'     => $validated['content'],
            'rating'      => $validated['rating'] ?? null,
            'status'      => 'approved',
        ]);

        $comment->load(['user' => fn($q) => $q->select('user_id', 'full_name', 'avatar')]);

        return response()->json([
            'message' => 'Đánh giá đã được đăng thành công',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Get user's rating for specific product (GET /api/v1/products/{product_id}/rating)
     */
    public function userRating($product_id)
    {
        $authUser = Auth::guard('sanctum')->user();
        if (!$authUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $rating = Comment::userRated($authUser->getKey(), $product_id)
                        ->with('user:id,user_id,full_name,avatar')
                        ->first();

        if (!$rating) {
            return response()->json(null, 200);
        }

        return response()->json($rating, 200);
    }

    /**
     * Trả lời bình luận (reply)
     * POST /api/v1/comments/{comment_id}/reply
     */
    public function reply(Request $request, $comment_id)
    {
    try {
        $authUser = Auth::guard('sanctum')->user() ?? Auth::guard('admin')->user();
        if (!$authUser) {
            return response()->json(['message' => 'Vui lòng đăng nhập để trả lời'], 401);
        }

        $parent = Comment::find($comment_id);
        if (!$parent) {
            return response()->json(['message' => 'Đánh giá gốc không tồn tại'], 404);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $reply = Comment::create([
            'product_id' => $parent->product_id,
            'user_id'    => $authUser->getKey(),
            'parent_id'  => $comment_id,
            'content'    => $validated['content'],
            'rating'     => null,
            'status'     => 'approved',
        ]);

        $reply->load(['user' => fn($q) => $q->select('user_id', 'full_name', 'avatar')]);

        return response()->json([
            'message' => 'Trả lời thành công',
            'reply' => $reply,
        ], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Reply comment failed: ' . $e->getMessage());
        return response()->json([
            'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Cập nhật bình luận của chính người dùng
     * PUT /api/v1/comments/{comment_id}
     */
    public function update(Request $request, $comment_id)
    {
        $authUser = Auth::guard('sanctum')->user();
        $comment = Comment::findOrFail($comment_id);

        if ($comment->user_id !== $authUser->getKey()) {
            return response()->json([
                'message' => 'Bạn không có quyền chỉnh sửa đánh giá này',
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'rating'  => 'nullable|integer|between:1,5',
        ]);

        $comment->update([
            'content' => $validated['content'],
            'rating'  => $validated['rating'] ?? $comment->rating,
            'status'  => 'approved',
        ]);

        return response()->json([
            'message' => 'Đánh giá đã được cập nhật',
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
        $authUser = Auth::guard('sanctum')->user();
        if (!$authUser) {
            return response()->json(['message' => 'Vui lòng đăng nhập để like'], 401);
        }

        $comment = Comment::findOrFail($comment_id);

        $alreadyLiked = $comment->likes()->where('user_id', $authUser->getKey())->exists();

        if ($alreadyLiked) {
            return response()->json(['message' => 'Bạn đã like đánh giá này'], 409);
        }

        $comment->likes()->attach($authUser->getKey());

        return response()->json([
            'message' => 'Đã like đánh giá',
            'likes_count' => $comment->likes()->count(),
        ]);
    }

    public function adminIndex(Request $request)
    {
        $query = Comment::query()
            ->with(['user' => function ($q) {
                $q->select('user_id', 'full_name', 'email');
            }, 'product' => function ($q) {
                $q->select('product_id', 'name');
            }])
            ->select('comment_id', 'user_id', 'product_id', 'content', 'rating', 'created_at', 'parent_id')
            ->whereNull('parent_id') // chỉ lấy đánh giá gốc, không lấy reply
            ->orderBy('created_at', 'desc');

        // Tìm kiếm theo nội dung hoặc tên người dùng
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', $search)
                  ->orWhereHas('user', fn($u) => $u->where('full_name', 'like', $search));
            });
        }

        // Lọc theo số sao
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

$perPage = min(max((int) $request->input('per_page', 10), 1), 10);
        $comments = $query->paginate($perPage);

        // Thống kê tổng quan
        $stats = [
            'total'             => Comment::whereNull('parent_id')->count(),
            'total_comments'    => Comment::whereNull('parent_id')->comments()->count(),
            'total_ratings'     => Comment::whereNull('parent_id')->ratings()->count(),
        ];

        // Transform dữ liệu
        $comments->getCollection()->transform(function ($comment) {
            return [
                'id'           => $comment->getKey(),
                'author'       => $comment->user->full_name ?? 'Khách',
                'author_email' => $comment->user->email ?? null,
                'user_id'      => $comment->user_id,
                'content'      => $comment->content,
                'product_name' => $comment->product->name ?? '—',
                'product_id'   => $comment->product_id,
                'rating'       => $comment->rating,
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
            'stats'        => $stats,
        ]);
    }

}
