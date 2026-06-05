<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CatalogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function index(Request $request)
    {
        $params = $request->only([
            'department', 'category', 'size', 'price_min', 'price_max', 'in_stock', 'sort',
        ]);

        $products   = $this->catalog->filter($params);
        $categories = $this->catalog->categories($params['department'] ?? null);
        $sizes      = $this->catalog->availableSizes($params['department'] ?? null);

        return view('catalog.index', compact('products', 'params', 'categories', 'sizes'));
    }

    public function ajaxGrid(Request $request)
    {
        $params = $request->only([
            'department', 'category', 'size', 'price_min', 'price_max', 'in_stock', 'sort',
        ]);

        $products = $this->catalog->filter($params);

        return view('catalog._product_grid', compact('products', 'params'));
    }

    public function show(Product $product)
    {
        abort_if($product->status !== 'active', 404);

        $product->load([
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'sizes'  => fn ($q) => $q->orderBy('id'),
        ]);

        return view('catalog.show', compact('product'));
    }
}
