@extends('layouts.app')

@section('title', 'My Orders — PickCloth')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 fw-bold mb-0">My Orders</h1>
    <a href="{{ route('catalog.index') }}" class="btn btn-outline-secondary btn-sm">Keep Shopping</a>
</div>

@if ($orders->isEmpty())
    <div class="text-center py-5">
        <p class="text-muted mb-1" style="font-size:1.0625rem;">No orders yet.</p>
        <p class="text-muted small mb-4">When you place an order, it will show up here.</p>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary px-4">Browse Collection</a>
    </div>
@else
    <div class="border rounded overflow-hidden">
        <table class="table table-hover mb-0" style="font-size:.9rem;">
            <thead class="table-light">
                <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="fw-medium">#{{ $order->id }}</td>
                        <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        <td style="font-weight:600;">${{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $order->statusBadgeClass() }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $order->paymentBadgeClass() }}">
                                {{ $order->paymentStatusLabel() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $orders->links() }}</div>
@endif

@endsection
