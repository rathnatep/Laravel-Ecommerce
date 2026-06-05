<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — PickCloth Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        :root { --admin-sidebar-bg: #161614; --admin-sidebar-w: 200px; }
        body { background: #F4F3F0; }

        .adm-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--admin-sidebar-w);
            background: var(--admin-sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .adm-brand {
            padding: 1.125rem 1.25rem .875rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            text-decoration: none;
        }
        .adm-brand__name {
            font-size: .8rem; font-weight: 700; letter-spacing: .14em;
            text-transform: uppercase; color: #fff;
        }
        .adm-brand__sub {
            font-size: .65rem; color: rgba(255,255,255,.3); letter-spacing: .06em;
            text-transform: uppercase; margin-top: 1px;
        }
        .adm-nav { flex: 1; padding: .5rem 0; overflow-y: auto; }
        .adm-nav__section {
            font-size: .6rem; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; color: rgba(255,255,255,.25);
            padding: .875rem 1.25rem .3rem;
        }
        .adm-nav__link {
            display: block; padding: .375rem 1.25rem;
            font-size: .8125rem; color: rgba(255,255,255,.5);
            text-decoration: none;
            border-left: 2px solid transparent;
        }
        .adm-nav__link:hover { color: rgba(255,255,255,.85); }
        .adm-nav__link.active { color: #fff; border-left-color: var(--brand); }

        .adm-footer {
            padding: .875rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .adm-footer a {
            display: block; font-size: .75rem; color: rgba(255,255,255,.35);
            text-decoration: none; margin-bottom: .5rem;
        }
        .adm-footer a:hover { color: rgba(255,255,255,.6); }
        .adm-footer button {
            background: none; border: 1px solid rgba(255,255,255,.12);
            color: rgba(255,255,255,.35); font-size: .75rem;
            padding: .2rem .75rem; border-radius: 3px; cursor: pointer;
        }
        .adm-footer button:hover { border-color: rgba(255,255,255,.3); color: rgba(255,255,255,.6); }

        .adm-body { margin-left: var(--admin-sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .adm-topbar {
            background: #fff; border-bottom: 1px solid #E8E6E1;
            padding: .75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            font-size: .8125rem; position: sticky; top: 0; z-index: 50;
        }
        .adm-topbar__title { font-weight: 600; color: #1A1A1A; }
        .adm-topbar__user { color: #9B9B9B; }
        .adm-content { padding: 1.75rem; flex: 1; }

        /* Table resets */
        .adm-table { font-size: .8125rem; }
        .adm-table thead th {
            font-weight: 600; font-size: .7rem; letter-spacing: .07em;
            text-transform: uppercase; color: #9B9B9B;
            border-bottom: 1px solid #E8E6E1; padding: .6rem .75rem;
        }
        .adm-table tbody td { padding: .75rem; border-bottom: 1px solid #F0EEE9; vertical-align: middle; }
        .adm-table tbody tr:last-child td { border-bottom: none; }

        /* Stat card */
        .adm-stat { background: #fff; border: 1px solid #E8E6E1; border-radius: 8px; padding: 1.25rem 1.5rem; }
        .adm-stat__label { font-size: .7rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: #9B9B9B; margin-bottom: .4rem; }
        .adm-stat__value { font-size: 1.75rem; font-weight: 700; color: #1A1A1A; line-height: 1; }
        .adm-stat__value.brand { color: var(--brand); }

        /* Section card */
        .adm-card { background: #fff; border: 1px solid #E8E6E1; border-radius: 8px; overflow: hidden; }
        .adm-card__head {
            padding: .875rem 1.25rem; border-bottom: 1px solid #E8E6E1;
            font-size: .75rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: #6B6B6B;
            display: flex; align-items: center; justify-content: space-between;
        }
    </style>
</head>
<body>

<aside class="adm-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="adm-brand">
        <div class="adm-brand__name">PickCloth</div>
        <div class="adm-brand__sub">Admin</div>
    </a>

    <nav class="adm-nav">
        <div class="adm-nav__section">Overview</div>
        <a href="{{ route('admin.dashboard') }}"
           class="adm-nav__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <div class="adm-nav__section">Catalog</div>
        <a href="{{ route('admin.products.index') }}"
           class="adm-nav__link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
            Products
        </a>
        <a href="{{ route('admin.products.create') }}"
           class="adm-nav__link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
            Add Product
        </a>

        <div class="adm-nav__section">Orders</div>
        <a href="{{ route('admin.orders.index') }}"
           class="adm-nav__link {{ request()->routeIs('admin.orders.index') && !request('status') ? 'active' : '' }}">
            All Orders
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
           class="adm-nav__link {{ request('status') === 'pending' ? 'active' : '' }}">
            Pending
        </a>
    </nav>

    <div class="adm-footer">
        <a href="{{ route('home') }}">← View store</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</aside>

<div class="adm-body">
    <div class="adm-topbar">
        <span class="adm-topbar__title">@yield('title', 'Dashboard')</span>
        <span class="adm-topbar__user">{{ Auth::user()->name }}</span>
    </div>

    <div class="adm-content">
        @if(session('status'))
            <div class="pc-flash mb-4" role="alert">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="pc-flash pc-flash-error mb-4" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="pc-flash pc-flash-error mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
