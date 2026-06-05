@extends('admin.layout')
@section('title', 'Orders')

@section('content')

{{-- Status tabs --}}
@php
    $tabs = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'cancelled' => 'Cancelled'];
    $cur  = request('status', '');
@endphp
<div class="d-flex gap-2 flex-wrap align-items-center mb-4">
    @foreach($tabs as $s => $label)
        @php $active = $cur === $s; @endphp
        <a href="{{ route('admin.orders.index', array_filter(['status' => $s])) }}"
           class="text-decoration-none"
           style="font-size:.8rem;padding:.3rem .85rem;border-radius:3px;background:{{ $active ? '#1A1A1A' : '#F0EEE9' }};color:{{ $active ? '#fff' : '#6B6B6B' }};">
            {{ $label }}
        </a>
    @endforeach
    <span style="margin-left:auto;font-size:.8rem;color:#9B9B9B;">{{ $orders->total() }} orders</span>
</div>

<div class="adm-card">
    <table class="table adm-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            @php $hasProof = $order->payment_status === 'proof_uploaded'; @endphp
            <tr {{ $hasProof ? 'style=background:#fdfcf8;' : '' }}>
                <td style="color:#9B9B9B;">{{ $order->id }}</td>
                <td style="font-weight:500;">{{ $order->user->name ?? '—' }}</td>
                <td style="color:#9B9B9B;">{{ $order->phone }}</td>
                <td style="font-weight:600;">${{ number_format($order->total, 2) }}</td>
                <td style="color:#9B9B9B;">{{ ucfirst($order->status) }}</td>
                <td>
                    @if($hasProof)
                        <span style="font-size:.75rem;font-weight:600;color:var(--brand);">Proof uploaded</span>
                    @else
                        <span style="font-size:.75rem;color:#9B9B9B;">{{ $order->paymentStatusLabel() }}</span>
                    @endif
                </td>
                <td style="color:#9B9B9B;">{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}"
                       style="color:var(--brand);text-decoration:none;font-size:.8rem;">
                        {{ $hasProof ? 'Review →' : 'View' }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;color:#9B9B9B;padding:2.5rem;">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $orders->links() }}</div>

@endsection
