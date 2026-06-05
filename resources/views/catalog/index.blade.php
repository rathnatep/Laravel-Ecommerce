@extends('layouts.app')

@section('title', (!empty($params['department']) ? ucfirst($params['department']).' — ' : '') . 'Shop — PickCloth')

@section('content')

{{-- Top bar --}}
<div class="d-flex align-items-center justify-content-between mb-4 gap-3 flex-wrap">
    <div>
        <h1 class="mb-0 fw-bold" style="font-size:1.125rem;letter-spacing:-.01em;">
            {{ !empty($params['department']) ? ucfirst($params['department']) : 'All Products' }}
        </h1>
    </div>

    <div class="d-flex align-items-center gap-3 flex-wrap">
        {{-- Department tabs --}}
        <div class="d-flex gap-1">
            <a href="{{ route('catalog.index', array_filter(array_merge($params, ['department' => null]))) }}"
               class="btn btn-sm {{ empty($params['department']) ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            @foreach (['men', 'women', 'kids'] as $dept)
            <a href="{{ route('catalog.index', array_filter(array_merge($params, ['department' => $dept, 'page' => null]))) }}"
               class="btn btn-sm {{ ($params['department'] ?? '') === $dept ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ ucfirst($dept) }}
            </a>
            @endforeach
        </div>

        {{-- Sort --}}
        <select id="sortSelect" name="sort" class="form-select form-select-sm" style="width:auto;font-size:.8125rem;">
            @php $sorts = ['new' => 'New Arrivals','popular' => 'Popular','price_asc' => 'Price ↑','price_desc' => 'Price ↓']; @endphp
            @foreach ($sorts as $val => $label)
                <option value="{{ $val }}" {{ ($params['sort'] ?? 'new') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Active filter pills --}}
@php
    $activeFilters = [];
    if (!empty($params['category']))   $activeFilters[] = ['label' => $params['category'],           'remove' => 'category'];
    if (!empty($params['size']))       $activeFilters[] = ['label' => 'Size: '.$params['size'],       'remove' => 'size'];
    if (isset($params['price_min']) && $params['price_min'] !== '') $activeFilters[] = ['label' => 'Min $'.$params['price_min'], 'remove' => 'price_min'];
    if (isset($params['price_max']) && $params['price_max'] !== '') $activeFilters[] = ['label' => 'Max $'.$params['price_max'], 'remove' => 'price_max'];
    if (!empty($params['in_stock']))   $activeFilters[] = ['label' => 'In stock',                     'remove' => 'in_stock'];
@endphp
@if (count($activeFilters))
<div class="d-flex flex-wrap gap-2 mb-4 align-items-center">
    @foreach ($activeFilters as $f)
        @php $cleared = array_filter(array_merge($params, [$f['remove'] => null]), fn($v) => $v !== null && $v !== ''); @endphp
        <a href="{{ route('catalog.index', $cleared) }}"
           class="badge text-decoration-none d-inline-flex align-items-center gap-1"
           style="background:var(--brand);color:#fff;font-weight:500;font-size:.75rem;padding:.3em .65em;">
            {{ $f['label'] }} ×
        </a>
    @endforeach
    <a href="{{ route('catalog.index') }}" class="text-decoration-none" style="font-size:.8125rem;color:var(--muted);">Clear all</a>
</div>
@endif

