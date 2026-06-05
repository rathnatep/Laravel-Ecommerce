@extends('admin.layout')
@section('title', 'Edit: ' . $product->name)

@section('content')

<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Products</a>
    <span class="text-muted">/</span>
    <span>{{ $product->name }}</span>
</div>

<div class="row g-4">

    {{-- Left: product details form --}}
    <div class="col-lg-7">

        {{-- Product Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Product Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  required>{{ old('description', $product->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department"
                                    class="form-select @error('department') is-invalid @enderror" required>
                                @foreach(['men','women','kids'] as $dept)
                                    <option value="{{ $dept }}"
                                        {{ old('department', $product->department) === $dept ? 'selected' : '' }}>
                                        {{ ucfirst($dept) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category"
                                   value="{{ old('category', $product->category) }}"
                                   class="form-control @error('category') is-invalid @enderror" required>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Base Price ($)</label>
                            <input type="number" name="base_price" step="0.01" min="0.01"
                                   value="{{ old('base_price', $product->base_price) }}"
                                   class="form-control @error('base_price') is-invalid @enderror" required>
                            @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status"
                                class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active"    {{ old('status', $product->status) === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive"  {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        {{-- Sizes --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Sizes & Stock</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Size</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->sizes as $size)
                        <tr>
                            <form method="POST"
                                  action="{{ route('admin.products.sizes.update', [$product, $size]) }}">
                                @csrf @method('PATCH')
                                <td>
                                    <input type="text" name="size" value="{{ $size->size }}"
                                           class="form-control form-control-sm" style="width:80px;" required>
                                </td>
                                <td>
                                    <input type="number" name="stock" value="{{ $size->stock }}"
                                           min="0" class="form-control form-control-sm" style="width:80px;" required>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </div>
                                </td>
                            </form>
                            <td>
                                <form method="POST"
                                      action="{{ route('admin.products.sizes.destroy', [$product, $size]) }}"
                                      onsubmit="return confirm('Remove this size?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">No sizes yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <form method="POST" action="{{ route('admin.products.sizes.store', $product) }}"
                      class="d-flex gap-2 align-items-end">
                    @csrf
                    <div>
                        <label class="form-label small mb-1">Size</label>
                        <input type="text" name="size" placeholder="e.g. M"
                               class="form-control form-control-sm" style="width:80px;" required>
                    </div>
                    <div>
                        <label class="form-label small mb-1">Stock</label>
                        <input type="number" name="stock" value="0" min="0"
                               class="form-control form-control-sm" style="width:80px;" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Add Size</button>
                </form>
            </div>
        </div>

    </div>

    {{-- Right: images --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Images</div>
            <div class="card-body">
                @forelse($product->images as $image)
                <div class="d-flex align-items-center gap-2 mb-3 p-2 border rounded">
                    <img src="{{ Storage::url($image->path) }}" alt=""
                         class="rounded" style="width:68px;height:68px;object-fit:cover;flex-shrink:0;">
                    <div class="flex-grow-1">
                        @if($image->is_primary)
                            <span class="text-muted" style="font-size:.75rem;">Primary</span>
                        @endif
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @unless($image->is_primary)
                            <form method="POST"
                                  action="{{ route('admin.products.images.primary', [$product, $image]) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2"
                                        style="font-size:.75rem;">Set primary</button>
                            </form>
                            @endunless
                            <form method="POST"
                                  action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
                                  onsubmit="return confirm('Delete this image?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2"
                                        style="font-size:.75rem;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted small">No images yet.</p>
                @endforelse

                <hr class="my-3">
                <div class="fw-semibold small mb-2">Upload New Image</div>
                <form method="POST" action="{{ route('admin.products.images.store', $product) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <input type="file" name="image" accept="image/*"
                               class="form-control form-control-sm @error('image') is-invalid @enderror" required>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Max 5 MB. JPG, PNG, WebP.</div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                </form>
            </div>
        </div>

        <div class="mt-3 d-flex gap-3 align-items-center">
            <a href="{{ route('catalog.show', $product) }}" target="_blank"
               class="btn btn-sm btn-outline-secondary">View in store ↗</a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('Permanently delete this product and all its images?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-secondary"
                        style="color:#999;">Delete product</button>
            </form>
        </div>
    </div>

</div>

@endsection
