@extends('layouts.app')

@section('title', 'PickCloth')

@section('content')

{{-- Hero --}}
<div class="pc-hero">
    <h1>Fresh fits for everyday life.</h1>
    <p>Clothing for men, women & kids   good quality, fair price, delivered to your door.</p>
    <a href="{{ route('catalog.index') }}" class="btn btn-primary px-5">Browse Collection</a>
</div>

{{-- Tinh tix tv --}}
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="pc-section-title">Shop by Department</span>
    </div>
    <div class="row g-3 row-cols-3">
        @foreach (['men' => "Men's", 'women' => "Women's", 'kids' => "Kids'"] as $dept => $label)
        <div class="col">
            <a href="{{ route('catalog.index', ['department' => $dept]) }}" class="pc-dept-tile">
                <div class="pc-dept-tile__label">{{ $label }}</div>
            </a>
        </div>
        @endforeach
    </div>
</section>

{{-- New Arrivals --}}
@if ($newArrivals->isNotEmpty())
<section class="mb-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="pc-section-title">Just In</span>
        <a href="{{ route('catalog.index', ['sort' => 'new']) }}"
           class="small text-decoration-none" style="color:var(--brand);">See all →</a>
    </div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        @foreach ($newArrivals as $product)
            @include('catalog._product_card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- Popular --}}
@if ($popular->isNotEmpty())
<section class="mb-2">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="pc-section-title">Customer Favourites</span>
        <a href="{{ route('catalog.index', ['sort' => 'popular']) }}"
           class="small text-decoration-none" style="color:var(--brand);">See all →</a>
    </div>
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
        @foreach ($popular as $product)
            @include('catalog._product_card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@endsection
