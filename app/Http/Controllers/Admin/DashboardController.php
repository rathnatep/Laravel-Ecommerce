<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $orderStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $revenue = Order::whereIn('status', ['approved', 'completed'])
            ->sum('total');

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $lowStock = ProductSize::with('product')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(20)
            ->get();

        $approvedOrderIds = Order::whereIn('status', ['approved', 'completed'])->pluck('id');

        $bestSellingSizes = OrderItem::select('size', DB::raw('SUM(quantity) as total_sold'))
            ->whereIn('order_id', $approvedOrderIds)
            ->groupBy('size')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $demandByCategory = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('order_items.order_id', $approvedOrderIds)
            ->groupBy('products.category')
            ->orderByDesc('total_sold')
            ->get();

        $demandByDepartment = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.department', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereIn('order_items.order_id', $approvedOrderIds)
            ->groupBy('products.department')
            ->orderByDesc('total_sold')
            ->get();

        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();

        return view('admin.dashboard', compact(
            'orderStats', 'revenue', 'recentOrders',
            'lowStock', 'bestSellingSizes',
            'demandByCategory', 'demandByDepartment',
            'totalProducts', 'activeProducts'
        ));
    }
}
