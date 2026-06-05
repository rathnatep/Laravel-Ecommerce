@extends('layouts.app')

@section('title', $product->name . ' — PickCloth')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4" style="font-size:.8125rem;">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('catalog.index') }}" class="text-decoration-none" style="color:var(--muted);">Shop</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('catalog.index', ['department' => $product->department]) }}"
               class="text-decoration-none" style="color:var(--muted);">{{ ucfirst($product->department) }}</a>
        </li>
        <li class="breadcrumb-item active" style="color:var(--ink);">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-5">

    {{-- LEFT: Image gallery --}}
    <div class="col-md-6">
        @php $images = $product->images; @endphp

        @if ($images->isNotEmpty())
            <div class="rounded overflow-hidden mb-2" style="aspect-ratio:3/4;background:#f0ede8;">
                <img id="mainImage"
                     src="{{ $images->first()->url() }}"
                     alt="{{ $product->name }}"
                     style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>

            @if ($images->count() > 1)
                <div class="d-flex gap-2 flex-wrap mt-2">
                    @foreach ($images as $img)
                        <button type="button"
                                class="thumb-btn border-0 p-0 rounded overflow-hidden"
                                data-src="{{ $img->url() }}"
                                style="width:60px;height:76px;cursor:pointer;outline:2px solid {{ $loop->first ? 'var(--brand)' : 'transparent' }};border-radius:4px;">
                            <img src="{{ $img->url() }}" alt=""
                                 style="width:100%;height:100%;object-fit:cover;display:block;">
                        </button>
                    @endforeach
                </div>
            @endif
        @else
            <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted"
                 style="aspect-ratio:3/4;">
                No image
            </div>
        @endif
    </div>

    {{-- RIGHT: Product info --}}
    <div class="col-md-6">
        <div class="mb-2" style="font-size:.75rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--brand);">
            {{ $product->department }} · {{ $product->category }}
        </div>

        <h1 class="fw-bold mb-2" style="font-size:1.75rem;letter-spacing:-.02em;">{{ $product->name }}</h1>

        <div class="fw-bold mb-4" style="font-size:1.5rem;color:var(--brand);">
            ${{ number_format($product->base_price, 2) }}
        </div>

        @if ($product->description)
            <p class="mb-4" style="color:var(--muted);line-height:1.7;font-size:.9375rem;">
                {{ $product->description }}
            </p>
        @endif

        <form id="addToCartForm" action="{{ route('cart.add') }}" method="POST">
            @csrf

            @error('product_size_id')
                <div class="pc-flash pc-flash-error mb-3">{{ $message }}</div>
            @enderror

            <div class="mb-4">
                <div class="mb-2" style="font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);">Size</div>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach ($product->sizes as $size)
                        @php $available = $size->stock > 0; @endphp
                        <input type="radio" class="btn-check"
                               name="product_size_id"
                               id="size_{{ $size->id }}"
                               value="{{ $size->id }}"
                               {{ !$available ? 'disabled' : '' }}
                               {{ old('product_size_id') == $size->id ? 'checked' : '' }}
                               autocomplete="off">
                        <label class="btn btn-sm {{ $available ? 'btn-outline-primary' : 'btn-outline-secondary' }}"
                               for="size_{{ $size->id }}"
                               style="{{ !$available ? 'opacity:.4;text-decoration:line-through;' : '' }}"
                               title="{{ $available ? 'Stock: '.$size->stock : 'Out of stock' }}">
                            {{ $size->size }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4" style="max-width:100px;">
                <div class="mb-2" style="font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);">Qty</div>
                <input type="number" id="quantity" name="quantity"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity', 1) }}" min="1" max="99">
                @error('quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary px-5">Add to Cart</button>
        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    var mainImg = document.getElementById('mainImage');
    if (!mainImg) return;
    document.querySelectorAll('.thumb-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mainImg.src = this.dataset.src;
            document.querySelectorAll('.thumb-btn').forEach(function (b) {
                b.style.outline = '2px solid transparent';
            });
            this.style.outline = '2px solid var(--brand)';
        });
    });
})();
</script>
@endpush
