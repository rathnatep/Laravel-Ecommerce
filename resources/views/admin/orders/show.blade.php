@extends('admin.layout')
@section('title', 'Order #' . $order->id)

@section('content')

<div class="d-flex align-items-center gap-2 mb-4" style="font-size:.8125rem;color:#9B9B9B;">
    <a href="{{ route('admin.orders.index') }}" style="color:#9B9B9B;text-decoration:none;">Orders</a>
    <span>/</span>
    <span style="color:#1A1A1A;">#{{ $order->id }}</span>
    <span class="ms-1 px-2 py-0" style="font-size:.7rem;background:#F0EEE9;border-radius:3px;color:#6B6B6B;">
        {{ ucfirst($order->status) }}
    </span>
</div>

<div class="row g-4">

    {{-- Items + proof --}}
    <div class="col-lg-8">

        <div class="adm-card mb-4">
            <div class="adm-card__head">Items</div>
            <table class="table adm-table mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight:500;">{{ $item->product_name }}</td>
                        <td style="color:#9B9B9B;">{{ $item->size }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td style="color:#9B9B9B;">{{ $item->quantity }}</td>
                        <td style="text-align:right;font-weight:600;">${{ number_format($item->lineTotal(), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid #E8E6E1;">
                        <td colspan="4" style="text-align:right;font-weight:600;padding:.75rem;">Total</td>
                        <td style="text-align:right;font-weight:700;color:var(--brand);padding:.75rem;">
                            ${{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($order->payment_proof)
        @php $proofUrl = Storage::url($order->payment_proof); @endphp
        <div class="adm-card">
            <div class="adm-card__head">
                Payment Proof
                <a href="{{ $proofUrl }}" target="_blank"
                   style="font-size:.75rem;color:var(--brand);text-decoration:none;font-weight:400;text-transform:none;letter-spacing:0;">
                    Open full ↗
                </a>
            </div>
            <div style="padding:1rem;text-align:center;">
                <a href="{{ $proofUrl }}" target="_blank">
                    <img src="{{ $proofUrl }}" alt="Payment proof"
                         style="max-height:360px;max-width:100%;object-fit:contain;border-radius:6px;">
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- Info + actions --}}
    <div class="col-lg-4">

        <div class="adm-card mb-3">
            <div class="adm-card__head">Order</div>
            <div style="padding:1rem;font-size:.8125rem;">
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #F0EEE9;">
                    <span style="color:#9B9B9B;">ID</span>
                    <span>#{{ $order->id }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #F0EEE9;">
                    <span style="color:#9B9B9B;">Date</span>
                    <span>{{ $order->created_at->format('d M Y H:i') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-bottom:1px solid #F0EEE9;">
                    <span style="color:#9B9B9B;">Status</span>
                    <span style="font-weight:600;">{{ ucfirst($order->status) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;">
                    <span style="color:#9B9B9B;">Payment</span>
                    <span style="font-weight:600;{{ $order->payment_status === 'confirmed' ? 'color:var(--brand);' : '' }}">
                        {{ $order->paymentStatusLabel() }}
                    </span>
                </div>
                @if($order->approved_at)
                <div style="display:flex;justify-content:space-between;padding:.3rem 0;border-top:1px solid #F0EEE9;">
                    <span style="color:#9B9B9B;">Approved</span>
                    <span>{{ $order->approved_at->format('d M Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="adm-card mb-3">
            <div class="adm-card__head">Customer</div>
            <div style="padding:1rem;font-size:.8125rem;">
                <div style="font-weight:600;margin-bottom:.25rem;">{{ $order->user->name ?? '—' }}</div>
                <div style="color:#9B9B9B;">{{ $order->phone }}</div>
                @if($order->address_kh)
                    <div style="color:#9B9B9B;margin-top:.5rem;">{{ $order->address_kh }}</div>
                @endif
            </div>
        </div>

        <div class="adm-card">
            <div class="adm-card__head">Actions</div>
            <div style="padding:1rem;display:flex;flex-direction:column;gap:.6rem;">

                <a href="{{ route('orders.invoice', $order) }}"
                   class="btn btn-sm btn-outline-secondary" target="_blank">
                    Download Invoice PDF
                </a>

                @if($order->payment_status === 'proof_uploaded')
                <form method="POST" action="{{ route('admin.orders.confirm-payment', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                        Confirm Payment
                    </button>
                </form>
                @endif

                @if($order->status === 'pending')
                    @if($order->payment_status === 'confirmed')
                    <form method="POST" action="{{ route('admin.orders.approve', $order) }}"
                          onsubmit="return confirm('Approve? Stock will be reduced — cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            Approve Order
                        </button>
                    </form>
                    @else
                    <p style="font-size:.8rem;color:#9B9B9B;text-align:center;margin:0;">
                        Confirm payment before approving.
                    </p>
                    @endif

                    <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                          onsubmit="return confirm('Cancel this order?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                            Cancel Order
                        </button>
                    </form>
                @endif

                @if($order->status === 'approved')
                <p style="font-size:.8rem;color:#9B9B9B;text-align:center;margin:0;">
                    Approved {{ $order->approved_at?->format('d M Y') }} — stock adjusted.
                </p>
                @elseif($order->status === 'cancelled')
                <p style="font-size:.8rem;color:#9B9B9B;text-align:center;margin:0;">
                    This order was cancelled.
                </p>
                @endif

            </div>
        </div>

    </div>
</div>

@endsection
