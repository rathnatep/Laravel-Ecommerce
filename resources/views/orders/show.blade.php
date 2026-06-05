@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' — PickCloth')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
            <a href="{{ route('orders.index') }}" class="text-muted text-decoration-none">&larr; My Orders</a>
            <h1 class="h3 fw-bold mb-0">Order #{{ $order->id }}</h1>
            <span class="badge {{ $order->statusBadgeClass() }}">{{ ucfirst($order->status) }}</span>
            <a href="{{ route('orders.invoice', $order) }}"
               class="btn btn-sm btn-outline-primary ms-auto">
                Download Invoice (PDF)
            </a>
        </div>

        <div class="row g-4">

            {{-- Left: items + delivery --}}
            <div class="col-lg-7">

                {{-- Items table --}}
                <div class="border rounded mb-3">
                    <div class="p-3 border-bottom">
                        <h2 class="h6 fw-semibold mb-0">Items</h2>
                    </div>
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="fw-medium">{{ $item->product_name }}</td>
                                    <td>{{ $item->size }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">${{ number_format($item->lineTotal(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total</td>
                                <td class="text-end" style="color:var(--bs-primary);">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Delivery info --}}
                <div class="border rounded p-3">
                    <h2 class="h6 fw-semibold mb-2">Delivery Details</h2>
                    <p class="mb-1 text-muted small">Phone</p>
                    <p class="mb-3">{{ $order->phone }}</p>
                    <p class="mb-1 text-muted small">អាសយដ្ឋានដឹកជញ្ជូន</p>
                    <p class="mb-0">{{ $order->address_kh }}</p>
                </div>

            </div>

            {{-- Right: payment --}}
            <div class="col-lg-5">
                <div class="border rounded p-4">
                    <h2 class="h6 fw-semibold mb-3">Payment</h2>

                    <div class="mb-3">
                        <span class="badge {{ $order->paymentBadgeClass() }} mb-2">
                            {{ $order->paymentStatusLabel() }}
                        </span>

                        @if ($order->payment_status === 'proof_uploaded')
                            <p class="text-muted small mb-0">
                                Screenshot received — we are checking it now. Thanks for your patience!
                            </p>
                        @elseif ($order->payment_status === 'confirmed')
                            <p class="text-muted small mb-0">Payment confirmed. We will arrange delivery soon.</p>
                        @endif
                    </div>

                    {{-- QR payment section — show when payment not yet confirmed --}}
                    @if (!in_array($order->payment_status, ['confirmed']) && !in_array($order->status, ['cancelled']))
                        <div class="border rounded p-3 mb-3" style="background:#fdfcf7;">
                            <p class="fw-semibold small mb-1">
                                Pay via {{ config('services.qr_payment.bank_name') }}
                            </p>
                            <p class="text-muted small mb-2">
                                Scan the QR code and transfer exactly
                                <strong style="color:var(--brand);">${{ number_format($order->total, 2) }}</strong>.
                                Then take a screenshot and upload it below.
                            </p>

                            @if ($qrExists)
                                <div class="text-center mb-2">
                                    <img src="{{ $qrAssetUrl }}"
                                         alt="Payment QR Code"
                                         class="border rounded"
                                         style="width:160px;height:160px;object-fit:contain;">
                                </div>
                            @else
                                <div class="border rounded text-center text-muted small py-4 mb-2"
                                     style="min-height:80px;">
                                    QR code coming soon.
                                </div>
                            @endif

                            <p class="text-muted small mb-0">
                                We will confirm your payment and get in touch about delivery.
                            </p>
                        </div>
                    @endif

                    {{-- Show uploaded proof --}}
                    @if ($order->payment_proof)
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Your payment screenshot:</p>
                            <a href="{{ Storage::url($order->payment_proof) }}"
                               target="_blank" rel="noopener noreferrer">
                                <img src="{{ Storage::url($order->payment_proof) }}"
                                     alt="Payment proof"
                                     class="img-fluid rounded border"
                                     style="max-height:180px;object-fit:contain;">
                            </a>
                        </div>
                    @endif

                    {{-- Upload form — show unless confirmed or cancelled --}}
                    @if (!in_array($order->payment_status, ['confirmed']) && !in_array($order->status, ['cancelled']))
                        <hr>
                        <h3 class="h6 fw-semibold mb-2">
                            {{ $order->payment_status === 'proof_uploaded' ? 'Upload a new screenshot' : 'Upload payment screenshot' }}
                        </h3>

                        <form method="POST"
                              action="{{ route('orders.proof', $order) }}"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="payment_proof" class="form-label visually-hidden">Payment Screenshot</label>
                                <input type="file"
                                       id="payment_proof"
                                       name="payment_proof"
                                       accept="image/*"
                                       class="form-control @error('payment_proof') is-invalid @enderror"
                                       required>
                                @error('payment_proof')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">JPG or PNG, max 5 MB. Make sure the amount and reference are visible.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Submit Proof
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
