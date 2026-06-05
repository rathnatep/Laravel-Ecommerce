<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CatalogService
{
    public function filter(array $params): LengthAwarePaginator
    {
        $query = Product::active()
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'sizes',
            ]);

        if (!empty($params['department'])) {
            $query->where('department', $params['department']);
        }

        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        if (!empty($params['size'])) {
            $query->whereHas('sizes', fn ($q) => $q
                ->where('size', $params['size'])
                ->where('stock', '>', 0)
            );
        }

        if (isset($params['price_min']) && $params['price_min'] !== '') {
            $query->where('base_price', '>=', (float) $params['price_min']);
        }

        if (isset($params['price_max']) && $params['price_max'] !== '') {
            $query->where('base_price', '<=', (float) $params['price_max']);
        }

        if (!empty($params['in_stock'])) {
            $query->whereHas('sizes', fn ($q) => $q->where('stock', '>', 0));
        }

        match ($params['sort'] ?? 'new') {
            'popular'    => $query->orderByDesc('sold_count'),
            'price_asc'  => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            default      => $query->latest(),
        };

        return $query->paginate(12)->withQueryString();
    }

    public function categories(?string $department = null): array
    {
        return Product::active()
            ->when($department, fn ($q) => $q->where('department', $department))
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }

    public function availableSizes(?string $department = null): array
    {
        return ProductSize::query()
            ->where('stock', '>', 0)
            ->whereHas('product', fn ($q) => $q->active()
                ->when($department, fn ($q2) => $q2->where('department', $department))
            )
            ->select('size')
            ->distinct()
            ->orderBy('size')
            ->pluck('size')
            ->toArray();
    }

    public function newArrivals(int $limit = 8): Collection
    {
        return Product::active()
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'sizes',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function popular(int $limit = 8): Collection
    {
        return Product::active()
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'sizes',
            ])
            ->orderByDesc('sold_count')
            ->limit($limit)
            ->get();
    }
}
