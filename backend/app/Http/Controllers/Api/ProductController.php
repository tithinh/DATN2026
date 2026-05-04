<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm (trang /products + trang chủ filter)
     */
    public function index(Request $request)
{
    $query = Product::query()
        ->with(['variants', 'category'])
        ->where('is_visible', 1);

    /* ================= FILTER TRANG CHỦ ================= */
    $filter = $request->input('filter');
    if ($filter === 'hot') {
        $query->orderBy('likes_count', 'desc');
    } elseif ($filter === 'new') {
        $query->orderBy('created_at', 'desc');
    } elseif ($filter === 'sale') {
        $query->whereNotNull('discount_price')
              ->whereRaw('discount_price < base_price')
              ->orderByRaw('(base_price - discount_price) DESC');
    }

    /* ================= FILTER SIDEBAR ================= */
    if ($request->filled('category_id')) {
        $ids = array_filter(explode(',', $request->category_id));
        if ($ids) {
            $query->whereIn('category_id', $ids);
        }
    }

    if ($request->filled('min_price')) {
        $query->whereRaw(
            'COALESCE(discount_price, base_price) >= ?',
            [$request->min_price]
        );
    }

    if ($request->filled('max_price')) {
        $query->whereRaw(
            'COALESCE(discount_price, base_price) <= ?',
            [$request->max_price]
        );
    }

    /* ================= SEARCH ================= */
    if ($request->filled('search')) {
        $search = mb_strtolower(trim($request->search));

        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(short_desc) LIKE ?', ["%{$search}%"])
              ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
        });
    }

    /* ================= SORT ================= */
    $sort = $request->input('sort', 'newest');
    switch ($sort) {
        case 'price_asc':
            $query->orderByRaw('COALESCE(discount_price, base_price) ASC');
            break;

        case 'price_desc':
            $query->orderByRaw('COALESCE(discount_price, base_price) DESC');
            break;

        case 'bestseller':
            $query->orderBy('likes_count', 'desc');
            break;

        case 'rating':
            $query->orderBy('average_rating', 'desc');
            break;

        case 'newest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    /* ================= PAGINATION ================= */
    $perPage = $request->input('per_page', 12);
    $products = $query->paginate($perPage);

    /* ================= TRANSFORM ================= */
    $products->getCollection()->transform(function ($p) {
        $p->final_price = $p->discount_price ?? $p->base_price;
        return $p;
    });

    return response()->json($products);
}


    /**
     * Chi tiết sản phẩm theo slug - WITH VARIANTS
     */
public function show($slug)
{
    try {
        Log::info("Loading product + variants: " . $slug);

        $product = Product::select([
            'product_id', 'name', 'slug', 'description', 'short_desc',
            'base_price', 'discount_price', 'stock_total', 'likes_count',
            'category_id', 'is_visible', 'is_featured', 'created_at'
        ])
        ->where('slug', $slug)
        ->where('is_visible', 1)
        ->first();

        if (!$product) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại',
                'debug' => ['slug' => $slug]
            ], 404);
        }

        // Load variants SAFE - no 'price' column issue
        $variants = \App\Models\ProductVariant::where('product_id', $product->product_id)
            ->select('variant_id', 'product_id', 'color', 'storage_size', 'name', 'price_extra', 'sku', 'stock', 'image_urls')
            ->get();

        $product->variants = $variants;
        $product->category = (object)['name' => 'Phụ kiện'];
