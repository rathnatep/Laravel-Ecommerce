@extends('layouts.app')

@section('title', 'Sign In — PickCloth')

@section('content')
<div class="row justify-content-center">
    <div class="col-sm-10 col-md-7 col-lg-5">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h2 class="h4 mb-1 fw-semibold">Welcome back</h2>
                <p class="text-muted small mb-4">
                    New here? <a href="{{ route('register') }}">Create a free account</a>
                </p>

                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone number</label>
                        <input type="tel" id="phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" required autocomplete="tel"
                               placeholder="e.g. 012 345 678">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember">Keep me signed in</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Sign In</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
