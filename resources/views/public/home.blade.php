@extends('public.layouts.app')

@section('title', $content->get('hero_title', config('app.name')))

@section('content')
    <section class="hero-section" id="home" @if ($content->get('hero_background')) style="--hero-image: url('{{ str_starts_with($content->get('hero_background'), 'http') ? $content->get('hero_background') : asset('storage/'.$content->get('hero_background')) }}')" @endif>
        <div class="hero-copy">
            @if (filled($content->get('hero_eyebrow')))<p class="public-eyebrow"><span></span>{{ $content->get('hero_eyebrow') }}<span></span></p>@endif
            @if (filled($content->get('hero_title')))<h1>{{ $content->get('hero_title') }}@if (filled($content->get('hero_accent_title')))<em>{{ $content->get('hero_accent_title') }}</em>@endif</h1>@endif
            @if (filled($content->get('hero_body')))<p class="hero-body">{{ $content->get('hero_body') }}</p>@endif
            <div class="hero-actions">
                @if (filled($content->get('hero_button_label')))<a class="hero-button hero-button-primary" href="{{ route('services') }}">{{ $content->get('hero_button_label') }}<span aria-hidden="true">&rsaquo;</span></a>@endif
                @if (filled($content->get('hero_contact_label')))<a class="hero-button hero-button-secondary" href="{{ route('contact') }}">{{ $content->get('hero_contact_label') }}<span aria-hidden="true">&rarr;</span></a>@endif
            </div>
        </div>
        <a class="hero-scroll" href="#highlights" aria-label="Scroll to highlights"><span></span></a>
    </section>

    <section class="value-strip" id="highlights" aria-label="Service values">
        @foreach (range(1, 4) as $number)
            <div class="value-item">
                <span class="value-icon" aria-hidden="true">
                    @if ($number === 1)<svg viewBox="0 0 48 48"><path d="M24 5v34m-15-27h30M14 12 4 29h20L14 12Zm20 0L24 29h20L34 12ZM18 40h12m-6-1v5m-13 0h26"/></svg>
                    @elseif ($number === 2)<svg viewBox="0 0 48 48"><path d="M24 4 40 10v12c0 11-7 18-16 22C15 40 8 33 8 22V10l16-6Z"/><path d="m16 23 6 6 11-12"/></svg>
                    @elseif ($number === 3)<svg viewBox="0 0 48 48"><path d="m4 24 10-10 9 5 4-3a6 6 0 0 1 8 1l8-5 3 4-9 9 5 5-4 4-6-5-5 5-4-4-6 5-4-4 6-6-6-4-5 5-6-7Z"/></svg>
                    @else<svg viewBox="0 0 48 48"><path d="M5 18h38M9 18 24 7l15 11M9 22v17m10-17v17m10-17v17m10-17v17M5 42h38M3 46h42"/></svg>@endif
                </span>
                <span>{{ $content->get('feature_'.$number) }}</span>
            </div>
        @endforeach
    </section>

    @if (filled($content->get('about_heading')) || filled($content->get('about_body')) || filled($profile?->short_bio))
        <section class="public-band about-band" id="about">
            @if (filled($content->get('about_heading')))<h2 class="about-heading">{{ $content->get('about_heading') }}</h2>@endif
            <div class="band-copy">
                @if (filled($content->get('about_body')))<p>{{ $content->get('about_body') }}</p>@elseif($profile?->short_bio)<p>{{ $profile->short_bio }}</p>@endif
                @if (filled($content->get('about_link_label')))<a class="editorial-link" href="{{ route('about') }}">{{ $content->get('about_link_label') }} <span aria-hidden="true">&rarr;</span></a>@endif
            </div>
            @if ($profile?->photo_path)<figure class="about-portrait"><img src="{{ str_starts_with($profile->photo_path, 'http') ? $profile->photo_path : asset('storage/'.$profile->photo_path) }}" alt="Portrait of {{ $lawyer?->name }}"></figure>@endif
        </section>
    @endif

    @if ($services->isNotEmpty())
        <section class="services-section">
            <div class="services-heading"><div>@if (filled($content->get('services_heading')))<h2>{{ $content->get('services_heading') }}</h2>@endif @if (filled($content->get('services_intro')))<p>{{ $content->get('services_intro') }}</p>@endif</div>@if (filled($content->get('services_link_label')))<a class="editorial-link" href="{{ route('services') }}">{{ $content->get('services_link_label') }} <span aria-hidden="true">&rarr;</span></a>@endif</div>
            <div class="service-list">@foreach ($services->take(4) as $index => $service)<article class="service-line"><span class="service-number">0{{ $index + 1 }}</span><div><h3>{{ $service->title }}</h3>@if ($service->summary)<p>{{ $service->summary }}</p>@endif</div><a class="service-arrow" href="{{ route('services.show', $service->slug) }}" aria-label="Learn more about {{ $service->title }}">&rarr;</a></article>@endforeach</div>
        </section>
    @endif

    @if (filled($content->get('contact_heading')) || filled($content->get('contact_intro')))
        <section class="contact-band" id="contact"><div>@if (filled($content->get('contact_heading')))<h2>{{ $content->get('contact_heading') }}</h2>@endif @if (filled($content->get('contact_intro')))<p>{{ $content->get('contact_intro') }}</p>@endif @if (filled($content->get('contact_link_label')))<a class="editorial-link" href="{{ route('contact') }}">{{ $content->get('contact_link_label') }} <span aria-hidden="true">&rarr;</span></a>@endif</div></section>
    @endif
@endsection