$product->load([
    'comments' => function($query) {
        $query->where('status', 'approved')
              ->whereNull('parent_id')
              ->with(['user:user_id,user_id,full_name,avatar', 'replies.user:user_id,user_id,full_name,avatar'])
              ->orderBy('created_at', 'desc')
              ->limit(100);
    }
]);

        $product->final_price = $product->discount_price ?? $product->base_price;
        $product->sales_count = $product->likes_count ?? 0;

        Log::info("Product + VARIANTS loaded", [
            'id' => $product->product_id,
            'variant_count' => $variants->count()
        ]);

        return response()->json($product);

    } catch (\Exception $e) {
        Log::error("Product show ERROR: " . $e->getMessage());
        return response()->json(['message' => 'Lỗi server', 'error' => $e->getMessage()], 500);
    }
}
    public function search(Request $request)
{
    $q = trim($request->input('q', ''));
    $limit = (int) $request->input('limit', 8);

    if ($q === '') {
        return response()->json([
            'data' => [],
            'total' => 0
        ]);
    }

    $keyword = mb_strtolower($q);

    $products = Product::query()
        ->where('is_visible', 1)
        ->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"])
        ->select('id','name','slug','base_price','discount_price','thumbnail')
        ->orderByRaw(
            "CASE
                WHEN LOWER(name) LIKE ? THEN 1
                WHEN LOWER(name) LIKE ? THEN 2
                ELSE 3
            END",
            ["{$keyword}%", "%{$keyword}%"]
        )
        ->limit($limit)
        ->get();

    return response()->json([
        'data' => $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'price' => $p->discount_price ?? $p->base_price,
            'formatted_price' =>
                number_format($p->discount_price ?? $p->base_price, 0, ',', '.') . '₫',
            'thumbnail' => $p->thumbnail
                ? asset('storage/' . $p->thumbnail)
                : asset('images/placeholder.png'),
        ]),
        'total' => $products->count(),
    ]);
}

public function adminIndex(Request $request)
{
    $query = Product::query()
        ->with(['category', 'variants' => function ($q) {
            $q->select('variant_id', 'product_id', 'name', 'color', 'storage_size', 'price_extra', 'stock', 'image_urls');
        }])
->select('product_id', 'name', 'slug', 'thumbnail', 'category_id', 'base_price', 'discount_price', 'stock_total', 'short_desc', 'description', 'is_visible', 'created_at');

    if ($request->filled('search')) {
        $search = '%' . $request->search . '%';
        $query->where('name', 'like', $search);
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $perPage = $request->input('per_page', 10);
    $products = $query->paginate($perPage);

$products->getCollection()->transform(function ($product) {
        $product->final_price = $product->discount_price ?? $product->base_price;

        // Prioritize product thumbnail, fallback to first variant image
        if ($product->thumbnail) {
            $product->thumbnail = ltrim($product->thumbnail, '/');  // Raw path for frontend storageUrl
        } elseif ($product->variants->isNotEmpty()) {
            $firstVariant = $product->variants->first();
            if ($firstVariant && $firstVariant->image_urls) {
                $urls = is_string($firstVariant->image_urls) ? json_decode($firstVariant->image_urls, true) : $firstVariant->image_urls;
                $product->thumbnail = !empty($urls[0]) ? ltrim($urls[0], '/') : null;
            } else {
                $product->thumbnail = null;
            }
        } else {
            $product->thumbnail = null;
        }

        return $product;
    });

    return response()->json([
        'data' => $products->items(),
        'total' => $products->total(),
        'current_page' => $products->currentPage(),
        'last_page' => $products->lastPage(),
        'per_page' => $products->perPage(),
    ]);
}

public function toggleVisibility($product_id)
{
    try {
        $product = Product::findOrFail($product_id);
        $product->is_visible = !$product->is_visible;
        $product->save();

        return response()->json([
            'success' => true,
            'is_visible' => $product->is_visible,
            'message' => $product->is_visible ? 'Đã hiển thị sản phẩm' : 'Đã ẩn sản phẩm'
        ]);
    } catch (\Exception $e) {
        \Log::error('Lỗi toggle visibility: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Lỗi hệ thống'
        ], 500);
    }
}
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:products,slug',
        'category_id' => 'required|exists:categories,category_id',
        'base_price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'stock_total' => 'required|integer|min:0',
        'short_desc' => 'nullable|string',
        'description' => 'nullable|string',
        'is_visible' => 'boolean',
        'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'variants' => 'nullable|array',
        'variant_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    // Xử lý ảnh đại diện
    $mainImagePath = null;
    if ($request->hasFile('main_image')) {
        $mainImagePath = $request->file('main_image')->store('products', 'public');
    }

    $product = Product::create([
        'name' => $validated['name'],
        'slug' => $validated['slug'],
        'category_id' => $validated['category_id'],
        'base_price' => $validated['base_price'],
        'discount_price' => !empty($validated['discount_price']) && $validated['discount_price'] > 0
            ? $validated['discount_price']
            : null,
        'stock_total' => $validated['stock_total'],
        'short_desc' => $validated['short_desc'] ?? null,
        'description' => $validated['description'] ?? null,
        'is_visible' => $validated['is_visible'] ?? true,
        'thumbnail' => $mainImagePath,
    ]);

    // Xử lý biến thể
    $variants = $request->input('variants', []);
    foreach ($variants as $idx => $v) {
        $variantImage = null;
        if ($request->hasFile("variant_images.$idx")) {
            $variantImage = $request->file("variant_images.$idx")->store('variants', 'public');
        }

        $product->variants()->create([
                'sku' => $v['sku'] ?? null,
                'color' => $v['color'] ?? null,
                'storage_size' => $v['storage_size'] ?? null,
                'name' => $v['name'] ?? null,
                'price' => isset($v['price']) && $v['price'] !== '' ? $v['price'] : null,
                'price_extra' => $v['price_extra'] ?? 0,
                'stock' => $v['stock'] ?? 0,
                'image_urls' => $variantImage ? json_encode([$variantImage]) : null,
            ]);
    }

    return response()->json(['success' => true, 'product' => $product], 201);
}

