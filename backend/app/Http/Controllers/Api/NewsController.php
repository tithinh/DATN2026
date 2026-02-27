<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Get all news (public)
     */
    public function index(Request $request)
    {
        $query = News::where('status', 'published')
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Pagination
        $perPage = $request->per_page ?? 10;
        $news = $query->paginate($perPage);

        return response()->json($news);
    }

    /**
     * Get single news (public)
     */
    public function show($slug)
    {
        $news = News::where('slug', $slug)->first();

        if (!$news) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        // Increment views
        $news->increment('views');

        return response()->json($news);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = News::where('status', 'published')
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json($categories);
    }

    /**
     * Get popular posts
     */
    public function popular(Request $request)
    {
        $limit = $request->limit ?? 5;
        $news = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($news);
    }

    // ======================
    // ADMIN ROUTES
    // ======================

    /**
     * Get all news for admin (including drafts)
     */
    public function adminIndex(Request $request)
    {
        $query = News::orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $perPage = $request->per_page ?? 15;
        $news = $query->paginate($perPage);

        return response()->json($news);
    }

    /**
     * Get single news for admin
     */
    public function adminShow($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        return response()->json($news);
    }

    /**
     * Create news
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'status' => 'nullable|in:published,draft'
        ]);

        $news = News::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'image' => $validated['image'] ?? null,
            'category' => $validated['category'] ?? 'Tin tức',
            'author' => $validated['author'] ?? 'Admin',
            'status' => $validated['status'] ?? 'published',
            'views' => 0
        ]);

        return response()->json($news, 201);
    }

    /**
     * Update news
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:100',
            'status' => 'nullable|in:published,draft'
        ]);

        if (isset($validated['title'])) {
            $news->slug = Str::slug($validated['title']);
        }

        $news->update($validated);

        return response()->json($news);
    }

    /**
     * Delete news
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $news->delete();

        return response()->json(['message' => 'Xóa bài viết thành công']);
    }

    /**
     * Toggle news status
     */
    public function toggleStatus($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $news->status = $news->status === 'published' ? 'draft' : 'published';
        $news->save();

        return response()->json($news);
    }
}
