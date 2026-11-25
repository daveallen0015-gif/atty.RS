@extends('public.layouts.app')

@section('title', $service->title)

@section('content')
    <section class="service-detail-heading">
        <a class="service-back-link" href="{{ route('services') }}">&larr; Legal Services</a>
        <div class="service-detail-icon">@include('public.services._icon', ['icon' => $service->icon])</div>
        <h1>{{ $service->title }}</h1>
        @if ($service->image_path)<img class="service-detail-image" src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->title }}">@endif
        @if ($service->summary)<p class="service-detail-summary">{{ $service->summary }}</p>@endif
    </section>
    <article class="service-detail-body">
        @if ($service->description)<div class="prose">{!! nl2br(e($service->description)) !!}</div>@endif
        <a class="hero-button hero-button-primary" href="{{ route('contact', ['service' => $service->slug]) }}">Send an Inquiry <span aria-hidden="true">&rarr;</span></a>
    </article>
@endsection
