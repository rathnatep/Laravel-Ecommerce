@extends('layouts.app')

@section('title', 'Create Account — PickCloth')

@section('content')
<div class="row justify-content-center">
    <div class="col-sm-10 col-md-7 col-lg-5">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h2 class="h4 mb-1 fw-semibold">Create your account</h2>
                <p class="text-muted small mb-4">
                    No email needed — just your phone number.<br>
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </p>

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required autocomplete="name"
                               placeholder="Your name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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
                               required autocomplete="new-password" minlength="8"
                               placeholder="At least 8 characters">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm password</label>
                        <input type="password" id="password_confirmation"
                               name="password_confirmation"
                               class="form-control" required autocomplete="new-password">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
