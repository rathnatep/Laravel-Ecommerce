@extends('layouts.app')

@section('title', 'Checkout — PickCloth')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">

        <h1 class="h3 fw-bold mb-4">Almost there</h1>

        <div class="row g-4 align-items-start">

            {{-- Delivery form --}}
            <div class="col-lg-7">
                <div class="border rounded p-4">
                    <h2 class="h6 fw-bold mb-3 text-uppercase" style="letter-spacing:.05em;">Delivery Details</h2>

                    <form method="POST" action="{{ route('checkout.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone number</label>
                            <input type="tel" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="address_kh" class="form-label">
                                អាសយដ្ឋានដឹកជញ្ជូន
                                <span class="text-muted small">(Delivery address in Khmer)</span>
                            </label>
                            <textarea id="address_kh" name="address_kh" rows="3"
                                      class="form-control @error('address_kh') is-invalid @enderror"
                                      placeholder="សូមបញ្ចូលអាសយដ្ឋានលម្អិត..."
                                      required>{{ old('address_kh', $user->address_kh) }}</textarea>
                            @error('address_kh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Write your full address so we can find you easily.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Confirm & Place Order
                        </button>
                    </form>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="col-lg-5">
                <div class="border rounded p-4">
                    <h2 class="h6 fw-bold mb-3 text-uppercase" style="letter-spacing:.05em;">Your Order</h2>

                    @foreach ($items as $item)
                        <div class="d-flex justify-content-between align-items-start mb-2" style="font-size:.9rem;">
                            <div class="me-2">
                                <span class="fw-medium">{{ $item['product']->name }}</span>
                                <span class="text-muted d-block" style="font-size:.8rem;">
                                    Size {{ $item['size'] }} · Qty {{ $item['quantity'] }}
                                </span>
                            </div>
                            <span class="text-nowrap">${{ number_format($item['line_total'], 2) }}</span>
                        </div>
                    @endforeach

                    <hr class="my-3">
                    <div class="d-flex justify-content-between fw-bold mb-4">
                        <span>Total</span>
                        <span style="color:var(--brand);font-size:1.125rem;">${{ number_format($total, 2) }}</span>
                    </div>

                    <div class="rounded p-3" style="background:#fdfcf7;border:1px solid #E8E6E1;font-size:.8125rem;">
                        <p class="fw-semibold mb-1">How payment works</p>
                        <p class="text-muted mb-0" style="line-height:1.6;">
                            After placing your order, scan the ABA or ACLEDA QR code and upload your payment screenshot.
                            We will confirm and arrange delivery once payment is verified.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