public function update($id, Request $request)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:products,slug,' . $id . ',product_id',
        'category_id' => 'required|exists:categories,category_id',
        'base_price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'stock_total' => 'required|integer|min:0',
        'short_desc' => 'nullable|string',
        'description' => 'nullable|string',
        'is_visible' => 'boolean',
        'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'variants' => 'nullable|array',
        'variant_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    // Cập nhật ảnh đại diện nếu có
    if ($request->hasFile('main_image')) {
        $validated['thumbnail'] = $request->file('main_image')->store('products', 'public');
    }

    // Đảm bảo discount_price = NULL khi không có giá trị hợp lệ
    $validated['discount_price'] = !empty($validated['discount_price']) && $validated['discount_price'] > 0
        ? $validated['discount_price']
        : null;

    $product->update($validated);

    // Cập nhật biến thể (đồng bộ)
    $variants = $request->input('variants', []);
    $existingVariantIds = [];

    foreach ($variants as $idx => $v) {
        $variantImage = null;
        if ($request->hasFile("variant_images.$idx")) {
            $variantImage = $request->file("variant_images.$idx")->store('variants', 'public');
        }

        $variantData = [
            'sku' => $v['sku'] ?? null,
            'color' => $v['color'] ?? null,
            'storage_size' => $v['storage_size'] ?? null,
            'name' => $v['name'] ?? null,
            'price' => isset($v['price']) && $v['price'] !== '' ? $v['price'] : null,
            'price_extra' => $v['price_extra'] ?? 0,
            'stock' => $v['stock'] ?? 0,
        ];

        if ($variantImage) {
            $variantData['image_urls'] = json_encode([$variantImage]);
        }

        if (!empty($v['variant_id'])) {
            $variant = $product->variants()->find($v['variant_id']);
            if ($variant) {
                $variant->update($variantData);
                $existingVariantIds[] = $variant->variant_id;
                continue;
            }
        }

        $newVariant = $product->variants()->create($variantData);
        $existingVariantIds[] = $newVariant->variant_id;
    }

    // Xóa những biến thể cũ không còn dùng (thử catch để tránh lỗi Foreign Key giỏ hàng)
    $variantsToDelete = $product->variants()->whereNotIn('variant_id', $existingVariantIds)->get();
    foreach ($variantsToDelete as $variantToDelete) {
        try {
            $variantToDelete->delete();
        } catch (\Exception $e) {
            $variantToDelete->update(['stock' => 0]); // Ẩn khỏi hệ thống nếu đang vướng giỏ hàng
        }
    }

    return response()->json(['success' => true, 'product' => $product]);
}

public function destroy($product_id)
{
    $product = Product::findOrFail($product_id);

    try {
        $product->variants()->delete();
        $product->delete();
        return response()->json(['message' => 'Đã xóa sản phẩm thành công']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Không thể xóa sản phẩm vì đang có trong đơn hàng'], 409);
    }
}


}
