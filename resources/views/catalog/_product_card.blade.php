<div class="col">
    <a href="{{ route('catalog.show', $product) }}" class="pc-card">
        @if ($product->images->isNotEmpty())
            <img class="pc-card__img"
                 src="{{ ($product->primaryImage() ?? $product->images->first())->url() }}"
                 alt="{{ $product->name }}"
                 loading="lazy">
        @else
            <div class="pc-card__no-img">No image</div>
        @endif

        <div class="pc-card__body">
            <div class="pc-card__dept">{{ $product->department }}</div>
            <div class="pc-card__name">{{ $product->name }}</div>
            <div class="pc-card__price">${{ number_format($product->base_price, 2) }}</div>
            @php $availableSizes = $product->sizes->filter(fn($s) => $s->stock > 0)->pluck('size'); @endphp
            @if ($availableSizes->isNotEmpty())
                <div class="pc-card__sizes">{{ $availableSizes->implode(' · ') }}</div>
            @else
                <div class="pc-card__sizes">Out of stock</div>
            @endif
        </div>
    </a>
</div>
