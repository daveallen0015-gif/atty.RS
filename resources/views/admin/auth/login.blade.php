@extends('admin.layouts.auth')

@section('title', 'Sign in')

@section('content')
    <a class="auth-back" href="{{ route('home') }}" aria-label="Back to landing page" title="Back to landing page"><span aria-hidden="true">←</span></a>
    <section class="auth-panel">
        <p class="eyebrow">Lawyer portal</p>
        <div class="auth-notice" role="alert">This portal is exclusively for the lawyer. Unauthorized access is not permitted.</div>
        <h1>Welcome back.</h1>
        <p class="auth-intro">Sign in to manage your profile and website inquiries.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="auth-password-field">
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                    <button class="auth-password-toggle" type="button" aria-label="Show password" aria-pressed="false" aria-controls="password" title="Show password">
                        <svg class="password-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="password-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.4 0 10 6 10 6a16 16 0 0 1-3.1 3.7M6.2 6.3C3.5 8 2 12 2 12s3.6 6 10 6c1.5 0 2.8-.3 4-.8"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                    </button>
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="auth-remember mb-4">
                <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <label for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-ink w-100">Sign in</button>
        </form>
    </section>
    <script>
        (() => {
            const password = document.getElementById('password');
            const toggle = document.querySelector('.auth-password-toggle');

            toggle.addEventListener('click', () => {
                const shouldShow = password.type === 'password';
                password.type = shouldShow ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', String(shouldShow));
                toggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
                toggle.title = shouldShow ? 'Hide password' : 'Show password';
            });
        })();
    </script>
@endsection
