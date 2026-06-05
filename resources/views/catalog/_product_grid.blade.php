{{-- Inner content of #product-grid — returned by both the full page and /ajax/catalog --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <span class="text-muted small">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</span>
</div>

@if ($products->isEmpty())
    <div class="py-5 text-center text-muted">
        <p class="mb-1 fw-semibold">No products found.</p>
        <p class="small mb-0">Try adjusting your filters.</p>
    </div>
@else
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        @foreach ($products as $product)
            @include('catalog._product_card', ['product' => $product])
        @endforeach
    </div>

    @if ($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
@endif
