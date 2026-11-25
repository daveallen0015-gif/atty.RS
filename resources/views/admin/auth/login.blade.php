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
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-ink w-100">Sign in</button>
        </form>
    </section>
@endsection
