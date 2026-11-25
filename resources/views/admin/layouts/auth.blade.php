<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lawyer-emblem.svg') }}">
    <title>@yield('title', 'Admin') &middot; Lawyer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lawyer.css') }}" rel="stylesheet">
</head>
<body class="admin-auth-page">
    <main class="auth-frame">
        <div class="auth-brand">
            <a class="auth-wordmark" href="{{ route('admin.login') }}">
                <span class="auth-logo-mark" aria-hidden="true">LP</span>
                <span>Lawyer Portal</span>
            </a>
        </div>
        @yield('content')
        <p class="auth-footnote">Private administration &middot; Authorized access only</p>
    </main>
</body>
</html>
