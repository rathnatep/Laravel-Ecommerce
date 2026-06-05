@extends('layouts.app')

@section('title', 'Your Cart — PickCloth')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 fw-bold mb-0">Your Cart</h1>
    @if(count($items) > 0)
        <form method="POST" action="{{ route('cart.clear') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-secondary"
                    onclick="return confirm('Remove everything from your cart?')">
                Clear all
            </button>
        </form>
    @endif
</div>

@if(count($items) === 0)
    <div class="text-center py-5">
        <p class="text-muted mb-1" style="font-size:1.0625rem;">Your cart is empty.</p>
        <p class="text-muted small mb-4">Find something you like and add it here.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary px-4">Browse Collection</a>
    </div>
@else
    <div class="row g-4 align-items-start">

        {{-- Cart items --}}
        <div class="col-lg-8">
            <div class="border rounded overflow-hidden">
                @foreach ($items as $item)
                    @php $product = $item['product']; @endphp
                    <div class="d-flex gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">

                        <a href="{{ route('catalog.show', $product) }}" class="flex-shrink-0">
                            @php $img = $product->primaryImage(); @endphp
                            @if ($img)
                                <img src="{{ $img->url() }}" alt="{{ $product->name }}"
                                     class="rounded"
                                     style="width:80px;height:100px;object-fit:cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted"
                                     style="width:80px;height:100px;font-size:.7rem;">No image</div>
                            @endif
                        </a>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('catalog.show', $product) }}"
                                       class="fw-semibold text-decoration-none text-body">
                                        {{ $product->name }}
                                    </a>
                                    <p class="text-muted small mb-0">
                                        Size {{ $item['size'] }} &middot; ${{ number_format($item['unit_price'], 2) }} each
                                    </p>
                                    @if ($item['quantity'] > $item['stock'])
                                        <p class="small mb-0" style="color:#dc3545;">
                                            Only {{ $item['stock'] }} left — please reduce the quantity.
                                        </p>
                                    @endif
                                </div>
                                <p class="fw-bold mb-0 ms-3" style="color:var(--brand);white-space:nowrap;">
                                    ${{ number_format($item['line_total'], 2) }}
                                </p>
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-2">
                                <form method="POST"
                                      action="{{ route('cart.update', $item['product_size_id']) }}"
                                      class="d-flex align-items-center gap-1">
                                    @csrf @method('PATCH')
                                    <label for="qty_{{ $item['product_size_id'] }}" class="visually-hidden">Qty</label>
                                    <input type="number"
                                           id="qty_{{ $item['product_size_id'] }}"
                                           name="quantity"
                                           value="{{ $item['quantity'] }}"
                                           min="1" max="{{ $item['stock'] }}"
                                           class="form-control form-control-sm"
                                           style="width:68px;">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                </form>

                                <form method="POST" action="{{ route('cart.remove', $item['product_size_id']) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-link p-0"
                                            style="color:#9B9B9B;font-size:.8125rem;">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="col-lg-4">
            <div class="border rounded p-4">
                <h2 class="h6 fw-bold mb-3 text-uppercase" style="letter-spacing:.05em;">Order Summary</h2>

                <div class="d-flex justify-content-between mb-2" style="font-size:.9rem;">
                    <span class="text-muted">Subtotal</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3" style="font-size:.9rem;">
                    <span class="text-muted">Delivery</span>
                    <span class="text-muted">Arranged after order</span>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between fw-bold mb-4">
                    <span>Total</span>
                    <span style="color:var(--brand);font-size:1.125rem;">${{ number_format($total, 2) }}</span>
                </div>

                @auth
                    <a href="{{ route('checkout') }}" class="btn btn-primary w-100">
                        Proceed to Checkout
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        Sign in to Checkout
                    </a>
                @endauth

                <a href="{{ route('catalog.index') }}"
                   class="btn btn-outline-secondary w-100 mt-2">
                    Keep Shopping
                </a>
            </div>
        </div>

    </div>
@endif

@endsection
