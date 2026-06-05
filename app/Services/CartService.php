<?php

namespace App\Services;

use App\Models\ProductSize;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function add(int $productSizeId, int $quantity): void
    {
        $size = ProductSize::with('product')->findOrFail($productSizeId);

        abort_if(
            !$size->product || $size->product->status !== 'active',
            422,
            'Product not available.'
        );
        abort_if($size->stock <= 0, 422, 'This size is out of stock.');

        $cart    = $this->raw();
        $current = $cart[$productSizeId] ?? 0;
        $newQty  = $current + $quantity;

        abort_if(
            $newQty > $size->stock,
            422,
            "Only {$size->stock} unit(s) available for this size."
        );

        $cart[$productSizeId] = $newQty;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $productSizeId, int $quantity): void
    {
        $size = ProductSize::with('product')->findOrFail($productSizeId);

        abort_if(
            !$size->product || $size->product->status !== 'active',
            422,
            'Product is no longer available.'
        );
        abort_if(
            $quantity > $size->stock,
            422,
            "Only {$size->stock} unit(s) available for this size."
        );

        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productSizeId]);
        } else {
            $cart[$productSizeId] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productSizeId): void
    {
        $cart = $this->raw();
        unset($cart[$productSizeId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /** @return array<int, array{product_size_id:int, product:\App\Models\Product, size:string, stock:int, quantity:int, unit_price:string, line_total:string}> */
    public function items(): array
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return [];
        }

        $sizes = ProductSize::with(['product' => fn ($q) => $q->with(['images' => fn ($q2) => $q2->orderBy('sort_order')])])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        foreach ($cart as $sizeId => $qty) {
            $size = $sizes->get($sizeId);
            if (!$size || !$size->product) {
                continue;
            }

            $unitPrice = (string) $size->product->base_price;

            $items[] = [
                'product_size_id' => $sizeId,
                'product'         => $size->product,
                'size'            => $size->size,
                'stock'           => $size->stock,
                'quantity'        => $qty,
                'unit_price'      => $unitPrice,
                'line_total'      => bcmul($unitPrice, (string) $qty, 2),
            ];
        }

        return $items;
    }

    public function total(): string
    {
        $total = '0.00';
        foreach ($this->items() as $item) {
            $total = bcadd($total, $item['line_total'], 2);
        }
        return $total;
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    private function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
