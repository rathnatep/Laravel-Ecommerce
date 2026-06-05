@extends('admin.layout')
@section('title', 'New Product')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex align-items-center mb-3 gap-2">
            <a href="{{ route('admin.products.index') }}" class="text-muted text-decoration-none">Products</a>
            <span class="text-muted">/</span>
            <span>New Product</span>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department"
                                    class="form-select @error('department') is-invalid @enderror" required>
                                <option value="">Select…</option>
                                @foreach(['men','women','kids'] as $dept)
                                    <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
                                        {{ ucfirst($dept) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" value="{{ old('category') }}"
                                   placeholder="e.g. T-Shirts"
                                   class="form-control @error('category') is-invalid @enderror" required>
                            @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Base Price ($)</label>
                            <input type="number" name="base_price" value="{{ old('base_price') }}"
                                   step="0.01" min="0.01"
                                   class="form-control @error('base_price') is-invalid @enderror" required>
                            @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status"
                                class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Create Product</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