<div class="row g-4">

    {{-- Sidebar --}}
    <div class="col-lg-2">
        <button class="btn btn-sm btn-outline-secondary w-100 mb-3 d-lg-none"
                type="button" data-bs-toggle="collapse" data-bs-target="#filterSidebar">
            Filters
        </button>

        <div class="collapse d-lg-block pc-filters" id="filterSidebar">
            <form id="filterForm" method="GET" action="{{ route('catalog.index') }}">
                @if (!empty($params['sort']) && $params['sort'] !== 'new')
                    <input type="hidden" name="sort" value="{{ $params['sort'] }}">
                @endif
                @if (!empty($params['department']))
                    <input type="hidden" name="department" value="{{ $params['department'] }}">
                @endif

                @if (count($categories) > 0)
                <div class="pc-filters__section">
                    <div class="pc-filters__label">Category</div>
                    @foreach ($categories as $cat)
                    <div class="form-check mb-1">
                        <input class="form-check-input filter-input" type="radio"
                               name="category" id="cat_{{ Str::slug($cat) }}"
                               value="{{ $cat }}"
                               {{ ($params['category'] ?? '') === $cat ? 'checked' : '' }}>
                        <label class="form-check-label" for="cat_{{ Str::slug($cat) }}">{{ $cat }}</label>
                    </div>
                    @endforeach
                    @if (!empty($params['category']))
                    <div class="form-check mb-1">
                        <input class="form-check-input filter-input" type="radio"
                               name="category" id="cat_any" value="">
                        <label class="form-check-label" style="color:var(--muted);" for="cat_any">Any</label>
                    </div>
                    @endif
                </div>
                @endif

                @if (count($sizes) > 0)
                <div class="pc-filters__section">
                    <div class="pc-filters__label">Size</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($sizes as $sz)
                        <input class="btn-check filter-input" type="radio"
                               name="size" id="sz_{{ $sz }}" value="{{ $sz }}"
                               autocomplete="off"
                               {{ ($params['size'] ?? '') === $sz ? 'checked' : '' }}>
                        <label class="btn btn-sm btn-outline-primary" for="sz_{{ $sz }}"
                               style="font-size:.75rem;padding:.2rem .55rem;">{{ $sz }}</label>
                        @endforeach
                        @if (!empty($params['size']))
                        <input class="btn-check filter-input" type="radio"
                               name="size" id="sz_any" value="" autocomplete="off">
                        <label class="btn btn-sm btn-outline-secondary" for="sz_any"
                               style="font-size:.75rem;padding:.2rem .55rem;">Any</label>
                        @endif
                    </div>
                </div>
                @endif

                <div class="pc-filters__section">
                    <div class="pc-filters__label">Price ($)</div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="number" class="form-control form-control-sm filter-input"
                               name="price_min" placeholder="Min" min="0" step="1"
                               value="{{ $params['price_min'] ?? '' }}" style="width:64px;">
                        <span style="color:var(--muted);">–</span>
                        <input type="number" class="form-control form-control-sm filter-input"
                               name="price_max" placeholder="Max" min="0" step="1"
                               value="{{ $params['price_max'] ?? '' }}" style="width:64px;">
                    </div>
                </div>

                <div class="pc-filters__section">
                    <div class="form-check">
                        <input class="form-check-input filter-input" type="checkbox"
                               name="in_stock" id="inStock" value="1"
                               {{ !empty($params['in_stock']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="inStock">In stock only</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">Apply</button>
                <a href="{{ route('catalog.index') }}"
                   class="btn btn-sm w-100" style="color:var(--muted);font-size:.8125rem;">Reset</a>
            </form>
        </div>
    </div>

    {{-- Grid --}}
    <div class="col-lg-10">
        <div id="product-grid">
            @include('catalog._product_grid')
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    var ajaxUrl = '{{ route('catalog.ajax') }}';
    var grid    = document.getElementById('product-grid');
    var form    = document.getElementById('filterForm');
    var sortSel = document.getElementById('sortSelect');
    var debounce;

    function fetchGrid() {
        var sp = new URLSearchParams();
        form.querySelectorAll('[name]').forEach(function (el) {
            if (el.type === 'hidden' || el.type === 'submit') return;
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
            if (el.value === '') return;
            sp.set(el.name, el.value);
        });
        if (sortSel.value && sortSel.value !== 'new') sp.set('sort', sortSel.value);
        var qs = sp.toString();
        grid.style.opacity = '.4';
        fetch(ajaxUrl + (qs ? '?' + qs : ''), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) { grid.innerHTML = html; grid.style.opacity = '1'; history.replaceState(null, '', '{{ route('catalog.index') }}' + (qs ? '?' + qs : '')); })
            .catch(function () { grid.style.opacity = '1'; });
    }

    form.querySelectorAll('.filter-input').forEach(function (el) {
        el.addEventListener(el.type === 'number' ? 'input' : 'change', function () {
            clearTimeout(debounce);
            debounce = setTimeout(fetchGrid, el.type === 'number' ? 500 : 0);
        });
    });

    sortSel.addEventListener('change', function () {
        var h = form.querySelector('input[name="sort"]') || Object.assign(document.createElement('input'), {type:'hidden',name:'sort'});
        h.value = sortSel.value;
        if (!h.parentNode) form.appendChild(h);
        fetchGrid();
    });
})();
</script>
@endpush
