<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#062664">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (filled($content->get('site_description')))<meta name="description" content="{{ $content->get('site_description') }}">@endif
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/lawyer-emblem.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/lawyer-icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>@yield('title', config('app.name')) · {{ $lawyer?->name ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/lawyer.css') }}" rel="stylesheet">
    <script defer src="{{ asset('js/pwa.js') }}"></script>
</head>
<body class="public-page">
    <header class="public-header">
        <a class="public-identity" href="{{ route('home') }}">
            <span class="public-seal" aria-hidden="true"><img src="{{ $content->get('navigation_logo') ? (str_starts_with($content->get('navigation_logo'), 'http') ? $content->get('navigation_logo') : asset('storage/'.$content->get('navigation_logo'))) : asset('images/lawyer-emblem.svg') }}" alt=""></span>
            <span class="identity-divider"></span>
            <span class="identity-copy"><strong>{{ $content->get('nav_brand_prefix') ?: 'ATTY.' }} {{ $content->get('nav_brand_name') ?: ($lawyer?->name ?? config('app.name')) }}</strong><small>{{ $content->get('nav_subtitle') ?: ($profile?->professional_title ?? 'ATTORNEY-AT-LAW') }}</small></span>
        </a>
        <nav class="public-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if (request()->routeIs('home')) aria-current="page" @endif>{{ $content->get('nav_home_label') ?: 'Home' }}</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}" @if (request()->routeIs('about')) aria-current="page" @endif>{{ $content->get('nav_about_label') ?: 'About Lawyer' }}</a>
            <a href="{{ route('services') }}" class="{{ request()->routeIs('services', 'services.show') ? 'active' : '' }}" @if (request()->routeIs('services', 'services.show')) aria-current="page" @endif>{{ $content->get('nav_services_label') ?: 'Services' }}</a>
            <a href="{{ route('laws.index') }}" class="{{ request()->routeIs('laws.*') ? 'active' : '' }}" @if (request()->routeIs('laws.*')) aria-current="page" @endif>{{ $content->get('nav_laws_label') ?: 'Philippine Laws' }}</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}" @if (request()->routeIs('contact')) aria-current="page" @endif>{{ $content->get('nav_contact_label') ?: 'Contact' }}</a>
        </nav>
        <div class="public-actions">
            <a class="portal-link" href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" aria-label="{{ auth()->check() ? ($content->get('nav_dashboard_label') ?: 'Go to Dashboard') : ($content->get('nav_portal_label') ?: 'Log in to Lawyer Portal') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 4a3.2 3.2 0 1 1 0 6.4A3.2 3.2 0 0 1 12 6Zm0 13.2a7.15 7.15 0 0 1-5.75-2.9c.05-1.9 3.85-2.95 5.75-2.95s5.7 1.05 5.75 2.95A7.15 7.15 0 0 1 12 19.2Z"/></svg>
                <span>{{ auth()->check() ? ($content->get('nav_dashboard_label') ?: 'Go to Dashboard') : ($content->get('nav_portal_label') ?: 'Lawyer Portal') }}</span>
            </a>
            <a class="consultation-link" href="{{ route('contact') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 4h20v16H2V4Zm10 8.2L4 6v12h16V6l-8 6.2Zm0-2.5L19 6H5l7 3.7Z"/></svg>{{ $content->get('nav_inquiry_label') ?: 'Send an Inquiry' }}</a>
        </div>
    </header>
    <main>@yield('content')</main>
    <footer class="public-footer">
        <span>{{ $lawyer?->name ?? config('app.name') }}</span>
        @if (filled($content->get('footer_note')))<span>{{ $content->get('footer_note') }}</span>@endif
        @if ($profile?->public_email)<a href="mailto:{{ $profile->public_email }}">{{ $profile->public_email }}</a>@endif
        @foreach (['social_facebook' => 'Facebook', 'social_instagram' => 'Instagram', 'social_linkedin' => 'LinkedIn'] as $key => $label)
            @if (filled($content->get($key)))
                <a class="social-icon-link" href="{{ $content->get($key) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label }}" title="{{ $label }}">
                    @if ($key === 'social_facebook')
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.4 21v-8h2.7l.4-3.1h-3.1V8c0-.9.3-1.5 1.6-1.5h1.7V3.7c-.3 0-1.4-.1-2.6-.1-2.6 0-4.4 1.6-4.4 4.6v2.6H7v3.1h2.7v8h3.7Z"/></svg>
                    @elseif ($key === 'social_instagram')
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle class="social-icon-dot" cx="17.7" cy="6.5" r="1"/></svg>
                    @elseif ($key === 'social_linkedin')
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.2 8.7H2.4V21h2.8V8.7ZM3.8 3a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4ZM21 13.9c0-3.7-2-5.4-4.7-5.4a4 4 0 0 0-3.6 2V8.7H10V21h2.8v-6.1c0-1.6.3-3.2 2.4-3.2s2.1 1.9 2.1 3.3V21H20v-6.8c0-.1 0-.2 0-.3Z"/></svg>
                    @endif
                </a>
            @endif
        @endforeach
    </footer>
    <button type="button" id="pwa-install" class="pwa-install" hidden>Install App</button>
    <p id="pwa-install-help" class="pwa-install-help" hidden>To install, open your browser menu and choose “Add to Home screen” or “Install app”.</p>
    @include('public.chat-widget')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'Yes, continue',
                cancelButtonText: 'No, cancel',
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
