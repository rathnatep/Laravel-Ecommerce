@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

{{-- Stat row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="adm-stat">
            <div class="adm-stat__label">Pending</div>
            <div class="adm-stat__value">{{ $orderStats['pending'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="adm-stat">
            <div class="adm-stat__label">Approved</div>
            <div class="adm-stat__value">{{ $orderStats['approved'] ?? 0 }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="adm-stat">
            <div class="adm-stat__label">Revenue</div>
            <div class="adm-stat__value brand">${{ number_format($revenue, 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="adm-stat">
            <div class="adm-stat__label">Products</div>
            <div class="adm-stat__value">
                {{ $activeProducts }}<span style="font-size:1rem;font-weight:400;color:#9B9B9B;"> / {{ $totalProducts }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- Recent Orders --}}
    <div class="col-lg-8">
        <div class="adm-card">
            <div class="adm-card__head">
                Recent Orders
                <a href="{{ route('admin.orders.index') }}"
                   style="font-size:.75rem;color:var(--brand);text-decoration:none;font-weight:400;text-transform:none;letter-spacing:0;">
                    View all →
                </a>
            </div>
            <table class="table adm-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td style="color:#9B9B9B;">{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? '—' }}</td>
                        <td style="font-weight:600;">${{ number_format($order->total, 2) }}</td>
                        <td style="color:#9B9B9B;">{{ ucfirst($order->status) }}</td>
                        <td style="color:#9B9B9B;">{{ $order->paymentStatusLabel() }}</td>
                        <td style="color:#9B9B9B;">{{ $order->created_at->format('d M H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}"
                               style="color:var(--brand);text-decoration:none;">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;color:#9B9B9B;padding:2rem;">No orders yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Low Stock --}}
    <div class="col-lg-4">
        <div class="adm-card">
            <div class="adm-card__head">
                Low Stock
                <span style="font-size:.7rem;color:#9B9B9B;font-weight:400;text-transform:none;letter-spacing:0;">≤ 5 units</span>
            </div>
            @forelse($lowStock as $row)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.625rem 1.25rem;border-bottom:1px solid #F0EEE9;">
                <div>
                    <div style="font-size:.8125rem;font-weight:500;">{{ Str::limit($row->product->name ?? '—', 22) }}</div>
                    <div style="font-size:.7rem;color:#9B9B9B;">Size {{ $row->size }}</div>
                </div>
                <span style="font-size:.8125rem;font-weight:600;{{ $row->stock === 0 ? 'color:#dc3545;' : 'color:#9B9B9B;' }}">
                    {{ $row->stock }}
                </span>
            </div>
            @empty
            <div style="text-align:center;color:#9B9B9B;padding:2rem;font-size:.8125rem;">All sizes well stocked.</div>
            @endforelse
        </div>
    </div>

    {{-- Best-selling sizes --}}
    <div class="col-lg-4">
        <div class="adm-card">
            <div class="adm-card__head">Best-Selling Sizes</div>
            @forelse($bestSellingSizes as $row)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 1.25rem;border-bottom:1px solid #F0EEE9;">
                <span style="font-size:.8125rem;font-weight:500;">{{ $row->size }}</span>
                <span style="font-size:.75rem;color:#9B9B9B;">{{ $row->total_sold }} sold</span>
            </div>
            @empty
            <div style="text-align:center;color:#9B9B9B;padding:1.5rem;font-size:.8125rem;">No data yet.</div>
            @endforelse
        </div>
    </div>

    {{-- By Department --}}
    <div class="col-lg-4">
        <div class="adm-card">
            <div class="adm-card__head">By Department</div>
            @forelse($demandByDepartment as $row)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 1.25rem;border-bottom:1px solid #F0EEE9;">
                <span style="font-size:.8125rem;font-weight:500;">{{ ucfirst($row->department) }}</span>
                <span style="font-size:.75rem;color:#9B9B9B;">{{ $row->total_sold }} units</span>
            </div>
            @empty
            <div style="text-align:center;color:#9B9B9B;padding:1.5rem;font-size:.8125rem;">No data yet.</div>
            @endforelse
        </div>
    </div>

    {{-- By Category --}}
    <div class="col-lg-4">
        <div class="adm-card">
            <div class="adm-card__head">By Category</div>
            @forelse($demandByCategory as $row)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 1.25rem;border-bottom:1px solid #F0EEE9;">
                <span style="font-size:.8125rem;font-weight:500;">{{ ucfirst($row->category) }}</span>
                <span style="font-size:.75rem;color:#9B9B9B;">{{ $row->total_sold }} units</span>
            </div>
            @empty
            <div style="text-align:center;color:#9B9B9B;padding:1.5rem;font-size:.8125rem;">No data yet.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
