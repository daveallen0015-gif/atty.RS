@extends('public.layouts.app')

@section('title', $content->get('services_page_heading', 'Legal Services'))

@section('content')
    <section class="services-page-heading">
        <h1>{{ $content->get('services_page_heading', 'Legal Services') }}</h1>
        <p class="interior-intro">{{ $content->get('services_page_intro') }}</p>
    </section>

    @if ($services->isNotEmpty())
        <section class="service-card-grid" aria-label="Available legal services">
            @foreach ($services as $service)
                <article class="service-card">
                    @if ($service->image_path)<img class="service-card-image" src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->title }}">@endif
                    <div class="service-card-icon">@include('public.services._icon', ['icon' => $service->icon])</div>
                    <h2>{{ $service->title }}</h2>
                    <p>{{ $service->summary ?: \Illuminate\Support\Str::limit(strip_tags($service->description ?? ''), 165) }}</p>
                    <a class="service-card-link" href="{{ route('services.show', $service->slug) }}">Learn More <span aria-hidden="true">&rarr;</span></a>
                </article>
            @endforeach
        </section>
    @else
        <p class="services-empty">No legal services are currently published.</p>
    @endif

    <section class="services-inquiry-cta">
        <div>
            <h2>{{ $content->get('services_cta_heading', 'Need Legal Assistance?') }}</h2>
            <p>{{ $content->get('services_cta_body') }}</p>
        </div>
        <a class="hero-button hero-button-primary" href="{{ route('contact') }}">{{ $content->get('services_cta_button', 'Send an Inquiry') }} <span aria-hidden="true">&rarr;</span></a>
    </section>
@endsection
