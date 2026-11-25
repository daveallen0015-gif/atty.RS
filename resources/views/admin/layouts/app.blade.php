<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lawyer-emblem.svg') }}">
    <title>@yield('title', 'Dashboard') · Lawyer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lawyer.css') }}" rel="stylesheet">
</head>
<body class="admin-page">
    <aside class="admin-sidebar">
        @php($adminProfile = auth()->user()->lawyerProfile)
        <div class="admin-account-summary">
            <a class="admin-account-card" href="{{ route('admin.profile.edit') }}">
                @if ($adminProfile?->photo_path)
                    <img src="{{ str_starts_with($adminProfile->photo_path, 'http') ? $adminProfile->photo_path : asset('storage/'.$adminProfile->photo_path) }}" alt="{{ auth()->user()->name }} profile photo">
                @else
                    <span class="admin-account-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
                <span class="admin-account-details"><strong>{{ auth()->user()->name }}</strong><small>Lawyer account</small></span>
            </a>
        </div>
        <p class="admin-caption">Administration</p>
        <nav class="admin-nav" aria-label="Admin navigation">
            <div class="admin-nav-group">
                <span class="admin-nav-label">Main</span>
                <a class="admin-nav-overview {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">Website Management</span>
                <a class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}" @if(request()->routeIs('admin.services.*')) aria-current="page" @endif>Services</a>
                <a class="{{ request()->routeIs('admin.laws.*') ? 'active' : '' }}" href="{{ route('admin.laws.index') }}" @if(request()->routeIs('admin.laws.*')) aria-current="page" @endif>Philippine Laws</a>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">Communication</span>
                <a class="{{ request()->routeIs('admin.inquiries.*') ? 'active' : '' }}" href="{{ route('admin.inquiries.index') }}" @if(request()->routeIs('admin.inquiries.*')) aria-current="page" @endif>Inquiries</a>
                <a class="{{ request()->routeIs('admin.chats.*') ? 'active' : '' }}" href="{{ route('admin.chats.index') }}" @if(request()->routeIs('admin.chats.*')) aria-current="page" @endif>Chat Management</a>
            </div>
            <div class="admin-nav-group">
                <span class="admin-nav-label">Content Management</span>
                <a class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}" @if(request()->routeIs('admin.profile.*')) aria-current="page" @endif>Account Settings</a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif>System Settings</a>
            </div>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}" class="admin-logout-form" data-confirm-title="Sign out?" data-confirm-message="Are you sure you want to sign out?" data-confirm-button="Yes, sign out" data-confirm-icon="question">
            @csrf
            <button type="submit" class="admin-logout">Sign out</button>
        </form>
    </aside>
    <main class="admin-main">
        <header class="admin-topline"><span>@yield('section', 'Overview')</span><span>{{ auth()->user()->name }}</span></header>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('status'))
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: @json(session('status')),
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        @endif

        document.querySelectorAll('.auto-dismiss-message').forEach((message) => {
            window.setTimeout(() => {
                message.classList.add('is-dismissing');
                window.setTimeout(() => message.remove(), 200);
            }, 800);
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form.matches('form[data-confirm-title]') || form.dataset.confirmed === 'true') return;

            event.preventDefault();
            Swal.fire({
                title: form.dataset.confirmTitle,
                text: form.dataset.confirmMessage || 'Are you sure you want to continue?',
                icon: form.dataset.confirmIcon || 'question',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'Yes, continue',
                cancelButtonText: form.dataset.cancelButton || 'No, cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit(event.submitter);
                }
            });
        }, true);
    </script>
</body>
</html>
