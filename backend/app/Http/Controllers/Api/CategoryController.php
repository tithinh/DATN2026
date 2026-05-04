<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Danh sách danh mục (cho frontend public)
     */
    public function index(Request $request)
    {
        try {
            $query = Category::query()
                ->select('category_id', 'name', 'slug', 'parent_id', 'description', 'icon', 'sort_order', 'is_active', 'is_featured', 'created_at')
                ->where('is_active', true) // chỉ lấy danh mục đang hiển thị
                ->orderBy('sort_order', 'asc');

            // Tìm kiếm nếu có
            if ($request->filled('search')) {
                $search = '%' . $request->search . '%';
                $query->where('name', 'like', $search);
            }

            // Phân trang
            $perPage = $request->input('per_page', 20);
            $categories = $query->paginate($perPage);

            // Thêm products_count an toàn (nếu có quan hệ)
            $categories->getCollection()->transform(function ($category) {
                $category->products_count = $category->products()->count(); // an toàn
                return $category;
            });

            return response()->json([
                'data' => $categories->items(),
                'total' => $categories->total(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh mục (index): ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            return response()->json([
                'message' => 'Lỗi hệ thống khi tải danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Danh sách danh mục cho admin (không lọc is_active)
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = Category::query()
                ->select('category_id', 'name', 'slug', 'parent_id', 'description', 'icon', 'sort_order', 'is_active', 'is_featured', 'created_at')
                ->orderBy('sort_order', 'asc');

            if ($request->filled('search')) {
                $search = '%' . $request->search . '%';
                $query->where('name', 'like', $search);
            }

            $perPage = $request->input('per_page', 10);
            $categories = $query->paginate($perPage);

            $categories->getCollection()->transform(function ($category) {
                $category->products_count = $category->products()->count();
                return $category;
            });

            return response()->json([
                'data' => $categories->items(),
                'total' => $categories->total(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh mục admin: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi hệ thống'], 500);
        }
    }

    /**
     * Thêm danh mục mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'sometimes|required|string|unique:categories,slug',
            'parent_id'   => 'nullable|exists:categories,category_id',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:7',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        // Map is_visible to is_active if present
        if ($request->has('is_visible')) {
            $validated['is_active'] = $request->is_visible;
        }

        $validated['is_featured'] = $validated['is_featured'] ?? false;

        $category = Category::create($validated);

        return response()->json(['success' => true, 'category' => $category], 201);
    }

    /**
     * Cập nhật danh mục
     */
    public function update($category_id, Request $request)
    {
        $category = Category::findOrFail($category_id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'sometimes|required|string|unique:categories,slug,' . $category_id,
            'parent_id'   => 'nullable|exists:categories,category_id',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:255',
            'color'       => 'nullable|string|max:7',
            'sort_order'  => 'nullable|integer',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($request->name);
        }

        // Map is_visible to is_active if present
        if ($request->has('is_visible')) {
            $validated['is_active'] = $request->is_visible;
        }

        $validated['is_featured'] = $validated['is_featured'] ?? false;

        $category->update($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    /**
     * Toggle trạng thái hiển thị (ẩn/hiện danh mục)
     */
    public function toggleStatus($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
            'message' => $category->is_active ? 'Đã hiển thị danh mục' : 'Đã ẩn danh mục'
        ]);
    }

    public function destroy($category_id)
    {
        $category = Category::findOrFail($category_id);

        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Không thể xóa danh mục đang có sản phẩm'], 409);
        }

        $category->delete();
        return response()->json(['message' => 'Đã xóa danh mục thành công']);
    }
}
