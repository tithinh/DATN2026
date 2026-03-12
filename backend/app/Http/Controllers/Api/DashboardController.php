<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Comment;
use App\Models\News;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Lấy thống kê tổng quan cho dashboard
     */
    public function stats()
    {
        // Thống kê người dùng
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Thống kê sản phẩm
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock_total');
        $lowStockProducts = Product::where('stock_total', '<', 10)->count();

        // Thống kê đơn hàng
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $totalRevenue = Order::where('status', 'completed')
            ->selectRaw('COALESCE(SUM(final_amount), 0) as total')
            ->value('total');

        $revenueThisMonth = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('COALESCE(SUM(final_amount), 0) as total')
            ->value('total');

        // Thống kê bình luận
        $totalComments = Comment::count();
        $pendingComments = Comment::where('status', 'pending')->count();

        // Thống kê tin tức
        $totalNews = News::count();
        $publishedNews = News::where('status', 'published')->count();

        // Thống kê liên hệ
        $totalContacts = Contact::count();
        $pendingContacts = Contact::where('status', 'pending')->count();

        // Đơn hàng gần đây (5 đơn mới nhất)
        $recentOrders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->select(
                'products.product_id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Doanh thu theo ngày (7 ngày gần nhất)
        $revenueByDay = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'users' => [
                'total' => $totalUsers,
                'new_this_month' => $newUsersThisMonth,
            ],
            'products' => [
                'total' => $totalProducts,
                'total_stock' => $totalStock,
                'low_stock' => $lowStockProducts,
            ],
            'orders' => [
                'total' => $totalOrders,
                'pending' => $pendingOrders,
                'recent' => $recentOrders,
            ],
            'revenue' => [
                'total' => (float) $totalRevenue,
                'this_month' => (float) $revenueThisMonth,
            ],
            'comments' => [
                'total' => $totalComments,
                'pending' => $pendingComments,
            ],
            'news' => [
                'total' => $totalNews,
                'published' => $publishedNews,
            ],
            'contacts' => [
                'total' => $totalContacts,
                'pending' => $pendingContacts,
            ],
            'top_products' => $topProducts,
            'revenue_by_day' => $revenueByDay,
        ]);
    }
}
