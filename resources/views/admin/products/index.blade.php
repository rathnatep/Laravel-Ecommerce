@extends('admin.layout')
@section('title', 'Products')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <span style="font-size:.8125rem;color:#9B9B9B;">{{ $products->total() }} products</span>
    <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">Add Product</a>
</div>

<div class="adm-card">
    <table class="table adm-table mb-0">
        <thead>
            <tr>
                <th style="width:52px;"></th>
                <th>Name</th>
                <th>Dept / Category</th>
                <th>Price</th>
                <th>Sizes</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            @php $img = $product->images->first(); @endphp
            <tr>
                <td>
                    @if($img)
                        <img src="{{ Storage::url($img->path) }}" alt=""
                             style="width:44px;height:44px;object-fit:cover;border-radius:4px;display:block;">
                    @else
                        <div style="width:44px;height:44px;background:#F0EEE9;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:.6rem;color:#9B9B9B;">
                            None
                        </div>
                    @endif
                </td>
                <td>
                    <div style="font-weight:600;">{{ $product->name }}</div>
                    <div style="font-size:.75rem;color:#9B9B9B;">{{ $product->sold_count }} sold</div>
                </td>
                <td style="color:#9B9B9B;">{{ ucfirst($product->department) }} / {{ $product->category }}</td>
                <td style="font-weight:500;">${{ number_format($product->base_price, 2) }}</td>
                <td style="color:#9B9B9B;">{{ $product->sizes_count }}</td>
                <td>
                    <span style="font-size:.75rem;font-weight:600;color:{{ $product->status === 'active' ? 'var(--brand)' : '#9B9B9B' }};">
                        {{ $product->status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           style="font-size:.8rem;color:var(--brand);text-decoration:none;">Edit</a>

                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}" class="mb-0">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm p-0"
                                    style="font-size:.8rem;color:#9B9B9B;background:none;border:none;">
                                {{ $product->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:#9B9B9B;padding:2.5rem;">
                    No products yet.
                    <a href="{{ route('admin.products.create') }}" style="color:var(--brand);">Add one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $products->links() }}</div>

@endsection
