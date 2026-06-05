<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PickCloth')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<nav class="pc-nav navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">PickCloth</a>

        <button class="navbar-toggler border-0 p-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="font-size:.875rem;"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}"
                       href="{{ route('catalog.index') }}">Shop</a>
                </li>
                @foreach (['men', 'women', 'kids'] as $dept)
                <li class="nav-item">
                    <a class="nav-link {{ request('department') === $dept ? 'active' : '' }}"
                       href="{{ route('catalog.index', ['department' => $dept]) }}">
                        {{ ucfirst($dept) }}
                    </a>
                </li>
                @endforeach
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link position-relative {{ request()->routeIs('cart.*') ? 'active' : '' }}"
                       href="{{ route('cart.index') }}">
                        Cart
                        @if(($cartCount ?? 0) > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                           href="{{ route('orders.index') }}">Orders</a>
                    </li>
                    @if(Auth::user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin*') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">Admin</a>
                    </li>
                    @endif
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm btn-outline-primary px-3">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                    </li>
                    <li class="nav-item ms-lg-1">
                        <a class="btn btn-sm btn-primary px-3"
                           href="{{ route('register') }}">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5">
    @if(session('status'))
        <div class="pc-flash mb-4" role="alert">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="pc-flash pc-flash-error mb-4" role="alert">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

<footer class="pc-footer">
    <div class="container">
        &copy; {{ date('Y') }} PickCloth
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
