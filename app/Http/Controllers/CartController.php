<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $items = $this->cartService->items();
        $total = $this->cartService->total();

        return view('cart.index', compact('items', 'total'));
    }

    public function add(AddToCartRequest $request)
    {
        $this->cartService->add(
            (int) $request->validated('product_size_id'),
            (int) $request->validated('quantity'),
        );

        return back()->with('status', 'Item added to cart.');
    }

    public function update(Request $request, int $productSizeId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cartService->update($productSizeId, (int) $request->quantity);

        return back()->with('status', 'Cart updated.');
    }

    public function remove(int $productSizeId)
    {
        $this->cartService->remove($productSizeId);

        return back()->with('status', 'Item removed from cart.');
    }

    public function clear()
    {
        $this->cartService->clear();

        return back()->with('status', 'Cart cleared.');
    }
}
